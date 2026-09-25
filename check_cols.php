<?php
use Illuminate\Support\Facades\Schema;
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = Schema::getColumnListing('maintenances');
print_r($columns);
if (in_array('costo', $columns)) {
    echo "\nCOLUMN 'costo' EXISTS\n";
} else {
    echo "\nCOLUMN 'costo' DOES NOT EXIST\n";
}
