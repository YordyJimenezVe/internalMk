<?php

use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;

// Maximizar recursos para el renderizado
ini_set('memory_limit', '1024M');
set_time_limit(300);

// Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Generando Reporte Final (Rutas Simples v3)...\n";

$baseDir = __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'images_report' . DIRECTORY_SEPARATOR;

$data = [
    'landingImage'    => $baseDir . 'landing.png',
    'dashboardImage'  => $baseDir . 'dashboard.png',
    'inventoryImage'  => $baseDir . 'inventory.png',
    'maintenanceImage'=> $baseDir . 'maintenance.png',
    'reportsImage'    => $baseDir . 'reports.png'
];

foreach ($data as $key => $path) {
    if (file_exists($path)) {
        echo "Validado: $key ($path)\n";
    } else {
        echo "ERROR: No existe $path\n";
    }
}

try {
    $pdf = Pdf::loadView('reports.system_report', $data)
        ->setPaper('letter', 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'chroot' => __DIR__,
        ]);
        
    $outputPath = __DIR__ . '/public/Informe_Sistema_Maikel_Cars_Final.pdf';
    $pdf->save($outputPath);
    
    echo "¡Reporte Final generado con éxito!\n";
    echo "Tamaño: " . filesize($outputPath) . " bytes\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
