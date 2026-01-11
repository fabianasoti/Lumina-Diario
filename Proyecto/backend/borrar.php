<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['usuario_id'])) { header("Location: ../pages/index.php"); exit(); }

$mi_id = $_SESSION['usuario_id'];
$id_entrada = $_GET['id'] ?? null;

if ($id_entrada) {
    // Verificar rol actual
    $check_rol = $conexion->query("SELECT rol FROM usuarios WHERE id = $mi_id");
    $soy_admin = ($check_rol->fetch_assoc()['rol'] === 'admin');

    if ($soy_admin) {
        // ADMIN: Puede borrar cualquier entrada (incluso de otros)
        $stmt = $conexion->prepare("DELETE FROM entradas WHERE id = ?");
        $stmt->bind_param("i", $id_entrada);
    } else {
        // USUARIO: Solo borra si el 'usuario_id' coincide con el suyo
        $stmt = $conexion->prepare("DELETE FROM entradas WHERE id = ? AND usuario_id = ?");
        $stmt->bind_param("ii", $id_entrada, $mi_id);
    }

    $stmt->execute();
}

// Redirección inteligente (Vuelve a donde estabas)
if (isset($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    header("Location: ../pages/dashboard.php");
}
?>
