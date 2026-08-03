<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Maintenance;
use App\Models\Inventario;
use App\Models\MaintenanceBill;
use App\Models\Employee;
use Inertia\Inertia;
use App\Models\AccesorioEngine;
use App\Models\Material;
use App\Models\MaintenanceItem;
use App\Models\Bitacora;
use App\Models\NotificationSetting;
use App\Notifications\SystemAlertNotification;
use App\Models\User;
use App\Models\MaintenanceStatusLog;
use App\Helpers\ImageHelper;

/**
 * Controlador para la gestión de Mantenimientos y Órdenes de Servicio.
 * 
 * Este controlador administra todo el ciclo de diagnóstico, reparación y control
 * de calidad de los artículos en taller (principalmente motores y transmisiones).
 * Gestiona el registro de mecánicos, prorrateo y liquidación de comisiones,
 * control de costos por accesorios y materiales, y generación de informes de servicio en PDF.
 */
class MaintenancesController extends Controller
{
    /**
     * Muestra la bandeja de órdenes de mantenimiento activas e históricas con filtros avanzados.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP con filtros de búsqueda, estado y ordenación.
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');
        $status = $request->input('status');

        $query = Maintenance::query()
            ->select('maintenances.*')
            ->leftJoin('inventarios', 'maintenances.partida_id', '=', 'inventarios.id')
            ->when($status, function ($q, $status) {
                return $q->where('maintenances.status', $status);
            });

        if ($search) {
            $query->where(function ($inner) use ($search) {
                $inner->where('maintenances.id', 'like', "%{$search}%")
                    ->orWhere('maintenances.nombre_mecanico', 'like', "%{$search}%")
                    ->orWhere('maintenances.apellido_mecanico', 'like', "%{$search}%")
                    ->orWhere('maintenances.tipo', 'like', "%{$search}%")
                    ->orWhere('maintenances.status', 'like', "%{$search}%")
                    ->orWhere('maintenances.costo', 'like', "%{$search}%")
                    ->orWhere('inventarios.codInv', 'like', "%{$search}%")
                    ->orWhere('inventarios.marca', 'like', "%{$search}%")
                    ->orWhere('inventarios.modelo', 'like', "%{$search}%")
                    ->orWhere('inventarios.tipo', 'like', "%{$search}%");
            });
        }

        if ($sort === 'partida.codInv') {
            $query->orderBy('inventarios.codInv', $direction);
        } elseif ($sort === 'partida.tipo') {
            $query->orderBy('inventarios.tipo', $direction);
        } elseif ($sort === 'partida.marca') {
            $query->orderBy('inventarios.marca', $direction);
        } elseif ($sort === 'partida.modelo') {
            $query->orderBy('inventarios.modelo', $direction);
        } elseif ($sort === 'mecanico') {
            $query->orderBy('maintenances.nombre_mecanico', $direction)
                  ->orderBy('maintenances.apellido_mecanico', $direction);
        } else {
            $allowedSorts = ['id', 'partida_id', 'tipo', 'costo', 'status', 'created_at'];
            $sortBy = in_array($sort, $allowedSorts) ? 'maintenances.' . $sort : 'maintenances.id';
            $query->orderBy($sortBy, $direction);
        }

        $maintenances = $query->with('partida')
            ->paginate(10)
            ->withQueryString();

        return inertia('Maintenance/Index', [
            'maintenances' => $maintenances,
            'filters' => $request->only(['search', 'sort', 'direction', 'status']),
        ]);
    }

    /**
     * Muestra el historial de mantenimientos terminados con filtros avanzados de búsqueda y ordenación.
     */
    public function history(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');

        $query = Maintenance::query()
            ->select('maintenances.*')
            ->leftJoin('inventarios', 'maintenances.partida_id', '=', 'inventarios.id')
            ->where('maintenances.status', 'TERMINADO');

        if ($search) {
            $query->where(function ($inner) use ($search) {
                $inner->where('maintenances.id', 'like', "%{$search}%")
                    ->orWhere('maintenances.nombre_mecanico', 'like', "%{$search}%")
                    ->orWhere('maintenances.apellido_mecanico', 'like', "%{$search}%")
                    ->orWhere('maintenances.tipo', 'like', "%{$search}%")
                    ->orWhere('maintenances.status', 'like', "%{$search}%")
                    ->orWhere('maintenances.costo', 'like', "%{$search}%")
                    ->orWhere('inventarios.codInv', 'like', "%{$search}%")
                    ->orWhere('inventarios.marca', 'like', "%{$search}%")
                    ->orWhere('inventarios.modelo', 'like', "%{$search}%")
                    ->orWhere('inventarios.tipo', 'like', "%{$search}%");
            });
        }

        if ($sort === 'partida.codInv') {
            $query->orderBy('inventarios.codInv', $direction);
        } elseif ($sort === 'partida.tipo') {
            $query->orderBy('inventarios.tipo', $direction);
        } elseif ($sort === 'partida.marca') {
            $query->orderBy('inventarios.marca', $direction);
        } elseif ($sort === 'partida.modelo') {
            $query->orderBy('inventarios.modelo', $direction);
        } elseif ($sort === 'mecanico') {
            $query->orderBy('maintenances.nombre_mecanico', $direction)
                  ->orderBy('maintenances.apellido_mecanico', $direction);
        } else {
            $allowedSorts = ['id', 'partida_id', 'tipo', 'costo', 'status', 'created_at'];
            $sortBy = in_array($sort, $allowedSorts) ? 'maintenances.' . $sort : 'maintenances.id';
            $query->orderBy($sortBy, $direction);
        }

        $maintenances = $query->with('partida')
            ->paginate(10)
            ->withQueryString();

        return inertia('Maintenance/History', [
            'maintenances' => $maintenances,
            'filters' => $request->only(['search', 'sort', 'direction']),
        ]);
    }
    /**
     * Muestra el formulario para crear una nueva orden de mantenimiento.
     * 
     * Incorpora redirecciones especiales si el usuario posee rol de facturación.
     * Filtra los artículos de inventario disponibles o devueltos que no se encuentren
     * en un proceso de mantenimiento activo.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP con el parámetro opcional de artículo.
     * @return \Inertia\Response|\Illuminate\Http\RedirectResponse
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        if ($user->hasAnyRole(['FACTURACION', 'Facturacion', 'facturacion']) && !$user->hasAnyRole(['Superusuario', 'Administrador', 'SUPERUSUARIO', 'ADMINISTRADOR'])) {
            $partidaId = $request->input('partida');
            if ($partidaId) {
                return redirect()->route('createBilling', $partidaId);
            }
            return redirect()->route('billing');
        }
        // Mostrar items DISPONIBLES, DEVUELTOS o en GARANTIA/GARANTÍA que NO tengan un mantenimiento activo
        $datas = Inventario::whereIn('status', ['DISPONIBLE', 'DEVUELTO', 'GARANTIA', 'GARANTÍA'])
            ->whereDoesntHave('maintenances', function ($query) {
                $query->where('status', '!=', 'TERMINADO');
            })->get();

        $partidaId = $request->input('partida');
        $selectedItem = $partidaId ? Inventario::find($partidaId) : $datas->first();

        // Si hay un item seleccionado externamente (ej: por escáner), nos aseguramos de que esté en la lista
        if ($selectedItem && !$datas->contains('id', $selectedItem->id)) {
            $datas->push($selectedItem);
        }

        return inertia('Maintenance/Create', [
            'datas' => $datas,
            'partidas' => $selectedItem,
        ]);
    }

    /**
     * Almacena una nueva orden de mantenimiento en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP con los datos de mantenimiento.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $maintenance = new Maintenance();
        $maintenance->fill($request->all());
        $maintenance->save();

        // Auto-transition inventory status if maintenance is finished or cancelled
        if ($maintenance->status === 'TERMINADO') {
            $inventario = $maintenance->partida;
            if ($inventario && ($inventario->status === 'DEVUELTO' || $inventario->status === 'GARANTIA' || $inventario->status === 'GARANTÍA')) {
                $newInvStatus = ($inventario->status === 'GARANTIA' || $inventario->status === 'GARANTÍA') ? 'VENDIDO' : 'DISPONIBLE';
                $inventario->update(['status' => $newInvStatus]);
            }
        } elseif ($maintenance->status === 'CANCELADO') {
            $inventario = $maintenance->partida;
            if ($inventario && ($inventario->status === 'GARANTIA' || $inventario->status === 'GARANTÍA')) {
                $inventario->update(['status' => 'DEVUELTO']);
                $billing = \App\Models\Billing::where('partida_id', $inventario->id)->where('status', '!=', 'ANULADA')->first();
                if ($billing) {
                    $billing->update(['status' => 'ANULADA']);
                }
            }
        }

        $photoPath = null;
        if ($request->hasFile('status_photo')) {
            $photoPath = \App\Helpers\ImageHelper::compressAndStore($request->file('status_photo'), 'maintenance_logs');
        }

        \App\Models\MaintenanceStatusLog::create([
            'maintenance_id' => $maintenance->id,
            'status' => $maintenance->status ?? 'EN ESPERA',
            'photo_path' => $photoPath,
        ]);

        // Notify via Telegram Group
        $partidaModel = $maintenance->partida;
        $itemName = $partidaModel ? "{$partidaModel->marca} {$partidaModel->modelo}" : 'Ítem';
        $mecanico = trim("{$maintenance->nombre_mecanico} {$maintenance->apellido_mecanico}");
        if ($mecanico === 'POR ASIGNAR') {
            $mecanico = '⚠️ POR ASIGNAR';
        }
        
        $statusLabel = 'RECIBIDO';
        if ($maintenance->status === 'EN PROCESO') $statusLabel = 'ARMANDO';
        elseif ($maintenance->status === 'TERMINADO') $statusLabel = 'TERMINADO';
        elseif ($maintenance->status === 'CANCELADO') $statusLabel = 'CANCELADO';

        $telegramMessage = "🔧 <b>Nuevo Ingreso a Taller (Mantenimiento)</b>\n\n"
            . "📦 <b>Motor:</b> {$itemName}\n"
            . "⚙️ <b>Tipo:</b> {$maintenance->tipo}\n"
            . "👤 <b>Mecánico:</b> {$mecanico}\n"
            . "📋 <b>Estatus:</b> {$statusLabel}\n"
            . "📝 <b>Descripción:</b> " . ($maintenance->descripcion ?? 'N/A') . "\n\n"
            . "🔗 <a href=\"" . route('maintenance') . "\">Ver bandeja de taller</a>";
        \App\Services\TelegramService::sendMessage($telegramMessage);

        return redirect()->route('maintenance');
    }

    /**
     * Muestra los detalles de una orden de mantenimiento específica.
     * 
     * Carga de forma ansiosa (Eager Loading) las facturas, materiales consumidos y accesorios asociados.
     *
     * @param  string|int  $id  Identificador único de la orden de mantenimiento.
     * @return \Inertia\Response|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        $user = auth()->user();
        $maintenance = Maintenance::with(['partida', 'bills', 'materials', 'accesorios_engine', 'statusLogs' => function($q) {
            $q->orderBy('created_at', 'asc');
        }])->findOrFail($id);

        if ($user->hasAnyRole(['FACTURACION', 'Facturacion', 'facturacion']) && !$user->hasAnyRole(['Superusuario', 'Administrador', 'SUPERUSUARIO', 'ADMINISTRADOR'])) {
            return redirect()->route('createBilling', $maintenance->partida_id);
        }

        // Generate full image URLs for status logs
        $statusLogs = $maintenance->statusLogs->map(function($log) {
            $log->photo_url = $log->photo_path ? asset('storage/' . $log->photo_path) : null;
            return $log;
        });

        return inertia('Maintenance/Show', [
            'maintenance' => $maintenance,
            'partida' => $maintenance->partida,
            'bill' => $maintenance->bills->first(),
            'materials' => $maintenance->materials->first(),
            'accesorios' => $maintenance->accesorios_engine->first(),
            'statusLogs' => $statusLogs,
        ]);
    }

    /**
     * Muestra el formulario para editar una orden de mantenimiento,
     * cargando sus relaciones de facturación, materiales y accesorios asociados.
     *
     * @param  \App\Models\Maintenance  $maintenance  Instancia de mantenimiento (herencia de ruta).
     * @param  string|int  $id  Identificador único del mantenimiento.
     * @return \Inertia\Response|\Illuminate\Http\RedirectResponse
     */
    public function edit(Maintenance $maintenance, $id)
    {
        $maintenance = Maintenance::with(['bills', 'items' => function ($q) {
            $q->orderBy('id', 'desc');
        }, 'statusLogs' => function($q) {
            $q->orderBy('created_at', 'asc');
        }])->findOrFail($id);

        if ($maintenance->status === 'TERMINADO' && !auth()->user()->hasAnyRole(['Superusuario', 'SUPERUSUARIO', 'Administrador', 'ADMINISTRADOR'])) {
            return redirect()->route('maintenance.history')->with('error', 'No tienes permisos para editar un mantenimiento terminado.');
        }

        if (auth()->user()->hasAnyRole(['FACTURACION', 'Facturacion', 'facturacion']) && !auth()->user()->hasAnyRole(['Superusuario', 'Administrador', 'SUPERUSUARIO', 'ADMINISTRADOR'])) {
            return redirect()->route('createBilling', $maintenance->partida_id);
        }

        // Generate full image URLs for status logs
        $statusLogs = $maintenance->statusLogs->map(function($log) {
            $log->photo_url = $log->photo_path ? asset('storage/' . $log->photo_path) : null;
            return $log;
        });

        $partida = $maintenance->partida;
        $bill = $maintenance->bills->first() ?? (object) [];
        $materials = $maintenance->materials->first() ?? (object) [];
        $accesorios = $maintenance->accesorios_engine->first() ?? (object) [];
        $items = $maintenance->items;

        return inertia('Maintenance/Edit', [
            'maintenance' => $maintenance,
            'partida' => $partida,
            'bill' => $bill,
            'materials' => $materials,
            'accesorios' => $accesorios,
            'items' => $items,
            'statusLogs' => $statusLogs,
        ]);
    }

