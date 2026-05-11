<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Impresión Masiva de Etiquetas</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 10mm;
            background-color: white;
        }

        .labels-grid {
            width: 100%;
            display: table;
            border-collapse: separate;
            border-spacing: 5mm;
        }

        .label-row {
            display: table-row;
        }

        .label-cell {
            display: table-cell;
            width: 45%; /* 2 labels per row */
            border: 1px dashed #ccc;
            padding: 5mm;
            box-sizing: border-box;
            text-align: center;
            vertical-align: top;
            height: 60mm;
        }

        .info-section {
            border-bottom: 1px dashed #eee;
            margin-bottom: 5px;
            padding-bottom: 5px;
        }

        .info-title {
            font-size: 8px;
            font-weight: bold;
            color: #666;
            text-transform: uppercase;
        }

        .info-value {
            font-size: 10px;
            font-weight: bold;
            color: #000;
        }

        .brand-logo {
            font-size: 12px;
            font-weight: 900;
            color: #000;
            margin-bottom: 5px;
        }

        .qr-code {
            margin-bottom: 5px;
        }

        .barcode {
            margin-top: 5px;
        }

        .barcode-text {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        @page {
            size: a4 portrait;
            margin: 0;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="labels-grid">
        @foreach($labels as $index => $label)
            @if($index % 2 == 0)
                <div class="label-row">
            @endif

            <div class="label-cell">
                <!-- Parte Superior (Recortable) -->
                <div class="info-section">
                    <div class="info-title">Maikel Cars - Identificación</div>
                    <div class="info-value">
                        {{ $label['inventario']->marca }} {{ $label['inventario']->modelo }} <br>
                        <span style="font-size: 9px;">Código: {{ $label['barcodeData'] }}</span>
                    </div>
                </div>

                <!-- Parte Inferior -->
                <div class="brand-logo">MAIKEL CARS</div>
                <div class="qr-code">
                    <img src="data:image/svg+xml;base64,{{ $label['qrCode'] }}" width="80" height="80">
                </div>
                <div class="barcode">
                    <img src="data:image/png;base64,{{ $label['barcode'] }}" width="120">
                    <div class="barcode-text">{{ $label['barcodeData'] }}</div>
                </div>
            </div>

            @if($index % 2 == 1 || $loop->last)
                </div>
            @endif

            @if(($index + 1) % 8 == 0 && !$loop->last)
                </div><div class="page-break"></div><div class="labels-grid">
            @endif
        @endforeach
    </div>
</body>
</html>
