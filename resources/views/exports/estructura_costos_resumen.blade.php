<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 0.5cm;
            size: landscape;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            width: 100%;
            color: #0f172a;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.data-table th {
            background-color: #1e1b4b;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            height: 35px;
            font-size: 9px;
            border: 1px solid #312e81;
            text-transform: uppercase;
        }

        table.data-table td {
            border: 1px solid #cbd5e1;
            text-align: center;
            vertical-align: middle;
            font-size: 9.5px;
            padding: 6px 4px;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .text-left {
            text-align: left !important;
        }

        .zebra {
            background-color: #f8fafc;
        }

        .header-bg {
            background-color: #312e81;
            color: #ffffff;
        }

        .highlight-usd {
            color: #0284c7;
            font-weight: bold;
        }

        .highlight-count {
            color: #4f46e5;
            font-weight: bold;
        }

        .highlight-total {
            color: #059669;
            font-weight: bold;
        }
    </style>
</head>

<body>
    @php
        $isExcel = $isExcel ?? true;

        $grandTotalCantidad = 0;
        $grandTotalCostoUsd = 0;
        $grandTotalBaseImponibleUsd = 0;

        foreach ($grupos as $g) {
            $grandTotalCantidad += $g['cantidad'];
            $grandTotalCostoUsd += $g['total_costo_usd'];
            $grandTotalBaseImponibleUsd += $g['total_base_imponible_usd'];
        }

        $headerStyle = 'background-color: #1e1b4b; color: #FFFFFF; font-weight: bold; text-align: center; vertical-align: middle; height: 36px; font-size: 9px; border: 1px solid #312e81; text-transform: uppercase;';
        $cellStyle = 'border: 1px solid #cbd5e1; text-align: center; vertical-align: middle; font-size: 9.5px; padding: 6px;';
        $zebraStyle = 'background-color: #f8fafc;';
    @endphp

    <table>
        <tr>
            <td colspan="8" style="font-size: 16px; font-weight: bold; color: #1e1b4b; text-align: left; height: 28px;">
                REPORTE DE INVENTARIO - RESUMEN AGRUPADO POR TIPO Y COSTO
            </td>
        </tr>
        <tr>
            <td colspan="8" style="font-size: 10px; color: #475569; text-align: left; height: 20px;">
                Generado el: {{ date('d/m/Y H:i:s') }} | Total Grupos: {{ count($grupos) }} | Total Piezas: {{ number_format($grandTotalCantidad, 0, ',', '.') }}
            </td>
        </tr>
        <tr><td colspan="8"></td></tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="{{ $headerStyle }} width: 6px;">N°</th>
                <th style="{{ $headerStyle }} width: 28px;">TIPO DE REGISTRO / PRODUCTO</th>
                <th style="{{ $headerStyle }} width: 18px;">COSTO UNITARIO BASE ($)</th>
                <th style="{{ $headerStyle }} width: 20px;">BASE IMPONIBLE UNITARIA ($)</th>
                <th style="{{ $headerStyle }} width: 14px;">CANTIDAD (PIEZAS)</th>
                <th style="{{ $headerStyle }} width: 22px;">TOTAL COSTO BASE ($)</th>
                <th style="{{ $headerStyle }} width: 22px;">TOTAL BASE IMPONIBLE ($)</th>
                <th style="{{ $headerStyle }} width: 14px;">% DEL INVENTARIO</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($grupos as $index => $group)
                @php
                    $isZebra = $index % 2 === 1;
                    $rowStyle = $isZebra ? $cellStyle . $zebraStyle : $cellStyle;
                    $percentOfTotal = $grandTotalCantidad > 0 ? round(($group['cantidad'] / $grandTotalCantidad) * 100, 2) : 0;
                @endphp
                <tr>
                    <td style="{{ $rowStyle }} font-weight: bold; color: #64748b;">{{ $index + 1 }}</td>
                    <td style="{{ $rowStyle }} text-align: left; font-weight: bold; color: #1e1b4b;">{{ $group['tipo'] }}</td>
                    <td style="{{ $rowStyle }} text-align: right; color: #0284c7; font-weight: bold;">
                        {{ $isExcel ? $group['costo_usd'] : '$ ' . number_format($group['costo_usd'], 2, ',', '.') }}
                    </td>
                    <td style="{{ $rowStyle }} text-align: right; color: #4f46e5; font-weight: bold;">
                        {{ $isExcel ? $group['base_imponible_usd'] : '$ ' . number_format($group['base_imponible_usd'], 2, ',', '.') }}
                    </td>
                    <td style="{{ $rowStyle }} font-weight: bold; color: #0f172a; font-size: 10px;">
                        {{ $isExcel ? $group['cantidad'] : number_format($group['cantidad'], 0, ',', '.') }}
                    </td>
                    <td style="{{ $rowStyle }} text-align: right; color: #0284c7; font-weight: bold;">
                        {{ $isExcel ? $group['total_costo_usd'] : '$ ' . number_format($group['total_costo_usd'], 2, ',', '.') }}
                    </td>
                    <td style="{{ $rowStyle }} text-align: right; color: #059669; font-weight: bold;">
                        {{ $isExcel ? $group['total_base_imponible_usd'] : '$ ' . number_format($group['total_base_imponible_usd'], 2, ',', '.') }}
                    </td>
                    <td style="{{ $rowStyle }} font-weight: bold; color: #475569;">
                        {{ $percentOfTotal }}%
                    </td>
                </tr>
            @endforeach

            <!-- Total Row -->
            <tr style="background-color: #1e1b4b; color: #ffffff; font-weight: bold; height: 32px;">
                <td colspan="4" style="border: 1px solid #312e81; text-align: right; font-size: 10px; padding: 6px; text-transform: uppercase;">
                    TOTALES GENERALES
                </td>
                <td style="border: 1px solid #312e81; text-align: center; font-size: 11px; font-weight: bold; color: #ffffff;">
                    {{ $isExcel ? $grandTotalCantidad : number_format($grandTotalCantidad, 0, ',', '.') }}
                </td>
                <td style="border: 1px solid #312e81; text-align: right; font-size: 10.5px; font-weight: bold; color: #38bdf8;">
                    {{ $isExcel ? $grandTotalCostoUsd : '$ ' . number_format($grandTotalCostoUsd, 2, ',', '.') }}
                </td>
                <td style="border: 1px solid #312e81; text-align: right; font-size: 10.5px; font-weight: bold; color: #34d399;">
                    {{ $isExcel ? $grandTotalBaseImponibleUsd : '$ ' . number_format($grandTotalBaseImponibleUsd, 2, ',', '.') }}
                </td>
                <td style="border: 1px solid #312e81; text-align: center; font-size: 10px; font-weight: bold; color: #ffffff;">
                    100%
                </td>
            </tr>
        </tbody>
    </table>

    <br><br>

    <!-- Subtotales por Tipo -->
    <table>
        <tr>
            <td colspan="4" style="font-size: 13px; font-weight: bold; color: #1e1b4b; text-align: left; height: 24px;">
                SUBTOTALES GENERALES AGRUPADOS POR TIPO
            </td>
        </tr>
    </table>

    <table class="data-table" style="width: 70%;">
        <thead>
            <tr>
                <th style="{{ $headerStyle }} width: 28px;">TIPO DE REGISTRO / PRODUCTO</th>
                <th style="{{ $headerStyle }} width: 16px;">CANTIDAD DE PIEZAS</th>
                <th style="{{ $headerStyle }} width: 22px;">TOTAL COSTO BASE ($)</th>
                <th style="{{ $headerStyle }} width: 22px;">TOTAL BASE IMPONIBLE ($)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($resumenPorTipo as $tipoName => $sub)
                <tr>
                    <td style="{{ $cellStyle }} text-align: left; font-weight: bold; color: #1e1b4b;">{{ $tipoName }}</td>
                    <td style="{{ $cellStyle }} font-weight: bold; color: #4f46e5;">
                        {{ $isExcel ? $sub['cantidad'] : number_format($sub['cantidad'], 0, ',', '.') }}
                    </td>
                    <td style="{{ $cellStyle }} text-align: right; color: #0284c7; font-weight: bold;">
                        {{ $isExcel ? $sub['total_costo_usd'] : '$ ' . number_format($sub['total_costo_usd'], 2, ',', '.') }}
                    </td>
                    <td style="{{ $cellStyle }} text-align: right; color: #059669; font-weight: bold;">
                        {{ $isExcel ? $sub['total_base_imponible_usd'] : '$ ' . number_format($sub['total_base_imponible_usd'], 2, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
