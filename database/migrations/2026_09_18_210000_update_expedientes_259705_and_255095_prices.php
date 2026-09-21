<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Expediente 259705 (Tasa BCV 311.84)
        // Items ~ $333.37 USD (103,958.08 Bs) -> $365.00 USD (113,821.60 Bs)
        DB::table('inventarios')
            ->where('expediente', '259705')
            ->whereBetween('costo_importacion_unitario', [100000, 108000])
            ->update([
                'costo' => 365.00,
                'costo_importacion_unitario' => 113821.60,
                'price' => 113821.60
            ]);

        // Items ~ $374.46 USD (116,772.81 Bs) -> $410.00 USD (127,854.40 Bs)
        DB::table('inventarios')
            ->where('expediente', '259705')
            ->whereBetween('costo_importacion_unitario', [113000, 120000])
            ->update([
                'costo' => 410.00,
                'costo_importacion_unitario' => 127854.40,
                'price' => 127854.40
            ]);

        // 2. Expediente 255095 (Tasa BCV 330.38)
        // Items ~ $244.14 USD (80,658.41 Bs) -> $300.00 USD (99,114.00 Bs)
        DB::table('inventarios')
            ->where('expediente', '255095')
            ->whereBetween('costo_importacion_unitario', [75000, 85000])
            ->update([
                'costo' => 300.00,
                'costo_importacion_unitario' => 99114.00,
                'price' => 99114.00
            ]);

        // Items ~ $329.59 USD (108,889.78 Bs) -> $405.00 USD (133,803.90 Bs)
        DB::table('inventarios')
            ->where('expediente', '255095')
            ->whereBetween('costo_importacion_unitario', [105000, 112000])
            ->update([
                'costo' => 405.00,
                'costo_importacion_unitario' => 133803.90,
                'price' => 133803.90
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
