# Configuración del Servidor de Correo para Yätzina

## Opción 1: Configurar XAMPP con Gmail (Recomendado para desarrollo)

### Paso 1: Editar php.ini

1. Abre XAMPP Control Panel
2. Click en "Config" al lado de Apache
3. Selecciona "PHP (php.ini)"
4. Busca la sección `[mail function]` y configura:

```ini
[mail function]
SMTP=smtp.gmail.com
smtp_port=587
sendmail_from=tu_correo@gmail.com
sendmail_path = "\"C:\xampp\sendmail\sendmail.exe\" -t"
```

### Paso 2: Editar sendmail.ini

1. Ve a la carpeta: `C:\xampp\sendmail\`
2. Abre el archivo `sendmail.ini`
3. Configura estas líneas:

```ini
[sendmail]

smtp_server=smtp.gmail.com
smtp_port=587
error_logfile=error.log
debug_logfile=debug.log
auth_username=tu_correo@gmail.com
auth_password=tu_contraseña_de_aplicacion
force_sender=tu_correo@gmail.com
```

### Paso 3: Generar Contraseña de Aplicación de Gmail

**IMPORTANTE:** No uses tu contraseña normal de Gmail. Debes generar una "Contraseña de Aplicación":

1. Ve a tu cuenta de Google: https://myaccount.google.com/
2. Ve a "Seguridad"
3. Activa "Verificación en dos pasos" si no la tienes
4. Busca "Contraseñas de aplicaciones"
5. Selecciona "Correo" y "Windows Computer"
6. Copia la contraseña de 16 caracteres generada
7. Pégala en `auth_password` del archivo `sendmail.ini`

### Paso 4: Reiniciar Apache

1. En XAMPP Control Panel, detén Apache
2. Inicia Apache nuevamente

---

## Opción 2: Usar PHPMailer (Más profesional y confiable)

Esta es una mejor opción para producción. Te permite usar SMTP de forma más robusta.

### Instalación con Composer:

1. Abre PowerShell en la carpeta del proyecto
2. Ejecuta:
```bash
composer require phpmailer/phpmailer
```

### Si no tienes Composer, descarga manual:

1. Descarga PHPMailer desde: https://github.com/PHPMailer/PHPMailer/releases
2. Extrae la carpeta en: `C:\xampp\htdocs\Yatzina-App\backend\PHPMailer\`

### Crear archivo de configuración de correo:

Crea `backend/email_config.php`:

```php
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function enviarCorreo($destinatario, $nombre_destinatario, $asunto, $mensaje) {
    $mail = new PHPMailer(true);
    
    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'tu_correo@gmail.com'; // Tu correo
        $mail->Password = 'tu_contraseña_de_aplicacion'; // Contraseña de aplicación
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        
        // Remitente
        $mail->setFrom('noreply@yatzina.com', 'Yätzina App');
        
        // Destinatario
        $mail->addAddress($destinatario, $nombre_destinatario);
        
        // Contenido
        $mail->isHTML(false);
        $mail->Subject = $asunto;
        $mail->Body = $mensaje;
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Error al enviar correo: {$mail->ErrorInfo}");
        return false;
    }
}
?>
```

### Actualizar recuperar_contrasena.php para usar PHPMailer:

Reemplaza la sección de envío de correo con:

```php
// Incluir la función de envío de correo
require_once 'email_config.php';

// Intentar enviar el correo
if (enviarCorreo($correo_usuario, $nombre_usuario, $asunto, $mensaje)) {
    echo json_encode([
        "status" => "ok",
        "msg" => "Se ha enviado un enlace de restablecimiento a tu correo electrónico."
    ]);
} else {
    echo json_encode([
        "status" => "ok",
        "msg" => "Si el correo existe en nuestro sistema, recibirás un enlace de restablecimiento."
    ]);
}
```

---

## Opción 3: Servicio de correo para pruebas (Mailtrap)

Para desarrollo sin configurar Gmail:

1. Crea cuenta gratuita en: https://mailtrap.io/
2. Obtén las credenciales SMTP
3. Configura en `sendmail.ini` o PHPMailer:

```ini
smtp_server=sandbox.smtp.mailtrap.io
smtp_port=2525
auth_username=tu_usuario_mailtrap
auth_password=tu_password_mailtrap
```

**Ventaja:** Todos los correos se capturan en Mailtrap, no se envían realmente. Perfecto para pruebas.

---

## Opción 4: Servicios Profesionales (Para producción)

- **SendGrid** - https://sendgrid.com/ (100 correos/día gratis)
- **Mailgun** - https://www.mailgun.com/ (5,000 correos/mes gratis)
- **Amazon SES** - https://aws.amazon.com/ses/ (62,000 correos/mes gratis)

---

## Verificar que funciona

Crea un archivo de prueba `backend/test_email.php`:

```php
<?php
$to = "tu_correo_de_prueba@example.com";
$subject = "Prueba desde Yätzina";
$message = "Este es un correo de prueba desde la aplicación Yätzina.";
$headers = "From: noreply@yatzina.com\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

if (mail($to, $subject, $message, $headers)) {
    echo "Correo enviado exitosamente!";
} else {
    echo "Error al enviar el correo.";
}
?>
```

Accede a: `http://localhost/Yatzina-App/backend/test_email.php`

---

## Recomendación Final

**Para desarrollo local:** Usa Mailtrap (Opción 3)
**Para producción:** Usa PHPMailer con SendGrid o Gmail (Opción 2)

¿Qué opción prefieres que configuremos juntos?
