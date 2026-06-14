<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Activa tu cuenta</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .code { font-size: 32px; font-weight: bold; letter-spacing: 5px; background: #f4f4f4; padding: 15px; text-align: center; }
        .btn { display: inline-block; background: #4a6cf7; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
        .footer { margin-top: 20px; font-size: 12px; color: #666; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h2>¡Gracias por tu compra, <?= esc($name) ?>!</h2>
        <p>Hemos creado una cuenta para ti en <strong><?= esc($storeName) ?></strong>.</p>
        <p>Para futuras compras, puedes iniciar sesión fácilmente sin contraseña. Usa el siguiente código de acceso:</p>
        
        <div class="code"><?= $code ?></div>
        
        <p>Este código expirará en 15 minutos.</p>
        <p>También puedes hacer clic en el siguiente enlace para activar tu cuenta y acceder directamente:</p>
        <p><a href="<?= $loginUrl ?>" class="btn">Iniciar sesión</a></p>
        <p>Si no reconoces esta actividad, ignora este mensaje.</p>
        
        <div class="footer">
            <p>&copy; <?= date('Y') ?> <?= esc($storeName) ?>. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>