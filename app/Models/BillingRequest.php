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
        'client_email',
        'status',
        'observation',
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

    public static function cleanupNotifications()
    {
        // 1. Get all billing requests that are processed
        $processedRequestIds = self::where('status', 'processed')->pluck('id')->toArray();

        // 2. Get all billing requests where underlying engine (partida) is VENDIDO
        $soldRequestIds = self::whereHas('inventario', function($q) {
            $q->where('status', 'VENDIDO');
        })->where('status', 'processed')->pluck('id')->toArray();

        $allCompletedRequestIds = array_unique(array_merge($processedRequestIds, $soldRequestIds));

        if (!empty($allCompletedRequestIds)) {
            // Delete notifications ONLY for these processed requests
            foreach ($allCompletedRequestIds as $requestId) {
                \Illuminate\Support\Facades\DB::table('notifications')
                    ->where(function($query) use ($requestId) {
                        $query->where('data->billing_request_id', $requestId)
                              ->orWhere('data', 'like', '%"billing_request_id":' . $requestId . '%');
                    })
                    ->delete();
            }
        }
    }
}
