<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Partida;
use App\Models\MaintenanceBill;
use App\Models\Material;
use App\Models\AccesorioEngine;

class Maintenance extends Model
{
    use HasFactory;
    protected $fillable = [
        'fecha',
        'descripcion',
        'tipo',
        'estado',
        'partida_id',
        'cedula_mecanico',
        'nombre_mecanico',
        'apellido_mecanico',
        'observaciones',
    ];

    public function partida()
    {
        return $this->belongsTo(Partida::class, 'partida_id');
    }

    public function bills()
    {
        return $this->hasMany(MaintenanceBill::class, 'maintenances_id');
    }

    public function materials()
    {
        return $this->hasMany(Material::class, 'maintenances_id');
    }

    public function accesorios_engine()
    {
        return $this->hasMany(AccesorioEngine::class, 'maintenances_id');
    }
}
