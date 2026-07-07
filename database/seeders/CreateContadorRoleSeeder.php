<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateContadorRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure permissions exist
        $permissions = [
            'view partida',
            'manage partida',
            'view billing',
            'manage billing',
            'view reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Create or retrieve the Contador role
        $role = Role::firstOrCreate(['name' => 'Contador', 'guard_name' => 'web']);
        $role->syncPermissions($permissions);

        // 3. Create or retrieve Raiza Cordero user
        $user = User::where('email', 'corderoraizae@gmail.com')->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Raiza Cordero',
                'email' => 'corderoraizae@gmail.com',
                'password' => Hash::make('password'), // In production this would be changed
                'rol' => 'Contador',
            ]);
        } else {
            $user->rol = 'Contador';
            $user->save();
        }

        // 4. Assign the role to user
        $user->syncRoles([$role]);

        $this->command->info('Role Contador created/updated and assigned to Raiza Cordero successfully.');
    }
}
