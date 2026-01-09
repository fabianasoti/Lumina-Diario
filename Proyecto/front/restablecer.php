<?php
require_once 'conexion.php';

$token = $_GET["token"] ?? "";
$mensaje = "";
$error = "";
$token_valido = false;

// 1. VERIFICAR TOKEN
if (!empty($token)) {
    $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE token_reset = ? AND token_expira > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $token_valido = true;
    } else {
        $error = "El enlace es inválido o ha expirado.";
    }
    $stmt->close();
} else {
    $error = "No se ha proporcionado un token.";
}

// 2. CAMBIAR CONTRASEÑA
if ($_SERVER["REQUEST_METHOD"] === "POST" && $token_valido) {
    $password = trim($_POST["password"]);
    
    // Validamos robustez (igual que en registro)
    if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $error = "La contraseña debe tener 8 caracteres, mayúscula y número.";
    } else {
        $nuevo_hash = password_hash($password, PASSWORD_DEFAULT);

        // Actualizamos password y borramos el token para que no se use más veces
        $stmtUpdate = $conexion->prepare("UPDATE usuarios SET password = ?, token_reset = NULL, token_expira = NULL WHERE token_reset = ?");
        $stmtUpdate->bind_param("ss", $nuevo_hash, $token);
        
        if ($stmtUpdate->execute()) {
            $mensaje = "¡Contraseña actualizada! <br><a href='index.php'>Inicia sesión aquí</a>.";
            $token_valido = false; // Ocultamos el formulario
        } else {
            $error = "Error técnico al actualizar.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="card fade-in">
        <h2>Nueva Contraseña</h2>

        <?php if ($error): ?>
            <div class="errores"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($mensaje): ?>
            <div class="exito"><?= $mensaje ?></div>
        <?php endif; ?>

        <?php if ($token_valido): ?>
            <form method="POST">
                <label>Escribe tu nueva contraseña</label>
                <input type="password" name="password" required placeholder="Mínimo 8 caract.">
                <button type="submit">Cambiar contraseña</button>
            </form>
        <?php endif; ?>
        
        <br>
        <a href="index.php" class="link">Volver al inicio</a>
    </div>
</body>
</html>
