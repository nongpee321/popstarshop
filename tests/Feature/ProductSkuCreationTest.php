<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\PosOfflineProductController;
use App\Http\Controllers\PosController;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Services\ProductSkuAllocator;
use App\Support\BarcodePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ProductSkuCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_pos_quick_add_and_offline_product_create_ean13_skus(): void
    {
        $quickAddResponse = app(PosController::class)->quickAddProduct(
            Request::create('/pos/products/quick-add', 'POST', [
                'barcode' => '8850000000017',
                'name_th' => 'สินค้าเพิ่มด่วน',
                'price' => 25,
            ]),
            app(ProductSkuAllocator::class),
        );
        $quickAdded = Product::where('name_th', 'สินค้าเพิ่มด่วน')->firstOrFail();

        $this->assertTrue($quickAddResponse->getData(true)['success']);
        $this->assertMatchesRegularExpression('/^\d{13}$/', $quickAdded->sku_code);
        $this->assertStringStartsWith('299', $quickAdded->sku_code);
        $this->assertTrue(app(BarcodePolicy::class)->isValidEan13($quickAdded->sku_code));
        $this->assertSame('8850000000017', $quickAdded->barcodes()->value('barcode'));

        ProductUnit::firstOrCreate(
            ['code' => 'EA-OFFLINE'],
            ['name' => 'ชิ้น', 'qty_per_base_unit' => 1],
        );
        $offlineResponse = app(PosOfflineProductController::class)->store(
            Request::create('/api/pos/products', 'POST', [
                'barcode' => '8850000000024',
                'name_th' => 'สินค้าออฟไลน์',
                'default_price' => 40,
            ]),
            app(ProductSkuAllocator::class),
        );
        $offlineProduct = Product::whereKey($offlineResponse->getData(true)['product_id'])->firstOrFail();

        $this->assertMatchesRegularExpression('/^\d{13}$/', $offlineProduct->sku_code);
        $this->assertNotSame($quickAdded->sku_code, $offlineProduct->sku_code);
        $this->assertTrue(app(BarcodePolicy::class)->isValidEan13($offlineProduct->sku_code));
        $this->assertSame('8850000000024', $offlineProduct->barcodes()->value('barcode'));
    }
}
