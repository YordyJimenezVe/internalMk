<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formato Manual</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 7pt;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
        }
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
            vertical-align: middle;
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
        .report-table td.num {
            text-align: right;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 6.5pt;
        }
        .report-table td.center {
            text-align: center;
        }
        .report-table tr.row-total td {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            font-size: 7pt;
        }
    </style>
</head>
<body>

    <!-- Header Rows para Excel -->
    <table>
        <tr>
            <td style="font-size: 14pt; font-weight: bold; color: #0f172a;">{{ $companyName ?? 'Maikel Cars, C.A.' }}</td>
        </tr>
        <tr>
            <td style="font-size: 11pt; font-weight: bold; color: #0f172a;">RIF: {{ $companyRif ?? 'J-305652481' }}</td>
        </tr>
        <tr>
            <td style="font-size: 11pt; font-weight: bold; color: #0f172a;">PERIODO:{{ !empty($periodName) ? ' ' . $periodName . ($year ? ' ' . $year : '') : '' }}</td>
        </tr>
        <tr>
            <td style="font-size: 11pt; font-weight: bold; color: #0f172a;">REGISTRO MANUAL DE ENTRADAS Y SALIDAS</td>
        </tr>
    </table>

    <!-- Inventory Table (Estructura idéntica y limpia sin contenido precargado) -->
    <table class="report-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 12%;">CÓDIGO</th>
                <th rowspan="2" style="width: 24%;">DESCRIPCIÓN</th>
                <th colspan="6" class="header-unidades">UNIDADES (FÍSICAS)</th>
                <th colspan="6" class="header-valores">VALORES EN BOLÍVARES (BS.)</th>
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
            @php
                $startRow = 8;
                $totalRows = 60;
                $endRow = $startRow + $totalRows - 1;
            @endphp

            @for ($i = 0; $i < $totalRows; $i++)
                @php
                    $r = $startRow + $i;
                @endphp
                <tr>
                    <!-- Código, Descripción (Vacíos para llenado manual) -->
                    <td class="center"></td>
                    <td></td>

                    <!-- Unidades Físicas (Vacías, y en FINAL fórmula condicional que no muestra cero) -->
                    <td class="num"></td>
                    <td class="num"></td>
                    <td class="num"></td>
                    <td class="num"></td>
                    <td class="num"></td>
                    <td class="num">=IF(COUNT(C{{ $r }}:G{{ $r }})>0,C{{ $r }}+D{{ $r }}-E{{ $r }}-F{{ $r }}-G{{ $r }},"")</td>

                    <!-- Valores en Bolívares (Vacíos, y en FINAL fórmula condicional que no muestra cero) -->
                    <td class="num"></td>
                    <td class="num"></td>
                    <td class="num"></td>
                    <td class="num"></td>
                    <td class="num"></td>
                    <td class="num">=IF(COUNT(I{{ $r }}:M{{ $r }})>0,I{{ $r }}+J{{ $r }}-K{{ $r }}-L{{ $r }}-M{{ $r }},"")</td>
                </tr>
            @endfor

            <!-- Fila de Totales Generales con Suma Dinámica -->
            @php
                $totalRow = $endRow + 1;
            @endphp
            <tr class="row-total">
                <td colspan="2" class="center">TOTALES GENERALES</td>

                <!-- Suma Unidades -->
                <td class="num">=IF(COUNT(C{{ $startRow }}:C{{ $endRow }})>0,SUM(C{{ $startRow }}:C{{ $endRow }}),"")</td>
                <td class="num">=IF(COUNT(D{{ $startRow }}:D{{ $endRow }})>0,SUM(D{{ $startRow }}:D{{ $endRow }}),"")</td>
                <td class="num">=IF(COUNT(E{{ $startRow }}:E{{ $endRow }})>0,SUM(E{{ $startRow }}:E{{ $endRow }}),"")</td>
                <td class="num">=IF(COUNT(F{{ $startRow }}:F{{ $endRow }})>0,SUM(F{{ $startRow }}:F{{ $endRow }}),"")</td>
                <td class="num">=IF(COUNT(G{{ $startRow }}:G{{ $endRow }})>0,SUM(G{{ $startRow }}:G{{ $endRow }}),"")</td>
                <td class="num"><strong>=IF(COUNT(H{{ $startRow }}:H{{ $endRow }})>0,SUM(H{{ $startRow }}:H{{ $endRow }}),"")</strong></td>

                <!-- Suma Valores (Bs.) -->
                <td class="num">=IF(COUNT(I{{ $startRow }}:I{{ $endRow }})>0,SUM(I{{ $startRow }}:I{{ $endRow }}),"")</td>
                <td class="num">=IF(COUNT(J{{ $startRow }}:J{{ $endRow }})>0,SUM(J{{ $startRow }}:J{{ $endRow }}),"")</td>
                <td class="num">=IF(COUNT(K{{ $startRow }}:K{{ $endRow }})>0,SUM(K{{ $startRow }}:K{{ $endRow }}),"")</td>
                <td class="num">=IF(COUNT(L{{ $startRow }}:L{{ $endRow }})>0,SUM(L{{ $startRow }}:L{{ $endRow }}),"")</td>
                <td class="num">=IF(COUNT(M{{ $startRow }}:M{{ $endRow }})>0,SUM(M{{ $startRow }}:M{{ $endRow }}),"")</td>
                <td class="num"><strong>=IF(COUNT(N{{ $startRow }}:N{{ $endRow }})>0,SUM(N{{ $startRow }}:N{{ $endRow }}),"")</strong></td>
            </tr>
        </tbody>
    </table>

    <!-- Signatures para la cuadrícula de Excel -->
    <table>
        <tr><td colspan="14"></td></tr>
        <tr><td colspan="14"></td></tr>
        <tr>
            <td colspan="6" style="text-align: center; font-weight: bold; font-size: 10pt;">FIRMA</td>
            <td colspan="2"></td>
            <td colspan="6" style="text-align: center; font-weight: bold; font-size: 10pt;">SELLO</td>
        </tr>
    </table>

</body>
</html>
