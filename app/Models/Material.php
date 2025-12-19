<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Maintenance;

class Material extends Model
{
    use HasFactory;
    protected $fillable = [
        'maintenances_id',
        'concha_biela',
        'concha_bancada',
        'anillos',
        'empacadura_camara',
        'empacadura_carter',
        'kit_empacaduras',
        'baño_quimico',
        'goma_valvula',
        'planos',
        'valvulas',
        'rectificadora',
        'asientos',
        'camisas',
        'levas',
        'pistones',
    ];
    
    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class, 'maintenances_id', 'id');
    }
}
