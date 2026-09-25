<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class EnrichBitacoraRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitacora:enrich';
    protected $description = 'Enriquece los registros de bitácora antiguos con identificadores de modelos ( nombres, códigos, etc.)';

    public function handle()
    {
        $bitacoras = \App\Models\Bitacora::all();
        $this->info("Procesando {$bitacoras->count()} registros...");

        $bar = $this->output->createProgressBar($bitacoras->count());
        $bar->start();

        foreach ($bitacoras as $bitacora) {
            $action = mb_strtoupper($bitacora->action);
            $description = mb_strtoupper($bitacora->description);

            // 1. Determinar el Verbo en Español Descriptivo
            $newVerb = 'ACCIÓN EN';
            if (preg_match('/(CREATE|CREA)/i', $action))
                $newVerb = 'CREACIÓN DE';
            elseif (preg_match('/(UPDATE|ACTUALIZA)/i', $action))
                $newVerb = 'ACTUALIZACIÓN DE';
            elseif (preg_match('/(DELETE|ELIMINA)/i', $action))
                $newVerb = 'ELIMINACIÓN DE';
            elseif (preg_match('/(RESTORE|RESTAURA)/i', $action))
                $newVerb = 'RESTAURACIÓN DE';

            // 2. Determinar el Modelo en Español
            $newModel = 'REGISTRO';
            $className = '';
            if (preg_match('/(USER|USUARIO)/i', $action)) {
                $newModel = 'USUARIO';
                $className = 'User';
            } elseif (preg_match('/(MAINTENANCE|MANTENIMIENTO)/i', $action)) {
                $newModel = 'MANTENIMIENTO';
                $className = 'Maintenance';
            } elseif (preg_match('/(INVENTARIO)/i', $action)) {
                $newModel = 'INVENTARIO';
                $className = 'Inventario';
            } elseif (preg_match('/(BILLING|FACTURACIÓN|FACTURA)/i', $action)) {
                $newModel = 'FACTURACIÓN';
                $className = 'Billing';
            } elseif (preg_match('/(CONTAINER|CONTENEDOR)/i', $action)) {
                $newModel = 'CONTENEDOR';
                $className = 'Container';
            } elseif (preg_match('/(BILLINGREQUEST|SOLICITUD)/i', $action)) {
                $newModel = 'SOLICITUD DE FACTURACIÓN';
                $className = 'BillingRequest';
            } elseif (preg_match('/(MAINTENANCEBILL)/i', $action)) {
                $newModel = 'FACTURA DE MANTENIMIENTO';
                $className = 'MaintenanceBill';
            } elseif (preg_match('/(MATERIAL)/i', $action)) {
                $newModel = 'MATERIAL';
                $className = 'Material';
            }

            // 3. Extraer el identificador actual (lo que esté después de ": ")
            $currentIdentifier = null;
            if (str_contains($action, ':')) {
                $currentIdentifier = trim(explode(':', $action)[1]);
            }

            // 4. Si no hay identificador o el identificador es solo un ID, intentar buscar nombre mejor
            $idMatch = null;
            if (preg_match('/#(\d+)/', $action, $matches)) {
                $idMatch = $matches[1];
            } elseif (preg_match('/#(\d+)/', $description, $matches)) {
                $idMatch = $matches[1];
            }

            $finalIdentifier = $currentIdentifier ?: ($idMatch ? "#{$idMatch}" : "ID DESCONOCIDO");

            if ($idMatch && $className) {
                // Intentar obtener el nombre/código real si el actual es solo el ID o si no tenemos nombre
                if ($currentIdentifier === "#{$idMatch}" || $currentIdentifier === null || $currentIdentifier === $idMatch || preg_match('/^\d+$/', $currentIdentifier)) {
                    $foundName = $this->findIdentifier($className, $idMatch);
                    $finalIdentifier = ($foundName == $idMatch) ? "#{$idMatch}" : $foundName;
                }
            }

            // 5. Reconstruir la acción con el formato deseado
            $bitacora->action = mb_strtoupper("{$newVerb} {$newModel}: {$finalIdentifier}");

            // 6. Normalizar descripción a Mayúsculas
            $bitacora->description = $description;
            $bitacora->save();

            $bar->advance();
        }

        $bar->finish();
        $this->info("\n¡Bitácora histórica actualizada con éxito!");
    }

    protected function findIdentifier($modelName, $id)
    {
        $class = "App\\Models\\{$modelName}";
        if (!class_exists($class))
            return $id;

        try {
            $model = $class::find($id);
            if (!$model)
                return $id;

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

            return (string) $id;
        } catch (\Exception $e) {
            return $id;
        }
    }
}
