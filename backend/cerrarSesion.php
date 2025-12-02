<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();

// Destruir la sesión completamente
session_unset();
session_destroy();

// Eliminar la cookie de sesión
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Evitar cache en el cliente
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
header('Content-Type: application/json');

// Responder en JSON para la llamada fetch desde el frontend
echo json_encode(['success' => true, 'message' => 'Sesión cerrada']);
exit;
?>
