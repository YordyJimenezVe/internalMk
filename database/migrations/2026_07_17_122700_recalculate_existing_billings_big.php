<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Billing;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('billings')) {
            return;
        }

        $billings = Billing::with('partida.maintenances.items')->get();

        foreach ($billings as $bill) {
            $partida = $bill->partida;
            if (!$partida) {
                continue;
            }

            $baseCosto = (float) ($partida->costo_importacion_unitario ?? $partida->costo ?? 0);
            
            $mantenimientosFacturables = 0;
            if ($partida->maintenances) {
                foreach ($partida->maintenances as $maint) {
                    $mantenimientosFacturables += (float) $maint->items()
                        ->where('document_type', 'FACTURA')
                        ->where('status', 'CONCILIADO')
                        ->sum('base_imponible');
                }
            }

            $correctBig = $baseCosto + $mantenimientosFacturables;
            
            $bigCents = (int) round($correctBig * 100);
            $ivaCents = (int) round($bigCents * 0.16);
            $totalCents = $bigCents + $ivaCents;

            $formattedBig = number_format($correctBig, 2, ',', '.');
            $formattedIva = number_format($ivaCents / 100, 2, ',', '.');
            $formattedPrecioTotal = number_format($totalCents / 100, 2, ',', '.');

            $bill->big = $formattedBig;
            $bill->iva = $formattedIva;
            $bill->precio_total = $formattedPrecioTotal;
            $bill->saveQuietly();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback operations needed for data correction
    }
};
