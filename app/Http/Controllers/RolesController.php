<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Inertia\Inertia;

/**
 * Controlador para la gestión de Roles y Permisos en el sistema.
 * 
 * Permite administrar las agrupaciones de permisos definidos por el sistema (Spatie),
 * creando nuevos roles, modificando sus permisos asignados y restringiendo de forma
 * segura la edición o eliminación del rol raíz 'Superusuario'.
 */
class RolesController extends Controller
{
    /**
     * Muestra la matriz de roles y permisos configurados.
     *
     * @return \Inertia\Response
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
     * Almacena un nuevo rol en la base de datos y le asocia los permisos indicados.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP con el nombre del rol y el array de permisos.
     * @return \Illuminate\Http\RedirectResponse
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
     * Actualiza el nombre del rol (exceptuando 'Superusuario') y sincroniza sus permisos.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP con los nuevos datos.
     * @param  int  $id  Identificador único del rol a actualizar.
     * @return \Illuminate\Http\RedirectResponse
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
     * Elimina un rol específico de la base de datos.
     * 
     * Impide estrictamente la eliminación del rol vital 'Superusuario'.
     *
     * @param  int  $id  Identificador único del rol a eliminar.
     * @return \Illuminate\Http\RedirectResponse
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
