<?php
session_start();
require_once '../config/conexion.php';

if (isset($_SESSION['usuario_id'])) {
    $uid = $_SESSION['usuario_id'];
    $conexion->query("UPDATE usuarios SET ultima_conexion = '2000-01-01 00:00:00' WHERE id = $uid");
}

session_unset();
session_destroy();
header("Location: ../pages/index.php");
exit();
?>
