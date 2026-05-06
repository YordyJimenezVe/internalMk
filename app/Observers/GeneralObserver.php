<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;

class GeneralObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        $this->logActivity($model, 'CREADO', 'Se creó el registro.');
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        $changes = $model->getChanges();
        unset($changes['updated_at']); // Ignore timestamp changes

        $details = 'Cambios: ' . json_encode($changes);
        $this->logActivity($model, 'ACTUALIZADO', $details);
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        $this->logActivity($model, 'ELIMINADO', 'Se eliminó el registro.');
    }

    /**
     * Handle the Model "restored" event.
     */
    public function restored(Model $model): void
    {
        $this->logActivity($model, 'RESTAURADO', 'Se restauró el registro.');
    }

    protected function logActivity(Model $model, string $action, string $description): void
    {
        if (Auth::check()) {
            $modelName = class_basename($model);
            $userId = Auth::id();
            $userName = Auth::user()->name;
            $modelId = $model->getKey();

            $actionVerb = '';
            if ($action === 'CREADO')
                $actionVerb = 'CREACIÓN DE';
            elseif ($action === 'ACTUALIZADO')
                $actionVerb = 'ACTUALIZACIÓN DE';
            elseif ($action === 'ELIMINADO')
                $actionVerb = 'ELIMINACIÓN DE';
            elseif ($action === 'RESTAURADO')
                $actionVerb = 'RESTAURACIÓN DE';

            $spanishModelName = $this->getSpanishModelName($modelName);

            if ($action === 'CREADO') {
                $description = "EL USUARIO {$userName} CREÓ UN NUEVO REGISTRO DE {$spanishModelName} CON ID #{$modelId}.";
            } elseif ($action === 'ACTUALIZADO') {
                $changes = $model->getChanges();
                unset($changes['updated_at']);

                if (empty($changes)) {
                    $description = "EL USUARIO {$userName} ACTUALIZÓ EL REGISTRO DE {$spanishModelName} #{$modelId} SIN CAMBIOS SIGNIFICATIVOS.";
                } else {
                    $details = [];
                    foreach ($changes as $key => $value) {
                        $oldValue = $model->getOriginal($key);
                        $details[] = "SE CAMBIÓ '{$key}' DE '{$oldValue}' A '{$value}'";
                    }
                    $description = "EL USUARIO {$userName} ACTUALIZÓ EL REGISTRO DE {$spanishModelName} #{$modelId}. " . implode(", ", $details);
                }
            } elseif ($action === 'ELIMINADO') {
                $description = "EL USUARIO {$userName} ELIMINÓ EL REGISTRO DE {$spanishModelName} #{$modelId}.";
            } elseif ($action === 'RESTAURADO') {
                $description = "EL USUARIO {$userName} RESTAURÓ EL REGISTRO DE {$spanishModelName} #{$modelId}.";
            }

            Bitacora::create([
                'users_id' => $userId,
                'action' => mb_strtoupper("{$actionVerb} {$spanishModelName}: " . $this->getModelIdentifier($model)),
                'description' => mb_strtoupper($description),
            ]);
        }
    }

    protected function getSpanishModelName(string $modelName): string
    {
        $map = [
            'User' => 'USUARIO',
            'Maintenance' => 'MANTENIMIENTO',
            'Inventario' => 'INVENTARIO',
            'Billing' => 'FACTURACIÓN',
            'Container' => 'CONTENEDOR',
            'BillingRequest' => 'SOLICITUD DE FACTURACIÓN',
            'MaintenanceBill' => 'FACTURA DE MANTENIMIENTO',
            'Material' => 'MATERIAL',
        ];

        return $map[$modelName] ?? mb_strtoupper($modelName);
    }

    protected function getModelIdentifier(Model $model): string
    {
        if (isset($model->name))
            return (string) $model->name;
        if (isset($model->item))
            return (string) $model->item;
        if (isset($model->numero_factura))
            return (string) $model->numero_factura;
        if (isset($model->codInv))
            return (string) $model->codInv;
        if (isset($model->cod))
            return (string) $model->cod;

        return "#" . (string) $model->getKey();
    }
}
