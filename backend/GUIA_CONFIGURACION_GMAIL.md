# ⚙️ CONFIGURACIÓN PASO A PASO - XAMPP + GMAIL

## 📋 PASO 1: Obtener Contraseña de Aplicación de Gmail

1. **Ve a tu cuenta de Google:** https://myaccount.google.com/
2. **Haz clic en "Seguridad"** en el menú izquierdo
3. **Activa la "Verificación en 2 pasos"** (si no la tienes activada):
   - Busca "Verificación en 2 pasos"
   - Sigue el proceso de activación
   - Usa tu teléfono para recibir códigos

4. **Genera una Contraseña de Aplicación**:
   - Busca "Contraseñas de aplicaciones" (aparece después de activar 2FA)
   - En "Selecciona la app", elige: **Correo**
   - En "Selecciona el dispositivo", elige: **Windows Computer**
   - Haz clic en **Generar**
   - Copia la contraseña de 16 caracteres (ejemplo: `abcd efgh ijkl mnop`)
   - **¡GUÁRDALA! La necesitarás en el siguiente paso**

---

## 📝 PASO 2: Configurar php.ini

1. **Abre XAMPP Control Panel**
2. **Haz clic en "Config"** al lado de **Apache**
3. **Selecciona "PHP (php.ini)"**
4. **Busca la sección `[mail function]`** (Ctrl+F para buscar)
5. **Reemplaza esas líneas con esto:**

```ini
[mail function]
; Para Win32 únicamente.
SMTP=smtp.gmail.com
smtp_port=587

; Para Win32 únicamente.
sendmail_from=TU_CORREO@gmail.com

; Para Unix únicamente. También puedes proporcionar argumentos aquí
; (el predeterminado es "sendmail -t -i").
sendmail_path = "\"C:\xampp\sendmail\sendmail.exe\" -t"

; Forzar el parámetro To adicional para pasar a sendmail
mail.add_x_header=Off
```

**⚠️ IMPORTANTE:** Reemplaza `TU_CORREO@gmail.com` con tu correo real.

6. **Guarda el archivo** (Ctrl+S)

---

## 📧 PASO 3: Configurar sendmail.ini

1. **Abre el explorador de archivos**
2. **Ve a:** `C:\xampp\sendmail\`
3. **Abre el archivo `sendmail.ini`** con un editor de texto
4. **Busca y modifica estas líneas:**

```ini
[sendmail]

; Configuración del servidor SMTP
smtp_server=smtp.gmail.com
smtp_port=587

; Configuración de autenticación
auth_username=TU_CORREO@gmail.com
auth_password=TU_CONTRASEÑA_DE_APLICACION

; Remitente forzado
force_sender=TU_CORREO@gmail.com

; Archivos de log
error_logfile=error.log
debug_logfile=debug.log

; Configuración adicional
hostname=localhost
```

**⚠️ REEMPLAZA:**
- `TU_CORREO@gmail.com` → Tu correo de Gmail completo
- `TU_CONTRASEÑA_DE_APLICACION` → La contraseña de 16 caracteres del Paso 1 (sin espacios: `abcdefghijklmnop`)

5. **Guarda el archivo** (Ctrl+S)

---

## 🔄 PASO 4: Reiniciar Apache

1. **Vuelve a XAMPP Control Panel**
2. **Haz clic en "Stop"** en Apache
3. **Espera unos segundos**
4. **Haz clic en "Start"** en Apache

---

## ✅ PASO 5: Probar la Configuración

1. **Edita el archivo de prueba:**
   - Abre: `C:\xampp\htdocs\Yatzina-App\backend\test_email.php`
   - En la línea 5, cambia: `$to = "tu_correo_de_prueba@gmail.com";`
   - Pon tu correo real donde quieres recibir el correo de prueba

2. **Abre tu navegador y ve a:**
   ```
   http://localhost/Yatzina-App/backend/test_email.php
   ```

3. **Deberías ver:**
   - ✅ "Correo enviado exitosamente!" en verde

4. **Revisa tu correo:**
   - Abre Gmail
   - Busca el correo de prueba
   - **Si no lo ves en la bandeja de entrada, revisa SPAM**

---

## 🔍 Solución de Problemas

### Si ves un error o no llega el correo:

**1. Verifica los archivos de log:**
   - Abre: `C:\xampp\sendmail\error.log`
   - Abre: `C:\xampp\sendmail\debug.log`
   - Busca mensajes de error

**2. Errores comunes:**

❌ **"Authentication failed"**
   - La contraseña de aplicación está mal
   - Verifica que copiaste los 16 caracteres sin espacios

❌ **"Connection timeout"**
   - Tu firewall o antivirus está bloqueando la conexión
   - Desactiva temporalmente el antivirus para probar

❌ **"535-5.7.8 Username and Password not accepted"**
   - La verificación en 2 pasos no está activada
   - La contraseña de aplicación no está generada correctamente

**3. Alternativa si Gmail no funciona:**
   - Usa Mailtrap (más fácil): https://mailtrap.io/
   - Solo crea cuenta y copia las credenciales SMTP

---

## 📌 Checklist Final

- [ ] Contraseña de aplicación de Gmail generada
- [ ] php.ini editado y guardado
- [ ] sendmail.ini editado y guardado
- [ ] Apache reiniciado
- [ ] test_email.php editado con tu correo
- [ ] Prueba realizada desde el navegador
- [ ] Correo recibido (verificar spam también)

---

## 🎯 ¿Todo funciona?

Si recibes el correo de prueba, ¡ya está listo! 🎉

Ahora puedes usar la función "¿Olvidaste tu contraseña?" en la aplicación y recibirás el enlace de restablecimiento por correo.

---

## 📞 ¿Necesitas ayuda?

Si algo no funciona, comparte:
1. El mensaje de error que ves
2. El contenido de `C:\xampp\sendmail\error.log`
3. La configuración que pusiste (sin incluir tu contraseña)
