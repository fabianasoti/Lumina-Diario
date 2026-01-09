<?php
// --- ACTIVAR REPORTE DE ERRORES (Solo para depurar) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// -----------------------------------------------------

// 1. INICIO DE SESIÓN Y CONEXIÓN
require_once 'conexion.php';
session_start();

// Si ya hay sesión, mandamos directo al Dashboard
if (isset($_SESSION["usuario_id"])) {
    header("Location: dashboard.php");
    exit();
}

$errores = [];
$identificador = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $identificador = trim($_POST["identificador"]);
    $password = trim($_POST["password"]);

    if (empty($identificador) || empty($password)) {
        $errores[] = "Rellena todos los campos.";
    } else {
        // Buscamos usuario
        $stmt = $conexion->prepare("SELECT id, nombre, password FROM usuarios WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $identificador, $identificador);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $nombre, $hash);
            $stmt->fetch();

            if (password_verify($password, $hash)) {
                // --- AQUÍ ESTÁ LA CLAVE ---
                // 1. Guardamos datos en sesión
                $_SESSION["usuario_id"] = $id;
                $_SESSION["usuario_nombre"] = $nombre;

                // 2. Redirigimos
                header("Location: dashboard.php");
                exit();
            } else {
                $errores[] = "Credenciales incorrectas.";
            }
        } else {
            $errores[] = "Credenciales incorrectas.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Lumina</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="card fade-in">
        <div style="font-size: 44px; margin-bottom: 10px;">🧠</div>
        <h2>Bienvenido/a</h2>
        <p class="sub">Cuida tu bienestar emocional</p>

        <?php if (!empty($errores)): ?>
            <div class="errores"><?= $errores[0] ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Email o Usuario</label>
            <input type="text" name="identificador" value="<?= htmlspecialchars($identificador) ?>" required>
            
            <label>Contraseña</label>
            <input type="password" name="password" required>
            
            <button type="submit">Iniciar sesión</button>
        </form>

        <div style="margin-top: 15px;">
            <a href="olvide_password.php" class="link">¿Olvidaste tu contraseña?</a>
            <br>
            <a href="registro.php" class="link" style="font-weight: bold;">Crear cuenta nueva</a>
        </div>
    </div>
</body>
</html>
