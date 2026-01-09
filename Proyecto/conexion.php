<?php
// conexion.php LIMPIO
$host = "localhost";
$user = "Gustavo";
$pass = "Hakaishin2.";  // Contraseña con el punto incluido
$db   = "diarioemocional";

// Configuración de errores
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conexion = new mysqli($host, $user, $pass, $db);
    $conexion->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("Error de conexión: " . $e->getMessage());
}
