<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 150);
            $table->string('discount_type', 20)->default('baht'); // baht or percent
            $table->decimal('discount_value', 18, 4);
            $table->decimal('min_purchase_amount', 18, 4)->default(0);
            $table->decimal('max_discount_amount', 18, 4)->nullable();
            $table->date('starts_date')->nullable();
            $table->date('ends_date')->nullable();
            $table->integer('usage_limit')->nullable(); // max times can be used
            $table->integer('times_used')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
