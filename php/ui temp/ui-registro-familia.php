<?php
// UI-03 Registrar Familia
// CU-003 Registro de familia
session_start();
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../clases/GestionarFamilia.php";

$gestionarFamilia = new GestionarFamilia($conn);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreFamilia = trim($_POST['nombre_familia'] ?? '');
    $contrasenaFamiliar = $_POST['contrasena_familiar'] ?? '';

    // Validar campos
    $errores = $gestionarFamilia->validarCampos($nombreFamilia, $contrasenaFamiliar);

    if (empty($errores)) {
        // Crear familia
        $resultado = $gestionarFamilia->crearGrupoFamiliar($nombreFamilia, $contrasenaFamiliar);
        
        if ($resultado['success']) {
            $success = $resultado['mensaje'] . ' Ahora puede registrar usuarios en esta familia.';
            $_SESSION['familia_id_creada'] = $resultado['id'];
            // Limpiar campos
            $_POST = [];
        } else {
            $error = $resultado['mensaje'];
        }
    } else {
        $error = implode('<br>', $errores);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Familia - On a budget</title>
    <link rel="stylesheet" href="css/registro.css">
</head>
<body>
    <div class="contenedor-registro">
        <div class="caja-registro">
            <div class="header-registro">
                <h1 class="titulo-app">On a budget</h1>
                <h2 class="titulo-form">Registrar Familia</h2>
                <p class="descripcion">Crea un nuevo grupo familiar para gestionar las finanzas en conjunto</p>
            </div>

            <form method="POST" action="registrar_familia.php" id="formRegistroFamilia" class="form-registro">
                
                <?php if (!empty($error)): ?>
                    <div class="mensaje-error">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="mensaje-exito">
                        <?php echo $success; ?>
                        <div class="botones-success">
                            <a href="registrar_usuario.php" class="boton-secundario">Registrar primer usuario</a>
                            <a href="login.php" class="boton-secundario">Ir a iniciar sesión</a>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="grupo-campo">
                    <label for="nombre_familia">Nombre de la Familia *</label>
                    <input 
                        type="text" 
                        id="nombre_familia" 
                        name="nombre_familia" 
                        placeholder="Ej: Familia Grillo"
                        value="<?php echo htmlspecialchars($_POST['nombre_familia'] ?? ''); ?>"
                        maxlength="100"
                        required
                    >
                    <small>Este será el nombre que identifique a su grupo familiar</small>
                </div>

                <div class="grupo-campo">
                    <label for="contrasena_familiar">Contraseña Familiar *</label>
                    <input 
                        type="password" 
                        id="contrasena_familiar" 
                        name="contrasena_familiar" 
                        placeholder="Mínimo 6 caracteres"
                        required
                    >
                    <small>Esta contraseña será requerida para que otros miembros se unan a la familia</small>
                </div>

                <div class="advertencia-info">
                    <strong>Importante:</strong> Guarde esta contraseña en un lugar seguro. 
                    Todos los miembros de la familia necesitarán esta contraseña para registrarse.
                </div>

                <button type="submit" class="boton-registrar">Crear Familia</button>

                <div class="enlaces-adicionales">
                    <a href="login.php" class="link-volver">Volver a iniciar sesión</a>
                    <a href="registrar_usuario.php" class="link-volver">Ya tengo una familia</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Validación en el cliente
        document.getElementById('formRegistroFamilia').addEventListener('submit', function(e) {
            const nombreFamilia = document.getElementById('nombre_familia').value.trim();
            const contrasenaFamiliar = document.getElementById('contrasena_familiar').value;

            if (!nombreFamilia || !contrasenaFamiliar) {
                e.preventDefault();
                alert('Por favor, complete todos los campos obligatorios.');
                return false;
            }

            if (nombreFamilia.length > 100) {
                e.preventDefault();
                alert('El nombre de la familia no debe exceder 100 caracteres.');
                return false;
            }

            if (contrasenaFamiliar.length < 6) {
                e.preventDefault();
                alert('La contraseña familiar debe tener al menos 6 caracteres.');
                return false;
            }

            // Confirmación antes de crear
            if (!confirm('¿Está seguro de crear la familia "' + nombreFamilia + '"?')) {
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>
?>