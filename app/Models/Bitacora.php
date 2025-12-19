<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Bitacora extends Model
{
    use HasFactory;
    protected $fillable = [
        'users_id',
        'action',
        'description',
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

   
}
