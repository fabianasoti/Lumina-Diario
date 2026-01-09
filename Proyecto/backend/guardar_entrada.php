<?php
session_start();
require_once '../config/conexion.php';

// Seguridad: Si no estás logueado, fuera
if (!isset($_SESSION['usuario_id'])) { header("Location: ../pages/index.php"); exit(); }

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $uid = $_SESSION['usuario_id'];
    $emocion = $_POST["emocion"] ?? '';
    $nota = trim($_POST["nota"]);

    // Validación: Emoción obligatoria
    if (empty($emocion)) {
        header("Location: ../pages/dashboard.php?error=falta_emocion&nota=" . urlencode($nota));
        exit();
    }

    $stmt = $conexion->prepare("INSERT INTO entradas (usuario_id, emocion, nota, fecha) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $uid, $emocion, $nota);
    
    if ($stmt->execute()) {
        header("Location: ../pages/dashboard.php?exito=1");
    } else {
        header("Location: ../pages/dashboard.php?error=db");
    }
}
?>
