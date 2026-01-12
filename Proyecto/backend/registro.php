<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$ruta_conexion = '../config/conexion.php';
if (!file_exists($ruta_conexion)) {
    die("❌ Error Fatal: No encuentro el archivo de conexión.");
}
require_once $ruta_conexion;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recoger y limpiar datos
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $pass = $_POST['password']; // <-- Faltaba definir esta variable

    // Guardar datos en sesión para el formulario sticky
    $_SESSION['datos_registro'] = $_POST;

    // 2. Validar campos vacíos
    if (empty($nombre) || empty($email) || empty($pass)) {
        header("Location: ../pages/registro_vista.php?error=vacios");
        exit();
    }

    // 3. Validar requisitos de contraseña (Regex)
    $patron = '/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/';
    if (!preg_match($patron, $pass)) {
        header("Location: ../pages/registro_vista.php?error=password_debil");
        exit();
    }

    // 4. Lógica de Base de Datos
    try {
        // A. Verificar si ya existe el correo o el nombre
        $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE email = ? OR nombre = ?");
        if (!$stmt) throw new Exception("Error en prepare (Select): " . $conexion->error);
        
        $stmt->bind_param("ss", $email, $nombre);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            header("Location: ../pages/registro_vista.php?error=existe");
            exit();
        }

        // B. Encriptar contraseña
        $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
        
        // C. Insertar usuario
        $query_insert = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, 'user')";
        
        $insert = $conexion->prepare($query_insert);
        if (!$insert) throw new Exception("Error en prepare (Insert): " . $conexion->error);
        
        $insert->bind_param("sss", $nombre, $email, $pass_hash);

        if ($insert->execute()) {
            // SI EL REGISTRO TIENE ÉXITO:
            unset($_SESSION['datos_registro']); // Limpiamos los datos sticky
            header("Location: ../pages/index.php?registro=exito");
            exit();
        } else {
            throw new Exception("Error al ejecutar registro.");
        }

    } catch (Exception $e) {
        die("<div style='color:red; font-family:sans-serif; padding:20px; border:2px solid red;'>
                <h3>⚠️ Error Detectado:</h3>
                <p><strong>Solución probable:</strong> Revisa que tu tabla 'usuarios' tenga las columnas: nombre, email, password, rol y fecha_creacion.</p>
             </div>");
    }
}
?>
