<?php

use App\Models\Inventario;
use App\Models\Container;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$isDryRun = in_array('--dry-run', $argv) || in_array('-d', $argv);
$isApply = in_array('--apply', $argv);

if (!$isDryRun && !$isApply) {
    echo "Uso:\n";
    echo "  php assign_060325_costs.php --dry-run   (Simulación sin modificar la BD)\n";
    echo "  php assign_060325_costs.php --apply     (Aplicar los cambios en la BD)\n\n";
    exit(0);
}

echo "===============================================================\n";
echo $isDryRun ? "MODO SIMULACIÓN (DRY-RUN)\n" : "MODO APLICACIÓN DIRECTA EN BD\n";
echo "===============================================================\n\n";

// Fetch all containers for date and tasa mapping
$containers = Container::all()->keyBy('expediente');

// Fetch reference pool: Items from other expedientes with prorrateo_gastos > 0
// Years 2023, 2024, 2025, 2026, ordered by date ascending
$referencePool = Inventario::where('expediente', '!=', '060325')
    ->where('prorrateo_gastos', '>', 0)
    ->get()
    ->filter(function ($item) use ($containers) {
        $container = $containers->get($item->expediente);
        if (!$container || !$container->fecha) return false;
        $year = (int) date('Y', strtotime($container->fecha));
        return $year >= 2023 && $year <= 2026;
    })
    ->sortBy(function ($item) use ($containers) {
        $container = $containers->get($item->expediente);
        return $container ? $container->fecha : '9999-99-99';
    })
    ->values();

echo "Pool de referencia disponible: " . $referencePool->count() . " ítems de expedientes 2023-2026.\n\n";

// Target items in expediente 060325
// Phase 1: prorrateo_gastos == 0 or null
// Phase 2: costo > 410
$targetItems = Inventario::where('expediente', '060325')
    ->get()
    ->filter(function ($i) {
        $noProrrateo = empty($i->prorrateo_gastos) || (float)$i->prorrateo_gastos == 0;
        $highCost = (float)$i->costo > 410;
        $hasRefNote = str_contains($i->observation ?? '', 'SE USÓ REF DE EXP:');
        return $noProrrateo || $highCost || $hasRefNote;
    });

echo "Total de ítems en expediente 060325 a procesar: " . $targetItems->count() . "\n\n";

$updatedCount = 0;
$notFoundCount = 0;

foreach ($targetItems as $target) {
    $tipoNorm = strtolower(trim($target->tipo ?? ''));
    $marcaNorm = strtolower(trim($target->marca ?? ''));
    $modeloNorm = strtolower(trim($target->modelo ?? ''));

    // 1. Try exact match: tipo + marca + modelo
    $match = $referencePool->first(function ($ref) use ($tipoNorm, $marcaNorm, $modeloNorm) {
        return strtolower(trim($ref->tipo ?? '')) === $tipoNorm
            && strtolower(trim($ref->marca ?? '')) === $marcaNorm
            && strtolower(trim($ref->modelo ?? '')) === $modeloNorm;
    });

    $matchLevel = "Exacto (Tipo + Marca + Modelo)";

    // 2. Try match: tipo + marca
    if (!$match && $marcaNorm !== '') {
        $match = $referencePool->first(function ($ref) use ($tipoNorm, $marcaNorm) {
            return strtolower(trim($ref->tipo ?? '')) === $tipoNorm
                && strtolower(trim($ref->marca ?? '')) === $marcaNorm;
        });
        $matchLevel = "Parcial (Tipo + Marca)";
    }

    // 3. Fallback match: tipo
    if (!$match) {
        $match = $referencePool->first(function ($ref) use ($tipoNorm) {
            return strtolower(trim($ref->tipo ?? '')) === $tipoNorm;
        });
        $matchLevel = "Categoría (Tipo)";
    }

    // 4. Smart fallback: if type starts with 'motor', match any motor type with marca/modelo
    if (!$match && str_contains($tipoNorm, 'motor')) {
        $match = $referencePool->first(function ($ref) use ($marcaNorm, $modeloNorm) {
            $refTipo = strtolower(trim($ref->tipo ?? ''));
            return str_contains($refTipo, 'motor')
                && ($marcaNorm === '' || strtolower(trim($ref->marca ?? '')) === $marcaNorm);
        });
        $matchLevel = "Motor Fallback (Marca/Motor)";
    }

    if (!$match) {
        echo "❌ [NO MATCH] ID: {$target->id} | Cod: {$target->codInv} | Tipo: {$target->tipo} | Marca: {$target->marca} | Modelo: {$target->modelo}\n";
        $notFoundCount++;
        continue;
    }

    $refContainer = $containers->get($match->expediente);
    $rate = $refContainer && (float)$refContainer->tasa_bcv > 0 ? (float)$refContainer->tasa_bcv : 736.933; // Fallback rate if missing

    $costoUsd = (float)($match->costo ?? 0);
    $costoBs = (float)($match->costo_importacion_unitario ?? 0);
    if ($costoBs == 0 && $costoUsd > 0) {
        $costoBs = round($costoUsd * $rate, 2);
    }
    if ($costoUsd == 0 && $costoBs > 0) {
        $costoUsd = round($costoBs / $rate, 2);
    }

    $prorrateo = (float)($match->prorrateo_gastos ?? 0);
    $utilidad = (float)($match->porcentaje_utilidad ?? 20.00);

    // Financial formulas
    $landedBs = $costoBs + $prorrateo;
    $baseImponibleBs = round($landedBs * (1 + ($utilidad / 100)), 2);
    $precioConIvaBs = $landedBs * (1 + ($utilidad / 100)) * 1.16;
    $priceSaleUsd = round($precioConIvaBs / $rate, 2);

    $fechaTasa = $refContainer && $refContainer->fecha ? $refContainer->fecha : date('Y-m-d');
    $refText = "SE USÓ REF DE EXP: {$match->expediente}";

    $existingObs = trim($target->observation ?? '');
    if (strpos($existingObs, 'SE USÓ REF DE EXP:') !== false) {
        $newObs = preg_replace('/SE USÓ REF DE EXP: \S+/', $refText, $existingObs);
    } else {
        $newObs = $existingObs ? "{$existingObs} | {$refText}" : $refText;
    }

    echo "✅ [MATCH {$matchLevel}] ID: {$target->id} ({$target->tipo} {$target->marca} {$target->modelo})\n";
    echo "   Ref: Exp {$match->expediente} | Costo USD: \${$costoUsd} | Costo Bs: {$costoBs} | Prorrateo: {$prorrateo} | Utilidad: {$utilidad}%\n";
    echo "   Price (BIG Bs): {$baseImponibleBs} | PriceSale (USD): \${$priceSaleUsd} | Obs: {$newObs}\n\n";

    if ($isApply) {
        Inventario::where('id', $target->id)->update([
            'costo' => $costoUsd,
            'costo_importacion_unitario' => $costoBs,
            'prorrateo_gastos' => $prorrateo,
            'porcentaje_utilidad' => $utilidad,
            'tasa_bcv' => $rate,
            'fecha_tasa_bcv' => $fechaTasa,
            'price' => $baseImponibleBs,
            'price_sale' => $priceSaleUsd,
            'observation' => $newObs,
        ]);
    }

    $updatedCount++;
}

echo "===============================================================\n";
echo "RESUMEN:\n";
echo "Procesados con exito: {$updatedCount}\n";
echo "Sin coincidencia: {$notFoundCount}\n";
echo $isDryRun ? "Simulación completada. No se modificó la BD.\n" : "Cambios aplicados exitosamente en la BD.\n";
echo "===============================================================\n";
