<?php
// --- SEGURIDAD ANTI-CACHÉ ---
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once '../config/conexion.php';
session_start();

if (!isset($_SESSION["usuario_id"])) { header("Location: index.php"); exit(); }
$uid = $_SESSION["usuario_id"];

$sql = "SELECT emocion, COUNT(*) as cantidad FROM entradas WHERE usuario_id = ? GROUP BY emocion";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $uid);
$stmt->execute();
$res = $stmt->get_result();

$labels = [];
$data = [];
while ($row = $res->fetch_assoc()) {
    $labels[] = ucfirst($row['emocion']);
    $data[] = $row['cantidad'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas - Lumina</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-wrapper {
            position: relative;
            height: 50vh;
            min-height: 300px;
            width: 100%;
        }
    </style>
</head>
<body>
<div class="card wide fade-in">
    <?php include 'menu.php'; ?>
    
    <h2 style="text-align:center; margin-bottom: 20px; color: #9d4edd;">Tus Emociones</h2>

    <?php if(empty($labels)): ?>
        <div style="text-align:center; padding: 50px 20px; color:#aaa;">
            <p>Aún no hay suficientes datos.</p>
            <p>¡Escribe en tu diario para ver la magia! ✨</p>
        </div>
    <?php else: ?>
        <div class="chart-wrapper">
            <canvas id="miGrafica"></canvas>
        </div>
    <?php endif; ?>

</div>

<script>
    const ctx = document.getElementById('miGrafica');
    const coloresPastel = ['#e0aaff', '#ffadad', '#a0c4ff', '#caffbf', '#fdffb6', '#ffc6ff'];

    if (ctx) {
        new Chart(ctx, {
            type: 'bar', 
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    label: 'Registros',
                    data: <?= json_encode($data) ?>,
                    backgroundColor: coloresPastel,
                    borderRadius: 8,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { 
                    y: { 
                        beginAtZero: true, 
                        ticks: { stepSize: 1, color: '#888', font: { size: 12 } },
                        grid: { color: '#f0f0f0' } 
                    },
                    x: {
                        ticks: { color: '#666', font: { size: 12 } },
                        grid: { display: false }
                    }
                }
            }
        });
    }
</script>
</body>
</html>
