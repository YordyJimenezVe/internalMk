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
        'status',
    ];

    public function partida()
    {
        return $this->belongsTo(Partida::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
