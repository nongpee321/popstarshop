<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;

class CouponTestSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::firstOrCreate(
            ['code' => 'DISC50'],
            [
                'name' => 'คูปองส่วนลด 50 บาท',
                'discount_type' => 'baht',
                'discount_value' => 50,
                'min_purchase_amount' => 200,
                'is_active' => true,
            ]
        );

        Coupon::firstOrCreate(
            ['code' => 'DISC10PCT'],
            [
                'name' => 'คูปองส่วนลด 10%',
                'discount_type' => 'percent',
                'discount_value' => 10,
                'min_purchase_amount' => 100,
                'max_discount_amount' => 100,
                'is_active' => true,
            ]
        );
    }
}
