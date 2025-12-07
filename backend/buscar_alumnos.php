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
    echo json_encode(["status" => "error", "msg" => "JSON inválido"]);
    exit;
}

// Obtener el término de búsqueda
$buscar = $data["buscar"] ?? null;

if (!$buscar || strlen($buscar) < 2) {
    echo json_encode(["status" => "ok", "sugerencias" => []]);
    $conn->close();
    exit;
}

// Limpiar el término para evitar inyecciones SQL
$buscar = trim($buscar);
$buscar_like = "%$buscar%";

// Buscar usuarios por nombre de usuario o correo (máximo 10 resultados)
$sql = "SELECT id, nombre_usuario, correo FROM usuarios 
        WHERE (nombre_usuario LIKE ? OR correo LIKE ?)
        LIMIT 10";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["status" => "error", "msg" => "Error en la consulta"]);
    $conn->close();
    exit;
}

$stmt->bind_param("ss", $buscar_like, $buscar_like);
$stmt->execute();
$resultado = $stmt->get_result();

$sugerencias = [];
while ($row = $resultado->fetch_assoc()) {
    $sugerencias[] = [
        "id" => $row['id'],
        "nombre_usuario" => $row['nombre_usuario'],
        "correo" => $row['correo'],
        "display" => $row['nombre_usuario'] . " (" . $row['correo'] . ")"
    ];
}

$stmt->close();
$conn->close();

echo json_encode([
    "status" => "ok",
    "sugerencias" => $sugerencias
]);
?>
