<?php
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

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
    $pagina_destino = $usuario_db["rol"] === NULL 
                        ? "selector_rol.html" 
                        : ($usuario_db["rol"] === 'alumno' ? "interfaz_alumno.html" : "interfaz_maestro.html");
    
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