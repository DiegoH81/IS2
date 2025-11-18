<?php
session_start();
require_once '../gtr/GTR-04_Validar.php';

$error = '';

// ------------------------------------------------------------
// UI-03: Registrar familia
// Caso de uso asociado: FALTA
// ------------------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $nombre = trim($_POST['nombre']);
    $contrasena = trim($_POST['password']);
    $confirmar = trim($_POST['confirm_password']);
    $familiaPassword = trim($_POST['family_password']);

    // Validar que las contraseñas coincidan
    if ($contrasena !== $confirmar) {
        $error = "Las contraseñas no coinciden";
    } else {
        // Aquí iría la lógica de registro
        // Por ejemplo: Validar::registrarUsuario($usuario, $nombre, $contrasena, $familiaPassword);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar</title>

    <!-- CSS principal -->
    <link rel="stylesheet" href="../css/principal.css">
    <link rel="stylesheet" href="../css/register.css"> <!-- NUEVO ARCHIVO -->
    <link rel="stylesheet" href="../css/icons.css">
</head>
<body>

<div class="contenedor-principal" style="height: 100%">
    <!-- Cabecera -->
    <header class="barra-superior" style="background-color: #3862AA;">
        <section class="seccion-izquierda">
            <h1 class="titulo-app">On a budget</h1>
        </section>
    </header>
    
    <div class="contenedor-form">
        <section class="contenedor-tablas-reg">
            <article class="tabla">
                <form class="form-crear-concepto" action="" method="POST">
                    <h2>Registrar Familia</h2>

                    <?php if ($error !== ''): ?>
                        <p class="mensaje-error"><?= htmlspecialchars($error) ?></p>
                    <?php endif; ?>

                    <div class="campo-formulario">
                        <label for="apellido">Apellido de la familia</label>
                        <input type="text" id="apellido" name="apellido" placeholder="Ingrese apellido familiar" required>
                    </div>
                    
                    <div class="campo-formulario">
                        <label for="family_password">Contraseña Familiar</label>
                        <div style="display: flex; align-items: center;">
                            <input type="text" id="family_password" name="family_password" placeholder="Contraseña generada" required readonly style="flex: 1; margin-right: 10px;">
                            <a href="javascript:void(0);" class="boton-cancelar" onclick="generarContrasena()" style="width: auto; padding: 8px 20px;">Generar</a>
                        </div>
                    </div>

                    <div class="botones-formulario">
                        <a href="UI-02_RegistrarUsuario.php" class="boton-cancelar">Cancelar</a>
                        <button type="submit" class="boton-registrar">Registrar</button>
                    </div>
                </form>
            </article>            
        </section>
    </div>
</div>

<script>
    function generarContrasena() {
        const caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let contrasenaGenerada = '';
        
        // Generar una contraseña aleatoria de 10 caracteres
        for (let i = 0; i < 10; i++) {
            const randomIndex = Math.floor(Math.random() * caracteres.length);
            contrasenaGenerada += caracteres[randomIndex];
        }
        
        // Asignar la contraseña generada al campo de "Contraseña Familiar"
        const campoContrasena = document.getElementById('family_password');
        campoContrasena.value = contrasenaGenerada; // Muestra la contraseña generada en el campo
    }
</script>

</body>
</html>