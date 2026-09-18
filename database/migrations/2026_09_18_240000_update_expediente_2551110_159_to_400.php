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
        // Items de ~159.04$ (134,952.72 Bs con tasa 848.5458) -> 400.00$ (339,418.32 Bs)
        DB::table('inventarios')
            ->where(function ($q) {
                $q->whereBetween('costo_importacion_unitario', [130000, 140000])
                  ->orWhereBetween('costo', [155, 163]);
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
