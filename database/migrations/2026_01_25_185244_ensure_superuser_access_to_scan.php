<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure Permission Exists
        $permScan = Permission::firstOrCreate(['name' => 'access scan']);

        // Explicitly give to Superusuario
        $roleSuper = Role::where('name', 'Superusuario')->first();
        if ($roleSuper) {
            $roleSuper->givePermissionTo($permScan);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to revert specifically for Superuser as they should have all anyway
    }
};
