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
    $idAlumno = $row['id'];
    
    // Calcular progreso general del alumno basado en lecciones totales
    // Total de lecciones esperadas en el sistema
    $totalLecciones = 7; // Ajustar según el número real de lecciones
    
    $sqlProgreso = "SELECT SUM(progreso) as sumaProgreso, COUNT(*) as leccionesRegistradas
                    FROM progreso_lecciones 
                    WHERE id_alumno = ?";
    $stmtProgreso = $conn->prepare($sqlProgreso);
    $stmtProgreso->bind_param("i", $idAlumno);
    $stmtProgreso->execute();
    $resultProgreso = $stmtProgreso->get_result();
    $progresoData = $resultProgreso->fetch_assoc();
    
    // Calcular progreso: (suma de progreso de lecciones registradas) / (total de lecciones * 100) * 100
    $sumaProgreso = $progresoData['sumaProgreso'] ? floatval($progresoData['sumaProgreso']) : 0;
    $progresoGeneral = ($sumaProgreso / ($totalLecciones * 100)) * 100;
    $progresoGeneral = round($progresoGeneral);
    
    $stmtProgreso->close();
    
    $alumnos[] = [
        'id' => $row['id'],
        'nombreCompleto' => $row['nombre_completo'] ?? $row['nombre_usuario'],
        'grado' => $row['grado'],
        'progresoGeneral' => $progresoGeneral
    ];
}

echo json_encode([
    'success' => true,
    'alumnos' => $alumnos
]);
