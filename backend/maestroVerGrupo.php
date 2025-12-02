<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();
require "conexion.php";

// Verificar que es maestro
if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] !== "maestro") {
    header("Location: Acceso.html");
    exit();
}

$idMaestro = $_SESSION['user_id'];
$gradoMaestro = $_SESSION['grado'] ?? null;

// Obtener alumnos asignados automáticamente por grado
$sql = "
SELECT u.id, up.nombre_completo, u.nombre_usuario, up.grado
FROM usuarios u
INNER JOIN alumnos_maestros am ON am.id_alumno = u.id
LEFT JOIN usuarios_perfiles up ON u.id = up.usuario_id
WHERE am.id_maestro = ?
ORDER BY up.nombre_completo ASC;
";

$stmt = $conn->prepare($sql);
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
