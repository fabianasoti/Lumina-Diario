<?php
// --- ACTIVAR REPORTE DE ERRORES (Solo para depurar el 500) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Verificar ruta de conexión
$ruta_conexion = '../config/conexion.php';
if (!file_exists($ruta_conexion)) {
    die("❌ Error Fatal: No encuentro el archivo de conexión en: " . realpath($ruta_conexion));
}
require_once $ruta_conexion;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $pass = $_POST['password'];

    // 1. Validar campos vacíos
    if (empty($nombre) || empty($email) || empty($pass)) {
        header("Location: ../pages/registro_vista.php?error=vacios");
        exit();
    }

    // 2. Verificar si ya existe el correo o el usuario
    // Usamos try-catch para capturar errores de BD
    try {
        $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE email = ? OR nombre = ?");
        if (!$stmt) {
            throw new Exception("Error en prepare (Select): " . $conexion->error);
        }
        $stmt->bind_param("ss", $email, $nombre);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            header("Location: ../pages/registro_vista.php?error=existe");
            exit();
        }

        // 3. Crear usuario
        $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
        
        // IMPORTANTE: Aquí es donde fallaba si no tenías la columna 'fecha_creacion'
        $query_insert = "INSERT INTO usuarios (nombre, email, password, rol, fecha_creacion) VALUES (?, ?, ?, 'user', NOW())";
        
        $insert = $conexion->prepare($query_insert);
        if (!$insert) {
            throw new Exception("Error en prepare (Insert): " . $conexion->error);
        }
        
        $insert->bind_param("sss", $nombre, $email, $pass_hash);

        if ($insert->execute()) {
            header("Location: ../pages/index.php?registro=exito");
        } else {
            throw new Exception("Error al ejecutar Insert: " . $insert->error);
        }
    } catch (Exception $e) {
        // En vez de Error 500, mostramos qué pasó
        die("<div style='color:red; font-family:sans-serif; padding:20px; border:2px solid red;'>
                <h3>⚠️ Error Detectado:</h3>
                <p>" . $e->getMessage() . "</p>
                <p><strong>Solución probable:</strong> Revisa que tu tabla 'usuarios' tenga las columnas: nombre, email, password, rol y fecha_creacion.</p>
             </div>");
    }
    exit();
}
?>
