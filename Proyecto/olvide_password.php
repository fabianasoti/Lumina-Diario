<?php
// CONFIGURACIÓN DB
$host = "localhost"; $user = "diarioemocional"; $pass = "Diarioemocional123$"; $db = "diarioemocional";
$conexion = new mysqli($host, $user, $pass, $db);

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
            // 2. Generamos un Token único y seguro (hexadecimal)
            $token = bin2hex(random_bytes(16)); // Ejemplo: a3f1...
            
            // 3. Establecemos expiración (1 hora desde ahora)
            $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

            // 4. Guardamos el token en la BD
            $stmtUpdate = $conexion->prepare("UPDATE usuarios SET token_reset = ?, token_expira = ? WHERE email = ?");
            $stmtUpdate->bind_param("sss", $token, $expira, $email);
            
            if ($stmtUpdate->execute()) {
                // SIMULACIÓN DE ENVÍO DE EMAIL (Para entorno de desarrollo)
                // En producción, aquí usarías la función mail() o PHPMailer.
                $link = "http://localhost/tu_proyecto/restablecer.php?token=" . $token;
                
                $mensaje = "Hemos enviado un enlace a tu correo (Simulación): <br> 
                            <a href='$link'>Click aquí para recuperar</a>";
            }
        } else {
            // Por seguridad, no decimos si el email existe o no.
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
    <title>Recuperar Contraseña</title>
    <style>
        /* Estilos base reutilizados */
        *{ box-sizing:border-box; font-family:'Segoe UI',sans-serif; }
        body{ margin:0; min-height:100vh; display:flex; justify-content:center; align-items:center; background:linear-gradient(135deg,#cdb4db,#bde0fe); }
        .container{ background:#fff; width:100%; max-width:400px; padding:32px; border-radius:20px; box-shadow:0 18px 35px rgba(0,0,0,.15); text-align:center; }
        input{ width:100%; padding:12px; border-radius:12px; border:1px solid #cdb4db; margin-top:10px; margin-bottom:20px; }
        button{ width:100%; padding:12px; border:none; border-radius:14px; background:linear-gradient(135deg,#9d4edd,#5fa8d3); color:#fff; font-weight:bold; cursor:pointer; }
        .msg{ background:#e0fbfc; padding:10px; border-radius:8px; margin-bottom:15px; color:#5fa8d3; word-break: break-all; }
        .error{ color: red; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Recuperar acceso</h2>
        <p>Introduce tu email para recuperar contraseña.</p>

        <?php if ($mensaje): ?>
            <div class="msg"><?= $mensaje ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="tu@email.com" required>
            <button type="submit">Enviar enlace</button>
        </form>
        <br>
        <a href="index.php" style="color:#6d597a; text-decoration:none">Volver al inicio</a>
    </div>
</body>
</html>
