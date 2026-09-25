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
        if (!Schema::hasColumn('inventarios', 'tasa_bcv')) {
            Schema::table('inventarios', function (Blueprint $table) {
                $table->decimal('tasa_bcv', 15, 4)->nullable()->after('porcentaje_utilidad');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('inventarios', 'tasa_bcv')) {
            Schema::table('inventarios', function (Blueprint $table) {
                $table->dropColumn('tasa_bcv');
            });
        }
    }
};
