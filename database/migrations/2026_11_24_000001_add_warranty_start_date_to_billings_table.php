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
        if (!Schema::hasColumn('billings', 'warranty_start_date')) {
            Schema::table('billings', function (Blueprint $table) {
                $afterCol = Schema::hasColumn('billings', 'warranty_months') ? 'warranty_months' : 'observaciones';
                $table->date('warranty_start_date')->nullable()->after($afterCol);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('billings', 'warranty_start_date')) {
            Schema::table('billings', function (Blueprint $table) {
                $table->dropColumn('warranty_start_date');
            });
        }
    }
};
