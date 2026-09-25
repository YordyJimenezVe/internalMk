<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Hoja Completa de Etiquetas - Maikel Cars</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
        }

        .page-container {
            width: 210mm;
            height: 297mm;
            padding: 18mm 5mm;
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            box-sizing: border-box;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(2, 100mm);
            grid-auto-rows: 60mm;
            gap: 8mm 4.4mm;
            justify-content: center;
        }

        .label-cell {
            width: 100mm;
            height: 60mm;
            padding: 4mm 6mm;
            border: 0.15mm dashed #bbb;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #fff;
            color: #000;
            box-sizing: border-box;
            position: relative;
        }

        /* Etiqueta 1: Logo y Contacto */
        .logo-container {
            width: 100%;
            height: 22mm;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 2mm;
        }

        .logo-img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .divider {
            width: 80mm;
            border-top: 1.2px solid #000;
            margin-bottom: 1.5mm;
        }

        .blank-write-space {
            width: 80mm;
            height: 14mm;
        }

        .columns-container {
            width: 80mm;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5mm;
        }

        .col {
            width: 48%;
            display: flex;
            flex-direction: column;
            gap: 2.2mm;
        }

        .info-row {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            width: 100%;
        }

        .icon-container {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 7mm;
            margin-right: 2.5mm;
        }

        .icon {
            width: 5.8mm;
            height: 5.8mm;
            stroke: #000;
            fill: none;
        }

        .icon-fill {
            width: 5.8mm;
            height: 5.8mm;
            fill: #000;
        }

        .info-text {
            font-size: 14.5px;
            font-weight: 800;
            line-height: 1;
            white-space: nowrap;
            letter-spacing: 0.1px;
        }

        /* Etiqueta 2: QR Único */
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
            size: A4;
            margin: 0mm !important;
        }

        @media print {
            body {
                background: white;
            }
            .page-container {
                width: 210mm;
                height: 297mm;
                padding: 18mm 5mm;
                border: none;
                box-shadow: none;
                margin: 0;
            }
            .label-cell {
                border: 0.1mm dashed #aaa;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="page-container">
        <div class="grid-container">
            @for ($row = 0; $row < 4; $row++)
                @for ($col = 0; $col < 2; $col++)
                    @if (($col % 2 == 0 && $row % 2 == 0) || ($col % 2 != 0 && $row % 2 != 0))
                        <!-- Etiqueta 1: Logo y Datos -->
                        <div class="label-cell">
                            <div class="logo-container">
                                @if($logoBase64)
                                    <img src="data:image/png;base64,{{ $logoBase64 }}" class="logo-img" alt="Maikel Cars Logo">
                                @else
                                    <span style="font-size: 24px; font-weight: bold; letter-spacing: 3px;">MAIKEL CARS</span>
                                @endif
                            </div>
                            <div class="divider"></div>
                            <div class="blank-write-space"></div>
                            <div class="columns-container">
                                <div class="col left-col">
                                    <div class="info-row">
                                        <div class="icon-container">
                                            <svg viewBox="0 0 24 24" class="icon" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="2" y1="12" x2="22" y2="12"></line>
                                                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                            </svg>
                                        </div>
                                        <div class="info-text">maikelcars.com</div>
                                    </div>
                                    <div class="info-row" style="margin-top: 1mm;">
                                        <div class="icon-container">
                                            <svg viewBox="0 0 24 24" class="icon" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                                            </svg>
                                        </div>
                                        <div class="info-text">@maikelcars51</div>
                                    </div>
                                </div>
                                <div class="col right-col">
                                    <div class="info-row">
                                        <div class="icon-container">
                                            <svg viewBox="0 0 24 24" class="icon-fill">
                                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.003 5.37 5.378 0 12.007 0a11.94 11.94 0 0 1 8.5 3.5 11.9 11.9 0 0 1 3.5 8.5c-.005 6.63-5.38 12.003-12.007 12.003-.1.1-2.006-.025-3.88-.56L0 24zm6.59-20.371c-.148-.328-.303-.333-.443-.339-.115-.005-.247-.005-.38-.005-.133 0-.35.05-.533.25-.183.2-.7.683-.7 1.666 0 .983.716 1.933.816 2.066.1.133 1.41 2.154 3.417 3.02.478.207.85.33 1.142.424.48.152.917.13 1.263.08.385-.057 1.183-.483 1.35-1.05.166-.567.166-1.05.116-1.15-.05-.1-.183-.166-.383-.266-.2-.1-1.183-.583-1.366-.65-.183-.066-.316-.1-.45-.1-.133 0-.266.05-.383.216-.117.166-.45.567-.55.683-.1.117-.2.133-.4.033a5.05 5.05 0 0 1-1.484-.917 5.57 5.57 0 0 1-1.028-1.283c-.117-.2-.013-.309.087-.409.09-.09.2-.233.3-.35.1-.117.133-.2.2-.333.067-.133.033-.25-.017-.35-.05-.1-.443-1.066-.607-1.46z"/>
                                            </svg>
                                        </div>
                                        <div class="info-text">0424-5213994</div>
                                    </div>
                                    <div class="info-row" style="margin-top: 1mm;">
                                        <div class="icon-container">
                                            <svg viewBox="0 0 24 24" class="icon-fill">
                                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.003 5.37 5.378 0 12.007 0a11.94 11.94 0 0 1 8.5 3.5 11.9 11.9 0 0 1 3.5 8.5c-.005 6.63-5.38 12.003-12.007 12.003-.1.1-2.006-.025-3.88-.56L0 24zm6.59-20.371c-.148-.328-.303-.333-.443-.339-.115-.005-.247-.005-.38-.005-.133 0-.35.05-.533.25-.183.2-.7.683-.7 1.666 0 .983.716 1.933.816 2.066.1.133 1.41 2.154 3.417 3.02.478.207.85.33 1.142.424.48.152.917.13 1.263.08.385-.057 1.183-.483 1.35-1.05.166-.567.166-1.05.116-1.15-.05-.1-.183-.166-.383-.266-.2-.1-1.183-.583-1.366-.65-.183-.066-.316-.1-.45-.1-.133 0-.266.05-.383.216-.117.166-.45.567-.55.683-.1.117-.2.133-.4.033a5.05 5.05 0 0 1-1.484-.917 5.57 5.57 0 0 1-1.028-1.283c-.117-.2-.013-.309.087-.409.09-.09.2-.233.3-.35.1-.117.133-.2.2-.333.067-.133.033-.25-.017-.35-.05-.1-.443-1.066-.607-1.46z"/>
                                            </svg>
                                        </div>
                                        <div class="info-text">0424-5665298</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Etiqueta 2: QR Único -->
                        <div class="label-cell">
                            <div class="qr-container">
                                <img src="data:image/svg+xml;base64,{{ $qrCode }}" class="qr-code" alt="QR Code">
                            </div>
                        </div>
                    @endif
                @endfor
            @endfor
        </div>
    </div>
</body>
</html>
