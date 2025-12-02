<?php
error_reporting(0);
ini_set('display_errors', 0);
require "conexion.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "No hay sesión"]);
    exit();
}

$idAlumno = $_SESSION['user_id'];

$sql = "SELECT leccion, progreso, completada as completado 
        FROM progreso_lecciones 
        WHERE id_alumno = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idAlumno);
$stmt->execute();
$resultado = $stmt->get_result();

$datos = [];

while ($fila = $resultado->fetch_assoc()) {
    $datos[] = $fila;
}

echo json_encode($datos);
?>
