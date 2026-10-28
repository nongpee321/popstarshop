<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_sku_sequences', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->unsignedInteger('last_number')->default(0);
        });

        $highestNumericSku = DB::table('products')->pluck('sku_code')
            ->map(function ($sku): int {
                $sku = (string) $sku;
                if (preg_match('/^[0-9]{6}$/', $sku) === 1) {
                    return (int) $sku;
                }
                if (preg_match('/^299([0-9]{9})[0-9]$/', $sku, $matches) === 1) {
                    return (int) $matches[1];
                }

                return 0;
            })
            ->max() ?? 0;

        DB::table('product_sku_sequences')->insert(['id' => 1, 'last_number' => $highestNumericSku]);
    }

    public function down(): void
    {
        Schema::dropIfExists('product_sku_sequences');
    }
};