    /**
     * Actualiza la orden de mantenimiento en la base de datos.
     * 
     * Procesa, limpia y actualiza en cascada las comisiones/factura asociadas,
     * los materiales consumidos en la reparación y los costos de accesorios.
     * Auto-actualiza el estado del artículo en inventario cuando la orden pasa a 'TERMINADO'.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP con datos de mantenimiento, materiales y comisiones.
     * @param  int  $id  Identificador único del mantenimiento a actualizar.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $id)
    {
        $data = [];
        foreach ($request->all() as $key => $value) {
            if (is_string($value)) {
                $data[$key] = str_replace(' %', '', $value);
            } else {
                $data[$key] = $value;
            }
        }

        // Separar datos específicos
        $extraDataMaintenance = [
            'maintenances_id' => $id,
            'multi_tools' => $data['multi_tools'] ?? null,
            'mechanic' => $data['mechanic'] ?? 0,
            'mechanic_assistant' => $data['mechanic_assistant'] ?? 0,
            'seller' => $data['seller'] ?? 0,
            'seller_assistant' => $data['seller_assistant'] ?? 0,
            'cleaning' => $data['grouped_commission'] ?? 0,
            'consumables' => $data['grouped_commission'] ?? 0,
            'camera_technician' => $data['camera_technician'] ?? 0,
            'camera_technical_assistant' => $data['camera_technical_assistant'] ?? 0,
            'forklift' => $data['grouped_commission'] ?? 0,
        ];
        // Separar datos específicos para Materiales
        $extraDataMaterials = [
            'maintenances_id' => $id,
            'concha_biela' => $data['concha_biela'] ?? null,
            'concha_bancada' => $data['concha_bancada'] ?? null,
            'anillos' => $data['anillos'] ?? null,
            'empacadura_camara' => $data['empacadura_camara'] ?? null,
            'empacadura_carter' => $data['empacadura_carter'] ?? null,
            'kit_empacaduras' => $data['kit_empacaduras'] ?? null,
            'baño_quimico' => $data['baño_quimico'] ?? null,
            'goma_valvula' => $data['goma_valvula'] ?? null,
            'planos' => $data['planos'] ?? null,
            'valvulas' => $data['valvulas'] ?? null,
            'rectificadora' => $data['rectificadora'] ?? null,
            'asientos' => $data['asientos'] ?? null,
            'camisas' => $data['camisas'] ?? null,
            'levas' => $data['levas'] ?? null,
            'pistones' => $data['pistones'] ?? null
        ];

        // Separar datos específicos para Accesorios (Costos)
        $extraDataAccesorios = [
            'maintenances_id' => $id,
            'valve_cover' => $data['valve_cover'] ?? null,
            'chain_cover' => $data['chain_cover'] ?? null,
            'carter' => $data['carter'] ?? null,
            'pescador' => $data['pescador'] ?? null,
        ];
        $maintenance = Maintenance::findOrFail($id);

        if ($maintenance->status === 'TERMINADO' && !auth()->user()->hasAnyRole(['Superusuario', 'SUPERUSUARIO', 'Administrador', 'ADMINISTRADOR'])) {
            return redirect()->back()->with('error', 'No tienes permisos para modificar un mantenimiento terminado.');
        }

        // Handle status change log and photo upload/compression
        $oldStatus = $maintenance->status;
        $newStatus = $data['status'] ?? $oldStatus;

        if ($oldStatus !== $newStatus) {
            // Require descriptive image for warranty maintenance status changes
            if (strtoupper($maintenance->tipo) === 'GARANTÍA' || strtoupper($maintenance->tipo) === 'GARANTIA') {
                $request->validate([
                    'status_photo' => 'required|image|max:4096',
                ], [
                    'status_photo.required' => 'Debe adjuntar una imagen descriptiva al cambiar el estado del mantenimiento en garantía.',
                    'status_photo.image' => 'El archivo adjunto debe ser una imagen.',
                ]);
            }

            $photoPath = null;
            if ($request->hasFile('status_photo')) {
                $photoPath = \App\Helpers\ImageHelper::compressAndStore($request->file('status_photo'), 'maintenance_logs');
            }

            \App\Models\MaintenanceStatusLog::create([
                'maintenance_id' => $maintenance->id,
                'status' => $newStatus,
                'photo_path' => $photoPath,
            ]);

            // Notify via Telegram Group
            $partidaModel = $maintenance->partida;
            $itemName = $partidaModel ? "{$partidaModel->marca} {$partidaModel->modelo}" : 'Ítem';
            
            $statusLabelOld = 'RECIBIDO';
            if ($oldStatus === 'EN PROCESO') $statusLabelOld = 'ARMANDO';
            elseif ($oldStatus === 'TERMINADO') $statusLabelOld = 'TERMINADO';
            elseif ($oldStatus === 'CANCELADO') $statusLabelOld = 'CANCELADO';
            
            $statusLabelNew = 'RECIBIDO';
            if ($newStatus === 'EN PROCESO') $statusLabelNew = 'ARMANDO';
            elseif ($newStatus === 'TERMINADO') $statusLabelNew = 'TERMINADO';
            elseif ($newStatus === 'CANCELADO') $statusLabelNew = 'CANCELADO';

            $telegramMessage = "🔄 <b>Cambio de Estatus de Mantenimiento</b>\n\n"
                . "📦 <b>Motor:</b> {$itemName}\n"
                . "⚙️ <b>Tipo:</b> {$maintenance->tipo}\n"
                . "📉 <b>Estatus anterior:</b> {$statusLabelOld}\n"
                . "📈 <b>Nuevo estatus:</b> {$statusLabelNew}\n"
                . "👤 <b>Modificado por:</b> " . auth()->user()->name . "\n\n"
                . "🔗 <a href=\"" . route('maintenance.item', $maintenance->id) . "\">Ver detalle de mantenimiento</a>";
            \App\Services\TelegramService::sendMessage($telegramMessage);
        }

        // Auto-transition inventory status if maintenance is finished or cancelled
        if (isset($data['status']) && $data['status'] === 'TERMINADO') {
            $inventario = $maintenance->partida;
            if ($inventario && ($inventario->status === 'DEVUELTO' || $inventario->status === 'GARANTIA' || $inventario->status === 'GARANTÍA')) {
                $newInvStatus = ($inventario->status === 'GARANTIA' || $inventario->status === 'GARANTÍA') ? 'VENDIDO' : 'DISPONIBLE';
                $inventario->update(['status' => $newInvStatus]);
            }
        } elseif (isset($data['status']) && $data['status'] === 'CANCELADO') {
            $inventario = $maintenance->partida;
            if ($inventario && ($inventario->status === 'GARANTIA' || $inventario->status === 'GARANTÍA')) {
                $inventario->update(['status' => 'DEVUELTO']);
                $billing = \App\Models\Billing::where('partida_id', $inventario->id)->where('status', '!=', 'ANULADA')->first();
                if ($billing) {
                    $billing->update(['status' => 'ANULADA']);
                }
            }
        }

        $maintenance->fill($data);
        $maintenance->save();
        // Crear nuevo registro en tabla maintenances_bills
        $otherModel = MaintenanceBill::firstOrCreate(['maintenances_id' => $id]);
        $otherModel->fill($extraDataMaintenance);
        $otherModel->save();

        // Crear nuevo registro en tabla Materials
        $material = Material::firstOrCreate(['maintenances_id' => $id]);
        $material->fill($extraDataMaterials);
        $material->save();

        // Crear/Actualizar registro en tabla AccesorioEngines (Costos)
        $accesorios = AccesorioEngine::firstOrCreate(['maintenances_id' => $id]);
        $accesorios->fill($extraDataAccesorios);
        $accesorios->save();

        // Recalcular costo total y actualizar mantenimiento
        $totalCost = $this->calculateTotalCost($id);
        $maintenance->update(['costo' => $totalCost]);

        return redirect()->route('maintenance');
    }

    /**
     * Actualiza exclusivamente los costos de los accesorios de un mantenimiento y recalcula el total.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP con los costos de accesorios.
     * @param  int  $id  Identificador único de la orden de mantenimiento.
     * @return void|\Illuminate\Http\JsonResponse
     */
    public function updateAccesorios(Request $request, int $id)
    {
        $maintenance = Maintenance::findOrFail($id);
        if ($maintenance->status === 'TERMINADO' && !auth()->user()->hasAnyRole(['Superusuario', 'SUPERUSUARIO', 'Administrador', 'ADMINISTRADOR'])) {
            return response()->json(['error' => 'No tienes permisos para modificar un mantenimiento terminado.'], 403);
        }

        $accesorios = AccesorioEngine::firstOrCreate(['maintenances_id' => $id]);
        $accesorios->fill($request->all());
        $accesorios->save();

        // Recalcular costo total
        $totalCost = $this->calculateTotalCost($id);
        $maintenance->update(['costo' => $totalCost]);
    }

