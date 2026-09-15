<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario Mensual</title>
    <style>
        @page {
            size: letter landscape;
            margin: 8mm 5mm 22mm 5mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 7pt;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
        }
        .header-container {
            width: 100%;
            margin-bottom: 8px;
            border-bottom: 2px solid #1e293b;
            padding-bottom: 4px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
        }
        .title-main {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: 0.5px;
        }
        .subtitle {
            font-size: 9pt;
            font-weight: bold;
            color: #475569;
            margin-top: 2px;
        }
        .info-box {
            text-align: right;
            font-size: 7.5pt;
            line-height: 1.3;
        }
        .info-box strong {
            color: #0f172a;
        }

        /* Report Table */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            table-layout: fixed;
        }
        .report-table th, .report-table td {
            border: 1px solid #cbd5e1;
            padding: 3px 2px;
            font-size: 6.5pt;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .report-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            padding: 3px 1px;
        }
        .report-table th.sub-header {
            background-color: #f8fafc;
            font-size: 6pt;
        }
        .report-table th.header-unidades {
            background-color: #e0f2fe;
            color: #0369a1;
        }
        .report-table th.header-valores {
            background-color: #dcfce7;
            color: #15803d;
        }
        .report-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .report-table td.num {
            text-align: right;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 6.5pt;
        }
        .report-table td.center {
            text-align: center;
        }
        .report-table td.container-tag {
            font-size: 6pt;
            color: #475569;
            line-height: 1.2;
        }
        .report-table tr.row-total td {
            background-color: #e2e8f0;
            font-weight: bold;
            border-top: 2px solid #475569;
            font-size: 7pt;
        }

        /* Footer Signatures (Fija en cada hoja para PDF) */
        .footer-signatures {
            position: fixed;
            bottom: -18mm;
            left: 0;
            right: 0;
            width: 100%;
            height: 16mm;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
        }
        .signature-line {
            width: 55%;
            margin: 0 auto;
            padding-top: 3px;
            font-size: 8pt;
            font-weight: bold;
            color: #334155;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

@if(!isset($isExcel) || !$isExcel)
    <!-- Signatures Fija en cada hoja para PDF (debe estar al inicio del body para que afecte desde la Pág 1) -->
    <div class="footer-signatures">
        <table class="signature-table">
            <tr>
                <td>
                    <div class="signature-line">
                        FIRMA
                    </div>
                </td>
                <td>
                    <div class="signature-line">
                        SELLO
                    </div>
                </td>
            </tr>
        </table>
    </div>
@endif

@if(isset($isExcel) && $isExcel)
    <!-- Header Rows para Excel con Colspans para que el título respire a lo ancho -->
    <table>
        <tr>
            <td colspan="10" style="font-size: 14pt; font-weight: bold; color: #0f172a;">{{ $companyName ?? 'INTERNAL MAIKEL CARS, C.A.' }}</td>
            <td colspan="6" style="text-align: right; font-size: 9pt; font-weight: bold; color: #334155;">RIF: {{ $companyRif ?? 'J-50000000-0' }}</td>
        </tr>
        <tr>
            <td colspan="10" style="font-size: 11pt; font-weight: bold; color: #475569;">REPORTE MENSUAL DE INVENTARIO (LIBRO DE CONTROL FISCAL)</td>
            <td colspan="6" style="text-align: right; font-size: 9pt; font-weight: bold; color: #334155;">MES Y AÑO: {{ $monthName }} / {{ $year }}</td>
        </tr>
        <tr><td colspan="16"></td></tr>
    </table>
@else
    <!-- Header Section para PDF / HTML -->
    <div class="header-container">
        <table class="header-table">
            <tr>
                <td>
                    <div class="title-main">{{ $companyName ?? 'INTERNAL MAIKEL CARS, C.A.' }}</div>
                    <div class="subtitle">REPORTE MENSUAL DE INVENTARIO (LIBRO DE CONTROL FISCAL)</div>
                </td>
                <td class="info-box">
                    <strong>RIF:</strong> {{ $companyRif ?? 'J-50000000-0' }}<br>
                    <strong>MES Y AÑO:</strong> {{ $monthName }} / {{ $year }}
                </td>
            </tr>
        </table>
    </div>
@endif

    <!-- Inventory Table -->
    <table class="report-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 9%;">MARCA</th>
                <th rowspan="2" style="width: 13%;">TIPO DE PRODUCTO</th>
                <th rowspan="2" style="width: 14%;">MODELO</th>
                <th colspan="6" class="header-unidades">UNIDADES (FÍSICAS)</th>
                <th colspan="6" class="header-valores">VALORES (BOLÍVARES - Bs.)</th>
            </tr>
            <tr>
                <!-- Unidades Subheaders -->
                <th class="sub-header" style="width: 4.5%;">INICIAL</th>
                <th class="sub-header" style="width: 5.0%;">ENTRADAS</th>
                <th class="sub-header" style="width: 4.5%;">SALIDAS</th>
                <th class="sub-header" style="width: 4.5%;">RETIROS</th>
                <th class="sub-header" style="width: 5.2%;">AUTOCONS.</th>
                <th class="sub-header" style="width: 4.3%;">FINAL</th>

                <!-- Valores Subheaders -->
                <th class="sub-header" style="width: 5.5%;">INICIAL</th>
                <th class="sub-header" style="width: 5.8%;">ENTRADAS</th>
                <th class="sub-header" style="width: 5.5%;">SALIDAS</th>
                <th class="sub-header" style="width: 5.5%;">RETIROS</th>
                <th class="sub-header" style="width: 5.7%;">AUTOCONS.</th>
                <th class="sub-header" style="width: 8.0%;">FINAL</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($brands) && count($brands) > 0)
                @foreach($brands as $brandGroup)
                    <!-- Encabezado de Sección por Marca -->
                    <tr class="row-brand-header">
                        <td colspan="15" style="background-color: #334155; color: #ffffff; font-weight: bold; font-size: 8pt; padding: 5px 8px; text-transform: uppercase;">
                            MARCA: {{ $brandGroup['brand'] }}
                        </td>
                    </tr>

                    @foreach($brandGroup['items'] as $item)
                        <tr>
                            <td class="center"><strong>{{ $item['marca'] }}</strong></td>
                            <td>{{ $item['description'] }}</td>
                            <td class="center"><strong>{{ $item['modelo'] }}</strong></td>

                            <!-- Unidades -->
                            <td class="num">{{ isset($isExcel) && $isExcel ? $item['unidades_inicial'] : number_format($item['unidades_inicial'], 0, ',', '.') }}</td>
                            <td class="num">{{ isset($isExcel) && $isExcel ? $item['unidades_entradas'] : number_format($item['unidades_entradas'], 0, ',', '.') }}</td>
                            <td class="num">{{ isset($isExcel) && $isExcel ? $item['unidades_salidas'] : number_format($item['unidades_salidas'], 0, ',', '.') }}</td>
                            <td class="num">{{ isset($isExcel) && $isExcel ? $item['unidades_retiros'] : number_format($item['unidades_retiros'], 0, ',', '.') }}</td>
                            <td class="num">{{ isset($isExcel) && $isExcel ? $item['unidades_autoconsumo'] : number_format($item['unidades_autoconsumo'], 0, ',', '.') }}</td>
                            <td class="num"><strong>{{ isset($isExcel) && $isExcel ? $item['unidades_final'] : number_format($item['unidades_final'], 0, ',', '.') }}</strong></td>

                            <!-- Valores (Bs.) -->
                            <td class="num"></td>
                            <td class="num"></td>
                            <td class="num"></td>
                            <td class="num"></td>
                            <td class="num"></td>
                            <td class="num"></td>
                        </tr>
                    @endforeach

                    <!-- Fila de Subtotal por Marca -->
                    <tr class="row-subtotal-brand">
                        <td colspan="3" style="background-color: #f1f5f9; color: #1e293b; font-weight: bold; text-align: right; padding-right: 10px;">SUBTOTAL {{ $brandGroup['brand'] }}</td>

                        <td class="num">{{ isset($isExcel) && $isExcel ? $brandGroup['totales']['unidades_inicial'] : number_format($brandGroup['totales']['unidades_inicial'], 0, ',', '.') }}</td>
                        <td class="num">{{ isset($isExcel) && $isExcel ? $brandGroup['totales']['unidades_entradas'] : number_format($brandGroup['totales']['unidades_entradas'], 0, ',', '.') }}</td>
                        <td class="num">{{ isset($isExcel) && $isExcel ? $brandGroup['totales']['unidades_salidas'] : number_format($brandGroup['totales']['unidades_salidas'], 0, ',', '.') }}</td>
                        <td class="num">{{ isset($isExcel) && $isExcel ? $brandGroup['totales']['unidades_retiros'] : number_format($brandGroup['totales']['unidades_retiros'], 0, ',', '.') }}</td>
                        <td class="num">{{ isset($isExcel) && $isExcel ? $brandGroup['totales']['unidades_autoconsumo'] : number_format($brandGroup['totales']['unidades_autoconsumo'], 0, ',', '.') }}</td>
                        <td class="num"><strong>{{ isset($isExcel) && $isExcel ? $brandGroup['totales']['unidades_final'] : number_format($brandGroup['totales']['unidades_final'], 0, ',', '.') }}</strong></td>

                        <td class="num"></td>
                        <td class="num"></td>
                        <td class="num"></td>
                        <td class="num"></td>
                        <td class="num"></td>
                        <td class="num"></td>
                    </tr>
                @endforeach
            @else
                @forelse($items as $item)
                    <tr>
                        <td class="center"><strong>{{ $item['marca'] }}</strong></td>
                        <td>{{ $item['description'] }}</td>
                        <td class="center"><strong>{{ $item['modelo'] }}</strong></td>

                        <!-- Unidades -->
                        <td class="num">{{ isset($isExcel) && $isExcel ? $item['unidades_inicial'] : number_format($item['unidades_inicial'], 0, ',', '.') }}</td>
                        <td class="num">{{ isset($isExcel) && $isExcel ? $item['unidades_entradas'] : number_format($item['unidades_entradas'], 0, ',', '.') }}</td>
                        <td class="num">{{ isset($isExcel) && $isExcel ? $item['unidades_salidas'] : number_format($item['unidades_salidas'], 0, ',', '.') }}</td>
                        <td class="num">{{ isset($isExcel) && $isExcel ? $item['unidades_retiros'] : number_format($item['unidades_retiros'], 0, ',', '.') }}</td>
                        <td class="num">{{ isset($isExcel) && $isExcel ? $item['unidades_autoconsumo'] : number_format($item['unidades_autoconsumo'], 0, ',', '.') }}</td>
                        <td class="num"><strong>{{ isset($isExcel) && $isExcel ? $item['unidades_final'] : number_format($item['unidades_final'], 0, ',', '.') }}</strong></td>

                        <!-- Valores (Bs.) -->
                        <td class="num"></td>
                        <td class="num"></td>
                        <td class="num"></td>
                        <td class="num"></td>
                        <td class="num"></td>
                        <td class="num"></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="15" class="center" style="padding: 15px; color: #64748b;">
                            No se encontraron registros de inventario ni movimientos para el mes de {{ $monthName }} del {{ $year }}.
                        </td>
                    </tr>
                @endforelse
            @endif

            <!-- Row Totales Generales -->
            <tr class="row-total">
                <td colspan="3" class="center">TOTALES GENERALES</td>

                <!-- Totales Unidades -->
                <td class="num">{{ isset($isExcel) && $isExcel ? ($totales['unidades_inicial'] ?? 0) : number_format($totales['unidades_inicial'] ?? 0, 0, ',', '.') }}</td>
                <td class="num">{{ isset($isExcel) && $isExcel ? ($totales['unidades_entradas'] ?? 0) : number_format($totales['unidades_entradas'] ?? 0, 0, ',', '.') }}</td>
                <td class="num">{{ isset($isExcel) && $isExcel ? ($totales['unidades_salidas'] ?? 0) : number_format($totales['unidades_salidas'] ?? 0, 0, ',', '.') }}</td>
                <td class="num">{{ isset($isExcel) && $isExcel ? ($totales['unidades_retiros'] ?? 0) : number_format($totales['unidades_retiros'] ?? 0, 0, ',', '.') }}</td>
                <td class="num">{{ isset($isExcel) && $isExcel ? ($totales['unidades_autoconsumo'] ?? 0) : number_format($totales['unidades_autoconsumo'] ?? 0, 0, ',', '.') }}</td>
                <td class="num">{{ isset($isExcel) && $isExcel ? ($totales['unidades_final'] ?? 0) : number_format($totales['unidades_final'] ?? 0, 0, ',', '.') }}</td>

                <!-- Totales Valores (Bs.) -->
                <td class="num"></td>
                <td class="num"></td>
                <td class="num"></td>
                <td class="num"></td>
                <td class="num"></td>
                <td class="num"></td>
            </tr>
        </tbody>
    </table>

@if(isset($isExcel) && $isExcel)
    <!-- Signatures para la cuadrícula de Excel -->
    <table>
        <tr><td colspan="15"></td></tr>
        <tr><td colspan="15"></td></tr>
        <tr>
            <td colspan="7" style="text-align: center; font-weight: bold; font-size: 10pt;">FIRMA</td>
            <td colspan="1"></td>
            <td colspan="7" style="text-align: center; font-weight: bold; font-size: 10pt;">SELLO</td>
        </tr>
    </table>
@endif

</body>
</html>
