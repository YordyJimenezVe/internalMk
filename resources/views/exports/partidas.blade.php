<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 0.8cm;
            size: landscape;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            width: 100%;
            color: #1e293b;
        }

        .header-container {
            width: 100%;
            margin-bottom: 20px;
        }

        .brand-section {
            width: 100%;
            border-bottom: 2px solid #2563EB;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .brand-name {
            font-size: 28px;
            font-weight: bold;
            color: #1e293b;
            letter-spacing: 1px;
        }

        .report-title {
            font-size: 18px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 500;
        }

        .footer-stats {
            margin-top: 30px;
            width: 100%;
        }

        .stats-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
        }

        .stat-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            width: 33.33%;
        }

        .stat-label {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #2563EB;
        }

        .gen-info {
            text-align: right;
            font-size: 10px;
            color: #94a3b8;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.data-table th {
            background-color: #2563EB;
            color: #FFFFFF;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            height: 32px;
            font-size: 10px;
            border: 1px solid #1e3a8a;
            text-transform: uppercase;
        }

        table.data-table td {
            border: 1px solid #e2e8f0;
            text-align: center;
            vertical-align: middle;
            font-size: 10.5px;
            padding: 7px;
            word-wrap: break-word;
        }

        .status-vendido {
            color: #dc2626;
            font-weight: bold;
        }

        .status-disponible {
            color: #16a34a;
            font-weight: bold;
        }

        .zebra {
            background-color: #f1f5f9;
        }
    </style>
</head>

