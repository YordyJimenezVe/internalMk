<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Crear el permiso si no existe
        $permission = Permission::firstOrCreate(['name' => 'edit maintenance items']);

        // Asignarlo a Superusuario
        $roleSuper = Role::where('name', 'Superusuario')->first();
        if ($roleSuper) {
            $roleSuper->givePermissionTo($permission);
        }

        // Asignarlo a Mecanico
        $roleMecanico = Role::where('name', 'Mecanico')->first();
        if ($roleMecanico) {
            $roleMecanico->givePermissionTo($permission);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Desasociar y eliminar el permiso
        $roleSuper = Role::where('name', 'Superusuario')->first();
        if ($roleSuper) {
            $roleSuper->revokePermissionTo('edit maintenance items');
        }

        $roleMecanico = Role::where('name', 'Mecanico')->first();
        if ($roleMecanico) {
            $roleMecanico->revokePermissionTo('edit maintenance items');
        }

        $permission = Permission::where('name', 'edit maintenance items')->first();
        if ($permission) {
            $permission->delete();
        }
    }
};
