<?php
// Configuración de sesión eterna (24 horas)
ini_set('session.gc_maxlifetime', 86400);
session_set_cookie_params(86400);

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

        <?php if(isset($_GET['error'])): ?>
            <p style="color:red; margin-bottom:10px; font-size: 0.9rem;">
              <?php 
            if($_GET['error'] == 'credenciales') echo "Usuario o contraseña incorrectos.";
            if($_GET['error'] == 'vacios') echo "Por favor, rellena todos los campos.";
              ?>
            </p>
       
    
        <?php endif; ?>

        <form action="../backend/login.php" method="POST">
            <input type="text" name="credencial" placeholder="Correo o Usuario" required>
            
            <div class="password-wrapper">
                <input type="password" name="password" id="passInput" placeholder="Contraseña" required>
                <span class="toggle-password" onclick="togglePass('passInput')">👁️</span>
            </div>

            <button type="submit">Entrar</button>
        </form>
        
        <p class="pie">
            ¿No tienes cuenta? <a href="registro_vista.php">Regístrate aquí</a><br>
            <a href="olvide_password.php" style="font-size: 0.8em; color: #999;">Recuperar contraseña</a>
        </p>
    </div>

    <script>
        function togglePass(id) {
            var input = document.getElementById(id);
            if (input.type === "password") input.type = "text";
            else input.type = "password";
        }
    </script>
</body>
</html>
