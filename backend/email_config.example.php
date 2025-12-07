<?php
/**
 * ARCHIVO DE EJEMPLO - email_config.php
 * 
 * Copia este archivo como "email_config.php" y configura tus credenciales SMTP.
 * 
 * OPCIONES RECOMENDADAS:
 * 
 * 1. GMAIL (Desarrollo):
 *    - Ve a: https://myaccount.google.com/apppasswords
 *    - Genera una "Contraseña de aplicación"
 *    - Usa: smtp.gmail.com, puerto 587, TLS
 * 
 * 2. BREVO/SENDINBLUE (Producción - GRATIS 300 emails/día):
 *    - Regístrate en: https://www.brevo.com
 *    - Ve a: Settings > SMTP & API
 *    - Copia las credenciales SMTP
 *    - Usa: smtp-relay.brevo.com, puerto 587, TLS
 * 
 * 3. MAILGUN (Producción):
 *    - Regístrate en: https://www.mailgun.com
 *    - 5,000 emails gratis al mes
 * 
 * 4. SENDGRID (Producción):
 *    - 100 emails/día gratis
 */

return [
    // Configuración SMTP
    'smtp_host' => 'smtp.gmail.com',  // Cambiar según proveedor
    'smtp_port' => 587,                // 587 (TLS) o 465 (SSL)
    'smtp_secure' => 'tls',            // 'tls' o 'ssl'
    'smtp_auth' => true,
    
    // ⚠️ CAMBIAR POR TUS CREDENCIALES REALES ⚠️
    'smtp_username' => 'tu-email@gmail.com',
    'smtp_password' => 'tu-contraseña-de-aplicacion',
    
    // Información del remitente
    'from_email' => 'noreply@yatzina.com',
    'from_name' => 'Yätzina - Plataforma Educativa',
    
    // Configuración adicional
    'charset' => 'UTF-8',
    'timeout' => 10,
];
?>
