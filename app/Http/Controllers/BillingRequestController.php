<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BillingRequest;
use Inertia\Inertia;

/**
 * Controlador para la gestión de Solicitudes de Facturación.
 * 
 * Permite a los vendedores y mecánicos solicitar el proceso de facturación
 * de artículos del inventario al departamento administrativo (Facturación),
 * gestionando el flujo desde la solicitud inicial con datos del cliente y captura de cédula
 * hasta su procesamiento por lotes y posterior conversión en factura de venta.
 */
class BillingRequestController extends Controller
{
    /**
     * Muestra la bandeja de solicitudes de facturación pendientes.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $requests = BillingRequest::with(['inventario', 'partida', 'user'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return inertia('BillingRequest/Index', [
            'requests' => $requests
        ]);
    }

    /**
     * Registra una nueva solicitud de facturación para una partida del inventario.
     * 
     * Admite y almacena opcionalmente un archivo de captura de cédula del cliente.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP con datos del cliente y montos solicitados.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'partida_id' => 'required|exists:inventarios,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric',
            'client_cedula_file' => 'nullable|image|max:2048',
            'client_name' => 'nullable|string|max:255',
            'client_cedula' => 'nullable|string|max:20',
            'client_phone' => 'nullable|string|max:30',
            'client_address' => 'nullable|string|max:500',
        ]);

        $partida = \App\Models\Inventario::findOrFail($request->partida_id);
        if ($partida->status === 'INOPERATIVO-DESARMADO') {
            return redirect()->back()->withErrors(['partida_id' => 'No se puede facturar un ítem inoperativo o desarmado.']);
        }

        $cedulaFilePath = null;
        if ($request->hasFile('client_cedula_file')) {
            $cedulaFilePath = $request->file('client_cedula_file')->store('billing_captures', 'public');
        }

        $billingRequest = BillingRequest::create([
            'partida_id' => $request->partida_id,
            'user_id' => auth()->id(),
            'quantity' => $request->quantity,
            'price' => $request->price,
            'client_name' => $request->client_name ? strip_tags($request->client_name) : null,
            'client_cedula' => $request->client_cedula ? strip_tags($request->client_cedula) : null,
            'client_cedula_file' => $cedulaFilePath,
            'client_phone' => $request->client_phone ? strip_tags($request->client_phone) : null,
            'client_address' => $request->client_address ? strip_tags($request->client_address) : null,
            'status' => 'pending',
        ]);

        $partida = \App\Models\Inventario::find($request->partida_id);
        if ($partida) {
            $partida->price_sale = $request->price;
            $partida->saveQuietly();
        }

        // Notify billing and admin users
        $usersToNotify = \App\Models\User::where(function($query) {
            $query->where('rol', 'LIKE', '%fact%')
                  ->orWhere('rol', 'LIKE', '%admin%')
                  ->orWhere('rol', 'LIKE', '%super%')
                  ->orWhereHas('roles', function($q) {
                      $q->where('name', 'LIKE', '%fact%')
                        ->orWhere('name', 'LIKE', '%admin%')
                        ->orWhere('name', 'LIKE', '%super%');
                  });
        })->get();

        $notification = new \App\Notifications\SystemAlertNotification(
            'Nueva Solicitud',
            "El asesor " . auth()->user()->name . " solicitó facturar: " . ($partida ? $partida->marca . ' ' . $partida->modelo : 'Item') . " por $" . number_format($request->price, 2),
            route('billing.requests.index'),
            'fa-file-circle-exclamation',
            'amber',
            ['billing_request_id' => $billingRequest->id]
        );

        foreach ($usersToNotify as $userToNotify) {
            $userToNotify->notify($notification);
        }

        // Notify via Telegram Group
        $itemName = $partida ? "{$partida->marca} {$partida->modelo}" : 'Ítem';
        $vendedor = auth()->user()->name;
        $priceFormatted = number_format($request->price, 2);
        
        $telegramMessage = "🔔 <b>Nueva Solicitud de Facturación</b>\n\n"
            . "👤 <b>Asesor:</b> {$vendedor}\n"
            . "📦 <b>Artículo:</b> {$itemName}\n"
            . "💵 <b>Precio:</b> \${$priceFormatted}\n\n"
            . "🔗 <a href=\"" . route('billing.requests.index') . "\">Ver bandeja de facturación</a>";

        \App\Services\TelegramService::sendMessage($telegramMessage);

        return redirect()->back()->with('success', 'Solicitud enviada correctamente.');
    }

    /**
     * Procesa y factura por lotes las solicitudes de facturación seleccionadas.
     * 
     * Genera automáticamente las facturas asociadas a cada solicitud pendiente,
     * descuenta/actualiza el inventario si el stock llega a cero, y marca
     * las solicitudes como procesadas ('processed').
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP con el array de IDs de solicitudes a procesar.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function process(Request $request)
    {
        $request->validate([
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:billing_requests,id',
        ]);

        foreach ($request->request_ids as $id) {
            $billingRequest = BillingRequest::with(['inventario', 'partida'])->find($id);
            $partida = $billingRequest->inventario;

            if ($partida->status === 'VENDIDO' || $partida->status === 'INOPERATIVO-DESARMADO') {
                continue; // Skip if already sold or inoperative/disassembled
            }

            // 1. Create Billing Record (Sale Log)
            // Mapping fields from request/partida to Billing
            $newBill = \App\Models\Billing::create([
                'fecha' => now()->format('Y-m-d'),
                'hora' => now()->format('H:i:s'),
                'partida_id' => $partida->id,
                'precio_total' => $billingRequest->price, // Storing unit price as total for now, logic might differ if qty > 1
                'user_id' => auth()->id(), // Processed by current user (Accountant)
                'client_name' => $billingRequest->client_name,
                'client_cedula' => $billingRequest->client_cedula,
                'client_phone' => $billingRequest->client_phone,
                'client_address' => $billingRequest->client_address,
                'bs' => 0, // Default values if needed
                'divisa' => $billingRequest->price * $billingRequest->quantity, // Total amount
            ]);

            // 2. Update Partida Status
            // Logic: If stock reaches 0, mark as sold. 
            // However, user asked "cambia el estatus de disponible a vendido".
            // Assuming 1-to-1 relationship for engines ("Motor") or singular items.
            // For bulk items, we might check stock.
            if ($partida->stock >= $billingRequest->quantity) {
                // $partida->decrement('stock', $billingRequest->quantity); 
                // If item is unique (Engine/Box), mark as VENDIDO
                if ($partida->stock <= $billingRequest->quantity) {
                    $partida->update(['status' => 'VENDIDO']);
                }
            } else {
                // Force sold if no stock tracking?
                $partida->update(['status' => 'VENDIDO']);
            }

            // 3. Mark Request as Processed
            $billingRequest->update(['status' => 'processed']);

            // Delete notifications for ALL users since the sale is finished!
            \Illuminate\Support\Facades\DB::table('notifications')
                ->where('data->billing_request_id', $id)
                ->delete();

            // Collect ID
            $createdBillingIds[] = $newBill->id;
        }

        return redirect()->back()->with('success', 'Solicitudes procesadas y ventas registradas.')->with('billing_ids', $createdBillingIds ?? []);
    }

    /**
     * Actualiza la información (cantidad, precio, datos del cliente) de una solicitud pendiente.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP con los datos modificados.
     * @param  string|int  $id  Identificador único de la solicitud a actualizar.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $billingRequest = BillingRequest::findOrFail($id);

        $request->validate([
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric',
            'client_name' => 'nullable|string',
            'client_cedula' => 'nullable|string',
        ]);

        $billingRequest->update($request->only(['quantity', 'price', 'client_name', 'client_cedula']));

        $partida = $billingRequest->inventario;
        if ($partida && $request->has('price')) {
            $partida->price_sale = $request->price;
            $partida->saveQuietly();
        }

        return redirect()->back()->with('success', 'Solicitud actualizada.');
    }

    /**
     * Elimina una solicitud de facturación específica.
     *
     * @param  string|int  $id  Identificador único de la solicitud a eliminar.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $billingRequest = BillingRequest::findOrFail($id);

        // Delete notifications for ALL users since the request is deleted/rejected!
        \Illuminate\Support\Facades\DB::table('notifications')
            ->where('data->billing_request_id', $id)
            ->delete();

        $billingRequest->delete();

        return redirect()->back()->with('success', 'Solicitud eliminada.');
    }
}
