<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // --- Create Permissions ---
        $permissions = [
            'manage users',
            'manage roles',
            'manage backups',
            'view bitacora',
            'delete bitacora',
            'view maintenance',
            'create maintenance',
            'view partida',
            'manage partida', // create/edit/delete
            'view billing',
            'manage billing',
            'view reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // --- Create Roles & Assign Permissions ---

        // 1. SUPERUSUARIO
        $superAdmin = Role::firstOrCreate(['name' => 'Superusuario']);
        $superAdmin->givePermissionTo(Permission::all());

        // 2. ADMINISTRADOR
        $admin = Role::firstOrCreate(['name' => 'Administrador']);
        $admin->givePermissionTo([
            'manage users',
            'manage roles',
            'view bitacora', // No delete
            'view maintenance',
            'create maintenance',
            'view partida',
            'manage partida',
            'view billing',
            'manage billing',
            'view reports',
        ]);

        // 3. TECNICO
        $tecnico = Role::firstOrCreate(['name' => 'Tecnico']);
        $tecnico->givePermissionTo([
            'view maintenance',
            'create maintenance',
        ]);

        // 4. INVENTARIO
        $inventario = Role::firstOrCreate(['name' => 'Inventario']);
        $inventario->givePermissionTo([
            'view partida',
            'manage partida',
        ]);

        // 5. FACTURACION (Antes Solicitudes)
        $facturacion = Role::firstOrCreate(['name' => 'Facturacion']);
        $facturacion->givePermissionTo([
            'view billing',
            'manage billing',
        ]);

        // --- Migrating Existing Users (Best Guess) ---
        // We look for users with 'rol' column if it exists or generic logic
        $users = User::all();
        foreach ($users as $user) {
            if ($user->rol) {
                $rolName = $user->rol; // Assuming raw string matches or close to it
                // Logic to map old roles to new Spatie roles
                if (stripos($rolName, 'super') !== false) {
                    $user->assignRole('Superusuario');
                } elseif (stripos($rolName, 'admin') !== false) {
                    $user->assignRole('Administrador');
                } elseif (stripos($rolName, 'tecn') !== false) {
                    $user->assignRole('Tecnico');
                } elseif (stripos($rolName, 'inv') !== false) {
                    $user->assignRole('Inventario');
                } elseif (stripos($rolName, 'fact') !== false) {
                    $user->assignRole('Facturacion');
                }
            }
        }
    }
}
