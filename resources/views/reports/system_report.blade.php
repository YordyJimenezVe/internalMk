<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe del Sistema - Maikel Cars</title>
    <style>
        @page {
            margin: 0cm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #0f172a;
            line-height: 1.5;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }
        /* --- PORTADA --- */
        .cover {
            background-color: #020617;
            color: #ffffff;
            width: 100%;
            padding: 4cm 50px;
            text-align: center;
            page-break-after: always;
        }
        .cover h1 {
            font-size: 64px;
            margin: 0;
            padding: 0;
            text-transform: uppercase;
            letter-spacing: 6px;
            font-weight: bold;
            color: #38bdf8;
        }
        .cover h2 {
            font-size: 24px;
            margin: 30px 0;
            font-weight: 500;
            color: #f8fafc;
            text-transform: uppercase;
            border-bottom: 0 !important;
        }
        .cover .date {
            margin-top: 1cm;
            font-size: 22px;
            color: #ffffff;
            font-weight: bold;
        }
        .cover-image-box {
            margin: 2cm auto;
            width: 80%;
        }
        .cover-image-box img {
            width: 100%;
            border-radius: 12px;
            border: 2px solid #38bdf8;
        }

        /* --- CABECERA --- */
        .header {
            background: #0f172a;
            color: white;
            padding: 15px 0;
            text-align: center;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* --- CONTENIDO --- */
        .content {
            padding: 30px 50px;
        }
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .main-title {
            color: #0284c7;
            font-size: 28px;
            text-align: center;
            text-transform: uppercase;
            font-weight: 900;
            margin-bottom: 20px;
            border-bottom: 2px solid #eff6ff;
            padding-bottom: 12px;
        }
        h2 {
            color: #1e293b;
            font-size: 20px;
            margin: 25px 0 10px 0;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 5px;
        }
        p {
            font-size: 14.5px;
            color: #334155;
            margin-bottom: 15px;
            text-align: justify;
        }
        .highlight {
            color: #0369a1;
            font-weight: bold;
        }

        /* --- IMÁGENES --- */
        .img-container {
            margin: 15px 0;
            text-align: center;
            padding: 5px;
            border: 1px solid #f1f5f9;
        }
        .img-container img {
            max-width: 100%;
            max-height: 380px;
            border-radius: 4px;
        }
        .caption {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 8px;
            text-align: center;
        }

        /* --- PRECIOS --- */
        .price-table {
            width: 100%;
            margin-top: 20px;
            background: #f8fafc;
            border-radius: 16px;
            padding: 25px;
            border: 1px solid #e2e8f0;
        }
        .price-row {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f1f5f9;
        }
        .price-row:last-child { border-bottom: none; }
        .price-name { font-weight: bold; color: #0f172a; font-size: 17px; display: block; margin-bottom: 8px;}
        .price-val { color: #0284c7; font-size: 22px; font-weight: 900; display: block; margin-bottom: 5px;}

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            padding: 10px 0;
            font-size: 10px;
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

<!-- PORTADA -->
<div class="cover">
    <h1>MAIKEL CARS</h1>
    <h2>Dossier de Gestión Integral e Inteligencia de Negocio</h2>
    
    <div class="cover-image-box">
        <img src="{{ $landingImage }}">
    </div>

    <div class="date">FECHA DE ENTREGA: 27/03/2026</div>
</div>

<!-- ELIMINADO EL DIV DE PAGE-BREAK QUE CAUSABA LA PÁGINA EN BLANCO -->

<div class="header">Dossier Técnico Informativo - Sistema Maikel Cars 2026</div>

<div class="content">
    <div class="section">
        <h1 class="main-title">Propósito y Visión del Sistema</h1>
        <p>
            El sistema de gestión de <strong>Maikel Cars</strong> nace con el objetivo de transformar la operatividad logística y técnica de la empresa en un flujo de trabajo digital, robusto y automatizado. La visión central del proyecto es <span class="highlight">centralizar el control absoluto</span> de los activos más críticos del negocio: motores y autopartes, garantizando trazabilidad total desde el puerto de llegada hasta el cliente final.
        </p>
        <p>
            Mediante la implementación de este ecosistema digital, la empresa elimina las redundancias de datos, acelera los tiempos de respuesta del taller y profesionaliza la comunicación con el cliente a través de facturas y reportes detallados, posicionando a Maikel Cars como un referente tecnológico en el sector automotriz.
        </p>
    </div>

    <div class="section">
        <h2>Panel de Dashboard (Gestión Estratégica)</h2>
        <p>
            La interfaz de mando ofrece una <span class="highlight">vista 360° en tiempo real</span> de la salud financiera y operativa del negocio. En una sola pantalla, la gerencia puede monitorear el volumen de ingresos, el estatus de las partidas activas y la carga de trabajo del taller mecánico. Este enfoque de "Dataviz" permite identificar cuellos de botella y oportunidades de venta de manera inmediata.
        </p>
        <div class="img-container">
            <img src="{{ $dashboardImage }}">
            <div class="caption">Centro de mando con indicadores clave de rendimiento (KPIs).</div>
        </div>
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2>Módulo de Inventario (Motores y Autopartes)</h2>
        <p>
            Este es el núcleo transaccional del sistema. Permite la administración milimétrica de <strong>motores, cajas, cámaras y autopartes</strong>. A diferencia de un inventario genérico, este módulo está optimizado para el sector automotriz, permitiendo rastrear cada pieza por su contenedor de origen, modelo de vehículo compatible y serial de fábrica.
        </p>
        <p>
            La arquitectura de datos garantiza que no existan piezas "perdidas". Cada activo tiene un historial de vida dentro del sistema que detalla cuándo entró al país, qué reparaciones se le realizaron y a qué precio se vendió finalmente, asegurando un <span class="highlight">margen de rentabilidad preciso</span>.
        </p>
        <div class="img-container">
            <img src="{{ $inventoryImage }}">
            <div class="caption">Control transaccional de piezas con trazabilidad por contenedor.</div>
        </div>
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2>Ingeniería y Mantenimiento de Motor</h2>
        <p>
            Diseñado para el área técnica, este módulo gestiona la <span class="highlight">reparación y puesta a punto de motores</span>. Cada orden de servicio (Ticket) detalla el diagnóstico inicial, los técnicos asignados y los repuestos utilizados del inventario propio.
        </p>
        <p>
            El flujo de trabajo automatizado notifica los cambios de estado (En Espera, En Proceso, Terminado), permitiendo una coordinación perfecta entre el equipo mecánico y el equipo administrativo para la entrega final del producto. Es una herramienta clave para garantizar la garantía de calidad de Maikel Cars.
        </p>
        <div class="img-container">
            <img src="{{ $maintenanceImage }}">
            <div class="caption">Flujo de trabajo técnico para el mantenimiento mayor de motores.</div>
        </div>
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2>Centro de Reportes e Inteligencia</h2>
        <p>
            El sistema procesa miles de registros para ofrecer <span class="highlight">información procesable</span>. El módulo de reportes permite filtrar datos masivos por fechas, categorías o estados de venta, generando documentos en Excel y PDF listos para presentaciones gerenciales o auditorías fiscales. Esta funcionalidad ahorra horas de trabajo manual y elimina el error humano en los balances de inventario.
        </p>
        <div class="img-container">
            <img src="{{ $reportsImage }}">
            <div class="caption">Herramienta de exportación de datos y auditoría operativa.</div>
        </div>
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h1 class="main-title">Modelos de Inversión y Escalamiento</h1>
        <div class="price-table">
            <div class="price-row">
                <span class="price-name">IMPLEMENTACIÓN INTEGRAL DE SISTEMA</span>
                <span class="price-val">$2,500 - $4,300 USD</span>
                <p style="margin-top: 5px; font-size: 13px;">Incluye el despliegue en servidor de producción, migración inicial de datos, capacitación técnica para el personal operativo, configuración de seguridad de datos y soporte técnico especializado por un periodo de 3 meses tras la entrega.</p>
            </div>
            <div class="price-row">
                <span class="price-name">ADQUISICIÓN TOTAL DEL CÓDIGO FUENTE</span>
                <span class="price-val">$7,000 - $11,000 USD</span>
                <p style="margin-top: 5px; font-size: 13px;">Ideal para empresas que buscan propiedad intelectual total. Incluye la transferencia de todos los repositorios Git, documentación de arquitectura escalable, esquemas de base de datos y la libertad comercial para modificar o revender el software sin restricciones.</p>
            </div>
        </div>
    </div>

    <div class="section" style="background-color: #f0fdf4; padding: 25px; border-radius: 12px; border: 1px solid #dcfce7;">
        <h3 style="margin-top:0; color: #166534; text-transform: uppercase;">Conclusión</h3>
        <p style="margin-bottom:0; font-size: 14px; color: #166534; font-weight: 500;">
            El sistema de Maikel Cars no es simplemente una herramienta digital, es el cimiento de una empresa moderna y competitiva. Su escalabilidad asegura que, a medida que el negocio crezca, el software evolucionará para soportar mayores volúmenes de inventario y operaciones, protegiendo así su inversión a largo plazo.
        </p>
    </div>
</div>

<div class="footer">
    DOCUMENTO CONFIDENCIAL - MAIKEL CARS - PROPIEDAD INTELECTUAL &copy; {{ date('Y') }}
</div>

</body>
</html>
