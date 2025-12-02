<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include "conexion.php";

// Obtener datos del POST
$data = json_decode(file_get_contents("php://input"), true);

// Verificar que el usuario está en sesión
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "msg" => "No hay sesión iniciada."]);
    exit;
}

$user_id = $_SESSION['user_id'];
$rol = $data["rol"] ?? null;

// Validar que el rol sea válido
if (!in_array($rol, ['alumno', 'maestro'])) {
    echo json_encode(["status" => "error", "msg" => "Rol inválido."]);
    exit;
}

// Actualizar el rol en la base de datos
$sql = "UPDATE usuarios SET rol = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $rol, $user_id);

if ($stmt->execute()) {
    // Actualizar la sesión
    $_SESSION['user_rol'] = $rol;
    
    // Determinar la página de redirección (rutas relativas desde selector_rol.html)
    $pagina_destino = $rol === 'alumno' ? 'alumno/interfaz_alumno.html' : 'interfaz_maestro.html';
    
    echo json_encode([
        "status" => "ok",
        "msg" => "Rol guardado correctamente.",
        "redirect_to" => $pagina_destino
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "msg" => "Error al guardar el rol: " . $conn->error
    ]);
}

$stmt->close();
$conn->close();
?>
