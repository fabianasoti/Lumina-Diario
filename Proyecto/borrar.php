<?php
// --- VER ERRORES EN PANTALLA ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// -------------------------------

session_start();
require_once 'conexion.php'; // Usamos require_once por seguridad

// Verificamos sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

// Verificamos que llegue un ID
if (isset($_GET['id'])) {
    $id_entrada = $_GET['id'];
    $usuario_id = $_SESSION['usuario_id'];

    // SQL Seguro
    $sql = "DELETE FROM entradas WHERE id = ? AND usuario_id = ?";
    
    // OJO: Aquí cambié $conn por $conexion
    $stmt = $conexion->prepare($sql);
    
    if ($stmt === false) {
        die("Error en la consulta SQL: " . $conexion->error);
    }

    $stmt->bind_param("ii", $id_entrada, $usuario_id);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
    } else {
        echo "Hubo un error al borrar: " . $stmt->error;
    }
    
    $stmt->close();
} else {
    header("Location: dashboard.php");
}

// OJO: Aquí cambié $conn por $conexion
$conexion->close();
?>
