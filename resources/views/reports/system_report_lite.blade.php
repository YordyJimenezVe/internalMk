<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resumen Ejecutivo - Maikel Cars</title>
    <style>
        @page {
            margin: 0cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        /* --- PORTADA CON TABLA (MÁXIMA ESTABILIDAD) --- */
        .cover-table {
            width: 100%;
            height: 100vh;
            background-color: #020617;
            border-collapse: collapse;
        }
        .cover-content {
            vertical-align: middle;
            text-align: center;
            padding: 50px;
        }
        .cover-h1 {
            font-size: 55px;
            color: #38bdf8;
            text-transform: uppercase;
            letter-spacing: 5px;
            margin: 0;
        }
        .cover-h2 {
            font-size: 22px;
            color: #f8fafc;
            margin: 20px 0;
            font-weight: 300;
        }
        .cover-badge {
            background: #0ea5e9;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 13px;
        }
        .cover-img {
            width: 70%;
            border: 2px solid #0ea5e9;
            border-radius: 10px;
            margin: 30px 0;
        }
        .cover-date {
            color: #ffffff;
            font-size: 18px;
            font-weight: bold;
        }

        /* --- CONTENIDO --- */
        .content {
            padding: 40px 50px;
        }
        .main-title {
            color: #0284c7;
            font-size: 24px;
            text-align: center;
            text-transform: uppercase;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
        
        /* --- GRID CON TABLA --- */
        .grid-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 15px;
        }
        .grid-cell {
            width: 50%;
            background: #f8fafc;
            padding: 15px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            vertical-align: top;
            text-align: center;
        }
        .grid-cell h3 {
            font-size: 15px;
            color: #0f172a;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .grid-cell p {
            font-size: 11px;
            color: #64748b;
            margin: 0 0 10px 0;
        }
        .grid-cell img {
            width: 100%;
            max-height: 180px;
            border-radius: 4px;
        }

        /* --- PRECIOS --- */
        .price-container {
            margin-top: 20px;
            text-align: center;
        }
        .price-card {
            background: #f1f5f9;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 10px;
            border: 1px solid #cbd5e1;
        }
        .price-card b { font-size: 14px; color: #0f172a; }
        .price-val { font-size: 22px; font-weight: 900; color: #0284c7; display: block; }
        
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            padding: 10px 0;
            font-size: 9px;
            color: #94a3b8;
            background: white;
            border-top: 1px solid #f1f5f9;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

<table class="cover-table">
    <tr>
        <td class="cover-content">
            <h1 class="cover-h1">MAIKEL CARS</h1>
            <h2 class="cover-h2">SISTEMA DE GESTIÓN AUTOMATIZADA</h2>
            <span class="cover-badge">RESUMEN EJECUTIVO</span>
            <br>
            <img src="{{ $landingImage }}" class="cover-img">
            <br>
            <div class="cover-date">ENTREGA: 27/03/2026</div>
        </td>
    </tr>
</table>

<div class="page-break"></div>

<div class="content">
    <h1 class="main-title">Visión General de Módulos</h1>
    
    <table class="grid-table">
        <tr>
            <td class="grid-cell">
                <h3>Dashboard</h3>
                <p>Métricas clave en tiempo real.</p>
                <img src="{{ $dashboardImage }}">
            </td>
            <td class="grid-cell">
                <h3>Inventario</h3>
                <p>Motores, cajas y autopartes.</p>
                <img src="{{ $inventoryImage }}">
            </td>
        </tr>
        <tr>
            <td class="grid-cell">
                <h3>Mantenimiento</h3>
                <p>Reparación técnica de motores.</p>
                <img src="{{ $maintenanceImage }}">
            </td>
            <td class="grid-cell">
                <h3>Reportes</h3>
                <p>Auditoría e inteligencia de datos.</p>
                <img src="{{ $reportsImage }}">
            </td>
        </tr>
    </table>

    <div style="margin-top: 30px; border-left: 5px solid #0ea5e9; padding: 15px; background: #f0f9ff; border-radius: 5px;">
        <p style="margin: 0; font-size: 13px; color: #0369a1;">
            <b>Objetivo:</b> Automatizar el control de activos especializados y optimizar la rentabilidad operativa de Maikel Cars mediante el uso de inteligencia de datos.
        </p>
    </div>
</div>

<div class="page-break"></div>

<div class="content">
    <h1 class="main-title">Propuesta de Inversión</h1>
    
    <div class="price-container">
        <div class="price-card">
            <b>DESPLIEGUE, CONFIGURACIÓN Y SOPORTE</b>
            <span class="price-val">$2,500 - $4,300 USD</span>
            <p style="margin: 5px 0 0 0; font-size: 11px;">Incluye infraestructura, capacitación y soporte por 3 meses.</p>
        </div>

        <div class="price-card">
            <b>PROPIEDAD TOTAL DEL CÓDIGO FUENTE</b>
            <span class="price-val">$7,000 - $11,000 USD</span>
            <p style="margin: 5px 0 0 0; font-size: 11px;">Acceso a repositorios, documentación técnica y derechos de autor.</p>
        </div>

        <p style="margin-top: 50px; font-style: italic; color: #64748b; font-size: 12px;">
            Maikel Cars &copy; 2026 - Soluciones Tecnológicas Automotrices
        </p>
    </div>
</div>

<div class="footer">
    RESUMEN EJECUTIVO - MAIKEL CARS - CONFIDENCIAL &copy; {{ date('Y') }}
</div>

</body>
</html>
