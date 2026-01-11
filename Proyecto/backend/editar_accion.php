<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['usuario_id'])) { header("Location: ../pages/index.php"); exit(); }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $emocion = $_POST['emocion'];
    $nota = $_POST['nota'];
    $intensidad = $_POST['intensidad']; // Nuevo campo
    $uid = $_SESSION['usuario_id'];
    
    $sql = "UPDATE entradas SET emocion = ?, nota = ?, intensidad = ? WHERE id = ? AND usuario_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssiii", $emocion, $nota, $intensidad, $id, $uid);
    
    $stmt->execute();
    header("Location: ../pages/dashboard.php");
}
?>
