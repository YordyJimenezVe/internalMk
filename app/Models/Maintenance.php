<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Inventario;
use App\Models\MaintenanceBill;
use App\Models\Material;
use App\Models\AccesorioEngine;
use App\Models\MaintenanceItem;
use App\Models\MaintenanceTeam;
use App\Models\MaintenanceStatusLog;

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

    protected $appends = ['costo_real', 'costo_b_i_g'];

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

    public function statusLogs()
    {
        return $this->hasMany(MaintenanceStatusLog::class, 'maintenance_id');
    }

    public function getCostoRealAttribute()
    {
        $total = 0;
        
        $material = $this->materials()->first();
        if ($material) {
            $fields = [
                'concha_biela', 'concha_bancada', 'anillos', 'empacadura_camara',
                'empacadura_carter', 'kit_empacaduras', 'baño_quimico', 'goma_valvula',
                'planos', 'valvulas', 'rectificadora', 'asientos', 'camisas', 'levas', 'pistones'
            ];
            foreach ($fields as $field) {
                $value = str_replace(',', '.', $material->$field ?? '0');
                $total += floatval($value);
            }
        }

        $accesorios = $this->accesorios_engine()->first();
        if ($accesorios) {
            $fields = ['valve_cover', 'chain_cover', 'carter', 'pescador'];
            foreach ($fields as $field) {
                $value = str_replace(',', '.', $accesorios->$field ?? '0');
                $total += floatval($value);
            }
        }

        $total += $this->items()->sum('cost');

        return $total;
    }

    public function getCostoBIGAttribute()
    {
        return (float) $this->items()
            ->where('document_type', 'FACTURA')
            ->where('status', 'CONCILIADO')
            ->sum('base_imponible');
    }
}
