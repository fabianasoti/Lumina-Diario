<?php
// --- DEBUG: VER ERRORES ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --------------------------

require_once 'conexion.php';
session_start();

// 1. SEGURIDAD: Si no hay sesión iniciada, expulsamos al usuario al index
if (!isset($_SESSION["usuario_id"])) {
    header("Location: index.php");
    exit();
}

$uid = $_SESSION["usuario_id"];
$nombre = $_SESSION["usuario_nombre"];
$mensaje = "";

// 2. LÓGICA: Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $emocion = $_POST["emocion"] ?? ''; 
    $nota = trim($_POST["nota"]);
    
    if (!empty($emocion)) {
        // Insertamos la emoción en la base de datos
        $stmt = $conexion->prepare("INSERT INTO entradas (usuario_id, emocion, nota) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $uid, $emocion, $nota);
        
        if ($stmt->execute()) {
            $mensaje = "¡Emoción registrada con éxito!";
            // Redirigimos para evitar reenvío de formulario al recargar
            header("Location: dashboard.php"); 
            exit();
        } else {
            $mensaje = "Error al guardar. Inténtalo de nuevo.";
        }
        $stmt->close();
    }
}

// 3. LÓGICA: Leer las últimas 5 entradas (IMPORTANTE: AÑADIDO 'id' AL SELECT)
$historial = [];
$stmt = $conexion->prepare("SELECT id, emocion, nota, fecha FROM entradas WHERE usuario_id = ? ORDER BY fecha DESC LIMIT 5");
$stmt->bind_param("i", $uid);
$stmt->execute();
$resultado = $stmt->get_result();

while ($fila = $resultado->fetch_assoc()) {
    $historial[] = $fila;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Lumina</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        /* Estilos específicos para los botones de emociones */
        .mood-selector { display: flex; gap: 15px; justify-content: center; margin-bottom: 15px; flex-wrap: wrap; }
        .mood-btn { font-size: 28px; cursor: pointer; transition: .2s; opacity: 0.5; filter: grayscale(100%); }
        
        input[type="radio"] { display: none; }
        input[type="radio"]:checked + .mood-btn { opacity: 1; transform: scale(1.3); filter: grayscale(0%); }
        .mood-btn:hover { opacity: 0.8; transform: scale(1.1); }

        /* Estilo mejorado para las entradas de la lista */
        .entry-item { 
            background: #f8f9fa; 
            border-left: 5px solid #9d4edd; 
            padding: 12px; 
            margin-bottom: 10px; 
            border-radius: 6px;
            /* Usamos Flexbox para separar contenido de botones */
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .entry-info { flex-grow: 1; } /* El texto ocupa el espacio disponible */
        
        .entry-date { font-size: 11px; color: #888; margin-bottom: 4px; }
        .entry-content { display: flex; align-items: center; gap: 10px; }
        .entry-emoji { font-size: 20px; }

        /* Estilos para los botones de Editar/Borrar */
        .acciones-rapidas {
            display: flex;
            gap: 8px;
            margin-left: 10px;
        }
        .btn-icono {
            text-decoration: none;
            padding: 5px;
            border-radius: 4px;
            font-size: 16px;
            transition: background 0.2s;
        }
        .btn-icono:hover { background-color: #e9ecef; transform: scale(1.1); }
        .btn-editar { color: #f39c12; }
        .btn-borrar { color: #e74c3c; }
    </style>
</head>
<body>

<div class="card wide fade-in">
    
    <?php include 'menu.php'; ?>

    <div style="text-align: center; margin-bottom: 20px;">
        <h2>Hola, <?= htmlspecialchars($nombre) ?> 👋</h2>
        <p class="sub">¿Cómo te sientes en este momento?</p>
    </div>

    <?php if ($mensaje): ?>
        <div class="exito"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mood-selector">
            <label><input type="radio" name="emocion" value="Feliz" required><span class="mood-btn">😄</span></label>
            <label><input type="radio" name="emocion" value="Calmado"><span class="mood-btn">😌</span></label>
            <label><input type="radio" name="emocion" value="Triste"><span class="mood-btn">😔</span></label>
            <label><input type="radio" name="emocion" value="Ansioso"><span class="mood-btn">😰</span></label>
            <label><input type="radio" name="emocion" value="Enojado"><span class="mood-btn">😡</span></label>
        </div>

        <label>Nota breve (opcional)</label>
        <input type="text" name="nota" placeholder="Hoy me ha pasado algo bueno..." autocomplete="off">
        
        <button type="submit">Guardar en mi diario</button>
    </form>

    <hr style="border: 0; border-top: 1px solid #eee; margin: 30px 0;">

    <h3 style="color:#4a4e69; font-size:18px; text-align:left; margin-bottom: 15px;">Últimos registros</h3>
    
    <?php if (empty($historial)): ?>
        <p style="color:#bbb; font-size:14px;">No hay registros recientes.</p>
    <?php else: ?>
        <?php foreach ($historial as $h): ?>
            <div class="entry-item">
                
                <div class="entry-info">
                    <div class="entry-date">
                        <?= date("d/m/Y - H:i", strtotime($h['fecha'])) ?>
                    </div>
                    <div class="entry-content">
                        <span class="entry-emoji">
                            <?php
                                $e = strtolower($h['emocion']); // Convertimos a minusculas por si acaso
                                if ($e == 'feliz') echo '😄';
                                elseif ($e == 'triste') echo '😔';
                                elseif ($e == 'enojado' || $e == 'enfadado') echo '😡';
                                elseif ($e == 'ansioso') echo '😰';
                                elseif ($e == 'calmado') echo '😌';
                                else echo '😐';
                            ?>
                        </span>
                        <span style="color:#444; font-size: 15px;">
                            <?= htmlspecialchars($h['nota']) ?>
                        </span>
                    </div>
                </div>

                <div class="acciones-rapidas">
                    <a href="editar.php?id=<?= $h['id'] ?>" class="btn-icono btn-editar" title="Editar">✏️</a>
                    <a href="borrar.php?id=<?= $h['id'] ?>" class="btn-icono btn-borrar" onclick="return confirm('¿Seguro que quieres borrar este recuerdo?')" title="Borrar">🗑️</a>
                </div>

            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

</body>
</html>
