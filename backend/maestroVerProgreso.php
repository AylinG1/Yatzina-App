<?php
require "conexion.php";
session_start();

// ---------------------------------------------------------------
// Validar que sea maestro
// ---------------------------------------------------------------
if (!isset($_SESSION['id']) || $_SESSION['rol'] !== "Maestro") {
    echo "Error: acceso no autorizado.";
    exit();
}

$idMaestro = $_SESSION['id'];

// ---------------------------------------------------------------
// Recibir ID del alumno a revisar
// ---------------------------------------------------------------
$idAlumno = intval($_GET['idAlumno'] ?? 0);

if ($idAlumno === 0) {
    echo "Error: no se recibió el alumno.";
    exit();
}

// ---------------------------------------------------------------
// Validar que el alumno pertenezca al maestro
// ---------------------------------------------------------------
$sqlValida = "SELECT 1 FROM alumnos_maestros WHERE idMaestro = ? AND idAlumno = ?";
$val = $conexion->prepare($sqlValida);
$val->bind_param("ii", $idMaestro, $idAlumno);
$val->execute();
$resVal = $val->get_result();

if ($resVal->num_rows === 0) {
    echo "Error: este alumno no pertenece a su grupo.";
    exit();
}

// ---------------------------------------------------------------
// Obtener datos del alumno
// ---------------------------------------------------------------
$sqlAlumno = "SELECT nombreCompleto FROM usuariosnuevos WHERE id = ?";
$stmtA = $conexion->prepare($sqlAlumno);
$stmtA->bind_param("i", $idAlumno);
$stmtA->execute();
$resA = $stmtA->get_result();
$alumno = $resA->fetch_assoc();

// ---------------------------------------------------------------
// Obtener su progreso en todas las lecciones
// ---------------------------------------------------------------
$sqlProg = "SELECT leccion, progreso, completado
            FROM progreso_lecciones
            WHERE idAlumno = ?";
$stmtP = $conexion->prepare($sqlProg);
$stmtP->bind_param("i", $idAlumno);
$stmtP->execute();
$resP = $stmtP->get_result();

// ---------------------------------------------------------------
// Mostrar información
// ---------------------------------------------------------------
echo "<h1>Progreso de: " . htmlspecialchars($alumno['nombreCompleto']) . "</h1>";
echo "<br>";

if ($resP->num_rows === 0) {
    echo "Este alumno aún no tiene progreso registrado.";
    exit();
}

echo "<table border='1' cellpadding='10' cellspacing='0'>";
echo "<thead>";
echo "<tr>";
echo "<th>Lección</th>";
echo "<th>Progreso (%)</th>";
echo "<th>Completada</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";

while ($fila = $resP->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($fila['leccion']) . "</td>";
    echo "<td>" . intval($fila['progreso']) . "%</td>";
    echo "<td>" . ($fila['completado'] ? "Sí" : "No") . "</td>";
    echo "</tr>";
}

echo "</tbody>";
echo "</table>";

?>
