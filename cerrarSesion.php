<?php
session_start();

// Destruir la sesión completamente
session_unset();
session_destroy();

// Eliminar cookies de sesión
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Headers para prevenir caché y mantener segurididad
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// Redirigir a index.html
header("Location: index.html");
exit;
?>
