<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Código de acceso</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .code { font-size: 32px; font-weight: bold; letter-spacing: 5px; background: #f4f4f4; padding: 15px; text-align: center; }
        .footer { margin-top: 20px; font-size: 12px; color: #666; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hola <?= esc($name) ?: 'cliente' ?>,</h2>
        <p>Has solicitado acceder a <strong><?= esc($storeName) ?></strong>. Usa el siguiente código para iniciar sesión:</p>
        
        <div class="code"><?= $code ?></div>
        
        <p>Este código expirará en 15 minutos.</p>
        <p>Si no solicitaste este acceso, ignora este mensaje.</p>
        
        <div class="footer">
            <p>&copy; <?= date('Y') ?> <?= esc($storeName) ?>. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>