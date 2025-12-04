<?php
// Verificar si un correo o usuario ya existe
// Usado para validación en tiempo real del formulario de registro

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include "conexion.php";

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(["status" => "error", "msg" => "JSON inválido"]);
    exit;
}

$tipo = $data["tipo"] ?? null; // "usuario" o "correo"
$valor = $data["valor"] ?? null;

if (!$tipo || !$valor) {
    http_response_code(400);
    echo json_encode(["status" => "error", "msg" => "Parámetros inválidos"]);
    exit;
}

if ($tipo === "usuario") {
    $sql = "SELECT id FROM usuarios WHERE nombre_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $valor);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        echo json_encode(["status" => "existe", "msg" => "El nombre de usuario ya existe"]);
    } else {
        echo json_encode(["status" => "disponible", "msg" => "El nombre de usuario está disponible"]);
    }
    $stmt->close();
} elseif ($tipo === "correo") {
    $sql = "SELECT id FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $valor);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        echo json_encode(["status" => "existe", "msg" => "El correo electrónico ya está registrado"]);
    } else {
        echo json_encode(["status" => "disponible", "msg" => "El correo electrónico está disponible"]);
    }
    $stmt->close();
}

$conn->close();
?>
