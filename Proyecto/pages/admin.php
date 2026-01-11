<?php
// --- SEGURIDAD ANTI-CACHÉ ---
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once '../config/conexion.php'; 
session_start();

if (!isset($_SESSION['usuario_id'])) { header("Location: index.php"); exit(); }
$uid = $_SESSION['usuario_id'];

$check = $conexion->query("SELECT rol FROM usuarios WHERE id = $uid");
if (!$check || $check->num_rows === 0) { session_destroy(); header("Location: index.php"); exit(); }
$datos_usuario = $check->fetch_assoc();
if ($datos_usuario['rol'] !== 'admin') { die("⛔ Acceso Denegado."); }

$total_entradas_global = $conexion->query("SELECT COUNT(*) as total FROM entradas")->fetch_assoc()['total'];
$total_users = $conexion->query("SELECT COUNT(*) as total FROM usuarios")->fetch_assoc()['total'];

$sql_stats = "SELECT emocion, COUNT(*) as cantidad FROM entradas 
              WHERE MONTH(fecha) = MONTH(CURRENT_DATE()) AND YEAR(fecha) = YEAR(CURRENT_DATE()) 
              GROUP BY emocion ORDER BY cantidad DESC";
$res_stats = $conexion->query($sql_stats);

$labels = []; $data = []; $emocion_predominante = "Sin datos"; $max_cantidad = 0;
while($row = $res_stats->fetch_assoc()) {
    $labels[] = ucfirst($row['emocion']);
    $data[] = $row['cantidad'];
    if ($row['cantidad'] > $max_cantidad) { $max_cantidad = $row['cantidad']; $emocion_predominante = $row['emocion']; }
}

$sql_users = "SELECT u.id, u.nombre, u.email, u.rol, u.fecha_creacion, u.ultima_conexion, COUNT(e.id) as num_entradas 
              FROM usuarios u LEFT JOIN entradas e ON u.id = e.usuario_id 
              GROUP BY u.id ORDER BY u.id DESC";
