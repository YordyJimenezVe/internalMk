<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingRequest extends Model
{
    protected $fillable = [
        'partida_id',
        'user_id',
        'quantity',
        'price',
        'client_name',
        'client_cedula',
        'client_cedula_file',
        'client_phone',
        'client_address',
        'status',
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
        return $this->belongsTo(User::class);
    }
}
