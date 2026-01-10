<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Lumina</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
    <div class="login-container fade-in">
        <div class="logo-area">
            <img src="../assets/img/luminalogo.png" alt="Logo Lumina" class="logo-circular">
            
            <h1>Lumina</h1>
            <p>Support with Purpose</p>
        </div>

        <?php if(isset($_GET['error']) && $_GET['error'] == 'credenciales'): ?>
            <p style="color:red; margin-bottom:10px;">Usuario o contraseña incorrectos</p>
        <?php endif; ?>
        
        <?php if(isset($_GET['registro']) && $_GET['registro']=='exito'): ?>
            <p style="color:green; margin-bottom:10px;">¡Cuenta creada! Ingresa ahora.</p>
        <?php endif; ?>

        <form action="../backend/login.php" method="POST">
            <input type="text" name="credencial" placeholder="Correo o Usuario" required>
            
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Entrar</button>
        </form>
        
        <p class="pie">
            ¿No tienes cuenta? <a href="registro_vista.php">Regístrate aquí</a><br>
            <a href="olvide_password.php" style="font-size: 0.8em; color: #999;">Recuperar contraseña</a>
        </p>
    </div>
</body>
</html>
