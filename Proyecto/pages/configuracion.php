<?php
// Configuración de sesión eterna
ini_set('session.gc_maxlifetime', 86400);
session_set_cookie_params(86400);

// Anti-caché
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once '../config/conexion.php';
session_start();

if (!isset($_SESSION['usuario_id'])) { header("Location: index.php"); exit(); }
$uid = $_SESSION['usuario_id'];

$stmt = $conexion->prepare("SELECT nombre, email, ultimo_cambio_nombre FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$puede_cambiar_nombre = true;
$tiempo_restante = "";

if ($user['ultimo_cambio_nombre']) {
    $ultimo_cambio = strtotime($user['ultimo_cambio_nombre']);
    $ahora = time();
    $diferencia = $ahora - $ultimo_cambio;
    
    if ($diferencia < 86400) {
        $puede_cambiar_nombre = false;
        $horas_restantes = ceil((86400 - $diferencia) / 3600);
        $tiempo_restante = "Debes esperar $horas_restantes horas para volver a cambiarlo.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración - Lumina</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <style>
        .alert { padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 0.9rem; text-align: center; }
        .alert-error { background: #ffdddd; color: #d63031; }
        .alert-success { background: #ddffdd; color: #2ecc71; }
        .disabled-input { background: #eee; color: #888; cursor: not-allowed; }
    </style>
</head>
<body>
<div class="card fade-in">
    <?php include 'menu.php'; ?>
    
    <h2 style="text-align:center; color:#9d4edd; margin-bottom:20px;">⚙️ Configuración</h2>

    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-error">
            <?php 
                if($_GET['error'] == 'existe') echo "¡Ese nombre de usuario ya está en uso!";
                if($_GET['error'] == 'tiempo') echo "Aún no han pasado 24 horas.";
                if($_GET['error'] == 'pass') echo "Las contraseñas no coinciden.";
                if($_GET['error'] == 'db') echo "Error del sistema.";
            ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['exito'])): ?>
        <div class="alert alert-success">¡Datos actualizados correctamente!</div>
    <?php endif; ?>

    <form action="../backend/actualizar_perfil.php" method="POST">
        
        <div class="config-section">
            <h4 style="color:#5a189a; margin-bottom:10px;">Perfil</h4>
            
            <label>Nombre de Usuario:</label>
            <?php if ($puede_cambiar_nombre): ?>
                <input type="text" name="nuevo_nombre" value="<?= htmlspecialchars($user['nombre']) ?>" required>
                <span class="info-text">El nombre debe ser único. Solo puedes cambiarlo una vez cada 24h.</span>
            <?php else: ?>
                <input type="text" value="<?= htmlspecialchars($user['nombre']) ?>" class="disabled-input" readonly>
                <span class="info-text" style="color:#d63031;">⏳ <?= $tiempo_restante ?></span>
            <?php endif; ?>
            
            <label>Correo (No editable):</label>
            <input type="email" value="<?= htmlspecialchars($user['email']) ?>" class="disabled-input" readonly>
        </div>

        <div class="config-section">
            <h4 style="color:#5a189a; margin-bottom:10px;">Seguridad</h4>
            <span class="info-text">Deja esto en blanco si no quieres cambiar tu contraseña.</span>
            
            <div class="password-wrapper">
                <input type="password" name="nueva_pass" id="newPass" placeholder="Nueva contraseña">
                <span class="toggle-password" onclick="togglePass('newPass')">👁️</span>
            </div>
            
            <div class="password-wrapper">
                <input type="password" name="confirmar_pass" id="confPass" placeholder="Confirmar nueva contraseña">
                <span class="toggle-password" onclick="togglePass('confPass')">👁️</span>
            </div>
        </div>

        <button type="submit">Guardar Cambios</button>
    </form>
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
