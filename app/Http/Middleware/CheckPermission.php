<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    /**
     * Maneja una petición entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        if ($request->user()) {
            // Check and clean up expired temporary permissions in real-time
            $expired = \Illuminate\Support\Facades\DB::table('permission_expirations')
                ->where('model_type', \App\Models\User::class)
                ->where('model_id', $request->user()->id)
                ->where('expires_at', '<=', now())
                ->get();

            foreach ($expired as $record) {
                // Verify if there are other overlapping unexpired temporary permission entries for the same permission
                $stillActive = \Illuminate\Support\Facades\DB::table('permission_expirations')
                    ->where('model_type', \App\Models\User::class)
                    ->where('model_id', $request->user()->id)
                    ->where('permission_name', $record->permission_name)
                    ->where('expires_at', '>', now())
                    ->exists();

                // Only revoke if there is no other overlapping active record
                if (!$stillActive && $request->user()->hasDirectPermission($record->permission_name)) {
                    $request->user()->revokePermissionTo($record->permission_name);
                }

                // Delete the specific expired record
                \Illuminate\Support\Facades\DB::table('permission_expirations')->where('id', $record->id)->delete();
            }
        }

        if (!$request->user() || !$request->user()->can($permission)) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a este módulo.');
        }

        return $next($request);
    }
}
