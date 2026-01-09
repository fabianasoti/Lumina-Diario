<?php
// NO incluimos conexion.php aquí para evitar conflictos
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificamos rol
$es_admin = false;
if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
    $es_admin = true;
}
?>

<nav class="app-menu">
    <a href="dashboard.php" class="menu-item">🏠 Inicio</a>
    <a href="historial.php" class="menu-item">📅 Historial</a>
    <a href="estadisticas.php" class="menu-item">📊 Estadísticas</a>
    
    <?php if ($es_admin): ?>
        <a href="admin.php" class="menu-item" style="color: #d63031;">🛡️ Admin</a>
    <?php endif; ?>

    <a href="../backend/logout.php" class="menu-item salir">❌ Salir</a>
</nav>
