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
        'origen',
    ];

    public function container()
    {
        return $this->belongsTo(Container::class, 'container_id');
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
}
