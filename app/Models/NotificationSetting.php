<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    /**
     * Verifica si una notificación específica está habilitada en el sistema.
     *
     * @param string $key
     * @return bool
     */
    public static function isNotificationEnabled(string $key): bool
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->enabled : true;
    }
}
