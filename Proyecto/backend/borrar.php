<?php
session_start();
// CORRECCIÓN: Ruta correcta a config (subir un nivel y entrar a config)
require_once '../config/conexion.php';

// Si no está logueado, fuera
if (!isset($_SESSION['usuario_id'])) { header("Location: ../pages/index.php"); exit(); }

$mi_id = $_SESSION['usuario_id'];
$id_entrada_a_borrar = $_GET['id'] ?? null;

if ($id_entrada_a_borrar) {
    // Verificar rol
    $check_rol = $conexion->query("SELECT rol FROM usuarios WHERE id = $mi_id");
    $soy_admin = ($check_rol->fetch_assoc()['rol'] === 'admin');

    if ($soy_admin) {
        // Admin borra lo que sea
        $stmt = $conexion->prepare("DELETE FROM entradas WHERE id = ?");
        $stmt->bind_param("i", $id_entrada_a_borrar);
    } else {
        // Usuario solo borra lo suyo
        $stmt = $conexion->prepare("DELETE FROM entradas WHERE id = ? AND usuario_id = ?");
        $stmt->bind_param("ii", $id_entrada_a_borrar, $mi_id);
    }

    $stmt->execute();
}

// Redirigir a la página anterior (Dashboard o Historial)
if (isset($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    header("Location: ../pages/dashboard.php");
}
?>
