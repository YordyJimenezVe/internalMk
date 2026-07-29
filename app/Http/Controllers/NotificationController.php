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
     * Muestra la bandeja de notificaciones y panel de control administrativo.
     */
    public function index()
    {
        \App\Models\BillingRequest::cleanupNotifications();
        // Solo administradores pueden ver el panel completo
        if (auth()->user()->rol !== 'Administrador') {
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
     * Obtiene el listado de las 5 últimas notificaciones no leídas y el total.
     */
    public function getUnread()
    {
        \App\Models\BillingRequest::cleanupNotifications();
        $unread = auth()->user()->unreadNotifications;
        
        return response()->json([
            'unread_count' => $unread->count(),
            'notifications' => $unread->take(5)->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'title' => $notif->data['title'] ?? 'Alerta del Sistema',
                    'message' => $notif->data['message'] ?? '',
                    'action_url' => $notif->data['action_url'] ?? null,
                    'icon' => $notif->data['icon'] ?? 'fa-bell',
                    'color' => $notif->data['color'] ?? 'indigo',
                    'created_at' => $notif->created_at->diffForHumans(),
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

        return redirect()->back();
    }

    /**
     * Marca todas las notificaciones como leídas.
     */
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'Todas las notificaciones marcadas como leídas.');
    }

    /**
     * Cambia la habilitación de un interruptor de notificación.
     */
    public function toggleSetting(Request $request, $id)
    {
        if (auth()->user()->rol !== 'Administrador') {
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
        if (auth()->user()->rol !== 'Administrador') {
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
            $users = User::where('rol', $request->input('target_role'))->get();
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
