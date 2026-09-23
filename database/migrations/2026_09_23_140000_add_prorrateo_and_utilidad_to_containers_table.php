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
        Schema::table('containers', function (Blueprint $table) {
            if (!Schema::hasColumn('containers', 'prorrateo_gastos')) {
                $table->decimal('prorrateo_gastos', 15, 2)->nullable()->default(0.00)->after('tasa_bcv');
            }
            if (!Schema::hasColumn('containers', 'porcentaje_utilidad')) {
                $table->decimal('porcentaje_utilidad', 5, 2)->nullable()->default(20.00)->after('prorrateo_gastos');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('containers', function (Blueprint $table) {
            if (Schema::hasColumn('containers', 'prorrateo_gastos')) {
                $table->dropColumn('prorrateo_gastos');
            }
            if (Schema::hasColumn('containers', 'porcentaje_utilidad')) {
                $table->dropColumn('porcentaje_utilidad');
            }
        });
    }
};
