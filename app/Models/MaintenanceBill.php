<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Maintenance;

class MaintenanceBill extends Model
{
    use HasFactory;
    protected $fillable = [
        'maintenances_id',
        'multi_tools',
        'multi_equipament',
        'mechanic',
        'mechanic_assistant',
        'seller',
        'seller_assistant',
        'cleaning',
        'drinking_water',
        'consumables',
        'camera_technician',
        'camera_technical_assistant',
        'forklift_driver',
    ];
    
    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class, 'maintenances_id', 'id');
    }
}

