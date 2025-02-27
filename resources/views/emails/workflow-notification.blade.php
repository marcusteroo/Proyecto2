<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
        }
        .header {
            background-color: #0078d4;
            color: white;
            padding: 10px 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{ $email_subject }}</h2>
        </div>
        <div class="content">
            {!! nl2br(e($email_message)) !!}
        </div>
        <div class="footer">
            <p>Este es un correo automático enviado desde el sistema de automatizaciones.</p>
            <p>Por favor no responda a este correo.</p>
        </div>
    </div>
</body>
</html>