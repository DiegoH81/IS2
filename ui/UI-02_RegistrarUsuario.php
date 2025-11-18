<?php
session_start();
require_once '../gtr/GTR-04_Validar.php';

$error = '';

// ------------------------------------------------------------
// UI-02: Registro de usuario
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

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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
                    <h2>Registrar</h2>

                    <?php if ($error !== ''): ?>
                        <p class="mensaje-error"><?= htmlspecialchars($error) ?></p>
                    <?php endif; ?>

                    <div class="campo-formulario">
                        <label for="usuario">Usuario</label>
                        <input type="text" id="usuario" name="usuario" placeholder="Ingrese nombre de usuario" required>
                    </div>
                    
                    <div class="campo-formulario">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Ingrese nombre" required>
                    </div>

                    <div class="campo-formulario">
                        <label for="password">Contraseña</label>
                        <div class="campo-password">
                            <input type="password" id="password" name="password" placeholder="Ingrese contraseña" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('password')">
                                <i class="fa-solid fa-eye" id="eye-icon-password"></i> <!-- Icono de ojo por defecto -->
                                <i class="fa-solid fa-eye-slash" id="eye-slash-icon-password" style="display: none;"></i> <!-- Icono de ojo tachado oculto -->
                            </button>
                        </div>
                    </div>

                    <div class="campo-formulario">
                        <label for="confirm_password">Confirmar Contraseña</label>
                        <div class="campo-password">
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Ingrese confirmación de contraseña" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                                <i class="fa-solid fa-eye" id="eye-icon-confirm_password"></i> <!-- Icono de ojo por defecto -->
                                <i class="fa-solid fa-eye-slash" id="eye-slash-icon-confirm_password" style="display: none;"></i> <!-- Icono de ojo tachado oculto -->
                            </button>
                        </div>
                    </div>

                    <div class="campo-formulario">
                        <label for="family_password">Contraseña Familiar</label>
                        <div class="campo-password">
                            <input type="password" id="family_password" name="family_password" placeholder="Ingrese contraseña familiar" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('family_password')">
                                <i class="fa-solid fa-eye" id="eye-icon-family_password"></i> <!-- Icono de ojo por defecto -->
                                <i class="fa-solid fa-eye-slash" id="eye-slash-icon-family_password" style="display: none;"></i> <!-- Icono de ojo tachado oculto -->
                            </button>
                        </div>
                    </div>

                    <div class="botones-formulario" style="display: flex; align-items: center; justify-content: center; gap: 12px;">
                        <a href="UI-01_InicioDeSesion.php" class="boton-cancelar">Cancelar</a>
                        <button type="submit" class="boton-registrar" style = "height: 45px">Registrar</button>
                        <a href="UI-03_RegistrarFamilia.php" class="boton-registrar-familia">Registrar Familia</a>
                    </div>
                </form>
            </article>            
        </section>
    </div>
</div>

<script>
    // Función para mostrar/ocultar contraseña
    function togglePassword(fieldId) {
        var input = document.getElementById(fieldId); // Campo de contraseña
        var eyeIcon = document.getElementById("eye-icon-" + fieldId); // Ícono de ojo
        var eyeSlashIcon = document.getElementById("eye-slash-icon-" + fieldId); // Ícono de ojo tachado

        // Alternar entre mostrar y ocultar la contraseña
        if (input.type === 'password') {
            input.type = 'text'; // Muestra la contraseña
            eyeIcon.style.display = 'none'; // Oculta el icono de ojo
            eyeSlashIcon.style.display = 'inline'; // Muestra el icono de ojo tachado
        } else {
            input.type = 'password'; // Oculta la contraseña
            eyeIcon.style.display = 'inline'; // Muestra el icono de ojo
            eyeSlashIcon.style.display = 'none'; // Oculta el icono de ojo tachado
        }
    }

    // Validación de contraseñas en el frontend
    document.querySelector('.form-crear-concepto').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Las contraseñas no coinciden');
            return false;
        }
    });
</script>

</body>
</html>