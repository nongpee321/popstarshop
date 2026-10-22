<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Branch;
use App\Models\PriceTable;
use App\Models\Product;
use App\Models\ProductBarcode;
use App\Models\ProductPrice;
use App\Models\ProductUnit;
use App\Models\Promotion;
use App\Models\QtyPromotion;
use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use App\Models\Coupon;
use App\Models\Member;
use App\Models\PosDevice;
use App\Models\PosPriceSchedule;
use App\Models\User;
use App\Http\Controllers\PosController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

class PosRequirementsComprehensiveTest extends TestCase
{
    use RefreshDatabase;

    private function masters(): array
    {
        $branch = Branch::create([
            'code' => 'B01', 'name_th' => 'สาขาหลัก', 'name_en' => 'Main', 'is_active' => true,
        ]);
        $user = User::factory()->create([
            'username' => 'tester_' . uniqid(),
            'branch_id' => $branch->id,
        ]);
        $table = PriceTable::create([
            'code' => 'RETAIL', 'name' => 'ราคาปลีก', 'name_th' => 'ราคาปลีก', 'name_en' => 'Retail', 'is_default' => true, 'is_active' => true,
        ]);
        $branch->update(['default_price_table_id' => $table->id]);

        $unit = ProductUnit::create(['code' => 'PCS', 'name' => 'ชิ้น', 'name_th' => 'ชิ้น', 'qty_per_base_unit' => 1, 'is_active' => true]);
        $product = Product::create([
            'sku_code' => 'PROD01', 'name_th' => 'สินค้าทดสอบ 1', 'name_en' => 'Product 01',
            'base_unit_id' => $unit->id, 'default_price' => 100, 'is_active' => true,
        ]);
        ProductPrice::create([
            'price_table_id' => $table->id, 'product_id' => $product->id,
            'unit_id' => $unit->id, 'price' => 100,
        ]);

        $device = PosDevice::create([
            'code' => 'DEV-01',
            'name' => 'เครื่องขายหลัก',
            'branch_id' => $branch->id,
            'user_id' => $user->id,
            'token_hash' => hash('sha256', 'token-test'),
            'is_active' => true,
        ]);

        return [$user, $branch, $product, $table, $unit, $device];
    }

    /**
     * Requirement 2 & 3: Normal price & Single Item Promotion (Discount in baht and percent)
     */
    public function test_single_item_promotion_discount(): void
    {
        [$user, $branch, $product] = $this->masters();
        $this->actingAs($user);

        $promo = Promotion::create([
            'code' => 'PROMO-TEST',
            'name' => 'ลด 10 บาท',
            'promotion_type' => 'discount',
            'product_id' => $product->id,
            'discount_amount' => 10.00,
            'starts_at' => now()->subDay()->toDateString(),
            'ends_at' => now()->addDay()->toDateString(),
            'is_active' => true,
        ]);

        $this->assertTrue($promo->isRunningAt(now()));

        $items = collect(app(PosController::class)->products(Request::create('/pos/products', 'GET', ['branch_id' => $branch->id]))->getData(true));
        $found = $items->firstWhere('id', $product->id);

        $this->assertNotNull($found);
        $this->assertEquals(90.00, (float) $found['pos_price']);
        $this->assertEquals(100.00, (float) $found['default_price']);
        $this->assertTrue($found['is_promotion']);
    }

    /**
     * Requirement 4: Time-window price schedule (PosPriceSchedule)
     */
    public function test_pos_price_schedule(): void
    {
        [$user, $branch, $product] = $this->masters();
        $this->actingAs($user);

        PosPriceSchedule::create([
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'price' => 150.00,
            'effective_from' => now()->subHour(),
            'effective_to' => now()->addHour(),
            'status' => 'published',
        ]);

        $items = collect(app(PosController::class)->products(Request::create('/pos/products', 'GET', ['branch_id' => $branch->id]))->getData(true));
        $found = $items->firstWhere('id', $product->id);

        $this->assertNotNull($found);
        $this->assertEquals(150.00, (float) $found['pos_price']);
    }

