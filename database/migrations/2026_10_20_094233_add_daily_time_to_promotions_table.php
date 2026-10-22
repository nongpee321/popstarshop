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
        Schema::table('promotions', function (Blueprint $table) {
            $table->time('start_time')->nullable()->after('ends_at');
            $table->time('end_time')->nullable()->after('start_time');
            $table->string('days_of_week')->nullable()->after('end_time'); // e.g. "0,1,2,3,4,5,6" or null = everyday
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time', 'days_of_week']);
        });
    }
};
