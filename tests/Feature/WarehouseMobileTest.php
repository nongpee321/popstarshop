<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\DocumentType;
use App\Models\Permission;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Role;
use App\Models\StockBalance;
use App\Models\StockCount;
use App\Models\StockCountItem;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class WarehouseMobileTest extends TestCase
{
    use RefreshDatabase;

    public function test_mobile_page_exposes_the_four_workflow_tabs_to_receiving_users(): void
    {
        $user = $this->userWith(['stock.manage', 'purchasing.manage']);

        $this->actingAs($user)->get(route('wh.index'))
            ->assertOk()
            ->assertSee('รับเข้า')
            ->assertSee('รับตาม PO')
            ->assertSee('ตรวจนับ')
            ->assertSee('เช็คสต๊อก')
            ->assertSee("tab: 'receive',", false)
            ->assertSee('x-show="tab === \'receive\'"', false)
            ->assertSee('x-show="tab === \'po\'"', false)
            ->assertSee('x-show="tab === \'count\'"', false)
            ->assertSee('x-show="tab === \'stock\'"', false);
    }

    public function test_stock_only_user_starts_on_stock_with_valid_javascript(): void
    {
        $this->actingAs($this->userWith(['stock.manage']))->get(route('wh.index'))
            ->assertOk()
            ->assertSee("tab: 'stock',", false)
            ->assertSee('switchTab(\'count\')', false)
            ->assertDontSee("switchTab('receive')", false)
            ->assertDontSee("switchTab('po')", false);
    }

    public function test_stock_lookup_is_scoped_to_the_selected_branch(): void
    {
        $user = $this->userWith(['stock.manage']);
        [$branchA, $locationA] = $this->branchWithLocation('A');
        [$branchB, $locationB] = $this->branchWithLocation('B');
        $unit = ProductUnit::create(['code' => 'EA', 'name' => 'ชิ้น']);
        $product = Product::create([
            'sku_code' => 'WH-001', 'name_th' => 'สินค้าทดสอบคลังมือถือ',
            'base_unit_id' => $unit->id, 'is_active' => true,
        ]);
        StockBalance::create(['product_id' => $product->id, 'warehouse_location_id' => $locationA->id, 'on_hand_qty' => 12]);
        StockBalance::create(['product_id' => $product->id, 'warehouse_location_id' => $locationB->id, 'on_hand_qty' => 99]);

        $this->actingAs($user)->getJson(route('wh.stock', [
            'product_id' => $product->id, 'branch_id' => $branchA->id,
        ]))
            ->assertOk()
            ->assertJsonCount(1, 'locations')
            ->assertJsonPath('locations.0.location_id', $locationA->id)
            ->assertJsonPath('total', 12);
    }

    public function test_branch_bound_user_cannot_widen_stock_lookup_to_another_branch(): void
    {
        [$branchA, $locationA] = $this->branchWithLocation('C');
        [$branchB, $locationB] = $this->branchWithLocation('D');
        $user = $this->userWith(['stock.manage'], $branchA);
        $unit = ProductUnit::create(['code' => 'EA-BOUND', 'name' => 'ชิ้น']);
        $product = Product::create([
            'sku_code' => 'WH-002', 'name_th' => 'สินค้าทดสอบสิทธิ์สาขา',
            'base_unit_id' => $unit->id, 'is_active' => true,
        ]);
        StockBalance::create(['product_id' => $product->id, 'warehouse_location_id' => $locationA->id, 'on_hand_qty' => 7]);
        StockBalance::create(['product_id' => $product->id, 'warehouse_location_id' => $locationB->id, 'on_hand_qty' => 88]);

        $this->actingAs($user)->getJson(route('wh.stock', [
            'product_id' => $product->id, 'branch_id' => $branchB->id,
        ]))
            ->assertOk()
            ->assertJsonCount(1, 'locations')
            ->assertJsonPath('locations.0.location_id', $locationA->id)
            ->assertJsonPath('total', 7);
    }

    public function test_mobile_stock_counts_list_and_counting_workflow(): void
    {
        [$branch, $location] = $this->branchWithLocation('STK');
        $user = $this->userWith(['stock.manage'], $branch);

        $unit = ProductUnit::create(['code' => 'EA-STK', 'name' => 'ชิ้น']);
        $product = Product::create([
            'sku_code' => 'STK-001', 'name_th' => 'สินค้าตรวจนับ',
            'base_unit_id' => $unit->id, 'is_active' => true,
        ]);

        $stockCount = StockCount::create([
            'doc_number' => 'SC-202610-0001',
            'branch_id' => $branch->id,
            'warehouse_location_id' => $location->id,
            'status' => 'counting',
            'count_mode' => 'partial',
        ]);

        $item = StockCountItem::create([
            'stock_count_id' => $stockCount->id,
            'product_id' => $product->id,
            'system_qty' => 10,
            'counted_qty' => null,
        ]);

        // 1. List active stock counts
        $this->actingAs($user)->getJson(route('wh.stock-counts', ['branch_id' => $branch->id]))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.doc_number', 'SC-202610-0001')
            ->assertJsonPath('0.status', 'counting');

        // 2. Fetch stock count detail
        $this->actingAs($user)->getJson(route('wh.stock-counts.detail', $stockCount))
            ->assertOk()
            ->assertJsonPath('doc_number', 'SC-202610-0001')
            ->assertJsonPath('items.0.sku_code', 'STK-001')
            ->assertJsonPath('items.0.system_qty', 10);

        // 3. Save counted quantities (Draft)
        $this->actingAs($user)->postJson(route('wh.stock-counts.save', $stockCount), [
            'items' => [
                ['id' => $item->id, 'counted_qty' => 12],
            ],
        ])
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('updated', 1);

        $this->assertEquals(12, $item->fresh()->counted_qty);

        // 4. Submit for review
        $this->actingAs($user)->postJson(route('wh.stock-counts.submit', $stockCount))
            ->assertOk()
            ->assertJsonPath('ok', true);

        $this->assertEquals('review', $stockCount->fresh()->status);
    }

    public function test_receive_store_prevents_duplicate_submission_via_idempotency_key(): void
    {
        DocumentType::firstOrCreate(['code' => 'PURCHASE'], ['name_th' => 'ใบซื้อ', 'stock_effect' => 'in']);
        [$branch, $location] = $this->branchWithLocation('RCV');
        $user = $this->userWith(['purchasing.manage', 'stock.manage'], $branch);

        $supplier = Supplier::create([
            'code' => 'SUP-001',
            'name_th' => 'ซัพพลายเออร์ ทดสอบ',
            'is_active' => true,
        ]);

        $unit = ProductUnit::create(['code' => 'EA-RCV', 'name' => 'ชิ้น']);
        $product = Product::create([
            'sku_code' => 'RCV-001',
            'name_th' => 'สินค้ารับเข้า',
            'base_unit_id' => $unit->id,
            'is_active' => true,
            'default_price' => 50,
        ]);

        $idempotencyKey = (string) Str::uuid();

        $payload = [
            'idempotency_key' => $idempotencyKey,
            'branch_id' => $branch->id,
            'supplier_id' => $supplier->id,
            'is_credit' => true,
            'remark' => 'ทดสอบ Idempotency',
            'items' => [
                [
                    'product_id' => $product->id,
                    'qty' => 5,
                    'unit_price' => 45,
                ],
            ],
        ];

        // First submit -> creates document
        $res1 = $this->actingAs($user)->postJson(route('wh.receive'), $payload);
        $res1->assertOk()
            ->assertJsonPath('ok', true);

        $docNumber1 = $res1->json('doc_number');

        // Immediate second submit with same idempotency key -> returns same cached response without duplicate creation
        $res2 = $this->actingAs($user)->postJson(route('wh.receive'), $payload);
        $res2->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('doc_number', $docNumber1);
    }

    private function userWith(array $permissionCodes, ?Branch $branch = null): User
    {
        static $sequence = 0;
        $user = User::factory()->create([
            'username' => 'warehouse-mobile-'.(++$sequence),
            'name' => 'Warehouse Mobile Test',
            'branch_id' => $branch?->id,
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $role = Role::create(['code' => 'WH_MOBILE_'.($sequence), 'name' => 'Warehouse Mobile']);
        $ids = collect($permissionCodes)->map(fn (string $code) =>
            Permission::firstOrCreate(['code' => $code], ['name' => $code])->id
        );
        $role->permissions()->sync($ids->all());
        $user->roles()->attach($role->id);

        return $user->fresh();
    }

    /** @return array{0: Branch, 1: WarehouseLocation} */
    private function branchWithLocation(string $suffix): array
    {
        $branch = Branch::create(['code' => 'WH-'.$suffix, 'name_th' => 'คลัง '.$suffix, 'is_active' => true]);
        $warehouse = Warehouse::create(['branch_id' => $branch->id, 'code' => 'WH-'.$suffix, 'name' => 'คลัง '.$suffix]);
        $location = WarehouseLocation::create(['warehouse_id' => $warehouse->id, 'code' => 'MAIN', 'name' => 'หลัก']);
        $branch->update(['default_warehouse_location_id' => $location->id]);

        return [$branch->fresh(), $location];
    }
}
