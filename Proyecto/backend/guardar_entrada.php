<?php
// --- MODO DEPURACIÓN ACTIVADO ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../config/conexion.php';

// Verificar sesión
if (!isset($_SESSION['usuario_id'])) { 
    header("Location: ../pages/index.php"); 
    exit(); 
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $uid = $_SESSION['usuario_id'];
    // RECIBIMOS ARRAY (Checkboxes)
    $emociones = $_POST["emocion"] ?? []; 
    $nota = trim($_POST["nota"]);
    $intensidad = $_POST["intensidad"] ?? 5; 

    // Si no llega nada (array vacío), error
    if (empty($emociones)) {
        header("Location: ../pages/dashboard.php?error=falta_emocion&nota=" . urlencode($nota));
        exit();
    }

    try {
        // Preparamos la consulta UNA sola vez
        $sql = "INSERT INTO entradas (usuario_id, emocion, nota, intensidad, fecha) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conexion->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Error al preparar SQL: " . $conexion->error);
        }

        // Iteramos sobre cada emoción seleccionada y guardamos
        // Si seleccionaste 3, se guardan 3 registros seguidos.
        foreach ($emociones as $una_emocion) {
            // "issi" -> id(int), emocion(string), nota(string), intensidad(int)
            $stmt->bind_param("issi", $uid, $una_emocion, $nota, $intensidad);
            if (!$stmt->execute()) {
                throw new Exception("Error al guardar '$una_emocion': " . $stmt->error);
            }
        }
        
        // Si todo salió bien
        header("Location: ../pages/dashboard.php?exito=1");

    } catch (Exception $e) {
        die("<div style='color:red; font-family:sans-serif; padding:20px; border:2px solid red; background:#fff;'>
                <h3>⚠️ Error al guardar:</h3>
                <p>" . $e->getMessage() . "</p>
             </div>");
    }
}
?>
