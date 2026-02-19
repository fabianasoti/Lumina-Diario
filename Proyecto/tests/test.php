<?php
// 1. Activar todos los errores para verlos en pantalla
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>1. PHP está funcionando ✅</h1>";

// 2. Intentar cargar el archivo de conexión
echo "<p>Intentando cargar conexion.php...</p>";

if (file_exists('conexion.php')) {
    require 'conexion.php';
    echo "<p>Archivo conexion.php cargado ✅</p>";
} else {
    die("<h2 style='color:red'>ERROR: No encuentro el archivo conexion.php</h2>");
}

// 3. Verificar la conexión a la base de datos
if (isset($conexion) && $conexion instanceof mysqli) {
    if ($conexion->connect_error) {
        die("<h2 style='color:red'>ERROR DE CONEXIÓN: " . $conexion->connect_error . "</h2>");
    } else {
        echo "<h2 style='color:green'>3. ¡CONEXIÓN EXITOSA A LA BASE DE DATOS! 🚀</h2>";
        echo "<p>Host: " . $host . "</p>";
        echo "<p>Usuario: " . $user . "</p>";
        // No mostramos la contraseña por seguridad
        echo "<p>Base de datos: " . $db . "</p>";
    }
} else {
    echo "<h2 style='color:red'>ERROR: La variable \$conexion no se creó correctamente.</h2>";
}
?>
