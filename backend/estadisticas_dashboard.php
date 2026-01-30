<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexion.php';

$response = [
    'success' => false,
    'data' => [
        'sesionesActivas' => 0,
        'leccionesHoy' => 0,
        'alertasSistema' => 0
    ]
];

try {
    // Sesiones activas aproximadas: usuarios con movimientos en los últimos 10 minutos
    $sqlSes = "SELECT COUNT(DISTINCT alumno_id) AS cnt FROM movimientos WHERE created_at >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)";
    $resSes = mysqli_query($conn, $sqlSes);
    if ($resSes) {
        $row = mysqli_fetch_assoc($resSes);
        $response['data']['sesionesActivas'] = intval($row['cnt']);
    }

    // Lecciones hoy: contar progresos registrados hoy (tabla progreso_lecciones)
    $sqlLecc = "SELECT COUNT(*) AS cnt FROM progreso_lecciones WHERE DATE(created_at) = CURDATE()";
    $resLecc = mysqli_query($conn, $sqlLecc);
    if ($resLecc) {
        $row = mysqli_fetch_assoc($resLecc);
        $response['data']['leccionesHoy'] = intval($row['cnt']);
    }

    // Alertas del sistema: movimientos con tipo alerta/error desde hoy
    $sqlAl = "SELECT COUNT(*) AS cnt FROM movimientos WHERE (tipo LIKE '%alert%' OR tipo LIKE '%error%') AND DATE(created_at) = CURDATE()";
    $resAl = mysqli_query($conn, $sqlAl);
    if ($resAl) {
        $row = mysqli_fetch_assoc($resAl);
        $response['data']['alertasSistema'] = intval($row['cnt']);
    }

    $response['success'] = true;
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
?>
