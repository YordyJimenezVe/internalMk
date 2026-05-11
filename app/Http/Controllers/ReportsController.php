<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventario;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PartidasExports;
use App\Exports\BillsExports;
use App\Exports\BitacoraExports;
use App\Exports\HistoryExports;

use App\Exports\MaintenanceExports;

class ReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('Reports/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function partidas()
    {

        // // return Excel::download(new PartidasExport, 'my-export.xlsx');

        // $export = new PartidasExport();

        // //return Excel::download($export, 'reporte-partida.pdf');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function exportExcel(Request $request, $tipo, $caso, $termino = null)
    {
        $startDate = $request->query('fecha_inicio');
        $endDate = $request->query('fecha_fin');
        $status = $request->query('status');

        if ($tipo == 'partidas') {
            return Excel::download(new PartidasExports($caso, $termino, $startDate, $endDate, $status), $tipo . '.xlsx');
        } else if ($tipo == 'facturas') {
            return Excel::download(new BillsExports($caso, $termino, $startDate, $endDate), $tipo . '.xlsx');
        } else if ($tipo == 'bitacora') {
            return Excel::download(new BitacoraExports($caso, $termino), $tipo . '.xlsx');
        } else if ($tipo == 'history') {
            return Excel::download(new HistoryExports($caso, $termino), $tipo . '.xlsx');
        } else if ($tipo == 'maintenance') {
            return Excel::download(new MaintenanceExports($caso, $termino, $startDate, $endDate, $status), $tipo . '.xlsx');
        }
    }
    public function exportPdf(Request $request, $tipo, $caso, $termino = null)
    {
        $startDate = $request->query('fecha_inicio');
        $endDate = $request->query('fecha_fin');
        $status = $request->query('status');

        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $tipo . '.pdf"'
        ];

        $export = null;

        if ($tipo == 'partidas') {
            $export = new PartidasExports($caso, $termino, $startDate, $endDate, $status);
            $data = $export->getCollection();
            $pdfContent = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.partidas', ['partidas' => $data])
                ->setPaper('a4', 'landscape')
                ->output();
            return response($pdfContent, 200, $headers);
        } else if ($tipo == 'facturas') {
            $export = new BillsExports($caso, $termino, $startDate, $endDate);
        } else if ($tipo == 'bitacora') {
            $export = new BitacoraExports($caso, $termino);
        } else if ($tipo == 'history') {
            $export = new HistoryExports($caso, $termino);
        } else if ($tipo == 'maintenance') {
            $export = new MaintenanceExports($caso, $termino, $startDate, $endDate, $status);
        }

        if ($export) {
            $pdf = Excel::raw($export, \Maatwebsite\Excel\Excel::DOMPDF);
            return response($pdf, 200, $headers);
        }
        public function bulkPrintLabels(Request $request, $tipo)
    {
        $query = Inventario::with('container')->whereIn('status', ['DISPONIBLE', 'DEVUELTO']);

        if ($tipo === 'motores') {
            $query->where('tipo', 'LIKE', '%MOTOR%');
        } elseif ($tipo === 'cajas') {
            $query->where('tipo', 'LIKE', '%CAJA%');
        } elseif ($tipo === 'autopartes') {
            $query->where('tipo', 'AUTOPARTE');
        }

        $items = $query->get();

        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(100),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $writer = new \BaconQrCode\Writer($renderer);
        $barcodeGenerator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $labels = $items->map(function($item) use ($writer, $barcodeGenerator) {
            $containerCode = $item->container ? substr($item->container->cod, 0, 4) : 'MK';
            $barcodeData = strtoupper($containerCode . '-' . $item->codInv);
            
            return [
                'inventario' => $item,
                'barcodeData' => $barcodeData,
                'qrCode' => base64_encode($writer->writeString($barcodeData)),
                'barcode' => base64_encode($barcodeGenerator->getBarcode($barcodeData, $barcodeGenerator::TYPE_CODE_128, 2, 40)),
            ];
        });

        $pdfContent = \Barryvdh\DomPDF\Facade\Pdf::loadView('labels.bulk', ['labels' => $labels])
            ->setPaper('a4', 'portrait')
            ->output();

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="etiquetas-' . $tipo . '.pdf"'
        ]);
    }
}
