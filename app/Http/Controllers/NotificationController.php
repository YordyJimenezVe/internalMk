<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\NotificationSetting;
use App\Notifications\SystemAlertNotification;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Comprueba si el usuario autenticado tiene rol administrativo.
     */
    private function isAdminUser(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        $role = strtolower(trim($user->rol ?? ''));
        if (in_array($role, ['administrador', 'superusuario'])) {
            return true;
        }
        return $user->hasAnyRole(['Administrador', 'ADMINISTRADOR', 'Superusuario', 'SUPERUSUARIO']);
    }

    /**
     * Muestra la bandeja de notificaciones y panel de control administrativo.
     */
    public function index()
    {
        \App\Models\BillingRequest::cleanupNotifications();

        if (!$this->isAdminUser()) {
            return redirect()->route('dashboard')->with('error', 'No tiene permisos para acceder a esta sección.');
        }

        // Obtener historial global de notificaciones enviadas
        $allNotifications = DB::table('notifications')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get()
            ->map(function ($notif) {
                $data = json_decode($notif->data, true);
                $notifiableUser = User::find($notif->notifiable_id);
                return [
                    'id' => $notif->id,
                    'title' => $data['title'] ?? 'Sin Título',
                    'message' => $data['message'] ?? '',
                    'icon' => $data['icon'] ?? 'fa-bell',
                    'color' => $data['color'] ?? 'indigo',
                    'user_name' => $notifiableUser ? $notifiableUser->name : 'Usuario Desconocido',
                    'user_email' => $notifiableUser ? $notifiableUser->email : '',
                    'read_at' => $notif->read_at,
                    'created_at' => $notif->created_at,
                ];
            });

        return Inertia::render('Notifications/AdminIndex', [
            'sentNotifications' => $allNotifications,
            'settings' => NotificationSetting::all(),
            'roles' => User::distinct()->whereNotNull('rol')->where('rol', '!=', '')->pluck('rol'),
            'users' => User::select('id', 'name', 'email', 'rol')->get(),
        ]);
    }

    /**
     * Obtiene el listado de las 10 últimas notificaciones no leídas y el total.
     */
    public function getUnread()
    {
        \App\Models\BillingRequest::cleanupNotifications();
        
        $user = auth()->user();
        if (!$user) {
            return response()->json(['unread_count' => 0, 'notifications' => []]);
        }

        $unreadCount = $user->unreadNotifications()->count();
        $recent = $user->notifications()->latest()->take(10)->get();
        
        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $recent->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'title' => $notif->data['title'] ?? 'Alerta del Sistema',
                    'message' => $notif->data['message'] ?? '',
                    'action_url' => $notif->data['action_url'] ?? null,
                    'icon' => $notif->data['icon'] ?? 'fa-bell',
                    'color' => $notif->data['color'] ?? 'indigo',
                    'is_read' => $notif->read_at !== null,
                    'created_at' => $notif->created_at ? $notif->created_at->diffForHumans() : '',
                ];
            }),
        ]);
    }

    /**
     * Marca una notificación como leída.
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back();
    }

    /**
     * Marca todas las notificaciones como leídas.
     */
    public function markAllAsRead(Request $request)
    {
        auth()->user()->unreadNotifications->markAsRead();

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Todas las notificaciones marcadas como leídas.');
    }

    /**
     * Cambia la habilitación de un interruptor de notificación.
     */
    public function toggleSetting(Request $request, $id)
    {
        if (!$this->isAdminUser()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $setting = NotificationSetting::findOrFail($id);
        $setting->update([
            'enabled' => (bool) $request->input('enabled'),
        ]);

        return redirect()->back()->with('success', 'Configuración de alertas modificada con éxito.');
    }

    /**
     * Envía un comunicado/broadcast manual de administración.
     */
    public function sendBroadcast(Request $request)
    {
        if (!$this->isAdminUser()) {
            return redirect()->route('dashboard')->with('error', 'No tiene permisos para enviar comunicados.');
        }

        $request->validate([
            'target_type' => 'required|in:ALL,ROLE,USER',
            'target_role' => 'nullable|string',
            'target_user' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'icon' => 'required|string',
            'color' => 'required|string|in:indigo,rose,emerald,amber',
        ], [
            'title.required' => 'El título de la notificación es obligatorio.',
            'message.required' => 'El cuerpo de la notificación es obligatorio.',
        ]);

        $title = $request->input('title');
        $message = $request->input('message');
        $icon = $request->input('icon');
        $color = $request->input('color');

        $notification = new SystemAlertNotification($title, $message, null, $icon, $color);

        if ($request->input('target_type') === 'ALL') {
            $users = User::all();
            foreach ($users as $user) {
                $user->notify($notification);
            }
        } elseif ($request->input('target_type') === 'ROLE') {
            $targetRole = strtolower($request->input('target_role'));
            $users = User::whereRaw('LOWER(rol) = ?', [$targetRole])->get();
            foreach ($users as $user) {
                $user->notify($notification);
            }
        } else {
            $user = User::findOrFail($request->input('target_user'));
            $user->notify($notification);
        }

        return redirect()->back()->with('success', 'Anuncio de administración enviado exitosamente.');
    }
}