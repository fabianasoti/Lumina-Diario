<?php
require_once 'conexion.php'; // Usamos la conexión centralizada

$mensaje = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email inválido.";
    } else {
        // 1. Verificamos si el email existe
        $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // 2. Generamos Token (Solo el token, la fecha la calcula MySQL)
            $token = bin2hex(random_bytes(16));

            // 3. Guardamos en BD
            // CAMBIO CLAVE: Usamos DATE_ADD(NOW()...) en vez de ? para la fecha
            $stmtUpdate = $conexion->prepare("UPDATE usuarios SET token_reset = ?, token_expira = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?");
            
            // CAMBIO CLAVE: "ss" en lugar de "sss" (porque ya no pasamos la fecha desde PHP)
            $stmtUpdate->bind_param("ss", $token, $email);
            
            if ($stmtUpdate->execute()) {
                // SIMULACIÓN DE EMAIL
                // Mantenemos tu ruta correcta: lumina_modificado
                $link = "http://localhost/lumina_modificado/Proyecto/restablecer.php?token=" . $token;
                
                $mensaje = "Hemos enviado un enlace a tu correo.<br> 
                            <small>(Modo desarrollo: <a href='$link'>Click aquí para simular el email</a>)</small>";
            }
        } else {
            // Por seguridad, damos el mismo mensaje exista o no
            $mensaje = "Si el email existe, recibirás instrucciones.";
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
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="card fade-in">
        <h2>Recuperar acceso</h2>
        <p class="sub">Introduce tu email asociado.</p>

        <?php if ($mensaje): ?>
            <div class="msg"><?= $mensaje ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="errores"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Email</label>
            <input type="email" name="email" placeholder="tu@email.com" required>
            <button type="submit">Enviar enlace</button>
        </form>
        
        <a href="index.php" class="link">Volver al inicio</a>
    </div>
</body>
</html>
