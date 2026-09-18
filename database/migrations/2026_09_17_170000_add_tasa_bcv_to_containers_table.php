<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('containers', 'tasa_bcv')) {
            Schema::table('containers', function (Blueprint $table) {
                $table->decimal('tasa_bcv', 10, 4)->nullable()->after('costo_importacion_general');
            });
        }

        // Backfill specific rates per expediente as requested
        DB::table('containers')
            ->where('expediente', '260723')
            ->update(['tasa_bcv' => 736.933]);

        DB::table('containers')
            ->where('expediente', '259705')
            ->update(['tasa_bcv' => 311.84]);

        DB::table('containers')
            ->where('expediente', '255095')
            ->update(['tasa_bcv' => 330.38]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('containers', 'tasa_bcv')) {
            Schema::table('containers', function (Blueprint $table) {
                $table->dropColumn('tasa_bcv');
            });
        }
    }
};
