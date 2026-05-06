<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Inertia\Inertia;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Don't show Superusuario permissions to everyone to prevent modification if not superuser?
        // But user asked for Admin and Superuser to access this.
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        return Inertia::render('Roles/Index', [
            'roles' => $roles,
            'availablePermissions' => $permissions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array'
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $role = Role::findById($id, 'web');

        // Prevent editing Superusuario name? Maybe just permissions.
        if ($role->name === 'Superusuario') {
            // Maybe restrict renaming standard roles
        } else {
            $request->validate([
                'name' => 'required|string|unique:roles,name,' . $id,
            ]);
            $role->name = $request->name;
            $role->save();
        }

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $role = Role::findById($id, 'web');
        if ($role->name === 'Superusuario') {
            return redirect()->back()->with('error', 'No se puede eliminar el rol Superusuario.');
        }
        $role->delete();
        return redirect()->route('roles.index');
    }
}
