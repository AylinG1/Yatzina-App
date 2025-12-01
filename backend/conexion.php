<?php
$host = "bd-yatzina.mysql.database.azure.com";
$usuario = "adminyatzina";
$contrasena = "HelenDelgadillo5";
$base = "yatzinaapp"; // tu base de datos

// Ruta al certificado CA (archivo en la misma carpeta)
$ruta_certificado = __DIR__ . '/DigiCertGlobalRootG2.crt (1).pem';

$conn = mysqli_init();

// Azure requiere SSL
// Se usa NULL para que PHP use su configuración SSL por defecto
mysqli_ssl_set($conn, NULL, NULL, $ruta_certificado, NULL, NULL);

mysqli_real_connect(
    $conn,
    $host,
    $usuario,
    $contrasena,
    $base,
    3306,
    MYSQLI_CLIENT_SSL
);

if (mysqli_connect_errno()) {
    // CAMBIO CLAVE: Devolvemos el error en JSON para que el frontend lo muestre
    // Nos dirá si falla por credenciales o por certificado.
    http_response_code(500);
    echo json_encode(["status" => "error_db", "msg" => "Fallo de conexión a Azure: " . mysqli_connect_error()]);
    exit; // Aseguramos que la ejecución se detiene
}

mysqli_set_charset($conn, "utf8");
?>