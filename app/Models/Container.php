<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($container) {
            if ($container->wasChanged('expediente')) {
                $container->items()->update(['expediente' => $container->expediente]);
            }
        });
    }
     
    protected $fillable = [
        'fecha',
        'cod',
        'expediente',
        'hora',
        'motores',
        'cajas',
        'camaras',
        'accesorios',
        'costo_importacion_general',
        'aplicar_costos',
    ];

    public function items()
    {
        return $this->hasMany(Inventario::class, 'container_id');
    }
}
