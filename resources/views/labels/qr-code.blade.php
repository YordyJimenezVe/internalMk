<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Etiqueta Maikel Cars - Código QR</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            margin: 0;
            padding: 0;
            background-color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .label-cell {
            width: 100mm;
            height: 60mm;
            padding: 6mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #fff;
            color: #000;
        }

        .qr-container {
            width: 48mm;
            height: 48mm;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .qr-code {
            width: 100%;
            height: 100%;
        }

        @page {
            size: 100mm 60mm;
            margin: 0mm !important;
        }

        @media print {
            html, body {
                width: 100mm;
                height: 60mm;
                background-color: white;
            }
            body {
                display: block;
            }
            .label-cell {
                width: 100mm;
                height: 60mm;
                border: none;
                margin: 0;
                padding: 6mm;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="label-cell">
        <!-- Código QR Único (Centrado) -->
        <div class="qr-container">
            <img src="data:image/svg+xml;base64,{{ $qrCode }}" class="qr-code" alt="QR Code">
        </div>
    </div>
</body>
</html>
