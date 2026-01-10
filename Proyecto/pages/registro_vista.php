<?php
require_once '../config/conexion.php'; // 1. Cargamos la conexión centralizada

$errores = [];
$exito = false;

// Variables Sticky Form
$nombre = ""; $apellido = ""; $username = ""; $email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre   = trim($_POST["nombre"]);
    $apellido = trim($_POST["apellido"]);
    $username = trim($_POST["username"]);
    $email    = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"];

    // Validaciones básicas
    if (empty($nombre) || empty($apellido) || empty($username) || empty($email) || empty($password)) {
        $errores[] = "Todos los campos son obligatorios.";
    }
    if (!preg_match('/^[a-zA-Z0-9._]{5,20}$/', $username)) {
        $errores[] = "El username debe tener 5-20 caracteres (letras, números, . _).";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Email inválido.";
    }
    if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errores[] = "Password insegura: Mín. 8 chars, 1 Mayúscula y 1 Número.";
    }

    // Verificar duplicados
    if (empty($errores)) {
        $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errores[] = "El usuario o email ya están registrados.";
        }
        $stmt->close();
    }

    // Insertar
    if (empty($errores)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, apellido, username, email, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nombre, $apellido, $username, $email, $passwordHash);
        
        if ($stmt->execute()) {
            $exito = true;
        } else {
            $errores[] = "Error al guardar en base de datos.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Diario Emocional</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>

<div class="card wide fade-in">
    <h2>Crear Cuenta</h2>

    <?php if (!empty($errores)): ?>
        <div class="errores">
            <?php foreach ($errores as $e): echo "<p style='margin:0'>• $e</p>"; endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($exito): ?>
        <div class="exito">¡Registro completado! <a href="index.php">Inicia sesión</a>.</div>
    <?php else: ?>
        <form method="POST">
            <div style="display: flex; gap: 10px;">
                <div style="flex:1"><label>Nombre</label><input type="text" name="nombre" value="<?= htmlspecialchars($nombre) ?>" required></div>
                <div style="flex:1"><label>Apellidos</label><input type="text" name="apellido" value="<?= htmlspecialchars($apellido) ?>" required></div>
            </div>
            <label>Usuario</label><input type="text" name="username" value="<?= htmlspecialchars($username) ?>" required>
            <label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            <label>Contraseña</label><input type="password" name="password" required>
            <button type="submit">Registrarme</button>
        </form>
    <?php endif; ?>
    <div class="registro-footer">
      <a href="index.php" class="link">¿Ya tienes cuenta? Inicia sesión</a>
    </div>
</div>
</body>
</html>
