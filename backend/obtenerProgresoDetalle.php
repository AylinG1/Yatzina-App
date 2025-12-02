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

$idAlumno = isset($_GET['idAlumno']) ? intval($_GET['idAlumno']) : 0;

if($idAlumno === 0) {
    echo json_encode(['success' => false, 'message' => 'ID de alumno inválido']);
    exit;
}

$sql = "SELECT 
            leccion as nombreLeccion,
            completada as completado,
            progreso as porcentaje,
            puntos,
            fecha_actualizacion
        FROM progreso_lecciones
        WHERE id_alumno = ?
        ORDER BY id ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idAlumno);
$stmt->execute();
$resultado = $stmt->get_result();

$lecciones = [];
while($row = $resultado->fetch_assoc()) {
    $lecciones[] = [
        'nombreLeccion' => $row['nombreLeccion'],
        'completado' => (int)$row['completado'],
        'porcentaje' => (int)$row['porcentaje'],
        'puntos' => (int)$row['puntos'],
        'fecha' => $row['fecha_actualizacion']
    ];
}

echo json_encode([
    'success' => true,
    'lecciones' => $lecciones
]);

$stmt->close();
$conn->close();
?>
