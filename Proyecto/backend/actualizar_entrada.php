<?php
// --- MODO DEPURACIÓN ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['usuario_id'])) { header("Location: ../pages/index.php"); exit(); }

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $uid = $_SESSION['usuario_id'];
    $id_entrada = $_POST['id_entrada'];
    
    // Datos nuevos
    $emociones = $_POST["emocion"] ?? []; 
    $nota = trim($_POST["nota"]);
    $intensidad = $_POST["intensidad"];

    if (empty($emociones)) {
        // Si desmarcó todo, volvemos atrás con error
        header("Location: ../pages/editar_vista.php?id=$id_entrada&error=falta_emocion");
        exit();
    }

    try {
        // PASO 1: Obtener la FECHA ORIGINAL del registro que vamos a borrar
        // (Para que al editar no se cambie la fecha a "ahora mismo")
        $stmt_fecha = $conexion->prepare("SELECT fecha FROM entradas WHERE id = ? AND usuario_id = ?");
        $stmt_fecha->bind_param("ii", $id_entrada, $uid);
        $stmt_fecha->execute();
        $res = $stmt_fecha->get_result();
        
        if ($res->num_rows === 0) { die("Entrada no encontrada o permiso denegado."); }
        $fecha_original = $res->fetch_assoc()['fecha'];

        // PASO 2: BORRAR el registro viejo
        $stmt_borrar = $conexion->prepare("DELETE FROM entradas WHERE id = ?");
        $stmt_borrar->bind_param("i", $id_entrada);
        $stmt_borrar->execute();

        // PASO 3: INSERTAR las nuevas emociones con la FECHA VIEJA
        $sql_insert = "INSERT INTO entradas (usuario_id, emocion, nota, intensidad, fecha) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = $conexion->prepare($sql_insert);

        foreach ($emociones as $una_emocion) {
            // "issis" -> int, string, string, int, string(fecha)
            $stmt_insert->bind_param("issis", $uid, $una_emocion, $nota, $intensidad, $fecha_original);
            if (!$stmt_insert->execute()) {
                throw new Exception("Error al re-insertar: " . $stmt_insert->error);
            }
        }

        // Éxito: Volver al historial
        header("Location: ../pages/historial.php");

    } catch (Exception $e) {
        die("Error crítico: " . $e->getMessage());
    }
}
?>
