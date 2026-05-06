<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
</head>

<body>
    <table>
        <thead>
            <tr>
                <td colspan="7"
                    style="text-align: center; font-size: 14px; font-weight: bold; color: #1E3A8A; height: 30px; vertical-align: middle;">
                    REPORTE DE MANTENIMIENTOS
                </td>
            </tr>
            <tr>
                <td colspan="7"
                    style="text-align: center; font-size: 10px; color: #6B7280; height: 20px; vertical-align: middle;">
                    Generado: {{ date('d/m/Y h:i A') }}
                </td>
            </tr>
            <tr>
                <td colspan="7" style="height: 10px;"></td>
            </tr>
            <tr>
                <th
                    style="border: 1px solid #000000; background-color: #4F46E5; color: #FFFFFF; font-weight: bold; text-align: center; padding: 5px;">
                    ID</th>
                <th
                    style="border: 1px solid #000000; background-color: #4F46E5; color: #FFFFFF; font-weight: bold; text-align: center; padding: 5px;">
                    UNIDAD</th>
                <th
                    style="border: 1px solid #000000; background-color: #4F46E5; color: #FFFFFF; font-weight: bold; text-align: center; padding: 5px;">
                    TIPO</th>
                <th
                    style="border: 1px solid #000000; background-color: #4F46E5; color: #FFFFFF; font-weight: bold; text-align: center; padding: 5px;">
                    MECÁNICO</th>
                <th
                    style="border: 1px solid #000000; background-color: #4F46E5; color: #FFFFFF; font-weight: bold; text-align: center; padding: 5px;">
                    COSTO</th>
                <th
                    style="border: 1px solid #000000; background-color: #4F46E5; color: #FFFFFF; font-weight: bold; text-align: center; padding: 5px;">
                    ESTADO</th>
                <th
                    style="border: 1px solid #000000; background-color: #4F46E5; color: #FFFFFF; font-weight: bold; text-align: center; padding: 5px;">
                    FECHA</th>
            </tr>
        </thead>
        <tbody>
            @foreach($maintenances as $index => $m)
                @php
                    $bg = ($index % 2 != 0) ? '#F3F4F6' : '#FFFFFF';
                    $statusColor = '#374151';
                    if ($m->status === 'TERMINADO')
                        $statusColor = '#059669';
                    if ($m->status === 'EN PROCESO')
                        $statusColor = '#D97706';
                    if ($m->status === 'EN ESPERA')
                        $statusColor = '#4B5563';
                @endphp
                <tr>
                    <td style="border: 1px solid #000000; text-align: center; background-color: {{ $bg }};">{{ $m->id }}
                    </td>
                    <td style="border: 1px solid #000000; background-color: {{ $bg }};">
                        {{ $m->partida ? $m->partida->marca . ' ' . $m->partida->modelo : 'N/A' }}
                    </td>
                    <td style="border: 1px solid #000000; background-color: {{ $bg }};">{{ $m->tipo }}</td>
                    <td style="border: 1px solid #000000; background-color: {{ $bg }};">{{ $m->nombre_mecanico }}
                        {{ $m->apellido_mecanico }}</td>
                    <td
                        style="border: 1px solid #000000; text-align: right; background-color: {{ $bg }}; font-family: monospace;">
                        {{ number_format($m->costo, 2) }}
                    </td>
                    <td
                        style="border: 1px solid #000000; text-align: center; font-weight: bold; color: {{ $statusColor }}; background-color: {{ $bg }};">
                        {{ $m->status }}
                    </td>
                    <td style="border: 1px solid #000000; text-align: center; background-color: {{ $bg }};">
                        {{ \Carbon\Carbon::parse($m->fecha)->format('d/m/Y') }}
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="7"
                    style="border: 1px solid #000000; background-color: #E0E7FF; color: #312E81; font-weight: bold; text-align: right; height: 25px; vertical-align: middle; padding-right: 10px;">
                    TOTAL REGISTROS: {{ count($maintenances) }}
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>