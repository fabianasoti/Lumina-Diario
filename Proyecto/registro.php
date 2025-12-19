<?php
/****************************
* CONFIGURACIÓN MYSQL
****************************/
$host = "localhost";
$user = "diarioemocional";
$pass = "Diarioemocional123$";
$db   = "diarioemocional";

$conexion = new mysqli($host, $user, $pass, $db);
if ($conexion->connect_error) {
    die("Error de conexión");
}

/****************************
* LÓGICA DE REGISTRO
****************************/
$errores = [];
$exito = false;

// Inicializamos variables para el Sticky Form
$nombre   = "";
$apellido = "";
$username = "";
$email    = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recogida de datos
    $nombre   = trim($_POST["nombre"]);
    $apellido = trim($_POST["apellido"]);
    $username = trim($_POST["username"]);
    $email    = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"];

    // 1. VALIDACIONES DE PHP
    if (empty($nombre) || empty($apellido) || empty($username) || empty($email) || empty($password)) {
        $errores[] = "Todos los campos son obligatorios.";
    }

    // Validación USERNAME (Longitud 5-20 y caracteres permitidos)
    if (!preg_match('/^[a-zA-Z0-9._]{5,20}$/', $username)) {
        $errores[] = "El username debe tener entre 5 y 20 caracteres (solo letras, números, '.' o '_').";
    }

    // Validación EMAIL
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El formato del email no es válido.";
    }

    // Validación PASSWORD (Longitud 8-16, Mayúscula y Número)
    $longitudPass = strlen($password);
    if ($longitudPass < 8 || $longitudPass > 16) {
        $errores[] = "La contraseña debe tener entre 8 y 16 caracteres.";
    } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errores[] = "La contraseña debe incluir al menos una mayúscula y un número.";
    }

    // 2. VERIFICAR SI USERNAME O EMAIL YA EXISTEN
    if (empty($errores)) {
        $checkUser = $conexion->prepare("SELECT id FROM usuarios WHERE email = ? OR username = ?");
        $checkUser->bind_param("ss", $email, $username);
        $checkUser->execute();
        $checkUser->store_result();
        
        if ($checkUser->num_rows > 0) {
            $errores[] = "El email o el username ya están en uso.";
        }
        $checkUser->close();
    }

    // 3. INSERTAR SI NO HAY ERRORES
    if (empty($errores)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, apellido, username, email, password) VALUES (?, ?, ?, ?, ?)");
        // sssss -> string, string, string, integer, string
        $stmt->bind_param("sssss", $nombre, $apellido, $username, $email, $passwordHash);

        if ($stmt->execute()) {
            $exito = true;
        } else {
            $errores[] = "Error técnico al registrar. Inténtalo de nuevo.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro Emocional</title>
    <style>
        *{ box-sizing:border-box; font-family:'Segoe UI',sans-serif; }
        body{ margin:0; min-height:100vh; display:flex; justify-content:center; align-items:center; background:linear-gradient(135deg,#cdb4db,#bde0fe); }
        .login-container{ background:#fff; width:100%; max-width:450px; padding:32px; border-radius:20px; box-shadow:0 18px 35px rgba(0,0,0,.15); }
        h2{ text-align:center; color:#4a4e69; margin-bottom: 20px; }
        label{ font-size:13px; color:#4a4e69; font-weight: bold; }
        input{ width:100%; padding:10px; border-radius:10px; border:1px solid #cdb4db; margin-top:5px; margin-bottom:15px; }
        button{ width:100%; padding:12px; border:none; border-radius:14px; background:linear-gradient(135deg,#9d4edd,#5fa8d3); color:#fff; font-size:16px; font-weight:bold; cursor:pointer; }
        .errores{ background:#fde2e4; border-left:5px solid #ff6b6b; padding:10px; border-radius:8px; margin-bottom:15px; font-size:14px; color: #721c24; }
        .exito{ background:#d4edda; border-left:5px solid #28a745; padding:10px; border-radius:8px; margin-bottom:15px; font-size:14px; color: #155724; }
        .link{ display: block; text-align: center; margin-top: 15px; font-size: 13px; color: #9d4edd; text-decoration: none; }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Crear Cuenta</h2>

    <?php if (!empty($errores)): ?>
        <div class="errores">
            <?php foreach ($errores as $e): echo "<p style='margin:0'>• $e</p>"; endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($exito): ?>
        <div class="exito">¡Registro completado! <a href="login.php">Inicia sesión</a>.</div>
    <?php else: ?>

        <form method="POST">
            <div style="display: flex; gap: 10px;">
                <div style="flex:1">
                    <label>Nombre</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($nombre) ?>" required>
                </div>
                <div style="flex:1">
                    <label>Apellidos</label>
                    <input type="text" name="apellido" value="<?= htmlspecialchars($apellido) ?>" required>
                </div>
            </div>

            <label>Usuario</label>
            <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" placeholder="mín. 5 caracteres" required>

            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            
            <label>Contraseña</label>
            <input type="password" name="password" placeholder="8 a 16 caracteres" required>
            
            <button type="submit">Registrarme</button>
        </form>
    <?php endif; ?>

    <a href="index.php" class="link">¿Ya tienes cuenta? Inicia sesión</a>
</div>

</body>
</html>
