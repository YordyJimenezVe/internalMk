<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Etiqueta {{ $barcodeData }}</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .page-container {
            width: 215.9mm; /* Ancho de hoja Carta */
            height: 279.4mm; /* Alto de hoja Carta */
            padding: 11.7mm 9.95mm; /* Margen centrado para el pliego 3x6 con gaps */
            background: #fff;
            box-sizing: border-box;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(3, 60mm);
            grid-auto-rows: 36mm;
            gap: 8mm; /* Espacio de 8mm para plastificar y cortar individualmente */
            justify-content: center;
        }

        .label-cell {
            width: 60mm;
            height: 36mm;
            box-sizing: border-box;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between; /* Distribuye el contenido para que no se encime ni se corte */
            padding: 4.5mm 1.2mm 1.2mm 1.2mm; /* 4.5mm libres arriba para la perforadora */
            overflow: hidden;
            border: 0.1mm dashed #ccc; /* Líneas de corte */
            background: #fff;}

        .info-title-container {
            width: 100%;
            height: 6.2mm;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin: 0;
            padding: 0 0.8mm;
            box-sizing: border-box;
        }

        .info-value {
            font-size: 9.5px; /* Permite hasta 2 líneas completas sin recortar con ... */
            font-weight: 800;
            color: #000;
            margin: 0;
            line-height: 1.15;
            text-transform: uppercase;
            text-align: center;
            width: 100%;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            word-break: normal;
        }

        
        .qr-row {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2.8mm;
            margin: 0;
        }

        .side-badge {
            width: 9.5mm;
            height: 9.5mm;
            border: 1px solid #000;
            border-radius: 2.5px;
            background: #000;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
            padding: 0.5px;
            flex-shrink: 0;
        }

        .side-badge .side-letter {
            font-size: 15px;
            font-weight: 900;
            line-height: 0.95;
            font-family: Arial, Helvetica, sans-serif;
            letter-spacing: -0.5px;
        }

        .side-badge .side-letter-sm {
            font-size: 11px;
            font-weight: 900;
            line-height: 1.1;
            font-family: Arial, Helvetica, sans-serif;
            letter-spacing: 0.5px;
        }

        .side-badge .side-sub {
            font-size: 5.5px;
            font-weight: 800;
            letter-spacing: 0.4px;
            line-height: 1;
            margin-top: 1px;
            text-transform: uppercase;
            opacity: 0.95;
        }

        .qr-code {
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .qr-code img {
            width: 17.5mm;
            height: 17.5mm;
            display: block;
        }

        .barcode {
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .barcode-text {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-top: 1px;
            line-height: 1.1;
            color: #000;
        }

        @page {
            size: letter;
            margin: 0mm !important;
        }

        @media print {
            body {
                background: white;
            }
            .page-container {
                box-shadow: none;
                margin: 0;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="page-container">
        <div class="grid-container">
            <div class="label-cell">
                <div class="info-title-container">
                    <div class="info-value">
                        {{ $inventario->label_title }}
                    </div>
                </div>
                <div class="qr-row">
                    @if(!$inventario->is_motor && $inventario->camera_side)
                        <div class="side-badge">
                            <span class="side-letter">{{ $inventario->camera_side }}</span>
                            <span class="side-sub">{{ $inventario->camera_side === 'L' ? 'IZQ' : 'DER' }}</span>
                        </div>
                    @endif

                    <div class="qr-code">
                        <img src="data:image/svg+xml;base64,{{ $qrCode }}" width="70" height="70" alt="QR Code">
                    </div>
                </div>
                <div class="barcode">
                    <img src="data:image/png;base64,{{ $barcode }}" width="135" height="14" alt="Barcode">
                    <div class="barcode-text">Cod: {{ $inventario->formatted_cod }} | Item: {{ str_pad($inventario->codInv, 4, '0', STR_PAD_LEFT) }}</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


