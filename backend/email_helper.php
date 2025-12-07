<?php
/**
 * Función simplificada para enviar correos mediante SMTP
 * Compatible con Gmail, Brevo, y otros proveedores SMTP
 */

function enviarEmail($destinatario, $asunto, $mensaje_texto, $mensaje_html = null) {
    // Cargar configuración
    $config = include __DIR__ . '/email_config.php';
    
    // Validar que las credenciales estén configuradas
    if ($config['smtp_username'] === 'tu-email@gmail.com' || empty($config['smtp_password'])) {
        error_log("[enviarEmail] SMTP no configurado. Edita backend/email_config.php");
        return false;
    }
    
    try {
        // Conectar al servidor SMTP
        $smtp = @fsockopen(
            ($config['smtp_secure'] === 'ssl' ? 'ssl://' : '') . $config['smtp_host'],
            $config['smtp_port'],
            $errno,
            $errstr,
            $config['timeout']
        );
        
        if (!$smtp) {
            error_log("[enviarEmail] No se pudo conectar a SMTP: $errstr ($errno)");
            return false;
        }
        
        // Función helper para enviar comandos SMTP
        $sendCommand = function($command, $expectedCode = 250) use ($smtp) {
            fputs($smtp, $command . "\r\n");
            $response = fgets($smtp, 512);
            $code = substr($response, 0, 3);
            
            if ($code != $expectedCode) {
                error_log("[enviarEmail] Error SMTP: $response (esperado: $expectedCode)");
                return false;
            }
            return true;
        };
        
        // Leer banner
        fgets($smtp, 512);
        
        // EHLO
        if (!$sendCommand("EHLO " . $config['smtp_host'], 250)) {
            fclose($smtp);
            return false;
        }
        
        // STARTTLS si es necesario
        if ($config['smtp_secure'] === 'tls') {
            if (!$sendCommand("STARTTLS", 220)) {
                fclose($smtp);
                return false;
            }
            
            stream_socket_enable_crypto($smtp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            
            // EHLO de nuevo después de STARTTLS
            if (!$sendCommand("EHLO " . $config['smtp_host'], 250)) {
                fclose($smtp);
                return false;
            }
        }
        
        // AUTH LOGIN
        if ($config['smtp_auth']) {
            if (!$sendCommand("AUTH LOGIN", 334)) {
                fclose($smtp);
                return false;
            }
            
            if (!$sendCommand(base64_encode($config['smtp_username']), 334)) {
                fclose($smtp);
                return false;
            }
            
            if (!$sendCommand(base64_encode($config['smtp_password']), 235)) {
                fclose($smtp);
                return false;
            }
        }
        
        // MAIL FROM
        if (!$sendCommand("MAIL FROM:<" . $config['from_email'] . ">", 250)) {
            fclose($smtp);
            return false;
        }
        
        // RCPT TO
        if (!$sendCommand("RCPT TO:<$destinatario>", 250)) {
            fclose($smtp);
            return false;
        }
        
        // DATA
        if (!$sendCommand("DATA", 354)) {
            fclose($smtp);
            return false;
        }
        
        // Construir mensaje
        $boundary = "----=_Part_" . md5(uniqid());
        $headers = "From: " . $config['from_name'] . " <" . $config['from_email'] . ">\r\n";
        $headers .= "Reply-To: " . $config['from_email'] . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        
        if ($mensaje_html) {
            $headers .= "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n";
            $body = "--$boundary\r\n";
            $body .= "Content-Type: text/plain; charset=" . $config['charset'] . "\r\n";
            $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $body .= $mensaje_texto . "\r\n\r\n";
            $body .= "--$boundary\r\n";
            $body .= "Content-Type: text/html; charset=" . $config['charset'] . "\r\n";
            $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $body .= $mensaje_html . "\r\n\r\n";
            $body .= "--$boundary--\r\n";
        } else {
            $headers .= "Content-Type: text/plain; charset=" . $config['charset'] . "\r\n";
            $body = $mensaje_texto . "\r\n";
        }
        
        $email = "To: $destinatario\r\n";
        $email .= "Subject: =?UTF-8?B?" . base64_encode($asunto) . "?=\r\n";
        $email .= $headers . "\r\n";
        $email .= $body;
        $email .= "\r\n.\r\n";
        
        fputs($smtp, $email);
        $response = fgets($smtp, 512);
        
        // QUIT
        fputs($smtp, "QUIT\r\n");
        fclose($smtp);
        
        $code = substr($response, 0, 3);
        if ($code == 250) {
            return true;
        } else {
            error_log("[enviarEmail] Error al enviar: $response");
            return false;
        }
        
    } catch (Exception $e) {
        error_log("[enviarEmail] Excepción: " . $e->getMessage());
        return false;
    }
}
?>
