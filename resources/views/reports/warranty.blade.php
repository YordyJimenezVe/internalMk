<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Póliza de Garantía - Maikel Cars</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9.5px;
            color: #222;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .logo-title {
            color: #1E3A8A;
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }
        .company-info {
            color: #555;
            font-size: 8.5px;
            margin: 1px 0 0 0;
        }
        .doc-title {
            color: #DC2626;
            font-size: 14px;
            font-weight: bold;
            text-align: right;
            margin: 0;
            letter-spacing: 0.5px;
        }
        .warranty-dates {
            background-color: #F9FAFB;
            border: 1px solid #E5E7EB;
            padding: 6px 10px;
            margin-bottom: 10px;
            border-radius: 6px;
        }
        .dates-table {
            width: 100%;
            border-collapse: collapse;
        }
        .dates-table td {
            font-size: 10px;
        }
        .section-title {
            background-color: #1E3A8A;
            color: #FFFFFF;
            font-size: 9px;
            font-weight: bold;
            padding: 3px 6px;
            margin-top: 8px;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 3px;
        }
        .content-block {
            margin-bottom: 6px;
            text-align: justify;
        }
        .list-style {
            margin: 0;
            padding-left: 15px;
        }
        .list-style li {
            margin-bottom: 1px;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
        .footer-table td {
            vertical-align: top;
            font-size: 9px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 3px 2px;
        }
        .info-label {
            font-weight: bold;
            color: #4B5563;
            width: 95px;
        }
        .info-value {
            border-bottom: 1px dotted #9CA3AF;
            padding-left: 4px;
            font-weight: bold;
        }
        .no-serial {
            color: #DC2626;
            font-weight: bold;
        }
        .signature-container {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .signature-container td {
            width: 50%;
            padding: 5px;
        }
        .signature-box {
            border: 1px dashed #9CA3AF;
            height: 55px;
            border-radius: 4px;
            background-color: #F9FAFB;
        }
        .signature-label {
            text-align: center;
            font-weight: bold;
            margin-bottom: 3px;
            font-size: 8.5px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    @php
        $fechaInicio = date('d/m/Y', strtotime($bill->fecha));
        $fechaFin = date('d/m/Y', strtotime($bill->fecha . ' + 90 days'));
        
        $serial = trim($bill->partida->serial ?? '');
        $hasNoSerial = empty($serial) || in_array(strtoupper($serial), ['S/N', 'SIN SERIAL', 'NO POSEE', 'N/A']);
        if ($hasNoSerial) {
            $serialDisplay = 'SIN SERIAL / NO POSEE';
        } else {
            $serialDisplay = strtoupper($serial);
        }
    @endphp

    <table class="header-table">
        <tr>
            <td style="width: 55%; vertical-align: top;">
                <h1 class="logo-title">Maikel Cars, C.A.</h1>
                <p class="company-info">Venta de Motores, Cajas y Autopartes</p>
                <p class="company-info">RIF: J-50029302-3</p>
                <p class="company-info">Calle 51 con Av. Libertador, frente al CC Babilon</p>
            </td>
            <td style="width: 45%; vertical-align: top; text-align: right;">
                <h2 class="doc-title">PÓLIZA DE GARANTÍA</h2>
                <p style="margin: 2px 0 0 0; font-size: 9px; color: #555;">N° de Factura: <strong>{{ str_pad($bill->id, 6, '0', STR_PAD_LEFT) }}</strong></p>
            </td>
        </tr>
    </table>

    <div class="warranty-dates">
        <table class="dates-table">
            <tr>
                <td style="width: 38%;"><strong>FECHA DE COMPRA (INICIO):</strong></td>
                <td style="width: 14%; color: #1E3A8A; font-weight: bold;">{{ $fechaInicio }}</td>
                <td style="width: 34%;"><strong>FECHA DE VENCIMIENTO (LÍMITE):</strong></td>
                <td style="width: 14%; color: #DC2626; font-weight: bold;">{{ $fechaFin }}</td>
            </tr>
        </table>
    </div>

    <div class="content-block">
        <strong>MAIKEL CARS. C.A</strong> GARANTIZA EL MOTOR EN SUS INSTALACIONES CONTRA DEFECTOS EN MATERIALES O EN MANO DE OBRA, SIEMPRE Y CUANDO SE INSTALE CORRECTAMENTE, Y SE LE DE USO NORMAL Y SE LE DE UN MANTENIMIENTO ADECUADO DURANTE EL PERIODO DE GARANTIA. SI EL MOTOR FALLARA COMO RESULTADO DE UN DEFECTO DURANTE EL PERIODO DE GARANTIA ESPECIFICADO QUE INICIA DESDE LA FECHA DE COMPRAVENTA, ESTE COMPROMISO APLICA A: <strong>AUTOMÓVILES (90 DÍAS PARA MOTOR)</strong>. GARANTÍA EFECTIVA PARA EVALUAR LOS COMPONENTES Y VERIFICAR LA FALLA, REPARAR O EN SU DEFECTO REEMPLAZAR.
    </div>

    <div class="section-title">Procedimiento de la Garantía de Motor</div>
    <div class="content-block">
        INMEDIATAMENTE QUE DESCUBRAS UN DEFECTO POTENCIAL Y ANTES DE HACER CUALQUIER PROCEDIMIENTO USTED DEBE LLAMAR AL DEPARTAMENTO DE SERVICIO AL CLIENTE <strong>(0424-5213994)</strong>, ENVIAR FOTO DE LA FACTURA Y GARANTÍA MEDIANTE WHATSAPP, ENVIAR FOTO Y VIDEO DE LA FALLA QUE ESTÁ PRESENTANDO. SE LE BRINDARÁ UN ASESORAMIENTO TÉCNICO PARA RESOLVER LA FALLA, SU TÉCNICO DEBE ESTAR DISPUESTO A BRINDAR SU COLABORACIÓN, PARA SER EFECTIVOS EN LA INSTALACIÓN Y EL FUNCIONAMIENTO.
    </div>

    <div class="section-title">La Garantía NO Aplica Cuando:</div>
    <ol class="list-style">
        <li>Cuando no presente factura y garantía como antes mencionado.</li>
        <li>Se instale en vehículos de carreras o se modifique para un alto desempeño.</li>
        <li>Se manipule, destape o abuse, se instale de manera incorrecta, si sufre daños por accidentes, fuego, negligencia, mal uso, sobrecalentamiento, si se utilizan componentes defectuosos o incorrectos que se conecten al motor, o si se instalan accesorios no autorizados por el fabricante.</li>
        <li>Si se instala el motor en un vehículo para el cual no ha sido diseñado.</li>
        <li>Colocación de aceite no recomendado o combustible no adecuado.</li>
        <li>Que el motor no esté contaminado por dentro con partículas o cosas extrañas en la culata.</li>
    </ol>

    <div class="section-title">La Garantía NO Incluye:</div>
    <ol class="list-style">
        <li>Cancelación de transporte, gastos de hoteles, viáticos, mano de obra, consumibles.</li>
        <li>Cualquier cargo por inconveniente, daños indirectos o de privación del uso del vehículo.</li>
        <li>Costo de mano de obra por desinstalar o instalar el motor.</li>
        <li>Costos de componentes que se hayan reemplazado sin previa autorización.</li>
        <li>No aplica si el cliente no realiza las recomendaciones de instalación dadas.</li>
    </ol>

    <div class="section-title">Recomendaciones para la Instalación</div>
    <ol class="list-style">
        <li>Mantenimiento al radiador.</li>
        <li>Mantenimiento al sistema de inyección.</li>
        <li>Mantenimiento al tanque de gasolina y conjunto (opcional).</li>
        <li>Cambio del sistema de enfriamiento de aceite (en caso de usar).</li>
        <li>Evaluación o cambio del funcionamiento de los catalizadores.</li>
        <li>1er cambio de aceite a los 1.500 kilómetros.</li>
        <li>2do cambio de aceite cada 4.000 kilómetros.</li>
        <li>Purgar el motor en la instalación, catalizadores en buen estado y sistema de enfriamiento.</li>
    </ol>

    <div style="margin-top: 10px; font-weight: bold; text-align: center; text-transform: uppercase;">
        Recibe conforme y se compromete a seguir las condiciones de garantía mencionadas.
    </div>

    <table class="footer-table">
        <tr>
            <td style="width: 55%; padding-right: 15px;">
                <table class="info-table">
                    <tr>
                        <td class="info-label">CLIENTE:</td>
                        <td class="info-value">{{ strtoupper($bill->client_name ?? 'CLIENTE GENÉRICO') }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">C.I. / RIF:</td>
                        <td class="info-value">{{ strtoupper($bill->client_cedula ?? 'N/A') }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">TELÉFONO:</td>
                        <td class="info-value">{{ $bill->client_phone ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Nº DE FACTURA:</td>
                        <td class="info-value">{{ $bill->numero_factura ?? str_pad($bill->id, 6, '0', STR_PAD_LEFT) }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">TIPO DE MOTOR:</td>
                        <td class="info-value">{{ strtoupper($bill->partida->tipo ?? 'MOTOR') }} {{ strtoupper($bill->partida->marca ?? '') }} {{ strtoupper($bill->partida->modelo ?? '') }} (AÑO: {{ $bill->partida->año ?? 'N/A' }})</td>
                    </tr>
                    <tr>
                        <td class="info-label">SERIAL:</td>
                        <td class="info-value @if($hasNoSerial) no-serial @endif">
                            {{ $serialDisplay }}
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 45%;">
                <table class="signature-container">
                    <tr>
                        <td>
                            <div class="signature-label">FIRMA</div>
                            <div class="signature-box">&nbsp;</div>
                        </td>
                        <td>
                            <div class="signature-label">SELLO</div>
                            <div class="signature-box">&nbsp;</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>
