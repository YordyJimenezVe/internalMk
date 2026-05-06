<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'yordyalejandro13@gmail.com';
$user = User::where('email', $email)->first();

if (!$user) {
    echo "User not found!\n";
    exit;
}

echo "User found: {$user->name} ({$user->id})\n";
echo "Legacy 'rol' column value: '{$user->rol}'\n";

// Check Spatie Roles
$roles = $user->getRoleNames();
echo "Current Spatie Roles: " . $roles->implode(', ') . "\n";

// Permissions
$permissions = $user->getAllPermissions()->pluck('name');
echo "Permissions count: " . $permissions->count() . "\n";
if ($permissions->contains('manage users')) {
    echo "HAS 'manage users' permission.\n";
} else {
    echo "MISSING 'manage users' permission.\n";
}

// Fix logic: Ensure case match
$roleName = 'Superusuario';
$role = Role::where('name', $roleName)->first();
if (!$role) {
    echo "Role '{$roleName}' does not exist in DB! Creating it...\n";
    $role = Role::create(['name' => $roleName]);
}

if (!$user->hasRole($roleName)) {
    echo "Assigning '{$roleName}' to user...\n";
    $user->syncRoles([$roleName]);
    echo "Assigned.\n";
} else {
    echo "User already has '{$roleName}' via Spatie.\n";
}

// Double check
$user->refresh();
echo "New Spatie Roles: " . $user->getRoleNames()->implode(', ') . "\n";
