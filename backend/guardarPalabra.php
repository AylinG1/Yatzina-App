<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();
header('Content-Type: application/json');
require_once 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado (sesión no iniciada)']);
    exit;
}

$usuario = $_SESSION['usuario'];
$tipo_creador = $_SESSION['user_rol'];   // alumno / maestro / hablante nativo
$grado_creador = $_SESSION['grado'];    // Alumno / Maestro o NULL para hablante

// Obtener ID del creador
$sql = "SELECT id FROM usuarios WHERE nombre_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();
$datos = $result->fetch_assoc();
$stmt->close();

if (!$datos) {
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
    exit;
}

$idCreador = $datos['id'];


// Recibir datos
$palabra = $_POST['palabra'] ?? $_POST['palabra_hnahnu'] ?? '';
$traduccion = $_POST['traduccion'] ?? $_POST['traduccion_espanol'] ?? '';

$palabra = trim($palabra);
$traduccion = trim($traduccion);

if ($palabra === "" || $traduccion === "") {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos']);
    exit;
}


// INSERT COMPLETO PARA TU TABLA
$sqlInsert = "INSERT INTO palabras_personalizadas
    (palabra_hnahnu, traduccion_espanol, categoria, idCreador, tipo_creador, grado, fecha_creacion) 
    VALUES (?, ?, NULL, ?, ?, ?, NOW())";

$stmtInsert = $conn->prepare($sqlInsert);
$stmtInsert->bind_param("ssiss", $palabra, $traduccion, $idCreador, $tipo_creador, $grado_creador);

if ($stmtInsert->execute()) {
    echo json_encode(['success' => true, 'message' => 'Palabra registrada exitosamente']);
} else {
    error_log("Error al insertar palabra: " . $stmtInsert->error);
    echo json_encode(['success' => false, 'message' => 'Error al registrar la palabra']);
}

$stmtInsert->close();
$conn->close();
?>