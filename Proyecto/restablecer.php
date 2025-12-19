<?php
// CONFIGURACIÓN DB
$host = "localhost"; $user = "diarioemocional"; $pass = "Diarioemocional123$"; $db = "diarioemocional";
$conexion = new mysqli($host, $user, $pass, $db);

$token = $_GET["token"] ?? "";
$mensaje = "";
$error = "";
$token_valido = false;

// 1. VERIFICAR TOKEN AL CARGAR LA PÁGINA
if (!empty($token)) {
    // Buscamos usuario con ese token y que la fecha de expiración sea MAYOR a ahora
    $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE token_reset = ? AND token_expira > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $token_valido = true;
    } else {
        $error = "El enlace es inválido o ha expirado.";
    }
} else {
    $error = "No se ha proporcionado un token.";
}

// 2. PROCESAR EL CAMBIO DE CONTRASEÑA
if ($_SERVER["REQUEST_METHOD"] === "POST" && $token_valido) {
    $password = trim($_POST["password"]);
    
    // Validación de robustez (Misma lógica que en Registro)
    if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $error = "La contraseña debe tener 8 caracteres, mayúscula y número.";
    } else {
        // HASHEAR NUEVA PASSWORD
        $nuevo_hash = password_hash($password, PASSWORD_DEFAULT);

        // Actualizar password Y limpiar el token (para que no se use dos veces)
        $stmtUpdate = $conexion->prepare("UPDATE usuarios SET password = ?, token_reset = NULL, token_expira = NULL WHERE token_reset = ?");
        $stmtUpdate->bind_param("ss", $nuevo_hash, $token);
        
        if ($stmtUpdate->execute()) {
            $mensaje = "¡Contraseña actualizada! <a href='login.php'>Inicia sesión aquí</a>.";
            $token_valido = false; // Para ocultar el formulario
        } else {
            $error = "Error al actualizar la base de datos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Contraseña</title>
    <style>
        /* Estilos idénticos para coherencia */
        *{ box-sizing:border-box; font-family:'Segoe UI',sans-serif; }
        body{ margin:0; min-height:100vh; display:flex; justify-content:center; align-items:center; background:linear-gradient(135deg,#cdb4db,#bde0fe); }
        .container{ background:#fff; width:100%; max-width:400px; padding:32px; border-radius:20px; text-align:center; }
        input{ width:100%; padding:12px; border-radius:12px; border:1px solid #cdb4db; margin-top:10px; margin-bottom:20px; }
        button{ width:100%; padding:12px; border:none; border-radius:14px; background:linear-gradient(135deg,#9d4edd,#5fa8d3); color:#fff; font-weight:bold; cursor:pointer; }
        .msg{ background:#e0fbfc; padding:10px; border-radius:8px; color:#5fa8d3; }
        .error{ background:#fde2e4; color:#ff6b6b; padding:10px; border-radius:8px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Restablecer</h2>

        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($mensaje): ?>
            <div class="msg"><?= $mensaje ?></div>
        <?php endif; ?>

        <?php if ($token_valido): ?>
            <form method="POST">
                <label>Nueva Contraseña</label>
                <input type="password" name="password" required placeholder="Mínimo 8 caract.">
                <button type="submit">Cambiar contraseña</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
