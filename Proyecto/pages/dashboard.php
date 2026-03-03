<?php
// Configuración de sesión eterna (24 horas)
ini_set('session.gc_maxlifetime', 86400);
session_set_cookie_params(86400);

// Anti-caché
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once '../config/conexion.php';
session_start();

if (!isset($_SESSION["usuario_id"])) { header("Location: index.php"); exit(); }
$uid = $_SESSION["usuario_id"];
$nombre = $_SESSION["usuario_nombre"];

$mensaje = "";
$tipo_mensaje = "";

if (isset($_GET['exito'])) $tipo_mensaje = "exito";
if (isset($_GET['error'])) {
    $tipo_mensaje = "error";
    if ($_GET['error'] == 'falta_emocion') $mensaje = "⚠️ Selecciona al menos una emoción.";
    if ($_GET['error'] == 'db') $mensaje = "⚠️ Error de conexión. Intenta de nuevo.";
}

// Historial breve (Solo lectura)
$historial = [];
$stmt = $conexion->prepare("SELECT id, emocion, nota, fecha, intensidad FROM entradas WHERE usuario_id = ? ORDER BY fecha DESC LIMIT 3");
$stmt->bind_param("i", $uid);
$stmt->execute();
$res = $stmt->get_result();
while ($r = $res->fetch_assoc()) { $historial[] = $r; }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Lumina</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <style>
        .error { background:#ffdddd; color:#d8000c; padding:10px; border-radius:5px; text-align:center; margin-bottom:15px; }
        .exito { background:#ddffdd; color:#4F8A10; padding:10px; border-radius:5px; text-align:center; margin-bottom:15px; }
    </style>
</head>
<body>
<div class="card wide fade-in">
    <?php include 'menu.php'; ?>

    <div style="text-align:center; margin-bottom:20px;">
        <h2>Hola, <?= htmlspecialchars($nombre) ?> 👋</h2>
        <p class="sub">¿Cómo te sientes hoy?</p>
    </div>

    <div class="help-banner">
        <span class="help-icon">❤️‍🩹</span>
        <div>
            <strong>¿Necesitas apoyo?</strong><br>
            Si te sientes abrumado, recuerda que no estás solo. Llama al <strong>024</strong> (Ayuda conducta suicida) o busca apoyo profesional.
        </div>
    </div>

    <?php if ($tipo_mensaje == "exito"): ?><div class="exito">¡Entrada guardada con éxito!</div><?php endif; ?>
    <?php if ($mensaje): ?><div class="error"><?= $mensaje ?></div><?php endif; ?>

    <form action="../backend/guardar_entrada.php" method="POST">
        
        <div class="mood-selector">
            
            <label class="mood-option">
                <input type="checkbox" name="emocion[]" value="Feliz">
                <span class="mood-emoji">😄</span>
                <span class="mood-label">Feliz</span>
            </label>

            <label class="mood-option">
                <input type="checkbox" name="emocion[]" value="Calmado">
                <span class="mood-emoji">😌</span>
                <span class="mood-label">Calmado</span>
            </label>

            <label class="mood-option">
                <input type="checkbox" name="emocion[]" value="Triste">
                <span class="mood-emoji">😔</span>
                <span class="mood-label">Triste</span>
            </label>

            <label class="mood-option">
                <input type="checkbox" name="emocion[]" value="Ansioso">
                <span class="mood-emoji">😰</span>
                <span class="mood-label">Ansioso</span>
            </label>

            <label class="mood-option">
                <input type="checkbox" name="emocion[]" value="Enojado">
                <span class="mood-emoji">😡</span>
                <span class="mood-label">Enojado</span>
            </label>

        </div>

        <div class="range-container">
            <div class="range-label">
                <span>Intensidad (General)</span>
                <span id="valorIntensidad">5/10</span>
            </div>
            <input type="range" name="intensidad" min="1" max="10" value="5" oninput="document.getElementById('valorIntensidad').innerText = this.value + '/10'">
        </div>

        <input type="text" name="nota" placeholder="Añade una nota breve para estas emociones..." autocomplete="off">
        <button type="submit">Guardar Registro</button>
    </form>

    <hr style="margin:20px 0; border:0; border-top:1px solid #eee;">
    <h3>Últimos registros</h3>
    
    <?php foreach ($historial as $h): ?>
        <div class="entry-item">
            <span class="entry-intensity">Nivel: <?= $h['intensidad'] ?? 5 ?></span>
            <div style="display:flex; align-items:center; gap: 10px;">
                <span style="font-size: 1.8rem;">
                    <?php 
                        $e = strtolower($h['emocion']);
                        if ($e == 'feliz') echo '😄'; 
                        elseif ($e == 'triste') echo '😔'; 
                        elseif ($e == 'enojado') echo '😡'; 
                        elseif ($e == 'ansioso') echo '😰'; 
                        elseif ($e == 'calmado') echo '😌'; 
                        else echo '😐';
                    ?>
                </span>
                
                <div>
                    <small style="color:#888;"><?= date("d/m H:i", strtotime($h['fecha'])) ?></small><br>
                    <strong style="color:#5a189a;"><?= ucfirst($h['emocion']) ?></strong>
                    <?php if(!empty($h['nota'])): ?>
                        <span style="color:#555;">: <?= htmlspecialchars($h['nota']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    
    <div style="text-align:center; margin-top:15px;">
        <a href="historial.php" style="color:#9d4edd; font-weight:bold; text-decoration:none;">Ver todo el historial →</a>
    </div>
</div>
</body>
</html>
