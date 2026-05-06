<?php
use Spatie\Permission\Models\Role;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Limpiar caché de Spatie
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
echo "✅ Caché de permisos limpiada.\n";

$roles = ['MECANICO', 'Tecnico', 'Mecanico'];

foreach ($roles as $name) {
    $role = Role::where('name', $name)->first();
    if ($role) {
        $role->givePermissionTo(['access scan', 'view maintenance', 'create maintenance']);
        echo "✅ Permisos asignados a: $name\n";
    } else {
        echo "⚠️ Rol no encontrado: $name\n";
    }
}

echo "--- Proceso completado. Prueba en el navegador. ---\n";