    /**
     * Requirement 5: Flash Sale / นาทีทอง
     */
    public function test_flash_sale_pricing(): void
    {
        [$user, $branch, $product] = $this->masters();
        $this->actingAs($user);

        $flashSale = FlashSale::create([
            'code' => 'FLASH01',
            'name' => 'Flash Sale แคมเปญ',
            'branch_id' => $branch->id,
            'starts_date' => now()->subDay()->toDateString(),
            'ends_date' => now()->addDay()->toDateString(),
            'start_time' => now()->subHour()->format('H:i'),
            'end_time' => now()->addHour()->format('H:i'),
            'days_of_week' => (string) now()->dayOfWeek,
            'is_active' => true,
        ]);

        FlashSaleItem::create([
            'flash_sale_id' => $flashSale->id,
            'product_id' => $product->id,
            'flash_price' => 79.00,
        ]);

        $this->assertTrue($flashSale->isRunningAt(now()));

        $items = collect(app(PosController::class)->products(Request::create('/pos/products', 'GET', ['branch_id' => $branch->id]))->getData(true));
        $found = $items->firstWhere('id', $product->id);

        $this->assertNotNull($found);
        $this->assertEquals(79.00, (float) $found['pos_price']);
        $this->assertTrue($found['is_flash_sale']);
    }

    /**
     * Requirement 6 & 7: Buy-N Promotion (ซื้อครบแล้วลด & ซื้อครบแล้วแถม)
     */
    public function test_buy_n_qty_promotions_endpoint(): void
    {
        [$user, $branch, $product, $table, $unit] = $this->masters();
        $this->actingAs($user);

        $freeProduct = Product::create([
            'sku_code' => 'FREE01',
            'name_th' => 'ของแถมฟรี',
            'base_unit_id' => $unit->id,
            'default_price' => 20.00,
            'is_active' => true,
        ]);

        $qtyPromo = QtyPromotion::create([
            'code' => 'QTY-TEST',
            'name' => 'ซื้อ 2 แถม 1 ฟรี',
            'promo_type' => 'free_item',
            'product_id' => $product->id,
            'min_qty' => 2,
            'free_product_id' => $freeProduct->id,
            'free_qty' => 1,
            'branch_id' => $branch->id,
            'starts_date' => now()->subDay()->toDateString(),
            'ends_date' => now()->addDay()->toDateString(),
            'is_active' => true,
        ]);

        $promos = collect(app(PosController::class)->promotions(Request::create('/pos/promotions', 'GET', ['branch_id' => $branch->id]))->getData(true));
        $found = $promos->firstWhere('id', $qtyPromo->id);
        $this->assertNotNull($found);
        $this->assertEquals('free_item', $found['promo_type']);
        $this->assertEquals(2, $found['min_qty']);
    }

    /**
     * Requirement 9: Coupon verification
     */
    public function test_coupon_verification(): void
    {
        [$user, $branch, $product, $table, $unit, $device] = $this->masters();

        $coupon = Coupon::create([
            'code' => 'COUPON-TEST',
            'name' => 'คูปองทดสอบ 50 บาท',
            'discount_type' => 'baht',
            'discount_value' => 50.00,
            'min_purchase_amount' => 200.00,
            'is_active' => true,
        ]);

        // Below min amount -> 422
        $requestFail = Request::create('/api/pos/coupon/verify', 'POST', [
            'code' => $coupon->code,
            'subtotal' => 150.00,
        ]);
        $requestFail->attributes->set('pos_device', $device);
        $resFail = app(PosController::class)->verifyCoupon($requestFail);
        $this->assertEquals(422, $resFail->getStatusCode());

        // Above min amount -> 200
        $requestSuccess = Request::create('/api/pos/coupon/verify', 'POST', [
            'code' => $coupon->code,
            'subtotal' => 300.00,
        ]);
        $requestSuccess->attributes->set('pos_device', $device);
        $resSuccess = app(PosController::class)->verifyCoupon($requestSuccess);
        $this->assertEquals(200, $resSuccess->getStatusCode());
        $data = $resSuccess->getData(true);
        $this->assertTrue($data['valid']);
        $this->assertEquals(50.00, $data['discount']);
    }

    /**
     * Requirement 10: Members & points
     */
    public function test_member_lookup(): void
    {
        [$user, $branch] = $this->masters();
        $this->actingAs($user);

        $member = Member::create([
            'member_code' => 'MEM-TEST',
            'name' => 'สมาชิกทดสอบ',
            'phone' => '0899999999',
            'points' => 120.00,
            'is_active' => true,
        ]);

        $members = collect(app(PosController::class)->members(Request::create('/pos/members', 'GET', ['q' => '0899999999']))->getData(true));
        $found = $members->firstWhere('id', $member->id);
        $this->assertNotNull($found);
        $this->assertEquals('สมาชิกทดสอบ', $found['name']);
    }
}
