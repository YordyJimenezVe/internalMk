<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Etiqueta {{ $barcodeData }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: white;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .label-cell {
            width: 50mm;
            height: 30mm;
            box-sizing: border-box;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1mm;
            overflow: hidden;
        }

        .info-value {
            font-size: 8px;
            font-weight: bold;
            color: #000;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
        }

        .qr-code {
            margin-bottom: 2px;
        }

        .barcode {
            margin-top: 2px;
        }

        .barcode-text {
            font-family: 'Courier New', Courier, monospace;
            font-size: 7px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-top: 1px;
        }

        @page {
            size: 50mm 30mm;
            margin: 0mm !important;
        }

        @media print {
            body {
                padding: 0;
                justify-content: flex-start;
            }
            .label-cell {
                border: none;
                margin: 0;
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
            <img src="data:image/svg+xml;base64,{{ $qrCode }}" width="45" height="45">
        </div>
        <div class="barcode">
            <img src="data:image/png;base64,{{ $barcode }}" width="130" height="15">
        </div>
    </div>
</body>
</html>
