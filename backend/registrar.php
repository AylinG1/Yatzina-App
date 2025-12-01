<?php
// Iniciar sesión DEBE ser lo primero en el script
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// 1. Incluir la conexión a Azure DB
include "conexion.php";

$data = json_decode(file_get_contents("php://input"), true);

// 2. Recolección y validación de datos
$usuario = $data["usuario"] ?? null;
$correo = $data["correo"] ?? null;
$contrasena_plana = $data["contrasena"] ?? null; // Contraseña sin hashear (plana)

if (!$usuario || !$correo || !$contrasena_plana) {
    http_response_code(400);
    echo json_encode(["status" => "error", "msg" => "Faltan campos obligatorios."]);
    exit;
}

// 3. Validar longitud de contraseña y hashing
if (strlen($contrasena_plana) < 8) {
    http_response_code(400);
    echo json_encode(["status" => "error", "msg" => "La contraseña debe tener al menos 8 caracteres."]);
    exit;
}
$passHash = password_hash($contrasena_plana, PASSWORD_DEFAULT);

// 4. Verificar si el usuario o correo ya existe
$sql_check = "SELECT id FROM usuarios WHERE nombre_usuario = ? OR correo = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ss", $usuario, $correo);
$stmt_check->execute();
$resultado_check = $stmt_check->get_result();

if ($resultado_check->num_rows > 0) {
    http_response_code(409); // Conflict
    echo json_encode(["status" => "error", "msg" => "El usuario o correo ya está registrado."]);
    $stmt_check->close();
    $conn->close();
    exit;
}
$stmt_check->close();

// 5. Insertar nuevo usuario
$sql = "INSERT INTO usuarios (nombre_usuario, correo, contrasena) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $usuario, $correo, $passHash);

if ($stmt->execute()) {
    
    // Obtener el ID del usuario recién insertado
    $last_id = $conn->insert_id;
    
    // Almacenar el ID y el ROL (que es NULL por defecto) en la sesión
    $_SESSION['user_id'] = $last_id;
    $_SESSION['user_rol'] = NULL; // Asignamos NULL ya que la DB lo tiene por defecto

    // Redirección: Siempre va al selector de rol después del primer registro
    $pagina_destino = "selector_rol.html";

    echo json_encode([
        "status" => "ok",
        "msg" => "¡Cuenta Creada con Éxito!",
        "redirect_to" => $pagina_destino
    ]);

} else {
    // Si la ejecución falla por un error de DB (ej. error de SQL)
    http_response_code(500);
    echo json_encode(["status" => "error_db", "msg" => "Error al registrar: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>