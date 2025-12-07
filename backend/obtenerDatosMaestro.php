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
        LEFT JOIN usuarios_perfiles up ON u.id = up.usuario_id 
        WHERE u.id = ? AND u.rol = 'maestro'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idMaestro);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Maestro no encontrado']);
    exit;
}

$maestro = $result->fetch_assoc();

$sqlCount = "SELECT COUNT(*) as total FROM alumnos_maestros WHERE id_maestro = ?";
$stmtCount = $conn->prepare($sqlCount);
$stmtCount->bind_param("i", $idMaestro);
$stmtCount->execute();
$resultCount = $stmtCount->get_result();
$countData = $resultCount->fetch_assoc();

echo json_encode([
    'success' => true,
    'nombreCompleto' => $maestro['nombre_completo'] ?? $maestro['nombre_usuario'] ?? 'Maestro',
    'grado' => $maestro['grado'] ?? 'N/A',
    'totalAlumnos' => (int)$countData['total']
]);