    /**
     * Calcula la sumatoria de costos de materiales y accesorios registrados para una orden.
     *
     * @param  int  $maintenanceId  Identificador único de la orden de mantenimiento.
     * @return float
     */
    private function calculateTotalCost($maintenanceId)
    {
        $material = Material::where('maintenances_id', $maintenanceId)->first();
        $accesorios = AccesorioEngine::where('maintenances_id', $maintenanceId)->first();

        $total = 0;

        if ($material) {
            $fields = [
                'concha_biela', 'concha_bancada', 'anillos', 'empacadura_camara',
                'empacadura_carter', 'kit_empacaduras', 'baño_quimico', 'goma_valvula',
                'planos', 'valvulas', 'rectificadora', 'asientos', 'camisas', 'levas', 'pistones'
            ];
            foreach ($fields as $field) {
                $value = str_replace(',', '.', $material->$field ?? '0');
                $total += floatval($value);
            }
        }

        if ($accesorios) {
            $fields = ['valve_cover', 'chain_cover', 'carter', 'pescador'];
            foreach ($fields as $field) {
                $value = str_replace(',', '.', $accesorios->$field ?? '0');
                $total += floatval($value);
            }
        }

        // Sumar costos de repuestos cargados dinámicamente
        $total += (float) MaintenanceItem::where('maintenance_id', $maintenanceId)->sum('cost');

        return $total;
    }

