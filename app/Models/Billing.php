<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use App\Models\Inventario;
use App\Models\BillingRequest;
use App\Models\User;

class Billing extends Model
{
    use HasFactory;

    public const MIN_WARRANTY_MONTHS = 1;
    public const MAX_WARRANTY_MONTHS = 4;

    protected $appends = [
        'tipo_item',
        'is_pair_camera',
    ];

    protected $casts = [
        'warranty_months' => 'integer',
    ];

    protected $fillable = [
        'fecha',
        'hora',
        'partida_id',
        'second_partida_id',
        'big',
        'igtf',
        'iva',
        'value_divisa',
        'bs',
        'divisa',
        'precio_total',
        'numero_factura',
        'numero_control',
        'numero_nota_credito',
        'numero_factura_afect',
        'client_name',
        'client_cedula',
        'client_phone',
        'client_address',
        'client_email',
        'total',
        'status',
        'observaciones',
        'warranty_months',
    ];

    public static function hasWarrantyMonthsColumn(): bool
    {
        try {
            return Schema::hasColumn((new static())->getTable(), 'warranty_months');
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function clampWarrantyMonths(int $months): int
    {
        return max(self::MIN_WARRANTY_MONTHS, min(self::MAX_WARRANTY_MONTHS, $months));
    }

    public function resolveWarrantyCategory(): ?string
    {
        if ($this->is_pair_camera || !empty($this->second_partida_id)) {
            return 'CAMARA';
        }
        if ($this->partida && $this->partida->is_camara) {
            return 'CAMARA';
        }
        if ($this->partida && $this->partida->is_caja) {
            return 'CAJA';
        }
        if ($this->partida && $this->partida->is_motor) {
            return 'MOTOR';
        }

        $tipoRaw = ($this->tipo_item ?? '') . ' ' . ($this->partida->tipo ?? '');
        $tipoNormalized = strtoupper(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $tipoRaw) ?: $tipoRaw);

        if (str_contains($tipoNormalized, 'MOTOR')) {
            return 'MOTOR';
        }
        if (str_contains($tipoNormalized, 'CAJA') || str_contains($tipoNormalized, 'TRANSMIS')) {
            return 'CAJA';
        }
        if (str_contains($tipoNormalized, 'CAMARA') || str_contains($tipoNormalized, 'CULATA')) {
            return 'CAMARA';
        }

        return null;
    }

    public function defaultWarrantyMonths(?string $category = null): int
    {
        $category = $category ?? $this->resolveWarrantyCategory();

        return match ($category) {
            'CAMARA' => 1,
            'CAJA' => 2,
            default => 3,
        };
    }

    public function effectiveWarrantyMonths(?int $overrideMonths = null, ?string $category = null): int
    {
        if ($overrideMonths !== null) {
            return self::clampWarrantyMonths($overrideMonths);
        }

        if ($this->warranty_months !== null && (int) $this->warranty_months > 0) {
            return self::clampWarrantyMonths((int) $this->warranty_months);
        }

        return $this->defaultWarrantyMonths($category);
    }

    public function getIsPairCameraAttribute()
    {
        if (!empty($this->second_partida_id)) return true;
        $obs = strtoupper($this->observaciones ?? '');
        return str_contains($obs, 'PAR DE CÁMARAS') || str_contains($obs, 'PAR DE CAMARAS') || str_contains($obs, 'JUEGO L + R');
    }

    public function secondInventario()
    {
        return $this->belongsTo(Inventario::class, 'second_partida_id');
    }

    public function secondPartida()
    {
        return $this->belongsTo(Inventario::class, 'second_partida_id');
    }

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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function billingRequests()
    {
        return $this->hasMany(BillingRequest::class, 'partida_id', 'partida_id');
    }

    /**
     * Obtiene el tipo de ítem tal como se aprecia en la solicitud de facturación o despacho.
     * Si fue registrado como 7/8 pero al momento de solicitar facturarlo se envía completo,
     * devolverá MOTOR COMPLETO.
     */
    public function getTipoItemAttribute()
    {
        if (!empty($this->second_partida_id)) {
            return 'PAR DE CÁMARAS (L + R)';
        }

        $dispatchDetails = [
            'PAR DE CÁMARAS (L + R)',
            'PAR DE CAMARAS',
            'MOTOR COMPLETO',
            'MOTOR 7/8',
            'MOTOR 3/4',
            'CAJA COMPLETA',
            'CAJA SIN TURBINA'
        ];

        // 1. Prioridad: Revisar si la solicitud de facturación asociada definió el detalle
        $req = null;
        if ($this->relationLoaded('billingRequests')) {
            $req = $this->billingRequests->first();
        } elseif ($this->relationLoaded('partida') && $this->partida && $this->partida->relationLoaded('billingRequests')) {
            $req = $this->partida->billingRequests->first();
        } else {
            $req = BillingRequest::where('partida_id', $this->partida_id)
                ->where(function ($q) {
                    if (!empty($this->client_cedula)) {
                        $q->where('client_cedula', $this->client_cedula);
                    } elseif (!empty($this->client_name)) {
                        $q->where('client_name', $this->client_name);
                    }
                })
                ->latest()
                ->first();

            if (!$req) {
                $req = BillingRequest::where('partida_id', $this->partida_id)->latest()->first();
            }
        }

        if ($req && !empty($req->observation)) {
            $reqObs = strtoupper(trim($req->observation));
            foreach ($dispatchDetails as $detail) {
                if ($reqObs === $detail || str_contains($reqObs, $detail)) {
                    return $detail;
                }
            }
            if ($reqObs === 'COMPLETO' || str_contains($reqObs, 'COMPLETO')) {
                return 'MOTOR COMPLETO';
            }
            if ($reqObs === '7/8' || str_contains($reqObs, '7/8')) {
                return 'MOTOR 7/8';
            }
            if ($reqObs === '3/4' || str_contains($reqObs, '3/4')) {
                return 'MOTOR 3/4';
            }
            if (!empty($reqObs)) {
                return $reqObs;
            }
        }

        // 2. Revisar observaciones registradas directamente en la factura
        if (!empty($this->observaciones)) {
            $billObs = strtoupper(trim($this->observaciones));
            foreach ($dispatchDetails as $detail) {
                if ($billObs === $detail || str_contains($billObs, $detail)) {
                    return $detail;
                }
            }
            if ($billObs === 'COMPLETO' || str_contains($billObs, 'COMPLETO')) {
                return 'MOTOR COMPLETO';
            }
            if ($billObs === '7/8' || str_contains($billObs, '7/8')) {
                return 'MOTOR 7/8';
            }
            if ($billObs === '3/4' || str_contains($billObs, '3/4')) {
                return 'MOTOR 3/4';
            }
            if (!empty($billObs)) {
                return $billObs;
            }
        }

        // 3. Fallback al tipo registrado originalmente en la partida/inventario
        return $this->partida->tipo ?? 'MOTOR';
    }
}