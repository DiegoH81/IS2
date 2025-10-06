<?php
// UI-02 Registrar Usuario
// CU-002 Registro de usuario en familia existente
session_start();
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../clases/GestionarUsuario.php";
require_once __DIR__ . "/../clases/GestionarFamilia.php";

$gestionarUsuario = new GestionarUsuario($conn);
$gestionarFamilia = new GestionarFamilia($conn);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';
    $confirmarContrasena = $_POST['confirmar_contrasena'] ?? '';
    $correo = trim($_POST['correo'] ?? '');
    $rol = $_POST['rol'] ?? '';
    $contrasenaFamiliar = $_POST['contrasena_familiar'] ?? '';
    $familiaId = intval($_POST['familia_id'] ?? 0);

    // Validar campos del usuario
    $errores = $gestionarUsuario->validarDatos($usuario, $nombre, $contrasena, $confirmarContrasena, $correo, $rol);

    // Validar contraseña familiar
    if (empty($contrasenaFamiliar)) {
        $errores[] = 'La contraseña familiar es obligatoria.';
    } elseif ($familiaId > 0) {
        $validacionFamiliar = $gestionarFamilia->validarContrasenaFamiliar($familiaId, $contrasenaFamiliar);
        if (!$validacionFamiliar['valido']) {
            $errores[] = $validacionFamiliar['mensaje'];
        }
    } else {
        $errores[] = 'Debe seleccionar una familia válida.';
    }

    if (empty($errores)) {
        // Crear usuario
        $resultado = $gestionarUsuario->crearUsuario($usuario, $nombre, $contrasena, $correo, $rol, $familiaId);
        
        if ($resultado['success']) {
            $success = $resultado['mensaje'] . ' Puede iniciar sesión ahora.';
            // Limpiar campos
            $_POST = [];
        } else {
            $error = $resultado['mensaje'];
        }
    } else {
        $error = implode('<br>', $errores);
    }
}

// Obtener lista de familias para el selector (en producción esto debería ser más seguro)
try {
    $stmtFamilias = $conn->prepare("SELECT id, nombre FROM familia ORDER BY nombre");
    $stmtFamilias->execute();
    $familias = $stmtFamilias->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $familias = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Usuario - On a budget</title>
    <link rel="stylesheet" href="css/registro.css">
</head>
<body>
    <div class="contenedor-registro">
        <div class="caja-registro">
            <div class="header-registro">
                <h1 class="titulo-app">On a budget</h1>
                <h2 class="titulo-form">Registrar Usuario</h2>
            </div>

            <form method="POST" action="registrar_usuario.php" id="formRegistro" class="form-registro">
                
                <?php if (!empty($error)): ?>
                    <div class="mensaje-error">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="mensaje-exito">
                        <?php echo $success; ?>
                        <a href="login.php" class="link-login">Ir a iniciar sesión</a>
                    </div>
                <?php endif; ?>

                <div class="grupo-campo">
                    <label for="usuario">Usuario *</label>
                    <input 
                        type="text" 
                        id="usuario" 
                        name="usuario" 
                        placeholder="Nombre de usuario (sin espacios)"
                        value="<?php echo htmlspecialchars($_POST['usuario'] ?? ''); ?>"
                        required
                    >
                    <small>Entre 3 y 50 caracteres, solo letras, números y guiones bajos</small>
                </div>

                <div class="grupo-campo">
                    <label for="nombre">Nombre completo *</label>
                    <input 
                        type="text" 
                        id="nombre" 
                        name="nombre" 
                        placeholder="Nombre y apellido"
                        value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>"
                        required
                    >
                </div>

                <div class="grupo-campo">
                    <label for="correo">Correo electrónico *</label>
                    <input 
                        type="email" 
                        id="correo" 
                        name="correo" 
                        placeholder="ejemplo@correo.com"
                        value="<?php echo htmlspecialchars($_POST['correo'] ?? ''); ?>"
                        required
                    >
                </div>

                <div class="grupo-campo">
                    <label for="contrasena">Contraseña *</label>
                    <input 
                        type