<?php

use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;

// Maximizar recursos
ini_set('memory_limit', '1024M');
set_time_limit(300);

// Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Generando Resumen Ejecutivo (Versión Lite)...\n";

$baseDir = __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'images_report' . DIRECTORY_SEPARATOR;

$data = [
    'landingImage'    => $baseDir . 'landing.png',
    'dashboardImage'  => $baseDir . 'dashboard.png',
    'inventoryImage'  => $baseDir . 'inventory.png',
    'maintenanceImage'=> $baseDir . 'maintenance.png',
    'reportsImage'    => $baseDir . 'reports.png'
];

try {
    $pdf = Pdf::loadView('reports.system_report_lite', $data)
        ->setPaper('letter', 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'chroot' => __DIR__,
        ]);
        
    $outputPath = __DIR__ . '/public/Resumen_Ejecutivo_Maikel_Cars.pdf';
    $pdf->save($outputPath);
    
    echo "¡Resumen Ejecutivo generado con éxito!\n";
    echo "Tamaño: " . filesize($outputPath) . " bytes\n";
    echo "Ruta: $outputPath\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
