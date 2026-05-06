<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = User::all();
foreach ($users as $user) {
    $user->password = Hash::make('password');
    $user->save();
    echo "Password updated for: {$user->email}\n";
}

echo "All passwords have been set to 'password'.\n";
