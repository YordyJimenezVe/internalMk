<?php
use App\Models\User;
use Spatie\Permission\Models\Role;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Limpiar caché de Spatie
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
echo "✅ Caché de permisos limpiada.\n";

// 2. Buscar al usuario Jose Sierra
$email = 'josesierraaes@gmail.com';
$user = User::where('email', $email)->first();

if (!$user) {
    echo "❌ Usuario no encontrado: $email\n";
    exit;
}

echo "Usuario: {$user->name} (ID: {$user->id})\n";
echo "Roles actuales (Spatie): " . $user->getRoleNames()->implode(', ') . "\n";
echo "Permisos totales: " . $user->getAllPermissions()->pluck('name')->implode(', ') . "\n";

if ($user->can('access scan')) {
    echo "✅ El usuario TIENE el permiso 'access scan'.\n";
} else {
    echo "❌ El usuario NO TIENE el permiso 'access scan'. Intentando asignar al rol...\n";
    
    // Asegurar que el rol tenga el permiso
    foreach ($user->roles as $role) {
        $role->givePermissionTo('access scan');
        echo "   -> Asignado permiso a rol: {$role->name}\n";
    }
}

echo "\n--- Intenta ahora en el navegador (refresca la página) ---\n";
