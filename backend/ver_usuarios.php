<?php
// Archivo para verificar qué correos están registrados en la base de datos
include "conexion.php";

echo "<h2>Usuarios Registrados en la Base de Datos</h2>";
echo "<p>Estos son los correos que puedes usar para recuperar contraseña:</p>";

$sql = "SELECT id, nombre_usuario, correo FROM usuarios ORDER BY id DESC LIMIT 20";
$resultado = $conn->query($sql);

if ($resultado && $resultado->num_rows > 0) {
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr style='background-color: #2bee4b;'>";
    echo "<th>ID</th><th>Usuario</th><th>Correo</th>";
    echo "</tr>";
    
    while ($row = $resultado->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nombre_usuario']) . "</td>";
        echo "<td><strong>" . htmlspecialchars($row['correo']) . "</strong></td>";
        echo "</tr>";
    }
    
    echo "</table>";
    echo "<br><p><strong>Usa uno de estos correos en la página de recuperación de contraseña.</strong></p>";
} else {
    echo "<p style='color: red;'>No hay usuarios registrados en la base de datos.</p>";
    echo "<p>Primero debes registrarte en: <a href='../registro.html'>registro.html</a></p>";
}

$conn->close();
?>
