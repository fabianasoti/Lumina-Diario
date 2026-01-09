<?php
require_once '../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    // Encriptamos la contraseña por seguridad
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // 1. Verificar si el email ya existe
    $check = $conexion->prepare("SELECT id FROM usuarios WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        // Error: Ya existe
        header("Location: ../pages/registro_vista.php?error=existe");
        exit();
    }

    // 2. Crear usuario nuevo (rol 'user' por defecto)
    $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, email, password, rol, ultima_conexion) VALUES (?, ?, ?, 'user', NOW())");
    $stmt->bind_param("sss", $nombre, $email, $pass);
    
    if ($stmt->execute()) {
        // Éxito: Mandar al login
        header("Location: ../pages/index.php?registro=exito");
    } else {
        header("Location: ../pages/registro_vista.php?error=db");
    }
}
?>
