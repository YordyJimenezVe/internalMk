<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pliego de Etiquetas por Contenedor - Maikel Cars</title>
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
            page-break-after: always;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .page-container:last-child {
            page-break-after: auto;
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
            padding: 6.5mm 1.2mm 1.5mm 1.2mm; /* 6.5mm libres arriba para la perforadora */
            overflow: hidden;
            border: 0.1mm dashed #ccc; /* Líneas de corte */
        }

        .info-value {
            font-size: 12px; /* Letra clara y visible */
            font-weight: bold;
            color: #000;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
        }

        .qr-code {
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
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
    @foreach(array_chunk($labels, 18) as $pageLabels)
        <div class="page-container">
            <div class="grid-container">
                @foreach($pageLabels as $label)
                    <div class="label-cell">
                        <div class="info-value">
                            {{ $label['inventario']->marca }} {{ $label['inventario']->modelo }}
                        </div>
                        <div class="qr-code">
                            <img src="data:image/svg+xml;base64,{{ $label['qrCode'] }}" width="58" height="58">
                        </div>
                        <div class="barcode">
                            <img src="data:image/png;base64,{{ $label['barcode'] }}" width="135" height="14">
                            <div class="barcode-text">Cod: {{ $label['inventario']->formatted_cod }} | Item: {{ str_pad($label['inventario']->codInv, 4, '0', STR_PAD_LEFT) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</body>
</html>
