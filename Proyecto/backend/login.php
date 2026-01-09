<?php
session_start();
require_once '../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $stmt = $conexion->prepare("SELECT id, nombre, password, rol FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($fila = $res->fetch_assoc()) {
        if (password_verify($pass, $fila['password'])) {
            $_SESSION['usuario_id'] = $fila['id'];
            $_SESSION['usuario_nombre'] = $fila['nombre'];
            $_SESSION['rol'] = $fila['rol'];
            
            $conexion->query("UPDATE usuarios SET ultima_conexion = NOW() WHERE id = {$fila['id']}");
            
            header("Location: ../pages/dashboard.php");
            exit();
        }
    }
    header("Location: ../pages/index.php?error=credenciales");
    exit();
}
?>
