<?php
// 1. Manejo seguro de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. EL "LATIDO" 💓 (Esto soluciona tu problema)
// Si hay un usuario logueado y hay conexión, actualizamos su hora
if (isset($_SESSION['usuario_id']) && isset($conexion)) {
    $uid_latido = $_SESSION['usuario_id'];
    // Actualiza la hora actual cada vez que carga el menú
    $conexion->query("UPDATE usuarios SET ultima_conexion = NOW() WHERE id = $uid_latido");
}

// 3. Verificar rol para mostrar botón Admin
$es_admin = false;
if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
    $es_admin = true;
}
?>

<nav class="app-menu">
    <a href="dashboard.php" class="menu-item">🏠 Inicio</a>
    <a href="historial.php" class="menu-item">📅 Historial</a>
    <a href="estadisticas.php" class="menu-item">📊 Estadísticas</a>
    
    <a href="configuracion.php" class="menu-item">⚙️ Configuración</a>
    
    <?php if ($es_admin): ?>
        <a href="admin.php" class="menu-item" style="color: #d63031;">🛡️ Admin</a>
    <?php endif; ?>

    <a href="../backend/logout.php" class="menu-item salir">❌ Salir</a>
</nav>