    /**
     * Elimina una orden de mantenimiento de la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP.
     * @param  int  $id  Identificador único de la orden a eliminar.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, int $id)
    {
        $maintenance = Maintenance::findOrFail($id);

        if ($maintenance->status === 'TERMINADO' && !auth()->user()->hasAnyRole(['Superusuario', 'SUPERUSUARIO', 'Administrador', 'ADMINISTRADOR'])) {
            return redirect()->back()->with('error', 'No tienes permisos para borrar un mantenimiento terminado.');
        }

        $maintenance->delete();
        return redirect()->route('maintenance');
    }

    /**
     * Revierte un mantenimiento por haber sido registrado o enviado por error.
     * Retorna el motor a VENDIDO y la factura a ACTIVA.
     */
    public function revertError(int $id)
    {
        $user = auth()->user();
        $isAdminOrSuperUser = stripos($user->rol, 'admin') !== false || stripos($user->rol, 'super') !== false 
            || $user->hasAnyRole(['Superusuario', 'Administrador', 'SUPERUSUARIO', 'ADMINISTRADOR']);
        
        if (!$isAdminOrSuperUser) {
            abort(403, 'No autorizado.');
        }

        $maintenance = Maintenance::findOrFail($id);
        
        $maintenance->update([
            'status' => 'CANCELADO',
            'observaciones' => ($maintenance->observaciones ? $maintenance->observaciones . "\n" : '') . 'Revertido por envío erróneo de forma administrativa.'
        ]);

        \App\Models\MaintenanceStatusLog::create([
            'maintenances_id' => $maintenance->id,
            'status' => 'CANCELADO',
            'observaciones' => 'Revertido por envío erróneo de forma administrativa.',
        ]);

        $inventario = $maintenance->partida;
        if ($inventario) {
            $inventario->update(['status' => 'VENDIDO']);
            
            $billing = \App\Models\Billing::where('partida_id', $inventario->id)->where('status', 'ANULADA')->first();
            if ($billing) {
                $billing->update(['status' => 'ACTIVA']);
            }
        }

        Bitacora::create([
            'users_id' => $user->id,
            'action' => 'REVERTIR_MANTENIMIENTO_ERROR',
            'description' => "Mantenimiento #{$maintenance->id} revertido por error administrativo.",
        ]);

        // Notify via Telegram Group
        $partidaModel = $maintenance->partida;
        $itemName = $partidaModel ? "{$partidaModel->marca} {$partidaModel->modelo}" : 'Ítem';
        $telegramMessage = "⚠️ <b>Mantenimiento Revertido por Error</b>\n\n"
            . "📦 <b>Motor:</b> {$itemName}\n"
            . "👤 <b>Revertido por:</b> " . $user->name . "\n"
            . "📝 <b>Acción:</b> El motor ha vuelto a estatus VENDIDO y la factura a ACTIVA.";
        \App\Services\TelegramService::sendMessage($telegramMessage);

        return redirect()->route('maintenance')->with('success', 'Mantenimiento revertido por error con éxito.');
    }

