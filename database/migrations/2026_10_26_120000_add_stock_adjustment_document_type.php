<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! DB::table('document_types')->where('code', 'STOCK_ADJUSTMENT')->exists()) {
            DB::table('document_types')->insert([
                'code' => 'STOCK_ADJUSTMENT',
                'name_th' => 'ใบปรับสต๊อก',
                'name_en' => 'Stock adjustment',
                'affects_stock' => true,
                'affects_ar' => false,
                'affects_ap' => false,
                'is_active' => true,
            ]);
        }
    }

    public function down(): void
    {
        $id = DB::table('document_types')->where('code', 'STOCK_ADJUSTMENT')->value('id');

        if ($id && ! DB::table('documents')->where('document_type_id', $id)->exists()) {
            DB::table('document_types')->where('id', $id)->delete();
        }
    }
};
