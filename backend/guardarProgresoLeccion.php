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
// Si la lección está 'completada' (1), asumimos que el progreso es 100%.
$progreso = ($completada === 1) ? 100.00 : 0.00; // Asignamos 100 si está completa
$puntos = isset($data['puntos']) ? intval($data['puntos']) : 0; // Este campo no se usa en progreso_lecciones, solo se recibe
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
    echo json_encode(['success' => true, 'message' => 'Progreso guardado correctamente', 'progreso' => $progreso]);
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