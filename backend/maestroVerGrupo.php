<?php
session_start();
require "conexion.php";

// Verificar que es maestro
if (!isset($_SESSION['tipoRol']) || $_SESSION['tipoRol'] !== "Maestro") {
    header("Location: Acceso.html");
    exit();
}

$idMaestro = $_SESSION['id'];
$gradoMaestro = $_SESSION['grado'];

// Obtener alumnos asignados automáticamente por grado
$sql = "
SELECT u.id, u.nombreCompleto, u.usuario, u.grado
FROM usuariosnuevos u
INNER JOIN alumnos_maestros am ON am.idAlumno = u.id
WHERE am.idMaestro = ?
ORDER BY u.nombreCompleto ASC;
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $idMaestro);
$stmt->execute();
$res = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mis Alumnos</title>
<style>
body {
  font-family: Arial;
  background: #f0f0f0;
  padding: 20px;
}
table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  border-radius: 10px;
  overflow: hidden;
}
th, td {
  padding: 15px;
  border-bottom: 1px solid #ccc;
}
th {
  background: #764ba2;
  color: white;
  font-size: 18px;
}
tr:hover {
  background: #f8eafc;
}
h1 {
  text-align: center;
  margin-bottom: 20px;
}
</style>
</head>
<body>

<h1>Alumnos de tu grupo (<?php echo $gradoMaestro; ?>)</h1>

<table>
  <tr>
    <th>ID</th>
    <th>Nombre</th>
    <th>Usuario</th>
    <th>Grado</th>
  </tr>

<?php
if ($res->num_rows > 0) {
    while ($al = $res->fetch_assoc()) {
        echo "<tr>
                <td>{$al['id']}</td>
                <td>{$al['nombreCompleto']}</td>
                <td>{$al['usuario']}</td>
                <td>{$al['grado']}</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='4' style='text-align:center;'>Aún no tienes alumnos asignados</td></tr>";
}
?>

</table>

</body>
</html>
