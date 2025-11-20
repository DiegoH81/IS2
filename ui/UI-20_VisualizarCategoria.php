<?php

/* FUN-50 filtrarCategoriasPorBusqueda
        Permite filtrar categorias por la barra de búsqueda*/
function filtrarCategoriasPorBusqueda($familiaId, $cadena, &$categorias, &$usuarios) {
    $categorias = GestionarCategoria::obtenerCategoriasBD($familiaId);
    $usuarios = GestionarUsuario::obtenerUsuariosBD($familiaId);

    //CATEGORIA
    //USUARIO

    $categorias = array_filter($categorias, function ($cat) use ($cadena) {
        return stripos($cat->nombre, $cadena) !== false ||
               stripos($cat->descripcion, $cadena) !== false ||
               stripos($cat->estado, $cadena) !== false;
    });

    // Filtrar los usuarios que coincidan con la cadena (por nombre, por ejemplo)
    $usuarios = GestionarUsuario::obtenerUsuariosBD($familiaId);    
}
?>

<?php

// ------------------------------------------------------------
// UI-20: Visualizar categoria
// Caso de uso asociado: CU-10 Gestionar categoría
// ------------------------------------------------------------

session_start();
require_once '../gtr/GTR-09_GestionarCategoria.php';
require_once '../gtr/GTR-01_GestionarUsuario.php';

$mensaje_exito = '';
if (isset($_SESSION['mensaje_exito'])) {
    $mensaje_exito = $_SESSION['mensaje_exito'];
    unset($_SESSION['mensaje_exito']);
}

$cadena = isset($_GET['cadena']) ? $_GET['cadena'] : '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idcategoria'], $_POST['estado'])) {
    $idCategoria = intval($_POST['idcategoria']);
    $estado = (intval($_POST['estado']) === 1); // Convertir a booleano
    
    // Llamar a la función para cambiar el estado
    $resultado = GestionarCategoria::editarEstadoCategoriaBD($idCategoria, $estado);
    
    // Si es petición AJAX, devolver JSON
    if(isset($_POST['ajax'])) {
        header('Content-Type: application/json');
        if($resultado) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al actualizar']);
        }
        exit;
    }
}


//<!-- Paso 1 del CU-10: Se obtienen las categorias. -->
$categorias = null;
$usuarios = null;

$familiaId = $_SESSION['familia_id'];
if ($cadena !== '') {
        filtrarCategoriasPorBusqueda($familiaId, $cadena, $categorias, $usuarios);
} else {
    $categorias = GestionarCategoria::obtenerCategoriasBD($familiaId);
    $usuarios = GestionarUsuario::obtenerUsuariosBD($familiaId);
}

