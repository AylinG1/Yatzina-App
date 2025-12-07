<?php
error_reporting(0);
ini_set('display_errors', 0);

// Headers JSON y CORS
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Responder a preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Incluir la conexión a la base de datos
include "conexion.php";

// Leer el cuerpo crudo y decodificar JSON
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

// Validar JSON
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(["status" => "error", "msg" => "JSON inválido: " . json_last_error_msg()]);
    exit;
}

// Obtener los datos
$token = $data["token"] ?? null;
$nueva_contrasena = $data["nueva_contrasena"] ?? null;

if (!$token || !$nueva_contrasena) {
    http_response_code(400);
    echo json_encode(["status" => "error", "msg" => "Faltan datos requeridos."]);
    exit;
}

// Validar longitud de contraseña
if (strlen($nueva_contrasena) < 8) {
    http_response_code(400);
    echo json_encode(["status" => "error", "msg" => "La contraseña debe tener al menos 8 caracteres."]);
    exit;
}

// Buscar el token en la base de datos
$sql = "SELECT user_id, fecha_expiracion, usado FROM password_reset_tokens WHERE token = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["status" => "error", "msg" => "El enlace de restablecimiento no es válido."]);
    $stmt->close();
    $conn->close();
    exit;
}

$token_data = $resultado->fetch_assoc();
$user_id = $token_data['user_id'];
$fecha_expiracion = $token_data['fecha_expiracion'];
$usado = $token_data['usado'];

$stmt->close();

// Verificar si el token ya fue usado
if ($usado == 1) {
    http_response_code(400);
    echo json_encode(["status" => "error", "msg" => "Este enlace ya ha sido utilizado."]);
    $conn->close();
    exit;
}

// Verificar si el token ha expirado
$fecha_actual = date('Y-m-d H:i:s');
if ($fecha_actual > $fecha_expiracion) {
    http_response_code(400);
    echo json_encode(["status" => "error", "msg" => "Este enlace ha expirado. Solicita uno nuevo."]);
    $conn->close();
    exit;
}

// Hashear la nueva contraseña
$contrasena_hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);

// Actualizar la contraseña del usuario
$sql_update = "UPDATE usuarios SET contrasena = ? WHERE id = ?";
$stmt_update = $conn->prepare($sql_update);
$stmt_update->bind_param("si", $contrasena_hash, $user_id);

if (!$stmt_update->execute()) {
    http_response_code(500);
    echo json_encode(["status" => "error", "msg" => "Error al actualizar la contraseña. Intenta nuevamente."]);
    $stmt_update->close();
    $conn->close();
    exit;
}

$stmt_update->close();

// Marcar el token como usado
$sql_mark = "UPDATE password_reset_tokens SET usado = 1 WHERE token = ?";
$stmt_mark = $conn->prepare($sql_mark);
$stmt_mark->bind_param("s", $token);
$stmt_mark->execute();
$stmt_mark->close();

$conn->close();

// Respuesta exitosa
echo json_encode([
    "status" => "ok",
    "msg" => "¡Tu contraseña ha sido actualizada exitosamente! Ahora puedes iniciar sesión con tu nueva contraseña."
]);
?>
