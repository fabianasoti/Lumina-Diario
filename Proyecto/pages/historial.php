<?php
// --- SEGURIDAD ANTI-CACHÉ ---
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once '../config/conexion.php';
session_start();

if (!isset($_SESSION["usuario_id"])) { header("Location: index.php"); exit(); }
$uid = $_SESSION["usuario_id"];

// Lógica de fechas
$mes = isset($_GET['mes']) ? (int)$_GET['mes'] : (int)date('n');
$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : (int)date('Y');
$dia_seleccionado = isset($_GET['dia']) ? (int)$_GET['dia'] : null;

// Navegación
$mes_siguiente = $mes + 1; $anio_siguiente = $anio;
if ($mes_siguiente > 12) { $mes_siguiente = 1; $anio_siguiente++; }
$mes_anterior = $mes - 1; $anio_anterior = $anio;
if ($mes_anterior < 1) { $mes_anterior = 12; $anio_anterior--; }

$meses_es = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
$dias_en_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
$primer_dia_semana = date('N', strtotime("$anio-$mes-01"));

$entradas_mes = [];
$stmt = $conexion->prepare("SELECT DAY(fecha) as dia, emocion FROM entradas WHERE usuario_id = ? AND MONTH(fecha) = ? AND YEAR(fecha) = ?");
$stmt->bind_param("iii", $uid, $mes, $anio);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $entradas_mes[$row['dia']][] = strtolower($row['emocion']);
}

$sql_lista = "SELECT * FROM entradas WHERE usuario_id = ? AND MONTH(fecha) = ? AND YEAR(fecha) = ?";
$params_types = "iii";
$params_vals = [$uid, $mes, $anio];
if ($dia_seleccionado) {
    $sql_lista .= " AND DAY(fecha) = ?";
    $params_types .= "i";
    $params_vals[] = $dia_seleccionado;
}
$sql_lista .= " ORDER BY fecha DESC";
$stmt_lista = $conexion->prepare($sql_lista);
$stmt_lista->bind_param($params_types, ...$params_vals);
$stmt_lista->execute();
$resultado_lista = $stmt_lista->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial - Lumina</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <style>
        body { display: block; overflow-y: auto; } 
        .main-container { max-width: 800px; margin: 0 auto; padding: 20px 10px; }
    </style>
</head>
<body>

<div class="main-container fade-in">
    
    <div class="card wide" style="margin-bottom: 0;">
        <?php include 'menu.php'; ?>
        
        <div class="calendar-header">
            <a href="?mes=<?= $mes_anterior ?>&anio=<?= $anio_anterior ?>" class="btn-nav">❮</a>
            <h3><?= $meses_es[$mes] . " " . $anio ?></h3>
            <a href="?mes=<?= $mes_siguiente ?>&anio=<?= $anio_siguiente ?>" class="btn-nav">❯</a>
        </div>
    </div>

    <div class="calendar-grid">
        <div class="day-name">Lun</div><div class="day-name">Mar</div><div class="day-name">Mié</div>
        <div class="day-name">Jue</div><div class="day-name">Vie</div><div class="day-name">Sáb</div>
        <div class="day-name">Dom</div>

        <?php
        for ($i = 1; $i < $primer_dia_semana; $i++) echo '<div class="day-cell empty"></div>';

        for ($dia = 1; $dia <= $dias_en_mes; $dia++) {
            $clase = "day-cell";
            if ($dia == date('j') && $mes == date('n') && $anio == date('Y')) $clase .= " today";
            if ($dia == $dia_seleccionado) $clase .= " selected";
            
            echo "<a href='?mes=$mes&anio=$anio&dia=$dia' class='$clase'>";
            echo "<span>$dia</span>";
            
            if (isset($entradas_mes[$dia])) {
                echo '<div class="dots-container">';
                $count = 0;
                foreach ($entradas_mes[$dia] as $emo) {
                    if ($count < 3) echo "<div class='dot $emo'></div>";
                    $count++;
                }
                echo '</div>';
            }
            echo "</a>";
        }
        ?>
    </div>

    <div class="card wide">
        <div style="text-align:center; margin-bottom:15px;">
            <?php if($dia_seleccionado): ?>
                <h4 style="color:#9d4edd;">Registros del día <?= $dia_seleccionado ?></h4>
                <a href="?mes=<?= $mes ?>&anio=<?= $anio ?>" style="font-size:0.8rem; color:#666;">(Ver todo el mes)</a>
            <?php else: ?>
                <h4 style="color:#9d4edd;">Todos los registros del mes</h4>
                <small style="color:#aaa;">Toca un día arriba para filtrar</small>
            <?php endif; ?>
        </div>
        <hr style="border:0; border-top:1px solid #eee; margin-bottom:20px;">

        <?php if ($resultado_lista->num_rows === 0): ?>
            <p style="text-align:center; color:#aaa; padding: 20px;">No hay registros en este periodo.</p>
        <?php else: ?>
            <?php while ($h = $resultado_lista->fetch_assoc()): ?>
                <div class="entry-item">
                    <div class="entry-header">
                        <span style="font-size:0.85rem; color:#888;">
                            <?= date("d/m H:i", strtotime($h['fecha'])) ?>
                        </span>
                        <span style="background:#e0c3fc; color:#5a189a; font-size:0.7rem; padding:2px 6px; border-radius:10px;">
                            Intensidad: <?= $h['intensidad'] ?? 5 ?>
                        </span>
                    </div>
                    
                    <div style="display:flex; align-items:center; gap:10px; margin: 5px 0;">
                        <span style="font-size:1.5rem;">
                            <?php
                                $e = strtolower($h['emocion']);
                                if ($e == 'feliz') echo '😄'; elseif ($e == 'triste') echo '😔'; 
                                elseif ($e == 'enojado') echo '😡'; elseif ($e == 'ansioso') echo '😰'; 
                                elseif ($e == 'calmado') echo '😌'; else echo '😐';
                            ?>
                        </span>
                        <div style="font-size:0.95rem;">
                            <strong><?= ucfirst($h['emocion']) ?></strong>: <?= htmlspecialchars($h['nota']) ?>
                        </div>
                    </div>

                    <div class="entry-actions">
                        <a href="editar_vista.php?id=<?= $h['id'] ?>" style="text-decoration:none; font-size:1.2rem;">✏️</a>
                        <a href="../backend/borrar.php?id=<?= $h['id'] ?>" style="text-decoration:none; font-size:1.2rem;" onclick="return confirm('¿Borrar?')">🗑️</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

</div>
</body>
</html>
