<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\RedirectResponse;
use App\Support\AutoCode;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PromotionController extends Controller
{
    public function index(): View
    {
        $now = now()->toDateString();
        $stats = [
            'total' => Promotion::count(),
            'active' => Promotion::where('is_active', true)
                ->where(function ($q) use ($now) {
                    $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                })
                ->where(function ($q) use ($now) {
                    $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
                })
                ->count(),
            'upcoming' => Promotion::where('is_active', true)
                ->where('starts_at', '>', $now)
                ->count(),
            'expired' => Promotion::whereNotNull('ends_at')
                ->where('ends_at', '<', $now)
                ->count(),
        ];

        $promotions = Promotion::with('product')->orderByDesc('starts_at')->orderBy('code')->paginate(50);

        return view('promotions.index', compact('promotions', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        AutoCode::fill($request, 'promotions', 'PM');
        $data = $this->validatePromotion($request);
        $promo = Promotion::create($data);

        $this->notifyPosUpdate();

        return redirect()->route('promotions.index')->with('success', "เพิ่มโปรโมชั่น {$promo->code} เรียบร้อยแล้ว");
    }

    public function update(Request $request, Promotion $promotion): RedirectResponse
    {
        $data = $this->validatePromotion($request, $promotion->id);
        $promotion->update($data);

        $this->notifyPosUpdate();

        return redirect()->route('promotions.index')->with('success', "บันทึกการแก้ไขโปรโมชั่น {$promotion->code} เรียบร้อยแล้ว");
    }

    public function destroy(Promotion $promotion): RedirectResponse
    {
        $code = $promotion->code;
        $promotion->delete();

        $this->notifyPosUpdate();

        return redirect()->route('promotions.index')->with('success', "ลบโปรโมชั่น {$code} เรียบร้อยแล้ว");
    }

    private function notifyPosUpdate(): void
    {
        if (class_exists(\App\Support\PosSyncNotifier::class)) {
            \App\Support\PosSyncNotifier::notifyChange();
        } elseif (class_exists(\App\Models\AppSetting::class)) {
            \App\Models\AppSetting::set('pos_price_catalog_version', (string) now()->timestamp);
        }
    }

    private function validatePromotion(Request $request, ?int $ignoreId = null): array
    {
        // Convert empty strings to null for clean database storage
        if ($request->input('starts_at') === '') {
            $request->merge(['starts_at' => null]);
        }
        if ($request->input('ends_at') === '') {
            $request->merge(['ends_at' => null]);
        }
        if ($request->input('product_id') === '') {
            $request->merge(['product_id' => null]);
        }
        if ($request->input('min_qty') === '') {
            $request->merge(['min_qty' => null]);
        }
        if ($request->input('min_amount') === '') {
            $request->merge(['min_amount' => null]);
        }
        if ($request->input('discount_amount') === '') {
            $request->merge(['discount_amount' => null]);
        }
        if ($request->input('discount_percent') === '') {
            $request->merge(['discount_percent' => null]);
        }

        $data = $request->validate([
            'code' => ['required', 'string', 'max:30', 'unique:promotions,code,'.($ignoreId ?? 'NULL').',id'],
            'name' => ['required', 'string', 'max:150'],
            'promotion_type' => ['required', 'string', 'max:40'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'min_qty' => ['nullable', 'numeric', 'min:0'],
            'min_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'note' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
