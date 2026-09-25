<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1e293b;
        }
        .header-title {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
        }
        .header-subtitle {
            font-size: 11px;
            color: #64748b;
        }
        th.col-header {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            text-align: center;
            vertical-align: middle;
            border: 1px solid #0f172a;
            height: 28px;
        }
        td.cell {
            border: 1px solid #cbd5e1;
            font-size: 10px;
            vertical-align: middle;
            height: 24px;
        }
        td.cell-center {
            text-align: center;
        }
        td.cell-right {
            text-align: right;
        }
        .zebra {
            background-color: #f8fafc;
        }
        .instructions-title {
            font-size: 11px;
            font-weight: bold;
            color: #0369a1;
            background-color: #e0f2fe;
            border: 1px solid #bae6fd;
            padding: 6px;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="13" class="header-title" style="font-size: 18px; font-weight: bold; color: #1e3a8a; height: 30px;">
                INTERNAL MAIKEL CARS, C.A.
            </td>
        </tr>
        <tr>
            <td colspan="8" class="header-subtitle" style="font-size: 12px; font-weight: bold; color: #475569;">
                FORMATO DE REGISTRO Y CONTEO DE INVENTARIO MANUAL
            </td>
            <td colspan="5" style="text-align: right; font-size: 10px; color: #64748b;">
                FECHA DE IMPRESIÓN / REGISTRO: ____ / ____ / ________
            </td>
        </tr>
        <tr>
            <td colspan="13" style="height: 10px;"></td>
        </tr>
        <tr>
            <td colspan="13" class="instructions-title" style="background-color: #e0f2fe; color: #0369a1; font-weight: bold; font-size: 10px; border: 1px solid #bae6fd;">
                INSTRUCCIONES: Complete los datos solicitados por cada pieza en existencia física. En TIPO use: MOTOR 7/8, MOTOR COMPLETO, CAJA, CÁMARA o AUTOPARTE.
            </td>
        </tr>
        <tr>
            <td colspan="13" style="height: 8px;"></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th class="col-header" style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #0f172a; width: 12px;">N°</th>
                <th class="col-header" style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #0f172a; width: 15px;">COD. INVENTARIO</th>
                <th class="col-header" style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #0f172a; width: 16px;">EXPEDIENTE / CONTENEDOR</th>
                <th class="col-header" style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #0f172a; width: 18px;">TIPO DE PRODUCTO</th>
                <th class="col-header" style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #0f172a; width: 16px;">MARCA</th>
                <th class="col-header" style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #0f172a; width: 22px;">MODELO / ESPECIFICACIÓN</th>
                <th class="col-header" style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #0f172a; width: 10px;">AÑO</th>
                <th class="col-header" style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #0f172a; width: 20px;">SERIAL DE PIEZA</th>
                <th class="col-header" style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #0f172a; width: 12px;">CANTIDAD</th>
                <th class="col-header" style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #0f172a; width: 16px;">CONDICIÓN / ESTADO</th>
                <th class="col-header" style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #0f172a; width: 14px;">COSTO ($)</th>
                <th class="col-header" style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #0f172a; width: 16px;">UBICACIÓN ALMACÉN</th>
                <th class="col-header" style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #0f172a; width: 28px;">OBSERVACIONES / DETALLES</th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 1; $i <= 60; $i++)
                @php
                    $isZebra = ($i % 2 === 0);
                    $rowBg = $isZebra ? 'background-color: #f8fafc;' : 'background-color: #ffffff;';
                @endphp
                <tr>
                    <td style="{{ $rowBg }} border: 1px solid #cbd5e1; text-align: center; font-weight: bold; color: #64748b; height: 22px;">{{ $i }}</td>
                    <td style="{{ $rowBg }} border: 1px solid #cbd5e1; text-align: center;"></td>
                    <td style="{{ $rowBg }} border: 1px solid #cbd5e1; text-align: center;"></td>
                    <td style="{{ $rowBg }} border: 1px solid #cbd5e1; text-align: center;"></td>
                    <td style="{{ $rowBg }} border: 1px solid #cbd5e1; text-align: center;"></td>
                    <td style="{{ $rowBg }} border: 1px solid #cbd5e1;"></td>
                    <td style="{{ $rowBg }} border: 1px solid #cbd5e1; text-align: center;"></td>
                    <td style="{{ $rowBg }} border: 1px solid #cbd5e1; text-align: center;"></td>
                    <td style="{{ $rowBg }} border: 1px solid #cbd5e1; text-align: center;">1</td>
                    <td style="{{ $rowBg }} border: 1px solid #cbd5e1; text-align: center;">DISPONIBLE</td>
                    <td style="{{ $rowBg }} border: 1px solid #cbd5e1; text-align: right;"></td>
                    <td style="{{ $rowBg }} border: 1px solid #cbd5e1; text-align: center;"></td>
                    <td style="{{ $rowBg }} border: 1px solid #cbd5e1;"></td>
                </tr>
            @endfor
        </tbody>
    </table>
</body>
</html>
