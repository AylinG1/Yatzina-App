<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();
header('Content-Type: application/json');

// La conexión debe ser incluida con la variable $conn
require_once 'conexion.php'; 

// 1. Control de Autorización y Rol
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'alumno') {
    echo json_encode(['success' => false, 'message' => 'No autorizado. Inicia sesión como alumno.']);
    exit;
}

// 2. Extracción y Limpieza de Datos
$data = json_decode(file_get_contents('php://input'), true);

$idAlumno = $_SESSION['user_id'];
$leccion = isset($data['leccion']) ? trim($data['leccion']) : '';
$completada = isset($data['completado']) ? intval($data['completado']) : 0;
// Usar el progreso enviado desde el frontend, o 100 si está completada
$progreso = isset($data['progreso']) ? floatval($data['progreso']) : (($completada === 1) ? 100.00 : 0.00);
$puntos = isset($data['puntos']) ? intval($data['puntos']) : 0;
$tiempo_dedicado = isset($data['tiempo']) ? intval($data['tiempo']) : 0;
$intentos = 1; // Se incrementará si ya existe

if(empty($leccion)) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos: falta la lección.']);
    exit;
}

// 3. Revisar si ya existe el progreso (SELECT)
// Nota: Se corrige la variable de conexión a $conn y la columna a id_alumno
$checkSql = "SELECT id, intentos FROM progreso_lecciones WHERE id_alumno = ? AND leccion = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("is", $idAlumno, $leccion);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    // 4a. Actualizar progreso existente (UPDATE)
    $row = $checkResult->fetch_assoc();
    $nuevosIntentos = $row['intentos'] + 1;
    
    // Se agregan 'progreso' (d) y 'puntos' (i) a la actualización
    $updateSql = "UPDATE progreso_lecciones 
                  SET completada = ?, progreso = ?, fecha_completada = NOW(), tiempo_dedicado = ?, intentos = ? 
                  WHERE id_alumno = ? AND leccion = ?";
                  
    $stmt = $conn->prepare($updateSql);
    // Tipos de parámetros: i (completada), d (progreso), i (tiempo), i (intentos), i (idAlumno), s (leccion)
    $stmt->bind_param("idiiis", $completada, $progreso, $tiempo_dedicado, $nuevosIntentos, $idAlumno, $leccion);

} else {
    // 4b. Insertar nuevo progreso (INSERT)
    
    // Nota: El campo 'progreso' (d) y 'puntos' (i) se agrega al INSERT
    $insertSql = "INSERT INTO progreso_lecciones 
                  (id_alumno, leccion, completada, progreso, fecha_completada, tiempo_dedicado, intentos) 
                  VALUES (?, ?, ?, ?, NOW(), ?, ?)";
                  
    $stmt = $conn->prepare($insertSql);
    // Tipos de parámetros: i (idAlumno), s (leccion), i (completada), d (progreso), i (tiempo), i (intentos)
    $stmt->bind_param("isidii", $idAlumno, $leccion, $completada, $progreso, $tiempo_dedicado, $intentos);
}

// 5. Ejecutar la sentencia (INSERT o UPDATE)
if ($stmt->execute()) {
    $puntosGanados = 0;
    $mensaje = 'Progreso guardado correctamente';
    
    // Si la lección se completó, registrar puntos automáticamente
    if ($completada === 1) {
        $puntosPorLeccion = 100; // Puntos por completar una lección
        
        // Crear tabla de puntos si no existe
        $crearTabla = "CREATE TABLE IF NOT EXISTS puntos_alumnos (
            id INT PRIMARY KEY AUTO_INCREMENT,
            id_alumno INT NOT NULL,
            tipo VARCHAR(50),
            id_referencia VARCHAR(255),
            puntos INT,
            fecha_obtenido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_punto (id_alumno, tipo, id_referencia),
            FOREIGN KEY (id_alumno) REFERENCES usuarios(id) ON DELETE CASCADE
        )";
        $conn->query($crearTabla);
        
        // Verificar si ya obtuvo puntos por esta lección
        $checkPuntosSql = "SELECT id FROM puntos_alumnos WHERE id_alumno = ? AND tipo = 'leccion' AND id_referencia = ?";
        $checkPuntosStmt = $conn->prepare($checkPuntosSql);
        $checkPuntosStmt->bind_param("is", $idAlumno, $leccion);
        $checkPuntosStmt->execute();
        $puntosResult = $checkPuntosStmt->get_result();
        
        // Solo registrar si no existen puntos previos
        if ($puntosResult->num_rows === 0) {
            $insertPuntosSql = "INSERT INTO puntos_alumnos (id_alumno, tipo, id_referencia, puntos) 
                               VALUES (?, 'leccion', ?, ?)";
            $insertPuntosStmt = $conn->prepare($insertPuntosSql);
            $insertPuntosStmt->bind_param("isi", $idAlumno, $leccion, $puntosPorLeccion);
            $insertPuntosStmt->execute();
            $insertPuntosStmt->close();
            
            $puntosGanados = $puntosPorLeccion;
            $mensaje = 'Progreso guardado y puntos registrados';
        }
        
        $checkPuntosStmt->close();
    }
    
    // Obtener puntos totales
    $totalPuntosSql = "SELECT COALESCE(SUM(puntos), 0) as total FROM puntos_alumnos WHERE id_alumno = ?";
    $totalPuntosStmt = $conn->prepare($totalPuntosSql);
    $totalPuntosStmt->bind_param("i", $idAlumno);
    $totalPuntosStmt->execute();
    $totalPuntosResult = $totalPuntosStmt->get_result();
    $totalPuntosData = $totalPuntosResult->fetch_assoc();
    $puntosTotales = $totalPuntosData['total'];
    $totalPuntosStmt->close();
    
    echo json_encode([
        'success' => true, 
        'message' => $mensaje, 
        'progreso' => $progreso,
        'puntosGanados' => $puntosGanados,
        'puntosTotales' => $puntosTotales,
        'leccionCompletada' => $completada === 1
    ]);
} else {
    // Si falla la ejecución por un error SQL, lo reporta
    http_response_code(500); 
    echo json_encode(['success' => false, 'message' => 'Error al ejecutar SQL: ' . $stmt->error]);
}

$checkStmt->close();
$stmt->close();
// Ojo: Si usas $conn->close() aquí, afectará a cualquier otro script que lo necesite después. 
// En un script corto como este, es aceptable.
$conn->close(); 
?>