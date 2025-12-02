<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();
header('Content-Type: application/json');
require_once 'conexion.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'maestro') {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$idMaestro = $_SESSION['user_id'];
$idAlumno = isset($data['idAlumno']) ? intval($data['idAlumno']) : 0;
$comentario = isset($data['comentario']) ? trim($data['comentario']) : '';

if($idAlumno === 0 || empty($comentario)) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$sql = "INSERT INTO comentarios_maestro (id_maestro, id_alumno, comentario, fecha) VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $idMaestro, $idAlumno, $comentario);

if($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Comentario guardado correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al guardar el comentario']);
}

$stmt->close();
$conn->close();
?>
