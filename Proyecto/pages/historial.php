<?php
require_once '../config/conexion.php';
session_start();

if (!isset($_SESSION["usuario_id"])) { header("Location: index.php"); exit(); }
$uid = $_SESSION["usuario_id"];

// Obtener todas las entradas
$historial = [];
$stmt = $conexion->prepare("SELECT id, emocion, nota, fecha FROM entradas WHERE usuario_id = ? ORDER BY fecha DESC");
$stmt->bind_param("i", $uid);
$stmt->execute();
$resultado = $stmt->get_result();
while ($fila = $resultado->fetch_assoc()) { $historial[] = $fila; }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial - Lumina</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <style>
        .entry-item { background: #f8f9fa; border-left: 5px solid #9d4edd; padding: 15px; margin-bottom: 15px; border-radius: 6px; }
        .fecha { color: #888; font-size: 0.9em; margin-bottom: 5px; }
        .emoji { font-size: 1.5em; margin-right: 10px; }
        .contenido { display: flex; align-items: center; justify-content: space-between; }
    </style>
</head>
<body>
<div class="card wide fade-in">
    <?php include 'menu.php'; ?>
    
    <h2 style="text-align:center; color:#9d4edd;">Tu Historial Completo</h2>
    <hr style="margin: 20px 0; border:0; border-top:1px solid #eee;">

    <?php if (empty($historial)): ?>
        <p style="text-align:center; color:#aaa;">Aún no has escrito nada.</p>
    <?php else: ?>
        <?php foreach ($historial as $h): ?>
            <div class="entry-item">
                <div class="fecha"><?= date("d/m/Y \a \l\a\s H:i", strtotime($h['fecha'])) ?></div>
                <div class="contenido">
                    <div style="display:flex; align-items:center;">
                        <span class="emoji">
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
                        <span><strong><?= ucfirst($h['emocion']) ?>:</strong> <?= htmlspecialchars($h['nota']) ?></span>
                    </div>
                    <div>
                        <a href="editar_vista.php?id=<?= $h['id'] ?>" style="text-decoration:none; margin-right:10px;">✏️</a>
                        <a href="../backend/borrar.php?id=<?= $h['id'] ?>" style="text-decoration:none;" onclick="return confirm('¿Borrar?')">🗑️</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
