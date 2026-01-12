<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Recuperar los datos si existen, si no, dejarlos vacíos
$nombre_temp = $_SESSION['datos_registro']['nombre'] ?? '';
$email_temp = $_SESSION['datos_registro']['email'] ?? '';

// Borrar los datos de la sesión para que no aparezcan si el usuario refresca manualmente
unset($_SESSION['datos_registro']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Lumina</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
    <div class="login-container fade-in">
        <div class="logo-area">
            <img src="../assets/img/luminalogo.png" alt="Logo Lumina" class="logo-circular" style="width:100px; height:100px;">
            <h2>Únete a Lumina</h2>
            <p>Comienza tu viaje emocional hoy</p>
        </div>

        <?php if(isset($_GET['error'])): ?>
            <div style="background:#ffdddd; color:#d63031; padding:10px; border-radius:8px; margin-bottom:15px; font-size:0.9rem;">
                <?php 
                    if($_GET['error'] == 'existe') echo "El correo o usuario ya están registrados.";
                    if($_GET['error'] == 'db') echo "Error del sistema. Intenta más tarde.";
                    if($_GET['error'] == 'vacios') echo "Por favor llena todos los campos.";
                    // --- ESTA LÍNEA ES NUEVA ---
                    if($_GET['error'] == 'password_debil') echo "La contraseña debe tener al menos 8 caracteres, incluir letras, números y un símbolo.";
                ?>
            </div>
        <?php endif; ?>

        <form action="../backend/registro.php" method="POST">
            <input type="text" name="nombre" placeholder="Nombre de Usuario" value="<?php echo htmlspecialchars($nombre_temp); ?>" required>
            <input type="email" name="email" placeholder="Correo electrónico" value="<?php echo htmlspecialchars($email_temp); ?>" required>
            <input type="password" name="password" placeholder="Contraseña segura" title="Mínimo 8 caracteres, incluyendo letras, números y símbolos (!@#$...)" required>
            
            <button type="submit">Registrarme</button>
        </form>
        
        <p class="pie">
            ¿Ya tienes cuenta? <a href="index.php">Inicia Sesión</a>
        </p>
    </div>
</body>
</html>
