<?php
session_start();
require_once 'conexion.php';

// 1. SEGURIDAD: ¿Quien hace la petición es Admin?
if (!isset($_SESSION['usuario_id'])) { header("Location: index.php"); exit(); }

$mi_id = $_SESSION['usuario_id'];
$check = $conexion->query("SELECT rol FROM usuarios WHERE id = $mi_id");
$mi_rol = $check->fetch_assoc()['rol'];

if ($mi_rol !== 'admin') {
    die("⛔ Acceso denegado.");
}

// 2. RECIBIR DATOS
if (isset($_GET['id']) && isset($_GET['rol'])) {
    $id_usuario_objetivo = $_GET['id'];
    $nuevo_rol = $_GET['rol'];

    // 3. REGLA DE SEGURIDAD (ANTI-SUICIDIO DIGITAL)
    // No permitimos que un admin se quite el rol a sí mismo
    if ($id_usuario_objetivo == $mi_id) {
        echo "<script>
                alert('⚠️ No puedes quitarte el rol de Admin a ti mismo.');
                window.location.href='admin.php';
              </script>";
        exit();
    }

    // 4. EJECUTAR EL CAMBIO
    // Solo permitimos roles válidos para evitar inyecciones raras
    if ($nuevo_rol == 'admin' || $nuevo_rol == 'user') {
        $stmt = $conexion->prepare("UPDATE usuarios SET rol = ? WHERE id = ?");
        $stmt->bind_param("si", $nuevo_rol, $id_usuario_objetivo);
        
        if ($stmt->execute()) {
            header("Location: admin.php"); // Éxito
        } else {
            echo "Error SQL: " . $conexion->error;
        }
    }
} else {
    header("Location: admin.php");
}
?>
