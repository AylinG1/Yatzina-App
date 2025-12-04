-- Tabla para almacenar tokens de recuperación de contraseña
-- Ejecuta este script en tu base de datos Azure MySQL

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

-- Eliminar tokens expirados automáticamente (opcional, ejecutar periódicamente)
-- DELETE FROM password_reset_tokens WHERE fecha_expiracion < NOW() OR usado = 1;
