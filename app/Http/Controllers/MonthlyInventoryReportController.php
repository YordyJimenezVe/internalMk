<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventario;
use App\Models\Billing;
use App\Models\Maintenance;
use App\Models\ExchangeRate;
use App\Models\Setting;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MonthlyInventoryExport;

class MonthlyInventoryReportController extends Controller
{
    /**
     * Muestra la vista del reporte mensual de inventario.
     */
    public function index(Request $request)
    {
        $month = (int) $request->input('month', date('n'));
        $year = (int) $request->input('year', date('Y'));

        $reportData = $this->calculateMonthlyData($month, $year);

        return inertia('Reports/MonthlyReport', [
            'initialMonth' => $month,
            'initialYear' => $year,
            'reportData' => $reportData,
        ]);
    }

    /**
     * Retorna los datos procesados en formato JSON (para peticiones AJAX).
     */
    public function data(Request $request)
    {
        $month = (int) $request->input('month', date('n'));
        $year = (int) $request->input('year', date('Y'));

        $reportData = $this->calculateMonthlyData($month, $year);

        return response()->json($reportData);
    }

    /**
     * Exporta el reporte a PDF (DomPDF en orientación Horizontal / Landscape).
     */
    public function exportPdf(Request $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(120);

        $month = (int) $request->input('month', date('n'));
        $year = (int) $request->input('year', date('Y'));

        $reportData = $this->calculateMonthlyData($month, $year);

        $pdf = Pdf::loadView('reports.monthly_inventory', $reportData)
            ->setPaper('letter', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif'
            ]);

        $monthName = $this->getMonthName($month);
        $fileName = "Reporte_Inventario_{$monthName}_{$year}.pdf";

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Exporta el reporte a Excel (.xlsx).
     */
    public function exportExcel(Request $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(120);

        $month = (int) $request->input('month', date('n'));
        $year = (int) $request->input('year', date('Y'));

        $reportData = $this->calculateMonthlyData($month, $year);
        $monthName = $this->getMonthName($month);

        return Excel::download(
            new MonthlyInventoryExport($reportData),
            "Reporte_Inventario_{$monthName}_{$year}.xlsx"
        );
    }

