<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

// Verificar si hay sesión activa y el usuario es maestro
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'maestro') {
    echo json_encode([
        "success" => false,
        "message" => "Sesión expirada o no autorizado"
    ]);
    exit;
}

echo json_encode([
    "success" => true,
    "user_id" => $_SESSION['user_id'],
    "nombre" => $_SESSION['user_nombre'] ?? 'Usuario'
]);
?>
