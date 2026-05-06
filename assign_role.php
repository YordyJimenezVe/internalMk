<?php

use App\Models\User;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::where('email', 'yordyalejandro13@gmail.com')->first();
if ($user) {
    // Remove old roles just in case to be clean, or just add. 
    // Spatie handles multiple roles, but user implies this is THE role.
    $user->syncRoles(['Superusuario']);
    echo "Role 'Superusuario' assigned to {$user->email}\n";
} else {
    echo "User not found.\n";
}
