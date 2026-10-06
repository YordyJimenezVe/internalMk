<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Inventario;
use App\Models\BillingRequest;
use App\Models\User;

class Billing extends Model
{
    use HasFactory;

    protected $appends = [
        'tipo_item',
    ];

    protected $fillable = [
        'fecha',
        'hora',
        'partida_id',
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
    ];

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
        $dispatchDetails = [
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