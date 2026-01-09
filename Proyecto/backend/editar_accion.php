<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['usuario_id'])) { header("Location: ../pages/index.php"); exit(); }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $emocion = $_POST['emocion'];
    $nota = $_POST['nota'];
    $uid = $_SESSION['usuario_id'];
    
    // Solo actualiza si el ID coincide Y pertenece al usuario logueado
    $sql = "UPDATE entradas SET emocion = ?, nota = ? WHERE id = ? AND usuario_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssii", $emocion, $nota, $id, $uid);
    
    $stmt->execute();
    header("Location: ../pages/dashboard.php");
}
?>