<body>
    @php
        $isExcel = $isExcel ?? false;
        $total = count($partidas);
        $disponibles = $partidas->where('status', 'DISPONIBLE')->count();
        $vendidos = $partidas->where('status', 'VENDIDO')->count();

        $headerStyle = 'background-color: #2563EB; color: #FFFFFF; font-weight: bold; text-align: center; vertical-align: middle; height: 32px; font-size: 10px; border: 1px solid #1e3a8a; text-transform: uppercase;';
        $cellStyle = 'border: 1px solid #e2e8f0; text-align: center; vertical-align: middle; font-size: 10.5px; padding: 7px;';
        $zebraStyle = 'background-color: #f1f5f9;';
    @endphp

    @if($isExcel)
        <table>
            <tr>
                <td colspan="10" style="font-size: 24px; font-weight: bold; color: #1e293b;">MAIKEL CARS</td>
            </tr>
            <tr>
                <td colspan="4" rowspan="2" style="font-size: 16px; color: #64748b; text-transform: uppercase; vertical-align: middle;">Reporte de Inventario</td>
                <td colspan="6" style="text-align: right; font-size: 10px; color: #94a3b8;">
                    Generado el {{ date('d/m/Y') }} a las {{ date('h:i A') }}
                </td>
            </tr>
            <tr>
                <td colspan="6" style="text-align: right; font-size: 10px; color: #94a3b8;">
                    Sistema de Gestión Interna
                </td>
            </tr>
            <tr>
                <td colspan="10" style="border-bottom: 2px solid #2563EB; height: 5px;"></td>
            </tr>
            <tr>
                <td colspan="10" style="height: 10px;"></td>
            </tr>
        </table>
    @else
        <div class="header-container">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="text-align: left; border: none; padding: 0;">
                        <div class="brand-name">MAIKEL CARS</div>
                        <div class="report-title">Reporte de Inventario</div>
                    </td>
                    <td style="text-align: right; border: none; padding: 0; vertical-align: bottom;">
                        <div class="gen-info">
                            Generado el {{ date('d/m/Y') }} a las {{ date('h:i A') }}<br>
                            Sistema de Gestión Interna
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="brand-section"></div>
    @endif

    <table class="data-table" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="{{ $headerStyle }} width: 10%;">TIPO</th>
                <th style="{{ $headerStyle }} width: 10%;">MARCA</th>
                <th style="{{ $headerStyle }} width: 15%;">MODELO</th>
                <th style="{{ $headerStyle }} width: 10%;">SERIAL</th>
                <th style="{{ $headerStyle }} width: 6%;">AÑO</th>
                <th style="{{ $headerStyle }} width: 8%;">COD. INV</th>
                <th style="{{ $headerStyle }} width: 8%;">EXPEDIENTE</th>
                <th style="{{ $headerStyle }} width: 10%;">ESTATUS</th>
                <th style="{{ $headerStyle }} width: 10%;">F. VENTA</th>
                <th style="{{ $headerStyle }} width: 13%;">NRO. FACTURA</th>
            </tr>
        </thead>
        <tbody>
            @foreach($partidas as $index => $partida)
                @php
                    $isZebra = ($index % 2 != 0);
                    $currentRowStyle = $cellStyle . ($isZebra ? $zebraStyle : '');

                    $statusColor = ($partida->status == 'VENDIDO') ? 'color: #dc2626;' : 'color: #16a34a;';

                    $bill = $partida->bill ? $partida->bill->first() : null;
                    
                    $fechaVenta = '-';
                    if ($bill && $bill->fecha) {
                        try {
                            $fechaVenta = \Carbon\Carbon::parse($bill->fecha)->format('d/m/Y');
                        } catch (\Exception $e) {
                            $fechaVenta = $bill->fecha;
                        }
                    }
                    
                    $nroFactura = $bill ? $bill->numero_factura : '-';
                @endphp
                <tr style="{{ $isZebra ? $zebraStyle : '' }}">
                    <td style="{{ $currentRowStyle }}">{{ $partida->tipo }}</td>
                    <td style="{{ $currentRowStyle }}">{{ $partida->marca }}</td>
                    <td style="{{ $currentRowStyle }}">{{ $partida->modelo }}</td>
                    <td style="{{ $currentRowStyle }}">{{ $partida->serial }}</td>
                    <td style="{{ $currentRowStyle }}">{{ $partida->año }}</td>
                    <td style="{{ $currentRowStyle }}">{{ $partida->codInv }}</td>
                    <td style="{{ $currentRowStyle }}">{{ $partida->expediente }}</td>
                    <td style="{{ $currentRowStyle }} {{ $statusColor }} font-weight: bold;">{{ $partida->status }}</td>
                    <td style="{{ $currentRowStyle }}">{{ $fechaVenta }}</td>
                    <td style="{{ $currentRowStyle }} font-weight: bold;">{{ $nroFactura }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($isExcel)
        @php
            $statCardStyle = 'background-color: #2563EB; border: 1px solid #1e3a8a; padding: 10px; text-align: center; color: #FFFFFF; font-weight: bold; font-size: 11px;';
        @endphp
        <table style="margin-top: 20px;">
            <tr>
                <td colspan="3" style="{{ $statCardStyle }}">
                    TOTAL UNIDADES: {{ $total }}
                </td>
                <td colspan="3" style="{{ $statCardStyle }}">
                    DISPONIBLES: {{ $disponibles }}
                </td>
                <td colspan="4" style="{{ $statCardStyle }}">
                    VENDIDOS: {{ $vendidos }}
                </td>
            </tr>
        </table>
    @else
        <div class="footer-stats">
            <table class="stats-table">
                <tr>
                    <td class="stat-card" style="{{ $headerStyle }}">
                        <div class="stat-label">Total Unidades:</div>
                        <div class="stat-value" style="color: #FFFFFF;">{{ $total }}</div>
                    </td>
                    <td class="stat-card" style="{{ $headerStyle }}">
                        <div class="stat-label">Disponibles:</div>
                        <div class="stat-value" style="color: #16a34a; background-color: #f8fafc; border-radius: 4px; padding: 2px;">{{ $disponibles }}</div>
                    </td>
                    <td class="stat-card" style="{{ $headerStyle }}">
                        <div class="stat-label">Vendidos:</div>
                        <div class="stat-value" style="color: #dc2626; background-color: #f8fafc; border-radius: 4px; padding: 2px;">{{ $vendidos }}</div>
                    </td>
                </tr>
            </table>
        </div>
    @endif
</body>

</html>