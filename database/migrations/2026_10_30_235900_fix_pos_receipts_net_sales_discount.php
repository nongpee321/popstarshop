<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fix past POS receipts where net_sales was recorded as gross_sales without subtracting discount_amount
        DB::table('pos_receipts')
            ->where('discount_amount', '>', 0)
            ->whereRaw('ABS(net_sales - gross_sales) < 0.01')
            ->update([
                'net_sales' => DB::raw('gross_sales - discount_amount'),
            ]);
    }

    public function down(): void
    {
        // No need to revert corrected net sales figures
    }
};
