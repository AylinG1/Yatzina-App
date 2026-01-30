<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexion.php';

$alumno_id = isset($_GET['alumno_id']) ? $_GET['alumno_id'] : null;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;
if ($limit <= 0 || $limit > 1000) $limit = 100;
$since = isset($_GET['since']) ? $_GET['since'] : null; // opcional: fecha ISO

$params = [];
$types = '';
$where = '';

if ($alumno_id !== null && $alumno_id !== '') {
    $where .= ' AND alumno_id = ?';
    $types .= 's';
    $params[] = $alumno_id;
}

if ($since) {
    $where .= ' AND created_at >= ?';
    $types .= 's';
    $params[] = $since;
}

$sql = "SELECT id, alumno_id, tipo, detalle, pagina, created_at FROM movimientos WHERE 1=1 " . $where . " ORDER BY created_at DESC LIMIT ?";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'Error al preparar la consulta: ' . mysqli_error($conn)]);
    exit;
}

// Bind dinámicamente
$bind_params = [];
if ($types !== '') {
    $bind_types = $types . 'i';
    $bind_params[] = &$bind_types;
    foreach ($params as $i => $p) {
        $bind_params[] = &$params[$i];
    }
    $bind_params[] = &$limit;
    call_user_func_array('mysqli_stmt_bind_param', array_merge([$stmt], $bind_params));
} else {
    mysqli_stmt_bind_param($stmt, 'i', $limit);
}

mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$rows = [];
while ($r = mysqli_fetch_assoc($res)) {
    $rows[] = $r;
}

mysqli_stmt_close($stmt);

echo json_encode(['status' => 'ok', 'data' => $rows]);
?>
