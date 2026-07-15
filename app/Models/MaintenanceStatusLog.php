<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_id',
        'status',
        'photo_path',
    ];

    /**
     * Get the maintenance record associated with this log entry.
     */
    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class);
    }
}
