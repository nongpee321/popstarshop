<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Branch;
use App\Models\DiscountCard;
use App\Models\Document;
use App\Models\FlashSaleItem;
use App\Models\Member;
use App\Models\PosCashMovement;
use App\Models\PosHeldBill;
use App\Models\PosPayment;
use App\Models\PosReceipt;
use App\Models\PosReceiptItem;
use App\Models\PosShift;
use App\Models\PosTerminal;
use App\Models\PriceTable;
use App\Models\Product;
use App\Models\ProductBarcode;
use App\Models\ProductCategory;
use App\Models\ProductPrice;
use App\Models\ProductUnit;
use App\Models\Promotion;
use App\Models\QrPaymentConfig;
use App\Models\QtyPromotion;
use App\Models\Salesman;
use App\Models\StockBalance;
use App\Models\StockDocument;
use App\Models\User;
use App\Services\Accounting\CashBookPostingService;
use App\Services\Accounting\GlPostingService;
use App\Services\Inventory\FifoStockService;
use App\Services\Sales\CashSaleService;
use App\Services\Sales\MemberPointService;
use App\Services\Sales\PosPaymentValidator;
use App\Services\Sales\PosPriceScheduleService;
use App\Services\Sales\PosPricingGuard;
use App\Support\BarcodePolicy;
use App\Support\DecimalMath;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class PosController extends Controller
{
    private const DEFAULT_POS_LAYOUT = [
        'schema' => 'popcentral-pos-layout',
        'version' => 1,
        'canvas' => ['columns' => 12, 'rows' => 8],
        'components' => [
            ['id' => 'search', 'type' => 'search', 'x' => 1, 'y' => 1, 'w' => 7, 'h' => 1],
            ['id' => 'category', 'type' => 'category_tabs', 'x' => 1, 'y' => 2, 'w' => 7, 'h' => 1],
            ['id' => 'products', 'type' => 'product_grid', 'x' => 1, 'y' => 3, 'w' => 7, 'h' => 5],
            ['id' => 'cart', 'type' => 'cart', 'x' => 8, 'y' => 1, 'w' => 5, 'h' => 5],
            ['id' => 'payment', 'type' => 'payment', 'x' => 8, 'y' => 6, 'w' => 5, 'h' => 2],
        ],
    ];


    public function branches(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'branches' => \App\Models\Branch::where('is_active', true)->select('id', 'name')->get()
        ]);
    }

    public function customerDisplay()
    {
        return view('pos.customer-display', [
            'company' => ['name' => \App\Models\AppSetting::company('name')],
            'logo' => \App\Models\AppSetting::logoUrl(),
        ]);
    }

    public function index(MemberPointService $points): View
    {
        $webMode = AppSetting::get('pos_web_mode', 'sell');
        if ($webMode === 'preview') {
            return $this->preview();
        }

        // à¸‚à¸±à¹‰à¸™ cutover: à¸¢à¹‰à¸²à¸¢à¸à¸²à¸£à¸‚à¸²à¸¢à¹„à¸›à¹à¸­à¸›à¹€à¸”à¸ªà¸à¹Œà¸—à¹‡à¸­à¸› PopCentral POS à¹à¸¥à¹‰à¸§ à¸›à¸´à¸”à¸«à¸™à¹‰à¸²à¸‚à¸²à¸¢à¹€à¸§à¹‡à¸šà¸”à¹‰à¸§à¸¢
        // flag à¹„à¸”à¹‰ à¸”à¸µà¸Ÿà¸­à¸¥à¸•à¹Œ 'sell' = à¸‚à¸²à¸¢à¹„à¸”à¹‰à¸•à¸²à¸¡à¹€à¸”à¸´à¸¡ (à¸¢à¸±à¸‡à¹„à¸¡à¹ˆà¹€à¸›à¸¥à¸µà¹ˆà¸¢à¸™à¸­à¸°à¹„à¸£) à¸•à¸±à¹‰à¸‡ 'redirect'
        // à¹€à¸¡à¸·à¹ˆà¸­à¸žà¸£à¹‰à¸­à¸¡ à¹€à¸žà¸·à¹ˆà¸­à¹ƒà¸«à¹‰ /pos à¹€à¸«à¸¥à¸·à¸­à¸«à¸™à¹‰à¸²à¸ªà¸–à¸²à¸™à¸° + à¸¥à¸´à¸‡à¸à¹Œà¸”à¸²à¸§à¸™à¹Œà¹‚à¸«à¸¥à¸”à¹à¸­à¸›
        if ($webMode === 'redirect') {
            return view('pos.retired');
        }

        $branches = Branch::orderBy('code')->get(['id', 'code', 'name_th']);
        $categories = ProductCategory::orderBy('name_th')->get(['id', 'code', 'name_th']);
        // User is the visible POS identity. The adapter id only travels in the
        // compatibility request field used by existing shift/document tables.
        $cashiers = User::query()
            ->where('is_active', true)
            ->whereHas('roles.permissions', fn ($permission) => $permission->where('code', 'pos.sell'))
            ->with('posCashierProfile:id,user_id')
            ->orderBy('name')
            ->get(['id', 'username', 'name'])
            ->filter(fn (User $user) => $user->posCashierProfile !== null)
            ->map(fn (User $user) => (object) [
                'id' => $user->posCashierProfile->id,
                'user_id' => $user->id,
                'code' => $user->username,
                'name' => $user->name,
            ]);
        $defaultBranchId = $branches->first()?->id;
        $qrConfig = QrPaymentConfig::where('is_active', true)->with('bankAccount')->first();
        $pointValueBaht = $points->pointValueBaht();

        // à¹à¸„à¸Šà¹€à¸Šà¸µà¸¢à¸£à¹Œà¹€à¸—à¹ˆà¸²à¸™à¸±à¹‰à¸™à¸—à¸µà¹ˆà¹€à¸›à¸´à¸”à¸à¸°/à¸„à¸´à¸”à¹€à¸‡à¸´à¸™à¹„à¸”à¹‰ - à¸œà¸ˆà¸.à¸ªà¸²à¸‚à¸²/à¸œà¸¹à¹‰à¸šà¸£à¸´à¸«à¸²à¸£à¹€à¸›à¸´à¸”à¸”à¸¹à¹„à¸”à¹‰à¸­à¸¢à¹ˆà¸²à¸‡à¹€à¸”à¸µà¸¢à¸§
        $authUser = auth()->user();
        $canSell = (bool) $authUser?->hasPermission('pos.sell');
        $canVoid = (bool) $authUser?->hasPermission('pos.void');
        $canManageSettings = (bool) $authUser?->hasPermission('settings.manage');

        // à¸šà¸±à¸‡à¸„à¸±à¸šà¹ƒà¸Šà¹‰à¸•à¸±à¸§à¹€à¸­à¸‡: à¸¥à¹‡à¸­à¸à¸ªà¸²à¸‚à¸²+à¸„à¸™à¸‚à¸²à¸¢à¹€à¸›à¹‡à¸™à¸‚à¸­à¸‡ user à¸—à¸µà¹ˆ login (à¹à¸„à¸Šà¹€à¸Šà¸µà¸¢à¸£à¹Œà¹€à¸¥à¸·à¸­à¸à¹€à¸­à¸‡à¹„à¸¡à¹ˆà¹„à¸”à¹‰)
        $authUser?->loadMissing(['branch', 'posCashierProfile']);
        $lockedBranch = $authUser?->branch;           // à¸ªà¸²à¸‚à¸²à¸—à¸µà¹ˆ user à¸ªà¸±à¸‡à¸à¸±à¸” (null = à¸¢à¸±à¸‡à¹„à¸¡à¹ˆà¸à¸³à¸«à¸™à¸”)
        $lockedCashier = $authUser?->posCashierProfile;
        if ($lockedBranch) {
            $defaultBranchId = $lockedBranch->id;
        }

        // à¸‚à¹‰à¸­à¸¡à¸¹à¸¥à¸­à¸­à¸à¹ƒà¸šà¸à¸³à¸à¸±à¸šà¸ à¸²à¸©à¸µà¸­à¸¢à¹ˆà¸²à¸‡à¸¢à¹ˆà¸­ (à¸¡à¸²à¸•à¸£à¸² 86/6): à¸Šà¸·à¹ˆà¸­+à¹€à¸¥à¸‚à¸ à¸²à¸©à¸µà¸œà¸¹à¹‰à¸‚à¸²à¸¢ + à¸­à¸±à¸•à¸£à¸² VAT
        $company = [
            'name' => AppSetting::company('name_th'),
            'tax_id' => AppSetting::company('tax_id'),
            'address' => AppSetting::company('address'),
            'phone' => AppSetting::company('phone'),
        ];
        $vatRate = (float) (DB::table('vat_rates')
            ->where('effective_from', '<=', now()->toDateString())
            ->where(fn ($w) => $w->whereNull('effective_to')->orWhere('effective_to', '>=', now()->toDateString()))
            ->orderByDesc('effective_from')->value('rate_percent') ?? 7.0);

        // Preload initial products for instant zero-latency render in web & desktop app
        $initialProducts = json_decode($this->products(request())->getContent(), true) ?? [];

        // NOTE: à¸«à¸™à¹‰à¸² POS à¸šà¸™à¹€à¸§à¹‡à¸šà¹€à¸›à¹‡à¸™ compatibility/staged flow; à¹à¸„à¸Šà¹€à¸Šà¸µà¸¢à¸£à¹Œà¸«à¸¥à¸±à¸à¸„à¸·à¸­ Python/PySide6 à¹ƒà¸™ pos-python/.
        return view('pos.index', compact('branches', 'categories', 'cashiers', 'defaultBranchId', 'qrConfig', 'pointValueBaht', 'canSell', 'canVoid', 'canManageSettings', 'company', 'vatRate', 'lockedBranch', 'lockedCashier', 'initialProducts'));
    }

    public function preview(): View
    {
        $published = json_decode((string) AppSetting::get('pos_layout_published'), true);
        $layout = is_array($published) ? $published : self::DEFAULT_POS_LAYOUT;
        $allowed = ['search', 'category_tabs', 'product_grid', 'cart', 'payment', 'customer', 'held_bills', 'numpad', 'shift_status'];

        $layout['components'] = collect($layout['components'] ?? [])
            ->filter(fn ($component) => is_array($component) && in_array($component['type'] ?? '', $allowed, true))
            ->map(function ($component) {
                $x = max(1, min(12, (int) ($component['x'] ?? 1)));
                $y = max(1, min(12, (int) ($component['y'] ?? 1)));

                return [
                    'id' => (string) ($component['id'] ?? $component['type']),
                    'type' => (string) $component['type'],
                    'x' => $x,
                    'y' => $y,
                    'w' => max(1, min(13 - $x, (int) ($component['w'] ?? 3))),
                    'h' => max(1, min(13 - $y, (int) ($component['h'] ?? 2))),
                ];
            })->values()->all();

        if ($layout['components'] === []) {
            $layout = self::DEFAULT_POS_LAYOUT;
        }

        $previewProducts = Product::where('is_active', true)
            ->orderBy('name_th')
            ->limit(8)
            ->get(['id', 'sku_code', 'image_url', 'name_th', 'default_price']);
        $previewCategories = ProductCategory::orderBy('name_th')
            ->limit(5)
            ->get(['id', 'name_th']);

        return view('pos.preview', [
            'layout' => $layout,
            'previewProducts' => $previewProducts,
            'previewCategories' => $previewCategories,
            'publishedAt' => AppSetting::get('pos_layout_published_at'),
            'publishedVersion' => (int) AppSetting::get('pos_layout_version', (string) ($layout['version'] ?? 1)),
        ]);
    }

    // à¸ªà¸²à¸‚à¸²+à¸„à¸™à¸‚à¸²à¸¢à¸—à¸µà¹ˆà¸šà¸±à¸‡à¸„à¸±à¸šà¹ƒà¸Šà¹‰à¸ªà¸³à¸«à¸£à¸±à¸š user à¸—à¸µà¹ˆ login (à¸–à¹‰à¸²à¸à¸³à¸«à¸™à¸”à¹„à¸§à¹‰) à¹ƒà¸Šà¹‰ override à¸„à¹ˆà¸²à¸—à¸µà¹ˆ client à¸ªà¹ˆà¸‡à¸¡à¸²
    private function enforcedBranchId(?int $requested): int
    {
        if ($requested) {
            return $requested;
        }
        
        $device = request()->attributes->get('pos_device');
        if ($device) {
            $branchId = (int) ($device->branch_id ?: auth()->user()?->branch_id);
        } else {
            $branchId = (int) (auth()->user()?->branch_id);
        }
        
        if (! $branchId) {
            $branchId = (int) \App\Models\Branch::where('is_active', true)->value('id') ?: 1;
        }
        return $branchId;
    }

    private function enforcedCashierId($requested): ?int
    {
        $requested = $requested ? (int) $requested : null;
        $device = request()->attributes->get('pos_device');
        if ($device) {
            // à¸¢à¸·à¸™à¸¢à¸±à¸™ PIN à¸šà¸™à¹€à¸„à¸£à¸·à¹ˆà¸­à¸‡à¸™à¸µà¹‰à¹à¸¥à¹‰à¸§ = à¸‚à¸²à¸¢à¹„à¸”à¹‰à¹ƒà¸™à¸Šà¸·à¹ˆà¸­à¸„à¸™à¸™à¸±à¹‰à¸™à¸„à¸™à¹€à¸”à¸µà¸¢à¸§ à¸ªà¹ˆà¸‡ cashier_id
            // à¸‚à¸­à¸‡à¸„à¸™à¸­à¸·à¹ˆà¸™à¸¡à¸²à¹„à¸¡à¹ˆà¸œà¹ˆà¸²à¸™ à¹à¸¡à¹‰à¸ˆà¸°à¸­à¸¢à¸¹à¹ˆà¸ªà¸²à¸‚à¸²à¹€à¸”à¸µà¸¢à¸§à¸à¸±à¸™à¸à¹‡à¸•à¸²à¸¡
            $verified = $device->verifiedCashierId();
            if ($verified !== null) {
                return ($requested === null || $requested === $verified) ? $verified : null;
            }

            // à¸¢à¸±à¸‡à¹„à¸¡à¹ˆà¸¢à¸·à¸™à¸¢à¸±à¸™: à¹‚à¸«à¸¡à¸”à¹€à¸‚à¹‰à¸¡à¸šà¸±à¸‡à¸„à¸±à¸šà¹ƒà¸«à¹‰à¹ƒà¸ªà¹ˆ PIN à¸à¹ˆà¸­à¸™, à¹‚à¸«à¸¡à¸”à¸›à¸à¸•à¸´à¸¢à¸­à¸¡à¹ƒà¸«à¹‰à¸‚à¸²à¸¢à¸•à¹ˆà¸­à¹„à¸”à¹‰
            // à¹€à¸žà¸·à¹ˆà¸­à¹„à¸¡à¹ˆà¹ƒà¸«à¹‰à¹€à¸„à¸£à¸·à¹ˆà¸­à¸‡à¸—à¸µà¹ˆà¹€à¸›à¸´à¸”à¸„à¹‰à¸²à¸‡à¸‚à¹‰à¸²à¸¡à¸„à¸·à¸™à¸–à¸¹à¸à¸•à¸±à¸”à¸à¸¥à¸²à¸‡à¸à¸°à¸•à¸­à¸™à¸­à¸±à¸›à¹€à¸”à¸•
            // à¸–à¹‰à¸²à¹„à¸¡à¹ˆà¸•à¹‰à¸­à¸‡à¹ƒà¸ªà¹ˆ PIN à¹à¸¥à¸°à¹„à¸¡à¹ˆà¹„à¸”à¹‰à¸£à¸°à¸šà¸¸à¸£à¸«à¸±à¸ªà¸¡à¸² à¹ƒà¸«à¹‰à¸”à¸¶à¸‡à¸ˆà¸²à¸ cashier profile à¸‚à¸­à¸‡ user à¸—à¸µà¹ˆà¸¥à¹‡à¸­à¸à¸­à¸´à¸™à¸­à¸¢à¸¹à¹ˆ
            $resolvedId = $this->validatedCashierId($requested ?: auth()->user()?->posCashierProfile?->id);
            if (! $resolvedId) {
                // à¸”à¸¶à¸‡à¸žà¸™à¸±à¸à¸‡à¸²à¸™à¸‚à¸²à¸¢à¸„à¸™à¹à¸£à¸à¸¡à¸²à¹ƒà¸Šà¹‰à¹€à¸›à¸´à¸”à¸à¸°à¸­à¸±à¸•à¹‚à¸™à¸¡à¸±à¸•à¸´à¹€à¸žà¸·à¹ˆà¸­à¸„à¸§à¸²à¸¡à¸ªà¸°à¸”à¸§à¸
                $resolvedId = \App\Models\Salesman::where('is_active', true)->value('id');
            }
            return self::requiresCashierPin() ? null : $resolvedId;
        }

        // POS à¸šà¸™à¹€à¸§à¹‡à¸š: à¹ƒà¸Šà¹‰ User à¸—à¸µà¹ˆà¸¥à¹‡à¸­à¸à¸­à¸´à¸™à¹€à¸›à¹‡à¸™à¸„à¸™à¸‚à¸²à¸¢à¹€à¸ªà¸¡à¸­
        $resolvedId = $this->validatedCashierId(auth()->user()?->posCashierProfile?->id ?: $requested);
        if (! $resolvedId) {
            return null;
        }
        return $resolvedId;
    }

    /** à¸£à¸«à¸±à¸ªà¸žà¸™à¸±à¸à¸‡à¸²à¸™à¸—à¸µà¹ˆ client à¸ªà¹ˆà¸‡à¸¡à¸²à¹ƒà¸Šà¹‰à¹„à¸”à¹‰à¸ˆà¸£à¸´à¸‡à¹„à¸«à¸¡ (à¸¢à¸±à¸‡à¹ƒà¸Šà¹‰à¸‡à¸²à¸™à¸­à¸¢à¸¹à¹ˆ) */
    private function validatedCashierId(?int $requested): ?int
    {
        if (! $requested) {
            return null;
        }

        $cashier = Salesman::whereKey($requested)->where('is_active', true)->first();
        if (! $cashier) {
            return null;
        }

        // ALWAYS ALLOW CROSS-BRANCH SHIFT OPENING
        return (int) $cashier->id;
    }

    /** à¹€à¸›à¸´à¸”à¸—à¸µà¹ˆà¸•à¸±à¹‰à¸‡à¸„à¹ˆà¸² pos_require_cashier_pin à¹€à¸¡à¸·à¹ˆà¸­à¸•à¸±à¹‰à¸‡ PIN à¹ƒà¸«à¹‰à¹à¸„à¸Šà¹€à¸Šà¸µà¸¢à¸£à¹Œà¸„à¸£à¸šà¸—à¸¸à¸à¸„à¸™à¹à¸¥à¹‰à¸§ */
    public static function requiresCashierPin(): bool
    {
        return AppSetting::get('pos_require_cashier_pin') === '1';
    }

    // Active buy-N campaigns (à¸‹à¸·à¹‰à¸­à¸„à¸£à¸šà¹à¸–à¸¡/à¸¥à¸”) for this branch; the POS cart
    // auto-adds gift lines and set discounts from this list.
    public function promotions(Request $request): JsonResponse
    {
        $branchId = $this->enforcedBranchId((int) $request->query('branch_id', 0));

        $promotions = QtyPromotion::with('freeProduct:id,sku_code,name_th')
            ->runningToday()
            ->where(fn ($w) => $w->whereNull('branch_id')->orWhere('branch_id', $branchId))
            ->get(['id', 'code', 'name', 'promo_type', 'product_id', 'min_qty',
                'free_product_id', 'free_qty', 'discount_type', 'discount_value', 'bundle_price']);

        return response()->json($promotions);
    }

    // Member lookup for attaching a member to the bill (à¸ªà¸°à¸ªà¸¡/à¹à¸¥à¸à¹à¸•à¹‰à¸¡)
    public function members(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        if ($q === '') {
            return response()->json([]);
        }

        $members = Member::where('is_active', true)
            ->where(fn ($w) => $w
                ->where('member_code', 'ilike', "%{$q}%")
                ->orWhere('name', 'ilike', "%{$q}%")
                ->orWhere('phone', 'ilike', "%{$q}%")
            )
            ->orderBy('member_code')
            ->limit(20)
            ->get(['id', 'member_code', 'name', 'phone', 'points']);

        return response()->json($members);
    }

    public function quickAddProduct(Request $request): JsonResponse
    {
        $data = $request->validate([
            'barcode' => 'required|string',
            'name_th' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $unit = \App\Models\ProductUnit::firstOrCreate(['name' => 'à¸Šà¸´à¹‰à¸™']);

        $product = \App\Models\Product::create([
            'sku_code' => $data['barcode'],
            'name_th' => $data['name_th'],
            'default_price' => $data['price'],
            'base_unit_id' => $unit->id,
            'is_active' => true,
        ]);

        $product->barcodes()->create([
            'barcode' => $data['barcode'],
            'unit_id' => $unit->id,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'à¹€à¸žà¸´à¹ˆà¸¡à¸ªà¸´à¸™à¸„à¹‰à¸²à¸ªà¸³à¹€à¸£à¹‡à¸ˆ',
            'product' => $product
        ]);
    }

    public function products(Request $request, ?PosPriceScheduleService $priceSchedules = null): JsonResponse
    {
        $priceSchedules ??= app(PosPriceScheduleService::class);
        $q = trim((string) $request->query('q', ''));
        $categoryId = $request->query('category_id');
        $branchId = $this->enforcedBranchId((int) $request->query('branch_id', 0));
        $exact = $request->boolean('exact');
        // A scanner must never be ambiguous with a newly allocated SKU.  Keep
        // lookup modes explicit: scanner/scale labels use barcode, while manual
        // code entry can intentionally ask for SKU after barcode lookup misses.
        $lookup = (string) $request->query('lookup', 'mixed');
        if (! in_array($lookup, ['barcode', 'sku', 'mixed'], true)) {
            $lookup = 'mixed';
        }
        $all = $request->boolean('all'); // POS desktop à¸”à¸¶à¸‡à¹à¸„à¸•à¸•à¸²à¸¥à¹‡à¸­à¸à¸—à¸±à¹‰à¸‡à¸«à¸¡à¸”à¹€à¸à¹‡à¸š offline

        // à¸•à¸²à¸£à¸²à¸‡à¸£à¸²à¸„à¸²à¹à¸šà¸šà¸Šà¸±à¹‰à¸™à¸‹à¹‰à¸­à¸™: à¸•à¸²à¸£à¸²à¸‡à¸ªà¸²à¸‚à¸² (override) -> à¸•à¸²à¸£à¸²à¸‡à¸«à¸¥à¸±à¸ (default) -> default_price
        // à¸ªà¸²à¸‚à¸²à¸—à¸µà¹ˆà¸•à¸²à¸£à¸²à¸‡à¸•à¸±à¸§à¹€à¸­à¸‡à¸¡à¸µà¸ªà¸´à¸™à¸„à¹‰à¸²à¹„à¸¡à¹ˆà¸„à¸£à¸š (à¸”à¸­à¸™à¸à¸¥à¸²à¸‡/à¸§à¸²à¸£à¸´à¸™/à¸›à¸¥à¸²à¸”à¸¸à¸) à¸ˆà¸°à¹„à¸”à¹‰à¸£à¸²à¸„à¸²à¸ˆà¸²à¸à¸•à¸²à¸£à¸²à¸‡à¸«à¸¥à¸±à¸
        // à¹„à¸¡à¹ˆà¸•à¸à¹„à¸› 1.00 à¸šà¸²à¸—. à¸›à¹‰à¸²à¸¢à¹€à¸„à¸£à¸·à¹ˆà¸­à¸‡à¸Šà¸±à¹ˆà¸‡ = à¸£à¸²à¸„à¸²à¸‚à¸²à¸¢à¸›à¸¥à¸µà¸à¸•à¸²à¸£à¸²à¸‡à¸™à¸µà¹‰à¹€à¸Šà¹ˆà¸™à¸à¸±à¸™.
        $branch = $branchId ? Branch::find($branchId) : null;
        $defaultTableId = PriceTable::where('is_default', true)->value('id');
        // à¸£à¸²à¸„à¸²à¸›à¸à¸•à¸´à¸à¸¥à¸²à¸‡à¹ƒà¸Šà¹‰à¸£à¹ˆà¸§à¸¡à¸à¸±à¸™à¸—à¸¸à¸ POS à¸—à¸¸à¸à¸ªà¸²à¸‚à¸² à¸£à¸²à¸„à¸²à¸—à¸µà¹ˆà¸•à¹ˆà¸²à¸‡à¸•à¸²à¸¡à¸ªà¸²à¸‚à¸²à¸•à¹‰à¸­à¸‡à¸—à¸³à¸œà¹ˆà¸²à¸™
        // à¹‚à¸›à¸£à¹‚à¸¡à¸Šà¸±à¹ˆà¸™/à¸™à¸²à¸—à¸µà¸—à¸­à¸‡à¸—à¸µà¹ˆà¸¡à¸µà¸Šà¹ˆà¸§à¸‡à¹€à¸§à¸¥à¸²à¹€à¸—à¹ˆà¸²à¸™à¸±à¹‰à¸™ à¹€à¸žà¸·à¹ˆà¸­à¹„à¸¡à¹ˆà¹ƒà¸«à¹‰à¸£à¸²à¸„à¸²à¸›à¸à¸•à¸´à¹à¸•à¹ˆà¸¥à¸°à¸ªà¸²à¸‚à¸²à¸ªà¸±à¸šà¸ªà¸™à¸à¸±à¸™.
        $branchTableId = null;
        $priceTableIds = array_values(array_filter([$defaultTableId]));

        $products = Product::where('is_active', true)
            ->with(['barcodes' => fn ($query) => $query->where('is_active', true)->with('unit'), 'category'])
            ->when($categoryId, fn ($query) => $query->where('product_category_id', $categoryId))
            ->when($q !== '' && $exact && $lookup === 'barcode', fn ($query) => $query
                ->whereHas('barcodes', fn ($barcodeQuery) => $barcodeQuery->where('is_active', true)->where('barcode', $q)))
            ->when($q !== '' && $exact && $lookup === 'sku', fn ($query) => $query->where('sku_code', $q))
            ->when($q !== '' && $exact && $lookup === 'mixed', fn ($query) => $query->where(fn ($w) => $w
                ->where('sku_code', $q)
                ->orWhereHas('barcodes', fn ($barcodeQuery) => $barcodeQuery->where('is_active', true)->where('barcode', $q))))
            ->when($q !== '' && ! $exact, fn ($query) => $query->where(fn ($w) => $w
                ->where('sku_code', 'ilike', "%{$q}%")
                ->orWhere('name_th', 'ilike', "%{$q}%")
                ->orWhereHas('barcodes', fn ($barcodeQuery) => $barcodeQuery->where('is_active', true)->where('barcode', 'ilike', "%{$q}%"))
            ))
            ->orderBy('name_th')
            ->when(! $all, fn ($query) => $query->limit(100))
            ->get([
                'id',
                'sku_code',
                'image_url',
                'name_th',
                'default_price',
                'average_cost',
                'is_vat',
                'minimum_margin_percent',
                'margin_control_policy',
                'product_category_id',
            ]);

        $priceRows = $priceTableIds === []
            ? collect()
            : ProductPrice::whereIn('price_table_id', $priceTableIds)
                ->whereIn('product_id', $products->pluck('id'))
                ->where('is_active', true)
                ->get(['product_id', 'price_table_id', 'unit_id', 'price'])
                ->groupBy('product_id');

        // à¸•à¸±à¸§à¸„à¸¹à¸“à¸«à¸™à¹ˆà¸§à¸¢ (id -> qty_per_base_unit) à¹ƒà¸Šà¹‰à¹€à¸¥à¸·à¸­à¸ "à¸£à¸²à¸„à¸²à¸«à¸™à¹ˆà¸§à¸¢à¸à¸²à¸™" à¹€à¸¡à¸·à¹ˆà¸­à¹„à¸¡à¹ˆà¸¡à¸µà¸£à¸²à¸„à¸² unit_id=null
        $unitFactors = ProductUnit::pluck('qty_per_base_unit', 'id');

        $products = $products->map(function ($p) use ($priceRows, $q, $exact, $lookup, $branchTableId, $defaultTableId, $unitFactors) {
            $matchedBarcode = $exact && $lookup !== 'sku' && $q !== ''
                ? $p->barcodes->first(fn ($barcode) => $barcode->barcode === $q)
                : null;
            $scalePlu = $p->barcodes->first(fn ($barcode) => $this->isScalePlu($barcode->barcode))?->barcode
                ?? ($this->isScalePlu($p->sku_code) ? $p->sku_code : null);

            $p->scale_plu = $scalePlu;
            $p->is_scale = (bool) ($scalePlu || $this->productHasScaleName($p->name_th));

            $rows = $priceRows->get($p->id, collect());

            // à¸£à¸²à¸„à¸²à¸ˆà¸²à¸à¸•à¸²à¸£à¸²à¸‡à¸—à¸µà¹ˆà¸£à¸°à¸šà¸¸: à¸¢à¸´à¸‡à¸šà¸²à¸£à¹Œà¹‚à¸„à¹‰à¸”à¸¡à¸µà¸«à¸™à¹ˆà¸§à¸¢ -> à¸£à¸²à¸„à¸²à¸«à¸™à¹ˆà¸§à¸¢à¸™à¸±à¹‰à¸™, à¹„à¸¡à¹ˆà¸‡à¸±à¹‰à¸™ -> à¸£à¸²à¸„à¸²à¸à¸²à¸™ (unit_id null)
            $priceFromTable = function ($tableId) use ($rows, $matchedBarcode, $unitFactors) {
                if (! $tableId) {
                    return null;
                }
                $forTable = $rows->where('price_table_id', (int) $tableId);
                if ($forTable->isEmpty()) {
                    return null;
                }
                if ($matchedBarcode) {
                    $unitPrice = $forTable->firstWhere('unit_id', $matchedBarcode->unit_id);
                    if ($unitPrice) {
                        return ['price' => (float) $unitPrice->price, 'source' => 'unit_price_table'];
                    }
                }
                $basePrice = $forTable->firstWhere('unit_id', null);
                if ($basePrice) {
                    return ['price' => (float) $basePrice->price, 'source' => 'price_table'];
                }
                // à¹„à¸¡à¹ˆà¸¡à¸µà¸£à¸²à¸„à¸²à¸à¸²à¸™ (unit_id null) -> à¹ƒà¸Šà¹‰à¸£à¸²à¸„à¸²à¸«à¸™à¹ˆà¸§à¸¢à¹€à¸¥à¹‡à¸à¸ªà¸¸à¸” (à¸«à¸™à¹ˆà¸§à¸¢à¸à¸²à¸™ à¹€à¸Šà¹ˆà¸™ à¸ªà¸´à¸™à¸„à¹‰à¸²à¸Šà¸±à¹ˆà¸‡ = à¸à¸.)
                $smallest = $forTable->sortBy(fn ($r) => (float) ($unitFactors[$r->unit_id] ?? 1))->first();

                return $smallest ? ['price' => (float) $smallest->price, 'source' => 'unit_price_table'] : null;
            };

            if ($matchedBarcode && $matchedBarcode->price !== null && (float) $matchedBarcode->price > 0) {
                $p->pos_price = (float) $matchedBarcode->price;
                $p->price_source = 'barcode';
            } else {
                // à¸•à¸²à¸£à¸²à¸‡à¸ªà¸²à¸‚à¸² (override) à¸à¹ˆà¸­à¸™ à¹à¸¥à¹‰à¸§ fallback à¸•à¸²à¸£à¸²à¸‡à¸«à¸¥à¸±à¸ à¹à¸¥à¹‰à¸§à¸„à¹ˆà¸­à¸¢ default_price
                $hit = $priceFromTable($branchTableId) ?? $priceFromTable($defaultTableId);
                if ($hit) {
                    $p->pos_price = $hit['price'];
                    $p->price_source = $hit['source'];
                } else {
                    $p->pos_price = (float) $p->default_price;
                    $p->price_source = 'default';
                }
            }

            if ($matchedBarcode) {
                $p->matched_barcode = [
                    'barcode' => $matchedBarcode->barcode,
                    // à¸ªà¹ˆà¸‡à¸›à¸£à¸°à¹€à¸ à¸—à¹„à¸›à¸”à¹‰à¸§à¸¢ à¹€à¸„à¸£à¸·à¹ˆà¸­à¸‡ POS à¸—à¸µà¹ˆà¸—à¸³à¸‡à¸²à¸™à¸­à¸­à¸Ÿà¹„à¸¥à¸™à¹Œà¸•à¹‰à¸­à¸‡à¸£à¸¹à¹‰à¸§à¹ˆà¸²à¸ˆà¸°à¸›à¸à¸´à¸šà¸±à¸•à¸´à¸à¸±à¸š
                    // à¸šà¸²à¸£à¹Œà¹‚à¸„à¹‰à¸”à¸™à¸µà¹‰à¸¢à¸±à¸‡à¹„à¸‡ à¹‚à¸”à¸¢à¹„à¸¡à¹ˆà¸•à¹‰à¸­à¸‡à¹€à¸”à¸²à¸ˆà¸²à¸à¸£à¸¹à¸›à¸£à¹ˆà¸²à¸‡à¸‚à¸­à¸‡à¸•à¸±à¸§à¹€à¸¥à¸‚
                    'barcode_type' => $matchedBarcode->barcode_type,
                    'unit_id' => $matchedBarcode->unit_id,
                    'unit_name' => $matchedBarcode->unit?->cleanName(),
                    'unit_factor' => (float) $matchedBarcode->unit_factor,
                ];
            }

            $p->normal_price = (float) $p->pos_price;

            return $p;
        });

        // Normal prices stay in the price table. A POS schedule is an approved
        // time-bound override and is sent to the device in advance so it can
        // activate at the exact time even while the device is offline.
        $catalogSchedules = $priceSchedules->catalog($products->pluck('id')->all(), $branchId, now())
            ->groupBy('product_id');
        $activeSchedules = $priceSchedules->active($products->pluck('id')->all(), $branchId, now())
            ->keyBy(fn ($schedule) => $schedule->product_id.':'.($schedule->unit_id ?? 'base'));

        $products = $products->map(function ($p) use ($catalogSchedules, $activeSchedules) {
            $p->base_pos_price = (float) $p->pos_price;
            $barcodeUnitId = $p->matched_barcode['unit_id'] ?? null;
            $schedule = $barcodeUnitId
                ? $activeSchedules->get($p->id.':'.$barcodeUnitId)
                : null;
            $schedule ??= $activeSchedules->get($p->id.':base');
            if ($schedule) {
                $p->pos_price = (float) $schedule->price;
                $p->price_source = 'scheduled_price';
            }
            $p->scheduled_prices = $catalogSchedules->get($p->id, collect())->map(fn ($row) => [
                'id' => $row->id,
                'branch_id' => $row->branch_id,
                'unit_id' => $row->unit_id,
                'price' => (float) $row->price,
                'effective_from' => $row->effective_from->toIso8601String(),
                'effective_to' => $row->effective_to?->toIso8601String(),
            ])->values();
            $p->normal_price = (float) $p->pos_price;

            return $p;
        });

        // à¸£à¸²à¸„à¸²à¸¥à¸”à¸•à¸²à¸¡à¸Šà¹ˆà¸§à¸‡à¸§à¸±à¸™à¸—à¸µà¹ˆ: à¹ƒà¸Šà¹‰à¸à¹ˆà¸­à¸™à¸£à¸²à¸„à¸²à¸›à¸à¸•à¸´ à¹€à¸‰à¸žà¸²à¸°à¹‚à¸›à¸£à¹‚à¸¡à¸Šà¸±à¸™à¸£à¸²à¸¢à¸ªà¸´à¸™à¸„à¹‰à¸²à¸—à¸µà¹ˆà¹„à¸¡à¹ˆà¸•à¹‰à¸­à¸‡à¸£à¸­
        // à¹€à¸‡à¸·à¹ˆà¸­à¸™à¹„à¸‚à¸¢à¸­à¸”à¸šà¸´à¸¥/à¸ˆà¸³à¸™à¸§à¸™à¸«à¸¥à¸²à¸¢à¸Šà¸´à¹‰à¸™ (à¹€à¸‡à¸·à¹ˆà¸­à¸™à¹„à¸‚à¹€à¸«à¸¥à¹ˆà¸²à¸™à¸±à¹‰à¸™à¸„à¸³à¸™à¸§à¸“à¹ƒà¸™à¸•à¸°à¸à¸£à¹‰à¸²à¹à¸¢à¸à¸•à¹ˆà¸²à¸‡à¸«à¸²à¸).
        $today = now()->toDateString();
        $discountsByProduct = Promotion::where('is_active', true)
            ->whereIn('product_id', $products->pluck('id'))
            ->where(fn ($w) => $w->whereNull('starts_at')->orWhere('starts_at', '<=', $today))
            ->where(fn ($w) => $w->whereNull('ends_at')->orWhere('ends_at', '>=', $today))
            ->where(fn ($w) => $w->whereNull('min_qty')->orWhere('min_qty', '<=', 1))
            ->whereNull('min_amount')
            ->get()
            ->groupBy('product_id');

        $products = $products->map(function ($p) use ($discountsByProduct) {
            $base = (float) $p->pos_price;
            $best = $discountsByProduct->get($p->id, collect())
                ->map(function ($promotion) use ($base) {
                    if ((float) $promotion->discount_amount > 0) {
                        return max(0, $base - (float) $promotion->discount_amount);
                    }
                    if ((float) $promotion->discount_percent > 0) {
                        return max(0, round($base * (1 - ((float) $promotion->discount_percent / 100)), 2));
                    }

                    return $base;
                })->min();

            if ($best !== null && $best < $base) {
                $p->original_price = $base;
                $p->pos_price = $best;
                $p->is_promotion = true;
                $p->price_source = 'promotion';
            } else {
                $p->is_promotion = false;
            }

            return $p;
        });

        // à¸£à¸²à¸„à¸²à¸™à¸²à¸—à¸µà¸—à¸­à¸‡: override with the active flash-sale price, if any, for
        // this branch and current date/time/day-of-week window.
        $now = now();
        $flashItemsByProduct = FlashSaleItem::with('flashSale')
            ->whereIn('product_id', $products->pluck('id'))
            ->whereHas('flashSale', function ($query) use ($branchId, $now) {
                $query->where('is_active', true)
                    ->where('starts_date', '<=', $now->toDateString())
                    ->where(fn ($w) => $w->whereNull('ends_date')->orWhere('ends_date', '>=', $now->toDateString()))
                    ->where(fn ($w) => $w->whereNull('branch_id')->orWhere('branch_id', $branchId));
            })
            ->get()
            ->filter(fn ($item) => $item->flashSale->isRunningAt($now))
            ->groupBy('product_id')
            ->map(fn ($items) => $items->sortBy('flash_price')->first());

        $products = $products->map(function ($p) use ($flashItemsByProduct) {
            $flashItem = $flashItemsByProduct->get($p->id);
            if ($flashItem && (float) $flashItem->flash_price < (float) $p->pos_price) {
                $p->original_price ??= $p->pos_price;
                $p->pos_price = (float) $flashItem->flash_price;
                $p->is_flash_sale = true;
                $p->price_source = 'flash_sale';
            } else {
                $p->is_flash_sale = false;
            }

            return $p;
        });

        $vatRate = (float) (DB::table('vat_rates')
            ->where('effective_from', '<=', now()->toDateString())
            ->where(fn ($query) => $query->whereNull('effective_to')->orWhere('effective_to', '>=', now()->toDateString()))
            ->orderByDesc('effective_from')->value('rate_percent') ?? 7);
        $products->each(function ($product) use ($vatRate) {
            $netRevenue = DecimalMath::round($product->pos_price, DecimalMath::COST_SCALE);
            if ($product->is_vat && $vatRate > 0) {
                $netRevenue = DecimalMath::divide(
                    DecimalMath::multiply($netRevenue, 100),
                    DecimalMath::add(100, $vatRate),
                    DecimalMath::COST_SCALE,
                );
            }
            $margin = DecimalMath::compare($netRevenue, 0) > 0
                ? DecimalMath::multiply(
                    DecimalMath::divide(
                        DecimalMath::subtract($netRevenue, $product->average_cost),
                        $netRevenue,
                        DecimalMath::COST_SCALE,
                    ),
                    100,
                    DecimalMath::COST_SCALE,
                )
                : '-1000';
            $product->margin_percent = (float) DecimalMath::round($margin, 4);
            $product->margin_warning = $product->minimum_margin_percent !== null
                && DecimalMath::compare($margin, $product->minimum_margin_percent) < 0;
            $product->makeHidden([
                'average_cost',
                'is_vat',
                'minimum_margin_percent',
                'margin_control_policy',
            ]);
        });

        // à¸ªà¸•à¹Šà¸­à¸à¸„à¸‡à¹€à¸«à¸¥à¸·à¸­ "à¸‚à¸­à¸‡à¸ªà¸²à¸‚à¸²à¸™à¸µà¹‰" (à¸„à¸¥à¸±à¸‡à¹ƒà¸„à¸£à¸„à¸¥à¸±à¸‡à¸¡à¸±à¸™) - à¸”à¸¶à¸‡à¸ˆà¸²à¸à¸„à¸¥à¸±à¸‡à¹€à¸£à¸´à¹ˆà¸¡à¸•à¹‰à¸™à¸‚à¸­à¸‡à¸ªà¸²à¸‚à¸²
        // Fetch TOTAL stock across all warehouse locations
        $stockByProduct = StockBalance::whereIn('product_id', $products->pluck('id'))
            ->selectRaw('product_id, SUM(on_hand_qty) as total')
            ->groupBy('product_id')
            ->pluck('total', 'product_id');
        $products->each(function ($p) use ($stockByProduct) {
            $p->stock_qty = (float) ($stockByProduct[$p->id] ?? 0);
        });

        return response()->json($products);
    }

    /**
     * à¸šà¸²à¸£à¹Œà¹‚à¸„à¹‰à¸”à¸—à¸µà¹ˆà¹€à¸„à¸£à¸·à¹ˆà¸­à¸‡à¸ªà¹à¸à¸™à¸¡à¸² à¸Šà¸µà¹‰à¹„à¸›à¸ªà¸´à¸™à¸„à¹‰à¸²à¸„à¸™à¸¥à¸°à¸•à¸±à¸§à¸à¸±à¸šà¸—à¸µà¹ˆà¸ªà¹ˆà¸‡à¸¡à¸²à¸‚à¸²à¸¢à¸«à¸£à¸·à¸­à¹€à¸›à¸¥à¹ˆà¸²
     *
     * à¹€à¸„à¸£à¸·à¹ˆà¸­à¸‡à¸—à¸µà¹ˆà¸­à¸­à¸Ÿà¹„à¸¥à¸™à¹Œà¸­à¸¢à¸¹à¹ˆà¹ƒà¸Šà¹‰à¹à¸„à¸•à¸•à¸²à¸¥à¹‡à¸­à¸à¸—à¸µà¹ˆ sync à¹„à¸§à¹‰ à¸–à¹‰à¸²à¹à¸„à¸•à¸•à¸²à¸¥à¹‡à¸­à¸à¹€à¸à¹ˆà¸²à¸«à¸£à¸·à¸­à¸–à¸¹à¸à¹à¸à¹‰
     * à¸šà¸²à¸£à¹Œà¹‚à¸„à¹‰à¸”à¸­à¸²à¸ˆà¸Šà¸µà¹‰à¸„à¸™à¸¥à¸°à¸•à¸±à¸§à¸à¸±à¸šà¸›à¸±à¸ˆà¸ˆà¸¸à¸šà¸±à¸™ à¸›à¸¥à¹ˆà¸­à¸¢à¸œà¹ˆà¸²à¸™à¹à¸¥à¹‰à¸§à¸ªà¸•à¹Šà¸­à¸à¸ˆà¸°à¸•à¸±à¸”à¸œà¸´à¸”à¸•à¸±à¸§
     * à¸›à¹‰à¸²à¸¢à¹€à¸„à¸£à¸·à¹ˆà¸­à¸‡à¸Šà¸±à¹ˆà¸‡à¹„à¸¡à¹ˆà¹„à¸”à¹‰à¸¥à¸‡à¸—à¸°à¹€à¸šà¸µà¸¢à¸™à¹€à¸›à¹‡à¸™à¸šà¸²à¸£à¹Œà¹‚à¸„à¹‰à¸” à¸ˆà¸¶à¸‡à¸‚à¹‰à¸²à¸¡à¸à¸²à¸£à¸•à¸£à¸§à¸ˆà¸™à¸µà¹‰
     *
     * @param  array<int, array<string, mixed>>  $items
     */
    private function barcodeBelongsToAnotherProduct(array $items): ?string
    {
        foreach ($items as $item) {
            $barcode = trim((string) ($item['barcode'] ?? ''));
            if ($barcode === '' || ($item['barcode_type'] ?? null) === BarcodePolicy::SCALE_WEIGHT) {
                continue;
            }

            $ownerId = ProductBarcode::where('barcode', $barcode)->value('product_id');
            if ($ownerId !== null && (int) $ownerId !== (int) $item['product_id']) {
                return "à¸šà¸²à¸£à¹Œà¹‚à¸„à¹‰à¸” {$barcode} à¹€à¸›à¹‡à¸™à¸‚à¸­à¸‡à¸ªà¸´à¸™à¸„à¹‰à¸²à¸£à¸«à¸±à¸ªà¸­à¸·à¹ˆà¸™à¹à¸¥à¹‰à¸§ à¸à¸£à¸¸à¸“à¸² sync à¹à¸„à¸•à¸•à¸²à¸¥à¹‡à¸­à¸à¹ƒà¸«à¸¡à¹ˆà¸à¹ˆà¸­à¸™à¸‚à¸²à¸¢";
            }
        }

        return null;
    }

    private function isScalePlu(?string $value): bool
    {
        return is_string($value) && preg_match('/^80[01][0-9]{3}$/', $value) === 1;
    }

    private function productHasScaleName(?string $name): bool
    {
        return is_string($name) && preg_match('/\x{0E0A}\x{0E31}\x{0E48}\x{0E07}|\x{0E0B}\x{0E31}\x{0E48}\x{0E07}/u', $name) === 1;
    }

    public function activeShift(Request $request): JsonResponse
    {
        $data = $request->validate([
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'cashier_id' => ['nullable', 'integer', 'exists:salesmen,id'],
        ]);

        $branchId = $this->enforcedBranchId(isset($data['branch_id']) ? (int) $data['branch_id'] : null);
        $cashierId = $this->enforcedCashierId(isset($data['cashier_id']) ? (int) $data['cashier_id'] : null);
        $shift = $this->findOpenShift($branchId, $cashierId);

        return response()->json(['shift' => $shift ? $this->shiftPayload($shift) : null]);
    }

    public function openShift(Request $request): JsonResponse
    {
        $data = $request->validate([
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'cashier_id' => ['nullable', 'integer', 'exists:salesmen,id'],
            'opening_cash' => ['required', 'numeric', 'min:0'],
            'opening_note' => ['nullable', 'string', 'max:500'],
        ]);

        // à¸šà¸±à¸‡à¸„à¸±à¸šà¹ƒà¸Šà¹‰à¸„à¹ˆà¸²à¸–à¹‰à¸²à¹„à¸¡à¹ˆà¸¡à¸µ: à¸¢à¸¶à¸”à¸ˆà¸²à¸à¸ªà¸²à¸‚à¸²/à¹à¸„à¸Šà¹€à¸Šà¸µà¸¢à¸£à¹Œà¸‚à¸­à¸‡ user à¸—à¸µà¹ˆ login à¹€à¸›à¹‡à¸™à¸«à¸¥à¸±à¸
        $data['branch_id'] = $this->enforcedBranchId((int) ($data['branch_id'] ?? null));
        $data['cashier_id'] = $this->enforcedCashierId((int) ($data['cashier_id'] ?? null));
        if (! $data['cashier_id']) {
            return response()->json(['success' => false, 'message' => 'à¸šà¸±à¸à¸Šà¸µà¸™à¸µà¹‰à¸¢à¸±à¸‡à¹„à¸¡à¹ˆà¹„à¸”à¹‰à¸à¸³à¸«à¸™à¸”à¸£à¸«à¸±à¸ªà¸žà¸™à¸±à¸à¸‡à¸²à¸™à¸‚à¸²à¸¢ à¸•à¸´à¸”à¸•à¹ˆà¸­à¸œà¸¹à¹‰à¸”à¸¹à¹à¸¥à¸£à¸°à¸šà¸š'], 422);
        }

        $existing = $this->findOpenShift((int) $data['branch_id'], (int) $data['cashier_id']);
        if ($existing) {
            return response()->json(['success' => true, 'shift' => $this->shiftPayload($existing), 'message' => 'à¸¡à¸µà¸à¸°à¹€à¸›à¸´à¸”à¸­à¸¢à¸¹à¹ˆà¹à¸¥à¹‰à¸§']);
        }

        $terminal = $this->terminalForBranch((int) $data['branch_id']);

        $shift = PosShift::create([
            'branch_id' => $data['branch_id'],
            'pos_terminal_id' => $terminal->id,
            'cashier_id' => $data['cashier_id'],
            'cashier_user_id' => $this->cashierUserId($data['cashier_id']),
            'shift_no' => $this->nextShiftNo((int) $data['branch_id']),
            'opened_at' => now(),
            'opening_cash' => $data['opening_cash'],
            'expected_cash' => $data['opening_cash'],
            'status' => 'open',
            'opening_note' => $data['opening_note'] ?? null,
        ]);

        return response()->json(['success' => true, 'shift' => $this->shiftPayload($shift)]);
    }

    public function closeShift(Request $request, ?CashBookPostingService $cashBook = null): JsonResponse
    {
        // à¸›à¸¥à¹ˆà¸­à¸¢à¹ƒà¸«à¹‰ resolve à¹€à¸­à¸‡à¹„à¸”à¹‰ à¹€à¸«à¸¡à¸·à¸­à¸™ products() â€” à¹€à¸—à¸ªà¸•à¹Œà¹€à¸”à¸´à¸¡à¹€à¸£à¸µà¸¢à¸ action à¸•à¸£à¸‡ à¹† à¸”à¹‰à¸§à¸¢à¸­à¸²à¸£à¹Œà¸à¸´à¸§à¹€à¸¡à¸™à¸•à¹Œà¹€à¸”à¸µà¸¢à¸§
        $cashBook ??= app(CashBookPostingService::class);

        $data = $request->validate([
            'shift_id' => ['required', 'integer', 'exists:pos_shifts,id'],
            'counted_cash' => ['required', 'numeric', 'min:0'],
            'closing_note' => ['nullable', 'string', 'max:500'],
        ]);

        $shift = PosShift::where('id', $data['shift_id'])->where('status', 'open')->first();
        if (! $shift) {
            return response()->json(['success' => false, 'message' => 'à¹„à¸¡à¹ˆà¸žà¸šà¸à¸°à¸—à¸µà¹ˆà¹€à¸›à¸´à¸”à¸­à¸¢à¸¹à¹ˆ'], 422);
        }
        $branchId = $this->enforcedBranchId((int) $shift->branch_id);
        $cashierId = $this->enforcedCashierId((int) $shift->cashier_id);
        if ($branchId !== (int) $shift->branch_id || $cashierId !== (int) $shift->cashier_id) {
            return response()->json(['success' => false, 'message' => 'à¸›à¸´à¸”à¸à¸°à¹„à¸”à¹‰à¹€à¸‰à¸žà¸²à¸°à¸à¸°à¸‚à¸­à¸‡à¸•à¸™à¹€à¸­à¸‡à¹ƒà¸™à¸ªà¸²à¸‚à¸²à¸™à¸µà¹‰'], 403);
        }

        $totals = $this->calculateShiftTotals($shift);
        $expectedCash = $this->expectedCash($shift, $totals);
        $countedCash = round((float) $data['counted_cash'], 2);

        $shift->update([
            'closed_at' => now(),
            'cash_sales' => $totals['cash'],
            'transfer_sales' => $totals['transfer'],
            'card_sales' => $totals['credit_card'],
            'cheque_sales' => $totals['cheque'],
            'expected_cash' => $expectedCash,
            'counted_cash' => $countedCash,
            'cash_difference' => round($countedCash - $expectedCash, 2),
            'receipt_count' => $totals['receipt_count'],
            'status' => 'closed',
            'closing_note' => $data['closing_note'] ?? null,
        ]);

        // à¹€à¸‡à¸´à¸™à¸ªà¸”à¸‚à¸­à¸‡à¸à¸°à¹€à¸‚à¹‰à¸²à¸ªà¸¡à¸¸à¸”à¹€à¸‡à¸´à¸™à¸ªà¸”à¸•à¸­à¸™à¸™à¸µà¹‰à¸„à¸£à¸±à¹‰à¸‡à¹€à¸”à¸µà¸¢à¸§ (idempotent à¸•à¸²à¸¡ shift id) â€” à¸à¸”à¸›à¸´à¸”à¸‹à¹‰à¸³à¹„à¸¡à¹ˆà¸¥à¸‡à¸‹à¹‰à¸³
        $cashBook->postShiftClose($shift->fresh(), (float) $totals['cash'], round($countedCash - $expectedCash, 2));

        return response()->json([
            'success' => true,
            'shift' => $this->shiftPayload($shift->fresh()),
            'report_url' => route('pos.shift.z-report', $shift),
        ]);
    }

    public function heldBills(Request $request): JsonResponse
    {
        $data = $request->validate([
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'cashier_id' => ['nullable', 'integer', 'exists:salesmen,id'],
        ]);
        $branchId = $this->enforcedBranchId((int) $data['branch_id']);

        $bills = PosHeldBill::with(['cashier:id,code,name', 'cashierUser:id,name,username', 'terminal:id,code,name'])
            ->where('branch_id', $branchId)
            ->where('status', 'held')
            ->orderByDesc('held_at')
            ->orderByDesc('id')
            ->limit(100)
            ->get()
            ->map(fn (PosHeldBill $bill) => [
                ...($bill->payload ?? []),
                'id' => $bill->id,
                'hold_no' => $bill->hold_no,
                'label' => $bill->note ?: $bill->hold_no,
                'createdAt' => $bill->held_at?->toIso8601String() ?? $bill->created_at?->toIso8601String(),
                'cashier_name' => $bill->cashierUser?->name ?? $bill->cashier?->name,
                'terminal_name' => $bill->terminal?->name,
                'total_amount' => (float) $bill->total_amount,
            ]);

        return response()->json(['success' => true, 'held_bills' => $bills]);
    }

    public function holdBill(Request $request): JsonResponse
    {
        $data = $request->validate([
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'shift_id' => ['required', 'integer', 'exists:pos_shifts,id'],
            'cashier_id' => ['required', 'integer', 'exists:salesmen,id'],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'label' => ['required', 'string', 'max:200'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'payload' => ['required', 'array'],
            'payload.cart' => ['required', 'array', 'min:1'],
            'payload.cart.*.id' => ['required', 'integer', 'exists:products,id'],
            'payload.cart.*.qty' => ['required', 'numeric', 'min:0.00000001'],
        ]);
        $branchId = $this->enforcedBranchId((int) $data['branch_id']);
        $cashierId = $this->enforcedCashierId((int) $data['cashier_id']);
        $shift = PosShift::whereKey($data['shift_id'])
            ->where('branch_id', $branchId)
            ->where('cashier_id', $cashierId)
            ->where('status', 'open')
            ->first();
        if (! $shift) {
            return response()->json(['success' => false, 'message' => 'à¸žà¸±à¸à¸šà¸´à¸¥à¹„à¸”à¹‰à¹€à¸‰à¸žà¸²à¸°à¸à¸°à¸—à¸µà¹ˆà¸à¸³à¸¥à¸±à¸‡à¹€à¸›à¸´à¸”à¸‚à¸­à¸‡à¸•à¸™à¹€à¸­à¸‡'], 422);
        }

        $bill = DB::transaction(function () use ($data, $branchId, $cashierId, $shift) {
            return PosHeldBill::create([
                'hold_no' => $this->nextHoldNo($branchId),
                'branch_id' => $branchId,
                'pos_terminal_id' => $shift->pos_terminal_id,
                'pos_shift_id' => $shift->id,
                'cashier_id' => $cashierId,
                'cashier_user_id' => $shift->cashier_user_id ?: $this->cashierUserId($cashierId),
                'held_by' => auth()->id(),
                'customer_id' => $data['customer_id'] ?? null,
                'total_amount' => $data['total_amount'],
                'status' => 'held',
                'note' => trim($data['label']),
                'payload' => $data['payload'],
                'held_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => "à¸žà¸±à¸à¸šà¸´à¸¥ {$bill->hold_no} à¹„à¸§à¹‰à¸ªà¹ˆà¸§à¸™à¸à¸¥à¸²à¸‡à¹à¸¥à¹‰à¸§",
            'held_bill_id' => $bill->id,
        ]);
    }

    public function resumeHeldBill(Request $request, PosHeldBill $heldBill): JsonResponse
    {
        $branchId = $this->enforcedBranchId((int) $heldBill->branch_id);
        if ($branchId !== (int) $heldBill->branch_id) {
            return response()->json(['success' => false, 'message' => 'à¹€à¸£à¸µà¸¢à¸à¸šà¸´à¸¥à¸žà¸±à¸à¸•à¹ˆà¸²à¸‡à¸ªà¸²à¸‚à¸²à¹„à¸¡à¹ˆà¹„à¸”à¹‰'], 403);
        }

        return DB::transaction(function () use ($heldBill) {
            $bill = PosHeldBill::whereKey($heldBill->id)->lockForUpdate()->firstOrFail();
            if ($bill->status !== 'held') {
                return response()->json(['success' => false, 'message' => 'à¸šà¸´à¸¥à¸™à¸µà¹‰à¸–à¸¹à¸à¹€à¸£à¸µà¸¢à¸à¹ƒà¸Šà¹‰à¸«à¸£à¸·à¸­à¸¢à¸à¹€à¸¥à¸´à¸à¹„à¸›à¹à¸¥à¹‰à¸§'], 409);
            }
            $bill->update(['status' => 'resumed', 'resumed_at' => now()]);

            return response()->json([
                'success' => true,
                'held_bill' => [
                    ...($bill->payload ?? []),
                    'id' => $bill->id,
                    'hold_no' => $bill->hold_no,
                    'label' => $bill->note,
                ],
            ]);
        });
    }

    public function recordCashMovement(Request $request): JsonResponse
    {
        $data = $request->validate([
            'shift_id' => ['required', 'integer', 'exists:pos_shifts,id'],
            'movement_type' => ['required', 'in:cash_in,drop,payout'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'reference_no' => ['nullable', 'string', 'max:80'],
            'reason' => ['required', 'string', 'max:500'],
        ]);
        $shift = PosShift::whereKey($data['shift_id'])->where('status', 'open')->first();
        if (! $shift) {
            return response()->json(['success' => false, 'message' => 'à¹„à¸¡à¹ˆà¸žà¸šà¸à¸°à¸—à¸µà¹ˆà¹€à¸›à¸´à¸”à¸­à¸¢à¸¹à¹ˆ'], 422);
        }
        $branchId = $this->enforcedBranchId((int) $shift->branch_id);
        $cashierId = $this->enforcedCashierId((int) $shift->cashier_id);
        if ($branchId !== (int) $shift->branch_id || $cashierId !== (int) $shift->cashier_id) {
            return response()->json(['success' => false, 'message' => 'à¸šà¸±à¸™à¸—à¸¶à¸à¹€à¸‡à¸´à¸™à¹€à¸‚à¹‰à¸²à¸­à¸­à¸à¹„à¸”à¹‰à¹€à¸‰à¸žà¸²à¸°à¸à¸°à¸‚à¸­à¸‡à¸•à¸™à¹€à¸­à¸‡'], 403);
        }
        if ($data['movement_type'] !== 'drop' && ! auth()->user()?->hasPermission('pos.cash.manage')) {
            return response()->json(['success' => false, 'message' => 'à¹€à¸‡à¸´à¸™à¹€à¸žà¸´à¹ˆà¸¡à¸«à¸£à¸·à¸­à¹€à¸šà¸´à¸à¸ˆà¹ˆà¸²à¸¢à¸•à¹‰à¸­à¸‡à¹ƒà¸«à¹‰à¸œà¸¹à¹‰à¸ˆà¸±à¸”à¸à¸²à¸£à¸­à¸™à¸¸à¸¡à¸±à¸•à¸´'], 403);
        }

        PosCashMovement::create([
            ...$data,
            'pos_shift_id' => $shift->id,
            'created_by' => auth()->id(),
            'approved_by' => auth()->user()?->hasPermission('pos.cash.manage') ? auth()->id() : null,
            'approved_at' => auth()->user()?->hasPermission('pos.cash.manage') ? now() : null,
        ]);
        $totals = $this->calculateShiftTotals($shift);
        $shift->update(['expected_cash' => $this->expectedCash($shift, $totals)]);

        return response()->json([
            'success' => true,
            'message' => $data['movement_type'] === 'drop' ? 'à¸šà¸±à¸™à¸—à¸¶à¸à¸™à¸³à¸ªà¹ˆà¸‡à¹€à¸‡à¸´à¸™à¹à¸¥à¹‰à¸§' : 'à¸šà¸±à¸™à¸—à¸¶à¸à¸£à¸²à¸¢à¸à¸²à¸£à¹€à¸‡à¸´à¸™à¸ªà¸”à¹à¸¥à¹‰à¸§',
            'shift' => $this->shiftPayload($shift->fresh()),
        ]);
    }

    public function zReport(Request $request, PosShift $shift): View
    {
        $branchId = $this->enforcedBranchId((int) $shift->branch_id);
        abort_unless($branchId === (int) $shift->branch_id || auth()->user()?->hasPermission('reports.view'), 403);
        $shift->load([
            'branch', 'cashier', 'cashierUser', 'terminal',
            'cashMovements' => fn ($query) => $query->with(['creator:id,name', 'approver:id,name'])->orderBy('id'),
        ]);

        return view('pos.z-report', [
            'shift' => $shift,
            'totals' => $this->calculateShiftTotals($shift),
            'cashMovements' => $this->cashMovementTotals($shift),
        ]);
    }

    /**
     * One place for a manager or finance user to close the daily POS loop:
     * shifts -> cash differences -> payment channels -> bank reconciliation.
     */
    public function control(Request $request): View
    {
        abort_unless(auth()->user()?->hasPermission('reports.view'), 403);

        $data = $request->validate([
            'date' => ['nullable', 'date'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
        ]);
        $date = Carbon::parse($data['date'] ?? now())->toDateString();
        $branchId = isset($data['branch_id']) ? (int) $data['branch_id'] : null;
        $branches = Branch::where('is_active', true)->orderBy('code')->get(['id', 'code', 'name_th']);

        $shiftQuery = PosShift::query()
            ->with(['branch:id,code,name_th', 'terminal:id,code,name', 'cashier:id,code,name'])
            ->whereDate('opened_at', $date)
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId));
        $shifts = $shiftQuery->orderByDesc('opened_at')->get();

        $receiptQuery = PosReceipt::query()
            ->join('pos_terminals as t', 't.id', '=', 'pos_receipts.pos_terminal_id')
            ->whereDate('pos_receipts.receipt_date', $date)
            ->where('pos_receipts.status', 'completed')
            ->when($branchId, fn ($query) => $query->where('t.branch_id', $branchId));
        $receiptStats = (clone $receiptQuery)->selectRaw(
            'count(*) as bill_count, coalesce(sum(pos_receipts.net_sales), 0) as net_sales, coalesce(avg(pos_receipts.net_sales), 0) as average_bill'
        )->first();

        $paymentTotals = DB::table('pos_payments as p')
            ->join('pos_receipts as r', 'r.id', '=', 'p.pos_receipt_id')
            ->join('pos_terminals as t', 't.id', '=', 'r.pos_terminal_id')
            ->whereDate('r.receipt_date', $date)
            ->where('r.status', 'completed')
            ->when($branchId, fn ($query) => $query->where('t.branch_id', $branchId))
            ->groupBy('p.method')
            ->selectRaw('p.method, coalesce(sum(p.amount), 0) as amount')
            ->pluck('amount', 'method');

        $unmatchedTransfers = DB::table('pos_payments as p')
            ->join('pos_receipts as r', 'r.id', '=', 'p.pos_receipt_id')
            ->join('pos_terminals as t', 't.id', '=', 'r.pos_terminal_id')
            ->join('branches as b', 'b.id', '=', 't.branch_id')
            ->whereDate('r.receipt_date', $date)
            ->where('r.status', 'completed')
            ->whereIn('p.method', ['transfer', 'qr', 'bank'])
            ->when($branchId, fn ($query) => $query->where('t.branch_id', $branchId))
            ->whereNotExists(fn ($query) => $query->selectRaw('1')
                ->from('bank_reconciliations as br')
                ->whereColumn('br.source_id', 'p.id')
                ->where('br.source_type', 'pos_payment')
                ->where('br.status', 'matched'))
            ->orderBy('r.receipt_date')
            ->limit(100)
            ->get(['r.receipt_no', 'r.receipt_date', 'b.code as branch_code', 'b.name_th as branch_name', 'p.method', 'p.payment_reference', 'p.amount']);

        return view('pos.control', [
            'date' => $date,
            'branchId' => $branchId,
            'branches' => $branches,
            'shifts' => $shifts,
            'receiptStats' => $receiptStats,
            'paymentTotals' => $paymentTotals,
            'unmatchedTransfers' => $unmatchedTransfers,
        ]);
    }

    public function checkout(
        Request $request,
        CashSaleService $service,
        MemberPointService $points,
        PosPaymentValidator $paymentValidator,
        PosPricingGuard $pricingGuard,
    ): JsonResponse {
        $data = $request->validate([
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'member_id' => ['nullable', 'integer', 'exists:members,id'],
            'shift_id' => ['required', 'integer', 'exists:pos_shifts,id'],
            'cashier_id' => ['required', 'integer', 'exists:salesmen,id'],
            'redeem_points' => ['nullable', 'numeric', 'min:0'],
            'method' => ['required', 'string', 'in:cash,transfer,credit_card,cheque,mixed'],
            'payment_ref' => ['nullable', 'string', 'max:80'],
            'payment_confirmed' => ['nullable', 'boolean'],
            'cash_received' => ['nullable', 'numeric', 'min:0'],
            'change_amount' => ['nullable', 'numeric', 'min:0'],
            'cash_amount' => ['nullable', 'numeric', 'min:0'],
            'transfer_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'manual_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_card_code' => ['nullable', 'string', 'max:30'],
            'vat_amount' => ['nullable', 'numeric', 'min:0'],
            'vat_mode' => ['nullable', 'string', 'in:included,excluded'],
            'is_full_tax' => ['nullable', 'boolean'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_tax_id' => ['nullable', 'string', 'max:50'],
            'customer_address' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.qty' => ['required', 'numeric', 'min:0.0001'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.barcode' => ['nullable', 'string', 'max:50'],
            'items.*.barcode_type' => ['nullable', Rule::in(BarcodePolicy::ALL)],
            'allow_negative_stock' => ['nullable', 'boolean'],
        ]);

        if ($mismatch = $this->barcodeBelongsToAnotherProduct($data['items'])) {
            return response()->json(['success' => false, 'message' => $mismatch], 422);
        }

        // à¸šà¸±à¸‡à¸„à¸±à¸šà¹ƒà¸Šà¹‰à¸•à¸±à¸§à¹€à¸­à¸‡: à¸•à¸±à¸”à¸ªà¸•à¹Šà¸­à¸+à¸¥à¸‡à¸„à¸™à¸‚à¸²à¸¢à¸•à¸²à¸¡à¸ªà¸²à¸‚à¸²/à¸£à¸«à¸±à¸ªà¸žà¸™à¸±à¸à¸‡à¸²à¸™à¸‚à¸­à¸‡ user à¸—à¸µà¹ˆ login
        // (client à¸ªà¹ˆà¸‡à¸„à¹ˆà¸²à¸­à¸°à¹„à¸£à¸¡à¸²à¸à¹‡ override) - à¸ªà¸•à¹Šà¸­à¸à¸ˆà¸¶à¸‡à¸•à¸±à¸”à¸ˆà¸²à¸à¸„à¸¥à¸±à¸‡à¸ªà¸²à¸‚à¸²à¸•à¸±à¸§à¹€à¸­à¸‡à¹€à¸ªà¸¡à¸­
        $data['branch_id'] = $this->enforcedBranchId((int) $data['branch_id']);
        $data['cashier_id'] = $this->enforcedCashierId((int) $data['cashier_id']);
        if (! $data['cashier_id']) {
            return response()->json(['success' => false, 'message' => 'à¸šà¸±à¸à¸Šà¸µà¸™à¸µà¹‰à¸¢à¸±à¸‡à¹„à¸¡à¹ˆà¹„à¸”à¹‰à¸à¸³à¸«à¸™à¸”à¸£à¸«à¸±à¸ªà¸žà¸™à¸±à¸à¸‡à¸²à¸™à¸‚à¸²à¸¢ à¸•à¸´à¸”à¸•à¹ˆà¸­à¸œà¸¹à¹‰à¸”à¸¹à¹à¸¥à¸£à¸°à¸šà¸š'], 422);
        }

        try {
            // à¸›à¹‰à¸²à¸¢à¸Šà¸±à¹ˆà¸‡à¸à¸±à¸‡à¸£à¸²à¸„à¸²à¸£à¸§à¸¡à¹„à¸§à¹‰à¹ƒà¸™à¸šà¸²à¸£à¹Œà¹‚à¸„à¹‰à¸” à¸•à¹‰à¸­à¸‡à¸–à¸­à¸”à¹€à¸›à¹‡à¸™à¸™à¹‰à¸³à¸«à¸™à¸±à¸+à¸£à¸²à¸„à¸²à¸•à¹ˆà¸­à¸«à¸™à¹ˆà¸§à¸¢à¸à¸±à¹ˆà¸‡ server à¸à¹ˆà¸­à¸™à¸•à¸£à¸§à¸ˆà¸£à¸²à¸„à¸²
            $data['items'] = $pricingGuard->resolveScaleLines($data['items'], (int) $data['branch_id']);
            $pricingGuard->validate($data, auth()->user());
            $data['items'] = $pricingGuard->normalizeItems($data['items']);
            $paymentValidator->validate($data);
        } catch (RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        $shift = PosShift::where('id', $data['shift_id'])
            ->where('branch_id', $data['branch_id'])
            ->where('cashier_id', $data['cashier_id'])
            ->where('status', 'open')
            ->first();
        if (! $shift) {
            return response()->json(['success' => false, 'message' => 'à¸à¸£à¸¸à¸“à¸²à¹€à¸›à¸´à¸”à¸à¸°à¹à¸„à¸Šà¹€à¸Šà¸µà¸¢à¸£à¹Œà¸à¹ˆà¸­à¸™à¸‚à¸²à¸¢'], 422);
        }

        $discountCard = null;
        if (! empty($data['discount_card_code'])) {
            $discountCard = DiscountCard::where('card_code', $data['discount_card_code'])->first();
            if (! $discountCard || ! $discountCard->isValidAt(now())) {
                return response()->json(['success' => false, 'message' => 'à¸šà¸±à¸•à¸£à¸ªà¹ˆà¸§à¸™à¸¥à¸”à¸«à¸¡à¸”à¸­à¸²à¸¢à¸¸à¸«à¸£à¸·à¸­à¹ƒà¸Šà¹‰à¸„à¸£à¸šà¸ˆà¸³à¸™à¸§à¸™à¹à¸¥à¹‰à¸§ à¸à¸£à¸¸à¸“à¸²à¸™à¸³à¸šà¸±à¸•à¸£à¸­à¸­à¸à¹à¸¥à¹‰à¸§à¸¥à¸­à¸‡à¹ƒà¸«à¸¡à¹ˆ'], 422);
            }
        }

        $member = null;
        $redeemPoints = (float) ($data['redeem_points'] ?? 0);
        if (! empty($data['member_id'])) {
            $member = Member::find($data['member_id']);
        }
        if ($redeemPoints > 0) {
            if (! $member) {
                return response()->json(['success' => false, 'message' => 'à¸•à¹‰à¸­à¸‡à¹€à¸¥à¸·à¸­à¸à¸ªà¸¡à¸²à¸Šà¸´à¸à¸à¹ˆà¸­à¸™à¸ˆà¸¶à¸‡à¸ˆà¸°à¹à¸¥à¸à¹à¸•à¹‰à¸¡à¹„à¸”à¹‰'], 422);
            }
            if ((float) $member->points < $redeemPoints) {
                return response()->json(['success' => false, 'message' => 'à¹à¸•à¹‰à¸¡à¸ªà¸°à¸ªà¸¡à¹„à¸¡à¹ˆà¸žà¸­ (à¸„à¸‡à¹€à¸«à¸¥à¸·à¸­ '.number_format((float) $member->points, 2).' à¹à¸•à¹‰à¸¡)'], 422);
            }
            if ($points->pointValueBaht() <= 0) {
                return response()->json(['success' => false, 'message' => 'à¸£à¸°à¸šà¸šà¸¢à¸±à¸‡à¹„à¸¡à¹ˆà¹€à¸›à¸´à¸”à¹ƒà¸«à¹‰à¹à¸¥à¸à¹à¸•à¹‰à¸¡à¹€à¸›à¹‡à¸™à¸ªà¹ˆà¸§à¸™à¸¥à¸”'], 422);
            }
        }

        $methodText = match ($data['method']) {
            'cash' => 'à¹€à¸‡à¸´à¸™à¸ªà¸”',
            'transfer' => 'à¹‚à¸­à¸™à¹€à¸‡à¸´à¸™/QR',
            'credit_card' => 'à¸šà¸±à¸•à¸£à¹€à¸„à¸£à¸”à¸´à¸•',
            'mixed' => 'à¹€à¸‡à¸´à¸™à¸ªà¸”+à¹‚à¸­à¸™',
            default => 'à¹€à¸Šà¹‡à¸„',
        };
        $remarkParts = ['POS: '.$methodText];
        if ($data['method'] === 'mixed') {
            $remarkParts[] = 'à¹€à¸‡à¸´à¸™à¸ªà¸”: '.number_format((float) ($data['cash_amount'] ?? 0), 2)
                .' | à¹‚à¸­à¸™: '.number_format((float) ($data['transfer_amount'] ?? 0), 2);
        }
        if (in_array($data['method'], ['transfer', 'mixed'], true)) {
            $remarkParts[] = 'à¸•à¸£à¸§à¸ˆà¹€à¸‡à¸´à¸™à¹€à¸‚à¹‰à¸²à¹à¸¥à¹‰à¸§';
        }
        if (! empty($data['payment_ref'])) {
            $remarkParts[] = 'à¸­à¹‰à¸²à¸‡à¸­à¸´à¸‡: '.$data['payment_ref'];
        }
        if (! empty($data['discount_amount'])) {
            $remarkParts[] = 'à¸ªà¹ˆà¸§à¸™à¸¥à¸”: '.number_format((float) $data['discount_amount'], 2);
        }
        if ($discountCard) {
            $remarkParts[] = 'à¸šà¸±à¸•à¸£à¸ªà¹ˆà¸§à¸™à¸¥à¸”: '.$discountCard->card_code;
        }
        if ($member) {
            $remarkParts[] = 'à¸ªà¸¡à¸²à¸Šà¸´à¸: '.$member->member_code;
        }
        if ($redeemPoints > 0) {
            $remarkParts[] = 'à¹à¸¥à¸à¹à¸•à¹‰à¸¡: '.number_format($redeemPoints, 2);
        }
        if (! empty($data['vat_amount'])) {
            $vatMode = ($data['vat_mode'] ?? 'included') === 'excluded' ? 'à¹à¸¢à¸ VAT' : 'à¸£à¸§à¸¡ VAT';
            $remarkParts[] = $vatMode.': '.number_format((float) $data['vat_amount'], 2);
        }

        try {
            [$document, $receipt, $earnedPoints] = DB::transaction(function () use (
                $service,
                $data,
                $remarkParts,
                $shift,
                $discountCard,
                $member,
                $points,
                $redeemPoints,
            ) {
                $document = $service->create([
                    'branch_id' => $data['branch_id'],
                    'customer_id' => $data['customer_id'] ?? null,
                    'remark' => implode(' | ', $remarkParts),
                    'items' => $data['items'],
                    'allow_negative_stock' => (bool) ($data['allow_negative_stock'] ?? false),
                    // à¹€à¸‡à¸´à¸™à¸ªà¸”à¸‚à¸­à¸‡à¸šà¸´à¸¥à¸™à¸µà¹‰à¹€à¸‚à¹‰à¸²à¸ªà¸¡à¸¸à¸”à¹€à¸‡à¸´à¸™à¸ªà¸”à¸•à¸­à¸™à¸›à¸´à¸”à¸à¸° à¹„à¸¡à¹ˆà¹ƒà¸Šà¹ˆà¸•à¸­à¸™à¸™à¸µà¹‰ â€” à¸à¸±à¸™à¸™à¸±à¸šà¹€à¸‡à¸´à¸™à¸‹à¹‰à¸³
                    'source' => 'pos',
                ]);

                $receipt = $this->recordPosReceipt($shift, $this->nextPosReceiptNo($shift), $data, $document->id);

                if ($discountCard) {
                    $lockedCard = DiscountCard::whereKey($discountCard->id)->lockForUpdate()->first();
                    if (! $lockedCard || ! $lockedCard->isValidAt(now())) {
                        throw new RuntimeException('à¸šà¸±à¸•à¸£à¸ªà¹ˆà¸§à¸™à¸¥à¸”à¸«à¸¡à¸”à¸­à¸²à¸¢à¸¸à¸«à¸£à¸·à¸­à¹ƒà¸Šà¹‰à¸„à¸£à¸šà¸ˆà¸³à¸™à¸§à¸™à¹à¸¥à¹‰à¸§ à¸à¸£à¸¸à¸“à¸²à¸™à¸³à¸šà¸±à¸•à¸£à¸­à¸­à¸à¹à¸¥à¹‰à¸§à¸¥à¸­à¸‡à¹ƒà¸«à¸¡à¹ˆ');
                    }
                    $lockedCard->increment('used_count');
                }

                $earnedPoints = $member ? $points->settle($member, $document, $redeemPoints) : 0.0;

                return [$document, $receipt, $earnedPoints];
            });

            return response()->json([
                'success' => true,
                'doc_number' => $receipt->receipt_no,
                'receipt_id' => $receipt->id,
                'receipt_no' => $receipt->receipt_no,
                'source_doc_number' => $document->doc_number,
                'total_amount' => (float) $document->total_amount,
                'earned_points' => $earnedPoints,
                'member_points' => $member ? (float) $member->fresh()->points : null,
            ]);
        } catch (RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function voidReceipt(Request $request, PosReceipt $receipt, GlPostingService $glPosting): JsonResponse
    {
        if (! auth()->user()?->hasPermission('pos.void')) {
            return response()->json(['success' => false, 'message' => 'à¹€à¸‰à¸žà¸²à¸°à¸œà¸¹à¹‰à¸ˆà¸±à¸”à¸à¸²à¸£à¸«à¸£à¸·à¸­ IT à¹€à¸—à¹ˆà¸²à¸™à¸±à¹‰à¸™à¸—à¸µà¹ˆà¸¢à¸à¹€à¸¥à¸´à¸à¸šà¸´à¸¥à¹„à¸”à¹‰'], 403);
        }

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
            'shift_id' => ['nullable', 'integer', 'exists:pos_shifts,id'],
        ]);

        if ($receipt->status !== 'completed') {
            return response()->json(['success' => false, 'message' => 'à¸šà¸´à¸¥à¸™à¸µà¹‰à¸–à¸¹à¸à¸¢à¸à¹€à¸¥à¸´à¸à¹„à¸›à¹à¸¥à¹‰à¸§à¸«à¸£à¸·à¸­à¹„à¸¡à¹ˆà¸­à¸¢à¸¹à¹ˆà¹ƒà¸™à¸ªà¸–à¸²à¸™à¸°à¸—à¸µà¹ˆà¸¢à¸à¹€à¸¥à¸´à¸à¹„à¸”à¹‰'], 422);
        }

        $receipt->loadMissing(['shift', 'terminal']);
        if (! $receipt->shift || $receipt->shift->status !== 'open') {
            return response()->json(['success' => false, 'message' => 'à¸¢à¸à¹€à¸¥à¸´à¸à¸šà¸´à¸¥à¹„à¸”à¹‰à¹€à¸‰à¸žà¸²à¸°à¸šà¸´à¸¥à¹ƒà¸™à¸à¸°à¸—à¸µà¹ˆà¸¢à¸±à¸‡à¹€à¸›à¸´à¸”à¸­à¸¢à¸¹à¹ˆ à¸«à¸²à¸à¸›à¸´à¸”à¸à¸°à¹à¸¥à¹‰à¸§à¹ƒà¸«à¹‰à¹ƒà¸Šà¹‰à¸£à¸±à¸šà¸„à¸·à¸™à¸ªà¸´à¸™à¸„à¹‰à¸²'], 422);
        }
        if (! empty($data['shift_id']) && (int) $receipt->shift->id !== (int) $data['shift_id']) {
            return response()->json(['success' => false, 'message' => 'à¸¢à¸à¹€à¸¥à¸´à¸à¹„à¸”à¹‰à¹€à¸‰à¸žà¸²à¸°à¸šà¸´à¸¥à¹ƒà¸™à¸à¸°à¸›à¸±à¸ˆà¸ˆà¸¸à¸šà¸±à¸™à¸‚à¸­à¸‡à¹€à¸„à¸£à¸·à¹ˆà¸­à¸‡à¸™à¸µà¹‰'], 422);
        }
        if ($receipt->receipt_date?->toDateString() !== now()->toDateString()) {
            return response()->json(['success' => false, 'message' => 'à¸¢à¸à¹€à¸¥à¸´à¸à¸šà¸´à¸¥à¸‚à¹‰à¸²à¸¡à¸§à¸±à¸™à¹„à¸¡à¹ˆà¹„à¸”à¹‰ à¹ƒà¸«à¹‰à¹ƒà¸Šà¹‰à¸£à¸±à¸šà¸„à¸·à¸™à¸ªà¸´à¸™à¸„à¹‰à¸²à¹à¸—à¸™'], 422);
        }
        $hasReturn = DB::table('pos_receipt_returns')
            ->where('pos_receipt_id', $receipt->id)
            ->where('status', 'completed')
            ->exists();
        if ($hasReturn) {
            return response()->json(['success' => false, 'message' => 'à¸šà¸´à¸¥à¸™à¸µà¹‰à¸¡à¸µà¸à¸²à¸£à¸£à¸±à¸šà¸„à¸·à¸™à¸ªà¸´à¸™à¸„à¹‰à¸²à¹à¸¥à¹‰à¸§ à¸¢à¸à¹€à¸¥à¸´à¸à¸—à¸±à¹‰à¸‡à¸šà¸´à¸¥à¹„à¸¡à¹ˆà¹„à¸”à¹‰'], 422);
        }

        try {
            DB::transaction(function () use ($receipt, $data, $glPosting) {
                $receipt = PosReceipt::with(['terminal', 'shift'])
                    ->whereKey($receipt->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($receipt->status !== 'completed') {
                    throw new RuntimeException('à¸šà¸´à¸¥à¸™à¸µà¹‰à¸–à¸¹à¸à¸¢à¸à¹€à¸¥à¸´à¸à¹„à¸›à¹à¸¥à¹‰à¸§');
                }
                if (! $receipt->shift || $receipt->shift->status !== 'open') {
                    throw new RuntimeException('à¸¢à¸à¹€à¸¥à¸´à¸à¸šà¸´à¸¥à¹„à¸”à¹‰à¹€à¸‰à¸žà¸²à¸°à¸šà¸´à¸¥à¹ƒà¸™à¸à¸°à¸—à¸µà¹ˆà¸¢à¸±à¸‡à¹€à¸›à¸´à¸”à¸­à¸¢à¸¹à¹ˆ à¸«à¸²à¸à¸›à¸´à¸”à¸à¸°à¹à¸¥à¹‰à¸§à¹ƒà¸«à¹‰à¹ƒà¸Šà¹‰à¸£à¸±à¸šà¸„à¸·à¸™à¸ªà¸´à¸™à¸„à¹‰à¸²');
                }
                if (! empty($data['shift_id']) && (int) $receipt->shift->id !== (int) $data['shift_id']) {
                    throw new RuntimeException('à¸¢à¸à¹€à¸¥à¸´à¸à¹„à¸”à¹‰à¹€à¸‰à¸žà¸²à¸°à¸šà¸´à¸¥à¹ƒà¸™à¸à¸°à¸›à¸±à¸ˆà¸ˆà¸¸à¸šà¸±à¸™à¸‚à¸­à¸‡à¹€à¸„à¸£à¸·à¹ˆà¸­à¸‡à¸™à¸µà¹‰');
                }
                if ($receipt->receipt_date?->toDateString() !== now()->toDateString()) {
                    throw new RuntimeException('à¸¢à¸à¹€à¸¥à¸´à¸à¸šà¸´à¸¥à¸‚à¹‰à¸²à¸¡à¸§à¸±à¸™à¹„à¸¡à¹ˆà¹„à¸”à¹‰ à¹ƒà¸«à¹‰à¹ƒà¸Šà¹‰à¸£à¸±à¸šà¸„à¸·à¸™à¸ªà¸´à¸™à¸„à¹‰à¸²à¹à¸—à¸™');
                }
                $hasReturn = DB::table('pos_receipt_returns')
                    ->where('pos_receipt_id', $receipt->id)
                    ->where('status', 'completed')
                    ->exists();
                if ($hasReturn) {
                    throw new RuntimeException('à¸šà¸´à¸¥à¸™à¸µà¹‰à¸¡à¸µà¸à¸²à¸£à¸£à¸±à¸šà¸„à¸·à¸™à¸ªà¸´à¸™à¸„à¹‰à¸²à¹à¸¥à¹‰à¸§ à¸¢à¸à¹€à¸¥à¸´à¸à¸—à¸±à¹‰à¸‡à¸šà¸´à¸¥à¹„à¸¡à¹ˆà¹„à¸”à¹‰');
                }

                $oldValues = [
                    'status' => $receipt->status,
                    'net_sales' => (float) $receipt->net_sales,
                    'receipt_no' => $receipt->receipt_no,
                    'document_id' => $receipt->document_id,
                ];

                $receipt->update([
                    'status' => 'void',
                    'voided_at' => now(),
                    'voided_by' => auth()->id(),
                    'void_reason' => $data['reason'],
                ]);

                if ($receipt->document_id) {
                    $document = Document::whereKey($receipt->document_id)->lockForUpdate()->first();
                    if ($document && $document->status !== 'cancelled') {
                        $document->forceFill([
                            'status' => 'cancelled',
                            'cancelled_at' => now(),
                            'remark' => trim(($document->remark ? $document->remark.' | ' : '').'POS void: '.$data['reason']),
                        ])->save();

                        $this->restoreStockForVoidedDocument($document);
                        $glPosting->reverseDocument($document, 'POS void '.$receipt->receipt_no.' - '.$data['reason']);
                    }
                }

                DB::table('audit_logs')->insert([
                    'user_id' => auth()->id(),
                    'branch_id' => $receipt->terminal?->branch_id ?? $receipt->shift?->branch_id,
                    'action' => 'void',
                    'table_name' => 'pos_receipts',
                    'record_id' => $receipt->id,
                    'old_values' => json_encode($oldValues, JSON_UNESCAPED_UNICODE),
                    'new_values' => json_encode([
                        'status' => 'void',
                        'voided_at' => now()->toDateTimeString(),
                        'voided_by' => auth()->id(),
                        'void_reason' => $data['reason'],
                    ], JSON_UNESCAPED_UNICODE),
                    'created_at' => now(),
                ]);

                if ($receipt->shift) {
                    $totals = $this->calculateShiftTotals($receipt->shift);
                    $receipt->shift->update([
                        'cash_sales' => $totals['cash'],
                        'transfer_sales' => $totals['transfer'],
                        'card_sales' => $totals['credit_card'],
                        'cheque_sales' => $totals['cheque'],
                        'expected_cash' => $this->expectedCash($receipt->shift, $totals),
                        'receipt_count' => $totals['receipt_count'],
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'à¸¢à¸à¹€à¸¥à¸´à¸à¸šà¸´à¸¥à¹€à¸£à¸µà¸¢à¸šà¸£à¹‰à¸­à¸¢',
                'receipt_no' => $receipt->receipt_no,
            ]);
        } catch (RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    private function findOpenShift(int $branchId, ?int $cashierId = null): ?PosShift
    {
        return PosShift::with(['branch', 'terminal', 'cashier'])
            ->where('branch_id', $branchId)
            ->where('status', 'open')
            ->when($cashierId, fn ($query) => $query->where('cashier_id', $cashierId))
            ->orderByDesc('opened_at')
            ->first();
    }

    private function terminalForBranch(int $branchId): PosTerminal
    {
        $branch = Branch::findOrFail($branchId);

        return PosTerminal::firstOrCreate(
            ['code' => 'WEB-'.$branch->code],
            ['branch_id' => $branchId, 'name' => 'JET POS '.$branch->code]
        );
    }

    private function nextShiftNo(int $branchId): string
    {
        $branchCode = Branch::find($branchId)?->code ?? sprintf('%04d', $branchId);

        return 'SHIFT-'.$branchCode.'-'.now()->format('Ymd-His');
    }

    private function nextHoldNo(int $branchId): string
    {
        $branchCode = Branch::find($branchId)?->code ?? sprintf('%04d', $branchId);
        $prefix = 'HOLD-'.preg_replace('/[^A-Za-z0-9]/', '', $branchCode).'-'.now()->format('Ymd').'-';
        $last = PosHeldBill::where('hold_no', 'like', $prefix.'%')
            ->orderByDesc('hold_no')
            ->lockForUpdate()
            ->value('hold_no');

        return $prefix.sprintf('%04d', $last ? ((int) substr($last, -4)) + 1 : 1);
    }

    private function nextPosReceiptNo(PosShift $shift): string
    {
        $rawBranchCode = $shift->branch?->code ?? Branch::find($shift->branch_id)?->code ?? sprintf('%04d', $shift->branch_id);
        $branchCode = substr(preg_replace('/[^A-Za-z0-9]/', '', (string) $rawBranchCode) ?: sprintf('%04d', $shift->branch_id), 0, 8);
        $prefix = 'CS'.$branchCode.now()->format('Ymd');
        $lastReceiptNo = PosReceipt::where('pos_terminal_id', $shift->pos_terminal_id)
            ->where('receipt_no', 'like', $prefix.'%')
            ->orderByDesc('receipt_no')
            ->lockForUpdate()
            ->value('receipt_no');
        $sequence = $lastReceiptNo ? ((int) substr($lastReceiptNo, -3)) + 1 : 1;

        return $prefix.sprintf('%03d', $sequence);
    }

    private function recordPosReceipt(PosShift $shift, string $receiptNo, array $data, int $documentId): PosReceipt
    {
        $items = collect($data['items']);
        $gross = round($items->sum(fn ($item) => (float) $item['qty'] * (float) $item['unit_price']), 2);
        $discount = round((float) ($data['discount_amount'] ?? 0), 2);
        $vat = round((float) ($data['vat_amount'] ?? 0), 2);
        $net = round($gross, 2);

        $receipt = PosReceipt::create([
            'pos_terminal_id' => $shift->pos_terminal_id,
            'pos_shift_id' => $shift->id,
            'document_id' => $documentId,
            'receipt_no' => $receiptNo,
            'receipt_date' => now(),
            'cashier_id' => $shift->cashier_user_id ?: auth()->id(),
            'cashier_salesman_id' => $data['cashier_id'],
            'member_id' => $data['member_id'] ?? null,
            'gross_sales' => $gross,
            'discount_amount' => $discount,
            'vat_amount' => $vat,
            'net_sales' => $net,
            'status' => 'completed',
        ]);

        foreach ($items->values() as $index => $item) {
            $lineNet = round((float) $item['qty'] * (float) $item['unit_price'], 2);
            PosReceiptItem::create([
                'pos_receipt_id' => $receipt->id,
                'seq' => $index + 1,
                'product_id' => $item['product_id'],
                'qty' => $item['qty'],
                'unit_price' => $item['unit_price'],
                'discount_amount' => 0,
                'vat_amount' => 0,
                'net_amount' => $lineNet,
            ]);
        }

        if ($data['method'] === 'mixed') {
            // à¸ˆà¹ˆà¸²à¸¢à¸œà¸ªà¸¡: à¹à¸¢à¸ 2 à¹à¸–à¸§à¸•à¸²à¸¡à¸Šà¹ˆà¸­à¸‡à¸—à¸²à¸‡ â€” à¸¢à¸­à¸”à¸à¸°/à¸£à¸²à¸¢à¸‡à¸²à¸™à¸£à¸§à¸¡à¸ˆà¸²à¸ pos_payments à¸ˆà¸¶à¸‡à¹€à¸‚à¹‰à¸²à¸Šà¹ˆà¸­à¸‡à¸–à¸¹à¸à¹€à¸­à¸‡
            $cashPart = round((float) ($data['cash_amount'] ?? 0), 2);
            $transferPart = round($net - $cashPart, 2); // à¸œà¸¹à¸à¸à¸±à¸šà¸¢à¸­à¸”à¸šà¸´à¸¥à¸ˆà¸£à¸´à¸‡ à¸à¸±à¸™à¹€à¸¨à¸©à¸›à¸±à¸”à¹„à¸¡à¹ˆà¸¥à¸‡à¸•à¸±à¸§
            PosPayment::create([
                'pos_receipt_id' => $receipt->id,
                'method' => 'cash',
                'amount' => $cashPart,
                'cash_received' => $data['cash_received'] ?? $cashPart,
                'change_amount' => $data['change_amount'] ?? 0,
            ]);
            PosPayment::create([
                'pos_receipt_id' => $receipt->id,
                'method' => 'transfer',
                'payment_reference' => $data['payment_ref'] ?? null,
                'amount' => $transferPart,
            ]);
        } else {
            PosPayment::create([
                'pos_receipt_id' => $receipt->id,
                'method' => $data['method'],
                'payment_reference' => $data['payment_ref'] ?? null,
                'amount' => $net,
                'cash_received' => $data['method'] === 'cash' ? ($data['cash_received'] ?? $net) : null,
                'change_amount' => $data['method'] === 'cash' ? ($data['change_amount'] ?? 0) : null,
                'card_no' => $data['method'] === 'credit_card' ? ($data['payment_ref'] ?? null) : null,
                'cheque_no' => $data['method'] === 'cheque' ? ($data['payment_ref'] ?? null) : null,
            ]);
        }

        $totals = $this->calculateShiftTotals($shift);
        $shift->update([
            'cash_sales' => $totals['cash'],
            'transfer_sales' => $totals['transfer'],
            'card_sales' => $totals['credit_card'],
            'cheque_sales' => $totals['cheque'],
            'expected_cash' => $this->expectedCash($shift, $totals),
            'receipt_count' => $totals['receipt_count'],
        ]);

        return $receipt;
    }

    private function restoreStockForVoidedDocument(Document $document): void
    {
        if (! StockDocument::where('document_id', $document->id)->exists()) {
            return;
        }

        app(FifoStockService::class)->restoreDocumentIssues($document->id, 'void_in');
    }

    private function calculateShiftTotals(PosShift $shift): array
    {
        $payments = PosPayment::query()
            ->join('pos_receipts', 'pos_receipts.id', '=', 'pos_payments.pos_receipt_id')
            ->where('pos_receipts.pos_shift_id', $shift->id)
            ->where('pos_receipts.status', 'completed')
            ->selectRaw('pos_payments.method, sum(pos_payments.amount) as total')
            ->groupBy('pos_payments.method')
            ->pluck('total', 'method');

        $returns = DB::table('pos_receipt_returns')
            ->where('pos_shift_id', $shift->id)
            ->where('status', 'completed')
            ->selectRaw('refund_method, sum(total_amount) as total')
            ->groupBy('refund_method')
            ->pluck('total', 'refund_method');

        return [
            'cash' => round((float) ($payments['cash'] ?? 0) - (float) ($returns['cash'] ?? 0), 2),
            'transfer' => round((float) ($payments['transfer'] ?? 0) - (float) ($returns['transfer'] ?? 0), 2),
            'credit_card' => round((float) ($payments['credit_card'] ?? 0), 2),
            'cheque' => round((float) ($payments['cheque'] ?? 0), 2),
            'receipt_count' => PosReceipt::where('pos_shift_id', $shift->id)->where('status', 'completed')->count(),
        ];
    }

    private function cashMovementTotals(PosShift $shift): array
    {
        $rows = PosCashMovement::where('pos_shift_id', $shift->id)
            ->selectRaw('movement_type, sum(amount) as total')
            ->groupBy('movement_type')
            ->pluck('total', 'movement_type');

        return [
            'cash_in' => round((float) ($rows['cash_in'] ?? 0), 2),
            'drop' => round((float) ($rows['drop'] ?? 0), 2),
            'payout' => round((float) ($rows['payout'] ?? 0), 2),
        ];
    }

    private function expectedCash(PosShift $shift, ?array $sales = null): float
    {
        $sales ??= $this->calculateShiftTotals($shift);
        $cash = $this->cashMovementTotals($shift);

        return round(
            (float) $shift->opening_cash
            + $sales['cash']
            + $cash['cash_in']
            - $cash['drop']
            - $cash['payout'],
            2,
        );
    }

    private function shiftPayload(PosShift $shift): array
    {
        $shift->loadMissing(['branch', 'cashier', 'cashierUser', 'terminal']);
        $movements = $this->cashMovementTotals($shift);

        return [
            'id' => $shift->id,
            'shift_no' => $shift->shift_no,
            'status' => $shift->status,
            'branch_id' => $shift->branch_id,
            'branch_name' => $shift->branch?->name_th,
            'cashier_id' => $shift->cashier_id,
            'cashier_name' => $shift->cashier?->name,
            'cashier_user_id' => $shift->cashier_user_id,
            'cashier_user_name' => $shift->cashierUser?->name,
            'opened_at' => $shift->opened_at?->format('Y-m-d H:i:s'),
            'closed_at' => $shift->closed_at?->format('Y-m-d H:i:s'),
            'opening_cash' => (float) $shift->opening_cash,
            'cash_sales' => (float) $shift->cash_sales,
            'transfer_sales' => (float) $shift->transfer_sales,
            'card_sales' => (float) $shift->card_sales,
            'cheque_sales' => (float) $shift->cheque_sales,
            'expected_cash' => (float) $shift->expected_cash,
            'counted_cash' => $shift->counted_cash !== null ? (float) $shift->counted_cash : null,
            'cash_difference' => $shift->cash_difference !== null ? (float) $shift->cash_difference : null,
            'receipt_count' => (int) $shift->receipt_count,
            'cash_in' => $movements['cash_in'],
            'cash_drops' => $movements['drop'],
            'cash_payouts' => $movements['payout'],
            'z_report_url' => route('pos.shift.z-report', $shift),
        ];
    }

    private function cashierUserId(?int $cashierId): ?int
    {
        return $cashierId
            ? (Salesman::whereKey($cashierId)->value('user_id') ?: auth()->id())
            : auth()->id();
    }
}

