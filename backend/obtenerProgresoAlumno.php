<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();
header('Content-Type: application/json');
require_once 'conexion.php';

// Verificar sesión activa
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$idAlumno = $_SESSION['user_id'];

// Obtener todas las lecciones con su estado de progreso
$sql = "SELECT leccion, completada, progreso as porcentaje, puntos, fecha_actualizacion 
        FROM progreso_lecciones 
        WHERE id_alumno = ? 
        ORDER BY id";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idAlumno);
$stmt->execute();
$result = $stmt->get_result();

$progreso = [];
while ($row = $result->fetch_assoc()) {
    $progreso[] = [
        'leccion' => $row['leccion'],
        'completado' => (int)$row['completada'],  // BD tiene 'completada'
        'porcentaje' => (int)$row['porcentaje'] > 0 ? (int)$row['porcentaje'] : (int)$row['progreso'],
        'puntos' => (int)$row['puntos'],
        'fecha' => $row['fecha_actualizacion']
    ];
}

echo json_encode([
    'success' => true,
    'progreso' => $progreso
]);

$stmt->close();
$conn->close();
?>
