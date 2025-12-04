# 🔧 INSTRUCCIONES FINALES - CONFIGURACIÓN COMPLETA

## ✅ Lo que ya está listo:

1. ✅ `iniciarsesion.html` - El enlace "¿Olvidaste tu contraseña?" ahora redirige a `recuperarcontraseña.html`
2. ✅ `recuperarcontraseña.html` - Página funcional con formulario para ingresar correo
3. ✅ `recuperar.js` - JavaScript que maneja el envío del formulario
4. ✅ `backend/recuperar_contrasena.php` - Backend que genera tokens y envía correos
5. ✅ `resetear_contrasena.html` - Página para restablecer la contraseña
6. ✅ `resetear_contrasena.js` - JavaScript para el formulario de nueva contraseña
7. ✅ `backend/procesar_reseteo.php` - Backend que procesa el cambio de contraseña
8. ✅ Configuración de correo con Gmail completada

---

## 🗄️ PASO IMPORTANTE: Crear la tabla en la base de datos

Necesitas ejecutar el siguiente SQL en tu base de datos Azure MySQL:

```sql
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    fecha_expiracion DATETIME NOT NULL,
    usado TINYINT(1) DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_user_id (user_id),
    INDEX idx_expiracion (fecha_expiracion)
);
```

### Cómo ejecutar el SQL en Azure:

**Opción 1: Desde Azure Portal**
1. Ve a https://portal.azure.com/
2. Busca tu servidor MySQL: `bd-yatzina`
3. Ve a "Query editor" o "Editor de consultas"
4. Ingresa tus credenciales
5. Pega el SQL de arriba
6. Ejecuta (Run)

**Opción 2: Desde MySQL Workbench o phpMyAdmin**
1. Conecta a tu base de datos Azure
2. Selecciona la base de datos `yatzinaapp`
3. Pega el SQL
4. Ejecuta

**Opción 3: Desde la terminal con MySQL**
```bash
mysql -h bd-yatzina.mysql.database.azure.com -u adminyatzina -p yatzinaapp < backend/crear_tabla_tokens.sql
```

---

## 🧪 PRUEBA EL FLUJO COMPLETO:

### 1. Probar Recuperación de Contraseña:
- Ve a: `http://localhost/Yatzina-App/iniciarsesion.html`
- Click en "¿Olvidaste tu contraseña?"
- Te llevará a `recuperarcontraseña.html`
- Ingresa un correo registrado (ej: `2330471@upt.edu.mx`)
- Click en "¡Enviar Instrucciones!"
- Deberías recibir un correo con un enlace

### 2. Probar Restablecimiento:
- Abre el correo que recibiste
- Click en el enlace (será algo como: `http://localhost/Yatzina-App/resetear_contrasena.html?token=...`)
- Ingresa tu nueva contraseña (mínimo 8 caracteres)
- Confirma la contraseña
- Click en "Cambiar Contraseña"
- Te redirigirá al login automáticamente

### 3. Probar Inicio de Sesión:
- Inicia sesión con tu nueva contraseña
- ¡Debería funcionar! 🎉

---

## 🔍 Solución de Problemas:

### ❌ Error: "No se encontró ninguna cuenta con ese correo"
- Verifica que el correo esté registrado en la base de datos
- Revisa la tabla `usuarios` en tu BD

### ❌ Error: "El enlace de restablecimiento no es válido"
- La tabla `password_reset_tokens` no existe → Ejecuta el SQL
- El token expiró (válido por 1 hora) → Solicita uno nuevo

### ❌ No llega el correo
- Verifica la configuración de Gmail en `sendmail.ini`
- Revisa `C:\xampp\sendmail\error.log`
- Prueba con: `http://localhost/Yatzina-App/backend/test_email.php`

---

## 📁 Archivos del Sistema de Recuperación:

### Frontend:
- `iniciarsesion.html` - Página de login
- `recuperarcontraseña.html` - Formulario para solicitar recuperación
- `resetear_contrasena.html` - Formulario para nueva contraseña
- `login.js` - JavaScript del login
- `recuperar.js` - JavaScript de recuperación
- `resetear_contrasena.js` - JavaScript de restablecimiento

### Backend:
- `backend/recuperar_contrasena.php` - Genera token y envía correo
- `backend/procesar_reseteo.php` - Procesa el cambio de contraseña
- `backend/crear_tabla_tokens.sql` - Script para crear la tabla

### Configuración:
- `C:\xampp\php\php.ini` - Configuración de PHP para correo
- `C:\xampp\sendmail\sendmail.ini` - Configuración de sendmail

---

## 🎯 Checklist Final:

- [ ] Tabla `password_reset_tokens` creada en Azure
- [ ] Servidor Apache reiniciado
- [ ] Probado flujo completo de recuperación
- [ ] Correo recibido exitosamente
- [ ] Contraseña cambiada exitosamente
- [ ] Login con nueva contraseña funciona

---

¡Todo listo! 🚀 El sistema de recuperación de contraseña está completamente implementado y es seguro.