$res_users = $conexion->query($sql_users);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Lumina</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
    <style>
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; gap: 15px; flex-wrap: wrap; }
        .stats-box { background: #9d4edd; color: white; padding: 20px; border-radius: 12px; flex: 1; text-align: center; box-shadow: 0 4px 6px rgba(157, 78, 221, 0.2); min-width: 150px; }
        .stats-num { font-size: 32px; font-weight: bold; margin-top: 5px; }
        .stats-label { font-size: 14px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px; }
        th { background: #f8f9fa; padding: 12px; text-align: left; color: #6c5ce7; font-weight: bold; border-bottom: 2px solid #e0c3fc; }
        td { padding: 12px; border-bottom: 1px solid #eee; vertical-align: middle; }
        
        .badge { padding: 5px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .badge-admin { background: #7b2cbf; color: white; }
        .badge-user { background: #e0aaff; color: #5a189a; }
        
        .btn-action { text-decoration: none; font-size: 12px; padding: 6px 12px; border-radius: 6px; display: inline-block; margin: 2px; transition: all 0.2s ease; }
        .btn-ver { background-color: transparent; color: #9d4edd; border: 2px solid #9d4edd; font-weight: bold; }
        .btn-ver:hover { background-color: #9d4edd; color: white; }
        .btn-ascender { border: 1px solid #ccc; background: white; color: #555; }
        
        .chart-container { background: #fff; border: 1px solid #e0c3fc; border-radius: 12px; padding: 25px; margin-bottom: 30px; display: flex; align-items: center; justify-content: space-around; flex-wrap: wrap; }
        .chart-box { width: 280px; height: 280px; }
        
        .status-dot { height: 10px; width: 10px; border-radius: 50%; display: inline-block; margin-right: 5px; }
        .online { background-color: #2ecc71; box-shadow: 0 0 5px #2ecc71; }
        .offline { background-color: #dfe6e9; }
    </style>
</head>
<body>

<div class="card wide fade-in">
    <?php include 'menu.php'; ?>

    <div class="admin-header">
        <div style="flex: 2; min-width: 200px;">
            <h2 style="color: #5a189a;">🛡️ Panel Administrativo</h2>
            <p class="sub">Gestión del sistema Lumina</p>
        </div>
        <div class="stats-box">
            <div class="stats-label">Usuarios</div>
            <div class="stats-num"><?= $total_users ?></div>
        </div>
        <div class="stats-box">
            <div class="stats-label">Emociones</div>
            <div class="stats-num"><?= $total_entradas_global ?></div>
        </div>
    </div>

    <div class="chart-container">
        <div class="chart-box"><canvas id="emocionesChart"></canvas></div>
        <div style="text-align: center; max-width: 300px;">
            <h3 style="color: #5a189a;">Tendencia del Mes</h3>
            <?php if($max_cantidad > 0): ?>
                <span style="font-size: 2.2rem; font-weight: 800; color: #9d4edd; text-transform: uppercase; letter-spacing: 2px;">
                    <?= strtoupper($emocion_predominante) ?>
                </span>
            <?php else: ?>
                <span style="color: #aaa;">Sin datos suficientes</span>
            <?php endif; ?>
        </div>
    </div>

    <h3 style="color: #5a189a; margin-bottom: 15px;">Usuarios Registrados</h3>
    
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Estado</th>
                    <th>Rol</th>
                    <th style="text-align: center;">Registros</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($u = $res_users->fetch_assoc()): ?>
                    <?php $is_online = ($u['ultima_conexion'] && (time() - strtotime($u['ultima_conexion']) < 300)); ?>
                    <tr>
                        <td>
                            <strong style="color: #333;"><?= htmlspecialchars($u['nombre']) ?></strong><br>
                            <span style="color:#888; font-size:12px;"><?= htmlspecialchars($u['email']) ?></span>
                        </td>
                        <td>
                            <?= $is_online ? '<span class="status-dot online"></span>' : '<span class="status-dot offline"></span>' ?>
                        </td>
                        <td>
                            <span class="badge <?= ($u['rol']=='admin')?'badge-admin':'badge-user' ?>">
                                <?= $u['rol'] ?>
                            </span>
                        </td>
                        <td style="font-weight: bold; text-align: center; color: #5a189a;">
                            <?= $u['num_entradas'] ?>
                        </td>
                        <td>
                            <a href="admin_usuario.php?id=<?= $u['id'] ?>" class="btn-action btn-ver">Ver Perfil</a>
                            
                            <?php if($u['id'] != $uid): ?>
                                <?php if($u['rol'] == 'user'): ?>
                                    <a href="../backend/cambiar_rol.php?id=<?= $u['id'] ?>&rol=admin" class="btn-action btn-ascender" onclick="return confirm('¿Dar permisos de Administrador?')">⬆️</a>
                                <?php else: ?>
                                    <a href="../backend/cambiar_rol.php?id=<?= $u['id'] ?>&rol=user" class="btn-action btn-ascender" onclick="return confirm('¿Quitar permisos de Administrador?')">⬇️</a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    const ctx = document.getElementById('emocionesChart').getContext('2d');
    const etiquetas = <?= json_encode($labels) ?>;
    const valores = <?= json_encode($data) ?>;
    const coloresLumina = ['#e0aaff', '#ffadad', '#a0c4ff', '#caffbf', '#fdffb6', '#ffc6ff'];

    if (etiquetas.length > 0) {
        new Chart(ctx, {
            type: 'doughnut',
            data: { labels: etiquetas, datasets: [{ data: valores, backgroundColor: coloresLumina, borderWidth: 0, hoverOffset: 10 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }, cutout: '70%' }
        });
    } else {
        ctx.font = "14px Arial"; ctx.fillStyle = "#888"; ctx.textAlign = "center"; ctx.fillText("No hay datos este mes", 140, 140);
    }
</script>
</body>
</html>
