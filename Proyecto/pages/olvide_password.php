<?php
// Corrección de ruta para evitar Error 500
require_once '../config/conexion.php';
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Lumina</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
    <div class="login-container fade-in">
        <div class="logo-area">
            <img src="../assets/img/luminalogo.png" alt="Logo Lumina" class="logo-circular" style="width:120px; height:120px;">
            <h3>Recuperar Acceso</h3>
            <p>Introduce tu correo para buscar tu cuenta</p>
        </div>

        <?php if(isset($_GET['error']) && $_GET['error'] == 'noexiste'): ?>
            <div style="background:#ffdddd; color:#d63031; padding:10px; border-radius:8px; margin-bottom:15px; font-size:0.9rem;">
                No encontramos ese correo electrónico.
            </div>
        <?php endif; ?>

        <form action="restablecer.php" method="POST">
            <input type="email" name="email_recuperacion" placeholder="Tu correo electrónico" required>
            <button type="submit">Buscar Cuenta</button>
        </form>
        
        <p class="pie">
            <a href="index.php">Volver al inicio de sesión</a>
        </p>
    </div>
</body>
</html>
