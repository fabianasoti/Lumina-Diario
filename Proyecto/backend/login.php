<?php
// --- CONFIGURACIÓN DE ERRORES ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Limpieza de entradas
    $credencial = trim($_POST['credencial']); 
    $pass = $_POST['password'];

    // 2. Validación rápida de campos vacíos
    if (empty($credencial) || empty($pass)) {
        header("Location: ../pages/index.php?error=vacios");
        exit();
    }

    try {
        // 3. Consulta preparada para buscar por Email o Nombre (Username)
        $sql = "SELECT id, nombre, password, rol FROM usuarios WHERE email = ? OR nombre = ? LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ss", $credencial, $credencial);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($fila = $res->fetch_assoc()) {
            // 4. Verificación de la contraseña encriptada
            if (password_verify($pass, $fila['password'])) {
                
                // Login Correcto: Generamos la sesión
                $_SESSION['usuario_id'] = $fila['id'];
                $_SESSION['usuario_nombre'] = $fila['nombre'];
                $_SESSION['rol'] = $fila['rol'];
                
                // 5. Actualizar última conexión (con sentencia preparada por seguridad)
                $update_sql = "UPDATE usuarios SET ultima_conexion = NOW() WHERE id = ?";
                $stmt_upd = $conexion->prepare($update_sql);
                $stmt_upd->bind_param("i", $fila['id']);
                $stmt_upd->execute();
                
                // Redirigir al Dashboard
                header("Location: ../pages/dashboard.php");
                exit();
            }
        }
        
        // 6. Si llega aquí, es que el usuario no existe o la contraseña no coincide
        header("Location: ../pages/index.php?error=credenciales");
        exit();

    } catch (Exception $e) {
        // Error de base de datos
        die("Error en el sistema: " . $e->getMessage());
    }
}
