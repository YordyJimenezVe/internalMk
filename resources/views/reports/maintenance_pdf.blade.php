<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ficha Técnica de Mantenimiento #{{ $maintenance->id }}</title>
    <style>
        @page {
            margin: 1cm 1.5cm 2cm 1.5cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #334155;
            padding-bottom: 50px;
        }

        .header {
            background-color: #1e293b;
            color: white;
            padding: 30px;
        }

        .header table {
            width: 100%;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .header p {
            margin: 5px 0 0;
            opacity: 0.8;
            font-size: 12px;
        }

        .badge {
            background: #3b82f6;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
        }

        .content {
            padding: 30px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e293b;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 5px;
            margin-bottom: 15px;
            margin-top: 25px;
            text-transform: uppercase;
        }

        .info-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .info-grid td {
            padding: 10px;
            border: 1px solid #f1f5f9;
            font-size: 12px;
            width: 50%;
        }

        .label {
            font-weight: bold;
            color: #64748b;
            margin-bottom: 3px;
            display: block;
        }

        .value {
            color: #1e293b;
            font-size: 13px;
            font-weight: 500;
        }

        .description-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 15px;
            border-radius: 8px;
            font-size: 12px;
            line-height: 1.5;
            color: #475569;
            margin-top: 10px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background: #f1f5f9;
            padding: 15px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }

        .stats-grid {
            width: 100%;
            margin-top: 10px;
        }

        .stat-card {
            border: 1px solid #e2e8f0;
            padding: 8px;
            border-radius: 4px;
            text-align: center;
        }

        .stat-label {
            font-size: 9px;
            color: #64748b;
            font-weight: bold;
        }

        .stat-value {
            font-size: 12px;
            color: #3b82f6;
            font-weight: bold;
        }

        .car-image {
            width: 100%;
            height: 150px;
            background-color: #f1f5f9;
            border-radius: 8px;
            margin-top: 10px;
            text-align: center;
            overflow: hidden;
        }

        .car-image img {
            height: 100%;
            width: auto;
        }
    </style>
</head>

