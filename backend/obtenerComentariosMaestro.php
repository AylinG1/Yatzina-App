<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();
header('Content-Type: application/json');
require_once 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$idMaestro = $_SESSION['user_id'];

$sql = "SELECT cm.id, cm.comentario, cm.fecha as fecha_comentario, u.nombre_completo as nombreAlumno, up.grado
        FROM comentarios_maestro cm
        INNER JOIN usuarios u ON cm.id_alumno = u.id
        LEFT JOIN usuarios_perfiles up ON u.id = up.usuario_id
        WHERE cm.id_maestro = ?
        ORDER BY cm.fecha DESC";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error preparando query: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $idMaestro);
$stmt->execute();
$result = $stmt->get_result();

$comentarios = [];
while ($row = $result->fetch_assoc()) {
    $comentarios[] = [
        'id' => $row['id'],
        'comentario' => $row['comentario'],
        'fecha' => $row['fecha_comentario'],
        'nombreAlumno' => $row['nombreAlumno'],
        'grado' => $row['grado']
    ];
}

echo json_encode([
    'success' => true,
    'comentarios' => $comentarios
]);
