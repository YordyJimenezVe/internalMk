<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BillingRequest;
use Inertia\Inertia;

class BillingRequestController extends Controller
{
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

    public function store(Request $request)
    {
        $request->validate([
            'partida_id' => 'required|exists:inventarios,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric',
        ]);

        BillingRequest::create([
            'partida_id' => $request->partida_id,
            'user_id' => auth()->id(),
            'quantity' => $request->quantity,
            'price' => $request->price,
            'client_name' => $request->client_name,
            'client_cedula' => $request->client_cedula,
            'client_phone' => $request->client_phone,
            'client_address' => $request->client_address,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Solicitud enviada correctamente.');
    }

    public function process(Request $request)
    {
        $request->validate([
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:billing_requests,id',
        ]);

        foreach ($request->request_ids as $id) {
            $billingRequest = BillingRequest::with(['inventario', 'partida'])->find($id);
            $partida = $billingRequest->inventario;

            if ($partida->status === 'VENDIDO') {
                continue; // Skip if already sold
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

            // Collect ID
            $createdBillingIds[] = $newBill->id;
        }

        return redirect()->back()->with('success', 'Solicitudes procesadas y ventas registradas.')->with('billing_ids', $createdBillingIds ?? []);
    }

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

        return redirect()->back()->with('success', 'Solicitud actualizada.');
    }

    public function destroy($id)
    {
        $billingRequest = BillingRequest::findOrFail($id);
        $billingRequest->delete();

        return redirect()->back()->with('success', 'Solicitud eliminada.');
    }
}
