<?php

namespace Tests\Feature;

use App\Models\MasterCutoverRun;
use App\Models\Product;
use App\Models\ProductBarcode;
use App\Models\ProductCategory;
use App\Models\ProductUnit;
use App\Services\InternalEan13Allocator;
use App\Services\ProductSkuAllocator;
use App\Services\ProductSkuRecodeService;
use App\Support\BarcodePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProductSkuRecodeTest extends TestCase
{
    use RefreshDatabase;

    public function test_plan_assigns_each_products_valid_ean13_as_its_new_sku(): void
    {
        $first = $this->product('SKU-OLD-A', 'CAT-4RLG');
        $second = $this->product('OLD-B', null);
        $third = $this->product('OLD-C', 'CC');

        $plan = collect($this->service()->plan())->keyBy('id');

        foreach ([$first, $second, $third] as $product) {
            $this->assertSame($product->barcodes()->value('barcode'), $plan[$product->id]['new']);
            $this->assertMatchesRegularExpression('/^[0-9]{13}$/', $plan[$product->id]['new']);
        }
        $this->assertCount(3, collect($plan)->pluck('new')->unique());
    }

    public function test_apply_uses_ean13_and_keeps_the_previous_sku_as_a_scannable_alias(): void
    {
        $product = $this->product('SKU-OLD-10', 'CAT-4RLG');
        $barcode = ProductBarcode::create([
            'product_id' => $product->id, 'barcode' => '8850001234567', 'unit_id' => $product->base_unit_id,
            'unit_factor' => 1, 'barcode_type' => 'CUSTOM', 'is_active' => true,
        ]);
        $newSku = $product->barcodes()->value('barcode');

        $result = $this->service()->apply();

        $this->assertSame(1, $result['products']);
        $this->assertSame($newSku, $product->fresh()->sku_code);
        $this->assertMatchesRegularExpression('/^[0-9]{13}$/', $product->fresh()->sku_code);
        $this->assertSame('SKU-OLD-10', $product->fresh()->legacy_sku);
        $this->assertSame($product->id, $barcode->fresh()->product_id);
        $this->assertSame($product->id, ProductBarcode::where('barcode', 'SKU-OLD-10')->value('product_id'));
        $this->assertSame(1, MasterCutoverRun::where('scope', ProductSkuRecodeService::SCOPE)->count());
    }

    public function test_unclassified_and_cancelled_products_are_included_in_ean13_recode(): void
    {
        $withoutCategory = $this->product('OLD-NO-CATEGORY', null);
        $cancelled = $this->product('OLD-CANCELLED', 'CC');
        $withoutCategorySku = $withoutCategory->barcodes()->value('barcode');
        $cancelledSku = $cancelled->barcodes()->value('barcode');

        $result = $this->service()->apply();

        $this->assertSame(2, $result['products']);
        $this->assertSame($withoutCategorySku, $withoutCategory->fresh()->sku_code);
        $this->assertSame($cancelledSku, $cancelled->fresh()->sku_code);
        $this->assertSame('OLD-CANCELLED', $cancelled->fresh()->legacy_sku);
    }

    public function test_cutover_cannot_be_applied_twice(): void
    {
        $this->product('SKU-OLD-A', 'CAT-4RLG');
        $service = $this->service();

        $service->apply();

        $this->assertNotEmpty(collect($service->preflight())->where('level', 'หยุด'));
    }

    public function test_next_sku_is_valid_unique_ean13(): void
    {
        $category = ProductCategory::firstOrCreate(['code' => 'CAT-4RLG'], ['name_th' => 'เนื้อวัว']);
        $first = $this->product('OLD-1', $category->code);
        $this->product('OLD-2', $category->code)->delete();

        $next = DB::transaction(fn () => app(ProductSkuAllocator::class)->nextForCategory($category->id));

        $this->assertMatchesRegularExpression('/^\d{13}$/', $next);
        $this->assertStringStartsWith('299', $next);
        $this->assertTrue(app(BarcodePolicy::class)->isValidEan13($next));
        $this->assertNotSame($first->sku_code, $next);
    }

    private function product(string $sku, ?string $categoryCode): Product
    {
        $unit = ProductUnit::firstOrCreate(['code' => 'EA-SKU'], ['name' => 'ชิ้น', 'qty_per_base_unit' => 1]);
        $category = $categoryCode === null
            ? null
            : ProductCategory::firstOrCreate(['code' => $categoryCode], ['name_th' => 'ประเภท '.$categoryCode]);

        $product = Product::create([
            'sku_code' => $sku,
            'name_th' => 'สินค้าทดสอบ '.$sku,
            'product_category_id' => $category?->id,
            'base_unit_id' => $unit->id,
            'default_price' => 10,
            'average_cost' => 0,
            'is_vat' => false,
            'is_active' => true,
        ]);
        $barcode = DB::transaction(fn () => app(InternalEan13Allocator::class)->next());
        ProductBarcode::create([
            'product_id' => $product->id,
            'barcode' => $barcode,
            'unit_id' => $unit->id,
            'unit_factor' => 1,
            'barcode_type' => BarcodePolicy::INTERNAL_13,
            'is_active' => true,
        ]);

        return $product->fresh();
    }

    private function service(): ProductSkuRecodeService
    {
        return app(ProductSkuRecodeService::class);
    }
}
