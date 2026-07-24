<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Container;
use App\Models\Billing;

class Inventario extends Model
{
    use HasFactory;

    protected $table = 'inventarios';

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->marca) {
                $model->marca = mb_strtoupper($model->marca);
            }
            if ($model->modelo) {
                $model->modelo = mb_strtoupper($model->modelo);
            }
            if ($model->serial) {
                $model->serial = mb_strtoupper($model->serial);
            }
        });

        static::saved(function ($model) {
            $model->recalculatePrice();
        });
    }

    protected $fillable = [
        'tipo',
        'marca',
        'modelo',
        'serial',
        'año',
        'codInv',
        'expediente',
        'condicion',
        'status',
        'price',
        'price_sale',
        'container_id',
        'cantidad',
        'categorie',
        'item',
        'costo',
        'costo_importacion_unitario',
        'origen',
        'observation',
        'serial_image_path',
    ];

    public function container()
    {
        return $this->belongsTo(Container::class, 'container_id');
    }

    /**
     * Get the formatted code for label printing.
     * If the container starts with letters, return those letters followed by the first 2 digits of the expediente.
     * Otherwise, if it has only numbers or doesn't start with letters, return the container code.
     * Fallback to the expediente if no container code is available.
     */
    public function getFormattedCodAttribute()
    {
        $cod = $this->container ? $this->container->cod : null;
        $expediente = $this->expediente;

        if (!$cod) {
            return $expediente;
        }

        if (preg_match('/^[a-zA-Z]+/', $cod, $matches)) {
            $letters = $matches[0];
            $firstTwoNumbers = substr($expediente ?? '', 0, 2);
            return $letters . $firstTwoNumbers;
        }

        return $cod;
    }

    public function bill()
    {
        return $this->hasMany(Billing::class, 'partida_id'); // Assuming 'partida_id' is the foreign key
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class, 'partida_id');
    }

    public function billingRequests()
    {
        return $this->hasMany(BillingRequest::class, 'partida_id');
    }

    public function getCostoTallerAttribute()
    {
        $total = 0;
        foreach ($this->maintenances as $maintenance) {
            $total += (float) $maintenance->items()
                ->where('document_type', 'FACTURA')
                ->sum('base_imponible');
        }
        return $total;
    }

    public function recalculatePrice()
    {
        $costoImportacion = (float) $this->costo_importacion_unitario;
        $costoTaller = (float) $this->getCostoTallerAttribute();
        $utilidad = (float) \App\Models\Setting::get('utility_percentage', 30);

        $newPrice = ($costoImportacion + $costoTaller) * (1 + $utilidad / 100);

        $this->price = number_format($newPrice, 2, '.', '');
        $this->saveQuietly();
    }
}
