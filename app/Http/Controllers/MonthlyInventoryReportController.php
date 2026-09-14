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
        $groupingMode = $request->input('grouping_mode', 'base');

        $reportData = $this->calculateMonthlyData($month, $year, $groupingMode);

        return inertia('Reports/MonthlyReport', [
            'initialMonth' => $month,
            'initialYear' => $year,
            'initialGroupingMode' => $groupingMode,
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
        $groupingMode = $request->input('grouping_mode', 'base');

        $reportData = $this->calculateMonthlyData($month, $year, $groupingMode);

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
        $groupingMode = $request->input('grouping_mode', 'base');

        $reportData = $this->calculateMonthlyData($month, $year, $groupingMode);

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
        $groupingMode = $request->input('grouping_mode', 'base');

        $reportData = $this->calculateMonthlyData($month, $year, $groupingMode);
        $monthName = $this->getMonthName($month);

        return Excel::download(
            new MonthlyInventoryExport($reportData),
            "Reporte_Inventario_{$monthName}_{$year}.xlsx"
        );
    }

    /**
     * Normaliza el Tipo de producto y el Modelo para agrupar por Tipo + Marca + Modelo Base.
     */
    private function normalizeModelInfo(string $tipo, string $marca, string $modelo, bool $useBaseModel = true): array
    {
        $tipoClean = mb_strtoupper(trim($tipo));
        $marcaClean = mb_strtoupper(trim($marca));
        $modeloClean = mb_strtoupper(trim($modelo));

        // Normalizar Tipo (MOTOR 7/8, MOTOR 3/4, MOTOR COMPLETO, CAJA, CÁMARA, AUTOPARTE)
        if (strpos($tipoClean, '7/8') !== false) {
            $tipoClean = 'MOTOR 7/8';
        } elseif (strpos($tipoClean, '3/4') !== false) {
            $tipoClean = 'MOTOR 3/4';
        } elseif (strpos($tipoClean, 'COMPLETO') !== false || strpos($tipoClean, '4/4') !== false) {
            $tipoClean = 'MOTOR COMPLETO';
        } elseif (strpos($tipoClean, 'CAJA') !== false) {
            $tipoClean = 'CAJA';
        } elseif (strpos($tipoClean, 'CÁMARA') !== false || strpos($tipoClean, 'CAMARA') !== false) {
            $tipoClean = 'CÁMARA';
        }

        if ($useBaseModel && !empty($modeloClean)) {
            if (preg_match('/\b(\d+\.\d+)\s*L?\b/i', $modeloClean, $matches)) {
                $displacement = $matches[1] . 'L';

                // Limpiar etiquetas secundarias de variantes (L83, L86, IV GEN, NEW GEN, TA, TP, V8, LS4, etc.)
                $baseName = preg_replace('/\b(\d+\.\d+)\s*L?\b/i', '', $modeloClean);
                $baseName = preg_replace('/\b(L83|L86|IV GEN|NEW GEN|OLD GEN|GEN 4|GEN 5|GEN III|III GEN|TA|TP|V8|LS4|V6|LS)\b/i', '', $baseName);
                $baseName = trim(preg_replace('/\s+/', ' ', $baseName));

                if (!empty($baseName)) {
                    $modeloClean = $baseName . ' ' . $displacement;
                } else {
                    $modeloClean = $displacement;
                }
            } else {
                $modeloClean = preg_replace('/\b(L83|L86|IV GEN|NEW GEN|OLD GEN|GEN 4|GEN 5|GEN III|III GEN|TA|TP|V8|LS4|V6|LS)\b/i', '', $modeloClean);
                $modeloClean = trim(preg_replace('/\s+/', ' ', $modeloClean));
            }
        }

        $codeParts = array_filter([$marcaClean, $modeloClean]);
        $code = !empty($codeParts) ? implode(' ', $codeParts) : ($tipoClean ?: 'PRODUCTO');

        $descParts = array_filter([$tipoClean, $marcaClean, $modeloClean]);
        $description = !empty($descParts) ? implode(' ', $descParts) : 'PRODUCTO GENERAL';

        return [
            'code' => $code,
            'description' => $description,
        ];
    }

    /**
     * Calcula los saldos de inventario del mes seleccionado convirtiendo la base en USD a Bolívares (Bs.) usando la Tasa BCV del día/período.
     */
    private function calculateMonthlyData(int $month, int $year, string $groupingMode = 'base'): array
    {
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // Tasa de Cambio BCV del día / período
        $exchangeRateObj = ExchangeRate::where('source', 'BCV')->latest()->first();
        $exchangeRate = $exchangeRateObj ? (float) $exchangeRateObj->rate : 1.0;

        // Datos de la empresa
        $companyName = Setting::where('key', 'company_name')->value('value') ?? 'INTERNAL MAIKEL CARS, C.A.';
        $companyRif = Setting::where('key', 'company_rif')->value('value') ?? 'J-50000000-0';

        // Obtener todos los inventarios con sus relaciones de contenedor, facturación y mantenimientos
        $inventarios = Inventario::with(['container', 'bill', 'maintenances'])->get();

        $groupedItems = [];
        $useBaseModel = ($groupingMode === 'base');

        foreach ($inventarios as $item) {
            $tipo = trim($item->tipo ?? '');
            $marca = trim($item->marca ?? '');
            $modelo = trim($item->modelo ?? '');

            // Normalizar Tipo + Modelo
            $modelInfo = $this->normalizeModelInfo($tipo, $marca, $modelo, $useBaseModel);
            $code = $modelInfo['code'];
            $description = $modelInfo['description'];

            // Código del Contenedor de origen
            $containerCode = $item->container ? ($item->container->cod ?: ($item->container->expediente ?? 'CONTAINER')) : 'S/C';

            // Determinar costo / valor base en USD por unidad
            // Si el valor viene en Bs., convertirlo a USD usando la tasa de registro o la tasa base de referencia
            $costUsd = 0.0;
            if ($item->costo_importacion_unitario && (float) $item->costo_importacion_unitario > 0) {
                $rawVal = (float) $item->costo_importacion_unitario;
                // Si el valor es mayor a 5000, es monto directo en Bs; dividir entre tasa para obtener base USD
                $costUsd = ($rawVal > 5000 && $exchangeRate > 0) ? ($rawVal / $exchangeRate) : $rawVal;
            } elseif ($item->costo && (float) $item->costo > 0) {
                $rawVal = (float) $item->costo;
                $costUsd = ($rawVal > 5000 && $exchangeRate > 0) ? ($rawVal / $exchangeRate) : $rawVal;
            } elseif ($item->price && (float) $item->price > 0) {
                $rawVal = (float) $item->price;
                $costUsd = ($rawVal > 5000 && $exchangeRate > 0) ? ($rawVal / $exchangeRate) : $rawVal;
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

            // Existencia Inicial (Creado antes del mes y NO vendido ni retirado antes del inicio del mes)
            $existenciaInicial = ($isCreatedBeforeMonth && !$isSoldBeforeMonth) ? 1 : 0;
            $entradas = $isCreatedInMonth ? 1 : 0;
            $salidas = $isSoldInMonth ? 1 : 0;
            $retiros = $isRetiroInMonth ? 1 : 0;
            $autoconsumos = $isAutoconsumoInMonth ? 1 : 0;

            // Existencia Final por unidad
            $existenciaFinal = $existenciaInicial + $entradas - $salidas - $retiros - $autoconsumos;
            if ($existenciaFinal < 0) {
                $existenciaFinal = 0;
            }

            // Si esta unidad específica no tuvo ningún movimiento ni stock en el mes, omitir
            if ($existenciaInicial == 0 && $entradas == 0 && $salidas == 0 && $retiros == 0 && $autoconsumos == 0 && $existenciaFinal == 0) {
                continue;
            }

            // Clave única de agrupación por Tipo + Modelo
            $groupKey = $description;

            if (!isset($groupedItems[$groupKey])) {
                $groupedItems[$groupKey] = [
                    'code' => $code,
                    'description' => $description,
                    'containers_map' => [],
                    'containers_str' => '',
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

            // Registrar desglose de contenedores
            if (!isset($groupedItems[$groupKey]['containers_map'][$containerCode])) {
                $groupedItems[$groupKey]['containers_map'][$containerCode] = 0;
            }
            $groupedItems[$groupKey]['containers_map'][$containerCode]++;

            // Valores en Bolívares (Bs.) calculados con la Tasa BCV del día
            $valInicial = $existenciaInicial * $costUsd * $exchangeRate;
            $valEntradas = $entradas * $costUsd * $exchangeRate;
            $valSalidas = $salidas * $costUsd * $exchangeRate;
            $valRetiros = $retiros * $costUsd * $exchangeRate;
            $valAutoconsumo = $autoconsumos * $costUsd * $exchangeRate;
            $valFinal = $existenciaFinal * $costUsd * $exchangeRate;

            // Acumular cantidades por tipo + modelo
            $groupedItems[$groupKey]['unidades_inicial'] += $existenciaInicial;
            $groupedItems[$groupKey]['unidades_entradas'] += $entradas;
            $groupedItems[$groupKey]['unidades_salidas'] += $salidas;
            $groupedItems[$groupKey]['unidades_retiros'] += $retiros;
            $groupedItems[$groupKey]['unidades_autoconsumo'] += $autoconsumos;
            $groupedItems[$groupKey]['unidades_final'] += $existenciaFinal;

            // Acumular valores en Bs. calculados a la Tasa del día
            $groupedItems[$groupKey]['valores_inicial'] += $valInicial;
            $groupedItems[$groupKey]['valores_entradas'] += $valEntradas;
            $groupedItems[$groupKey]['valores_salidas'] += $valSalidas;
            $groupedItems[$groupKey]['valores_retiros'] += $valRetiros;
            $groupedItems[$groupKey]['valores_autoconsumo'] += $valAutoconsumo;
            $groupedItems[$groupKey]['valores_final'] += $valFinal;
        }

        // Construir string de contenedores de origen para cada fila (ej: MK-2025-01 (7), MK-2025-02 (3))
        foreach ($groupedItems as $key => &$gItem) {
            $cList = [];
            foreach ($gItem['containers_map'] as $cCode => $cnt) {
                $cList[] = "{$cCode} ({$cnt})";
            }
            $gItem['containers_str'] = implode("\n", $cList);
            unset($gItem['containers_map']);
        }

        // Ordenar alfabéticamente por tipo + modelo
        ksort($groupedItems);

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
            'groupingMode' => $groupingMode,
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
