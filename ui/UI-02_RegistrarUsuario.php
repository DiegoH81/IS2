<?php
session_start();
require_once '../gtr/GTR-04_Validar.php';
require_once '../gtr/GTR-10_GestionarFamilia.php';
require_once '../gtr/GTR-01_GestionarUsuario.php';

$error = '';

// ------------------------------------------------------------
// UI-02: Registro de usuario
// Caso de uso asociado: CU-02 Registrarse en la aplicación
// ------------------------------------------------------------


if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
    $usuario = trim($_POST['usuario']);
    $nombre = trim($_POST['nombre']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $family_password = trim($_POST['family_password']);
    
    $usuarioExistente = Validar::solicitarValidacionUsuario("ana_rod");
    $familiaId = GestionarFamilia::obtenerFamiliaPorCodigoBD($family_password);
    
    
    //<!-- Paso 9-12 del CU-02: Se empiezan a hacer los procesos de validacion necesarios -->
    if ($password !== $confirm_password) {
        $error = 'Las contraseñas no coinciden';
    }
    else if ($usuarioExistente === 't')
    {
        $error = "El nombre de usuario ya está registrado.";
    }
    else if ($familiaId === -1)
    {
        $error = "Contraseña familiar incorrecta.";
    }
    else
    {
        //<!-- Paso 13 del CU-02: El GTR-01 crea un usuario nuevo en la BD -->

        $usuarios = GestionarUsuario::obtenerUsuariosBD($familiaId);
        if (empty($usuarios)) {
            $rol = "Administrador familiar";
        } else {
            $rol = "Familiar";
        }

        GestionarUsuario::crearUsuarioBD($usuario, $nombre, $password, $rol, $familiaId);

        //<!-- Paso 14 del CU-02: La UI-02 redirige al AC-02 Familiar a la UI-01 -->
        header("Location: UI-01_InicioDeSesion.php");
        exit();
    }
}
?>

<!-- Paso 1 del CU-02: Cargar interfaces -->
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
                    
                    <!-- Paso 3-6 del CU-02: Ingresar usuario, nobmre, contraseña y contraseña familiar -->
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

                    <!-- Paso 2 del CU-02: La interfaz presenta la opcion de Registrar y Registrar Familia -->
                    <div class="botones-formulario" style="display: flex; align-items: center; justify-content: center; gap: 12px;">
                        <a href="UI-01_InicioDeSesion.php" class="boton-cancelar">Cancelar</a>
                        <!-- Paso 7 del CU-02: El AC-02 selecciona la opcion de registrar -->
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
</script>

</body>
</html>