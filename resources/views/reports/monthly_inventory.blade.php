<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Mensual de Inventario - {{ $monthName }} {{ $year }}</title>
    <style>
        @page {
            size: letter landscape;
            margin: 12mm 8mm 12mm 8mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 7.5pt;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
        }
        .header-container {
            width: 100%;
            margin-bottom: 10px;
            border-bottom: 2px solid #1e293b;
            padding-bottom: 6px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
        }
        .title-main {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: 0.5px;
        }
        .subtitle {
            font-size: 9.5pt;
            font-weight: bold;
            color: #475569;
            margin-top: 2px;
        }
        .info-box {
            text-align: right;
            font-size: 8pt;
            line-height: 1.3;
        }
        .info-box strong {
            color: #0f172a;
        }

        /* Report Table */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .report-table th, .report-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 4px;
            font-size: 7pt;
        }
        .report-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        .report-table th.sub-header {
            background-color: #f8fafc;
            font-size: 6.5pt;
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
            font-family: 'Courier New', Courier, monospace;
        }
        .report-table td.center {
            text-align: center;
        }
        .report-table td.container-tag {
            font-size: 6.5pt;
            color: #475569;
        }
        .report-table tr.row-total td {
            background-color: #e2e8f0;
            font-weight: bold;
            border-top: 2px solid #475569;
            font-size: 7.5pt;
        }

        /* Footer Signatures */
        .footer-signatures {
            margin-top: 30px;
            width: 100%;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
            padding-top: 35px;
        }
        .signature-line {
            width: 60%;
            margin: 0 auto;
            border-top: 1px solid #334155;
            padding-top: 4px;
            font-size: 7.5pt;
            font-weight: bold;
            color: #334155;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <div class="header-container">
        <table class="header-table">
            <tr>
                <td>
                    <div class="title-main">{{ $companyName ?? 'INTERNAL MAIKEL CARS, C.A.' }}</div>
                    <div class="subtitle">REPORTE MENSUAL DE INVENTARIO (LIBRO DE CONTROL FISCAL)</div>
                </td>
                <td class="info-box">
                    <strong>RIF:</strong> {{ $companyRif ?? 'J-50000000-0' }}<br>
                    <strong>MES Y AÑO:</strong> {{ $monthName }} / {{ $year }}<br>
                    <strong>TASA BCV:</strong> {{ number_format($exchangeRate ?? 1.0, 2, ',', '.') }} Bs./USD
                </td>
            </tr>
        </table>
    </div>

    <!-- Inventory Table -->
    <table class="report-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 12%;">MARCA / MODELO</th>
                <th rowspan="2" style="width: 18%;">PRODUCTO / DESCRIPCIÓN</th>
                <th rowspan="2" style="width: 12%;">LOTES (CONTENEDORES)</th>
                <th colspan="6" class="header-unidades">UNIDADES (FÍSICAS)</th>
                <th colspan="6" class="header-valores">VALORES (BOLÍVARES - Bs.)</th>
            </tr>
            <tr>
                <!-- Unidades Subheaders -->
                <th class="sub-header" style="width: 4%;">INICIAL</th>
                <th class="sub-header" style="width: 4%;">ENTRADAS</th>
                <th class="sub-header" style="width: 4%;">SALIDAS</th>
                <th class="sub-header" style="width: 4%;">RETIROS</th>
                <th class="sub-header" style="width: 4%;">AUTOCONS.</th>
                <th class="sub-header" style="width: 4.5%;">FINAL</th>

                <!-- Valores Subheaders -->
                <th class="sub-header" style="width: 6.5%;">INICIAL</th>
                <th class="sub-header" style="width: 6.5%;">ENTRADAS</th>
                <th class="sub-header" style="width: 6.5%;">SALIDAS</th>
                <th class="sub-header" style="width: 6.5%;">RETIROS</th>
                <th class="sub-header" style="width: 6.5%;">AUTOCONS.</th>
                <th class="sub-header" style="width: 7%;">FINAL</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td class="center"><strong>{{ $item['code'] }}</strong></td>
                    <td>{{ $item['description'] }}</td>
                    <td class="container-tag">{{ $item['containers_str'] }}</td>

                    <!-- Unidades -->
                    <td class="num">{{ number_format($item['unidades_inicial'], 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($item['unidades_entradas'], 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($item['unidades_salidas'], 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($item['unidades_retiros'], 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($item['unidades_autoconsumo'], 0, ',', '.') }}</td>
                    <td class="num"><strong>{{ number_format($item['unidades_final'], 0, ',', '.') }}</strong></td>

                    <!-- Valores (Bs.) -->
                    <td class="num">{{ number_format($item['valores_inicial'], 2, ',', '.') }}</td>
                    <td class="num">{{ number_format($item['valores_entradas'], 2, ',', '.') }}</td>
                    <td class="num">{{ number_format($item['valores_salidas'], 2, ',', '.') }}</td>
                    <td class="num">{{ number_format($item['valores_retiros'], 2, ',', '.') }}</td>
                    <td class="num">{{ number_format($item['valores_autoconsumo'], 2, ',', '.') }}</td>
                    <td class="num"><strong>{{ number_format($item['valores_final'], 2, ',', '.') }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="15" class="center" style="padding: 15px; color: #64748b;">
                        No se encontraron registros de inventario ni movimientos para el mes de {{ $monthName }} del {{ $year }}.
                    </td>
                </tr>
            @endforelse

            <!-- Row Totales -->
            <tr class="row-total">
                <td colspan="3" class="center">TOTALES GENERALES</td>

                <!-- Totales Unidades -->
                <td class="num">{{ number_format($totales['unidades_inicial'] ?? 0, 0, ',', '.') }}</td>
                <td class="num">{{ number_format($totales['unidades_entradas'] ?? 0, 0, ',', '.') }}</td>
                <td class="num">{{ number_format($totales['unidades_salidas'] ?? 0, 0, ',', '.') }}</td>
                <td class="num">{{ number_format($totales['unidades_retiros'] ?? 0, 0, ',', '.') }}</td>
                <td class="num">{{ number_format($totales['unidades_autoconsumo'] ?? 0, 0, ',', '.') }}</td>
                <td class="num">{{ number_format($totales['unidades_final'] ?? 0, 0, ',', '.') }}</td>

                <!-- Totales Valores (Bs.) -->
                <td class="num">{{ number_format($totales['valores_inicial'] ?? 0, 2, ',', '.') }}</td>
                <td class="num">{{ number_format($totales['valores_entradas'] ?? 0, 2, ',', '.') }}</td>
                <td class="num">{{ number_format($totales['valores_salidas'] ?? 0, 2, ',', '.') }}</td>
                <td class="num">{{ number_format($totales['valores_retiros'] ?? 0, 2, ',', '.') }}</td>
                <td class="num">{{ number_format($totales['valores_autoconsumo'] ?? 0, 2, ',', '.') }}</td>
                <td class="num">{{ number_format($totales['valores_final'] ?? 0, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Signatures -->
    <div class="footer-signatures">
        <table class="signature-table">
            <tr>
                <td>
                    <div class="signature-line">
                        ELABORADO POR / CONTADOR<br>
                        <span style="font-weight: normal; font-size: 6.5pt; color: #64748b;">Firma y C.I.</span>
                    </div>
                </td>
                <td>
                    <div class="signature-line">
                        REPRESENTANTE LEGAL / SELLO<br>
                        <span style="font-weight: normal; font-size: 6.5pt; color: #64748b;">Firma y Sello de la Empresa</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
