<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotificationSetting;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        NotificationSetting::updateOrCreate(
            ['key' => 'notify_outflow'],
            [
                'name' => 'Pieza a Rectificadora',
                'description' => 'Notifica a los administradores cuando una pieza sale del taller hacia la rectificadora.',
                'enabled' => true
            ]
        );

        NotificationSetting::updateOrCreate(
            ['key' => 'notify_return'],
            [
                'name' => 'Entrada de Rectificadora',
                'description' => 'Notifica a los administradores y encargados cuando una pieza regresa de la rectificadora para que se registre la factura.',
                'enabled' => true
            ]
        );

        NotificationSetting::updateOrCreate(
            ['key' => 'notify_pending_conciliation'],
            [
                'name' => 'Conciliación Pendiente',
                'description' => 'Notifica al departamento de facturación cuando se ha cargado una nueva factura de taller para ser conciliada.',
                'enabled' => true
            ]
        );

        NotificationSetting::updateOrCreate(
            ['key' => 'notify_low_stock'],
            [
                'name' => 'Bajo Stock de Repuestos',
                'description' => 'Notifica a los administradores y almacén si la existencia de algún repuesto cae por debajo del mínimo.',
                'enabled' => true
            ]
        );
    }
}
