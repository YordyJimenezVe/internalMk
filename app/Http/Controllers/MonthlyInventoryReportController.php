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
     * Muestra la vista del reporte de inventario por rango de meses / período.
     */
    public function index(Request $request)
    {
        $startMonth = (int) $request->input('start_month', $request->input('month', date('n')));
        $endMonth = (int) $request->input('end_month', $request->input('bimonth' ? ($request->input('bimonth') * 2) : $startMonth));
        if ($endMonth < $startMonth) {
            $endMonth = $startMonth;
        }

        $year = (int) $request->input('year', date('Y'));
        $groupingMode = $request->input('grouping_mode', 'base');

        $reportData = $this->calculateReportData($startMonth, $endMonth, $year, $groupingMode);

        return inertia('Reports/MonthlyReport', [
            'initialStartMonth' => $startMonth,
            'initialEndMonth' => $endMonth,
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
        $startMonth = (int) $request->input('start_month', $request->input('month', date('n')));
        $endMonth = (int) $request->input('end_month', $startMonth);
        if ($endMonth < $startMonth) {
            $endMonth = $startMonth;
        }

        $year = (int) $request->input('year', date('Y'));
        $groupingMode = $request->input('grouping_mode', 'base');

        $reportData = $this->calculateReportData($startMonth, $endMonth, $year, $groupingMode);

        return response()->json($reportData);
    }

    /**
     * Exporta el reporte a PDF (DomPDF en orientación Horizontal / Landscape).
     */
    public function exportPdf(Request $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(120);

        $startMonth = (int) $request->input('start_month', $request->input('month', date('n')));
        $endMonth = (int) $request->input('end_month', $startMonth);
        if ($endMonth < $startMonth) {
            $endMonth = $startMonth;
        }

        $year = (int) $request->input('year', date('Y'));
        $groupingMode = $request->input('grouping_mode', 'base');

        $reportData = $this->calculateReportData($startMonth, $endMonth, $year, $groupingMode);

        $pdf = Pdf::loadView('reports.monthly_inventory', $reportData)
            ->setPaper('letter', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif'
            ]);

        $periodSlug = str_replace(' ', '_', $reportData['periodName']);
        $prefix = ($startMonth === $endMonth) ? 'Reporte_Inventario_Mensual' : 'Reporte_Inventario_Periodo';
        $fileName = "{$prefix}_{$periodSlug}_{$year}.pdf";

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

        $startMonth = (int) $request->input('start_month', $request->input('month', date('n')));
        $endMonth = (int) $request->input('end_month', $startMonth);
        if ($endMonth < $startMonth) {
            $endMonth = $startMonth;
        }

        $year = (int) $request->input('year', date('Y'));
        $groupingMode = $request->input('grouping_mode', 'base');

        $reportData = $this->calculateReportData($startMonth, $endMonth, $year, $groupingMode);
        $periodSlug = str_replace(' ', '_', $reportData['periodName']);
        $prefix = ($startMonth === $endMonth) ? 'Reporte_Inventario_Mensual' : 'Reporte_Inventario_Periodo';

        return Excel::download(
            new MonthlyInventoryExport($reportData),
            "{$prefix}_{$periodSlug}_{$year}.xlsx"
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

        $description = $tipoClean ?: 'PRODUCTO GENERAL';

        return [
            'marca' => $marcaClean ?: 'OTRAS MARCAS',
            'modelo' => $modeloClean ?: 'N/A',
            'code' => $code,
            'description' => $description,
        ];
    }

    /**
     * Determina la prioridad de ordenamiento según el tipo de motor / producto.
     */
    private function getTypePriority(string $tipo): int
    {
        $tipoClean = mb_strtoupper(trim($tipo));
        if (strpos($tipoClean, 'COMPLETO') !== false || strpos($tipoClean, '4/4') !== false) {
            return 1;
        }
        if (strpos($tipoClean, '7/8') !== false) {
            return 2;
        }
        if (strpos($tipoClean, '5/8') !== false) {
            return 3;
        }
        if (strpos($tipoClean, '3/4') !== false) {
            return 4;
        }
        if (strpos($tipoClean, 'CAJA') !== false) {
            return 5;
        }
        if (strpos($tipoClean, 'CÁMARA') !== false || strpos($tipoClean, 'CAMARA') !== false) {
            return 6;
        }
        return 7;
    }

    /**
     * Calcula los saldos de inventario para cualquier rango de meses (Desde - Hasta).
     */
    private function calculateReportData(int $startMonth, int $endMonth, int $year, string $groupingMode = 'base'): array
    {
        if ($startMonth === $endMonth) {
            $periodName = $this->getMonthName($startMonth);
            $reportTitle = 'REPORTE MENSUAL DE INVENTARIO (LIBRO DE CONTROL FISCAL)';
        } else {
            $periodName = $this->getMonthName($startMonth) . ' - ' . $this->getMonthName($endMonth);
            $reportTitle = 'REPORTE DE INVENTARIO POR PERÍODO (LIBRO DE CONTROL FISCAL)';
        }

        $startOfPeriod = Carbon::createFromDate($year, $startMonth, 1)->startOfMonth();
        $endOfPeriod = Carbon::createFromDate($year, $endMonth, 1)->endOfMonth();

        // Tasa de Cambio BCV del día / período
        $exchangeRateObj = ExchangeRate::where('source', 'BCV')->latest()->first();
        $exchangeRate = $exchangeRateObj ? (float) $exchangeRateObj->rate : 1.0;

        // Datos de la empresa
        $companyName = Setting::where('key', 'company_name')->value('value') ?? 'INTERNAL MAIKEL CARS, C.A.';
        $companyRif = Setting::where('key', 'company_rif')->value('value') ?? 'J-50000000-0';

        // Obtener todos los inventarios con sus relaciones
        $inventarios = Inventario::with(['container', 'bill', 'maintenances'])->get();

        $groupedItems = [];
        $useBaseModel = ($groupingMode === 'base');

        foreach ($inventarios as $item) {
            $tipo = trim($item->tipo ?? '');
            $marca = mb_strtoupper(trim($item->marca ?? 'OTRAS MARCAS'));
            if (empty($marca)) {
                $marca = 'OTRAS MARCAS';
            }
            $modelo = trim($item->modelo ?? '');

            // Normalizar Tipo + Modelo
            $modelInfo = $this->normalizeModelInfo($tipo, $marca, $modelo, $useBaseModel);
            $modeloClean = $modelInfo['modelo'];
            $code = $modelInfo['code'];
            $description = $modelInfo['description'];

            // Código del Contenedor de origen
            $containerCode = $item->container ? ($item->container->cod ?: ($item->container->expediente ?? 'CONTAINER')) : 'S/C';

            // Determinar costo / valor base en USD por unidad
            $costUsd = 0.0;
            if ($item->costo_importacion_unitario && (float) $item->costo_importacion_unitario > 0) {
                $rawVal = (float) $item->costo_importacion_unitario;
                $costUsd = ($rawVal > 5000 && $exchangeRate > 0) ? ($rawVal / $exchangeRate) : $rawVal;
            } elseif ($item->costo && (float) $item->costo > 0) {
                $rawVal = (float) $item->costo;
                $costUsd = ($rawVal > 5000 && $exchangeRate > 0) ? ($rawVal / $exchangeRate) : $rawVal;
            } elseif ($item->price && (float) $item->price > 0) {
                $rawVal = (float) $item->price;
                $costUsd = ($rawVal > 5000 && $exchangeRate > 0) ? ($rawVal / $exchangeRate) : $rawVal;
            }

            // Determinar la fecha real de ingreso / llegada
            $entryDate = null;
            if ($item->container) {
                if (!empty($item->container->fecha)) {
                    $entryDate = Carbon::parse($item->container->fecha);
                } else {
                    $entryDate = Carbon::parse($item->container->created_at);
                }
            } else {
                $entryDate = Carbon::parse($item->created_at);
            }
            $entryTimestamp = $entryDate ? $entryDate->timestamp : 0;

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

            // Evaluar movimientos con respecto al período (Desde - Hasta)
            $isCreatedBeforePeriod = $entryDate->lt($startOfPeriod);
            $isCreatedInPeriod = $entryDate->gte($startOfPeriod) && $entryDate->lte($endOfPeriod);

            $isSoldBeforePeriod = $soldAt && $soldAt->lt($startOfPeriod);
            $isSoldInPeriod = $soldAt && $soldAt->gte($startOfPeriod) && $soldAt->lte($endOfPeriod);

            $isAutoconsumoInPeriod = ($item->status === 'USO INTERNO' || $item->status === 'MANTENIMIENTO') 
                && Carbon::parse($item->updated_at)->gte($startOfPeriod) 
                && Carbon::parse($item->updated_at)->lte($endOfPeriod);

            $isRetiroInPeriod = in_array($item->status, ['DEVUELTO', 'GARANTIA', 'GARANTÍA', 'INOPERATIVO-DESARMADO'])
                && Carbon::parse($item->updated_at)->gte($startOfPeriod)
                && Carbon::parse($item->updated_at)->lte($endOfPeriod);

            // Existencia Inicial
            $existenciaInicial = ($isCreatedBeforePeriod && !$isSoldBeforePeriod) ? 1 : 0;
            $entradas = $isCreatedInPeriod ? 1 : 0;
            $salidas = $isSoldInPeriod ? 1 : 0;
            $retiros = $isRetiroInPeriod ? 1 : 0;
            $autoconsumos = $isAutoconsumoInPeriod ? 1 : 0;

            // Existencia Final
            $existenciaFinal = $existenciaInicial + $entradas - $salidas - $retiros - $autoconsumos;
            if ($existenciaFinal < 0) {
                $existenciaFinal = 0;
            }

            // Si esta unidad no tuvo ningún movimiento ni stock en el período, omitir
            if ($existenciaInicial == 0 && $entradas == 0 && $salidas == 0 && $retiros == 0 && $autoconsumos == 0 && $existenciaFinal == 0) {
                continue;
            }

            // Clave única de agrupación por Marca + Tipo + Modelo
            $groupKey = $marca . '||' . $description . '||' . $modeloClean;

            if (!isset($groupedItems[$groupKey])) {
                $groupedItems[$groupKey] = [
                    'marca' => $marca,
                    'modelo' => $modeloClean ?: 'N/A',
                    'code' => $code,
                    'description' => $description,
                    'type_priority' => $this->getTypePriority($tipo),
                    'newest_container_timestamp' => $entryTimestamp,
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
            } else {
                if ($entryTimestamp > $groupedItems[$groupKey]['newest_container_timestamp']) {
                    $groupedItems[$groupKey]['newest_container_timestamp'] = $entryTimestamp;
                }
            }

            // Registrar desglose de contenedores
            if (!isset($groupedItems[$groupKey]['containers_map'][$containerCode])) {
                $groupedItems[$groupKey]['containers_map'][$containerCode] = 0;
            }
            $groupedItems[$groupKey]['containers_map'][$containerCode]++;

            // Valores en Bolívares (Bs.)
            $valInicial = $existenciaInicial * $costUsd * $exchangeRate;
            $valEntradas = $entradas * $costUsd * $exchangeRate;
            $valSalidas = $salidas * $costUsd * $exchangeRate;
            $valRetiros = $retiros * $costUsd * $exchangeRate;
            $valAutoconsumo = $autoconsumos * $costUsd * $exchangeRate;
            $valFinal = $existenciaFinal * $costUsd * $exchangeRate;

            // Acumular cantidades
            $groupedItems[$groupKey]['unidades_inicial'] += $existenciaInicial;
            $groupedItems[$groupKey]['unidades_entradas'] += $entradas;
            $groupedItems[$groupKey]['unidades_salidas'] += $salidas;
            $groupedItems[$groupKey]['unidades_retiros'] += $retiros;
            $groupedItems[$groupKey]['unidades_autoconsumo'] += $autoconsumos;
            $groupedItems[$groupKey]['unidades_final'] += $existenciaFinal;

            // Acumular valores
            $groupedItems[$groupKey]['valores_inicial'] += $valInicial;
            $groupedItems[$groupKey]['valores_entradas'] += $valEntradas;
            $groupedItems[$groupKey]['valores_salidas'] += $valSalidas;
            $groupedItems[$groupKey]['valores_retiros'] += $valRetiros;
            $groupedItems[$groupKey]['valores_autoconsumo'] += $valAutoconsumo;
            $groupedItems[$groupKey]['valores_final'] += $valFinal;
        }

        // Construir string de contenedores
        foreach ($groupedItems as $key => &$gItem) {
            $cList = [];
            foreach ($gItem['containers_map'] as $cCode => $cnt) {
                $cList[] = "{$cCode} ({$cnt})";
            }
            $gItem['containers_str'] = implode("\n", $cList);
            unset($gItem['containers_map']);
        }

        // Agrupar por Marca
        $byBrand = [];
        foreach ($groupedItems as $gItem) {
            $b = $gItem['marca'] ?: 'OTRAS MARCAS';
            if (!isset($byBrand[$b])) {
                $byBrand[$b] = [];
            }
            $byBrand[$b][] = $gItem;
        }

        // Ordenar los ítems de cada marca
        foreach ($byBrand as $bName => &$bItems) {
            usort($bItems, function ($a, $b) {
                if ($a['type_priority'] !== $b['type_priority']) {
                    return $a['type_priority'] <=> $b['type_priority'];
                }
                $modelCmp = strnatcasecmp($a['modelo'] ?? '', $b['modelo'] ?? '');
                if ($modelCmp !== 0) {
                    return $modelCmp;
                }
                return strnatcasecmp($a['description'] ?? '', $b['description'] ?? '');
            });
        }

        // Ordenar Marcas
        $popularBrandsOrder = ['CHEVROLET', 'FORD', 'TOYOTA', 'JEEP', 'HYUNDAI', 'NISSAN', 'MITSUBISHI', 'DODGE', 'RAM', 'CHRYSLER', 'HONDA', 'MAZDA', 'ISUZU', 'CHERY', 'VOLKSWAGEN', 'CUMMINS', 'MACK', 'INTERNATIONAL', 'DAEWOO'];

        uksort($byBrand, function ($a, $b) use ($popularBrandsOrder) {
            if ($a === 'OTRAS MARCAS') return 1;
            if ($b === 'OTRAS MARCAS') return -1;

            $posA = array_search($a, $popularBrandsOrder);
            $posB = array_search($b, $popularBrandsOrder);

            if ($posA !== false && $posB !== false) {
                return $posA <=> $posB;
            }
            if ($posA !== false) return -1;
            if ($posB !== false) return 1;

            return strcmp($a, $b);
        });

        // Estructurar array final por marcas
        $brandsData = [];
        $flatItemsList = [];

        foreach ($byBrand as $brandName => $bItems) {
            $brandTotales = [
                'unidades_inicial' => array_sum(array_column($bItems, 'unidades_inicial')),
                'unidades_entradas' => array_sum(array_column($bItems, 'unidades_entradas')),
                'unidades_salidas' => array_sum(array_column($bItems, 'unidades_salidas')),
                'unidades_retiros' => array_sum(array_column($bItems, 'unidades_retiros')),
                'unidades_autoconsumo' => array_sum(array_column($bItems, 'unidades_autoconsumo')),
                'unidades_final' => array_sum(array_column($bItems, 'unidades_final')),
                'valores_inicial' => array_sum(array_column($bItems, 'valores_inicial')),
                'valores_entradas' => array_sum(array_column($bItems, 'valores_entradas')),
                'valores_salidas' => array_sum(array_column($bItems, 'valores_salidas')),
                'valores_retiros' => array_sum(array_column($bItems, 'valores_retiros')),
                'valores_autoconsumo' => array_sum(array_column($bItems, 'valores_autoconsumo')),
                'valores_final' => array_sum(array_column($bItems, 'valores_final')),
            ];

            $brandsData[] = [
                'brand' => $brandName,
                'items' => $bItems,
                'totales' => $brandTotales,
            ];

            foreach ($bItems as $it) {
                $flatItemsList[] = $it;
            }
        }

        // Calcular Totales Generales
        $totales = [
            'unidades_inicial' => array_sum(array_column($flatItemsList, 'unidades_inicial')),
            'unidades_entradas' => array_sum(array_column($flatItemsList, 'unidades_entradas')),
            'unidades_salidas' => array_sum(array_column($flatItemsList, 'unidades_salidas')),
            'unidades_retiros' => array_sum(array_column($flatItemsList, 'unidades_retiros')),
            'unidades_autoconsumo' => array_sum(array_column($flatItemsList, 'unidades_autoconsumo')),
            'unidades_final' => array_sum(array_column($flatItemsList, 'unidades_final')),
            'valores_inicial' => array_sum(array_column($flatItemsList, 'valores_inicial')),
            'valores_entradas' => array_sum(array_column($flatItemsList, 'valores_entradas')),
            'valores_salidas' => array_sum(array_column($flatItemsList, 'valores_salidas')),
            'valores_retiros' => array_sum(array_column($flatItemsList, 'valores_retiros')),
            'valores_autoconsumo' => array_sum(array_column($flatItemsList, 'valores_autoconsumo')),
            'valores_final' => array_sum(array_column($flatItemsList, 'valores_final')),
        ];

        return [
            'companyName' => $companyName,
            'companyRif' => $companyRif,
            'startMonth' => $startMonth,
            'endMonth' => $endMonth,
            'monthName' => strtoupper($periodName),
            'periodName' => strtoupper($periodName),
            'reportTitle' => $reportTitle,
            'year' => $year,
            'groupingMode' => $groupingMode,
            'exchangeRate' => $exchangeRate,
            'brands' => $brandsData,
            'items' => $flatItemsList,
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
