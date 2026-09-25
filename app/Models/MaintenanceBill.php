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
        'mechanic',
        'mechanic_assistant',
        'seller',
        'seller_assistant',
        'cleaning',
        'consumables',
        'camera_technician',
        'camera_technical_assistant',
        'forklift',
    ];

    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class, 'maintenances_id', 'id');
    }
}

