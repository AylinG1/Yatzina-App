<?php
require "conexion.php";
session_start();

if (!isset($_SESSION['id'])) {
    echo json_encode(["error" => "No hay sesión"]);
    exit();
}

$idAlumno = $_SESSION['id'];

$sql = "SELECT leccion, progreso, completado 
        FROM progreso_lecciones 
        WHERE idAlumno = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $idAlumno);
$stmt->execute();
$resultado = $stmt->get_result();

$datos = [];

while ($fila = $resultado->fetch_assoc()) {
    $datos[] = $fila;
}

echo json_encode($datos);
?>
