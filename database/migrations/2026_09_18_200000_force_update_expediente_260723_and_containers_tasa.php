<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ensure tasa_bcv column exists on containers table
        if (!Schema::hasColumn('containers', 'tasa_bcv')) {
            Schema::table('containers', function ($table) {
                $table->decimal('tasa_bcv', 10, 4)->nullable()->after('costo_importacion_general');
            });
        }

        // 2. Update container rates
        DB::table('containers')->where('expediente', '260723')->update(['tasa_bcv' => 736.933]);
        DB::table('containers')->where('expediente', '259705')->update(['tasa_bcv' => 311.84]);
        DB::table('containers')->where('expediente', '255095')->update(['tasa_bcv' => 330.38]);

        // 3. Update items in expediente 260723
        DB::table('inventarios')
            ->where('expediente', '260723')
            ->update([
                'costo' => 218.00,
                'costo_importacion_unitario' => 160651.39,
                'price' => 282532.91
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
