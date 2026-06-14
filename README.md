# LP Store - Tienda Online Profesional

LP Store es un sistema de tienda online desarrollado en CodeIgniter 4, con autenticación sin contraseña (OTP), carrito de compras, integración con Mercado Pago (Checkout Bricks), y panel de usuario completo.

## Requisitos

- PHP 8.2 o superior
- MySQL 8.0 o superior
- Composer
- Apache / Nginx

## Instalación

1. Clonar el repositorio
2. Copiar `env` a `.env` y configurar:
   - `CI_ENVIRONMENT` (development / production)
   - Base de datos (host, name, user, password)
   - `MERCADOPAGO_ACCESS_TOKEN`
   - `MERCADOPAGO_PUBLIC_KEY`
   - Email SMTP (para envío de códigos OTP)
3. Instalar dependencias: `composer install`
4. Ejecutar migraciones: `php spark migrate`
5. Configurar el servidor web para apuntar a `/public`

## Configuración

El archivo `.env` contiene todas las variables necesarias. Las más importantes:

### Mercado Pago
```
MERCADOPAGO_ACCESS_TOKEN = "APP_USR-xxxxx"
MERCADOPAGO_PUBLIC_KEY = "APP_USR-xxxxx"
```

### Email (para envío de códigos OTP)
```
email.SMTPHost = smtp.gmail.com
email.SMTPUser = tuemail@gmail.com
email.SMTPPass = tucontraseña
email.SMTPPort = 587
email.SMTPCrypto = tls
```

### Datos de la tienda
```
Config\LpStore.storeName = "Mi Tienda"
Config\LpStore.storeDescription = "Tu tienda online de confianza"
Config\LpStore.storeLogo = "logo.png"
Config\LpStore.storeEmail = "contacto@dominio.com"
```

### Sucursales
Las sucursales se configuran en .env como un array JSON de la siguiente manera:
```
Config\LpStore.branches = '[{ "streetAddress": "Av. 18 de Julio 1234", "addressLocality": "Montevideo", "addressCountry": "Uruguay", "phone": "+598 2900 1234", "schedule": [{"days":["Monday","Tuesday","Wednesday","Thursday","Friday"],"opens":"09:00","closes":"18:00"}], "isMain": true }]'
```

## Licencia

MIT