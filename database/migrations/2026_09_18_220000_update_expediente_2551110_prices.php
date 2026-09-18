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
        $expedientes = ['2551110', '255110', '255111'];

        foreach ($expedientes as $exp) {
            $container = DB::table('containers')
                ->where('expediente', $exp)
                ->first();

            $tasa = ($container && isset($container->tasa_bcv) && (float)$container->tasa_bcv > 0)
                ? (float)$container->tasa_bcv
                : null;

            // 1. Items de ~136.06$ -> 340.00$
            $items136 = DB::table('inventarios')
                ->where('expediente', $exp)
                ->where(function ($q) {
                    $q->whereBetween('costo', [130, 140])
                      ->orWhereBetween('costo_importacion_unitario', [130, 140]);
                })
                ->get();

            foreach ($items136 as $item) {
                $itemTasa = $tasa;
                if (!$itemTasa && (float)$item->costo > 0 && (float)$item->costo_importacion_unitario > (float)$item->costo) {
                    $itemTasa = (float)$item->costo_importacion_unitario / (float)$item->costo;
                }

                $newCostoUsd = 340.00;
                $newCostoBs = $itemTasa ? round($newCostoUsd * $itemTasa, 2) : $newCostoUsd;

                DB::table('inventarios')
                    ->where('id', $item->id)
                    ->update([
                        'costo' => $newCostoUsd,
                        'costo_importacion_unitario' => $newCostoBs,
                        'price' => $newCostoBs,
                    ]);
            }

            // 2. Items de ~205.16$ -> 400.00$
            $items205 = DB::table('inventarios')
                ->where('expediente', $exp)
                ->where(function ($q) {
                    $q->whereBetween('costo', [200, 210])
                      ->orWhereBetween('costo_importacion_unitario', [200, 210]);
                })
                ->get();

            foreach ($items205 as $item) {
                $itemTasa = $tasa;
                if (!$itemTasa && (float)$item->costo > 0 && (float)$item->costo_importacion_unitario > (float)$item->costo) {
                    $itemTasa = (float)$item->costo_importacion_unitario / (float)$item->costo;
                }

                $newCostoUsd = 400.00;
                $newCostoBs = $itemTasa ? round($newCostoUsd * $itemTasa, 2) : $newCostoUsd;

                DB::table('inventarios')
                    ->where('id', $item->id)
                    ->update([
                        'costo' => $newCostoUsd,
                        'costo_importacion_unitario' => $newCostoBs,
                        'price' => $newCostoBs,
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
