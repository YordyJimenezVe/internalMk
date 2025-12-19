<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Container;
use App\Models\Billing;

class Partida extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'tipo',
        'marca',
        'modelo',
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
    ];
    
    public function container()
    {
        return $this->belongsTo(Container::class, 'container_id');
    }

    public function bill()
    {
        return $this->hasMany(Billing::class, 'partida_id'); // Assuming 'partida_id' is the foreign key
    }
}
