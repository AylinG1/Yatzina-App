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

$sql = "SELECT u.id, u.nombre_usuario, up.nombre_completo, up.grado
        FROM usuarios u
        INNER JOIN alumnos_maestros am ON u.id = am.id_alumno
        LEFT JOIN usuarios_perfiles up ON u.id = up.usuario_id
        WHERE am.id_maestro = ? AND u.rol = 'alumno'
        ORDER BY u.nombre_usuario";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error preparando query: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $idMaestro);
$stmt->execute();
$result = $stmt->get_result();

$alumnos = [];
while ($row = $result->fetch_assoc()) {
    $alumnos[] = [
        'id' => $row['id'],
        'nombreCompleto' => $row['nombre_completo'] ?? $row['nombre_usuario'],
        'grado' => $row['grado'],
        'progreso' => 0
    ];
}

echo json_encode([
    'success' => true,
    'alumnos' => $alumnos
]);
