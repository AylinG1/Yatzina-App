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

$sql = "SELECT u.id, u.nombre_usuario, u.correo, up.nombre_completo, up.grado, up.id_maestro, m.nombre_usuario as maestroNombre
        FROM usuarios u 
        LEFT JOIN usuarios_perfiles up ON u.id = up.usuario_id
        LEFT JOIN usuarios m ON up.id_maestro = m.id
        WHERE u.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idAlumno);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    echo json_encode([
        'success' => true,
        'nombreCompleto' => $data['nombre_completo'] ?? $data['nombre_usuario'],
        'grado' => $data['grado'],
        'maestroNombre' => $data['maestroNombre'] ?? 'Sin maestro asignado'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
}

$stmt->close();
$conn->close();
?>
