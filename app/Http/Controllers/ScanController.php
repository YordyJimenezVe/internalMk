<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ScanController extends Controller
{
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

    public function index()
    {
        return Inertia::render('Scan/Index');
    }

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

    private function processInventorySearch($code, $roles, $silent = false, $forceMaintenance = false)
    {
        $user = auth()->user();
        $partida = \App\Models\Inventario::where('codInv', $code)
            ->orWhere('id', $code)
            ->first();

        // Try stripping prefix if not found
        if (!$partida && str_contains($code, '-')) {
            $parts = explode('-', $code);
            if (count($parts) >= 2) {
                $strippedCode = implode('-', array_slice($parts, -2));
                $partida = \App\Models\Inventario::where('codInv', $strippedCode)->first();
            }
        }

        if ($partida) {
            // Check roles professionally (Spatie)
            $isMechanic = $user->hasAnyRole(['MECANICO', 'Tecnico', 'Mecanico', 'TECNICO', 'Superusuario', 'Administrador']);

            if ($isMechanic || $forceMaintenance) {
                return $this->directToMaintenance($partida->id);
            }
            return redirect()->route('showInventario', $partida->id);
        }

        return $silent ? null : back()->with('error', "No se encontró el item en el inventario: $code");
    }
}
