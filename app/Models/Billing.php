<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Inventario;
use App\Models\User;

class Billing extends Model
{
    use HasFactory;
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
        'total',
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


}
