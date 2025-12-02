<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();
header('Content-Type: application/json');
require_once 'conexion.php';

if(!isset($_SESSION['id']) || $_SESSION['user_rol'] !== 'maestro') {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$palabra = isset($data['palabra']) ? trim($data['palabra']) : '';
$traduccion = isset($data['traduccion']) ? trim($data['traduccion']) : '';
$id_usuario = $_SESSION['id'];

if(empty($palabra) || empty($traduccion)) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$sql = "INSERT INTO palabras_personalizadas (palabra_hnahnu, traduccion_espanol, fecha_registro) VALUES (?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $palabra, $traduccion);

if($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Palabra guardada correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al guardar la palabra']);
}

$stmt->close();
$conn->close();
?>
