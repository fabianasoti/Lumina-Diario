<?php
// --- CORRECCIÓN DE RUTA ---
require_once '../config/conexion.php';
session_start();

if (!isset($_SESSION["usuario_id"])) { header("Location: index.php"); exit(); }
$uid = $_SESSION["usuario_id"];
$nombre = $_SESSION["usuario_nombre"];

$mensaje = "";
$tipo_mensaje = "";
$nota_input = "";

if (isset($_GET['exito'])) {
    $mensaje = "¡Emoción registrada!";
    $tipo_mensaje = "exito";
}
if (isset($_GET['error'])) {
    $tipo_mensaje = "error";
    if ($_GET['error'] == 'falta_emocion') {
        $mensaje = "⚠️ Selecciona una emoción.";
        $nota_input = $_GET['nota'] ?? '';
    }
}

// Historial
$historial = [];
$stmt = $conexion->prepare("SELECT id, emocion, nota, fecha FROM entradas WHERE usuario_id = ? ORDER BY fecha DESC LIMIT 5");
$stmt->bind_param("i", $uid);
$stmt->execute();
$res = $stmt->get_result();
while ($r = $res->fetch_assoc()) { $historial[] = $r; }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Lumina</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <style>
        .error { background:#ffdddd; color:#d8000c; padding:10px; border-radius:5px; text-align:center; margin-bottom:15px; }
        .exito { background:#ddffdd; color:#4F8A10; padding:10px; border-radius:5px; text-align:center; margin-bottom:15px; }
        .mood-selector { display:flex; gap:15px; justify-content:center; margin-bottom:15px; flex-wrap:wrap; }
        .mood-btn { font-size:28px; cursor:pointer; opacity:0.5; filter:grayscale(100%); transition:.2s; }
        input[type="radio"]:checked + .mood-btn { opacity:1; transform:scale(1.3); filter:grayscale(0%); }
        input[type="radio"] { display:none; }
        .entry-item { background:#f8f9fa; border-left:5px solid #9d4edd; padding:12px; margin-bottom:10px; border-radius:6px; display:flex; justify-content:space-between; align-items:center; }
        .entry-emoji { font-size: 24px; margin-right: 10px; }
        .btn-icono { text-decoration:none; padding:5px; font-size:16px; }
    </style>
</head>
<body>
<div class="card wide fade-in">
    <?php include 'menu.php'; ?>

    <div style="text-align:center; margin-bottom:20px;">
        <h2>Hola, <?= htmlspecialchars($nombre) ?> 👋</h2>
        <p class="sub">¿Cómo te sientes?</p>
    </div>

    <?php if ($mensaje): ?><div class="<?= $tipo_mensaje ?>"><?= $mensaje ?></div><?php endif; ?>

    <form action="../backend/guardar_entrada.php" method="POST">
        <div class="mood-selector">
            <label><input type="radio" name="emocion" value="Feliz"><span class="mood-btn">😄</span></label>
            <label><input type="radio" name="emocion" value="Calmado"><span class="mood-btn">😌</span></label>
            <label><input type="radio" name="emocion" value="Triste"><span class="mood-btn">😔</span></label>
            <label><input type="radio" name="emocion" value="Ansioso"><span class="mood-btn">😰</span></label>
            <label><input type="radio" name="emocion" value="Enojado"><span class="mood-btn">😡</span></label>
        </div>
        <input type="text" name="nota" placeholder="Nota breve..." value="<?= htmlspecialchars($nota_input) ?>" autocomplete="off">
        <button type="submit">Guardar</button>
    </form>

    <hr style="margin:30px 0; border:0; border-top:1px solid #eee;">
    <h3>Últimos registros</h3>
    
    <?php foreach ($historial as $h): ?>
        <div class="entry-item">
            <div style="display:flex; align-items:center;">
                <span class="entry-emoji">
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
                    <small style="color:#888; font-size:11px;"><?= date("d/m H:i", strtotime($h['fecha'])) ?></small><br>
                    <span style="color:#333;"><?= htmlspecialchars($h['nota']) ?></span>
                </div>
            </div>
            <div>
                <a href="editar_vista.php?id=<?= $h['id'] ?>" class="btn-icono">✏️</a>
                <a href="../backend/borrar.php?id=<?= $h['id'] ?>" class="btn-icono" onclick="return confirm('¿Borrar?')">🗑️</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
