<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Etiqueta {{ $barcodeData }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 50mm;
            height: 30mm;
            background-color: white;
            font-family: 'Arial', sans-serif;
            overflow: hidden;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .label-cell {
            width: 50mm;
            height: 30mm;
            padding: 1.2mm 1mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            overflow: hidden;
            text-align: center;
            background: #fff;
        }

        .info-value {
            font-size: 10px;
            font-weight: bold;
            color: #000;
            margin: 0;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
            text-transform: uppercase;
        }

        .qr-code {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .qr-code img {
            width: 48px;
            height: 48px;
            display: block;
        }

        .barcode {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            margin: 0;
        }

        .barcode img {
            width: 130px;
            height: 14px;
            display: block;
        }

        .barcode-text {
            font-family: 'Courier New', Courier, monospace;
            font-size: 8px;
            font-weight: bold;
            letter-spacing: 0.3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
            margin-top: 1px;
            line-height: 1.1;
            color: #000;
        }

        @page {
            size: 50mm 30mm;
            margin: 0mm !important;
        }

        @media print {
            html, body {
                width: 50mm;
                height: 30mm;
                padding: 0;
                margin: 0;
            }
            body {
                display: block;
            }
            .label-cell {
                width: 50mm;
                height: 30mm;
                border: none;
                margin: 0;
                padding: 1.2mm 1mm;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="label-cell">
        <div class="info-value">
            {{ $inventario->marca }} {{ $inventario->modelo }}
        </div>
        <div class="qr-code">
            <img src="data:image/svg+xml;base64,{{ $qrCode }}" width="48" height="48" alt="QR Code">
        </div>
        <div class="barcode">
            <img src="data:image/png;base64,{{ $barcode }}" width="130" height="14" alt="Barcode">
            <div class="barcode-text">Cod: {{ $inventario->formatted_cod }} | Item: {{ str_pad($inventario->codInv, 4, '0', STR_PAD_LEFT) }}</div>
        </div>
    </div>
</body>
</html>

