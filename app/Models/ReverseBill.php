<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReverseBill extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'users_id',
        'numero_factura',
        'numero_control',
        'numero_nota_credito',
        'numero_factura_afect',
    ];
}
