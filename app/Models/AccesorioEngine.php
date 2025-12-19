<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Maintenance;

class AccesorioEngine extends Model
{
    use HasFactory;
    protected $fillable = [
        'maintenances_id',
        'valve_cover',
        'chain_cover',
        'carter',
        'pescador',
    ];
    
    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class, 'maintenances_id', 'id');
    }
}
