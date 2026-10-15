<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Nota de Entrega</title>
</head>

<body style="font-family: Arial, sans-serif; font-size: 12px; color: #333;">

    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <h1 style="color: #1E3A8A; margin-bottom: 5px;">Maikel Cars</h1>
                <p style="margin: 0; color: #555;">Venta de Motores, Cajas y Autopartes</p>
                <p style="margin: 0; color: #555;">RIF: J-50029302-3</p>
                <p style="margin: 0; color: #555;">Dirección: Calle 51 con Av. Libertador, frente al CC Babilon</p>
            </td>
            <td style="width: 40%; vertical-align: top; text-align: right;">
                <h2 style="color: #DC2626; margin: 0;">NOTA DE ENTREGA</h2>
                <p style="font-size: 14px; font-weight: bold; margin: 5px 0;">N°:
                    {{ str_pad($bill->id, 6, '0', STR_PAD_LEFT) }}
                </p>
                <br>
                <p><strong>Fecha:</strong> {{ date('d/m/Y', strtotime($bill->fecha)) }}</p>
                <p><strong>Hora:</strong> {{ date('h:i A', strtotime($bill->hora)) }}</p>
            </td>
        </tr>
    </table>

    <div style="margin-top: 30px; border: 1px solid #ccc; padding: 10px; background-color: #F3F4F6;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 15%; font-weight: bold;">Cliente:</td>
                <td style="width: 85%;">{{ $bill->client_name ?? 'CLIENTE GENÉRICO' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Cédula/RIF:</td>
                <td>{{ $bill->client_cedula ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Teléfono:</td>
                <td>{{ $bill->client_phone ?? 'N/A' }}</td>
            </tr>
            @if(!empty($bill->client_email))
            <tr>
                <td style="font-weight: bold;">Correo:</td>
                <td>{{ $bill->client_email }}</td>
            </tr>
            @endif
            <tr>
                <td style="font-weight: bold;">Dirección:</td>
                <td>{{ $bill->client_address ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <table style="width: 100%; margin-top: 30px; border-collapse: collapse; border: 1px solid #000;">
        <thead>
            <tr style="background-color: #1E3A8A; color: white;">
                <th style="padding: 10px; text-align: center; border: 1px solid #000;">CANT</th>
                <th style="padding: 10px; text-align: left; border: 1px solid #000;">DESCRIPCIÓN</th>
                <th style="padding: 10px; text-align: right; border: 1px solid #000;">PRECIO UNIT ($)</th>
                <th style="padding: 10px; text-align: right; border: 1px solid #000;">TOTAL ($)</th>
            </tr>
        </thead>
        <tbody>
            @if($bill->partida)
                @if($bill->is_pair_camera || !empty($bill->second_partida_id) || str_contains(strtoupper($bill->observaciones ?? ''), 'PAR DE CÁMARAS') || str_contains(strtoupper($bill->observaciones ?? ''), 'PAR DE CAMARAS'))
                    <tr>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd; font-weight: bold;">
                            1 (PAR)
                        </td>
                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <strong style="color: #1E3A8A; font-size: 13px;">PAR DE CÁMARAS (JUEGO LADO DERECHO + IZQUIERDO)</strong>
                            <br>
                            <strong style="font-size: 12px;">{{ $bill->partida->marca }} {{ $bill->partida->modelo }}</strong>
                            <div style="margin-top: 6px; padding: 6px 8px; background-color: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 4px; font-size: 10px; color: #334155; line-height: 1.5;">
                                <div>• <strong>Cámara 1:</strong> [Cod: {{ str_pad($bill->partida->codInv, 4, '0', STR_PAD_LEFT) }}] Lado {{ $bill->partida->camera_side_label ?? 'DERECHO' }} ({{ $bill->partida->camera_side ?? 'R' }}) @if($bill->partida->container) - Exp: {{ $bill->partida->container->expediente }} @endif</div>
                                @if($bill->secondPartida)
                                    <div>• <strong>Cámara 2:</strong> [Cod: {{ str_pad($bill->secondPartida->codInv, 4, '0', STR_PAD_LEFT) }}] Lado {{ $bill->secondPartida->camera_side_label ?? 'IZQUIERDO' }} ({{ $bill->secondPartida->camera_side ?? 'L' }}) @if($bill->secondPartida->container) - Exp: {{ $bill->secondPartida->container->expediente }} @endif</div>
                                @endif
                                @if(!empty($bill->observaciones))
                                    <div style="margin-top: 4px; color: #64748B; font-style: italic;">Detalles: {{ $bill->observaciones }}</div>
                                @endif
                            </div>
                        </td>
                        <td style="padding: 10px; text-align: right; border: 1px solid #ddd; font-weight: bold;">
                            {{ number_format($bill->divisa, 2) }}
                        </td>
                        <td style="padding: 10px; text-align: right; border: 1px solid #ddd; font-weight: bold;">
                            {{ number_format($bill->divisa, 2) }}
                        </td>
                    </tr>
                @else
                    <tr>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">1</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <strong>{{ $bill->partida->marca }} {{ $bill->partida->modelo }}</strong>
                            <br>
                            <span style="font-size: 10px; color: #666;">
                                Tipo: {{ $bill->tipo_item ?? $bill->partida->tipo }} | Año: {{ $bill->partida->año }} | Cod: {{ str_pad($bill->partida->codInv, 4, '0', STR_PAD_LEFT) }}
                                @if($bill->partida->camera_side)
                                    | Lado: {{ $bill->partida->camera_side_label }} ({{ $bill->partida->camera_side }})
                                @endif
                            </span>
                            @if(!empty($bill->observaciones))
                                <br><span style="font-size: 9px; color: #888; font-style: italic;">Detalles: {{ $bill->observaciones }}</span>
                            @endif
                        </td>
                        <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">
                            {{ number_format($bill->divisa, 2) }}
                        </td>
                        <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">
                            {{ number_format($bill->divisa, 2) }}
                        </td>
                    </tr>
                @endif
            @else
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px;">Sin detalles de partida asociados</td>
                </tr>
            @endif
        </tbody>
    </table>

    <table style="width: 100%; margin-top: 20px;">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr style="background-color: #10B981; color: white;">
                        <td style="padding: 10px; font-weight: bold; border: 1px solid #000;">TOTAL A PAGAR:</td>
                        <td style="padding: 10px; text-align: right; font-weight: bold; border: 1px solid #000;">
                            $ {{ number_format($bill->divisa, 2) }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div style="position: fixed; bottom: 50px; width: 100%; text-align: center; font-size: 10px; color: #999;">
        <p>Gracias por su compra</p>
        <p>Documento generado electrónicamente por Sistema Internal Maikel Cars</p>
    </div>

</body>

</html>