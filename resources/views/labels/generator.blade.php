<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maikel Cars - Generador de Etiquetas</title>
    
    <!-- Fonts and Stylesheets -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Orbitron:wght@600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --bg-color: #0b0f17;
            --card-bg: rgba(17, 24, 39, 0.7);
            --border-color: rgba(245, 158, 11, 0.2);
            --gold: #fbbf24;
            --gold-hover: #f59e0b;
            --text-primary: #f3f4f6;
            --text-secondary: #9ca3af;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(245, 158, 11, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(245, 158, 11, 0.05) 0%, transparent 40%);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 2rem 1rem;
            position: relative;
        }

        /* Back to App Button */
        .back-button {
            position: absolute;
            top: 2rem;
            left: 2rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.15rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 9999px;
            color: var(--text-primary);
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
            backdrop-filter: blur(8px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            z-index: 100;
        }
        .back-button:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--gold);
            color: var(--gold);
            transform: translateX(-3px);
            box-shadow: 0 4px 12px rgba(251, 191, 36, 0.15);
        }
        
        @media (max-width: 1024px) {
            .back-button {
                position: static;
                margin-bottom: 0rem;
                align-self: flex-start;
            }
        }

        .container {
            max-width: 1000px;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 2.5rem;
        }

        /* Header Premium */
        header {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            animation: fadeInDown 0.8s ease-out;
        }

        .brand-logo-container {
            width: 220px;
            height: auto;
            margin-bottom: 0.5rem;
        }

        .brand-logo {
            width: 100%;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 0 10px rgba(251, 191, 36, 0.3));
        }

        h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.2rem;
            font-weight: 800;
            background: linear-gradient(to right, #ffffff, var(--gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .subtitle {
            font-size: 1.05rem;
            color: var(--text-secondary);
            max-width: 650px;
            line-height: 1.6;
            font-weight: 300;
        }

        /* Main Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            animation: fadeInUp 0.8s ease-out;
        }

        @media (max-width: 500px) {
            .cards-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Card Component */
        .label-card {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .label-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }

        .label-card:hover {
            transform: translateY(-8px);
            border-color: rgba(251, 191, 36, 0.4);
            box-shadow: 0 15px 40px rgba(251, 191, 36, 0.08);
        }

        .label-card:hover::before {
            transform: translateX(100%);
        }

        .card-header {
            text-align: center;
        }

        .card-number {
            font-family: 'Orbitron', sans-serif;
            font-size: 0.8rem;
            font-weight: 800;
            color: var(--gold);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }

        .card-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .card-desc {
            font-size: 0.9rem;
            color: var(--text-secondary);
            margin-top: 0.4rem;
            font-weight: 300;
        }

        /* Realistic Mockup Sticker Frame */
        .sticker-wrapper {
            background: #111827;
            padding: 2.5rem;
            border-radius: 16px;
            border: 1px dashed rgba(255, 255, 255, 0.1);
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.8);
        }

        /* 50x30mm sticker preview aspect ratio (5:3) */
        .sticker-preview {
            width: 250px;
            height: 150px;
            background-color: #ffffff;
            border-radius: 4px;
            box-shadow: 
                0 15px 35px rgba(0, 0, 0, 0.5),
                0 0 0 1px rgba(0, 0, 0, 0.05);
            display: flex;
            overflow: hidden;
            color: #000000;
            position: relative;
            user-select: none;
            transition: transform 0.3s ease;
        }

        .sticker-preview:hover {
            transform: scale(1.03);
        }

        /* Preview Label 1 inside dashboard */
        .label1-preview {
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            padding: 8px 12px;
        }

        .label1-preview .preview-logo {
            width: 80%;
            height: 48px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .label1-preview .preview-logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .label1-preview .preview-divider {
            width: 90%;
            border-top: 1px solid #000;
            margin: 4px 0;
        }

        .label1-preview .preview-info {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .label1-preview .preview-row {
            display: flex;
            align-items: center;
            font-size: 8.5px;
            font-weight: 700;
            padding-left: 12px;
        }

        .label1-preview .preview-row i {
            margin-right: 8px;
            font-size: 8.5px;
            width: 10px;
            text-align: center;
        }

        /* Preview Label 2 inside dashboard (centered QR) */
        .label2-preview {
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 8px;
        }

        .label2-preview .preview-left {
            width: 48%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .label2-preview .preview-logo-small {
            width: 100%;
            height: 42px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 6px;
        }

        .label2-preview .preview-logo-small img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .label2-preview .preview-left .preview-divider {
            width: 80%;
            border-top: 1px solid #000;
            margin-bottom: 6px;
        }

        .label2-preview .preview-text {
            font-size: 6.5px;
            font-weight: 800;
            text-transform: uppercase;
            line-height: 1.2;
            letter-spacing: 0.2px;
        }

        .label2-preview .preview-text span {
            font-size: 5.5px;
            font-weight: 600;
            color: #444;
            display: block;
            margin-top: 2px;
        }

        .label2-preview .preview-right {
            width: 48%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .label2-preview .preview-qr {
            width: 105px;
            height: 105px;
            background: #f3f4f6;
            border-radius: 6px;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 1px solid #e5e7eb;
            position: relative;
            overflow: hidden;
        }

        .label2-preview .preview-qr i {
            font-size: 4rem;
            color: #111827;
        }

        /* Action Buttons */
        .btn-print {
            font-family: 'Orbitron', sans-serif;
            width: 100%;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, var(--gold), var(--gold-hover));
            color: #000;
            font-size: 0.95rem;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(251, 191, 36, 0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-print:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 20px rgba(251, 191, 36, 0.4);
            background: linear-gradient(135deg, #ffffff, var(--gold));
        }

        .btn-print:active {
            transform: scale(0.98);
        }

        /* Footer */
        footer {
            margin-top: auto;
            padding-top: 4rem;
            font-size: 0.85rem;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            animation: fadeIn 1s ease-out;
        }

        footer i {
            color: #ef4444;
        }

        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>

    <div class="container">
        
        <!-- Back to App Button -->
        <a href="{{ route('dashboard') }}" class="back-button">
            <i class="fa-solid fa-arrow-left"></i> Volver al Panel
        </a>
        
        <!-- Header -->
        <header>
            <div class="brand-logo-container">
                <img src="/logo-mk-transparent.png" class="brand-logo" alt="Maikel Cars Logo">
            </div>
            <h1>Generador de Etiquetas</h1>
            <p class="subtitle">
                Herramienta premium para la creación e impresión de etiquetas autoadhesivas en motores. Optimizadas para formato de **100x60mm** para máxima legibilidad.
            </p>
        </header>

        <!-- Cards Grid -->
        <div class="cards-grid">
            
            <!-- Etiqueta 1: Logo + Contacto -->
            <div class="label-card">
                <div class="card-header">
                    <div class="card-number">Etiqueta 01</div>
                    <h2 class="card-title">Logo y Contacto</h2>
                    <p class="card-desc">Logotipo principal con datos de contacto ordenados en lista.</p>
                </div>

                <div class="sticker-wrapper">
                    <div class="sticker-preview label1-preview" style="padding: 10px 14px; flex-direction: column; justify-content: center; align-items: center;">
                        <div class="preview-logo" style="height: 52px; margin-bottom: 4px; width: 100%; display: flex; justify-content: center;">
                            <img src="/logo-mk-transparent.png" alt="Maikel Cars" style="max-height: 100%; object-fit: contain;">
                        </div>
                        <div class="preview-divider" style="width: 80%; margin-bottom: 2px; border-top: 1.5px solid #000;"></div>
                        <!-- Espacio en blanco completamente limpio de altura equivalente -->
                        <div class="preview-blank-space" style="width: 80%; height: 40px;"></div>
                        <!-- 2 columns layout preview súper compactado y centrado (ancho 80%) -->
                        <div class="preview-columns" style="width: 80%; display: flex; justify-content: space-between; align-items: flex-start; text-align: left;">
                            <div class="preview-col-left" style="width: 48%; display: flex; flex-direction: column; gap: 4px;">
                                <div class="preview-row" style="font-size: 10.5px; padding-left: 0; white-space: nowrap; font-weight: bold; color: #000;">
                                    <i class="fa-solid fa-globe" style="font-size: 10.5px; margin-right: 4px; width: auto;"></i> maikelcars.com
                                </div>
                                <div class="preview-row" style="font-size: 10.5px; padding-left: 0; white-space: nowrap; font-weight: bold; color: #000;">
                                    <i class="fa-brands fa-instagram" style="font-size: 10.5px; margin-right: 4px; width: auto;"></i> @maikelcars51
                                </div>
                            </div>
                            <div class="preview-col-right" style="width: 48%; display: flex; flex-direction: column; gap: 4px;">
                                <div class="preview-row" style="font-size: 10.5px; padding-left: 0; white-space: nowrap; font-weight: bold; color: #000;">
                                    <i class="fa-brands fa-whatsapp" style="font-size: 10.5px; margin-right: 4px; width: auto;"></i> 0424-5213994
                                </div>
                                <div class="preview-row" style="font-size: 10.5px; padding-left: 0; white-space: nowrap; font-weight: bold; color: #000;">
                                    <i class="fa-brands fa-whatsapp" style="font-size: 10.5px; margin-right: 4px; width: auto;"></i> 0424-5665298
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('labels.print.logo-info') }}" target="_blank" class="btn-print">
                    <i class="fa-solid fa-print"></i> Imprimir Etiqueta
                </a>
            </div>

            <!-- Etiqueta 2: Código QR -->
            <div class="label-card">
                <div class="card-header">
                    <div class="card-number">Etiqueta 02</div>
                    <h2 class="card-title">Código QR</h2>
                    <p class="card-desc">Código QR de contacto a tamaño completo para escaneo directo.</p>
                </div>

                <div class="sticker-wrapper">
                    <div class="sticker-preview label2-preview">
                        <div class="preview-qr" style="width: 110px; height: 110px; border: none; background: transparent; display: flex; justify-content: center; align-items: center;">
                            <i class="fa-solid fa-qrcode" style="font-size: 7rem; color: #111827;"></i>
                        </div>
                    </div>
                </div>

                <a href="{{ route('labels.print.qr-code') }}" target="_blank" class="btn-print">
                    <i class="fa-solid fa-print"></i> Imprimir Etiqueta
                </a>
            </div>

            <!-- Etiqueta 3: Hoja A4 Completa -->
            <div class="label-card">
                <div class="card-header">
                    <div class="card-number">Etiqueta 03</div>
                    <h2 class="card-title">Hoja A4 Completa</h2>
                    <p class="card-desc">Pliego completo con 8 etiquetas de 100x60mm alternadas listas para cortar.</p>
                </div>

                <div class="sticker-wrapper">
                    <!-- A mini-sheet preview for 2x4 grid -->
                    <div class="sticker-preview sheet-preview" style="padding: 10px 14px; display: grid; grid-template-columns: repeat(2, 1fr); grid-template-rows: repeat(4, 1fr); gap: 4px; background: #e5e7eb; border-radius: 12px; width: 270px; height: 162px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1); justify-content: center; align-content: center; border: none;">
                        <!-- Row 1 -->
                        <div class="mini-label" style="background: white; border: 0.5px dashed #999; border-radius: 2px; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 2px; height: 32px; box-sizing: border-box;">
                            <div style="width: 80%; height: 3px; background: #000; margin-bottom: 2px;"></div>
                            <div style="width: 60%; height: 1px; background: #666; margin-bottom: 2px;"></div>
                            <div style="width: 80%; height: 2px; background: #888;"></div>
                        </div>
                        <div class="mini-label" style="background: white; border: 0.5px dashed #999; border-radius: 2px; display: flex; justify-content: center; align-items: center; padding: 2px; height: 32px; box-sizing: border-box;">
                            <i class="fa-solid fa-qrcode" style="font-size: 14px; color: #111827;"></i>
                        </div>

                        <!-- Row 2 -->
                        <div class="mini-label" style="background: white; border: 0.5px dashed #999; border-radius: 2px; display: flex; justify-content: center; align-items: center; padding: 2px; height: 32px; box-sizing: border-box;">
                            <i class="fa-solid fa-qrcode" style="font-size: 14px; color: #111827;"></i>
                        </div>
                        <div class="mini-label" style="background: white; border: 0.5px dashed #999; border-radius: 2px; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 2px; height: 32px; box-sizing: border-box;">
                            <div style="width: 80%; height: 3px; background: #000; margin-bottom: 2px;"></div>
                            <div style="width: 60%; height: 1px; background: #666; margin-bottom: 2px;"></div>
                            <div style="width: 80%; height: 2px; background: #888;"></div>
                        </div>

                        <!-- Row 3 -->
                        <div class="mini-label" style="background: white; border: 0.5px dashed #999; border-radius: 2px; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 2px; height: 32px; box-sizing: border-box;">
                            <div style="width: 80%; height: 3px; background: #000; margin-bottom: 2px;"></div>
                            <div style="width: 60%; height: 1px; background: #666; margin-bottom: 2px;"></div>
                            <div style="width: 80%; height: 2px; background: #888;"></div>
                        </div>
                        <div class="mini-label" style="background: white; border: 0.5px dashed #999; border-radius: 2px; display: flex; justify-content: center; align-items: center; padding: 2px; height: 32px; box-sizing: border-box;">
                            <i class="fa-solid fa-qrcode" style="font-size: 14px; color: #111827;"></i>
                        </div>

                        <!-- Row 4 -->
                        <div class="mini-label" style="background: white; border: 0.5px dashed #999; border-radius: 2px; display: flex; justify-content: center; align-items: center; padding: 2px; height: 32px; box-sizing: border-box;">
                            <i class="fa-solid fa-qrcode" style="font-size: 14px; color: #111827;"></i>
                        </div>
                        <div class="mini-label" style="background: white; border: 0.5px dashed #999; border-radius: 2px; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 2px; height: 32px; box-sizing: border-box;">
                            <div style="width: 80%; height: 3px; background: #000; margin-bottom: 2px;"></div>
                            <div style="width: 60%; height: 1px; background: #666; margin-bottom: 2px;"></div>
                            <div style="width: 80%; height: 2px; background: #888;"></div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('labels.print.full-page') }}" target="_blank" class="btn-print">
                    <i class="fa-solid fa-print"></i> Imprimir Hoja Completa
                </a>
            </div>

        </div>

        <!-- Footer -->
        <footer style="justify-content: center;">
            <span>Maikel Cars &copy; 2026</span>
            <span>&bull;</span>
            <span>Diseñado con <i class="fa-solid fa-heart animate__animated animate__pulse animate__infinite"></i> para el Taller</span>
        </footer>

    </div>

</body>
</html>
