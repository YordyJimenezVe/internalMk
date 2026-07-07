<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * Controlador del Lector / Escáner de Códigos QR y de Barras.
 * 
 * Este controlador procesa de forma unificada el escaneo de etiquetas físicas.
 * Realiza una limpieza intensiva de los códigos escaneados y, basándose en la página de procedencia
 * y los roles asignados del usuario, redirige dinámicamente a la acción correspondiente:
 * creación/edición de facturas, bandeja de mantenimiento o ficha técnica detallada del artículo.
 */
class ScanController extends Controller
{
    /**
     * Redirige dinámicamente a la orden de mantenimiento activa de un artículo
     * o, en su defecto, al formulario de creación de una nueva orden.
     *
     * @param  int  $id  Identificador único de la partida del inventario.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function directToMaintenance($id)
    {
        $activeMaintenance = \App\Models\Maintenance::where('partida_id', $id)
            ->where('status', '!=', 'TERMINADO')
            ->first();
        
        if ($activeMaintenance) {
            return redirect()->route('editMaintenance', $activeMaintenance->id);
        }
        
        return redirect()->route('createMaintenance', ['partida' => $id]);
    }

    /**
     * Muestra la pantalla del lector de códigos de barra y QR.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Scan/Index');
    }

    /**
     * Procesa y limpia el código escaneado para buscar facturas o artículos del inventario.
     * 
     * Auto-detecta si el usuario está escaneando desde el contexto de Mantenimiento o Facturación
     * para refinar la prioridad de búsqueda, y realiza redirecciones profesionales de acuerdo con
     * el rol del usuario actual.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP con el código leído e indicación opcional de redirección.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function process(Request $request)
    {
        $user = auth()->user();
        $code = $request->input('code');
        $redirectTo = $request->input('redirect_to'); 
        
        // Auto-detect origin if parameter is missing (fallback for uncompiled frontend)
        $referer = $request->headers->get('referer');
        \Log::debug("Scan debug - redirectTo: $redirectTo, Referer: $referer, Code: $code");
        
        if (!$redirectTo && str_contains($referer, '/maintenance')) {
            $redirectTo = 'maintenance';
        }

        $user = $request->user();
        $roles = $user->getRoleNames(); 

        // 1. Intensive cleaning
        $cleanCode = trim($code);
        $cleanCode = str_replace("'", "-", $cleanCode);
        $cleanCode = strtoupper($cleanCode);
        
        // Remove ANY non-alphanumeric character (except dashes for compatibility)
        $cleanCode = preg_replace('/[^a-zA-Z0-9-]/', '', $cleanCode);
        
        // 2. Extract ID if it's a full URL
        if (str_contains($cleanCode, 'http')) {
            if (preg_match('/(\d+)$/', $cleanCode, $matches)) {
                $cleanCode = $matches[1];
            }
        }

        // 3. Search Priority based on origin
        if ($redirectTo === 'maintenance') {
            return $this->processInventorySearch($cleanCode, $roles, false, true);
        }

        if ($redirectTo === 'billing') {
            return $this->processBillingSearch($cleanCode, $roles);
        }

        // Default: Search Invoices first, then Inventory
        $billingResult = $this->processBillingSearch($cleanCode, $roles, true);
        if ($billingResult) return $billingResult;

        $inventoryResult = $this->processInventorySearch($cleanCode, $roles, true);
        if ($inventoryResult) return $inventoryResult;

        // Fallback: If nothing found, return with error
        return back()->with('error', "No se encontró ningún registro para el código: $cleanCode");
    }

    /**
     * Busca facturas por su número o ID y redirige a edición o PDF según los privilegios del usuario.
     *
     * @param  string  $code  Código escaneado.
     * @param  \Illuminate\Support\Collection  $roles  Roles del usuario actual.
     * @param  bool  $silent  Si es true, no retorna error en la sesión al no encontrar coincidencias.
     * @return \Illuminate\Http\RedirectResponse|null
     */
    private function processBillingSearch($code, $roles, $silent = false)
    {
        $billing = \App\Models\Billing::where('numero_factura', $code)
            ->orWhere('id', $code)
            ->first();

        if ($billing) {
            if ($roles->contains('Facturacion') || $roles->contains('Superusuario') || $roles->contains('Administrador')) {
                return redirect()->route('editBilling', $billing->id);
            }
            return redirect()->route('billing.pdf', $billing->id);
        }

        return $silent ? null : back()->with('error', "No se encontró ninguna factura con el código: $code");
    }

