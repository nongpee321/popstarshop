<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! DB::table('document_types')->where('code', 'CASH_SALE')->exists()) {
            DB::table('document_types')->insert([
                'code' => 'CASH_SALE',
                'name_th' => 'ใบขายสด',
                'name_en' => 'Cash sale',
                'affects_stock' => true,
                'affects_ar' => false,
                'affects_ap' => false,
                'is_active' => true,
            ]);
        }
    }

    public function down(): void
    {
        $id = DB::table('document_types')->where('code', 'CASH_SALE')->value('id');

        if ($id && ! DB::table('documents')->where('document_type_id', $id)->exists()) {
            DB::table('document_types')->where('id', $id)->delete();
        }
    }
};
