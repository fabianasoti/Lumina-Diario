<?php
session_start();
require_once '../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recibimos "credencial" (puede ser el correo O el nombre)
    $credencial = $_POST['credencial']; 
    $pass = $_POST['password'];

    // 2. Consulta SQL con lógica "OR"
    // Buscamos si la credencial coincide con el email O con el nombre
    $sql = "SELECT id, nombre, password, rol FROM usuarios WHERE email = ? OR nombre = ?";
    $stmt = $conexion->prepare($sql);
    
    // "ss" significa que pasamos dos strings.
    // Pasamos la variable $credencial dos veces: una para comparar con email y otra para nombre.
    $stmt->bind_param("ss", $credencial, $credencial);
    
    $stmt->execute();
    $res = $stmt->get_result();

    if ($fila = $res->fetch_assoc()) {
        if (password_verify($pass, $fila['password'])) {
            // Login Correcto
            $_SESSION['usuario_id'] = $fila['id'];
            $_SESSION['usuario_nombre'] = $fila['nombre'];
            $_SESSION['rol'] = $fila['rol'];
            
            // Actualizar estado Online
            $conexion->query("UPDATE usuarios SET ultima_conexion = NOW() WHERE id = {$fila['id']}");
            
            header("Location: ../pages/dashboard.php");
            exit();
        }
    }
    
    // Si falla
    header("Location: ../pages/index.php?error=credenciales");
    exit();
}
?>
