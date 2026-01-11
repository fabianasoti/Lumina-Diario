<?php
// Configuración de sesión y caché
ini_set('session.gc_maxlifetime', 86400);
session_set_cookie_params(86400);
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once '../config/conexion.php';
session_start();

if (!isset($_SESSION["usuario_id"])) { header("Location: index.php"); exit(); }
$uid = $_SESSION["usuario_id"];

// Verificar ID
if (!isset($_GET['id'])) { header("Location: historial.php"); exit(); }
$id_entrada = $_GET['id'];

// Obtener datos actuales de la entrada
$stmt = $conexion->prepare("SELECT * FROM entradas WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $id_entrada, $uid);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) { header("Location: historial.php"); exit(); }
$entrada = $res->fetch_assoc();

// La emoción actual la guardamos para marcarla en el checkbox
$emocion_actual = $entrada['emocion']; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Entrada - Lumina</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
<div class="card wide fade-in">
    <?php include 'menu.php'; ?>

    <div style="text-align:center; margin-bottom:20px;">
        <h2 style="color:#9d4edd;">✏️ Editar Registro</h2>
        <p class="sub">Puedes cambiar las emociones o la nota.</p>
        <p style="font-size:0.8rem; color:#888;">Fecha original: <?= date("d/m/Y H:i", strtotime($entrada['fecha'])) ?></p>
    </div>

    <form action="../backend/actualizar_entrada.php" method="POST">
        <input type="hidden" name="id_entrada" value="<?= $entrada['id'] ?>">

        <div class="mood-selector">
            
            <label class="mood-option">
                <input type="checkbox" name="emocion[]" value="Feliz" <?= ($emocion_actual == 'Feliz') ? 'checked' : '' ?>>
                <span class="mood-emoji">😄</span>
                <span class="mood-label">Feliz</span>
            </label>

            <label class="mood-option">
                <input type="checkbox" name="emocion[]" value="Calmado" <?= ($emocion_actual == 'Calmado') ? 'checked' : '' ?>>
                <span class="mood-emoji">😌</span>
                <span class="mood-label">Calmado</span>
            </label>

            <label class="mood-option">
                <input type="checkbox" name="emocion[]" value="Triste" <?= ($emocion_actual == 'Triste') ? 'checked' : '' ?>>
                <span class="mood-emoji">😔</span>
                <span class="mood-label">Triste</span>
            </label>

            <label class="mood-option">
                <input type="checkbox" name="emocion[]" value="Ansioso" <?= ($emocion_actual == 'Ansioso') ? 'checked' : '' ?>>
                <span class="mood-emoji">😰</span>
                <span class="mood-label">Ansioso</span>
            </label>

            <label class="mood-option">
                <input type="checkbox" name="emocion[]" value="Enojado" <?= ($emocion_actual == 'Enojado') ? 'checked' : '' ?>>
                <span class="mood-emoji">😡</span>
                <span class="mood-label">Enojado</span>
            </label>

        </div>

        <div class="range-container">
            <div class="range-label">
                <span>Intensidad</span>
                <span id="valorIntensidad"><?= $entrada['intensidad'] ?? 5 ?>/10</span>
            </div>
            <input type="range" name="intensidad" min="1" max="10" value="<?= $entrada['intensidad'] ?? 5 ?>" oninput="document.getElementById('valorIntensidad').innerText = this.value + '/10'">
        </div>

        <input type="text" name="nota" value="<?= htmlspecialchars($entrada['nota']) ?>" placeholder="Nota..." autocomplete="off">
        
        <div style="display:flex; gap:10px; margin-top:15px;">
            <a href="historial.php" style="flex:1; padding:14px; text-align:center; background:#eee; color:#555; border-radius:12px; text-decoration:none; font-weight:bold;">Cancelar</a>
            <button type="submit" style="flex:2; margin-top:0;">Guardar Cambios</button>
        </div>
    </form>
</div>
</body>
</html>
