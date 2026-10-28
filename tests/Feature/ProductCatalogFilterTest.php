<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Product;
use App\Models\ProductBarcode;
use App\Models\ProductCategory;
use App\Models\ProductUnit;
use App\Models\Role;
use App\Models\User;
use App\Services\InternalEan13Allocator;
use App\Support\BarcodePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProductCatalogFilterTest extends TestCase
{
    use RefreshDatabase;

    private function manager(): User
    {
        $user = User::factory()->create([
            'username' => 'product-filter-manager',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $role = Role::create(['code' => 'PRODUCT_FILTER', 'name' => 'Product filter']);
        $permission = Permission::firstOrCreate(['code' => 'masterdata.manage'], ['name' => 'จัดการข้อมูลหลัก']);
        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        return $user->fresh();
    }

    public function test_product_page_can_show_all_active_and_inactive_products(): void
    {
        $category = ProductCategory::create(['code' => '101', 'name_th' => 'เนื้อสัตว์']);
        $unit = ProductUnit::create(['code' => 'KG', 'name' => 'กิโลกรัม', 'qty_per_base_unit' => 1]);
        Product::create(['sku_code' => '101001', 'name_th' => 'สินค้าใช้งาน', 'product_category_id' => $category->id, 'base_unit_id' => $unit->id, 'default_price' => 10, 'is_active' => true]);
        Product::create(['sku_code' => '101002', 'name_th' => 'สินค้าพักไว้', 'product_category_id' => $category->id, 'base_unit_id' => $unit->id, 'default_price' => 10, 'is_active' => false]);
        $user = $this->manager();

        $this->actingAs($user)->get('/products?status=active')
            ->assertOk()->assertSee('สินค้าใช้งาน')->assertDontSee('สินค้าพักไว้');
        $this->actingAs($user)->get('/products?status=inactive')
            ->assertOk()->assertSee('สินค้าพักไว้')->assertDontSee('สินค้าใช้งาน');
        $this->actingAs($user)->get('/products?status=all&category_id='.$category->id)
            ->assertOk()->assertSee('สินค้าใช้งาน')->assertSee('สินค้าพักไว้')
            ->assertSee('สินค้าทั้งหมด')->assertSee('กำลังใช้งาน')->assertSee('ไม่ใช้งาน / พักไว้');
    }

    public function test_scale_product_filter_matches_scale_names_and_scale_barcodes(): void
    {
        $unit = ProductUnit::create(['code' => 'KG-SCALE', 'name' => 'กิโลกรัม', 'qty_per_base_unit' => 1]);
        Product::create([
            'sku_code' => '101101', 'legacy_sku' => 'OLD-WEIGHT', 'name_th' => 'หมูชั่งน้ำหนัก', 'base_unit_id' => $unit->id,
            'default_price' => 189, 'is_active' => true,
        ]);
        $barcodeScaleProduct = Product::create([
            'sku_code' => '101102', 'name_th' => 'หมูสด', 'base_unit_id' => $unit->id,
            'default_price' => 189, 'is_active' => true,
        ]);
        Product::create([
            'sku_code' => '101103', 'name_th' => 'น้ำดื่ม', 'base_unit_id' => $unit->id,
            'default_price' => 10, 'is_active' => true,
        ]);
        ProductBarcode::create([
            'product_id' => $barcodeScaleProduct->id, 'barcode' => '801001',
            'unit_id' => $unit->id, 'unit_factor' => 1, 'is_active' => true,
        ]);

        $user = $this->manager();
        $this->actingAs($user)->get('/products?product_type=scale')
            ->assertOk()
            ->assertSee('หมูชั่งน้ำหนัก')
            ->assertSee('หมูสด')
            ->assertDontSee('น้ำดื่ม');
        $this->actingAs($user)->get('/products?q=OLD-WEIGHT&product_type=scale')
            ->assertOk()->assertSee('หมูชั่งน้ำหนัก');
    }

    public function test_product_barcode_label_displays_copyable_ean13(): void
    {
        $unit = ProductUnit::create(['code' => 'EA-QR', 'name' => 'ชิ้น', 'qty_per_base_unit' => 1]);
        $product = Product::create([
            'sku_code' => '000123', 'name_th' => 'สินค้าทดสอบ QR',
            'base_unit_id' => $unit->id, 'default_price' => 10, 'is_active' => true,
        ]);
        $barcode = DB::transaction(function () use ($product): string {
            $value = app(InternalEan13Allocator::class)->next();
            $product->barcodes()->create([
                'barcode' => $value,
                'barcode_type' => BarcodePolicy::INTERNAL_13,
                'unit_id' => $product->base_unit_id,
                'unit_factor' => 1,
                'is_active' => true,
            ]);

            return $value;
        });

        $this->actingAs($this->manager())
            ->get(route('products.barcode-label', $product))
            ->assertOk()
            ->assertSee($barcode)
            ->assertSee('EAN-13 barcode')
            ->assertSee('คัดลอกเลข EAN-13')
            ->assertSee('navigator.clipboard.writeText(value)')
            ->assertSee('32–34 มม.')
            ->assertSee('18 มม.')
            ->assertSee('01010', false)
            ->assertDontSee('Code 128 barcode')
            ->assertSee('window.print()');
    }

    public function test_product_barcode_label_requires_ean13(): void
    {
        $unit = ProductUnit::create(['code' => 'EA-EAN-MISSING', 'name' => 'ชิ้น', 'qty_per_base_unit' => 1]);
        $product = Product::create([
            'sku_code' => '000456', 'name_th' => 'สินค้าที่ยังไม่มี EAN',
            'base_unit_id' => $unit->id, 'default_price' => 10, 'is_active' => true,
        ]);

        $this->actingAs($this->manager())
            ->get(route('products.barcode-label', $product))
            ->assertStatus(422);
    }

    public function test_internal_ean13_command_adds_codes_without_changing_skus(): void
    {
        $unit = ProductUnit::create(['code' => 'EA-EAN-BATCH', 'name' => 'ชิ้น', 'qty_per_base_unit' => 1]);
        $product = Product::create([
            'sku_code' => '000789', 'name_th' => 'สินค้าเตรียมบาร์โค้ด',
            'base_unit_id' => $unit->id, 'default_price' => 10, 'is_active' => true,
        ]);
        $database = DB::connection()->getDatabaseName();

        $this->artisan('products:ensure-internal-ean13')
            ->expectsOutputToContain('ยังไม่มีการเปลี่ยนข้อมูล')
            ->assertExitCode(0);
        $this->assertSame(0, $product->barcodes()->count());

        $this->artisan('products:ensure-internal-ean13', [
            '--apply' => true,
            '--confirm-database' => $database,
            '--force' => true,
        ])->assertExitCode(0);

        $barcode = $product->barcodes()->sole();
        $this->assertSame('000789', $product->fresh()->sku_code);
        $this->assertSame(BarcodePolicy::INTERNAL_13, $barcode->barcode_type);
        $this->assertTrue(app(BarcodePolicy::class)->isValidEan13($barcode->barcode));
    }
}
