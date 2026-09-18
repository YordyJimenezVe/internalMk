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
        // Search for items in expedientes matching 2551110, 255110, 255111, or 25511
        $items = DB::table('inventarios')
            ->where(function ($query) {
                $query->where('expediente', '2551110')
                      ->orWhere('expediente', '255110')
                      ->orWhere('expediente', '255111')
                      ->orWhere('expediente', 'like', '%2551110%')
                      ->orWhere('expediente', 'like', '%255110%');
            })
            ->get();

        foreach ($items as $item) {
            // Find container rate if available
            $container = DB::table('containers')
                ->where('expediente', $item->expediente)
                ->orWhere('id', $item->container_id ?? 0)
                ->first();

            $tasa = 0;
            if ($container && isset($container->tasa_bcv) && (float)$container->tasa_bcv > 0) {
                $tasa = (float)$container->tasa_bcv;
            } elseif ((float)$item->costo > 0 && (float)$item->costo_importacion_unitario > (float)$item->costo) {
                $tasa = (float)$item->costo_importacion_unitario / (float)$item->costo;
            } else {
                // Default rate for expediente 2551110 as seen in UI: 848.5458
                $tasa = 848.5458;
            }

            // Calculate current USD import cost
            $currentUsdCost = (float)$item->costo;
            if ($currentUsdCost <= 0 && $tasa > 0 && (float)$item->costo_importacion_unitario > 0) {
                $currentUsdCost = (float)$item->costo_importacion_unitario / $tasa;
            }

            // Check ranges for $136.06 and $205.16 (both in USD and in converted Bs.)
            $is136 = ($currentUsdCost >= 130 && $currentUsdCost <= 142) ||
                     ((float)$item->costo_importacion_unitario >= 110000 && (float)$item->costo_importacion_unitario <= 125000);

            $is205 = ($currentUsdCost >= 200 && $currentUsdCost <= 210) ||
                     ((float)$item->costo_importacion_unitario >= 165000 && (float)$item->costo_importacion_unitario <= 180000);

            if ($is136) {
                $newUsd = 340.00;
                $newBs = round($newUsd * $tasa, 2);

                DB::table('inventarios')
                    ->where('id', $item->id)
                    ->update([
                        'costo' => $newUsd,
                        'costo_importacion_unitario' => $newBs,
                        'price' => $newBs,
                    ]);
            } elseif ($is205) {
                $newUsd = 400.00;
                $newBs = round($newUsd * $tasa, 2);

                DB::table('inventarios')
                    ->where('id', $item->id)
                    ->update([
                        'costo' => $newUsd,
                        'costo_importacion_unitario' => $newBs,
                        'price' => $newBs,
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