    /**
     * Consulta y retorna datos de inventario y empleados para poblar la vista de creación.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP.
     * @return \Inertia\Response
     */
    public function getInventario(Request $request)
    {
        $inputPartida = $request->input('partida');
        $inputEmployee = $request->input('employee');
        $partida = Inventario::find($inputPartida);
        $employee = Employee::where('cedula', $inputEmployee)->first();
        
        $datas = Inventario::whereIn('status', ['DISPONIBLE', 'DEVUELTO', 'GARANTIA', 'GARANTÍA'])
            ->whereDoesntHave('maintenances', function ($query) {
                $query->where('status', '!=', 'TERMINADO');
            })->get();

        if ($partida && !$datas->contains('id', $partida->id)) {
            $datas->push($partida);
        }

        return Inertia::render('Maintenance/Create', [
            'partidas' => $partida,
            'employee' => $employee,
            'datas' => $datas,
        ]);
    }

    /**
     * Genera e imprime el reporte PDF de la ficha de mantenimiento técnico.
     * 
     * Incorpora obtención dinámica de imágenes de vehículos de referencia basados en marca y modelo,
     * y maneja fallbacks automáticos para motores diésel de maquinaria pesada.
     *
     * @param  string|int  $id  Identificador único de la orden de mantenimiento.
     * @return \Illuminate\Http\Response
     */
    public function pdf($id)
    {
        $maintenance = Maintenance::with(['partida', 'bills', 'materials', 'accesorios_engine'])->findOrFail($id);

        // --- LOGIC FOR DYNAMIC VEHICLE IMAGE ---
        $brand = strtolower(trim($maintenance->partida->marca ?? ''));
        $rawModel = strtolower(trim($maintenance->partida->modelo ?? ''));

        // Limpiamos nombres compuestos y caracteres especiales
        $cleanModel = str_replace(['/', '-', '(', ')'], ' ', $rawModel);
        $parts = array_filter(explode(' ', $cleanModel));
        $model = reset($parts) ?: '';

        // Filtro de seguridad: Si el modelo es "Turpial" (que es un pájaro), forzamos términos de auto
        $tags = [$brand, $model, 'car'];
        if ($model == 'turpial' || $model == 'festiva') {
            $tags = ['ford', 'festiva', 'car'];
        }

        // Usamos el formato solicitado: marca-modelo
        $query = "{$brand}-{$model}";

        // --- LOGICA PARA MOTORES DIESEL (Cummins, Caterpillar, Perkins, etc) ---
        $engineBrands = ['cummins', 'perkins', 'caterpillar', 'detroit', 'deutz', 'yanmar', 'kubota', 'iveco', 'mack'];
        if (in_array($brand, $engineBrands)) {
            $query = "{$brand}-engine";
        }

        $sourceUrl = "https://loremflickr.com/400/300/{$query}";
        $fallbackUrl = "https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&q=80&w=400";

        try {
            $arrContextOptions = array(
                "ssl" => array("verify_peer" => false, "verify_peer_name" => false),
                "http" => array("timeout" => 5)
            );
            $imageData = @file_get_contents($sourceUrl, false, stream_context_create($arrContextOptions));

            if ($imageData && strlen($imageData) > 2000) {
                $base64Image = 'data:image/jpeg;base64,' . base64_encode($imageData);
            } else {
                $base64Image = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($fallbackUrl, false, stream_context_create($arrContextOptions)));
            }
        } catch (\Exception $e) {
            $base64Image = null;
        }

