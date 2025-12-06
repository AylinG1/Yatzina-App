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

// Obtener el correo del usuario
$correo = $data["email"] ?? null;

if (!$correo) {
    http_response_code(400);
    echo json_encode(["status" => "error", "msg" => "Por favor, proporciona un correo electrónico."]);
    exit;
}

// Validar formato de correo
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "msg" => "El correo electrónico no tiene un formato válido."]);
    exit;
}

// Buscar el usuario en la base de datos por correo
$sql = "SELECT id, nombre_usuario, correo FROM usuarios WHERE correo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $correo);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    // Para evitar permitir enumeración de cuentas, no revelar si el correo existe.
    // Respondemos con el mismo mensaje genérico que se usa cuando el correo se envía correctamente.
    $stmt->close();
    $conn->close();
    echo json_encode([
        "status" => "ok",
        "msg" => "Si el correo existe en nuestro sistema, recibirás un enlace de restablecimiento."
    ]);
    exit;
}

// Obtener los datos del usuario
$usuario = $resultado->fetch_assoc();
$user_id = $usuario['id'];
$nombre_usuario = $usuario['nombre_usuario'];
$correo_usuario = $usuario['correo'];

$stmt->close();

// Generar un token único y seguro
$token = bin2hex(random_bytes(32)); // 64 caracteres hexadecimales

// Establecer fecha de expiración (1 hora desde ahora)
$fecha_expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));

// Eliminar tokens antiguos del usuario (opcional, para limpieza)
$sql_delete = "DELETE FROM password_reset_tokens WHERE user_id = ? AND (fecha_expiracion < NOW() OR usado = 1)";
$stmt_delete = $conn->prepare($sql_delete);
$stmt_delete->bind_param("i", $user_id);
$stmt_delete->execute();
$stmt_delete->close();

// Insertar el nuevo token en la base de datos
$sql_insert = "INSERT INTO password_reset_tokens (user_id, token, fecha_expiracion) VALUES (?, ?, ?)";
$stmt_insert = $conn->prepare($sql_insert);
$stmt_insert->bind_param("iss", $user_id, $token, $fecha_expiracion);

if (!$stmt_insert->execute()) {
    http_response_code(500);
    echo json_encode(["status" => "error", "msg" => "Error al generar el token de recuperación. Intenta nuevamente."]);
    $stmt_insert->close();
    $conn->close();
    exit;
}

$stmt_insert->close();
$conn->close();

// Construir el enlace de restablecimiento
// Detectar si estamos en localhost o en producción
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];

// Ajustar la ruta dependiendo del entorno
if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false) {
    // Entorno local
    $reset_link = $base_url . "/Yatzina-App/crearnuevacontrasena.html?token=" . $token;
} else {
    // Producción (Azure u otro host)
    $reset_link = $base_url . "/crearnuevacontrasena.html?token=" . $token;
}

// Preparar el correo electrónico
$asunto = "Restablecimiento de Contraseña - Yätzina";
$mensaje = "Hola $nombre_usuario,\n\n";
$mensaje .= "Hemos recibido una solicitud para restablecer tu contraseña en Yätzina.\n\n";
$mensaje .= "Para crear una nueva contraseña, haz clic en el siguiente enlace:\n\n";
$mensaje .= "$reset_link\n\n";
$mensaje .= "Este enlace es válido por 1 hora.\n\n";
$mensaje .= "Si no solicitaste este restablecimiento, puedes ignorar este correo de forma segura. Tu contraseña actual no cambiará.\n\n";
$mensaje .= "Saludos,\nEquipo de Yätzina";

// Headers del correo
$headers = "From: noreply@yatzina.com\r\n";
$headers .= "Reply-To: soporte@yatzina.com\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Intentar enviar el correo
// NOTA: en entornos de producción es preferible usar PHPMailer + SMTP autenticado.
if (mail($correo_usuario, $asunto, $mensaje, $headers)) {
    echo json_encode([
        "status" => "ok",
        "msg" => "Se ha enviado un enlace de restablecimiento a tu correo electrónico. Por favor, revisa tu bandeja de entrada."
    ]);
} else {
    // Registrar en log para depuración sin revelar información al cliente
    error_log("[recuperar_contrasena] mail() fallo al enviar a: $correo_usuario | headers: " . $headers);

    // Responder de forma genérica para no filtrar existencia de cuentas
    echo json_encode([
        "status" => "ok",
        "msg" => "Si el correo existe en nuestro sistema, recibirás un enlace de restablecimiento."
    ]);
}
?>
