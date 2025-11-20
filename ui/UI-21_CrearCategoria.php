<?php

// ------------------------------------------------------------
// UI-21: Crear categoría
// Caso de uso asociado: CU-10-1 Crear categoría
// ------------------------------------------------------------


session_start();
require_once '../gtr/GTR-09_GestionarCategoria.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_val          = $_POST['nombre'];
    $descripcion_val     = $_POST['descripcion'];
    
    GestionarCategoria::crearCategoriaBD($nombre_val, $descripcion_val, $_SESSION['familia_id'], $_SESSION['id_usuario']);
    $_SESSION['mensaje_exito'] = "Categoría creada correctamente.";
    header("Location: UI-20_VisualizarCategoria.php");
    exit;
}

?>

<!-- Paso 1 del CU-10-1: La interfaz de Crear categoría (UI-21) se carga. -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear concepto</title>
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
                        <a class="opcion-submenu" href="UI-12_VisualizarUsuarios.php">
                            <i></i>Usuarios
                        </a>
                    <?php endif; ?>
                    <a class="opcion-submenu" href="UI-16_VisualizarConceptos.php">
                        <i></i>Conceptos
                    </a>
                    <a class="opcion-submenu activa" href="UI-20_VisualizarCategoria.php">
                        <i></i>Categorías
                    </a>
                </nav>
            </aside>

            <section class="contenedor-tablas">
                <article class="tabla">
                    <header>
                        <h2 class="titulo-tabla">Crear categoría</h2>
                        <div class="linea-separadora"></div>
                        <div class="linea-azul"></div>
                    </header>
                    
                    
                    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

                    
                    <!-- Paso 3-6 del CU-10-1: Proceso de registro de datos. -->
                    <form class="form-crear-concepto" method="POST">

                    <h1 style="text-align: center;">Crear categoría</h1>
                        
                        <div class="campo-formulario">
                            <label for="nombre">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" placeholder="Ingrese nombre" required>
                        </div>

                        <div class="campo-formulario">
                            <label for="descripcion">Descripcion:</label>
                            <textarea rows="5" cols="40" name="descripcion" id="descripcion" placeholder="Ingrese la descripcion"   spellcheck="false"></textarea>
                        </div>

                        <!-- Paso 5 del CU-10-1: El AC-02 selecciona la opción Crear. -->
                        <div style="text-align:center;">

                            <div class="grupo-botones">
                                <!-- Paso 5 del CU-10-1: La interfaz (UI-21) redirige al AC-02-Familiar a la interfaz (UI-20). -->
                                <button type="button" class="boton-crear boton-cancelar" onclick="window.location.href='UI-20_VisualizarCategoria.php'">Cancelar</button>
                                <button type="submit" class="boton-crear">Guardar categoria</button>
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
