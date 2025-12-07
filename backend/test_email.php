<?php
// Archivo de prueba para verificar el envío de correos
// Accede a: http://localhost/Yatzina-App/backend/test_email.php

$to = "2330471@upt.edu.mx"; // CAMBIA ESTO por tu correo
$subject = "Prueba de Correo - Yätzina";
$message = "¡Hola!\n\n"; 
$message .= "Este es un correo de prueba desde la aplicación Yätzina.\n\n";
$message .= "Si recibes este mensaje, la configuración de correo está funcionando correctamente.\n\n";
$message .= "Fecha y hora: " . date('Y-m-d H:i:s') . "\n\n";
$message .= "Saludos,\nEquipo Yätzina";

$headers = "From: noreply@yatzina.com\r\n";
$headers .= "Reply-To: soporte@yatzina.com\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

echo "<h2>Prueba de Envío de Correo - Yätzina</h2>";
echo "<p>Intentando enviar correo a: <strong>$to</strong></p>";
echo "<hr>";

if (mail($to, $subject, $message, $headers)) {
    echo "<p style='color: green; font-weight: bold;'>✅ Correo enviado exitosamente!</p>";
    echo "<p>Verifica tu bandeja de entrada (y spam) en: <strong>$to</strong></p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ Error al enviar el correo.</p>";
    echo "<p>Verifica la configuración de PHP y sendmail.</p>";
    echo "<p>Revisa los archivos:</p>";
    echo "<ul>";
    echo "<li>C:\\xampp\\php\\php.ini</li>";
    echo "<li>C:\\xampp\\sendmail\\sendmail.ini</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<h3>Información de Configuración Actual:</h3>";
echo "<pre>";
echo "SMTP: " . ini_get('SMTP') . "\n";
echo "smtp_port: " . ini_get('smtp_port') . "\n";
echo "sendmail_from: " . ini_get('sendmail_from') . "\n";
echo "sendmail_path: " . ini_get('sendmail_path') . "\n";
echo "</pre>";
?>
