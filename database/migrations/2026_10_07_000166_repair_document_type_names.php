<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const NAMES = [
        'CASH_SALE' => 'ใบขายสด',
        'CREDIT_SALE' => 'ใบขายเชื่อ',
        'PURCHASE' => 'ใบซื้อ',
        'SALE_RETURN' => 'ใบรับคืนสินค้า',
        'STOCK_ISSUE' => 'ใบเบิกสินค้า',
        'STOCK_COUNT' => 'ใบตรวจนับสินค้า',
    ];

    public function up(): void
    {
        $types = DB::table('document_types')
            ->whereIn('code', array_keys(self::NAMES))
            ->get(['id', 'code', 'name_th']);

        foreach ($types as $type) {
            if (preg_match('/^\?+$/', (string) $type->name_th) !== 1) {
                continue;
            }

            DB::table('document_types')
                ->where('id', $type->id)
                ->update(['name_th' => self::NAMES[$type->code]]);
        }
    }

    public function down(): void
    {
        // Repaired labels are not reverted.
    }
};
