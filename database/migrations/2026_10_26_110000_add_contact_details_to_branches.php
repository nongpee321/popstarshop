<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->text('address')->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('business_hours', 150)->nullable();
            $table->string('location_note', 255)->nullable();
            $table->text('service_area')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['address', 'phone', 'business_hours', 'location_note', 'service_area']);
        });
    }
};
