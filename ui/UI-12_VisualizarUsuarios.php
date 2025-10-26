<?php

// ------------------------------------------------------------
// UI-12: Visualizar usuarios
// Caso de uso asociado: CU-19 Gestionar categoría
// ------------------------------------------------------------
session_start();
require_once '../gtr/GTR-01_GestionarUsuario.php';

$usuarios = GestionarUsuario::obtenerUsuariosBD($_SESSION['familia_id']);
//var_dump($usuarios);


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuracion</title>

    <!-- CSS principal -->
    <link rel="stylesheet" href="../css/principal.css">
    <link rel="stylesheet" href="../css/configuracion.css">
    <!-- CSS de íconos -->
    <link rel="stylesheet" href="../css/icons.css">

    <link rel="stylesheet" href="../css/modal.css">
    <link rel="stylesheet" href="../css/busqueda.css">
</head>
<body>

<div class="contenedor-principal">
    <!-- Cabecera -->
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
        <!-- Menú lateral -->
        <aside class="menu-lateral" id="menuLateral">
            <nav>
                <a class="opcion-menu" href="daily_input.php">
                    <i class="icono icono-documento"></i>Registro Diario
                </a>
                <a class="opcion-menu" href="#">
                    <i class="icono icono-grafico"></i>Balance
                </a>
                <a class="opcion-menu" href="#">
                    <i class="icono icono-persona"></i>Cuenta
                </a>
                <a class="opcion-menu" href="#">
                    <i class="icono icono-grafico"></i>Agenda
                </a>
                <a class="opcion-menu" href="#">
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

        <!-- Área principal -->
        <main class="contenedor-medio">
            <aside class="submenu-configuracion" id="Sub_menuConfig">
                <nav>
                    <a class="opcion-submenu activa" href="UI-12_VisualizarUsuarios.php">
                        <i></i>Usuarios
                    </a>
                    <a class="opcion-submenu" href="UI-16_VisualizarConceptos.php">
                        <i></i>Conceptos
                    </a>
                    <a class="opcion-submenu" href="UI-20_VisualizarCategoria.php">
                        <i></i>Categorías
                    </a>
                </nav>
            </aside>

            <section class="contenedor-tablas">
                <article class="tabla">
                    <!-- Paso 8 del CU-15: Mostrar opción de Crear concepto. -->
                    <header>
                        <div class="encabezado-tabla-superior">
                            <form method="GET" action="UI-16_VisualizarConceptos.php" class="form-busqueda">
                                <input 
                                    type="text" 
                                    name="cadena" 
                                    placeholder="Buscar..." 
                                    value="A buscar"
                                    class="input-busqueda">
                                <button type="submit" class="boton-buscar">Buscar</button>


                                <!--<?php if ($cadena !== ''): ?>

                                    <a href="UI-16_VisualizarConceptos.php" class="boton-limpiar">Limpiar</a>
                                <?php endif; ?>
                                -->


                            </form>

                            <a href="UI-13_CrearUsuario.php" class="boton-crear">Crear usuario</a>
                        </div>
                        <h2 class="titulo-tabla">Configuración usuarios</h2>
                        <div class="linea-separadora"></div>
                        <div class="linea-azul"></div>
                    </header>

                    <table class="tabla-datos">
                        <thead>
                            <tr>
                                <th class="encabezado-tabla">Usuario</th>
                                <th class="encabezado-tabla">Nombre</th>
                                <th class="encabezado-tabla">Rol</th>
                                <th class="encabezado-tabla">Estado</th>
                                <th class="encabezado-tabla">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                        <!--<?php if ($usuarios && count($usuarios) > 0): ?>-->
                            <?php foreach ($usuarios as $u): ?>
                                <tr class="fila-tabla" id="fila-<?= $u['id_usuario'] ?>">
                                    <td class="celda">
                                        <?= htmlspecialchars($u['usuario']) ?>
                                    </td>
                                    <td class="celda">
                                        <?= htmlspecialchars(string: $u['nombre']) ?>
                                    </td>
                                    <td class="celda">
                                        <?= htmlspecialchars($u['rol']) ?>
                                    </td>

                                    <td class="celda celda-estado">
                                        <button 
                                            type="button" 
                                            class="link-editar" 
                                            data-estado="<?= $u['estado'] === 'Habilitado' ? '1' : '0' ?>" 
                                            onclick="abrirModal(<?= $u['id_usuario'] ?>, '<?= $u['estado'] === 'Habilitado' ? '1' : '0' ?>', 'usuario')">
                                            <?= htmlspecialchars($u['estado']) ?>
                                        </button>
                                    </td>



                                    <!-- Paso 9 del CU-15: Mostrar opciones de gestión según el rol. -->
                                    <!-- Paso 9.1/9.2: Si es familiar, solo puede editar los suyos. -->

                                    <td class="celda">
                                        <form action="UI-14_EditarUsuario.php" method="GET">
                                            <input type="hidden" name="usuario" value="<?= htmlspecialchars($u['usuario']) ?>">
                                            <button type="submit" class="link-editar">
                                                Editar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                                <!--           
                        <?php else: ?>
                            <tr><td colspan="8" class="celda">No hay usuarios registrados.</td></tr>
                        <?php endif; ?>
                        -->


                        </tbody>
                    </table>
                </article>
            </section>
        </main>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const switchBtn = document.querySelector('.boton-switch input');
        if (switchBtn) {
            switchBtn.addEventListener('change', function() {
                console.log('Modo:', this.checked ? 'Personal' : 'Familiar');
            });
        }
    });
</script>
<!-- UI-19 Modificar Estado del Concepto -->
<div id="modalConfirmar" class="modal" style="display:none;">
    <div class="modal-contenido">
        <p>¿Seguro que desea cambiar el estado del usuario?</p>
        <div class="modal-botones">
            <button id="btnSi">Sí</button>
            <button id="btnNo">No</button>
        </div>
    </div>
</div>
<script src="../js/popup_estado.js"></script>
</body>
</html>