<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    use HasFactory;
     
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
