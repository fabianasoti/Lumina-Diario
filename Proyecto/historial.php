<?php
require_once 'conexion.php';
session_start();

// 1. SEGURIDAD
if (!isset($_SESSION["usuario_id"])) {
    header("Location: index.php");
    exit();
}

$uid = $_SESSION["usuario_id"];

// 2. CONSULTA: Traer TODO el historial ordenado por fecha (el más nuevo primero)
$sql = "SELECT id, emocion, nota, fecha FROM entradas WHERE usuario_id = ? ORDER BY fecha DESC";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $uid);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Historial - Lumina</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

<div class="card wide fade-in">
    <div class="header">
        <h2>📜 Tu Historial</h2>
        <a href="dashboard.php" class="link" style="margin:0;">Volver al Dashboard</a>
    </div>
    
    <p class="sub">Aquí tienes todas tus anotaciones ordenadas cronológicamente.</p>

    <?php if ($resultado->num_rows === 0): ?>
        <div class="msg">Aún no tienes registros. ¡Ve al Dashboard y crea el primero!</div>
    <?php else: ?>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Emoción</th>
                        <th>Nota</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($fila = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td style="color:#666; font-size:13px; white-space:nowrap;">
                                <?= date("d/m/Y h:i A", strtotime($fila['fecha'])) ?>
                            </td>
                            
                            <td style="font-size: 20px; text-align: center;">
                                <?php
                                $e = $fila['emocion'];
                                if ($e == 'feliz') echo '😄';
                                elseif ($e == 'triste') echo '😔';
                                elseif ($e == 'enfadado') echo '😡';
                                elseif ($e == 'ansioso') echo '😰';
                                else echo '😌';
                                ?>
                            </td>
                            
                            <td style="color:#444;">
                                <?= htmlspecialchars($fila['nota']) ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>
    
    <?php $stmt->close(); ?>
</div>

</body>
</html>
