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
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
        }

        .label-container {
            width: 80mm;
            height: 60mm;
            background-color: white;
            padding: 5mm;
            box-sizing: border-box;
            border: 1px solid #ddd;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
        }

        /* Information section (Top - Trimmable) */
        .info-section {
            border-bottom: 1px dashed #ccc;
            width: 100%;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .info-title {
            font-size: 10px;
            font-weight: bold;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 12px;
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
        }

        /* Principal section (Bottom) */
        .main-section {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .qr-code {
            margin-bottom: 5px;
        }

        .barcode {
            margin-top: 5px;
        }

        .barcode-text {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            font-weight: bold;
            margin-top: 2px;
            letter-spacing: 2px;
        }

        .brand-logo {
            font-size: 14px;
            font-weight: 900;
            color: #1a202c;
            margin-bottom: 5px;
        }

        @media print {
            body {
                background-color: white;
            }
            .label-container {
                border: none;
                margin: 0;
                width: 100%;
                height: auto;
            }
            @page {
                size: 80mm 60mm;
                margin: 0;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="label-container">
        <!-- Parte Superior (Recortable) -->
        <div class="info-section">
            <div class="info-title">Identificación de Producto</div>
            <div class="info-value">
                {{ $inventario->marca }} {{ $inventario->modelo }} <br>
                <span style="font-size: 10px; color: #444;">Código: {{ $barcodeData }}</span>
            </div>
        </div>

        <!-- Parte Inferior (Principal) -->
        <div class="main-section">
            <div class="brand-logo">MAIKEL CARS</div>
            
            <div class="qr-code">
                <img src="data:image/svg+xml;base64,{{ $qrCode }}" width="100" height="100">
            </div>

            <div class="barcode">
                <img src="data:image/png;base64,{{ $barcode }}">
                <div class="barcode-text">{{ $barcodeData }}</div>
            </div>
        </div>
    </div>
</body>
</html>
