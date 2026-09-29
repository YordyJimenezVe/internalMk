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
     * Exporta el formato manual limpio para registro y conteo de inventario (.xlsx SENIAT).
     */
    public function exportManualTemplate(Request $request)
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
        $prefix = ($startMonth === $endMonth) ? 'Formato_Manual_Inventario_Mensual' : 'Formato_Manual_Inventario_Periodo';

        return Excel::download(
            new \App\Exports\ManualInventoryTemplateExport($reportData),
            "{$prefix}_{$periodSlug}_{$year}.xlsx"
        );
    }

    /**
     * Normaliza el Tipo de producto y el Modelo para agrupar por Tipo + Marca + Modelo Base,
     * generando un CÓDIGO único (ej: MTR-CH-53L, CAJ-CH-AVEO-1.6) y una DESCRIPCIÓN clara (ej: Motor Completo Chevrolet 5.3L).
     */
    private function normalizeModelInfo(string $tipo, string $marca, string $modelo, bool $useBaseModel = true): array
    {
        $tipoClean = mb_strtoupper(trim($tipo));
        $marcaClean = mb_strtoupper(trim($marca));
        if (empty($marcaClean)) {
            $marcaClean = 'OTRAS MARCAS';
        }
        $modeloClean = trim($modelo);

        // 1. Tipo Prefix & Label
        if (strpos($tipoClean, '7/8') !== false) {
            $tipoPrefix = 'M78';
            $tipoLabel = 'Motor 7/8';
        } elseif (strpos($tipoClean, '3/4') !== false) {
            $tipoPrefix = 'M34';
            $tipoLabel = 'Motor 3/4';
        } elseif (strpos($tipoClean, '5/8') !== false) {
            $tipoPrefix = 'M58';
            $tipoLabel = 'Motor 5/8';
        } elseif (strpos($tipoClean, 'COMPLETO') !== false || strpos($tipoClean, '4/4') !== false) {
            $tipoPrefix = 'MTR';
            $tipoLabel = 'Motor Completo';
        } elseif (strpos($tipoClean, 'CAJA') !== false) {
            $tipoPrefix = 'CAJ';
            $tipoLabel = 'Caja';
        } elseif (strpos($tipoClean, 'CÁMARA') !== false || strpos($tipoClean, 'CAMARA') !== false) {
            $tipoPrefix = 'CAM';
            $tipoLabel = 'Cámara';
        } elseif (strpos($tipoClean, 'AUTOPARTE') !== false) {
            $tipoPrefix = 'AUT';
            $tipoLabel = 'Autoparte';
        } else {
            $tipoPrefix = 'MTR';
            $tipoLabel = !empty($tipoClean) ? ucwords(mb_strtolower($tipoClean)) : 'Motor';
        }

        // 2. Brand Code & Label
        $brandMap = [
            'CHEVROLET' => 'CH', 'FORD' => 'FD', 'TOYOTA' => 'TY', 'JEEP' => 'JEP',
            'DODGE' => 'DOD', 'NISSAN' => 'NIS', 'MITSUBISHI' => 'MIT', 'HYUNDAI' => 'HYU',
            'HONDA' => 'HON', 'MAZDA' => 'MAZ', 'ISUZU' => 'ISZ', 'VOLKSWAGEN' => 'VW',
            'CHRYSLER' => 'CHR', 'RAM' => 'RAM', 'CUMMINS' => 'CUM', 'MACK' => 'MCK',
            'INTERNATIONAL' => 'INT', 'CHERY' => 'CHE', 'DAEWOO' => 'DAE', 'FIAT' => 'FIA',
            'SUZUKI' => 'SUZ', 'RENAULT' => 'REN', 'PEUGEOT' => 'PEU', 'BMW' => 'BMW',
            'MERCEDES' => 'MB',
        ];
        $brandCode = $brandMap[$marcaClean] ?? (strlen($marcaClean) <= 4 ? $marcaClean : substr($marcaClean, 0, 3));
        $brandLabel = ucwords(mb_strtolower($marcaClean));

        // 3. Normalizar Modelo si agrupamos por modelo base
        if ($useBaseModel && !empty($modeloClean)) {
            $modeloClean = preg_replace('/\b(L83|L86|IV GEN|NEW GEN|OLD GEN|GEN 4|GEN 5|GEN III|III GEN|TA|TP|LS4|LS)\b/i', '', $modeloClean);
            $modeloClean = trim(preg_replace('/\s+/', ' ', $modeloClean));
        }

        // 4. Construir Código Único Estructurado (ej: MTR-CH-53L, CAJ-CH-AVEO-1.6)
        $modAscii = strtr($modeloClean, [
            'Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','Ñ'=>'N',
            'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n'
        ]);

        $parts = preg_split('/[\s\/\-]+/', $modAscii, -1, PREG_SPLIT_NO_EMPTY);
        $codeParts = [];
        foreach ($parts as $p) {
            if (preg_match('/^(\d+)\.(\d+)L?$/i', $p, $matches)) {
                if (count($parts) === 1) {
                    $codeParts[] = $matches[1] . $matches[2] . 'L'; // Ej: 5.3L -> 53L
                } else {
                    $codeParts[] = $matches[1] . '.' . $matches[2]; // Ej: AVEO 1.6L -> AVEO-1.6
                }
            } else {
                $cleanPart = preg_replace('/[^A-Z0-9]/i', '', $p);
                if (!empty($cleanPart)) {
                    $codeParts[] = $cleanPart;
                }
            }
        }
        $modCode = implode('-', $codeParts) ?: 'GEN';

        if (str_starts_with($modCode, $brandCode . '-')) {
            $code = "{$tipoPrefix}-{$modCode}";
        } else {
            $code = "{$tipoPrefix}-{$brandCode}-{$modCode}";
        }
        $code = preg_replace('/-+/', '-', trim($code, '-'));

        // 5. Construir Descripción Amigable (ej: Motor Completo Chevrolet 5.3L)
        $displayModel = ucwords(mb_strtolower($modeloClean));
        $displayModel = preg_replace_callback('/\b(\d+\.\d+)l\b/i', function($m) {
            return strtoupper($m[1]) . 'L';
        }, $displayModel);
        $displayModel = preg_replace_callback('/\b(v6|v8|v10|v12|2wd|4wd|4x4|4x2|l83|l86|ls|ls4|kj|kk|hemi)\b/i', function($m) {
            return strtoupper($m[1]);
        }, $displayModel);

        if (stripos($displayModel, $brandLabel) !== false) {
            $description = "{$tipoLabel} {$displayModel}";
        } else {
            $description = "{$tipoLabel} {$brandLabel} {$displayModel}";
        }
        $description = trim(preg_replace('/\s+/', ' ', $description));

        return [
            'marca' => $marcaClean,
            'modelo' => $modeloClean ?: 'N/A',
            'code' => $code,
            'description' => $description,
            'tipo_normalized' => $tipoLabel,
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
    public function calculateReportData(int $startMonth, int $endMonth, int $year, string $groupingMode = 'base'): array
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
        $companyName = Setting::where('key', 'company_name')->value('value') ?? 'Maikel Cars, C.A.';
        $companyRif = Setting::where('key', 'company_rif')->value('value') ?? 'J-305652481';

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

            // Clave única de agrupación por Marca + Código Único
            $groupKey = $marca . '||' . $code;

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

        // Ordenar los ítems de cada marca por tipo, código y descripción
        foreach ($byBrand as $bName => &$bItems) {
            usort($bItems, function ($a, $b) {
                if ($a['type_priority'] !== $b['type_priority']) {
                    return $a['type_priority'] <=> $b['type_priority'];
                }
                $codeCmp = strnatcasecmp($a['code'] ?? '', $b['code'] ?? '');
                if ($codeCmp !== 0) {
                    return $codeCmp;
                }
                return strnatcasecmp($a['description'] ?? '', $b['description'] ?? '');
            });
        }
        unset($bItems);

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
            if (empty($bItems)) {
                continue;
            }

            $unidadesInicial = array_sum(array_column($bItems, 'unidades_inicial'));
            $unidadesEntradas = array_sum(array_column($bItems, 'unidades_entradas'));
            $unidadesSalidas = array_sum(array_column($bItems, 'unidades_salidas'));
            $unidadesRetiros = array_sum(array_column($bItems, 'unidades_retiros'));
            $unidadesAutoconsumo = array_sum(array_column($bItems, 'unidades_autoconsumo'));
            $unidadesFinal = array_sum(array_column($bItems, 'unidades_final'));

            if ($unidadesInicial == 0 && $unidadesEntradas == 0 && $unidadesSalidas == 0 && $unidadesRetiros == 0 && $unidadesAutoconsumo == 0 && $unidadesFinal == 0) {
                continue;
            }

            $brandTotales = [
                'unidades_inicial' => $unidadesInicial,
                'unidades_entradas' => $unidadesEntradas,
                'unidades_salidas' => $unidadesSalidas,
                'unidades_retiros' => $unidadesRetiros,
                'unidades_autoconsumo' => $unidadesAutoconsumo,
                'unidades_final' => $unidadesFinal,
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
