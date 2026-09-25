<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_id',
        'description',
        'cost',
        'invoice_path',
        'type', // REPUESTO, SERVICIO
        'source',
        'document_type',
        'invoice_number',
        'base_imponible',
        'status',
        'outflow_date',
        'return_date',
        'notes',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($item) {
            if ($item->maintenance && $item->maintenance->inventario) {
                $item->maintenance->inventario->recalculatePrice();
            }
        });

        static::deleted(function ($item) {
            if ($item->maintenance && $item->maintenance->inventario) {
                $item->maintenance->inventario->recalculatePrice();
            }
        });
    }

    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class);
    }
}
