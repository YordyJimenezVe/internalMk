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
        })->pluck('id')->toArray();

        $allCompletedRequestIds = array_unique(array_merge($processedRequestIds, $soldRequestIds));

        if (!empty($allCompletedRequestIds)) {
            // Mark the requests as processed just in case
            self::whereIn('id', $allCompletedRequestIds)
                ->where('status', 'pending')
                ->update(['status' => 'processed']);

            // Delete notifications for these requests
            foreach ($allCompletedRequestIds as $requestId) {
                \Illuminate\Support\Facades\DB::table('notifications')
                    ->where(function($query) use ($requestId) {
                        $query->where('data->billing_request_id', $requestId)
                              ->orWhere('data', 'like', '%"billing_request_id":' . $requestId . '%');
                    })
                    ->delete();
            }
        }

        // 3. Clean up notifications using sold items description (for old notifications that don't have billing_request_id)
        try {
            $soldItems = \App\Models\Inventario::where('status', 'VENDIDO')->get();
            foreach ($soldItems as $item) {
                $brand = trim($item->marca);
                $model = trim($item->modelo);
                if (!empty($brand) && !empty($model)) {
                    \Illuminate\Support\Facades\DB::table('notifications')
                        ->where('data', 'like', '%' . $brand . '%')
                        ->where('data', 'like', '%' . $model . '%')
                        ->delete();
                }
            }
        } catch (\Exception $e) {
            // Silence exceptions in background cleanup
        }
    }
}
