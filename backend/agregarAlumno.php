<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Responder a preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'conexion.php';

// Validar sesión y rol de maestro
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'maestro') {
    echo json_encode(['success' => false, 'message' => 'No autorizado. Debes ser maestro.']);
    exit;
}

$idMaestro = $_SESSION['user_id'];

// Leer y decodificar JSON del body
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['buscar']) || empty(trim($data['buscar']))) {
    echo json_encode(['success' => false, 'message' => 'Por favor ingresa un nombre de usuario o correo.']);
    exit;
}

$buscar = trim($data['buscar']);

// Buscar el alumno por nombre_usuario o correo
$sql = "SELECT id, nombre_usuario, correo, rol FROM usuarios WHERE (nombre_usuario = ? OR correo = ?) AND rol = 'alumno'";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error preparando búsqueda: ' . $conn->error]);
    exit;
}

$stmt->bind_param("ss", $buscar, $buscar);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Alumno no encontrado. Verifica el nombre de usuario o correo.']);
    exit;
}

$alumno = $result->fetch_assoc();
$idAlumno = $alumno['id'];

// Verificar que el alumno no esté ya asignado al maestro
$sqlVerifica = "SELECT id FROM alumnos_maestros WHERE id_maestro = ? AND id_alumno = ?";
$stmtVerifica = $conn->prepare($sqlVerifica);

if (!$stmtVerifica) {
    echo json_encode(['success' => false, 'message' => 'Error en verificación: ' . $conn->error]);
    exit;
}

$stmtVerifica->bind_param("ii", $idMaestro, $idAlumno);
$stmtVerifica->execute();
$resultVerifica = $stmtVerifica->get_result();

if ($resultVerifica->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Este alumno ya está en tu lista.']);
    exit;
}

// Insertar la relación maestro-alumno
$sqlInsert = "INSERT INTO alumnos_maestros (id_maestro, id_alumno) VALUES (?, ?)";
$stmtInsert = $conn->prepare($sqlInsert);

if (!$stmtInsert) {
    echo json_encode(['success' => false, 'message' => 'Error preparando inserción: ' . $conn->error]);
    exit;
}

$stmtInsert->bind_param("ii", $idMaestro, $idAlumno);

if ($stmtInsert->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Alumno ' . htmlspecialchars($alumno['nombre_usuario']) . ' agregado exitosamente a tu lista.'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al agregar alumno: ' . $stmtInsert->error]);
}

$stmt->close();
$stmtVerifica->close();
$stmtInsert->close();
$conn->close();
?>
