<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();
header('Content-Type: application/json');

require_once 'conexion.php';

// Validar que sea alumno
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'alumno') {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$id_alumno = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

$id_insignia = isset($data['id_insignia']) ? trim($data['id_insignia']) : '';
$nombre_insignia = isset($data['nombre']) ? trim($data['nombre']) : '';
$puntos_ganados = isset($data['puntos']) ? intval($data['puntos']) : 50; // Por defecto 50 puntos por insignia

if (empty($id_insignia) || empty($nombre_insignia)) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

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

// Verificar si ya obtuvo puntos por esta insignia
$checkSql = "SELECT id FROM puntos_alumnos WHERE id_alumno = ? AND tipo = 'insignia' AND id_referencia = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("is", $id_alumno, $id_insignia);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    // Ya tiene puntos de esta insignia
    echo json_encode([
        'success' => false, 
        'message' => 'Ya obtuviste puntos por esta insignia'
    ]);
    $checkStmt->close();
    exit;
}

// Insertar nuevos puntos
$insertSql = "INSERT INTO puntos_alumnos (id_alumno, tipo, id_referencia, puntos) 
              VALUES (?, 'insignia', ?, ?)";
$insertStmt = $conn->prepare($insertSql);
$insertStmt->bind_param("isi", $id_alumno, $id_insignia, $puntos_ganados);

if ($insertStmt->execute()) {
    // Obtener total de puntos
    $totalSql = "SELECT COALESCE(SUM(puntos), 0) as total FROM puntos_alumnos WHERE id_alumno = ?";
    $totalStmt = $conn->prepare($totalSql);
    $totalStmt->bind_param("i", $id_alumno);
    $totalStmt->execute();
    $totalResult = $totalStmt->get_result();
    $totalRow = $totalResult->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'message' => 'Puntos de insignia registrados correctamente',
        'puntos_ganados' => $puntos_ganados,
        'puntos_totales' => $totalRow['total']
    ]);
    
    $totalStmt->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error al registrar puntos: ' . $insertStmt->error
    ]);
}

$checkStmt->close();
$insertStmt->close();
$conn->close();
?>
