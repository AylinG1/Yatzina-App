# 📧 Configuración de Recuperación de Contraseña

## ⚠️ IMPORTANTE: Debes configurar el envío de correos

Para que funcione la recuperación de contraseña, necesitas configurar un servidor SMTP.

## 🚀 Opción Recomendada: Brevo (Gratis - 300 emails/día)

### Paso 1: Crear cuenta en Brevo
1. Ve a https://www.brevo.com y crea una cuenta gratuita
2. Verifica tu email

### Paso 2: Obtener credenciales SMTP
1. Entra al panel de Brevo
2. Ve a **Settings** (⚙️) → **SMTP & API**
3. Copia las credenciales que aparecen:
   - **SMTP Server**: `smtp-relay.brevo.com`
   - **Port**: `587`
   - **Login**: tu email de Brevo
   - **SMTP Key**: copia la clave que te muestran

### Paso 3: Configurar en tu proyecto
1. Ve a `backend/email_config.example.php`
2. Copia el archivo y renómbralo como `backend/email_config.php`
3. Edita `backend/email_config.php` con tus credenciales:

```php
return [
    'smtp_host' => 'smtp-relay.brevo.com',
    'smtp_port' => 587,
    'smtp_secure' => 'tls',
    'smtp_auth' => true,
    
    // TUS CREDENCIALES DE BREVO
    'smtp_username' => 'tu-email@ejemplo.com',  // Tu email de Brevo
    'smtp_password' => 'tu-clave-smtp-de-brevo', // La clave SMTP que copiaste
    
    'from_email' => 'noreply@yatzina.com',
    'from_name' => 'Yätzina - Plataforma Educativa',
    'charset' => 'UTF-8',
    'timeout' => 10,
];
```

### Paso 4: Probar
1. Ve a la página de login
2. Haz clic en "¿Olvidaste tu contraseña?"
3. Ingresa un correo registrado
4. Revisa tu bandeja de entrada (y spam)

---

## 📧 Alternativa: Gmail (Solo para desarrollo)

### Paso 1: Generar Contraseña de Aplicación
1. Ve a https://myaccount.google.com/apppasswords
2. Selecciona "Correo" y "Otro (nombre personalizado)"
3. Escribe "Yatzina" y genera la contraseña
4. **Copia la contraseña de 16 dígitos**

### Paso 2: Configurar
En `backend/email_config.php`:

```php
return [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_secure' => 'tls',
    'smtp_auth' => true,
    
    'smtp_username' => 'tu-correo@gmail.com',
    'smtp_password' => 'xxxx xxxx xxxx xxxx',  // Contraseña de aplicación (sin espacios)
    
    'from_email' => 'tu-correo@gmail.com',
    'from_name' => 'Yätzina',
    'charset' => 'UTF-8',
    'timeout' => 10,
];
```

⚠️ **Nota**: Gmail tiene límite de 500 emails/día y puede bloquear si detecta spam.

---

## 🔧 Verificar que funciona

### Logs de error
Si no funciona, revisa los logs en:
- **Local**: `C:\xampp\php\logs\php_error_log`
- **Azure**: Portal → App Service → Log stream

### Probar manualmente
Puedes probar el envío ejecutando:

```bash
curl -X POST https://tu-app.azurewebsites.net/backend/recuperar_contrasena.php \
  -H "Content-Type: application/json" \
  -d '{"email":"correo-registrado@ejemplo.com"}'
```

---

## 📦 Archivos del sistema

- `backend/email_config.php` - Tu configuración (NO subir a Git)
- `backend/email_config.example.php` - Plantilla de ejemplo
- `backend/email_helper.php` - Función para enviar emails via SMTP
- `backend/recuperar_contrasena.php` - Genera token y envía email
- `backend/procesar_reseteo.php` - Valida token y cambia contraseña
- `crearnuevacontrasena.html` - Formulario para nueva contraseña

---

## 🐛 Solución de problemas

### "No se pudo enviar el correo"
- Verifica que `email_config.php` existe y tiene credenciales válidas
- Revisa los logs de PHP
- Prueba las credenciales manualmente con un cliente de correo

### "El enlace no funciona"
- Verifica que la URL en el correo sea correcta
- Comprueba que la tabla `password_reset_tokens` existe en la BD

### "Token expirado"
- Los tokens expiran en 1 hora
- Solicita un nuevo enlace de recuperación

---

## ✅ Checklist de configuración

- [ ] Crear cuenta en Brevo (o Gmail)
- [ ] Obtener credenciales SMTP
- [ ] Copiar `email_config.example.php` → `email_config.php`
- [ ] Configurar credenciales en `email_config.php`
- [ ] Probar recuperación de contraseña
- [ ] Verificar que el correo llega
- [ ] Probar cambio de contraseña con el enlace

---

¿Necesitas ayuda? Revisa los logs o contacta al desarrollador.
