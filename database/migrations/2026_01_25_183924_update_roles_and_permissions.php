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
        // 1. Rename Roles
        $roleTecnico = Role::where('name', 'Tecnico')->first();
        if ($roleTecnico) {
            $roleTecnico->name = 'Mecanico';
            $roleTecnico->save();
        }

        $roleInventario = Role::where('name', 'Inventario')->first();
        if ($roleInventario) {
            $roleInventario->name = 'Gestor de Inventario';
            $roleInventario->save();
        }

        // 2. Create 'Vendedor' Role
        $roleVendedor = Role::firstOrCreate(['name' => 'Vendedor']);

        // 3. Create Permissions if they don't exist
        Permission::firstOrCreate(['name' => 'view partida']);
        Permission::firstOrCreate(['name' => 'access scan']);

        // 4. Assign Permissions

        // Vendedor permissions
        $roleVendedor->givePermissionTo('view partida');
        $roleVendedor->givePermissionTo('access scan');

        // SuperUser & Admin also get access scan
        $roleSuper = Role::where('name', 'Superusuario')->first();
        if ($roleSuper)
            $roleSuper->givePermissionTo('access scan');

        $roleAdmin = Role::where('name', 'Administrador')->first();
        if ($roleAdmin)
            $roleAdmin->givePermissionTo('access scan');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert Names
        $roleMecanico = Role::where('name', 'Mecanico')->first();
        if ($roleMecanico) {
            $roleMecanico->name = 'Tecnico';
            $roleMecanico->save();
        }

        $roleGestor = Role::where('name', 'Gestor de Inventario')->first();
        if ($roleGestor) {
            $roleGestor->name = 'Inventario';
            $roleGestor->save();
        }
    }
};
