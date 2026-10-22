<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PromotionController extends Controller
{
    public function index(): View
    {
        $promotions = Promotion::with('product')->orderByDesc('starts_at')->orderByDesc('id')->paginate(50);
        $products = Product::where('is_active', true)
            ->with(['barcodes:id,product_id,barcode'])
            ->orderBy('sku_code')
            ->get(['id', 'sku_code', 'name_th', 'default_price', 'average_cost', 'minimum_margin_percent', 'margin_control_policy']);

        $branches = Branch::where('is_active', true)->orderBy('code')->get(['id', 'code', 'name_th']);

        $now = now();
        $stats = [
            'total' => Promotion::count(),
            'active' => Promotion::where('is_active', true)
                ->where(fn ($w) => $w->whereNull('starts_at')->orWhere('starts_at', '<=', $now->toDateString()))
                ->where(fn ($w) => $w->whereNull('ends_at')->orWhere('ends_at', '>=', $now->toDateString()))->count(),
            'upcoming' => Promotion::where('is_active', true)->where('starts_at', '>', $now->toDateString())->count(),
            'expired' => Promotion::where('ends_at', '<', $now->toDateString())->count(),
        ];

        return view('promotions.index', compact('promotions', 'products', 'branches', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePromotion($request);
        Promotion::create($data);
        if (class_exists(\App\Support\PosSyncNotifier::class)) {
            \App\Support\PosSyncNotifier::notifyChange();
        } elseif (class_exists(\App\Models\AppSetting::class)) {
            \App\Models\AppSetting::set('pos_price_catalog_version', (string) now()->timestamp);
        }

        return redirect()->route('promotions.index')->with('success', "เพิ่มโปรโมชั่น {$data['code']} แล้ว");
    }

    public function update(Request $request, Promotion $promotion): RedirectResponse
    {
        $data = $this->validatePromotion($request, $promotion->id);
        $promotion->update($data);
        if (class_exists(\App\Support\PosSyncNotifier::class)) {
            \App\Support\PosSyncNotifier::notifyChange();
        } elseif (class_exists(\App\Models\AppSetting::class)) {
            \App\Models\AppSetting::set('pos_price_catalog_version', (string) now()->timestamp);
        }

        return redirect()->route('promotions.index')->with('success', 'บันทึกโปรโมชั่นแล้ว');
    }

    public function destroy(Promotion $promotion): RedirectResponse
    {
        $code = $promotion->code;
        $promotion->delete();
        if (class_exists(\App\Support\PosSyncNotifier::class)) {
            \App\Support\PosSyncNotifier::notifyChange();
        } elseif (class_exists(\App\Models\AppSetting::class)) {
            \App\Models\AppSetting::set('pos_price_catalog_version', (string) now()->timestamp);
        }

        return redirect()->route('promotions.index')->with('success', "ลบโปรโมชั่น {$code} เรียบร้อยแล้ว");
    }

    public function bulkCreate(Request $request): RedirectResponse
    {
        $productIds = $request->input('product_ids', []);
        if (empty($productIds) || !is_array($productIds)) {
            return redirect()->back()->with('error', 'กรุณาเลือกสินค้าอย่างน้อย 1 รายการ');
        }

        $rawDiscountType = (string) $request->input('discount_type', 'fixed');
        $discountType = in_array($rawDiscountType, ['percentage', 'percent'], true) ? 'percent' : 'baht';
        $discountValue = (float) $request->input('discount_value', 0);
        if ($discountValue <= 0) {
            return redirect()->back()->with('error', 'กรุณาระบุมูลค่าส่วนลดที่มากกว่า 0');
        }

        $branchIds = $request->input('branch_ids', []);
        if (!empty($branchIds) && is_array($branchIds)) {
            $branchIds = array_values(array_unique(array_filter(array_map('intval', $branchIds))));
        } else {
            $branchIds = null;
        }

        $startsAt = $request->filled('start_date') ? $request->input('start_date') : ($request->filled('starts_at') ? $request->input('starts_at') : null);
        $endsAt = $request->filled('end_date') ? $request->input('end_date') : ($request->filled('ends_at') ? $request->input('ends_at') : null);
        $minQty = $request->filled('min_quantity') ? (float) $request->input('min_quantity') : ($request->filled('min_qty') ? (float) $request->input('min_qty') : 1);
        $customName = trim((string) $request->input('name', ''));

        $createdCount = 0;
        $products = Product::whereIn('id', $productIds)->get(['id', 'sku_code', 'name_th', 'default_price']);

        foreach ($products as $p) {
            $code = 'PROMO-' . now()->format('ymd') . '-' . $p->sku_code . '-' . rand(10, 99);
            // Ensure unique code
            while (Promotion::where('code', $code)->exists()) {
                $code = 'PROMO-' . now()->format('ymd') . '-' . $p->sku_code . '-' . rand(100, 999);
            }

            $discountText = $discountType === 'percent' ? "ลด {$discountValue}%" : "ลด ฿{$discountValue}";
            $promoName = $customName !== '' ? "{$customName} ({$discountText})" : "{$p->name_th} ({$discountText})";

            Promotion::create([
                'code' => $code,
                'name' => $promoName,
                'promotion_type' => 'discount',
                'product_id' => $p->id,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'min_qty' => $minQty,
                'discount_amount' => $discountType === 'baht' ? $discountValue : null,
                'discount_percent' => $discountType === 'percent' ? $discountValue : null,
                'branch_ids' => $branchIds,
                'is_active' => true,
            ]);
            $createdCount++;
        }

        if (class_exists(\App\Support\PosSyncNotifier::class)) {
            \App\Support\PosSyncNotifier::notifyChange();
        } elseif (class_exists(\App\Models\AppSetting::class)) {
            \App\Models\AppSetting::set('pos_price_catalog_version', (string) now()->timestamp);
        }

        return redirect()->route('products.index')->with('success', "สร้างโปรโมชั่นส่วนลดให้กับสินค้า {$createdCount} รายการเรียบร้อยแล้ว");
    }

    private function validatePromotion(Request $request, ?int $ignoreId = null): array
    {
        if (! $request->filled('code')) {
            $request->merge(['code' => 'PROMO-'.now()->format('ymd').'-'.rand(1000, 9999)]);
        }

        if (! $request->filled('name')) {
            $productName = '';
            if ($request->filled('product_id')) {
                $productName = Product::where('id', $request->input('product_id'))->value('name_th') ?? '';
            }
            $discountText = $request->filled('discount_amount')
                ? 'ลด '.$request->input('discount_amount').' บาท'
                : ($request->filled('discount_percent') ? 'ลด '.$request->input('discount_percent').'%' : 'โปรโมชั่นพิเศษ');

            $request->merge(['name' => $productName ? "{$productName} ({$discountText})" : "แคมเปญ {$discountText}"]);
        }

        $data = $request->validate([
            'code' => ['required', 'string', 'max:30', 'unique:promotions,code,'.($ignoreId ?? 'NULL').',id'],
            'name' => ['required', 'string', 'max:150'],
            'promotion_type' => ['nullable', 'string', 'max:40'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'start_time' => ['nullable', 'string', 'max:8'],
            'end_time' => ['nullable', 'string', 'max:8'],
            'days_of_week' => ['nullable'],
            'branch_ids' => ['nullable', 'array'],
            'branch_ids.*' => ['integer', 'exists:branches,id'],
            'min_qty' => ['nullable', 'numeric', 'min:0'],
            'min_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'note' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (isset($data['days_of_week']) && is_array($data['days_of_week'])) {
            $data['days_of_week'] = implode(',', $data['days_of_week']);
        }

        if (isset($data['branch_ids']) && is_array($data['branch_ids'])) {
            $data['branch_ids'] = array_values(array_unique(array_filter(array_map('intval', $data['branch_ids']))));
            if (empty($data['branch_ids'])) {
                $data['branch_ids'] = null;
            }
        } else {
            $data['branch_ids'] = null;
        }

        $data['promotion_type'] = $data['promotion_type'] ?? 'discount';
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
