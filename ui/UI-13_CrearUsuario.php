<?php

// ------------------------------------------------------------
// UI-13: Crear Usuario
// Caso de uso asociado: CU-08-1 Crear usuario
// ------------------------------------------------------------


session_start();
require_once '../gtr/GTR-01_GestionarUsuario.php';
require_once '../gtr/GTR-04_Validar.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_val   = $_POST['usuario'];
    $nombre_val     = $_POST['nombre'];
    $rol_val    = $_POST['rol'];
    $password_val = $_POST['password'];
    
    //<!-- Paso 10 del CU-08-1: El GTR-01 verificara la disponibilidad del usuario -->
    $repetido = Validar::solicitarValidacionUsuario($usuario_val);

    if ($repetido) {
        $error = "Usuario existente.";
    } else {
        //<!-- Paso 11 del CU-08-1: Se obtiene la ID de la familia actual -->
        //<!-- Paso 12-14 del CU-08-1: Se crea al nuevo usuario, mostrando un mensaje de exito y redirigiendo a la UI-12 -->
        GestionarUsuario::crearUsuarioBD($usuario_val, $nombre_val, $password_val, $rol_val, $_SESSION['familia_id']);
        $_SESSION['mensaje_exito'] = "Usuario creado correctamente.";
        header("Location: UI-12_VisualizarUsuarios.php");
        exit;
    }
}
?>

<!-- Paso 1 del CU-08-1: La UI-13 se carga y presenta los campos
                         Usuario, Nombre, Rol, Contraseña -->

<!-- Paso 7-9 del CU-08-1: La UI-13 Validara los datos -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear usuario</title>
    <link rel="stylesheet" href="../css/principal.css">
    <link rel="stylesheet" href="../css/icons.css">
    <link rel="stylesheet" href="../css/configuracion.css">
</head>
<body>
<div class="contenedor-principal">
    <header class="barra-superior">
        <section class="seccion-izquierda">
            <h1 class="titulo-app">On a budget</h1>
        </section>
        <section class="seccion-derecha">
            <h2 class="subtitulo">Configuración</h2>
            <div class="info-usuario">
                <span class="nombre-usuario">
                        <?php echo htmlspecialchars($_SESSION['nombre']); ?>
                </span>
                <span class="rol-usuario">
                        <?php echo htmlspecialchars($_SESSION['rol']); ?>
                </span>
            </div>
        </section>
    </header>

    <div class="contenedor-medio">
        <aside class="menu-lateral" id="menuLateral">
            <nav>
                <a class="opcion-menu" href="UI-04_RegistroDiario.php">
                    <i class="icono icono-documento"></i>Registro Diario
                </a>
                <a class="opcion-menu" href="UI-05_Balance.php">
                    <i class="icono icono-grafico"></i>Balance
                </a>
                <a class="opcion-menu" href="UI-07_CuentaPersonal.php">
                    <i class="icono icono-persona"></i>Cuenta
                </a>
                <a class="opcion-menu" href="UI-10_VisualizarAgenda.php">
                    <i class="icono icono-grafico"></i>Agenda
                </a>
                <a class="opcion-menu" href="UI-11_VisualizarRanking.php">
                    <i class="icono icono-grafico"></i>Ranking
                </a>
                <a class="opcion-menu activa" href="UI-16_VisualizarConceptos.php">
                    <i class="icono icono-configuracion"></i>Configuración
                </a>
            </nav>

            <footer class="parte-abajo">
                <a class="opcion-menu" href="UI-01_InicioDeSesion.php">
                    <i class="icono icono-salir"></i>Cerrar sesión
                </a>
            </footer>
        </aside>

        <main class="contenedor-medio">
            <aside class="submenu-configuracion" id="Sub_menuConfig">
                <nav>
                    <?php if ($_SESSION['rol'] === 'Administrador familiar'): ?>
                        <a class="opcion-submenu activa" href="UI-12_VisualizarUsuarios.php">
                            <i></i>Usuarios
                        </a>
                    <?php endif; ?>
                    <a class="opcion-submenu" href="UI-16_VisualizarConceptos.php">
                        <i></i>Conceptos
                    </a>
                    <a class="opcion-submenu" href="UI-20_VisualizarCategoria.php">
                        <i></i>Categorías
                    </a>
                    <a class="opcion-submenu" href="UI-23_VisualizarTransaccion.php">
                        <i></i>Transacciones
                    </a>
                </nav>
            </aside>

            <section class="contenedor-tablas">
                <article class="tabla">
                    <header>
                        <h2 class="titulo-tabla">Crear usuario</h2>
                        <div class="linea-separadora"></div>
                        <div class="linea-azul"></div>
                    </header>

                    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

                    
                    <form class="form-crear-concepto" method="POST">

                    <h1 style="text-align: center;">Crear usuario</h1>

                        <!-- Paso 2-5 del CU-08-1: El usuario ingresara los datos necesarios -->

                        <div class="campo-formulario">
                            <label for="usuario">Usuario:</label>
                            <input type="text" id="usuario" name="usuario" placeholder="Ingrese usuario" required>
                        </div>
                        <div class="campo-formulario">
                            <label for="nombre">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" placeholder="Ingrese nombre" required>
                        </div>

                        <div class="campo-formulario">
                            <label for="rol">Rol:</label>
                            <select id="rol" name="rol" required>
                                <option value="">Seleccionar categoría</option>
                                <option value="Administrador familiar">Administrador familiar</option>
                                <option value="Familiar">Familiar</option>
                            </select>
                        </div>

                        <div class="campo-formulario">
                            <label for="password">Contraseña:</label>
                            <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required>
                        </div>
                        

                        <!-- Paso 6 del CU-08-1: El administrador confirma la contraseña en el campo ConfirmarContraseña -->
                        <div style="text-align:center;">
                            <div class="grupo-botones">
                                <button type="button" class="boton-crear boton-cancelar" onclick="window.location.href='UI-12_VisualizarUsuarios.php'">Cancelar</button>
                                <button type="submit" class="boton-crear">Crear</button>
                            </div>

                        </div>

                    </form>
                </article>
            </section>
        </main>
    </div>
</div>


</body>
</html>
