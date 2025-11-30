<?php
/* FUN-48 filtrarUsuariosPorBusqueda
        Permite filtrar usuarios por la barra de búsqueda*/

function filtrarUsuariosPorBusqueda($familiaId, $cadena, &$usuarios) {
    $usuarios = GestionarUsuario::obtenerUsuariosBD($familiaId);


    $usuarios = array_filter($usuarios, function ($u) use ($cadena) {
        return stripos($u->usuario, $cadena) !== false ||
               stripos($u->nombre, $cadena) !== false ||
               stripos($u->rol, $cadena) !== false ||
               stripos($u->estado, $cadena) !== false;
    });    
}
?>


<?php

// ------------------------------------------------------------
// UI-12: Visualizar usuarios
// Caso de uso asociado: CU-08 Gestionar usuarios
// ------------------------------------------------------------
session_start();
require_once '../gtr/GTR-01_GestionarUsuario.php';

$mensaje_exito = '';
if (isset($_SESSION['mensaje_exito'])) {
    $mensaje_exito = $_SESSION['mensaje_exito'];
    unset($_SESSION['mensaje_exito']);
}

$cadena = isset($_GET['cadena']) ? $_GET['cadena'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_usuario'], $_POST['estado'])) {
    $idUsuario = intval($_POST['id_usuario']);
    $estado = (intval($_POST['estado']) === 1); // Convertir a booleano
    
    $resultado = GestionarUsuario::cambiarEstadoUsuarioBD($idUsuario, $estado);
    
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

$usuarios = null;
$familiaId = $_SESSION['familia_id'];

//<!-- Paso 1 del CU-08: La UI-12 obtiene los usuarios -->
if ($cadena !== '') {
        filtrarUsuariosPorBusqueda($familiaId, $cadena, $usuarios);
} else {
    $usuarios = GestionarUsuario::obtenerUsuariosBD($familiaId);
}
?>

<!-- Paso 2 del CU-08: La UI-12 se carga y presenta el campo de búsqueda -->
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
                        <div class="encabezado-tabla-superior">
                            <form method="GET" action="UI-12_VisualizarUsuarios.php" class="form-busqueda">
                                <input 
                                    type="text" 
                                    name="cadena" 
                                    placeholder="Buscar..." 
                                    value="<?= htmlspecialchars($cadena) ?>"
                                    class="input-busqueda">
                                <button type="submit" class="boton-buscar" style="background-color: #335ca4ff;">Buscar</button>


                                <?php if ($cadena !== ''): ?>

                                    <a href="UI-12_VisualizarUsuarios.php" class="boton-limpiar">Limpiar</a>
                                <?php endif; ?>
                                


                            </form>

                            <!-- Paso 3 del CU-08: La UI-12 muestra la opcion de crear usuario -->
                            <a href="UI-13_CrearUsuario.php" class="boton-crear">Crear usuario</a>
                        </div>
                        <h2 class="titulo-tabla">Configuración usuarios</h2>
                        <div class="linea-separadora"></div>
                        <div class="linea-azul"></div>
                    </header>

                    <!-- Paso 4 del CU-08: Presenta la lista de usuario con opciones (Habilitado/Deshabilitado) -->
                    <table class="tabla-datos" style="height: 600px;">
                        <thead>
                            <tr>
                                <th class="encabezado-tabla">Usuario</th>
                                <th class="encabezado-tabla">Nombre</th>
                                <th class="encabezado-tabla">Rol</th>
                                <th class="encabezado-tabla">Estado</th>
                                <th class="encabezado-tabla">Acción</th>
                            </tr>
                        </thead>
                        <tbody style="display: block; overflow-y: auto; max-height: 500px;">
                        <?php if ($usuarios && count($usuarios) > 0): ?>
                            <?php foreach ($usuarios as $u): ?>
                                <tr class="fila-tabla" id="fila-<?= $u->idUsuario ?>">
                                    <td class="celda">
                                        <?= htmlspecialchars($u->usuario) ?>
                                    </td>
                                    <td class="celda">
                                        <?= htmlspecialchars(string: $u->nombre) ?>
                                    </td>
                                    <td class="celda">
                                        <?= htmlspecialchars($u->rol) ?>
                                    </td>

                                    <td class="celda celda-estado">
                                        <?php
                                            // Convertimos el estado a booleano
                                            $estadoBool = ($u->estado === 'Habilitado'); 
                                            $estadoTexto = $estadoBool ? 'Habilitado' : 'Deshabilitado';
                                            $estadoValor = $estadoBool ? '1' : '0';
                                        ?>
                                        <button 
                                            type="button" 
                                            class="link-editar" 
                                            data-estado="<?= $estadoValor ?>" 
                                            onclick="abrirModal(<?= $u->idUsuario ?>, '<?= $estadoValor ?>', 'usuario')">
                                            <?= $estadoTexto ?>
                                        </button>
                                    </td>

                                    <td class="celda">

                                        <form action="UI-14_EditarUsuario.php" method="GET">
                                            <input type="hidden" name="usuario" value="<?= htmlspecialchars($u->usuario) ?>">
                                            <button type="submit" class="link-editar"
                                            <?= ($u->estado === 'Deshabilitado') ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : '' ?>>
                                                Editar
                                            </button>
                                        </form>
                                        
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                                      
                        <?php else: ?>
                            <tr><td colspan="8" class="celda">No se encontraron usuarios.</td></tr>
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
        <p>¿Seguro que desea cambiar el estado del usuario?</p>
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