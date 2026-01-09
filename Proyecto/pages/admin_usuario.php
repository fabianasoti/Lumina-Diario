<?php
// --- CORRECCIÓN AQUÍ: Subimos un nivel (..) y entramos a config ---
require_once '../config/conexion.php';
session_start();

if (!isset($_SESSION['usuario_id'])) { header("Location: index.php"); exit(); }

// Verificar Admin
$mi_id = $_SESSION['usuario_id'];
$check = $conexion->query("SELECT rol FROM usuarios WHERE id = $mi_id");
if (!$check || $check->num_rows === 0 || $check->fetch_assoc()['rol'] !== 'admin') { 
    die("⛔ Acceso Denegado."); 
}

$uid_target = $_GET['id'] ?? null;
if (!$uid_target) { header("Location: admin.php"); exit(); }

// Obtener datos del usuario
$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $uid_target);
$stmt->execute();
$user_info = $stmt->get_result()->fetch_assoc();

// Obtener historial
$entradas = $conexion->query("SELECT * FROM entradas WHERE usuario_id=$uid_target ORDER BY fecha DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Perfil Usuario</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <style>
         .entry-item { background: #fff; border: 1px solid #eee; padding: 15px; margin-bottom: 10px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; }
         .entry-danger { border-left: 5px solid #d63031; }
         .entry-emoji { font-size: 24px; margin-right: 15px; }
    </style>
</head>
<body>
<div class="card wide fade-in">
    <?php include 'menu.php'; ?>
    
    <div style="background:#f4f4f9; padding:20px; border-radius:10px; margin-bottom:20px; text-align:center;">
        <h2 style="color:#9d4edd;"><?= htmlspecialchars($user_info['nombre']) ?></h2>
        <p><?= htmlspecialchars($user_info['email']) ?></p>
        <br>
        <a href="admin.php" style="color:#555; text-decoration:none;">⬅️ Volver al Panel</a>
    </div>

    <h3>Historial de Entradas</h3>
    <?php while($h = $entradas->fetch_assoc()): ?>
        <div class="entry-item entry-danger">
            <div style="display:flex; align-items:center;">
                <span class="entry-emoji">
                    <?php
                        $e = strtolower($h['emocion']);
                        if ($e == 'feliz') echo '😄'; elseif ($e == 'triste') echo '😔'; elseif ($e == 'enojado') echo '😡'; elseif ($e == 'ansioso') echo '😰'; elseif ($e == 'calmado') echo '😌'; else echo '😐';
                    ?>
                </span>
                <div>
                    <b><?= htmlspecialchars($h['nota']) ?></b> <br>
                    <small style="color:#888;"><?= date("d/m/Y H:i", strtotime($h['fecha'])) ?></small>
                </div>
            </div>
            <a href="../backend/borrar.php?id=<?= $h['id'] ?>" style="color:#d63031; text-decoration:none; font-weight:bold;" onclick="return confirm('¿Borrar esta entrada?')">🗑️ Eliminar</a>
        </div>
    <?php endwhile; ?>
</div>
</body>
</html>
