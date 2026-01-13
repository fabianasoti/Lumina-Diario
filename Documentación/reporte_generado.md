# Reporte de proyecto

## Estructura del proyecto

```
/var/www/html/Lumina-Diario/Proyecto
├── assets
│   ├── css
│   │   └── estilos.css
│   └── img
│       └── luminalogo.png
├── backend
│   ├── actualizar_entrada.php
│   ├── actualizar_perfil.php
│   ├── borrar.php
│   ├── cambiar_rol.php
│   ├── editar_accion.php
│   ├── guardar_entrada.php
│   ├── login.php
│   ├── logout.php
│   └── registro.php
├── base de datos.sql
├── config
│   └── conexion.php
├── pages
│   ├── admin.php
│   ├── admin_usuario.php
│   ├── configuracion.php
│   ├── dashboard.php
│   ├── editar_vista.php
│   ├── estadisticas.php
│   ├── historial.php
│   ├── index.php
│   ├── menu.php
│   ├── olvide_password.php
│   ├── registro_vista.php
│   └── restablecer.php
└── test.php
```

## Código (intercalado)

# Proyecto
**base de datos.sql**
```sql
CREATE DATABASE diarioemocional;
USE diarioemocional;

CREATE USER 
'diarioemocional'@'localhost' 
IDENTIFIED  BY 'Diarioemocional123$';

GRANT USAGE ON *.* TO 'diarioemocional'@'localhost';

ALTER USER 'diarioemocional'@'localhost' 
REQUIRE NONE 
WITH MAX_QUERIES_PER_HOUR 0 
MAX_CONNECTIONS_PER_HOUR 0 
MAX_UPDATES_PER_HOUR 0 
MAX_USER_CONNECTIONS 0;

GRANT ALL PRIVILEGES ON diarioemocional.* 
TO 'diarioemocional'@'localhost';

FLUSH PRIVILEGES;


CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol VARCHAR(20) DEFAULT 'user',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    token_reset VARCHAR(64) NULL DEFAULT NULL,
    token_expira DATETIME NULL DEFAULT NULL,
    ultima_conexion DATETIME NULL DEFAULT NULL,
    ultimo_cambio_nombre DATETIME NULL DEFAULT NULL
);

CREATE TABLE entradas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    emocion VARCHAR(50),
    intensidad INT DEFAULT 5,
    nota TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

/* 3. ¡IMPORTANTE! Conviértete TÚ en el administrador */
/* Cambia 'tu_email@ejemplo.com' por TU email real con el que te registraste */
UPDATE usuarios SET rol = 'admin' WHERE email = 'tu-nombre@ejemplo.com';



```
**test.php**
```php
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

```
## assets
### css
**estilos.css**
```css
/* --- 1. RESET Y BASE --- */
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }

body {
    background: linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 15px;
}

/* --- 2. CONTENEDORES RESPONSIVE --- */
.login-container, .card {
    background: rgba(255, 255, 255, 0.95);
    padding: 1.5rem;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    width: 100%;
    margin: 0 auto;
}

.login-container { max-width: 400px; text-align: center; }
.card { max-width: 500px; }
.wide { max-width: 800px; } 

/* --- 3. LOGO --- */
.logo-circular {
    width: 140px; 
    height: 140px;
    border-radius: 50%;
    object-fit: cover;
    display: block;
    margin: 0 auto 15px auto;
    border: 4px solid #e0c3fc;
    box-shadow: 0 5px 15px rgba(157, 78, 221, 0.3);
}
.logo-area h1 { color: #5a189a; margin-bottom: 5px; font-size: 1.8rem; }
.logo-area p { color: #888; margin-bottom: 20px; font-size: 0.95rem; }

/* --- 4. FORMULARIOS E INPUTS --- */
input, select, textarea {
    width: 100%;
    padding: 12px;
    margin: 8px 0;
    border: 1px solid #ddd;
    border-radius: 12px;
    outline: none;
    font-size: 16px; 
    background: #fdfdfd;
}
input:focus, textarea:focus, select:focus { border-color: #9d4edd; background: white; }

/* OJITO DENTRO DE LA BARRA */
.password-wrapper { position: relative; width: 100%; margin: 8px 0; }
.password-wrapper input { margin: 0; padding-right: 45px; width: 100%; }
.toggle-password { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; font-size: 1.2rem; color: #888; z-index: 2; user-select: none; }
.toggle-password:hover { color: #9d4edd; }

/* Slider de Intensidad */
.range-container { margin: 15px 0; }
.range-label { display: flex; justify-content: space-between; font-size: 0.9rem; color: #555; }
input[type=range] { -webkit-appearance: none; background: transparent; margin: 10px 0; width: 100%; }
input[type=range]::-webkit-slider-thumb { -webkit-appearance: none; height: 20px; width: 20px; border-radius: 50%; background: #9d4edd; cursor: pointer; margin-top: -8px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
input[type=range]::-webkit-slider-runnable-track { width: 100%; height: 6px; background: #e0c3fc; border-radius: 5px; cursor: pointer; }

button {
    width: 100%; padding: 14px; background: #9d4edd; color: white;
    border: none; border-radius: 12px; cursor: pointer; font-size: 16px; font-weight: bold; margin-top: 10px;
    transition: 0.2s;
}
button:hover { background: #7b2cbf; transform: scale(1.02); }

/* --- 5. SELECTOR DE EMOCIONES (CHECKBOX MULTIPLE) --- */
.mood-selector { display: flex; gap: 15px; justify-content: center; margin-bottom: 20px; padding: 10px 0; }
.mood-option { position: relative; cursor: pointer; display: flex; flex-direction: column; align-items: center; transition: transform 0.2s; }
.mood-emoji { font-size: 32px; filter: grayscale(100%); opacity: 0.6; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); display: block; }

/* Etiqueta flotante */
.mood-label { position: absolute; top: -30px; background: #5a189a; color: white; font-size: 0.75rem; padding: 4px 8px; border-radius: 12px; opacity: 0; transform: translateY(10px); pointer-events: none; transition: all 0.3s ease; white-space: nowrap; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
.mood-label::after { content: ''; position: absolute; bottom: -4px; left: 50%; transform: translateX(-50%); border-left: 5px solid transparent; border-right: 5px solid transparent; border-top: 5px solid #5a189a; }

/* Animaciones para Checkbox */
.mood-option:hover .mood-emoji, 
input[type="checkbox"]:checked + .mood-emoji { 
    filter: grayscale(0%); opacity: 1; transform: scale(1.4) translateY(-5px); 
}
.mood-option:hover .mood-label, 
input[type="checkbox"]:checked ~ .mood-label { 
    opacity: 1; transform: translateY(0); 
}
.mood-option input[type="checkbox"] { display: none; }

/* --- 6. CALENDARIO FLOTANTE --- */
.calendar-header { display: flex; justify-content: space-between; align-items: center; }
.calendar-header h3 { color: #5a189a; text-transform: capitalize; font-size: 1.4rem; margin: 0; }
.btn-nav { text-decoration: none; color: #9d4edd; font-weight: bold; font-size: 1.2rem; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; background: rgba(157, 78, 221, 0.1); border-radius: 50%; transition: 0.3s; }
.btn-nav:hover { background: #9d4edd; color: white; }
.calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px; margin: 25px auto; max-width: 800px; width: 100%; }
.day-name { text-align: center; font-size: 0.9rem; color: #fff; font-weight: bold; padding-bottom: 5px; text-transform: uppercase; text-shadow: 0 1px 2px rgba(0,0,0,0.1); }
.day-cell { aspect-ratio: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; background: rgba(255, 255, 255, 0.35); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px); border: 1px solid rgba(255, 255, 255, 0.6); border-radius: 50%; box-shadow: 0 4px 10px rgba(0,0,0,0.05); color: #4a148c; text-decoration: none; font-weight: 600; font-size: 1rem; position: relative; transition: all 0.2s ease; }
.day-cell:hover { transform: translateY(-5px) scale(1.05); background: rgba(255, 255, 255, 0.8); border-color: #fff; }
.day-cell.today { border: 3px solid #fff; background: rgba(157, 78, 221, 0.6); color: white; }
.day-cell.selected { background: #5a189a; color: white; border-color: #5a189a; box-shadow: 0 5px 15px rgba(90, 24, 154, 0.4); }
.day-cell.empty { background: transparent; border: none; box-shadow: none; pointer-events: none; backdrop-filter: none; }
.dots-container { display: flex; gap: 3px; position: absolute; bottom: 8px; }
.dot { width: 5px; height: 5px; border-radius: 50%; box-shadow: 0 1px 2px rgba(0,0,0,0.2); }
.dot.feliz { background: #ffd700; }
.dot.triste { background: #3498db; }
.dot.enojado { background: #e74c3c; }
.dot.ansioso { background: #9b59b6; }
.dot.calmado { background: #2ecc71; }

/* --- 7. MENÚ Y OTROS --- */
.app-menu { display: flex; justify-content: center; gap: 8px; margin-bottom: 20px; flex-wrap: wrap; }
.menu-item { padding: 8px 15px; border-radius: 20px; background: rgba(255,255,255,0.6); border: 1px solid rgba(255,255,255,0.8); text-decoration: none; color: #555; font-size: 0.9rem; }
.menu-item:hover { background: #e0c3fc; color: #5a189a; }
.salir { color: #d63031; border-color: #ffe6e6; }
.help-banner { background: #ffeaa7; border-left: 5px solid #fdcb6e; color: #d35400; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 15px; font-size: 0.95rem; }
.help-icon { font-size: 2rem; }
.config-section { margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
.config-section:last-child { border-bottom: none; }
.info-text { font-size: 0.85rem; color: #888; margin-top: -5px; margin-bottom: 10px; display: block; }
.entry-item { background: #fff; border-left: 5px solid #9d4edd; padding: 15px; margin-bottom: 15px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); position: relative; }
.entry-intensity { position: absolute; top: 10px; right: 10px; background: #e0c3fc; color: #5a189a; font-size: 0.7rem; padding: 2px 8px; border-radius: 10px; font-weight: bold; }

@media (max-width: 600px) {
    body { padding: 10px; align-items: flex-start; }
    .card, .login-container { padding: 1.2rem; border-radius: 15px; }
    .calendar-grid { gap: 4px; } 
    .day-cell { font-size: 0.85rem; }
    .logo-circular { width: 100px; height: 100px; }
    .menu-item { padding: 8px 12px; font-size: 0.8rem; flex-grow: 1; text-align: center; }
}

```
### img
## backend
**actualizar_entrada.php**
```php
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

```
**actualizar_perfil.php**
```php
<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['usuario_id'])) { header("Location: ../pages/index.php"); exit(); }

$uid = $_SESSION['usuario_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- 1. LÓGICA DE NOMBRE DE USUARIO ---
    if (isset($_POST['nuevo_nombre']) && !empty($_POST['nuevo_nombre'])) {
        $nuevo_nombre = trim($_POST['nuevo_nombre']);
        
        // Verificar si cambió
        if ($nuevo_nombre !== $_SESSION['usuario_nombre']) {
            
            // A. Verificar 24 horas (Seguridad extra backend)
            $stmt = $conexion->prepare("SELECT ultimo_cambio_nombre FROM usuarios WHERE id = ?");
            $stmt->bind_param("i", $uid);
            $stmt->execute();
            $last_change = $stmt->get_result()->fetch_assoc()['ultimo_cambio_nombre'];
            
            if ($last_change && (time() - strtotime($last_change) < 86400)) {
                header("Location: ../pages/configuracion.php?error=tiempo");
                exit();
            }

            // B. Verificar que sea único
            $check = $conexion->prepare("SELECT id FROM usuarios WHERE nombre = ? AND id != ?");
            $check->bind_param("si", $nuevo_nombre, $uid);
            $check->execute();
            
            if ($check->get_result()->num_rows > 0) {
                header("Location: ../pages/configuracion.php?error=existe");
                exit();
            }

            // C. Actualizar nombre y fecha
            $update = $conexion->prepare("UPDATE usuarios SET nombre = ?, ultimo_cambio_nombre = NOW() WHERE id = ?");
            $update->bind_param("si", $nuevo_nombre, $uid);
            $update->execute();
            
            $_SESSION['usuario_nombre'] = $nuevo_nombre; // Actualizar sesión
        }
    }

    // --- 2. LÓGICA DE CONTRASEÑA ---
    if (!empty($_POST['nueva_pass'])) {
        $pass1 = $_POST['nueva_pass'];
        $pass2 = $_POST['confirmar_pass'];
        
        if ($pass1 !== $pass2) {
            header("Location: ../pages/configuracion.php?error=pass");
            exit();
        }
        
        $pass_hash = password_hash($pass1, PASSWORD_DEFAULT);
        $update_pass = $conexion->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $update_pass->bind_param("si", $pass_hash, $uid);
        $update_pass->execute();
    }

    header("Location: ../pages/configuracion.php?exito=1");
    exit();
}
?>

```
**borrar.php**
```php
<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['usuario_id'])) { header("Location: ../pages/index.php"); exit(); }

$mi_id = $_SESSION['usuario_id'];
$id_entrada = $_GET['id'] ?? null;

if ($id_entrada) {
    // Verificar rol actual
    $check_rol = $conexion->query("SELECT rol FROM usuarios WHERE id = $mi_id");
    $soy_admin = ($check_rol->fetch_assoc()['rol'] === 'admin');

    if ($soy_admin) {
        // ADMIN: Puede borrar cualquier entrada (incluso de otros)
        $stmt = $conexion->prepare("DELETE FROM entradas WHERE id = ?");
        $stmt->bind_param("i", $id_entrada);
    } else {
        // USUARIO: Solo borra si el 'usuario_id' coincide con el suyo
        $stmt = $conexion->prepare("DELETE FROM entradas WHERE id = ? AND usuario_id = ?");
        $stmt->bind_param("ii", $id_entrada, $mi_id);
    }

    $stmt->execute();
}

// Redirección inteligente (Vuelve a donde estabas)
if (isset($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    header("Location: ../pages/dashboard.php");
}
?>

```
**cambiar_rol.php**
```php
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

```
**editar_accion.php**
```php
<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['usuario_id'])) { header("Location: ../pages/index.php"); exit(); }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $emocion = $_POST['emocion'];
    $nota = $_POST['nota'];
    $intensidad = $_POST['intensidad']; // Nuevo campo
    $uid = $_SESSION['usuario_id'];
    
    $sql = "UPDATE entradas SET emocion = ?, nota = ?, intensidad = ? WHERE id = ? AND usuario_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssiii", $emocion, $nota, $intensidad, $id, $uid);
    
    $stmt->execute();
    header("Location: ../pages/dashboard.php");
}
?>

```
**guardar_entrada.php**
```php
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

```
**login.php**
```php
<?php
// --- CONFIGURACIÓN DE ERRORES ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Limpieza de entradas
    $credencial = trim($_POST['credencial']); 
    $pass = $_POST['password'];

    // 2. Validación rápida de campos vacíos
    if (empty($credencial) || empty($pass)) {
        header("Location: ../pages/index.php?error=vacios");
        exit();
    }

    try {
        // 3. Consulta preparada para buscar por Email o Nombre (Username)
        $sql = "SELECT id, nombre, password, rol FROM usuarios WHERE email = ? OR nombre = ? LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ss", $credencial, $credencial);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($fila = $res->fetch_assoc()) {
            // 4. Verificación de la contraseña encriptada
            if (password_verify($pass, $fila['password'])) {
                
                // Login Correcto: Generamos la sesión
                $_SESSION['usuario_id'] = $fila['id'];
                $_SESSION['usuario_nombre'] = $fila['nombre'];
                $_SESSION['rol'] = $fila['rol'];
                
                // 5. Actualizar última conexión (con sentencia preparada por seguridad)
                $update_sql = "UPDATE usuarios SET ultima_conexion = NOW() WHERE id = ?";
                $stmt_upd = $conexion->prepare($update_sql);
                $stmt_upd->bind_param("i", $fila['id']);
                $stmt_upd->execute();
                
                // Redirigir al Dashboard
                header("Location: ../pages/dashboard.php");
                exit();
            }
        }
        
        // 6. Si llega aquí, es que el usuario no existe o la contraseña no coincide
        header("Location: ../pages/index.php?error=credenciales");
        exit();

    } catch (Exception $e) {
        // Error de base de datos
        die("Error en el sistema: " . $e->getMessage());
    }
}

```
**logout.php**
```php
<?php
session_start();
require_once '../config/conexion.php';

if (isset($_SESSION['usuario_id'])) {
    $uid = $_SESSION['usuario_id'];
    $conexion->query("UPDATE usuarios SET ultima_conexion = '2000-01-01 00:00:00' WHERE id = $uid");
}

session_unset();
session_destroy();
header("Location: ../pages/index.php");
exit();
?>

```
**registro.php**
```php
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$ruta_conexion = '../config/conexion.php';
if (!file_exists($ruta_conexion)) {
    die("❌ Error Fatal: No encuentro el archivo de conexión.");
}
require_once $ruta_conexion;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recoger y limpiar datos
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $pass = $_POST['password']; // <-- Faltaba definir esta variable

    // Guardar datos en sesión para el formulario sticky
    $_SESSION['datos_registro'] = $_POST;

    // 2. Validar campos vacíos
    if (empty($nombre) || empty($email) || empty($pass)) {
        header("Location: ../pages/registro_vista.php?error=vacios");
        exit();
    }

    // 3. Validar requisitos de contraseña (Regex)
    $patron = '/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/';
    if (!preg_match($patron, $pass)) {
        header("Location: ../pages/registro_vista.php?error=password_debil");
        exit();
    }

    // 4. Lógica de Base de Datos
    try {
        // A. Verificar si ya existe el correo o el nombre
        $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE email = ? OR nombre = ?");
        if (!$stmt) throw new Exception("Error en prepare (Select): " . $conexion->error);
        
        $stmt->bind_param("ss", $email, $nombre);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            header("Location: ../pages/registro_vista.php?error=existe");
            exit();
        }

        // B. Encriptar contraseña
        $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
        
        // C. Insertar usuario
        $query_insert = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, 'user')";
        
        $insert = $conexion->prepare($query_insert);
        if (!$insert) throw new Exception("Error en prepare (Insert): " . $conexion->error);
        
        $insert->bind_param("sss", $nombre, $email, $pass_hash);

        if ($insert->execute()) {
            // SI EL REGISTRO TIENE ÉXITO:
            unset($_SESSION['datos_registro']); // Limpiamos los datos sticky
            header("Location: ../pages/index.php?registro=exito");
            exit();
        } else {
            throw new Exception("Error al ejecutar registro.");
        }

    } catch (Exception $e) {
        die("<div style='color:red; font-family:sans-serif; padding:20px; border:2px solid red;'>
                <h3>⚠️ Error Detectado:</h3>
                <p><strong>Solución probable:</strong> Revisa que tu tabla 'usuarios' tenga las columnas: nombre, email, password, rol y fecha_creacion.</p>
             </div>");
    }
}
?>

```
## config
**conexion.php**
```php
<?php
date_default_timezone_set('Europe/Madrid');

$host = "localhost";
$user = "diarioemocional";
$pass = "Diarioemocional123$"; 
$db   = "diarioemocional";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conexion = new mysqli($host, $user, $pass, $db);
    $conexion->set_charset("utf8mb4");
    $conexion->query("SET time_zone = '+01:00'");
} catch (mysqli_sql_exception $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>

```
## pages
**admin.php**
```php
<?php
// --- SEGURIDAD ANTI-CACHÉ ---
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once '../config/conexion.php'; 
session_start();

if (!isset($_SESSION['usuario_id'])) { header("Location: index.php"); exit(); }
$uid = $_SESSION['usuario_id'];

$check = $conexion->query("SELECT rol FROM usuarios WHERE id = $uid");
if (!$check || $check->num_rows === 0) { session_destroy(); header("Location: index.php"); exit(); }
$datos_usuario = $check->fetch_assoc();
if ($datos_usuario['rol'] !== 'admin') { die("⛔ Acceso Denegado."); }

$total_entradas_global = $conexion->query("SELECT COUNT(*) as total FROM entradas")->fetch_assoc()['total'];
$total_users = $conexion->query("SELECT COUNT(*) as total FROM usuarios")->fetch_assoc()['total'];

$sql_stats = "SELECT emocion, COUNT(*) as cantidad FROM entradas 
              WHERE MONTH(fecha) = MONTH(CURRENT_DATE()) AND YEAR(fecha) = YEAR(CURRENT_DATE()) 
              GROUP BY emocion ORDER BY cantidad DESC";
$res_stats = $conexion->query($sql_stats);

$labels = []; $data = []; $emocion_predominante = "Sin datos"; $max_cantidad = 0;
while($row = $res_stats->fetch_assoc()) {
    $labels[] = ucfirst($row['emocion']);
    $data[] = $row['cantidad'];
    if ($row['cantidad'] > $max_cantidad) { $max_cantidad = $row['cantidad']; $emocion_predominante = $row['emocion']; }
}

$sql_users = "SELECT u.id, u.nombre, u.email, u.rol, u.fecha_creacion, u.ultima_conexion, COUNT(e.id) as num_entradas 
              FROM usuarios u LEFT JOIN entradas e ON u.id = e.usuario_id 
              GROUP BY u.id ORDER BY u.id DESC";
$res_users = $conexion->query($sql_users);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Lumina</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
    <style>
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; gap: 15px; flex-wrap: wrap; }
        .stats-box { background: #9d4edd; color: white; padding: 20px; border-radius: 12px; flex: 1; text-align: center; box-shadow: 0 4px 6px rgba(157, 78, 221, 0.2); min-width: 150px; }
        .stats-num { font-size: 32px; font-weight: bold; margin-top: 5px; }
        .stats-label { font-size: 14px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px; }
        th { background: #f8f9fa; padding: 12px; text-align: left; color: #6c5ce7; font-weight: bold; border-bottom: 2px solid #e0c3fc; }
        td { padding: 12px; border-bottom: 1px solid #eee; vertical-align: middle; }
        
        .badge { padding: 5px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .badge-admin { background: #7b2cbf; color: white; }
        .badge-user { background: #e0aaff; color: #5a189a; }
        
        .btn-action { text-decoration: none; font-size: 12px; padding: 6px 12px; border-radius: 6px; display: inline-block; margin: 2px; transition: all 0.2s ease; }
        .btn-ver { background-color: transparent; color: #9d4edd; border: 2px solid #9d4edd; font-weight: bold; }
        .btn-ver:hover { background-color: #9d4edd; color: white; }
        .btn-ascender { border: 1px solid #ccc; background: white; color: #555; }
        
        .chart-container { background: #fff; border: 1px solid #e0c3fc; border-radius: 12px; padding: 25px; margin-bottom: 30px; display: flex; align-items: center; justify-content: space-around; flex-wrap: wrap; }
        .chart-box { width: 280px; height: 280px; }
        
        .status-dot { height: 10px; width: 10px; border-radius: 50%; display: inline-block; margin-right: 5px; }
        .online { background-color: #2ecc71; box-shadow: 0 0 5px #2ecc71; }
        .offline { background-color: #dfe6e9; }
    </style>
</head>
<body>

<div class="card wide fade-in">
    <?php include 'menu.php'; ?>

    <div class="admin-header">
        <div style="flex: 2; min-width: 200px;">
            <h2 style="color: #5a189a;">🛡️ Panel Administrativo</h2>
            <p class="sub">Gestión del sistema Lumina</p>
        </div>
        <div class="stats-box">
            <div class="stats-label">Usuarios</div>
            <div class="stats-num"><?= $total_users ?></div>
        </div>
        <div class="stats-box">
            <div class="stats-label">Emociones</div>
            <div class="stats-num"><?= $total_entradas_global ?></div>
        </div>
    </div>

    <div class="chart-container">
        <div class="chart-box"><canvas id="emocionesChart"></canvas></div>
        <div style="text-align: center; max-width: 300px;">
            <h3 style="color: #5a189a;">Tendencia del Mes</h3>
            <?php if($max_cantidad > 0): ?>
                <span style="font-size: 2.2rem; font-weight: 800; color: #9d4edd; text-transform: uppercase; letter-spacing: 2px;">
                    <?= strtoupper($emocion_predominante) ?>
                </span>
            <?php else: ?>
                <span style="color: #aaa;">Sin datos suficientes</span>
            <?php endif; ?>
        </div>
    </div>

    <h3 style="color: #5a189a; margin-bottom: 15px;">Usuarios Registrados</h3>
    
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Estado</th>
                    <th>Rol</th>
                    <th style="text-align: center;">Registros</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($u = $res_users->fetch_assoc()): ?>
                    <?php $is_online = ($u['ultima_conexion'] && (time() - strtotime($u['ultima_conexion']) < 300)); ?>
                    <tr>
                        <td>
                            <strong style="color: #333;"><?= htmlspecialchars($u['nombre']) ?></strong><br>
                            <span style="color:#888; font-size:12px;"><?= htmlspecialchars($u['email']) ?></span>
                        </td>
                        <td>
                            <?= $is_online ? '<span class="status-dot online"></span>' : '<span class="status-dot offline"></span>' ?>
                        </td>
                        <td>
                            <span class="badge <?= ($u['rol']=='admin')?'badge-admin':'badge-user' ?>">
                                <?= $u['rol'] ?>
                            </span>
                        </td>
                        <td style="font-weight: bold; text-align: center; color: #5a189a;">
                            <?= $u['num_entradas'] ?>
                        </td>
                        <td>
                            <a href="admin_usuario.php?id=<?= $u['id'] ?>" class="btn-action btn-ver">Ver Perfil</a>
                            
                            <?php if($u['id'] != $uid): ?>
                                <?php if($u['rol'] == 'user'): ?>
                                    <a href="../backend/cambiar_rol.php?id=<?= $u['id'] ?>&rol=admin" class="btn-action btn-ascender" onclick="return confirm('¿Dar permisos de Administrador?')">⬆️</a>
                                <?php else: ?>
                                    <a href="../backend/cambiar_rol.php?id=<?= $u['id'] ?>&rol=user" class="btn-action btn-ascender" onclick="return confirm('¿Quitar permisos de Administrador?')">⬇️</a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    const ctx = document.getElementById('emocionesChart').getContext('2d');
    const etiquetas = <?= json_encode($labels) ?>;
    const valores = <?= json_encode($data) ?>;
    const coloresLumina = ['#e0aaff', '#ffadad', '#a0c4ff', '#caffbf', '#fdffb6', '#ffc6ff'];

    if (etiquetas.length > 0) {
        new Chart(ctx, {
            type: 'doughnut',
            data: { labels: etiquetas, datasets: [{ data: valores, backgroundColor: coloresLumina, borderWidth: 0, hoverOffset: 10 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }, cutout: '70%' }
        });
    } else {
        ctx.font = "14px Arial"; ctx.fillStyle = "#888"; ctx.textAlign = "center"; ctx.fillText("No hay datos este mes", 140, 140);
    }
</script>
</body>
</html>

```
**admin_usuario.php**
```php
<?php
// --- CORRECCIÓN AQUÍ: Subimos un nivel (..) y entramos a config ---
require_once '../config/conexion.php';
session_start();

if (!isset($_SESSION['usuario_id'])) { header("Location: index.php"); exit(); }

// Verificar Admin
$mi_id = $_SESSION['usuario_id'];
$check = $conexion->query("SELECT rol FROM usuarios WHERE id = $mi_id");
if (!$check || $check->num_rows === 0 || $check->fetch_assoc()['rol'] !== 'admin') { 
    die("⛔ Acceso Denegado."); 
}

$uid_target = $_GET['id'] ?? null;
if (!$uid_target) { header("Location: admin.php"); exit(); }

// Obtener datos del usuario
$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $uid_target);
$stmt->execute();
$user_info = $stmt->get_result()->fetch_assoc();

// Obtener historial
$entradas = $conexion->query("SELECT * FROM entradas WHERE usuario_id=$uid_target ORDER BY fecha DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Perfil Usuario</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <style>
         .entry-item { background: #fff; border: 1px solid #eee; padding: 15px; margin-bottom: 10px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; }
         .entry-danger { border-left: 5px solid #d63031; }
         .entry-emoji { font-size: 24px; margin-right: 15px; }
    </style>
</head>
<body>
<div class="card wide fade-in">
    <?php include 'menu.php'; ?>
    
    <div style="background:#f4f4f9; padding:20px; border-radius:10px; margin-bottom:20px; text-align:center;">
        <h2 style="color:#9d4edd;"><?= htmlspecialchars($user_info['nombre']) ?></h2>
        <p><?= htmlspecialchars($user_info['email']) ?></p>
        <br>
        <a href="admin.php" style="color:#555; text-decoration:none;">⬅️ Volver al Panel</a>
    </div>

    <h3>Historial de Entradas</h3>
    <?php while($h = $entradas->fetch_assoc()): ?>
        <div class="entry-item entry-danger">
            <div style="display:flex; align-items:center;">
                <span class="entry-emoji">
                    <?php
                        $e = strtolower($h['emocion']);
                        if ($e == 'feliz') echo '😄'; elseif ($e == 'triste') echo '😔'; elseif ($e == 'enojado') echo '😡'; elseif ($e == 'ansioso') echo '😰'; elseif ($e == 'calmado') echo '😌'; else echo '😐';
                    ?>
                </span>
                <div>
                    <b><?= htmlspecialchars($h['nota']) ?></b> <br>
                    <small style="color:#888;"><?= date("d/m/Y H:i", strtotime($h['fecha'])) ?></small>
                </div>
            </div>
            <a href="../backend/borrar.php?id=<?= $h['id'] ?>" style="color:#d63031; text-decoration:none; font-weight:bold;" onclick="return confirm('¿Borrar esta entrada?')">🗑️ Eliminar</a>
        </div>
    <?php endwhile; ?>
</div>
</body>
</html>

```
**configuracion.php**
```php
<?php
// Configuración de sesión eterna
ini_set('session.gc_maxlifetime', 86400);
session_set_cookie_params(86400);

// Anti-caché
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once '../config/conexion.php';
session_start();

if (!isset($_SESSION['usuario_id'])) { header("Location: index.php"); exit(); }
$uid = $_SESSION['usuario_id'];

$stmt = $conexion->prepare("SELECT nombre, email, ultimo_cambio_nombre FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$puede_cambiar_nombre = true;
$tiempo_restante = "";

if ($user['ultimo_cambio_nombre']) {
    $ultimo_cambio = strtotime($user['ultimo_cambio_nombre']);
    $ahora = time();
    $diferencia = $ahora - $ultimo_cambio;
    
    if ($diferencia < 86400) {
        $puede_cambiar_nombre = false;
        $horas_restantes = ceil((86400 - $diferencia) / 3600);
        $tiempo_restante = "Debes esperar $horas_restantes horas para volver a cambiarlo.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración - Lumina</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <style>
        .alert { padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 0.9rem; text-align: center; }
        .alert-error { background: #ffdddd; color: #d63031; }
        .alert-success { background: #ddffdd; color: #2ecc71; }
        .disabled-input { background: #eee; color: #888; cursor: not-allowed; }
    </style>
</head>
<body>
<div class="card fade-in">
    <?php include 'menu.php'; ?>
    
    <h2 style="text-align:center; color:#9d4edd; margin-bottom:20px;">⚙️ Configuración</h2>

    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-error">
            <?php 
                if($_GET['error'] == 'existe') echo "¡Ese nombre de usuario ya está en uso!";
                if($_GET['error'] == 'tiempo') echo "Aún no han pasado 24 horas.";
                if($_GET['error'] == 'pass') echo "Las contraseñas no coinciden.";
                if($_GET['error'] == 'db') echo "Error del sistema.";
            ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['exito'])): ?>
        <div class="alert alert-success">¡Datos actualizados correctamente!</div>
    <?php endif; ?>

    <form action="../backend/actualizar_perfil.php" method="POST">
        
        <div class="config-section">
            <h4 style="color:#5a189a; margin-bottom:10px;">Perfil</h4>
            
            <label>Nombre de Usuario:</label>
            <?php if ($puede_cambiar_nombre): ?>
                <input type="text" name="nuevo_nombre" value="<?= htmlspecialchars($user['nombre']) ?>" required>
                <span class="info-text">El nombre debe ser único. Solo puedes cambiarlo una vez cada 24h.</span>
            <?php else: ?>
                <input type="text" value="<?= htmlspecialchars($user['nombre']) ?>" class="disabled-input" readonly>
                <span class="info-text" style="color:#d63031;">⏳ <?= $tiempo_restante ?></span>
            <?php endif; ?>
            
            <label>Correo (No editable):</label>
            <input type="email" value="<?= htmlspecialchars($user['email']) ?>" class="disabled-input" readonly>
        </div>

        <div class="config-section">
            <h4 style="color:#5a189a; margin-bottom:10px;">Seguridad</h4>
            <span class="info-text">Deja esto en blanco si no quieres cambiar tu contraseña.</span>
            
            <div class="password-wrapper">
                <input type="password" name="nueva_pass" id="newPass" placeholder="Nueva contraseña">
                <span class="toggle-password" onclick="togglePass('newPass')">👁️</span>
            </div>
            
            <div class="password-wrapper">
                <input type="password" name="confirmar_pass" id="confPass" placeholder="Confirmar nueva contraseña">
                <span class="toggle-password" onclick="togglePass('confPass')">👁️</span>
            </div>
        </div>

        <button type="submit">Guardar Cambios</button>
    </form>
</div>

<script>
    function togglePass(id) {
        var input = document.getElementById(id);
        if (input.type === "password") input.type = "text";
        else input.type = "password";
    }
</script>
</body>
</html>

```
**dashboard.php**
```php
<?php
// Configuración de sesión eterna (24 horas)
ini_set('session.gc_maxlifetime', 86400);
session_set_cookie_params(86400);

// Anti-caché
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once '../config/conexion.php';
session_start();

if (!isset($_SESSION["usuario_id"])) { header("Location: index.php"); exit(); }
$uid = $_SESSION["usuario_id"];
$nombre = $_SESSION["usuario_nombre"];

$mensaje = "";
$tipo_mensaje = "";

if (isset($_GET['exito'])) $tipo_mensaje = "exito";
if (isset($_GET['error'])) {
    $tipo_mensaje = "error";
    if ($_GET['error'] == 'falta_emocion') $mensaje = "⚠️ Selecciona al menos una emoción.";
    if ($_GET['error'] == 'db') $mensaje = "⚠️ Error de conexión. Intenta de nuevo.";
}

// Historial breve (Solo lectura)
$historial = [];
$stmt = $conexion->prepare("SELECT id, emocion, nota, fecha, intensidad FROM entradas WHERE usuario_id = ? ORDER BY fecha DESC LIMIT 3");
$stmt->bind_param("i", $uid);
$stmt->execute();
$res = $stmt->get_result();
while ($r = $res->fetch_assoc()) { $historial[] = $r; }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Lumina</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <style>
        .error { background:#ffdddd; color:#d8000c; padding:10px; border-radius:5px; text-align:center; margin-bottom:15px; }
        .exito { background:#ddffdd; color:#4F8A10; padding:10px; border-radius:5px; text-align:center; margin-bottom:15px; }
    </style>
</head>
<body>
<div class="card wide fade-in">
    <?php include 'menu.php'; ?>

    <div style="text-align:center; margin-bottom:20px;">
        <h2>Hola, <?= htmlspecialchars($nombre) ?> 👋</h2>
        <p class="sub">¿Cómo te sientes hoy?</p>
    </div>

    <div class="help-banner">
        <span class="help-icon">❤️‍🩹</span>
        <div>
            <strong>¿Necesitas apoyo?</strong><br>
            Si te sientes abrumado, recuerda que no estás solo. Llama al <strong>024</strong> (Ayuda conducta suicida) o busca apoyo profesional.
        </div>
    </div>

    <?php if ($tipo_mensaje == "exito"): ?><div class="exito">¡Entrada guardada con éxito!</div><?php endif; ?>
    <?php if ($mensaje): ?><div class="error"><?= $mensaje ?></div><?php endif; ?>

    <form action="../backend/guardar_entrada.php" method="POST">
        
        <div class="mood-selector">
            
            <label class="mood-option">
                <input type="checkbox" name="emocion[]" value="Feliz">
                <span class="mood-emoji">😄</span>
                <span class="mood-label">Feliz</span>
            </label>

            <label class="mood-option">
                <input type="checkbox" name="emocion[]" value="Calmado">
                <span class="mood-emoji">😌</span>
                <span class="mood-label">Calmado</span>
            </label>

            <label class="mood-option">
                <input type="checkbox" name="emocion[]" value="Triste">
                <span class="mood-emoji">😔</span>
                <span class="mood-label">Triste</span>
            </label>

            <label class="mood-option">
                <input type="checkbox" name="emocion[]" value="Ansioso">
                <span class="mood-emoji">😰</span>
                <span class="mood-label">Ansioso</span>
            </label>

            <label class="mood-option">
                <input type="checkbox" name="emocion[]" value="Enojado">
                <span class="mood-emoji">😡</span>
                <span class="mood-label">Enojado</span>
            </label>

        </div>

        <div class="range-container">
            <div class="range-label">
                <span>Intensidad (General)</span>
                <span id="valorIntensidad">5/10</span>
            </div>
            <input type="range" name="intensidad" min="1" max="10" value="5" oninput="document.getElementById('valorIntensidad').innerText = this.value + '/10'">
        </div>

        <input type="text" name="nota" placeholder="Añade una nota breve para estas emociones..." autocomplete="off">
        <button type="submit">Guardar Registro</button>
    </form>

    <hr style="margin:20px 0; border:0; border-top:1px solid #eee;">
    <h3>Últimos registros</h3>
    
    <?php foreach ($historial as $h): ?>
        <div class="entry-item">
            <span class="entry-intensity">Nivel: <?= $h['intensidad'] ?? 5 ?></span>
            <div style="display:flex; align-items:center; gap: 10px;">
                <span style="font-size: 1.8rem;">
                    <?php 
                        $e = strtolower($h['emocion']);
                        if ($e == 'feliz') echo '😄'; 
                        elseif ($e == 'triste') echo '😔'; 
                        elseif ($e == 'enojado') echo '😡'; 
                        elseif ($e == 'ansioso') echo '😰'; 
                        elseif ($e == 'calmado') echo '😌'; 
                        else echo '😐';
                    ?>
                </span>
                
                <div>
                    <small style="color:#888;"><?= date("d/m H:i", strtotime($h['fecha'])) ?></small><br>
                    <strong style="color:#5a189a;"><?= ucfirst($h['emocion']) ?></strong>
                    <?php if(!empty($h['nota'])): ?>
                        <span style="color:#555;">: <?= htmlspecialchars($h['nota']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    
    <div style="text-align:center; margin-top:15px;">
        <a href="historial.php" style="color:#9d4edd; font-weight:bold; text-decoration:none;">Ver todo el historial →</a>
    </div>
</div>
</body>
</html>

```
**editar_vista.php**
```php
<?php
// Configuración de sesión y caché
ini_set('session.gc_maxlifetime', 86400);
session_set_cookie_params(86400);
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once '../config/conexion.php';
session_start();

if (!isset($_SESSION["usuario_id"])) { header("Location: index.php"); exit(); }
$uid = $_SESSION["usuario_id"];

// Verificar ID
if (!isset($_GET['id'])) { header("Location: historial.php"); exit(); }
$id_entrada = $_GET['id'];

// Obtener datos actuales de la entrada
$stmt = $conexion->prepare("SELECT * FROM entradas WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $id_entrada, $uid);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) { header("Location: historial.php"); exit(); }
$entrada = $res->fetch_assoc();

// La emoción actual la guardamos para marcarla en el checkbox
$emocion_actual = $entrada['emocion']; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Entrada - Lumina</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
<div class="card wide fade-in">
    <?php include 'menu.php'; ?>

    <div style="text-align:center; margin-bottom:20px;">
        <h2 style="color:#9d4edd;">✏️ Editar Registro</h2>
        <p class="sub">Puedes cambiar las emociones o la nota.</p>
        <p style="font-size:0.8rem; color:#888;">Fecha original: <?= date("d/m/Y H:i", strtotime($entrada['fecha'])) ?></p>
    </div>

    <form action="../backend/actualizar_entrada.php" method="POST">
        <input type="hidden" name="id_entrada" value="<?= $entrada['id'] ?>">

        <div class="mood-selector">
            
            <label class="mood-option">
                <input type="checkbox" name="emocion[]" value="Feliz" <?= ($emocion_actual == 'Feliz') ? 'checked' : '' ?>>
                <span class="mood-emoji">😄</span>
                <span class="mood-label">Feliz</span>
            </label>

            <label class="mood-option">
                <input type="checkbox" name="emocion[]" value="Calmado" <?= ($emocion_actual == 'Calmado') ? 'checked' : '' ?>>
                <span class="mood-emoji">😌</span>
                <span class="mood-label">Calmado</span>
            </label>

            <label class="mood-option">
                <input type="checkbox" name="emocion[]" value="Triste" <?= ($emocion_actual == 'Triste') ? 'checked' : '' ?>>
                <span class="mood-emoji">😔</span>
                <span class="mood-label">Triste</span>
            </label>

            <label class="mood-option">
                <input type="checkbox" name="emocion[]" value="Ansioso" <?= ($emocion_actual == 'Ansioso') ? 'checked' : '' ?>>
                <span class="mood-emoji">😰</span>
                <span class="mood-label">Ansioso</span>
            </label>

            <label class="mood-option">
                <input type="checkbox" name="emocion[]" value="Enojado" <?= ($emocion_actual == 'Enojado') ? 'checked' : '' ?>>
                <span class="mood-emoji">😡</span>
                <span class="mood-label">Enojado</span>
            </label>

        </div>

        <div class="range-container">
            <div class="range-label">
                <span>Intensidad</span>
                <span id="valorIntensidad"><?= $entrada['intensidad'] ?? 5 ?>/10</span>
            </div>
            <input type="range" name="intensidad" min="1" max="10" value="<?= $entrada['intensidad'] ?? 5 ?>" oninput="document.getElementById('valorIntensidad').innerText = this.value + '/10'">
        </div>

        <input type="text" name="nota" value="<?= htmlspecialchars($entrada['nota']) ?>" placeholder="Nota..." autocomplete="off">
        
        <div style="display:flex; gap:10px; margin-top:15px;">
            <a href="historial.php" style="flex:1; padding:14px; text-align:center; background:#eee; color:#555; border-radius:12px; text-decoration:none; font-weight:bold;">Cancelar</a>
            <button type="submit" style="flex:2; margin-top:0;">Guardar Cambios</button>
        </div>
    </form>
</div>
</body>
</html>

```
**estadisticas.php**
```php
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

```
**historial.php**
```php
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

```
**index.php**
```php
<?php
// Configuración de sesión eterna (24 horas)
ini_set('session.gc_maxlifetime', 86400);
session_set_cookie_params(86400);

session_start();
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Lumina</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
    <div class="login-container fade-in">
        <div class="logo-area">
            <img src="../assets/img/luminalogo.png" alt="Logo Lumina" class="logo-circular">
            <h1>Lumina</h1>
            <p>Support with Purpose</p>
        </div>

        <?php if(isset($_GET['error'])): ?>
            <p style="color:red; margin-bottom:10px; font-size: 0.9rem;">
              <?php 
            if($_GET['error'] == 'credenciales') echo "Usuario o contraseña incorrectos.";
            if($_GET['error'] == 'vacios') echo "Por favor, rellena todos los campos.";
              ?>
            </p>
       
    
        <?php endif; ?>

        <form action="../backend/login.php" method="POST">
            <input type="text" name="credencial" placeholder="Correo o Usuario" required>
            
            <div class="password-wrapper">
                <input type="password" name="password" id="passInput" placeholder="Contraseña" required>
                <span class="toggle-password" onclick="togglePass('passInput')">👁️</span>
            </div>

            <button type="submit">Entrar</button>
        </form>
        
        <p class="pie">
            ¿No tienes cuenta? <a href="registro_vista.php">Regístrate aquí</a><br>
            <a href="olvide_password.php" style="font-size: 0.8em; color: #999;">Recuperar contraseña</a>
        </p>
    </div>

    <script>
        function togglePass(id) {
            var input = document.getElementById(id);
            if (input.type === "password") input.type = "text";
            else input.type = "password";
        }
    </script>
</body>
</html>

```
**menu.php**
```php
<?php
// 1. Manejo seguro de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. EL "LATIDO" 💓 (Esto soluciona tu problema)
// Si hay un usuario logueado y hay conexión, actualizamos su hora
if (isset($_SESSION['usuario_id']) && isset($conexion)) {
    $uid_latido = $_SESSION['usuario_id'];
    // Actualiza la hora actual cada vez que carga el menú
    $conexion->query("UPDATE usuarios SET ultima_conexion = NOW() WHERE id = $uid_latido");
}

// 3. Verificar rol para mostrar botón Admin
$es_admin = false;
if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
    $es_admin = true;
}
?>

<nav class="app-menu">
    <a href="dashboard.php" class="menu-item">🏠 Inicio</a>
    <a href="historial.php" class="menu-item">📅 Historial</a>
    <a href="estadisticas.php" class="menu-item">📊 Estadísticas</a>
    
    <a href="configuracion.php" class="menu-item">⚙️ Configuración</a>
    
    <?php if ($es_admin): ?>
        <a href="admin.php" class="menu-item" style="color: #d63031;">🛡️ Admin</a>
    <?php endif; ?>

    <a href="../backend/logout.php" class="menu-item salir">❌ Salir</a>
</nav>

```
**olvide_password.php**
```php
<?php
// Corrección de ruta para evitar Error 500
require_once '../config/conexion.php';
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Lumina</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
    <div class="login-container fade-in">
        <div class="logo-area">
            <img src="../assets/img/luminalogo.png" alt="Logo Lumina" class="logo-circular" style="width:120px; height:120px;">
            <h3>Recuperar Acceso</h3>
            <p>Introduce tu correo para buscar tu cuenta</p>
        </div>

        <?php if(isset($_GET['error']) && $_GET['error'] == 'noexiste'): ?>
            <div style="background:#ffdddd; color:#d63031; padding:10px; border-radius:8px; margin-bottom:15px; font-size:0.9rem;">
                No encontramos ese correo electrónico.
            </div>
        <?php endif; ?>

        <form action="restablecer.php" method="POST">
            <input type="email" name="email_recuperacion" placeholder="Tu correo electrónico" required>
            <button type="submit">Buscar Cuenta</button>
        </form>
        
        <p class="pie">
            <a href="index.php">Volver al inicio de sesión</a>
        </p>
    </div>
</body>
</html>

```
**registro_vista.php**
```php
<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Recuperar los datos si existen, si no, dejarlos vacíos
$nombre_temp = $_SESSION['datos_registro']['nombre'] ?? '';
$email_temp = $_SESSION['datos_registro']['email'] ?? '';

// Borrar los datos de la sesión para que no aparezcan si el usuario refresca manualmente
unset($_SESSION['datos_registro']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Lumina</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
    <div class="login-container fade-in">
        <div class="logo-area">
            <img src="../assets/img/luminalogo.png" alt="Logo Lumina" class="logo-circular" style="width:100px; height:100px;">
            <h2>Únete a Lumina</h2>
            <p>Comienza tu viaje emocional hoy</p>
        </div>

        <?php if(isset($_GET['error'])): ?>
            <div style="background:#ffdddd; color:#d63031; padding:10px; border-radius:8px; margin-bottom:15px; font-size:0.9rem;">
                <?php 
                    if($_GET['error'] == 'existe') echo "El correo o usuario ya están registrados.";
                    if($_GET['error'] == 'db') echo "Error del sistema. Intenta más tarde.";
                    if($_GET['error'] == 'vacios') echo "Por favor llena todos los campos.";
                    // --- ESTA LÍNEA ES NUEVA ---
                    if($_GET['error'] == 'password_debil') echo "La contraseña debe tener al menos 8 caracteres, incluir letras, números y un símbolo.";
                ?>
            </div>
        <?php endif; ?>

        <form action="../backend/registro.php" method="POST">
            <input type="text" name="nombre" placeholder="Nombre de Usuario" value="<?php echo htmlspecialchars($nombre_temp); ?>" required>
            <input type="email" name="email" placeholder="Correo electrónico" value="<?php echo htmlspecialchars($email_temp); ?>" required>
            <input type="password" name="password" placeholder="Contraseña segura" title="Mínimo 8 caracteres, incluyendo letras, números y símbolos (!@#$...)" required>
            
            <button type="submit">Registrarme</button>
        </form>
        
        <p class="pie">
            ¿Ya tienes cuenta? <a href="index.php">Inicia Sesión</a>
        </p>
    </div>
</body>
</html>

```
**restablecer.php**
```php
<?php
require_once '../config/conexion.php';
$paso = 1; // Paso 1: Verificar correo | Paso 2: Cambiar contraseña
$email = "";
$error = "";
$mensaje = "";

// 1. SI VENIMOS DE OLVIDE_PASSWORD (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email_recuperacion'])) {
    $email = $_POST['email_recuperacion'];
    
    // Verificar si existe el email
    $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $paso = 2; // Email encontrado, mostrar formulario de cambio
    } else {
        header("Location: olvide_password.php?error=noexiste");
        exit();
    }
}

// 2. SI VENIMOS DE CAMBIAR LA CONTRASEÑA (POST FINAL)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nueva_pass'])) {
    $email_final = $_POST['email_hidden'];
    $pass = $_POST['nueva_pass'];
    
    // Encriptar y guardar
    $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $conexion->prepare("UPDATE usuarios SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $pass_hash, $email_final);
    
    if($stmt->execute()) {
        header("Location: index.php?registro=exito"); // Reusamos el mensaje de éxito
        exit();
    } else {
        $error = "Hubo un error al actualizar.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer - Lumina</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
    <div class="login-container fade-in">
        <div class="logo-area">
            <img src="../assets/img/luminalogo.png" alt="Logo Lumina" class="logo-circular" style="width:100px; height:100px;">
            <h2>Nueva Contraseña</h2>
        </div>

        <?php if($error): ?>
            <p style="color:red; text-align:center;"><?= $error ?></p>
        <?php endif; ?>

        <?php if ($paso == 2): ?>
            <p style="text-align:center; color:#5a189a; margin-bottom:15px;">
                Hola, hemos encontrado tu cuenta: <br><strong><?= htmlspecialchars($email) ?></strong>
            </p>

            <form action="restablecer.php" method="POST">
                <input type="hidden" name="email_hidden" value="<?= htmlspecialchars($email) ?>">
                <input type="password" name="nueva_pass" placeholder="Escribe tu nueva contraseña" required>
                <button type="submit">Guardar Nueva Contraseña</button>
            </form>
        <?php else: ?>
            <p style="text-align:center;">Acceso inválido.</p>
            <a href="index.php" style="display:block; text-align:center; margin-top:15px;">Volver</a>
        <?php endif; ?>
    </div>
</body>
</html>

```