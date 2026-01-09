<?php
require_once '../config/conexion.php';
session_start();

if (!isset($_SESSION["usuario_id"])) { header("Location: index.php"); exit(); }
$uid = $_SESSION["usuario_id"];

// Contar emociones
$sql = "SELECT emocion, COUNT(*) as cantidad FROM entradas WHERE usuario_id = ? GROUP BY emocion";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $uid);
$stmt->execute();
$res = $stmt->get_result();

$labels = [];
$data = [];
while ($row = $res->fetch_assoc()) {
    $labels[] = ucfirst($row['emocion']); // Primera letra mayúscula
    $data[] = $row['cantidad'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estadísticas - Lumina</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="card wide fade-in">
    <?php include 'menu.php'; ?>
    
    <h2 style="text-align:center; margin-bottom: 20px; color: #9d4edd;">Tus Emociones</h2>

    <div style="height: 350px; width: 100%;">
        <?php if(empty($labels)): ?>
            <div style="text-align:center; padding-top:100px; color:#aaa;">
                <p>Aún no hay suficientes datos.</p>
                <p>¡Escribe en tu diario para ver la magia! ✨</p>
            </div>
        <?php else: ?>
            <canvas id="miGrafica"></canvas>
        <?php endif; ?>
    </div>
</div>

<script>
    const ctx = document.getElementById('miGrafica');
    
    // PALETA PASTEL LUMINA (Coherente con el diseño)
    const coloresPastel = [
        '#e0aaff', // Lila suave (Color principal)
        '#ffadad', // Rojo pastel suave
        '#a0c4ff', // Azul cielo
        '#caffbf', // Verde menta
        '#fdffb6', // Amarillo suave
        '#ffc6ff'  // Rosa
    ];

    if (ctx) {
        new Chart(ctx, {
            type: 'bar', 
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    label: 'Registros',
                    data: <?= json_encode($data) ?>,
                    backgroundColor: coloresPastel,
                    borderRadius: 8, // Bordes redondeados en las barras
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { 
                    y: { 
                        beginAtZero: true, 
                        ticks: { stepSize: 1, color: '#888' },
                        grid: { color: '#f0f0f0' } // Líneas de guía muy suaves
                    },
                    x: {
                        ticks: { color: '#666' },
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false } // Ocultamos la leyenda porque ya se entiende
                }
            }
        });
    }
</script>
</body>
</html>
