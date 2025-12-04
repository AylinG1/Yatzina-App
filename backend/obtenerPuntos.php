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

// Crear tabla si no existe
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

// Obtener puntos totales
$totalSql = "SELECT COALESCE(SUM(puntos), 0) as total FROM puntos_alumnos WHERE id_alumno = ?";
$totalStmt = $conn->prepare($totalSql);
$totalStmt->bind_param("i", $id_alumno);
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalRow = $totalResult->fetch_assoc();

// Obtener desglose
$desgloseSql = "SELECT tipo, COUNT(*) as cantidad, SUM(puntos) as total 
                FROM puntos_alumnos 
                WHERE id_alumno = ? 
                GROUP BY tipo";
$desgloseStmt = $conn->prepare($desgloseSql);
$desgloseStmt->bind_param("i", $id_alumno);
$desgloseStmt->execute();
$desgloseResult = $desgloseStmt->get_result();

$desglose = [];
while ($row = $desgloseResult->fetch_assoc()) {
    $desglose[] = $row;
}

echo json_encode([
    'success' => true,
    'puntos_totales' => intval($totalRow['total']),
    'desglose' => $desglose
]);

$totalStmt->close();
$desgloseStmt->close();
$conn->close();
?>
