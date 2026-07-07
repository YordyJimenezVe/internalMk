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

/**
 * Controlador para la administración de Usuarios e integración de Permisos Temporales.
 * 
 * Permite listar, registrar, editar y borrar usuarios del sistema, controlando
 * la asignación de roles de Spatie y proveyendo un mecanismo de seguridad avanzado
 * para otorgar permisos temporales que vencen automáticamente tras n minutos.
 */
class UserController extends Controller
{
    /**
     * Muestra la bandeja de usuarios registrados con filtros de búsqueda y listado de roles/permisos.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP con filtros opcionales.
     * @return \Inertia\Response
     */
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

    /**
     * Registra un nuevo usuario en la base de datos y le asigna el rol de Spatie indicado.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP con los datos de registro.
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Actualiza la información de un usuario existente.
     * 
     * Sincroniza su rol y actualiza de manera segura la contraseña si fue provista.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP con los datos modificados.
     * @param  string|int  $id  Identificador único del usuario a actualizar.
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Elimina un usuario de la base de datos.
     * 
     * Impide que un usuario autenticado se elimine a sí mismo por razones de seguridad.
     *
     * @param  string|int  $id  Identificador único del usuario a eliminar.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'No puedes eliminar tu propio usuario.');
        }
        $user->delete();
        return redirect()->back()->with('success', 'Usuario eliminado.');
    }

    /**
     * Otorga un permiso específico de Spatie a un usuario por un tiempo limitado de minutos.
     * 
     * Almacena el vencimiento en la tabla `permission_expirations` para su posterior
     * revocación automatizada.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP con el nombre del permiso y duración en minutos.
     * @param  string|int  $id  Identificador único del usuario al que se le otorga el permiso.
     * @return \Illuminate\Http\RedirectResponse
     */
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
