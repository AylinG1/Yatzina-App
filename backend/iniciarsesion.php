<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();
// Forzar JSON y permitir CORS simples + preflight
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Responder a preflight OPTIONS para evitar 405 cuando el navegador lo solicita
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Incluye la conexión a la DB (Azure)
include "conexion.php";

$data = json_decode(file_get_contents("php://input"), true);

$credencial = $data["credencial"] ?? null;
$password = $data["contrasena"] ?? null;

if (!$credencial || !$password) {
    echo json_encode(["status" => "error", "msg" => "Faltan credenciales."]);
    exit;
}

// Determinar si la credencial es un correo o un nombre de usuario
$is_email = filter_var($credencial, FILTER_VALIDATE_EMAIL);

if ($is_email) {
    $sql = "SELECT id, nombre_usuario, correo, contrasena, rol FROM usuarios WHERE correo = ?";
} else {
    $sql = "SELECT id, nombre_usuario, correo, contrasena, rol FROM usuarios WHERE nombre_usuario = ?";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $credencial);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario_db = $resultado->fetch_assoc();

if (!$usuario_db) {
    echo json_encode(["status" => "error", "msg" => "Usuario o contraseña incorrectos."]);
    exit;
}

// Verificar la contraseña usando el hash almacenado
if (password_verify($password, $usuario_db["contrasena"])) {
    // ÉXITO: Guardar en sesión
    $_SESSION['user_id'] = $usuario_db["id"];
    $_SESSION['user_rol'] = $usuario_db["rol"];
    
    // Determinar página de redirección según el rol
    // Si el rol es NULL pedimos selector; si es alumno, redirigimos a la interfaz dentro de la carpeta alumno; si es maestro, interfaz_maestro
    if ($usuario_db["rol"] === NULL) {
        $pagina_destino = "selector_rol.html";
    } elseif (strtolower($usuario_db["rol"]) === 'alumno') {
        $pagina_destino = "alumno/interfaz_alumno.html";
    } else {
        $pagina_destino = "interfaz_maestro.html";
    }
    
    echo json_encode([
        "status" => "ok",
        "msg" => "¡Bienvenido/a " . $usuario_db["nombre_usuario"] . "!",
        "usuario_id" => $usuario_db["id"],
        "redirect_to" => $pagina_destino
    ]);
} else {
    // FALLO DE VERIFICACIÓN
    echo json_encode(["status" => "error", "msg" => "Usuario o contraseña incorrectos."]);
}

$stmt->close();
$conn->close();
?>