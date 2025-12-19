<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Partida;
use App\Models\Container;
use App\Models\Bills;

class PartidasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $searchRaw = $request->input('search', '');

        // --- Smart Redirect Logic (QR Code Fix) ---
        // Detect corrupted URL pattern from scanner (US->ES keyboard mismatch)
        // Expected: http://... -> Received: httpÑ--...
        if (str_starts_with($searchRaw, 'http')) {
            // Attempt to restore valid characters
            // 'Ñ' might be ':' 
            // '-' might be '/'
            // We just look for the ID at the end to be safe
            if (preg_match('/(\d+)$/', $searchRaw, $matches)) {
                return redirect()->route('showPartida', $matches[1]);
            }
        }

        // --- Barcode Sanitization ---
        // Fix Barcode scanner mapping: ''' (US key ' in ES layout?) -> '-'
        // The user reported "TRHU'616'128" -> "TRHU-616-128"
        $search = str_replace("'", "-", $searchRaw);

        // --- Search Query ---
        // --- Filter Logic ---
        $statusFilter = $request->input('status', 'DISPONIBLE'); // Default: Only Available

        $partidas = Partida::with('container')
            ->where('tipo', '!=', 'AUTOPARTE');

        // Apply Status Filter
        if ($statusFilter === 'DISPONIBLE') {
            $partidas->whereDoesntHave('bill')->where('status', '!=', 'VENDIDO');
        } elseif ($statusFilter === 'VENDIDO') {
            $partidas->where(function ($q) {
                $q->has('bill')->orWhere('status', 'VENDIDO');
            });
        }
        // If 'ALL', we don't filter by billing/status, just show everything.

        if ($search) {
            // Dividimos la búsqueda en palabras clave separadas por espacios
            $keywords = explode(' ', strtolower($search));

            $marcaKeyword = array_shift($keywords);

            $partidas->where(function ($query) use ($marcaKeyword, $keywords, $search) {
                // Expanded Search Logic
                $query->whereRaw('LOWER(marca) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(modelo) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(tipo) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(codInv) LIKE ?', ['%' . $search . '%'])
                    ->orWhereHas('container', function ($q) use ($search) {
                        $q->whereRaw("CONCAT(SUBSTR(cod, 1, 4), '-', codInv) LIKE CONCAT('%', ?, '%')", [
                            $search
                        ]);
                    });

                // Original complex logic for keyword matching
                $query->orWhere(function ($subQuery) use ($marcaKeyword, $keywords) {
                    $subQuery->whereRaw('LOWER(marca) LIKE ?', ['%' . $marcaKeyword . '%']);
                    if (!empty($keywords)) {
                        $subQuery->where(function ($modelQuery) use ($keywords) {
                            foreach ($keywords as $keyword) {
                                $modelQuery->orWhereRaw('LOWER(modelo) LIKE ?', ['%' . $keyword . '%']);
                            }
                        });
                    }
                });
            });

            // --- Single Match Redirect ---
            // If the search yields exactly one result, redirect to it immediately.
            // Clone query to avoid modifying the main builder
            $count = (clone $partidas)->count();
            if ($count === 1) {
                return redirect()->route('showPartida', (clone $partidas)->first()->id);
            }
        }

        $motorTypes = ['MOTOR 7/8', 'MOTOR 3/4', 'MOTOR COMPLETO', 'MOTOR 5/8'];
        $tipos = Partida::whereDoesntHave('bill')
            ->selectRaw('
            SUM(CASE WHEN tipo LIKE "%motor%" THEN 1 ELSE 0 END) AS motores,
            SUM(CASE WHEN tipo = "CAJA AUTOMÁTICA" THEN 1 ELSE 0 END) AS cajas_automaticas,
            SUM(CASE WHEN tipo = "AUTOPARTE" THEN 1 ELSE 0 END) AS autopartes,
            SUM(CASE WHEN tipo = "CÁMARA" THEN 1 ELSE 0 END) AS camaras
        ')
            ->orderBy('created_at', 'desc')
            ->get();

        $response = $partidas->paginate(15)->withQueryString();

        return inertia('Partida/Index', [
            'partidas' => $response,
            "filters" => [
                'search' => $searchRaw, // Return acceptable search value
            ],
            'tipos' => $tipos,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $containers = Container::all();
        return inertia(
            'Partida/Create',
            [
                'containers' => $containers
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $partida = new Partida();
        $partida->fill($request->all());
        $partida->save();
        if ($request['tipo'] == 'AUTOPARTE') {
            return redirect()->route('autopart');
        } else if ($request['tipo'] == 'CÁMARA') {
            return redirect()->route('camara');
        } else {
            return redirect()->route('partida');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Partida $partida, $id)
    {
        $data = Partida::with('container')->findOrFail($id);

        // QR Code
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(150),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $writer = new \BaconQrCode\Writer($renderer);
        $qrCode = $writer->writeString(route('showPartida', $data->id));

        // Barcode
        $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
        $containerCode = substr($data->container->cod, 0, 4);
        $barcodeData = strtoupper($containerCode . '-' . $data->codInv);
        // $barcodeData = $data->codInv && $data->codInv != '0' ? $data->codInv : str_pad($data->id, 8, '0', STR_PAD_LEFT);
        $barcode = $generator->getBarcode($barcodeData, $generator::TYPE_CODE_128, 1);

        return inertia('Partida/Show', [
            'partida' => $data,
            'qrCode' => (string) $qrCode,
            'barcode' => (string) $barcode,
            'barcodeData' => $barcodeData,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Partida $partida, $id)
    {
        $data = Partida::with('container')
            ->whereId($id)
            ->get()->first();
        $containers = Container::all();
        $tipos = ['MOTOR 3/4', 'MOTOR 5/8', 'MOTOR 7/8', 'MOTOR COMPLETO', 'CAJA AUTOMÁTICA', 'CÁMARA', 'AUTOPARTE'];
        return inertia('Partida/Edit', [
            'partida' => $data,
            'containers' => $containers,
            'tipos' => $tipos,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $partida = Partida::findOrFail($id);
        $partida->fill($request->all());
        $partida->save();
        if ($request['tipo'] == 'AUTOPARTE') {
            return redirect()->route('autopart');
        } else if ($request['tipo'] == 'CÁMARA') {
            return redirect()->route('camara');
        } else {
            return redirect()->route('partida');
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $id)
    {
        $partida = Partida::findOrFail($id);
        $partida->delete();
        if ($partida['tipo'] == 'AUTOPARTE') {
            return redirect()->route('autopart');
        } else if ($partida['tipo'] == 'CÁMARA') {
            return redirect()->route('camara');
        } else {
            return redirect()->route('partida');
        }
    }
}
