<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Partida;

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
    ];

    public function partidas()
    {
        return $this->belongsTo(Partida::class, 'partida_id');
    }


}
