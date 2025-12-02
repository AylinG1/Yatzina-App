<?php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Incluir conexión a la base de datos
require_once 'conexion.php';

try {
    $sql = "SELECT id, palabra_hnahnu, traduccion_espanol, fecha_registro 
            FROM palabras_diccionario 
            ORDER BY palabra_hnahnu ASC";
    
    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception('Error en la consulta: ' . $conn->error);
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

$conn->close();
?>