<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    <h1>Ficha Técnica de Mantenimiento</h1>
                    <p>Internal Maikel Cars - Sistema de Gestión de Inventario</p>
                </td>
                <td style="text-align: right;">
                    <span class="badge">ORDEN #{{ str_pad($maintenance->id, 5, '0', STR_PAD_LEFT) }}</span>
                    <p style="margin-top: 10px;">Fecha: {{ date('d/m/Y', strtotime($maintenance->fecha)) }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="content">
        <table style="width: 100%;">
            <tr>
                <td style="width: 65%; vertical-align: top; padding-right: 20px;">
                    <div class="section-title">Datos de la Unidad</div>
                    <table class="info-grid">
                        <tr>
                            <td><span class="label">Marca / Modelo:</span> <span
                                    class="value">{{ $partida->marca ?? 'N/A' }} {{ $partida->modelo ?? '' }}</span>
                            </td>
                            <td><span class="label">Año / Expediente:</span> <span
                                    class="value">{{ $partida->año ?? 'N/A' }} /
                                    {{ $partida->expediente ?? 'N/A' }}</span></td>
                        </tr>
                        <tr>
                            <td><span class="label">Código Inventario:</span> <span
                                    class="value">{{ $partida->codInv ?? 'N/A' }}</span></td>
                            <td><span class="label">Tipo Unidad:</span> <span
                                    class="value">{{ $partida->tipo ?? 'N/A' }}</span></td>
                        </tr>
                    </table>

                    <div class="section-title">Información del Trabajo</div>
                    <table class="info-grid">
                        <tr>
                            <td><span class="label">Tipo Mantenimiento:</span> <span
                                    class="value">{{ $maintenance->tipo }}</span></td>
                            <td><span class="label">Estado Final:</span> <span
                                    class="value">
                                    @php
                                        $statusDisplay = $maintenance->status;
                                        if ($maintenance->status === 'EN ESPERA') $statusDisplay = 'RECIBIDO';
                                        if ($maintenance->status === 'EN PROCESO') $statusDisplay = 'ARMANDO';
                                    @endphp
                                    {{ $statusDisplay }}
                                </span></td>
                        </tr>
                        <tr>
                            <td colspan="2"><span class="label">Mecánico Responsable:</span> <span
                                    class="value">{{ $maintenance->nombre_mecanico }}
                                    {{ $maintenance->apellido_mecanico }} (C.I.
                                    {{ $maintenance->cedula_mecanico }})</span></td>
                        </tr>
                    </table>
                </td>
                <td style="width: 35%; vertical-align: top;">
                    <div class="section-title">Vehículo Referencial</div>
                    <!-- Aquí se cargará la imagen generada o una genérica -->
                    <div class="car-image">
                        @if($vehicleImage)
                            <img src="{{ $vehicleImage }}" alt="Vehículo">
                        @else
                            <img src="https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&q=80&w=400"
                                alt="Vehículo">
                        @endif
                    </div>
                    <div style="font-size: 9px; color: #94a3b8; text-align: center; margin-top: 5px;">*Imagen
                        referencial basada en la unidad</div>
                </td>
            </tr>
        </table>

        <div class="section-title">Descripción del Trabajo</div>
        <div class="description-box">
            {{ $maintenance->descripcion }}
        </div>

        @if($bill)
            @php
                $user = auth()->user();
                $isAdmin = $user && ($user->hasAnyRole(['Superusuario', 'Administrador', 'SUPERUSUARIO', 'ADMINISTRADOR']) || in_array($user->rol, ['Superusuario', 'Administrador']));

                $stats = [];
                if (floatval($bill->consumables) > 0) {
                    $stats[] = ['label' => 'LIMPIEZA, CONSUMIBLES Y MONTACARGA', 'value' => $bill->consumables, 'isSpecial' => true];
                }

                if (floatval($bill->multi_tools) > 0)
                    $stats[] = ['label' => 'HERRAMIENTAS', 'value' => $bill->multi_tools];

                if ($isAdmin) {
                    if (floatval($bill->mechanic) > 0)
                        $stats[] = ['label' => 'MECÁNICO', 'value' => $bill->mechanic];
                    if (floatval($bill->mechanic_assistant) > 0)
                        $stats[] = ['label' => 'AYUDANTE M.', 'value' => $bill->mechanic_assistant];
                    if (floatval($bill->seller) > 0)
                        $stats[] = ['label' => 'VENDEDOR', 'value' => $bill->seller];
                    if (floatval($bill->seller_assistant) > 0)
                        $stats[] = ['label' => 'AYUDANTE V.', 'value' => $bill->seller_assistant];
                    if (floatval($bill->camera_technician) > 0)
                        $stats[] = ['label' => 'TÉC. CÁMARAS', 'value' => $bill->camera_technician];
                    if (floatval($bill->camera_technical_assistant) > 0)
                        $stats[] = ['label' => 'AYUDANTE C.', 'value' => $bill->camera_technical_assistant];
                }
            @endphp

            @if(count($stats) > 0)
                <div class="section-title">Participación y Mano de Obra (%)</div>
                <table style="width: 100%; border-collapse: separate; border-spacing: 5px;">
                    @foreach(array_chunk($stats, 4) as $chunk)
                        <tr>
                            @foreach($chunk as $stat)
                                <td class="stat-card"
                                    style="{{ isset($stat['isSpecial']) ? 'background-color: #fefce8; border-color: #fef08a;' : '' }} width: 25%;">
                                    <div class="stat-label" style="{{ isset($stat['isSpecial']) ? 'color: #a16207;' : '' }}">
                                        {{ $stat['label'] }}</div>
                                    <div class="stat-value"
                                        style="{{ isset($stat['isSpecial']) ? 'color: #a16207; font-size: 16px;' : '' }}">
                                        {{ $stat['value'] }}%</div>
                                </td>
                            @endforeach
                            @for($i = count($chunk); $i < 4; $i++)
                                <td style="width: 25%;"></td>
                            @endfor
                        </tr>
                    @endforeach
                </table>
            @endif
        @endif

        @if($accesorios)
            @php
                $accList = [];
                if (floatval($accesorios->valve_cover) > 0)
                    $accList[] = ['label' => 'Tapa Válvula', 'value' => $accesorios->valve_cover];
                if (floatval($accesorios->chain_cover) > 0)
                    $accList[] = ['label' => 'Tapa Cadena', 'value' => $accesorios->chain_cover];
                if (floatval($accesorios->carter) > 0)
                    $accList[] = ['label' => 'Carter', 'value' => $accesorios->carter];
                if (floatval($accesorios->pescador) > 0)
                    $accList[] = ['label' => 'Pescador', 'value' => $accesorios->pescador];
            @endphp

            @if(count($accList) > 0)
                <div class="section-title">Accesorios Instalados</div>
                <table class="info-grid">
                    @foreach(array_chunk($accList, 2) as $chunk)
                        <tr>
                            @foreach($chunk as $acc)
                                <td>
                                    <span class="label">{{ $acc['label'] }}:</span>
                                    <span class="value">$ {{ number_format(floatval($acc['value']), 2) }}</span>
                                </td>
                            @endforeach
                            @if(count($chunk) == 1)
                                <td></td>
                            @endif
                        </tr>
                    @endforeach
                </table>
            @endif
        @endif
    </div>

    <div class="footer">
        <p>Este documento es una ficha técnica informativa generada por el Sistema de Gestión Internal Maikel Cars.</p>
        <p>&copy; {{ date('Y') }} Maikel Cars. Todos los derechos reservados.</p>
    </div>

</body>

</html>