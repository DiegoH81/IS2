<?php
// UI-01 Inicio de sesión
// CU-001 Validar ingreso de usuario
session_start();
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../clases/GestionarUsuario.php";

$gestionarUsuario = new GestionarUsuario($conn);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';

    // Validar campos no vacíos
    if (empty($usuario) || empty($contrasena)) {
        $error = 'Por favor, complete todos los campos.';
    } else {
        // Validar credenciales
        $resultado = $gestionarUsuario->validarIngreso($usuario, $contrasena);
        
        if ($resultado['valido']) {
            // Login exitoso
            $_SESSION['usuario_id'] = $resultado['usuario']['id'];
            $_SESSION['usuario_nombre'] = $resultado['usuario']['nombre'];
            $_SESSION['usuario_nickname'] = $resultado['usuario']['nickname'];
            $_SESSION['usuario_rol'] = $resultado['usuario']['rol'];
            $_SESSION['familia_id'] = $resultado['usuario']['familia_id'];
            $_SESSION['usuario_foto'] = $resultado['usuario']['foto'];
            
            // Redirigir al registro diario
            header("Location: daily_input.php");
            exit;
        } else {
            $error = implode('<br>', $resultado['errores']);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - On a budget</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="contenedor-login">
        <div class="caja-login">
            <div class="logo-section">
                <h1 class="titulo-app">On a budget</h1>
                <p class="subtitulo-login">Gestiona tus finanzas familiares</p>
            </div>

            <form method="POST" action="login.php" id="formLogin" class="form-login">
                <h2 class="titulo-form">Iniciar Sesión</h2>
                
                <?php if (!empty($error)): ?>
                    <div class="mensaje-error">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <div class="grupo-campo">
                    <label for="usuario">Usuario</label>
                    <input 
                        type="text" 
                        id="usuario" 
                        name="usuario" 
                        placeholder="Ingrese su usuario"
                        value="<?php echo htmlspecialchars($_POST['usuario'] ?? ''); ?>"
                        required
                    >
                </div>

                <div class="grupo-campo">
                    <label for="contrasena">Contraseña</label>
                    <input 
                        type="password" 
                        id="contrasena" 
                        name="contrasena" 
                        placeholder="Ingrese su contraseña"
                        required
                    >
                </div>

                <button type="submit" class="boton-ingresar">Ingresar</button>

                <div class="enlaces-adicionales">
                    <a href="registrar_usuario.php" class="link-registro">¿No tienes cuenta? Regístrate</a>
                    <a href="registrar_familia.php" class="link-registro">Crear nueva familia</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Validación en el cliente
        document.getElementById('formLogin').addEventListener('submit', function(e) {
            const usuario = document.getElementById('usuario').value.trim();
            const contrasena = document.getElementById('contrasena').value;

            if (usuario === '' || contrasena === '') {
                e.preventDefault();
                alert('Por favor, complete todos los campos.');
                return false;
            }

            if (usuario.length < 3) {
                e.preventDefault();
                alert('El usuario debe tener al menos 3 caracteres.');
                return false;
            }
        });
    </script>
</body>
</html>
?>