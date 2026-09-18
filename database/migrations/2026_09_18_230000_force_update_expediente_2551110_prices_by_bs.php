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
        // 1. Items de ~136.06$ (115,457.03 Bs) -> 340.00$ (288,505.57 Bs con tasa 848.5458)
        DB::table('inventarios')
            ->where(function ($q) {
                $q->whereBetween('costo_importacion_unitario', [110000, 120000])
                  ->orWhereBetween('costo', [130, 140]);
            })
            ->update([
                'costo' => 340.00,
                'costo_importacion_unitario' => 288505.57,
                'price' => 288505.57
            ]);

        // 2. Items de ~205.16$ (174,091.46 Bs) -> 400.00$ (339,418.32 Bs con tasa 848.5458)
        DB::table('inventarios')
            ->where(function ($q) {
                $q->whereBetween('costo_importacion_unitario', [170000, 180000])
                  ->orWhereBetween('costo', [200, 210]);
            })
            ->update([
                'costo' => 400.00,
                'costo_importacion_unitario' => 339418.32,
                'price' => 339418.32
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