//var_dump( $usuarios );
//var_dump($categorias);
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

        <!-- Área principal -->
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
                        <div class="encabezado-tabla-superior">
                            <form method="GET" action="UI-20_VisualizarCategoria.php" class="form-busqueda">
                                <!-- Paso 2 del CU-10: Se muestra la búsqueda. -->
                                <input 
                                    type="text" 
                                    name="cadena" 
                                    placeholder="Buscar..." 
                                    value="<?= htmlspecialchars($cadena) ?>"
                                    class="input-busqueda">
                                <button type="submit" class="boton-buscar" style="background-color: #4A5568;">Buscar</button>


                                <?php if ($cadena !== ''): ?>

                                    <a href="UI-20_VisualizarCategoria.php" class="boton-limpiar">Limpiar</a>
                                <?php endif; ?>
                                


                            </form>

                            <!-- Paso 3 del CU-10: Se muestra la opción de crear categoría. -->
                            <a href="UI-21_CrearCategoria.php" class="boton-crear">Crear categoría</a>
                        </div>
                        <h2 class="titulo-tabla">Configuración categoria</h2>
                        <div class="linea-separadora"></div>
                        <div class="linea-azul"></div>
                    </header>


                    <!-- Paso 4 del CU-10: Se presentan la lista de categorías. -->
                    <table class="tabla-datos">
                        <thead>
                            <tr>
                                <th class="encabezado-tabla">Nombre</th>
                                <th class="encabezado-tabla">Descripción</th>
                                <th class="encabezado-tabla">Creado por</th>
                                <th class="encabezado-tabla">Estado</th>
                                <th class="encabezado-tabla">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($categorias && count($categorias) > 0): ?>
                            <?php
                                // Crear un mapa id_usuario → nombre para buscar rápido
                                $mapaUsuarios = [];
                                foreach ($usuarios as $u) {
                                    $mapaUsuarios[$u->idUsuario] = $u->nombre;
                                }
                            ?>
                            <?php foreach ($categorias as $c): ?>
                                <tr class="fila-tabla" id="fila-<?= $c->idCategoria ?>">
                                    <td class="celda"><?= htmlspecialchars($c->nombre) ?></td>
                                    <td class="celda"><?= htmlspecialchars(string: $c->descripcion) ?></td>

                                    <td class="celda">
                                        <?= htmlspecialchars($mapaUsuarios[$c->idUsuario] ?? 'Desconocido') ?>
                                    </td>

                                    <!-- Paso 5 del CU-10: Se muestran opciones de gestión según el rol. -->
                                    <?php
                                        // Permite editar si es Admin Familiar o si el concepto lo subió el mismo usuario
                                        $puedeEditar = ($_SESSION['rol'] === 'Administrador familiar') || ($_SESSION['id_usuario'] == $c->idUsuario);
                                        $estadoValor = $c->estado === 'Habilitado' ? '1' : '0';
                                        $onclick = $puedeEditar ? "abrirModal({$c->idCategoria}, '{$estadoValor}', 'categoria')" : '';
                                    ?>
                                    <td class="celda celda-estado">
                                        <button
                                            type="button"
                                            class="link-editar"
                                            data-estado="<?= $c->estado ?>"
                                            onclick="<?= $onclick ?>"
                                            <?= !$puedeEditar ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : '' ?>
                                        >
                                            <?= htmlspecialchars($c->estado) ?>
                                        </button>
                                    </td>

                                    <td class="celda">
                                        <?php
                                            // Permite editar si es Admin Familiar o si el concepto lo subió el mismo usuario
                                            $puedeEditar = ($_SESSION['rol'] === 'Administrador familiar') || ($_SESSION['id_usuario'] == $c->idUsuario);

                                            // Bloquea el botón si no puede editar o si la categoría está deshabilitada
                                            $deshabilitarBoton = !$puedeEditar || ($c->estado === 'Deshabilitado');
                                        ?>
                                        <form action="UI-22_EditarCategoria.php" method="GET">
                                            <input type="hidden" name="idcategoria" value="<?= $c->idCategoria ?>">
                                            <button type="submit" class="link-editar"
                                            <?= $deshabilitarBoton ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : '' ?>
                                            >
                                                Editar
                                            </button>
                                        </form>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="celda">No se encontraron categorias.</td></tr>
                        <?php endif; ?>


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
        <p>¿Seguro que desea cambiar el estado de la categoria?</p>
        <div class="modal-botones">
            <button id="btnSi">Sí</button>
            <button id="btnNo">No</button>
        </div>
    </div>
</div>

<?php if ($mensaje_exito): ?>
<div id="modalExito" class="modal" style="display:block;">
    <div class="modal-contenido modal-exito">
        <div class="icono-exito">✓</div>
        <p><?= htmlspecialchars($mensaje_exito) ?></p>
        <button id="btnCerrarExito" class="boton-aceptar">Aceptar</button>
    </div>
</div>

<script>
// Cerrar modal de éxito
document.addEventListener('DOMContentLoaded', function() {
    const btnCerrar = document.getElementById('btnCerrarExito');
    const modal = document.getElementById('modalExito');
    
    if (btnCerrar && modal) {
        btnCerrar.addEventListener('click', function() {
            modal.style.display = 'none';
        });
        
        // También cerrar al hacer clic fuera del modal
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    }
});
</script>
<?php endif; ?>

<script src="../js/popup_estado.js"></script>

</body>
</html>