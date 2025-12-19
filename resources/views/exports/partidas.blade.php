<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
</head>

<body>
    <table>
        <thead>
            <tr>
                <td colspan="8"
                    style="text-align: center; font-size: 14px; font-weight: bold; color: #1E3A8A; height: 30px; vertical-align: middle;">
                    REPORTE DE INVENTARIO
                </td>
            </tr>
            <tr>
                <td colspan="8"
                    style="text-align: center; font-size: 10px; color: #6B7280; height: 20px; vertical-align: middle;">
                    Generado: {{ date('d/m/Y h:i A') }}
                </td>
            </tr>
            <tr>
                <td colspan="8" style="height: 10px;"></td>
            </tr>
            <tr>
                <th
                    style="border: 1px solid #000000; background-color: #2563EB; color: #FFFFFF; font-weight: bold; text-align: center; vertical-align: middle; height: 20px;">
                    TIPO</th>
                <th
                    style="border: 1px solid #000000; background-color: #2563EB; color: #FFFFFF; font-weight: bold; text-align: center; vertical-align: middle; height: 20px;">
                    MARCA</th>
                <th
                    style="border: 1px solid #000000; background-color: #2563EB; color: #FFFFFF; font-weight: bold; text-align: center; vertical-align: middle; height: 20px;">
                    MODELO</th>
                <th
                    style="border: 1px solid #000000; background-color: #2563EB; color: #FFFFFF; font-weight: bold; text-align: center; vertical-align: middle; height: 20px;">
                    AÑO</th>
                <th
                    style="border: 1px solid #000000; background-color: #2563EB; color: #FFFFFF; font-weight: bold; text-align: center; vertical-align: middle; height: 20px;">
                    COD. INV</th>
                <th
                    style="border: 1px solid #000000; background-color: #2563EB; color: #FFFFFF; font-weight: bold; text-align: center; vertical-align: middle; height: 20px;">
                    EXPEDIENTE</th>
                <th
                    style="border: 1px solid #000000; background-color: #2563EB; color: #FFFFFF; font-weight: bold; text-align: center; vertical-align: middle; height: 20px;">
                    ESTATUS</th>
                <th
                    style="border: 1px solid #000000; background-color: #2563EB; color: #FFFFFF; font-weight: bold; text-align: center; vertical-align: middle; height: 20px;">
                    FECHA REGISTRO</th>
            </tr>
        </thead>
        <tbody>
            @foreach($partidas as $index => $partida)
                @php
                    $bg = '#FFFFFF';
                    if ($index % 2 != 0) {
                        $bg = '#EFF6FF'; // Light blue for alternating
                    }
                    $statusColor = ($partida->status == 'VENDIDO') ? '#DC2626' : '#059669';
                @endphp
                <tr>
                    <td
                        style="border: 1px solid #000000; text-align: center; vertical-align: middle; background-color: {{ $bg }};">
                        {{ $partida->tipo }}</td>
                    <td
                        style="border: 1px solid #000000; text-align: center; vertical-align: middle; background-color: {{ $bg }};">
                        {{ $partida->marca }}</td>
                    <td
                        style="border: 1px solid #000000; text-align: center; vertical-align: middle; background-color: {{ $bg }};">
                        {{ $partida->modelo }}</td>
                    <td
                        style="border: 1px solid #000000; text-align: center; vertical-align: middle; background-color: {{ $bg }};">
                        {{ $partida->año }}</td>
                    <td
                        style="border: 1px solid #000000; text-align: center; vertical-align: middle; background-color: {{ $bg }};">
                        {{ $partida->codInv }}</td>
                    <td
                        style="border: 1px solid #000000; text-align: center; vertical-align: middle; background-color: {{ $bg }};">
                        {{ $partida->expediente }}</td>
                    <td
                        style="border: 1px solid #000000; text-align: center; vertical-align: middle; font-weight: bold; color: {{ $statusColor }}; background-color: {{ $bg }};">
                        {{ $partida->status }}
                    </td>
                    <td
                        style="border: 1px solid #000000; text-align: center; vertical-align: middle; background-color: {{ $bg }};">
                        {{ $partida->fecha_creacion }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="8"
                    style="border: 1px solid #000000; background-color: #DBEAFE; color: #1E3A8A; font-weight: bold; text-align: right; height: 25px; vertical-align: middle;">
                    TOTAL REGISTROS: {{ count($partidas) }}
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>