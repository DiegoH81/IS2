<?php

/* FUN-80 filtrarTransaccionesPorBusqueda
    Permite filtrar transacciones por la barra de búsqueda */
function filtrarTransaccionesPorBusqueda($familiaId, $cadena, &$transacciones) {
    $transacciones = GestionarTransaccion::relacionarDatosTransacciones($familiaId);

    $transacciones = array_filter($transacciones, function ($t) use ($cadena) {
        return stripos($t['concepto'], $cadena) !== false ||
               stripos($t['usuario'], $cadena) !== false ||
               stripos($t['tipo'], $cadena) !== false ||
               stripos($t['fecha'], $cadena) !== false ||
               stripos($t['monto'], $cadena) !== false;
    });
}
?>

<?php
// ------------------------------------------------------------
// UI-23: Visualizar transacción
// Caso de uso asociado: CU-11 - Gestionar Transacciones
// ------------------------------------------------------------


//<!-- Paso 1 del CU-11: El GTR-07 obtiene los datos necesarios para poder ser visualizados -->
session_start();
require_once '../gtr/GTR-07_GestionarTransaccion.php';

$mensaje_exito = '';
if (isset($_SESSION['mensaje_exito'])) {
    $mensaje_exito = $_SESSION['mensaje_exito'];
    unset($_SESSION['mensaje_exito']);
}

$cadena = isset($_GET['cadena']) ? $_GET['cadena'] : '';

// Obtener las transacciones
$transacciones = null;
$familiaId = $_SESSION['familia_id'];

if ($cadena !== '') {
    filtrarTransaccionesPorBusqueda($familiaId, $cadena, $transacciones);
} else {
    $transacciones = GestionarTransaccion::relacionarDatosTransacciones($familiaId);
}
?>

<!-- Paso 2-3 del CU-11: La UI-23 Presenta las interfaces -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transacciones</title>

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
            <h2 class="subtitulo">Transacciones</h2>

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
                <a class="opcion-menu activa" href="UI-23_VisualizarTransaccion.php">
                    <i class="icono icono-configuracion"></i>Transacciones
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
                    <a class="opcion-submenu" href="UI-20_VisualizarCategoria.php">
                        <i></i>Categorías
                    </a>
                    <a class="opcion-submenu activa" href="UI-23_VisualizarTransaccion.php">
                        <i></i>Transacciones
                    </a>
                </nav>
            </aside>
            <section class="contenedor-tablas">
                <article class="tabla">
                    <header>
                        <div class="encabezado-tabla-superior">
                            <form method="GET" action="UI-23_VisualizarTransaccion.php" class="form-busqueda">
                                <input 
                                    type="text" 
                                    name="cadena" 
                                    placeholder="Buscar..." 
                                    value="<?= htmlspecialchars($cadena) ?>"
                                    class="input-busqueda">
                                <button type="submit" class="boton-buscar" style="background-color: #335ca4ff;">Buscar</button>

                                <?php if ($cadena !== ''): ?>
                                    <a href="UI-23_VisualizarTransaccion.php" class="boton-limpiar">Limpiar</a>
                                <?php endif; ?>
                            </form>
                            
                        </div>
                        <h2 class="titulo-tabla">Gestión de Transacciones</h2>
                        <div class="linea-separadora"></div>
                        <div class="linea-azul"></div>
                    </header>

                    <!-- Paso 4 del CU-11: Se muestra la tabla y las distintas opciones de gestion según el rol -->
                    <table class="tabla-datos" style="height: 600px;">
                        <thead>
                            <tr>
                                <th class="encabezado-tabla">Fecha</th>
                                <th class="encabezado-tabla">Concepto</th>
                                <th class="encabezado-tabla">Usuario</th>
                                <th class="encabezado-tabla">Tipo</th>
                                <th class="encabezado-tabla">Monto</th>
                                <th class="encabezado-tabla">Acción</th>
                            </tr>
                        </thead>
                        <tbody style="display: block; overflow-y: auto; max-height: 500px;">
                        <?php if ($transacciones && count($transacciones) > 0): ?>
                            <?php 
                            $fechaActual = new DateTime();
                            foreach ($transacciones as $t): 
                                // Verificar permisos: Admin familiar o usuario que creó la transacción
                                $puedeEditar = ($_SESSION['rol'] === 'Administrador familiar') || ($_SESSION['id_usuario'] == $t['idUsuario']);
                                
                                // Verificar que la transacción tenga menos de 30 días
                                $fechaTransaccion = new DateTime($t['fecha']);
                                $diferenciaDias = $fechaActual->diff($fechaTransaccion)->days;
                                $esReciente = $diferenciaDias <= 30;
                                
                                // Solo puede editar si tiene permisos Y la transacción es reciente
                                $puedeEditarFinal = $puedeEditar && $esReciente;
                            ?>
                                <tr class="fila-tabla" id="fila-<?= $t['idTransaccion'] ?>">
                                    <td class="celda"><?= htmlspecialchars($t['fecha']) ?></td>
                                    <td class="celda"><?= htmlspecialchars($t['concepto']) ?></td>
                                    <td class="celda"><?= htmlspecialchars($t['usuario']) ?></td>
                                    <td class="celda"><?= htmlspecialchars($t['tipo']) ?></td>
                                    <td class="celda">S/ <?= number_format($t['monto'], 2) ?></td>
                                    <td class="celda">
                                        <form action="UI-24_EditarTransaccion.php" method="GET">
                                            <input type="hidden" name="idtransaccion" value="<?= $t['idTransaccion'] ?>">
                                            <input type="hidden" name="origen" value="visualizar_transacciones">
                                            <button type="submit" class="link-editar"
                                            <?= !$puedeEditarFinal ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : '' ?>
                                            <?= !$esReciente ? 'title="No se puede editar transacciones con más de 30 días"' : '' ?>
                                            >
                                                Editar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="celda">No se encontraron transacciones.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </article>
            </section>
        </main>
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
document.addEventListener('DOMContentLoaded', function() {
    const btnCerrar = document.getElementById('btnCerrarExito');
    const modal = document.getElementById('modalExito');
    
    if (btnCerrar && modal) {
        btnCerrar.addEventListener('click', function() {
            modal.style.display = 'none';
        });
        
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    }
});
</script>
<?php endif; ?>

</body>
</html>