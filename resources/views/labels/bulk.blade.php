<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Impresión Masiva de Etiquetas</title>
    <style>
        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            background-color: white;
            font-family: 'Arial', sans-serif;
        }

        body {
            display: flex;
            flex-direction: column;
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
            page-break-after: always;
            border: 1px dashed #ccc;
        }

        .label-cell:last-child {
            page-break-after: auto;
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
            body {
                padding: 0;
                justify-content: flex-start;
            }
            .label-cell {
                border: none;
                margin: 0;
                margin-bottom: 0;
                padding: 1.2mm 1mm;
            }
        }
    </style>
</head>
<body>
    @foreach($labels as $index => $label)
        <div class="label-cell">
            <div class="info-value">
                {{ $label['inventario']->marca }} {{ $label['inventario']->modelo }}
            </div>

            <div class="qr-code">
                <img src="data:image/svg+xml;base64,{{ $label['qrCode'] }}" width="48" height="48" alt="QR Code">
            </div>
            <div class="barcode">
                <img src="data:image/png;base64,{{ $label['barcode'] }}" width="130" height="14" alt="Barcode">
                <div class="barcode-text">Cod: {{ $label['inventario']->formatted_cod }} | Item: {{ str_pad($label['inventario']->codInv, 4, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>
    @endforeach
</body>
</html>

