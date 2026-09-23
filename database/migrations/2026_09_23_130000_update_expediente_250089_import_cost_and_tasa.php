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

        $rate = 67.858;
        $fechaTasa = '2025-03-26';
        $prorrateo = 936.39;
        $utilidad = 20.00;

        // 2. Update containers matching expediente 250089 or container MRKU7908457
        DB::table('containers')
            ->where('expediente', '250089')
            ->orWhere('expediente', 'like', '%250089%')
            ->orWhere('cod', 'like', '%MRKU7908457%')
            ->update([
                'tasa_bcv' => $rate
            ]);

        // Get matching container IDs
        $containerIds = DB::table('containers')
            ->where('expediente', '250089')
            ->orWhere('expediente', 'like', '%250089%')
            ->orWhere('cod', 'like', '%MRKU7908457%')
            ->pluck('id');

        // 3. Get all inventario items for expediente 250089 or container MRKU7908457
        $items = DB::table('inventarios')
            ->where(function ($q) use ($containerIds) {
                $q->where('expediente', '250089')
                  ->orWhere('expediente', 'like', '%250089%');
                if ($containerIds->isNotEmpty()) {
                    $q->orWhereIn('container_id', $containerIds);
                }
            })
            ->get();

        foreach ($items as $item) {
            $costoBs = (float) ($item->costo_importacion_unitario ?? 0);
            
            // USD FOB = costoBs / 67.858 (e.g. 11,875.15 / 67.858 = 175.00 USD)
            $costoUsd = $costoBs > 0 ? round($costoBs / $rate, 2) : (float) ($item->costo ?? 0);
            
            $prorrateoItem = ($item->prorrateo_gastos && (float)$item->prorrateo_gastos > 0) ? (float)$item->prorrateo_gastos : $prorrateo;
            $utilidadItem = ($item->porcentaje_utilidad && (float)$item->porcentaje_utilidad > 0) ? (float)$item->porcentaje_utilidad : $utilidad;

            $landedBs = $costoBs + $prorrateoItem;
            $baseImponibleBs = round($landedBs * (1 + ($utilidadItem / 100)), 2); // 15,373.85
            $precioConIvaBs = $landedBs * (1 + ($utilidadItem / 100)) * 1.16; // 17,833.66
            $priceSaleUsd = round($precioConIvaBs / $rate, 2); // 262.81 USD

            DB::table('inventarios')
                ->where('id', $item->id)
                ->update([
                    'costo' => $costoUsd,
                    'fecha_tasa_bcv' => $fechaTasa,
                    'prorrateo_gastos' => $prorrateoItem,
                    'porcentaje_utilidad' => $utilidadItem,
                    'price' => $baseImponibleBs,
                    'price_sale' => $priceSaleUsd,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
