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

$id_alumno = $_SESSION['user_id'];

// Crear tabla de puntos si no existe
$crearTabla = "CREATE TABLE IF NOT EXISTS puntos_alumnos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_alumno INT NOT NULL,
    tipo VARCHAR(50),
    id_referencia VARCHAR(255),
    puntos INT,
    fecha_obtenido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_punto (id_alumno, tipo, id_referencia),
    FOREIGN KEY (id_alumno) REFERENCES usuarios(id) ON DELETE CASCADE
)";
$conn->query($crearTabla);

// Obtener progreso de todas las lecciones
$query = "SELECT leccion, completada, progreso, puntos 
          FROM progreso_lecciones 
          WHERE id_alumno = ? 
          ORDER BY id ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_alumno);
$stmt->execute();
$result = $stmt->get_result();

$lecciones = [];
$total_completadas = 0;
$total_lecciones = 7; // Total de lecciones disponibles

while ($row = $result->fetch_assoc()) {
    $lecciones[] = $row;
    if ($row['completada'] == 1) {  // Cambiar 'completado' a 'completada'
        $total_completadas++;
    }
}

// Obtener puntos totales de la tabla puntos_alumnos
$puntosSql = "SELECT COALESCE(SUM(puntos), 0) as total FROM puntos_alumnos WHERE id_alumno = ?";
$puntosStmt = $conn->prepare($puntosSql);
$puntosStmt->bind_param("i", $id_alumno);
$puntosStmt->execute();
$puntosResult = $puntosStmt->get_result();
$puntosRow = $puntosResult->fetch_assoc();
$total_puntos = intval($puntosRow['total']);
$puntosStmt->close();

// Calcular logros
$logros = [];

// Logro: Primera lección
if ($total_completadas >= 1) {
    $logros[] = [
        'nombre' => 'Primer Paso',
        'descripcion' => 'Completaste tu primera lección',
        'icono' => '🎯',
        'obtenido' => true
    ];
}

// Logro: 3 lecciones
if ($total_completadas >= 3) {
    $logros[] = [
        'nombre' => 'Estudiante Dedicado',
        'descripcion' => 'Completaste 3 lecciones',
        'icono' => '📚',
        'obtenido' => true
    ];
}

// Logro: 5 lecciones
if ($total_completadas >= 5) {
    $logros[] = [
        'nombre' => 'Experto en Hñähñu',
        'descripcion' => 'Completaste 5 lecciones',
        'icono' => '⭐',
        'obtenido' => true
    ];
}

// Logro: Todas las lecciones
if ($total_completadas >= 7) {
    $logros[] = [
        'nombre' => 'Maestro del Hñähñu',
        'descripcion' => 'Completaste todas las lecciones',
        'icono' => '🏆',
        'obtenido' => true
    ];
}

// Logro: 100 puntos
if ($total_puntos >= 100) {
    $logros[] = [
        'nombre' => 'Coleccionista de Puntos',
        'descripcion' => 'Acumulaste 100 puntos',
        'icono' => '💯',
        'obtenido' => true
    ];
}

echo json_encode([
    'success' => true,
    'logros' => $logros,
    'lecciones' => $lecciones,
    'total_completadas' => $total_completadas,
    'total_lecciones' => $total_lecciones,
    'total_puntos' => $total_puntos,
    'porcentaje_general' => round(($total_completadas / $total_lecciones) * 100)
]);

$stmt->close();
$conn->close();
?>
