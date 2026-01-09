<?php
// CORRECCIÓN: Ruta correcta a config
require_once '../config/conexion.php';
session_start();

if (!isset($_SESSION['usuario_id'])) { header("Location: index.php"); exit(); }

$id = $_GET['id'] ?? null;
$uid = $_SESSION['usuario_id'];

if (!$id) { header("Location: dashboard.php"); exit(); }

// Obtener datos para rellenar el formulario
$stmt = $conexion->prepare("SELECT * FROM entradas WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $id, $uid);
$stmt->execute();
$resultado = $stmt->get_result();
$fila = $resultado->fetch_assoc();

if (!$fila) { die("Entrada no encontrada o no tienes permiso."); }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Entrada</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
    <div class="card fade-in">
        <h2 style="text-align:center; color:#9d4edd; margin-bottom:20px;">✏️ Editar Entrada</h2>
        
        <form action="../backend/editar_accion.php" method="POST">
            <input type="hidden" name="id" value="<?= $fila['id'] ?>">
            
            <label>¿Cómo te sentías?</label>
            <select name="emocion">
                <option value="Feliz" <?= ($fila['emocion']=='Feliz')?'selected':'' ?>>Feliz</option>
                <option value="Triste" <?= ($fila['emocion']=='Triste')?'selected':'' ?>>Triste</option>
                <option value="Enojado" <?= ($fila['emocion']=='Enojado')?'selected':'' ?>>Enojado</option>
                <option value="Ansioso" <?= ($fila['emocion']=='Ansioso')?'selected':'' ?>>Ansioso</option>
                <option value="Calmado" <?= ($fila['emocion']=='Calmado')?'selected':'' ?>>Calmado</option>
            </select>

            <label>Tu nota:</label>
            <textarea name="nota" rows="5"><?= htmlspecialchars($fila['nota']) ?></textarea>
            
            <div style="display:flex; gap:10px; margin-top:15px;">
                <a href="dashboard.php" style="flex:1; background:#ccc; text-align:center; padding:12px; border-radius:8px; text-decoration:none; color:black;">Cancelar</a>
                <button type="submit" style="flex:2;">Guardar Cambios</button>
            </div>
        </form>
    </div>
</body>
</html>
