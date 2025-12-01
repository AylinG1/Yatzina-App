<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Incluir conexión a la base de datos
require_once 'conexion.php';

try {
    $sql = "SELECT id, palabra_hnahnu, traduccion_espanol, fecha_registro 
            FROM palabras_diccionario 
            ORDER BY palabra_hnahnu ASC";
    
    $result = $conexion->query($sql);

    if (!$result) {
        throw new Exception('Error en la consulta: ' . $conexion->error);
    }

    $palabras = [];
    while ($row = $result->fetch_assoc()) {
        $palabras[] = $row;
    }

    echo json_encode([
        'success' => true,
        'palabras' => $palabras,
        'total' => count($palabras)
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'palabras' => [],
        'total' => 0
    ]);
}

$conexion->close();
?>
