<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Inventario;
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
        'status',
        'partida_id',
        'cedula_mecanico',
        'nombre_mecanico',
        'apellido_mecanico',
        'observaciones',
        'costo',
    ];

    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'partida_id');
    }

    public function inventarios()
    {
        return $this->belongsTo(Inventario::class, 'partida_id');
    }

    public function partida()
    {
        return $this->belongsTo(Inventario::class, 'partida_id');
    }

    public function partidas()
    {
        return $this->belongsTo(Inventario::class, 'partida_id');
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

    public function items()
    {
        return $this->hasMany(MaintenanceItem::class, 'maintenance_id');
    }

    public function team()
    {
        return $this->hasMany(MaintenanceTeam::class, 'maintenance_id');
    }
}
