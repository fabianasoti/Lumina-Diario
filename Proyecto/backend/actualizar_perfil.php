<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['usuario_id'])) { header("Location: ../pages/index.php"); exit(); }

$uid = $_SESSION['usuario_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- 1. LÓGICA DE NOMBRE DE USUARIO ---
    if (isset($_POST['nuevo_nombre']) && !empty($_POST['nuevo_nombre'])) {
        $nuevo_nombre = trim($_POST['nuevo_nombre']);
        
        // Verificar si cambió
        if ($nuevo_nombre !== $_SESSION['usuario_nombre']) {
            
            // A. Verificar 24 horas (Seguridad extra backend)
            $stmt = $conexion->prepare("SELECT ultimo_cambio_nombre FROM usuarios WHERE id = ?");
            $stmt->bind_param("i", $uid);
            $stmt->execute();
            $last_change = $stmt->get_result()->fetch_assoc()['ultimo_cambio_nombre'];
            
            if ($last_change && (time() - strtotime($last_change) < 86400)) {
                header("Location: ../pages/configuracion.php?error=tiempo");
                exit();
            }

            // B. Verificar que sea único
            $check = $conexion->prepare("SELECT id FROM usuarios WHERE nombre = ? AND id != ?");
            $check->bind_param("si", $nuevo_nombre, $uid);
            $check->execute();
            
            if ($check->get_result()->num_rows > 0) {
                header("Location: ../pages/configuracion.php?error=existe");
                exit();
            }

            // C. Actualizar nombre y fecha
            $update = $conexion->prepare("UPDATE usuarios SET nombre = ?, ultimo_cambio_nombre = NOW() WHERE id = ?");
            $update->bind_param("si", $nuevo_nombre, $uid);
            $update->execute();
            
            $_SESSION['usuario_nombre'] = $nuevo_nombre; // Actualizar sesión
        }
    }

    // --- 2. LÓGICA DE CONTRASEÑA ---
    if (!empty($_POST['nueva_pass'])) {
        $pass1 = $_POST['nueva_pass'];
        $pass2 = $_POST['confirmar_pass'];
        
        if ($pass1 !== $pass2) {
            header("Location: ../pages/configuracion.php?error=pass");
            exit();
        }
        
        $pass_hash = password_hash($pass1, PASSWORD_DEFAULT);
        $update_pass = $conexion->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $update_pass->bind_param("si", $pass_hash, $uid);
        $update_pass->execute();
    }

    header("Location: ../pages/configuracion.php?exito=1");
    exit();
}
?>
