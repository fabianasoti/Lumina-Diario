<?php
require_once '../config/conexion.php';
$paso = 1; // Paso 1: Verificar correo | Paso 2: Cambiar contraseña
$email = "";
$error = "";
$mensaje = "";

// 1. SI VENIMOS DE OLVIDE_PASSWORD (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email_recuperacion'])) {
    $email = $_POST['email_recuperacion'];
    
    // Verificar si existe el email
    $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $paso = 2; // Email encontrado, mostrar formulario de cambio
    } else {
        header("Location: olvide_password.php?error=noexiste");
        exit();
    }
}

// 2. SI VENIMOS DE CAMBIAR LA CONTRASEÑA (POST FINAL)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nueva_pass'])) {
    $email_final = $_POST['email_hidden'];
    $pass = $_POST['nueva_pass'];
    
    // Encriptar y guardar
    $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $conexion->prepare("UPDATE usuarios SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $pass_hash, $email_final);
    
    if($stmt->execute()) {
        header("Location: index.php?registro=exito"); // Reusamos el mensaje de éxito
        exit();
    } else {
        $error = "Hubo un error al actualizar.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer - Lumina</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
    <div class="login-container fade-in">
        <div class="logo-area">
            <img src="../assets/img/luminalogo.png" alt="Logo Lumina" class="logo-circular" style="width:100px; height:100px;">
            <h2>Nueva Contraseña</h2>
        </div>

        <?php if($error): ?>
            <p style="color:red; text-align:center;"><?= $error ?></p>
        <?php endif; ?>

        <?php if ($paso == 2): ?>
            <p style="text-align:center; color:#5a189a; margin-bottom:15px;">
                Hola, hemos encontrado tu cuenta: <br><strong><?= htmlspecialchars($email) ?></strong>
            </p>

            <form action="restablecer.php" method="POST">
                <input type="hidden" name="email_hidden" value="<?= htmlspecialchars($email) ?>">
                <input type="password" name="nueva_pass" placeholder="Escribe tu nueva contraseña" required>
                <button type="submit">Guardar Nueva Contraseña</button>
            </form>
        <?php else: ?>
            <p style="text-align:center;">Acceso inválido.</p>
            <a href="index.php" style="display:block; text-align:center; margin-top:15px;">Volver</a>
        <?php endif; ?>
    </div>
</body>
</html>
