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

        .highlight-bs {
            color: #059669;
            font-weight: bold;
        }

        .highlight-final {
            color: #4f46e5;
            font-weight: bold;
        }
    </style>
</head>

<body>
    @php
        $isExcel = $isExcel ?? true;
        $totalItems = count($partidas);
        $totalImportacionBs = $partidas->sum('costo_bs_calc');
        $totalImportacionUsd = $partidas->sum('costo_usd_calc');
        $totalProrrateoBs = $partidas->sum('prorrateo_bs_calc');
        $totalLandedBs = $partidas->sum('costo_landed_bs_calc');
        $totalBaseImponibleBs = $partidas->sum('base_imponible_bs_calc');
        $totalIvaBs = $partidas->sum('iva_bs_calc');
        $totalPrecioConIvaBs = $partidas->sum('precio_con_iva_bs_calc');
        $totalPrecioVentaUsd = $partidas->sum('precio_venta_usd_calc');

        $headerStyle = 'background-color: #312e81; color: #FFFFFF; font-weight: bold; text-align: center; vertical-align: middle; height: 36px; font-size: 9px; border: 1px solid #1e1b4b; text-transform: uppercase;';
        $cellStyle = 'border: 1px solid #cbd5e1; text-align: center; vertical-align: middle; font-size: 9.5px; padding: 6px;';
        $zebraStyle = 'background-color: #f8fafc;';
    @endphp

    @if($isExcel)
        <table>
            <tr>
                <td colspan="24" style="font-size: 22px; font-weight: bold; color: #1e1b4b;">MAIKEL CARS, C.A.</td>
            </tr>
            <tr>
                <td colspan="12" style="font-size: 14px; font-weight: bold; color: #4338ca; text-transform: uppercase;">
                    REPORTE DE INVENTARIO CON ESTRUCTURA DE COSTOS Y FACTURACIÓN DECLARADA
                </td>
                <td colspan="12" style="text-align: right; font-size: 10px; color: #64748b;">
                    Generado el {{ date('d/m/Y') }} a las {{ date('h:i A') }}
                </td>
            </tr>
            <tr>
                <td colspan="24" style="border-bottom: 3px solid #4338ca; height: 6px;"></td>
            </tr>
            <tr>
                <td colspan="24" style="height: 10px;"></td>
            </tr>
        </table>
    @else
        <div style="margin-bottom: 15px;">
            <div style="font-size: 22px; font-weight: bold; color: #1e1b4b;">MAIKEL CARS, C.A.</div>
            <div style="font-size: 14px; font-weight: bold; color: #4338ca; text-transform: uppercase;">REPORTE DE INVENTARIO CON ESTRUCTURA DE COSTOS</div>
            <div style="font-size: 10px; color: #64748b; margin-top: 4px;">Generado el {{ date('d/m/Y') }} a las {{ date('h:i A') }}</div>
        </div>
    @endif

    <table class="data-table">
        <thead>
            <tr>
                <th style="{{ $headerStyle }}">COD. INV</th>
                <th style="{{ $headerStyle }}">TIPO</th>
                <th style="{{ $headerStyle }}">MARCA</th>
                <th style="{{ $headerStyle }}">MODELO</th>
                <th style="{{ $headerStyle }}">SERIAL</th>
                <th style="{{ $headerStyle }}">AÑO</th>
                <th style="{{ $headerStyle }}">EXPEDIENTE</th>
                <th style="{{ $headerStyle }}">CONTENEDOR</th>
                <th style="{{ $headerStyle }}">FECHA TASA BCV</th>
                <th style="{{ $headerStyle }}">TASA BCV (BS./$)</th>
                <th style="{{ $headerStyle }}">COSTO IMP. ($ USD)</th>
                <th style="{{ $headerStyle }}">COSTO IMP. (BS.)</th>
                <th style="{{ $headerStyle }}">PRORRATEO GASTOS (BS.)</th>
                <th style="{{ $headerStyle }}">COSTO TALLER (BS.)</th>
                <th style="{{ $headerStyle }}">COSTO LANDED (BS.)</th>
                <th style="{{ $headerStyle }}">% UTILIDAD</th>
                <th style="{{ $headerStyle }}">BASE IMPONIBLE B.I.G. (BS.)</th>
                <th style="{{ $headerStyle }}">BASE IMPONIBLE ($ USD)</th>
                <th style="{{ $headerStyle }}">16% IVA (BS.)</th>
                <th style="{{ $headerStyle }}">16% IVA ($ USD)</th>
                <th style="{{ $headerStyle }}">PRECIO FINAL CON IVA (BS.)</th>
                <th style="{{ $headerStyle }}">PRECIO VENTA ($ USD)</th>
                <th style="{{ $headerStyle }}">ESTATUS</th>
                <th style="{{ $headerStyle }}">OBSERVACIONES</th>
            </tr>
        </thead>
        <tbody>
            @foreach($partidas as $index => $partida)
                @php
                    $isZebra = ($index % 2 != 0);
                    $currentRowStyle = $cellStyle . ($isZebra ? $zebraStyle : '');
                    $statusColor = ($partida->status == 'VENDIDO') ? 'color: #dc2626;' : (($partida->status == 'DISPONIBLE') ? 'color: #16a34a;' : 'color: #d97706;');
                    $containerCod = $partida->container ? $partida->container->cod : '-';
                @endphp
                <tr>
                    <td style="{{ $currentRowStyle }} font-weight: bold;">{{ str_pad($partida->codInv, 4, '0', STR_PAD_LEFT) }}</td>
                    <td style="{{ $currentRowStyle }}">{{ $partida->tipo }}</td>
                    <td style="{{ $currentRowStyle }}">{{ $partida->marca }}</td>
                    <td style="{{ $currentRowStyle }} font-weight: bold;">{{ $partida->modelo }}</td>
                    <td style="{{ $currentRowStyle }}">{{ $partida->serial ?? 'S/S' }}</td>
                    <td style="{{ $currentRowStyle }}">{{ $partida->año }}</td>
                    <td style="{{ $currentRowStyle }}">{{ $partida->expediente ?? '-' }}</td>
                    <td style="{{ $currentRowStyle }}">{{ $containerCod }}</td>
                    <td style="{{ $currentRowStyle }}">{{ $partida->fecha_tasa_bcv ? \Carbon\Carbon::parse($partida->fecha_tasa_bcv)->format('d/m/Y') : '-' }}</td>
                    <td style="{{ $currentRowStyle }} font-weight: bold; color: #4338ca;">{{ number_format($partida->tasa_bcv_aplicada, 4, ',', '.') }}</td>
                    <td style="{{ $currentRowStyle }} font-weight: bold; color: #0284c7;">$ {{ number_format($partida->costo_usd_calc, 2, ',', '.') }}</td>
                    <td style="{{ $currentRowStyle }} font-weight: bold; color: #059669;">Bs. {{ number_format($partida->costo_bs_calc, 2, ',', '.') }}</td>
                    <td style="{{ $currentRowStyle }}">Bs. {{ number_format($partida->prorrateo_bs_calc, 2, ',', '.') }}</td>
                    <td style="{{ $currentRowStyle }}">Bs. {{ number_format($partida->costo_taller_bs_calc, 2, ',', '.') }}</td>
                    <td style="{{ $currentRowStyle }} font-weight: bold;">Bs. {{ number_format($partida->costo_landed_bs_calc, 2, ',', '.') }}</td>
                    <td style="{{ $currentRowStyle }} font-weight: bold;">{{ number_format($partida->utilidad_percent_calc, 2, ',', '.') }}%</td>
                    <td style="{{ $currentRowStyle }} font-weight: bold; color: #1e1b4b;">Bs. {{ number_format($partida->base_imponible_bs_calc, 2, ',', '.') }}</td>
                    <td style="{{ $currentRowStyle }}">$ {{ number_format($partida->base_imponible_usd_calc, 2, ',', '.') }}</td>
                    <td style="{{ $currentRowStyle }}">Bs. {{ number_format($partida->iva_bs_calc, 2, ',', '.') }}</td>
                    <td style="{{ $currentRowStyle }}">$ {{ number_format($partida->iva_usd_calc, 2, ',', '.') }}</td>
                    <td style="{{ $currentRowStyle }} font-weight: bold; color: #059669;">Bs. {{ number_format($partida->precio_con_iva_bs_calc, 2, ',', '.') }}</td>
                    <td style="{{ $currentRowStyle }} font-weight: bold; color: #4f46e5;">$ {{ number_format($partida->precio_venta_usd_calc, 2, ',', '.') }}</td>
                    <td style="{{ $currentRowStyle }} {{ $statusColor }} font-weight: bold;">{{ $partida->status }}</td>
                    <td style="{{ $currentRowStyle }} text-align: left;">{{ $partida->observation ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals Summary Section -->
    @php
        $summaryHeaderStyle = 'background-color: #1e1b4b; color: #ffffff; font-weight: bold; font-size: 10px; text-align: center; padding: 8px; border: 1px solid #312e81;';
        $summaryValStyle = 'background-color: #f1f5f9; font-weight: bold; font-size: 11px; text-align: center; padding: 8px; border: 1px solid #cbd5e1;';
    @endphp

    <table style="margin-top: 25px;">
        <tr>
            <td colspan="4" style="{{ $summaryHeaderStyle }}">TOTAL UNIDADES</td>
            <td colspan="5" style="{{ $summaryHeaderStyle }}">TOTAL COSTO IMP. (BS.)</td>
            <td colspan="5" style="{{ $summaryHeaderStyle }}">TOTAL COSTO LANDED (BS.)</td>
            <td colspan="5" style="{{ $summaryHeaderStyle }}">TOTAL BASE IMPONIBLE B.I.G. (BS.)</td>
            <td colspan="5" style="{{ $summaryHeaderStyle }}">TOTAL COMERCIAL ($ USD)</td>
        </tr>
        <tr>
            <td colspan="4" style="{{ $summaryValStyle }} color: #1e1b4b;">{{ $totalItems }} items</td>
            <td colspan="5" style="{{ $summaryValStyle }} color: #059669;">Bs. {{ number_format($totalImportacionBs, 2, ',', '.') }}</td>
            <td colspan="5" style="{{ $summaryValStyle }} color: #0284c7;">Bs. {{ number_format($totalLandedBs, 2, ',', '.') }}</td>
            <td colspan="5" style="{{ $summaryValStyle }} color: #312e81;">Bs. {{ number_format($totalBaseImponibleBs, 2, ',', '.') }}</td>
            <td colspan="5" style="{{ $summaryValStyle }} color: #4f46e5;">$ {{ number_format($totalPrecioVentaUsd, 2, ',', '.') }}</td>
        </tr>
    </table>
</body>

</html>
