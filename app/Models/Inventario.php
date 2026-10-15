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

    protected $appends = [
        'formatted_cod',
        'costo_taller',
        'is_motor',
        'is_camara',
        'is_caja',
        'camera_side',
        'camera_side_label',
        'label_title',
    ];

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
        'prorrateo_gastos',
        'porcentaje_utilidad',
        'tasa_bcv',
        'fecha_tasa_bcv',
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
        $expediente = ($this->container && $this->container->expediente) ? $this->container->expediente : $this->expediente;

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

        if ($this->price && (float) $this->price > 0 && !session()->has('temp_utilidad')) {
            return;
        }

        $newPrice = $costoImportacion + $costoTaller;

        $this->price = number_format($newPrice, 2, '.', '');
        $this->saveQuietly();
    }
    public function getIsMotorAttribute()
    {
        $all = strtoupper(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', ($this->tipo ?? '') . ' ' . ($this->item ?? '')));
        return str_contains($all, 'MOTOR');
    }

    public function getIsCamaraAttribute()
    {
        $all = strtoupper(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', ($this->tipo ?? '') . ' ' . ($this->item ?? '')));
        return str_contains($all, 'CAMARA') || str_contains($all, 'CULATA');
    }

    public function getIsCajaAttribute()
    {
        $all = strtoupper(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', ($this->tipo ?? '') . ' ' . ($this->item ?? '')));
        return str_contains($all, 'CAJA') || str_contains($all, 'TRANSMIS');
    }

    public function getCameraSideAttribute()
    {
        if (!$this->is_camara) {
            return null;
        }

        $all = strtoupper(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', ($this->observation ?? '') . ' ' . ($this->item ?? '') . ' ' . ($this->modelo ?? '')));
        $obs = strtoupper(trim($this->observation ?? ''));

        // Check Left: IZQUIERDA, IZQUIERDO, IZQ, LADO L, or standalone L
        if (
            preg_match('/\b(IZQUIERD[OA]|IZQ)\b/', $all) ||
            preg_match('/LADO\s*L\b/', $all) ||
            preg_match('/\bL\b/', $obs)
        ) {
            return 'L';
        }

        // Check Right: DERECHA, DERECHO, DER, LADO R, or standalone R
        if (
            preg_match('/\b(DERECH[OA]|DER)\b/', $all) ||
            preg_match('/LADO\s*R\b/', $all) ||
            preg_match('/\bR\b/', $obs)
        ) {
            return 'R';
        }

        return null;
    }

    public function getCameraSideLabelAttribute()
    {
        $side = $this->camera_side;
        if ($side === 'L') return 'IZQUIERDA';
        if ($side === 'R') return 'DERECHA';
        return null;
    }

    public function getLabelTitleAttribute()
    {
        $marca = trim($this->marca ?? '');
        $modelo = trim($this->modelo ?? '');

        if ($this->is_camara) {
            $side = $this->camera_side;
            $sideText = $side ? " ({$side})" : "";
            return "CÁMARA{$sideText} {$marca} {$modelo}";
        }

        if ($this->is_caja) {
            return "CAJA {$marca} {$modelo}";
        }

        if (!$this->is_motor && $this->tipo) {
            return "{$this->tipo} {$marca} {$modelo}";
        }

        return "{$marca} {$modelo}";
    }
}
