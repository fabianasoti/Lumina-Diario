<?php
require_once 'conexion.php';
session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: index.php");
    exit();
}

$uid = $_SESSION["usuario_id"];

// 1. SQL AVANZADO: Contar emociones
// Resultado esperado: [ {'emocion': 'feliz', 'cantidad': 5}, {'emocion': 'triste', 'cantidad': 2} ... ]
$sql = "SELECT emocion, COUNT(*) as cantidad FROM entradas WHERE usuario_id = ? GROUP BY emocion";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $uid);
$stmt->execute();
$res = $stmt->get_result();

// Preparamos los datos para JavaScript
$etiquetas = []; // Ej: ['feliz', 'triste']
$datos = [];     // Ej: [5, 2]

while ($fila = $res->fetch_assoc()) {
    $etiquetas[] = ucfirst($fila['emocion']); // ucfirst pone la primera mayúscula
    $datos[] = $fila['cantidad'];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas - Lumina</title>
    <link rel="stylesheet" href="estilos.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="card wide fade-in">
    
    <?php include 'menu.php'; ?>

    <h2>📊 Tu Balance Emocional</h2>
    <p class="sub">Visualiza cuáles son tus emociones predominantes.</p>

    <?php if (empty($etiquetas)): ?>
        <div class="msg">Necesitas registrar más emociones para ver gráficas.</div>
    <?php else: ?>
        
        <div style="position: relative; height:300px; width:100%">
            <canvas id="miGrafico"></canvas>
        </div>

        <script>
            // Pasamos los datos de PHP a JavaScript
            // json_encode convierte el array PHP en texto que JS entiende
            const etiquetasJS = <?= json_encode($etiquetas) ?>;
            const datosJS = <?= json_encode($datos) ?>;

            // Configuración del Gráfico
            const ctx = document.getElementById('miGrafico');

            new Chart(ctx, {
                type: 'doughnut', // Tipo de gráfico: 'pie' (tarta) o 'doughnut' (dona)
                data: {
                    labels: etiquetasJS,
                    datasets: [{
                        label: 'Número de veces',
                        data: datosJS,
                        backgroundColor: [
                            '#a2d2ff', // Colores pasteles acordes a tu diseño
                            '#ffafcc',
                            '#cdb4db',
                            '#bde0fe',
                            '#ffc8dd'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' } // Leyenda abajo
                    }
                }
            });
        </script>
    <?php endif; ?>
</div>

</body>
</html>
