<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexion.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data || !isset($data['tipo'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'Solicitud inválida: falta campo "tipo".']);
    exit;
}

$alumno_id = isset($data['alumno_id']) ? $data['alumno_id'] : null;
$tipo = $data['tipo'];
$detalle = isset($data['detalle']) ? $data['detalle'] : null;
$pagina = isset($data['pagina']) ? $data['pagina'] : null;

$stmt = mysqli_prepare($conn, "INSERT INTO movimientos (alumno_id, tipo, detalle, pagina) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'Error al preparar la consulta: ' . mysqli_error($conn)]);
    exit;
}

mysqli_stmt_bind_param($stmt, 'ssss', $alumno_id, $tipo, $detalle, $pagina);
$exec = mysqli_stmt_execute($stmt);
if (!$exec) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'Error al insertar movimiento: ' . mysqli_stmt_error($stmt)]);
    exit;
}

$insert_id = mysqli_insert_id($conn);
mysqli_stmt_close($stmt);

echo json_encode(['status' => 'ok', 'id' => $insert_id]);
?>
