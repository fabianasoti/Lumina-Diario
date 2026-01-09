<?php
// --- VER ERRORES EN PANTALLA ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// -------------------------------

session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

// Validar que el ID existe
if (!isset($_GET['id'])) {
    die("Error: No has especificado qué entrada editar.");
}

$id_entrada = $_GET['id'];
$usuario_id = $_SESSION['usuario_id'];

// --- PARTE A: GUARDAR CAMBIOS (UPDATE) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emocion = $_POST['emocion'];
    $nota = $_POST['nota'];
    
    $sql = "UPDATE entradas SET emocion = ?, nota = ? WHERE id = ? AND usuario_id = ?";
    
    // CORREGIDO: $conexion
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssii", $emocion, $nota, $id_entrada, $usuario_id);
    
    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error al actualizar: " . $conexion->error;
    }
}

// --- PARTE B: LEER DATOS (SELECT) ---
$sql = "SELECT * FROM entradas WHERE id = ? AND usuario_id = ?";
// CORREGIDO: $conexion
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $id_entrada, $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();
$fila = $resultado->fetch_assoc();

if (!$fila) {
    die("Error: No se encontró la entrada o no tienes permiso para editarla.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Entrada - Lumina</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        /* Un poco de CSS rápido para que se vea bien */
        body { font-family: sans-serif; background: #f4f4f9; display: flex; justify-content: center; padding-top: 50px; }
        .contenedor-editar { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .btn-guardar { background: #6c5ce7; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; width: 100%; font-size: 16px; }
        .btn-cancelar { display: block; text-align: center; margin-top: 10px; color: #666; text-decoration: none; }
    </style>
</head>
<body>
    <div class="contenedor-editar">
        <h2>✏️ Editar recuerdo</h2>
        
        <form action="editar.php?id=<?php echo $id_entrada; ?>" method="POST">
            
            <div class="form-group">
                <label>¿Cómo te sentías?</label>
                <select name="emocion" style="width: 100%; padding: 10px;">
                    <option value="Feliz" <?php if($fila['emocion']=='Feliz' || $fila['emocion']=='feliz') echo 'selected'; ?>>😄 Feliz</option>
                    <option value="Triste" <?php if($fila['emocion']=='Triste' || $fila['emocion']=='triste') echo 'selected'; ?>>😔 Triste</option>
                    <option value="Enojado" <?php if($fila['emocion']=='Enojado' || $fila['emocion']=='enfadado') echo 'selected'; ?>>😡 Enojado</option>
                    <option value="Ansioso" <?php if($fila['emocion']=='Ansioso' || $fila['emocion']=='ansioso') echo 'selected'; ?>>😰 Ansioso</option>
                    <option value="Calmado" <?php if($fila['emocion']=='Calmado' || $fila['emocion']=='calmado') echo 'selected'; ?>>😌 Calmado</option>
                </select>
            </div>

            <div class="form-group">
                <label>Tu nota:</label>
                <textarea name="nota" rows="4" required><?php echo htmlspecialchars($fila['nota']); ?></textarea>
            </div>

            <button type="submit" class="btn-guardar">Guardar Cambios</button>
            <a href="dashboard.php" class="btn-cancelar">Cancelar</a>
        </form>
    </div>
</body>
</html>