    /**
     * Calcula los datos del reporte agrupados por producto/ítem para el mes y año solicitados.
     */
    private function calculateMonthlyData(int $month, int $year): array
    {
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // Tasa de Cambio BCV actual o la más reciente
        $exchangeRateObj = ExchangeRate::where('source', 'BCV')->latest()->first();
        $exchangeRate = $exchangeRateObj ? (float) $exchangeRateObj->rate : 1.0;

        // Datos de la empresa
        $companyName = Setting::where('key', 'company_name')->value('value') ?? 'INTERNAL MAIKEL CARS, C.A.';
        $companyRif = Setting::where('key', 'company_rif')->value('value') ?? 'J-50000000-0';

        // Obtener todos los inventarios con sus relaciones de facturación y mantenimientos
        $inventarios = Inventario::with(['container', 'bill', 'maintenances'])->get();

        // Agrupar items por Código / Tipo / Producto o procesar individualmente
        $groupedItems = [];

        foreach ($inventarios as $item) {
            $code = $item->getFormattedCodAttribute() ?? ($item->codInv ?? "INV-{$item->id}");
            $description = trim("{$item->tipo} {$item->marca} {$item->modelo} {$item->serial}");
            if (empty($description)) {
                $description = $item->item ?? $item->categorie ?? 'PRODUCTO';
            }

            // Determinar costo / valor base en USD
            $costUsd = (float) ($item->costo ?? $item->price ?? 0);
            if ($costUsd <= 0 && $item->costo_importacion_unitario) {
                $costUsd = (float) $item->costo_importacion_unitario;
            }

            $createdAt = Carbon::parse($item->created_at);

            // Fechas de salida/venta
            $soldAt = null;
            if ($item->bill && $item->bill->count() > 0) {
                $firstBill = $item->bill->sortBy('created_at')->first();
                if ($firstBill && $firstBill->created_at) {
                    $soldAt = Carbon::parse($firstBill->created_at);
                }
            } elseif ($item->status === 'VENDIDO') {
                $soldAt = Carbon::parse($item->updated_at);
            }

            // Evaluar movimientos
            $isCreatedBeforeMonth = $createdAt->lt($startOfMonth);
            $isCreatedInMonth = $createdAt->gte($startOfMonth) && $createdAt->lte($endOfMonth);

            $isSoldBeforeMonth = $soldAt && $soldAt->lt($startOfMonth);
            $isSoldInMonth = $soldAt && $soldAt->gte($startOfMonth) && $soldAt->lte($endOfMonth);

            $isAutoconsumoInMonth = ($item->status === 'USO INTERNO' || $item->status === 'MANTENIMIENTO') 
                && Carbon::parse($item->updated_at)->gte($startOfMonth) 
                && Carbon::parse($item->updated_at)->lte($endOfMonth);

            $isRetiroInMonth = in_array($item->status, ['DEVUELTO', 'GARANTIA', 'GARANTÍA', 'INOPERATIVO-DESARMADO'])
                && Carbon::parse($item->updated_at)->gte($startOfMonth)
                && Carbon::parse($item->updated_at)->lte($endOfMonth);

            // Existencia Inicial (Estaba creado antes del mes y NO había sido vendido ni retirado antes del mes)
            $existenciaInicial = ($isCreatedBeforeMonth && !$isSoldBeforeMonth) ? 1 : 0;

            // Entradas en el mes
            $entradas = $isCreatedInMonth ? 1 : 0;

            // Salidas en el mes (Ventas)
            $salidas = $isSoldInMonth ? 1 : 0;

            // Retiros en el mes (Garantías / Desincorporaciones)
            $retiros = $isRetiroInMonth ? 1 : 0;

            // Autoconsumos en el mes (Uso interno / Taller)
            $autoconsumos = $isAutoconsumoInMonth ? 1 : 0;

            // Existencia Final = Inicial + Entradas - Salidas - Retiros - Autoconsumos
            $existenciaFinal = $existenciaInicial + $entradas - $salidas - $retiros - $autoconsumos;
            if ($existenciaFinal < 0) {
                $existenciaFinal = 0;
            }

            // Si el ítem no tuvo ningún movimiento ni existencia en este mes, omitir
            if ($existenciaInicial == 0 && $entradas == 0 && $salidas == 0 && $retiros == 0 && $autoconsumos == 0 && $existenciaFinal == 0) {
                continue;
            }

            // Clave de agrupación (Código de item / producto)
            $groupKey = $code . ' - ' . $description;

            if (!isset($groupedItems[$groupKey])) {
                $groupedItems[$groupKey] = [
                    'code' => $code,
                    'description' => $description,
                    'unidades_inicial' => 0,
                    'unidades_entradas' => 0,
                    'unidades_salidas' => 0,
                    'unidades_retiros' => 0,
                    'unidades_autoconsumo' => 0,
                    'unidades_final' => 0,
                    'valores_inicial' => 0.0,
                    'valores_entradas' => 0.0,
                    'valores_salidas' => 0.0,
                    'valores_retiros' => 0.0,
                    'valores_autoconsumo' => 0.0,
                    'valores_final' => 0.0,
                ];
            }

            $valInicial = $existenciaInicial * $costUsd * $exchangeRate;
            $valEntradas = $entradas * $costUsd * $exchangeRate;
            $valSalidas = $salidas * $costUsd * $exchangeRate;
            $valRetiros = $retiros * $costUsd * $exchangeRate;
            $valAutoconsumo = $autoconsumos * $costUsd * $exchangeRate;
            $valFinal = $existenciaFinal * $costUsd * $exchangeRate;

            $groupedItems[$groupKey]['unidades_inicial'] += $existenciaInicial;
            $groupedItems[$groupKey]['unidades_entradas'] += $entradas;
            $groupedItems[$groupKey]['unidades_salidas'] += $salidas;
            $groupedItems[$groupKey]['unidades_retiros'] += $retiros;
            $groupedItems[$groupKey]['unidades_autoconsumo'] += $autoconsumos;
            $groupedItems[$groupKey]['unidades_final'] += $existenciaFinal;

            $groupedItems[$groupKey]['valores_inicial'] += $valInicial;
            $groupedItems[$groupKey]['valores_entradas'] += $valEntradas;
            $groupedItems[$groupKey]['valores_salidas'] += $valSalidas;
            $groupedItems[$groupKey]['valores_retiros'] += $valRetiros;
            $groupedItems[$groupKey]['valores_autoconsumo'] += $valAutoconsumo;
            $groupedItems[$groupKey]['valores_final'] += $valFinal;
        }

        $itemsList = array_values($groupedItems);

        // Calcular Totales Generales
        $totales = [
            'unidades_inicial' => array_sum(array_column($itemsList, 'unidades_inicial')),
            'unidades_entradas' => array_sum(array_column($itemsList, 'unidades_entradas')),
            'unidades_salidas' => array_sum(array_column($itemsList, 'unidades_salidas')),
            'unidades_retiros' => array_sum(array_column($itemsList, 'unidades_retiros')),
            'unidades_autoconsumo' => array_sum(array_column($itemsList, 'unidades_autoconsumo')),
            'unidades_final' => array_sum(array_column($itemsList, 'unidades_final')),
            'valores_inicial' => array_sum(array_column($itemsList, 'valores_inicial')),
            'valores_entradas' => array_sum(array_column($itemsList, 'valores_entradas')),
            'valores_salidas' => array_sum(array_column($itemsList, 'valores_salidas')),
            'valores_retiros' => array_sum(array_column($itemsList, 'valores_retiros')),
            'valores_autoconsumo' => array_sum(array_column($itemsList, 'valores_autoconsumo')),
            'valores_final' => array_sum(array_column($itemsList, 'valores_final')),
        ];

        return [
            'companyName' => $companyName,
            'companyRif' => $companyRif,
            'month' => $month,
            'monthName' => strtoupper($this->getMonthName($month)),
            'year' => $year,
            'exchangeRate' => $exchangeRate,
            'items' => $itemsList,
            'totales' => $totales,
        ];
    }

    private function getMonthName(int $month): string
    {
        $months = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        return $months[$month] ?? 'Enero';
    }
}
