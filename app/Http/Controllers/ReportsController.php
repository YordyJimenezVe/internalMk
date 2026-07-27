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

/**
 * Controlador para la exportación de Reportes y generación masiva de etiquetas.
 * 
 * Permite filtrar y exportar a formato Excel y PDF información relevante de inventario,
 * facturación, historial y bitácora de auditoría.
 * También gestiona la generación e impresión masiva de etiquetas térmicas de código QR
 * y código de barras agrupadas por categoría de artículos.
 */
class ReportsController extends Controller
{
    /**
     * Muestra la pantalla principal del generador de reportes.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return inertia('Reports/Index');
    }

    /**
     * Stub para el formulario de creación de reportes de partidas.
     *
     * @return void
     */
    public function partidas()
    {
        // // return Excel::download(new PartidasExport, 'my-export.xlsx');
        // $export = new PartidasExport();
        // //return Excel::download($export, 'reporte-partida.pdf');
    }

    /**
     * Stub para almacenar recursos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Stub para mostrar un recurso específico.
     *
     * @param  string  $id
     * @return void
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Stub para editar un recurso específico.
     *
     * @param  string  $id
     * @return void
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Stub para actualizar un recurso en almacenamiento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return void
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Stub para eliminar un recurso de almacenamiento.
     *
     * @param  string  $id
     * @return void
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Filtra y exporta datos de inventario, facturas, bitácora, historial o mantenimiento en formato Excel.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP con filtros de fechas e inventario.
     * @param  string  $tipo  Tipo de reporte ('partidas', 'facturas', 'bitacora', 'history', 'maintenance').
     * @param  string  $caso  Criterio o columna por la cual buscar o agrupar.
     * @param  string|null  $termino  Término de búsqueda textual.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel(Request $request, $tipo, $caso, $termino = null)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(120);

        $startDate = $request->query('fecha_inicio');
        $endDate = $request->query('fecha_fin');
        $status = $request->query('status');

        if ($tipo == 'partidas' || $tipo == 'Inventarios' || $tipo == 'inventario') {
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

    /**
     * Filtra y exporta datos en formato PDF (visualización inline) utilizando DomPDF o Excel-raw.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP con filtros de rango de fechas y estado.
     * @param  string  $tipo  Tipo de reporte ('partidas', 'facturas', 'bitacora', 'history', 'maintenance').
     * @param  string  $caso  Criterio de búsqueda o agrupación.
     * @param  string|null  $termino  Término de búsqueda textual.
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Request $request, $tipo, $caso, $termino = null)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(120);

        $startDate = $request->query('fecha_inicio');
        $endDate = $request->query('fecha_fin');
        $status = $request->query('status');

        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $tipo . '.pdf"'
        ];

        $export = null;

        if ($tipo == 'partidas' || $tipo == 'Inventarios' || $tipo == 'inventario') {
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
    }

    /**
     * Genera e imprime masivamente en PDF etiquetas térmicas de artículos (código de barra CODE-128 y código QR).
     * 
     * Las etiquetas contienen código de inventario compuesto (ej: CONTAINER-CODINV) y están filtradas
     * por categoría ('motores', 'cajas', o 'autopartes').
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP.
     * @param  string  $tipo  Categoría de artículo ('motores', 'cajas', 'autopartes').
     * @return \Illuminate\Http\Response
     */
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
