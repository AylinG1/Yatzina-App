<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();
header('Content-Type: application/json');
require_once 'conexion.php';

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$idAlumno = $_SESSION['user_id'];

$sql = "SELECT 
            c.comentario,
            DATE_FORMAT(c.fecha, '%d/%m/%Y %H:%i') as fecha,
            u.nombre_usuario as nombreMaestro
        FROM comentarios_maestro c
        INNER JOIN usuarios u ON c.id_maestro = u.id
        WHERE c.id_alumno = ?
        ORDER BY c.fecha DESC
        LIMIT 20";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idAlumno);
$stmt->execute();
$resultado = $stmt->get_result();

$comentarios = [];
while($row = $resultado->fetch_assoc()) {
    $comentarios[] = $row;
}

echo json_encode([
    'success' => true,
    'comentarios' => $comentarios
]);

$stmt->close();
$conn->close();
?>
