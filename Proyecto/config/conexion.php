<?php
date_default_timezone_set('Europe/Madrid');

$host = "localhost";
$user = "diarioemocional";
$pass = "Diarioemocional123$"; 
$db   = "diarioemocional";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conexion = new mysqli($host, $user, $pass, $db);
    $conexion->set_charset("utf8mb4");
    $conexion->query("SET time_zone = '+01:00'");
} catch (mysqli_sql_exception $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
