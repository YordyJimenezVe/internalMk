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
                    style="text-align: center; font-size: 14px; font-weight: bold; color: #047857; height: 30px; vertical-align: middle;">
                    REPORTE DE VENTAS
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
                    style="border: 1px solid #000000; background-color: #10B981; color: #FFFFFF; font-weight: bold; text-align: center; vertical-align: middle; height: 20px;">
                    PARTIDA</th>
                <th
                    style="border: 1px solid #000000; background-color: #10B981; color: #FFFFFF; font-weight: bold; text-align: center; vertical-align: middle; height: 20px;">
                    TIPO</th>
                <th
                    style="border: 1px solid #000000; background-color: #10B981; color: #FFFFFF; font-weight: bold; text-align: center; vertical-align: middle; height: 20px;">
                    MARCA</th>
                <th
                    style="border: 1px solid #000000; background-color: #10B981; color: #FFFFFF; font-weight: bold; text-align: center; vertical-align: middle; height: 20px;">
                    MODELO</th>
                <th
                    style="border: 1px solid #000000; background-color: #10B981; color: #FFFFFF; font-weight: bold; text-align: center; vertical-align: middle; height: 20px;">
                    FECHA</th>
                <th
                    style="border: 1px solid #000000; background-color: #10B981; color: #FFFFFF; font-weight: bold; text-align: center; vertical-align: middle; height: 20px;">
                    FACTURA</th>
                <th
                    style="border: 1px solid #000000; background-color: #10B981; color: #FFFFFF; font-weight: bold; text-align: center; vertical-align: middle; height: 20px;">
                    CONTROL</th>
                <th
                    style="border: 1px solid #000000; background-color: #10B981; color: #FFFFFF; font-weight: bold; text-align: center; vertical-align: middle; height: 20px;">
                    MONTO ($)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bills as $index => $bill)
                @php
                    $bg = '#FFFFFF';
                    if ($index % 2 != 0) {
                        $bg = '#ECFDF5'; // Light green for alternating
                    }
                @endphp
                <tr>
                    <td
                        style="border: 1px solid #000000; text-align: center; vertical-align: middle; background-color: {{ $bg }};">
                        {{ $bill->partida_id }}</td>
                    <td
                        style="border: 1px solid #000000; text-align: center; vertical-align: middle; background-color: {{ $bg }};">
                        {{ $bill->tipo }}</td>
                    <td
                        style="border: 1px solid #000000; text-align: center; vertical-align: middle; background-color: {{ $bg }};">
                        {{ $bill->marca }}</td>
                    <td
                        style="border: 1px solid #000000; text-align: center; vertical-align: middle; background-color: {{ $bg }};">
                        {{ $bill->modelo }}</td>
                    <td
                        style="border: 1px solid #000000; text-align: center; vertical-align: middle; background-color: {{ $bg }};">
                        {{ $bill->fecha }}</td>
                    <td
                        style="border: 1px solid #000000; text-align: center; vertical-align: middle; background-color: {{ $bg }};">
                        {{ $bill->numero_factura }}</td>
                    <td
                        style="border: 1px solid #000000; text-align: center; vertical-align: middle; background-color: {{ $bg }};">
                        {{ $bill->numero_control }}</td>
                    <td
                        style="border: 1px solid #000000; text-align: center; vertical-align: middle; font-weight: bold; background-color: {{ $bg }};">
                        {{ number_format($bill->divisa, 2) }}
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="7"
                    style="border: 1px solid #000000; background-color: #D1FAE5; color: #064E3B; font-weight: bold; text-align: right; height: 25px; vertical-align: middle;">
                    TOTAL VENTAS ($):
                </td>
                <td
                    style="border: 1px solid #000000; background-color: #D1FAE5; color: #064E3B; font-weight: bold; text-align: center; height: 25px; vertical-align: middle;">
                    {{ number_format($bills->sum('divisa'), 2) }}
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>