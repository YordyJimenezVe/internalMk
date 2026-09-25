<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\NotificationSetting;
use App\Notifications\SystemAlertNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationSystemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Valida que la configuración de notificaciones se pueda leer y cambiar de estado.
     */
    public function test_notification_settings_can_be_checked(): void
    {
        $setting = NotificationSetting::create([
            'key' => 'test_alert',
            'name' => 'Alerta de Prueba',
            'description' => 'Descripción de la alerta de prueba',
            'enabled' => true,
        ]);

        $this->assertTrue(NotificationSetting::isNotificationEnabled('test_alert'));

        $setting->update(['enabled' => false]);

        $this->assertFalse(NotificationSetting::isNotificationEnabled('test_alert'));
    }

    /**
     * Valida que las notificaciones del sistema se envíen de manera correcta y tengan los atributos correctos.
     */
    public function test_system_alert_notification_can_be_sent(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'rol' => 'Administrador',
        ]);

        $notification = new SystemAlertNotification(
            'Test Title',
            'Test Message',
            '/test-url',
            'fa-bell',
            'indigo'
        );

        $user->notify($notification);

        Notification::assertSentTo(
            [$user],
            SystemAlertNotification::class,
            function ($notif) {
                $data = $notif->toArray(new \stdClass());
                return $data['title'] === 'Test Title' && $data['message'] === 'Test Message' && $data['action_url'] === '/test-url';
            }
        );
    }
}