    /**
     * Busca un artículo en el inventario (por su código de inventario o ID) y redirige
     * a mantenimiento, facturación o vista detallada según los roles Spatie detectados.
     *
     * @param  string  $code  Código escaneado.
     * @param  \Illuminate\Support\Collection  $roles  Roles del usuario actual.
     * @param  bool  $silent  Si es true, no retorna error en la sesión al no encontrar coincidencias.
     * @param  bool  $forceMaintenance  Fuerza la redirección hacia el flujo de mantenimiento técnico.
     * @return \Illuminate\Http\RedirectResponse|null
     */
    private function processInventorySearch($code, $roles, $silent = false, $forceMaintenance = false)
    {
        $user = auth()->user();
        \Log::debug("processInventorySearch - Started searching for code: $code, forceMaintenance: " . ($forceMaintenance ? 'true' : 'false'));
        
        $partida = \App\Models\Inventario::where('codInv', $code)
            ->orWhere('id', $code)
            ->first();

        if ($partida) {
            \Log::debug("processInventorySearch - Found by exact code/id match. ID: {$partida->id}");
        }

        // Try stripping prefix if not found (e.g. CXDU-1 -> container CXDU, codInv 1)
        if (!$partida && str_contains($code, '-')) {
            $lastDashPos = strrpos($code, '-');
            $containerPart = substr($code, 0, $lastDashPos);
            $codInvPart = substr($code, $lastDashPos + 1);
            
            \Log::debug("processInventorySearch - Stripping prefix: containerPart: $containerPart, codInvPart: $codInvPart");
            
            // First try matching container code AND inventory code
            $partida = \App\Models\Inventario::where('codInv', $codInvPart)
                ->whereHas('container', function($q) use ($containerPart) {
                    $q->where('cod', 'LIKE', $containerPart . '%');
                })->first();
                
            if ($partida) {
                \Log::debug("processInventorySearch - Found by container and codInv. ID: {$partida->id}");
            }
                
            // Fallback: just match by the inventory code
            if (!$partida) {
                $partida = \App\Models\Inventario::where('codInv', $codInvPart)->first();
                if ($partida) {
                    \Log::debug("processInventorySearch - Found by fallback codInv. ID: {$partida->id}");
                }
            }
        }

        if ($partida) {
            // Check roles professionally (Spatie)
            $isMechanic = $user->hasAnyRole(['MECANICO', 'Tecnico', 'Mecanico', 'TECNICO']);
            $isBilling = $user->hasAnyRole(['FACTURACION', 'Facturacion', 'facturacion']) && !$user->hasAnyRole(['Superusuario', 'Administrador', 'SUPERUSUARIO', 'ADMINISTRADOR']);

            \Log::debug("processInventorySearch - User ID: {$user->id}, isMechanic: " . ($isMechanic ? 'true' : 'false') . ", isBilling: " . ($isBilling ? 'true' : 'false'));

            if ($isBilling) {
                $partida = \App\Models\Inventario::with('bill')->find($partida->id);
                if ($partida->status === 'VENDIDO' || $partida->bill->count() > 0) {
                    $bill = $partida->bill->last();
                    if ($bill) {
                        \Log::debug("processInventorySearch - Redirecting to editBilling: {$bill->id}");
                        return redirect()->route('editBilling', $bill->id);
                    }
                }

                // Check for a pending billing request for this item to prefill client/price data
                $pendingRequest = \App\Models\BillingRequest::where('partida_id', $partida->id)
                    ->where('status', 'pending')
                    ->first();

                if ($pendingRequest) {
                    \Log::debug("processInventorySearch - Redirecting to createBilling with request_id: {$pendingRequest->id} for item: {$partida->id}");
                    return redirect()->route('createBilling', ['id' => $partida->id, 'request_id' => $pendingRequest->id]);
                }

                \Log::debug("processInventorySearch - Redirecting to createBilling for item: {$partida->id}");
                return redirect()->route('createBilling', $partida->id);
            }

            if ($isMechanic || $forceMaintenance) {
                \Log::debug("processInventorySearch - Redirecting to directToMaintenance for item: {$partida->id}");
                return $this->directToMaintenance($partida->id);
            }
            
            \Log::debug("processInventorySearch - Redirecting to showInventario for item: {$partida->id}");
            return redirect()->route('showInventario', $partida->id);
        }

        \Log::debug("processInventorySearch - Item NOT found for code: $code");
        return $silent ? null : back()->with('error', "No se encontró el item en el inventario: $code");
    }
}
