<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #1e293b;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
            border: 1px solid #334155;
        }

        .header {
            background: linear-gradient(135deg, #1e40af, #2563eb);
            padding: 40px 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            color: #ffffff;
            font-size: 24px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .content {
            padding: 40px;
            text-align: center;
        }

        .content p {
            font-size: 16px;
            line-height: 1.6;
            color: #94a3b8;
        }

        .content b {
            color: #f8fafc;
        }

        .button {
            display: inline-block;
            padding: 14px 32px;
            background-color: #2563eb;
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin-top: 25px;
            transition: background 0.3s;
        }

        .button:hover {
            background-color: #1d4ed8;
        }

        .footer {
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #334155;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Maikel Cars</h1>
        </div>
        <div class="content">
            <p>Hola <b>{{ $name }}</b>,</p>
            <p>Has recibido este correo porque solicitaste restablecer la contraseña de tu cuenta en nuestro sistema
                interno.</p>
            <p>Este enlace de recuperación expirará en <b>30 minutos</b>.</p>
            <a href="{{ $url }}" class="button">RESTABLECER CONTRASEÑA</a>
            <p style="margin-top: 30px; font-size: 14px;">Si no realizaste esta solicitud, puedes ignorar este mensaje
                de forma segura.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Maikel Cars. Todos los derechos reservados.
        </div>
    </div>
</body>

</html>