        $data = [
            'maintenance' => $maintenance,
            'partida' => $maintenance->partida,
            'bill' => $maintenance->bills->first(),
            'materials' => $maintenance->materials->first(),
            'accesorios' => $maintenance->accesorios_engine->first(),
            'vehicleImage' => $base64Image,
        ];

        $pdf = \PDF::loadView('reports.maintenance_pdf', $data);
        $pdf->setPaper('letter', 'portrait');

        $filename = 'Ficha-Mantenimiento-' . str_pad($maintenance->id, 5, '0', STR_PAD_LEFT) . '.pdf';
        return $pdf->stream($filename);
    }

    /**
     * Registra un repuesto/servicio dinámico en el mantenimiento.
     */
    public function storeItem(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'type' => 'required|in:REPUESTO,SERVICIO',
            'source' => 'required|in:INVENTARIO,COMPRADO',
            'cost' => 'nullable|numeric|min:0',
            'document_type' => 'required|in:FACTURA,RECIBO,NINGUNO',
            'invoice_number' => 'nullable|string|max:255',
            'base_imponible' => 'nullable|numeric|min:0|lte:cost',
            'status' => 'required|in:COMPLETADO,FUERA,RETORNADO,CONCILIADO',
            'notes' => 'nullable|string',
            'invoice_file' => 'nullable|image|max:2048',
        ], [
            'base_imponible.lte' => 'La Base Imponible (BIG) no puede ser mayor al Costo Real.',
            'base_imponible.min' => 'La Base Imponible no puede ser un valor negativo.',
            'cost.min' => 'El costo real no puede ser un valor negativo.',
        ]);

        $cost = $request->input('cost') ?? 0;
        $baseImponible = $request->input('base_imponible') ?? 0;

        if ($request->input('source') === 'INVENTARIO') {
            $baseImponible = 0;
        }

        $invoicePath = null;
        if ($request->hasFile('invoice_file')) {
            $invoicePath = $request->file('invoice_file')->store('maintenance_captures', 'public');
        }

        $item = new MaintenanceItem();
        $item->maintenance_id = $id;
        $item->description = mb_strtoupper($request->input('description'));
        $item->type = $request->input('type');
        $item->source = $request->input('source');
        $item->cost = $cost;
        $item->document_type = $request->input('document_type');
        $item->invoice_number = mb_strtoupper($request->input('invoice_number'));
        $item->base_imponible = $baseImponible;
        $item->status = $request->input('status');
        $item->notes = $request->input('notes');
        $item->invoice_path = $invoicePath;

        if ($item->status === 'FUERA') {
            $item->outflow_date = now();
        } else if ($item->status === 'RETORNADO' || $item->status === 'CONCILIADO' || $item->status === 'COMPLETADO') {
            $item->return_date = now();
        }

        $item->save();

        // Recalcular costo de mantenimiento
        $totalCost = $this->calculateTotalCost($id);
        Maintenance::findOrFail($id)->update(['costo' => $totalCost]);

        return redirect()->back()->with('success', 'Ítem de mantenimiento registrado con éxito.');
    }

    /**
     * Elimina un repuesto/servicio dinámico del mantenimiento.
     */
    public function deleteItem($itemId)
    {
        $item = MaintenanceItem::findOrFail($itemId);
        $maintenanceId = $item->maintenance_id;
        $item->delete();

        // Recalcular costo de mantenimiento
        $totalCost = $this->calculateTotalCost($maintenanceId);
        Maintenance::findOrFail($maintenanceId)->update(['costo' => $totalCost]);

        return redirect()->back()->with('success', 'Ítem eliminado con éxito.');
    }

    /**
     * Registra la salida de una pieza a la rectificadora.
     */
    public function registerOutflow($itemId)
    {
        $item = MaintenanceItem::findOrFail($itemId);
        $item->update([
            'status' => 'FUERA',
            'outflow_date' => now(),
        ]);

        if (NotificationSetting::isNotificationEnabled('notify_outflow')) {
            $admins = User::where('rol', 'Administrador')->get();
            $notification = new SystemAlertNotification(
                'Pieza a Rectificadora',
                "La pieza '{$item->description}' salió a la rectificadora (Orden #{$item->maintenance_id}).",
                route('maintenance.edit', $item->maintenance_id),
                'fa-truck-arrow-right',
                'amber'
            );
            foreach ($admins as $admin) {
                $admin->notify($notification);
            }
        }

        return redirect()->back()->with('success', 'Salida a rectificadora registrada.');
    }

    /**
     * Registra el retorno de una pieza desde la rectificadora.
     */
    public function registerReturn(Request $request, $itemId)
    {
        $request->validate([
            'cost' => 'required|numeric|min:0',
            'document_type' => 'required|in:FACTURA,RECIBO',
            'invoice_number' => 'nullable|string|max:255',
            'base_imponible' => 'nullable|numeric|min:0|lte:cost',
            'notes' => 'nullable|string',
            'invoice_file' => 'nullable|image|max:2048',
        ], [
            'base_imponible.lte' => 'La Base Imponible (BIG) no puede ser mayor al Costo Real.',
            'base_imponible.min' => 'La Base Imponible no puede ser un valor negativo.',
            'cost.min' => 'El costo real no puede ser un valor negativo.',
        ]);

        $item = MaintenanceItem::findOrFail($itemId);
        
        $status = 'COMPLETADO';
        if ($request->input('document_type') === 'FACTURA') {
            $status = 'RETORNADO'; // PENDIENTE por conciliar
        }

        $invoicePath = $item->invoice_path;
        if ($request->hasFile('invoice_file')) {
            $invoicePath = $request->file('invoice_file')->store('maintenance_captures', 'public');
        }

        $item->update([
            'status' => $status,
            'cost' => $request->input('cost'),
            'document_type' => $request->input('document_type'),
            'invoice_number' => mb_strtoupper($request->input('invoice_number')),
            'base_imponible' => $request->input('base_imponible'),
            'return_date' => now(),
            'notes' => $request->input('notes'),
            'invoice_path' => $invoicePath,
        ]);

        // Recalcular costo de mantenimiento
        $totalCost = $this->calculateTotalCost($item->maintenance_id);
        Maintenance::findOrFail($item->maintenance_id)->update(['costo' => $totalCost]);

        // Trigger notifications
        if (NotificationSetting::isNotificationEnabled('notify_return')) {
            $admins = User::where('rol', 'Administrador')->get();
            $notification = new SystemAlertNotification(
                'Entrada de Rectificadora',
                "La pieza '{$item->description}' retornó de la rectificadora (Orden #{$item->maintenance_id}).",
                route('maintenance.edit', $item->maintenance_id),
                'fa-right-to-bracket',
                'emerald'
            );
            foreach ($admins as $admin) {
                $admin->notify($notification);
            }
        }

        if ($status === 'RETORNADO' && NotificationSetting::isNotificationEnabled('notify_pending_conciliation')) {
            $billingUsers = User::whereIn('rol', ['Administrador', 'Administrador Consulta', 'Facturación'])->get();
            $notification = new SystemAlertNotification(
                'Conciliación Pendiente',
                "Se cargó la factura Nro. '{$item->invoice_number}' para la pieza '{$item->description}' (Orden #{$item->maintenance_id}). Pendiente de conciliación.",
                route('maintenance.conciliacion'),
                'fa-calculator',
                'indigo'
            );
            foreach ($billingUsers as $bUser) {
                $bUser->notify($notification);
            }
        }

        return redirect()->back()->with('success', 'Retorno de rectificadora registrado.');
    }

    /**
     * Bandeja de conciliación de facturas de taller.
     *
     * @return \Inertia\Response
     */
    public function conciliacionIndex()
    {
        $items = MaintenanceItem::with(['maintenance', 'maintenance.partida'])
            ->where('document_type', 'FACTURA')
            ->where('status', 'RETORNADO')
            ->orderBy('id', 'desc')
            ->get();

        $finalizedItems = MaintenanceItem::with(['maintenance', 'maintenance.partida'])
            ->where('document_type', 'FACTURA')
            ->where('status', 'CONCILIADO')
            ->orderBy('id', 'desc')
            ->limit(150)
            ->get();

        return Inertia::render('Maintenance/Conciliacion', [
            'items' => $items,
            'finalizedItems' => $finalizedItems,
        ]);
    }

    /**
     * Concilia una factura de taller.
     *
     * @param  int  $itemId  ID del ítem de mantenimiento a conciliar.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function conciliarItem($itemId)
    {
        $item = MaintenanceItem::with('maintenance')->findOrFail($itemId);
        $item->update(['status' => 'CONCILIADO']);

        $partidaId = $item->maintenance ? $item->maintenance->partida_id : 'N/A';

        // Registrar auditoría en Bitácora
        Bitacora::create([
            'users_id' => auth()->id(),
            'action' => 'CONCILIAR',
            'description' => mb_strtoupper("CONCILIADA FACTURA DE TALLER: {$item->invoice_number} (BIG: {$item->base_imponible}$) PARA MOTOR ID: {$partidaId}"),
        ]);

        return redirect()->back()->with('success', 'Factura conciliada con éxito.');
    }

    /**
     * Revierte una factura de taller conciliada a estado pendiente.
     *
     * @param  int  $itemId  ID del ítem de mantenimiento a revertir.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function revertConciliarItem($itemId)
    {
        $item = MaintenanceItem::with('maintenance')->findOrFail($itemId);
        $item->update(['status' => 'RETORNADO']);

        $partidaId = $item->maintenance ? $item->maintenance->partida_id : 'N/A';

        // Registrar auditoría en Bitácora
        Bitacora::create([
            'users_id' => auth()->id(),
            'action' => 'REVERTIR CONCILIACION',
            'description' => mb_strtoupper("REVERTIDA CONCILIACION DE FACTURA DE TALLER: {$item->invoice_number} A ESTADO PENDIENTE PARA MOTOR ID: {$partidaId}"),
        ]);

        return redirect()->back()->with('success', 'Conciliación revertida a pendiente con éxito.');
    }

    /**
     * Actualiza los datos de costo y factura de un ítem de mantenimiento antes de ser conciliado.
     */
    public function updateItem(Request $request, $itemId)
    {
        $item = MaintenanceItem::findOrFail($itemId);

        if ($item->status === 'CONCILIADO') {
            return redirect()->back()->with('error', 'No se puede editar un ítem que ya ha sido conciliado.');
        }

        $request->validate([
            'cost' => 'nullable|numeric|min:0',
            'document_type' => 'required|in:FACTURA,RECIBO,NINGUNO',
            'invoice_number' => 'nullable|string|max:255',
            'base_imponible' => 'nullable|numeric|min:0|lte:cost',
            'notes' => 'nullable|string',
            'invoice_file' => 'nullable|image|max:2048',
        ], [
            'base_imponible.lte' => 'La Base Imponible (BIG) no puede ser mayor al Costo Real.',
            'base_imponible.min' => 'La Base Imponible no puede ser un valor negativo.',
            'cost.min' => 'El costo real no puede ser un valor negativo.',
        ]);

        $cost = $request->input('cost') ?? 0;
        $baseImponible = $request->input('base_imponible') ?? 0;
        $documentType = $request->input('document_type');

        // Si el origen es inventario, forzar base imponible a 0 y tipo de documento a NINGUNO, pero permitir costo
        if ($item->source === 'INVENTARIO') {
            $baseImponible = 0;
            $documentType = 'NINGUNO';
        }

        $invoicePath = $item->invoice_path;
        if ($request->hasFile('invoice_file')) {
            $invoicePath = $request->file('invoice_file')->store('maintenance_captures', 'public');
        }

        // Si el estado actual es COMPLETADO o RETORNADO, y cambia el tipo de documento, actualizar el estado
        $status = $item->status;
        if ($status === 'COMPLETADO' || $status === 'RETORNADO') {
            if ($documentType === 'FACTURA') {
                $status = 'RETORNADO'; // PENDIENTE por conciliar
            } else {
                $status = 'COMPLETADO';
            }
        }

        $item->update([
            'cost' => $cost,
            'document_type' => $documentType,
            'invoice_number' => $request->input('invoice_number') ? mb_strtoupper($request->input('invoice_number')) : null,
            'base_imponible' => $baseImponible,
            'notes' => $request->input('notes'),
            'invoice_path' => $invoicePath,
            'status' => $status,
        ]);

        // Recalcular costo de mantenimiento
        $maintenance = $item->maintenance;
        if ($maintenance) {
            $totalCost = $maintenance->items()->sum('cost');
            $maintenance->update(['costo' => $totalCost]);
        }

        if ($status === 'RETORNADO' && NotificationSetting::isNotificationEnabled('notify_pending_conciliation')) {
            $billingUsers = User::whereIn('rol', ['Administrador', 'Administrador Consulta', 'Facturación'])->get();
            $notification = new SystemAlertNotification(
                'Conciliación Pendiente',
                "Se actualizó la factura Nro. '{$item->invoice_number}' para la pieza '{$item->description}' (Orden #{$item->maintenance_id}). Pendiente de conciliación.",
                route('maintenance.conciliacion'),
                'fa-calculator',
                'indigo'
            );
            foreach ($billingUsers as $bUser) {
                $bUser->notify($notification);
            }
        }

        return redirect()->back()->with('success', 'Detalles del ítem actualizados correctamente.');
    }
}
