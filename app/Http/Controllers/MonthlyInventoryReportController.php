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
        $productType = $request->input('product_type', 'all');

        $reportData = $this->calculateReportData($startMonth, $endMonth, $year, $groupingMode, $productType);

        return inertia('Reports/MonthlyReport', [
            'initialStartMonth' => $startMonth,
            'initialEndMonth' => $endMonth,
            'initialYear' => $year,
            'initialGroupingMode' => $groupingMode,
            'initialProductType' => $productType,
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
        $productType = $request->input('product_type', 'all');

        $reportData = $this->calculateReportData($startMonth, $endMonth, $year, $groupingMode, $productType);

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
        $productType = $request->input('product_type', 'all');

        $reportData = $this->calculateReportData($startMonth, $endMonth, $year, $groupingMode, $productType);

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
        $productType = $request->input('product_type', 'all');

        $reportData = $this->calculateReportData($startMonth, $endMonth, $year, $groupingMode, $productType);
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
        $productType = $request->input('product_type', 'all');

        $reportData = $this->calculateReportData($startMonth, $endMonth, $year, $groupingMode, $productType);
        $periodSlug = str_replace(' ', '_', $reportData['periodName']);
        $prefix = ($startMonth === $endMonth) ? 'Formato_Manual_Inventario_Mensual' : 'Formato_Manual_Inventario_Periodo';

        return Excel::download(
            new \App\Exports\ManualInventoryTemplateExport($reportData),
            "{$prefix}_{$periodSlug}_{$year}.xlsx"
        );
    }

    /**
     * Normaliza el Tipo de producto y el Modelo para agrupar por Tipo + Marca + Modelo Base,
     * generando un CÓDIGO compacto alfanumérico (ej: MTCH43, MTFD54, MTCH53, MTCHAVEO16) y una DESCRIPCIÓN clara (ej: Motor Completo Ford 5.4L 3v, Motor Completo Chevrolet 4.3L Vortec 262).
     */
    private function normalizeModelInfo(string $tipo, string $marca, string $modelo, bool $useBaseModel = true): array
    {
        $tipoClean = mb_strtoupper(trim($tipo));
        $marcaClean = mb_strtoupper(trim($marca));
        if (empty($marcaClean)) {
            $marcaClean = 'OTRAS MARCAS';
        }
        $modeloClean = trim($modelo);

        // 1. Tipo Prefix (2 caracteres) & Label
        if (strpos($tipoClean, 'CAJA') !== false) {
            $tipoPrefix = 'CJ';
            $tipoLabel = 'Caja';
        } elseif (strpos($tipoClean, 'CÁMARA') !== false || strpos($tipoClean, 'CAMARA') !== false) {
            $tipoPrefix = 'CM';
            $tipoLabel = 'Cámara';
        } elseif (strpos($tipoClean, 'AUTOPARTE') !== false) {
            $tipoPrefix = 'AP';
            $tipoLabel = 'Autoparte';
        } else {
            // Todo lo que sea motor (7/8, 3/4, 5/8, Completo, etc.) inicia por MT
            $tipoPrefix = 'MT';
            if (strpos($tipoClean, '7/8') !== false) {
                $tipoLabel = 'Motor 7/8';
            } elseif (strpos($tipoClean, '3/4') !== false) {
                $tipoLabel = 'Motor 3/4';
            } elseif (strpos($tipoClean, '5/8') !== false) {
                $tipoLabel = 'Motor 5/8';
            } elseif (strpos($tipoClean, 'COMPLETO') !== false || strpos($tipoClean, '4/4') !== false) {
                $tipoLabel = 'Motor Completo';
            } else {
                $tipoLabel = !empty($tipoClean) ? ucwords(mb_strtolower($tipoClean)) : 'Motor';
            }
        }

        // 2. Brand Code (2 caracteres) & Label
        $brandMap = [
            'CHEVROLET' => 'CH', 'FORD' => 'FD', 'TOYOTA' => 'TY', 'JEEP' => 'JP',
            'DODGE' => 'DG', 'NISSAN' => 'NS', 'MITSUBISHI' => 'MB', 'HYUNDAI' => 'HY',
            'HYUNDAI/KIA' => 'HY', 'KIA' => 'KA', 'HONDA' => 'HN', 'MAZDA' => 'MZ',
            'ISUZU' => 'IS', 'VOLKSWAGEN' => 'VW', 'CHRYSLER' => 'CR', 'RAM' => 'RM',
            'CUMMINS' => 'CU', 'MACK' => 'MK', 'INTERNATIONAL' => 'IN', 'CHERY' => 'CY',
            'DAEWOO' => 'DW', 'FIAT' => 'FT', 'SUZUKI' => 'SZ', 'RENAULT' => 'RN',
            'PEUGEOT' => 'PG', 'BMW' => 'BM', 'MERCEDES' => 'MB', 'MERCEDES-BENZ' => 'MB',
            'CARIBE' => 'CB', 'CATERPILLAR' => 'CT', 'MINI' => 'MN'
        ];
        $brandCode = $brandMap[$marcaClean] ?? substr(preg_replace('/[^A-Z]/', '', $marcaClean), 0, 2);
        if (empty($brandCode)) {
            $brandCode = 'OT';
        }
        $brandLabel = ucwords(mb_strtolower($marcaClean));

        // 3. Normalizar Modelo si agrupamos por modelo base
        if ($useBaseModel && !empty($modeloClean)) {
            $modeloClean = preg_replace('/\b(L83|L86|IV GENERACION|IV GENERACIÓN|IV GEN|NEW GEN|OLD GEN|GEN 4|GEN 5|GEN III|III GEN|TA|TP|LS4|LS)\b/i', '', $modeloClean);
            $modeloClean = trim(preg_replace('/\s+/', ' ', $modeloClean));
        }

        // 4. Construir Código Compacto Alfanumérico (ej: MTCH43, MTFD54, MTCH53, MTCHMA, MTCH454)
        $mod = mb_strtoupper(trim($modeloClean));
        $mod = strtr($mod, [
            'Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','Ñ'=>'N',
            'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n'
        ]);

        // Si tiene cilindrada en litros (ej: 4.3), eliminar pulgadas cúbicas secundarias como 262
        if (preg_match('/(\d+)\.(\d+)/', $mod)) {
            $mod = preg_replace('/\b(262|350|305|302|454|366)\b/', '', $mod);
        }

        // Eliminar palabras secundarias que no forman parte del código base
        $noisePatterns = [
            '/\b(PREPARADO|DIESEL|GASOLINA)\b/i',
            '/\b(IV GEN|III GEN|IV GENERACION|IV GENERACIÓN|III GENERACION|III GENERACIÓN|NEW GEN|OLD GEN|1RA GEN|2DA GEN|GEN 4|GEN 5|GEN III)\b/i',
            '/\b(L83|L86|LS4|LS3|LS|TA|TP|2WD|4WD|4X4|4X2|EGR|CON EGR|SIN EGR|TBI|VVT-I|2VVTI|VVTI|DUAL|SIN|CON|FULL|INYECCION|INYECCIÓN|HOUSING|PARA|MAQUINARIA)\b/i',
            '/\b(4 CIL|6 CIL|8 CIL|6 CILINDROS|4 CILINDROS|5 CILINDROS|3 CILINDROS|CILINDROS|CIL)\b/i',
            '/\b(V6|V8|V10|V12)\b/i',
        ];
        foreach ($noisePatterns as $pattern) {
            $mod = preg_replace($pattern, ' ', $mod);
        }

        // Remover marca si está repetida en el modelo
        $mod = preg_replace('/\b' . preg_quote($marcaClean, '/') . '\b/i', ' ', $mod);
        $mod = trim(preg_replace('/\s+/', ' ', $mod));

        // Determinar sufijo del código (buscando que el código total tenga de 5 a 6 caracteres)
        if (preg_match('/^(\d{3,4})$/', $mod, $m)) {
            // Bloque numérico directo (ej: 454 -> CH454, 350 -> CH350, 305 -> CH305, etc.)
            $suffix = $m[1];
        } elseif (preg_match('/^(\d)$/', $mod, $m)) {
            // Mazda 3 -> MZMA3 (5 caracteres)
            $suffix = 'MA' . $m[1];
        } elseif (preg_match('/(\d+)\.(\d+)/', $mod, $dm)) {
            $disp = $dm[1] . $dm[2]; // Ej: 1.6 -> 16, 2.5 -> 25, 4.2 -> 42, 4.3 -> 43, 5.3 -> 53
            // Dividir lo que está antes y después de la cilindrada
            $parts = preg_split('/(\d+)\.(\d+)\s*L?/i', $mod);
            $before = trim($parts[0] ?? '');
            $after = trim($parts[1] ?? '');

            // 1. Si hay nombre de vehículo ANTES de la cilindrada (ej: AVEO 1.6L -> AV16, MALIBU 2.5 -> MA25, TRAILBLAZER 4.2L -> TR42, VITARA 1.6L -> VI16)
            if (preg_match('/\b([A-Z]{2,})\b/i', $before, $wm)) {
                $suffix = substr(strtoupper($wm[1]), 0, 2) . $disp; // Ej: CHAV16, CHMA25, CHTR42, CHVI16
            } elseif (preg_match('/^(\d)\b/', $before, $nm)) {
                // Ej: Mazda 6 2.3L -> 623 (MZ623)
                $suffix = $nm[1] . $disp;
            } elseif (preg_match('/\b([A-Z])\w*\s+([A-Z])\w*\b/', $after, $twm)) {
                // 2a. Si hay 2 palabras descriptoras DESPUÉS (ej: 6.0L REY CAMION -> 60RC -> CH60RC)
                $suffix = $disp . strtoupper($twm[1] . $twm[2]);
            } elseif (preg_match('/\b([A-Z0-9]{2,})\b/i', $after, $desc)) {
                // 2b. Si hay descriptor DESPUÉS de la cilindrada (ej: 4.3L VORTEC -> 43VO -> CH43VO, 5.4L 3V -> 543V -> FD543V, 3.7L KJ -> 37KJ -> JP37KJ)
                $word = strtoupper($desc[1]);
                $descCode = substr($word, 0, 2);
                $suffix = $disp . $descCode;
            } else {
                // 3. Cilindrada pura sin vehículo ni descriptor (ej: 5.3L -> 53L -> CH53L, 6.0L -> 60L -> CH60L, 2.0L -> 20L -> FD20L)
                $suffix = $disp . 'L';
            }
        } else {
            // Código con dígito (ej: 2ZR, 1ZZ, QR25, MR18, C7, 4BT, DT466, J18)
            if (preg_match('/\b([A-Z]{0,2}\d[A-Z0-9]{0,4})\b/i', $mod, $em)) {
                $codePart = preg_replace('/[^A-Z0-9]/', '', $em[1]);
                $rest = trim(preg_replace('/\b' . preg_quote($em[1], '/') . '\b/i', ' ', $mod));
                if (preg_match('/\b([A-Z]{2,})\b/i', $rest, $wm)) {
                    // Hay nombre de vehículo + código (ej: VITARA J18 -> V + J18 = VJ18 -> CHVJ18)
                    $availChars = max(1, 4 - strlen($codePart));
                    $prefixModel = substr(strtoupper($wm[1]), 0, $availChars);
                    $suffix = $prefixModel . $codePart;
                } else {
                    $suffix = $codePart;
                    if (strlen($suffix) == 2) {
                        $suffix = $suffix . '0';
                    }
                }
            } else {
                // Nombre de vehículo solo (ej: MALIBU -> MAL -> CHMAL, CAPTIVA -> CAP -> CHCAP, CRUZE -> CRU -> CHCRU, RAM -> RAM -> DGRAM)
                // Se toman 3 caracteres para garantizar 5 letras con la marca (2 + 3 = 5)
                $cleanWords = preg_replace('/[^A-Z]/', '', $mod);
                $suffix = substr($cleanWords, 0, 3);
                if (strlen($suffix) < 3) {
                    $suffix = str_pad($suffix, 3, 'X');
                }
            }
        }

        if (empty($suffix)) {
            $suffix = 'GEN';
        }

        // Limitar sufijo del modelo a máximo 4 caracteres (ej: AV16, MA25, TR42, 43VO, 53L, VJ18, G4KE, 454)
        $suffix = substr($suffix, 0, 4);

        // Código compacto sin prefijo de producto (5 a 6 caracteres, ej: CH43VO, CH53L, CHAV16, CHMA25, CHTR42, CH454, CHVJ18)
        $code = "{$brandCode}{$suffix}";

        // 5. Construir Descripción Amigable (ej: Motor Completo Ford 5.4L 3v, Motor Completo Chevrolet 4.3L Vortec 262)
        $displayModel = ucwords(mb_strtolower($modeloClean));
        $displayModel = preg_replace_callback('/\b(\d+\.\d+)l\b/i', function($m) {
            return strtoupper($m[1]) . 'L';
        }, $displayModel);
        $displayModel = preg_replace('/\b(\d)(\d)l\b/i', '$1.$2L', $displayModel);
        $displayModel = preg_replace('/\b(\d+\.\d+)(?!\s*L\b)/i', '$1L', $displayModel);
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
    public function calculateReportData(int $startMonth, int $endMonth, int $year, string $groupingMode = 'base', string $productType = 'all'): array
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

            // Filtrar por Tipo de Producto si no es 'all'
            if ($productType !== 'all') {
                $tipoUpper = mb_strtoupper($tipo);
                if (strpos($tipoUpper, 'CAJA') !== false || strpos($tipoUpper, 'TRANSMIS') !== false) {
                    $itemCategory = 'cajas';
                } elseif (strpos($tipoUpper, 'CAMARA') !== false || strpos($tipoUpper, 'CÁMARA') !== false) {
                    $itemCategory = 'camaras';
                } elseif (strpos($tipoUpper, 'ACCESORIO') !== false || strpos($tipoUpper, 'AUTOPARTE') !== false || strpos($tipoUpper, 'REPUESTO') !== false) {
                    $itemCategory = 'accesorios';
                } else {
                    $itemCategory = 'motores';
                }

                if ($productType !== $itemCategory) {
                    continue;
                }
            }

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

            // Clave única de agrupación por Marca + Código + Descripción
            $groupKey = $marca . '||' . $code . '||' . $description;

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
            'productType' => $productType,
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
