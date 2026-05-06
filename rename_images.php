<?php

$dir = __DIR__ . '/public/images_report/';
$files = [
    'maikelcars_landing_page_1774657943191.png' => 'landing.png',
    'maikelcars_dashboard_1774657952508.png' => 'dashboard.png',
    'maikelcars_inventario_1774657967946.png' => 'inventory.png',
    'maikelcars_maintenance_1774657972527.png' => 'maintenance.png',
    'maikelcars_reports_1774657976002.png' => 'reports.png'
];

foreach ($files as $old => $new) {
    if (file_exists($dir . $old)) {
        if (rename($dir . $old, $dir . $new)) {
            echo "Renombrado: $old -> $new\n";
        } else {
            echo "ERROR al renombrar: $old\n";
        }
    } else {
        echo "No se encontró: $old\n";
    }
}
