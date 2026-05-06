<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::with('roles', 'permissions')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->paginate(10)
            ->withQueryString();

        $roles = Role::all();
        $permissions = Permission::all();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'roles' => $roles,
            'availablePermissions' => $permissions,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole(Role::findByName($request->role, 'web'));

        return redirect()->back()->with('success', 'Usuario creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|exists:roles,name',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->syncRoles([Role::findByName($request->role, 'web')]);

        return redirect()->back()->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'No puedes eliminar tu propio usuario.');
        }
        $user->delete();
        return redirect()->back()->with('success', 'Usuario eliminado.');
    }

    public function assignTemporaryPermission(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'permission' => 'required|exists:permissions,name',
            'minutes' => 'required|integer|min:1',
        ]);

        $permission = $request->permission;
        $minutes = $request->minutes;

        // Give permission
        $user->givePermissionTo(Permission::findByName($permission, 'web'));

        // Record expiration
        DB::table('permission_expirations')->insert([
            'model_type' => User::class,
            'model_id' => $user->id,
            'permission_name' => $permission,
            'expires_at' => Carbon::now()->addMinutes($minutes),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', "Permiso '{$permission}' otorgado por {$minutes} minutos.");
    }
}
