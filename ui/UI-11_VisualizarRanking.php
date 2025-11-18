<?php

// ------------------------------------------------------------
// UI-11: Visualizar Ranking
// Caso de uso asociado: FALTAFALTA
// ------------------------------------------------------------

session_start();
require_once '../gtr/GTR-01_GestionarUsuario.php';
require_once '../gtr/GTR-04_Validar.php';
require_once '../gtr/GTR-05_ObtenerRanking.php';

$usuario = Validar::obtenerUsuarioActual();

// Obtener parámetros de los filtros (por GET)
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'egresos';
$periodo = isset($_GET['periodo']) ? $_GET['periodo'] : '4semanas';

// Obtener ranking según el tipo seleccionado
if ($tipo === 'ingresos') {
    $ranking = ObtenerRanking::obtenerIngresos($usuario->idFamilia);
} else {
    $ranking = ObtenerRanking::obtenerEgresos($usuario->idFamilia);
}

// Filtrar según el período seleccionado
switch($periodo) {
    case '4semanas':
        $rankingFiltrado = ObtenerRanking::filtrarPorUltimas4Semanas($ranking);
        break;
    case '6meses':
        $rankingFiltrado = ObtenerRanking::filtrarPorUltimos6Meses($ranking);
        break;
    case '12meses':
        $rankingFiltrado = ObtenerRanking::filtrarPorUltimos12Meses($ranking);
        break;
    default:
        $rankingFiltrado = ObtenerRanking::filtrarPorUltimas4Semanas($ranking);
}


//var_dump($rankingFiltrado)
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking</title>

    <!-- CSS principal -->
    <link rel="stylesheet" href="../css/daily_input.css">
    <link rel="stylesheet" href="../css/configuracion.css">
    <link rel="stylesheet" href="../css/principal.css">
    <link rel="stylesheet" href="../css/ranking.css">
    <!-- CSS de íconos -->
    <link rel="stylesheet" href="../css/icons.css">
</head>
<body>
<div class="contenedor-principal">

    <!-- Cabecera -->
    <header class="barra-superior">
        <section class="seccion-izquierda">
            <h1 class="titulo-app">On a budget</h1>
        </section>

        <section class="seccion-derecha">
            <h2 class="subtitulo">Ranking</h2>

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

    <!-- Contenido principal -->
    <div class="contenedor-medio">
        <!-- Menu lateral -->
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
                <a class="opcion-menu activa" href="UI-11_VisualizarRanking.php">
                    <i class="icono icono-grafico"></i>Ranking
                </a>
                <a class="opcion-menu" href="UI-16_VisualizarConceptos.php">
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
        <main class="area-ranking">
            <!-- Controles superiores -->
            <div class="controles-ranking">
                <!-- Botones Egresos/Ingresos -->
                <div class="grupo-tipo">
                    <button class="btn-tipo <?php echo $tipo === 'egresos' ? 'activo' : ''; ?>" 
                            data-tipo="egresos">
                        Egresos
                    </button>
                    <button class="btn-tipo <?php echo $tipo === 'ingresos' ? 'activo' : ''; ?>" 
                            data-tipo="ingresos">
                        Ingresos
                    </button>
                </div>

                <!-- Filtros de período -->
                <div class="grupo-periodo">
                    <button class="btn-periodo <?php echo $periodo === '4semanas' ? 'activo' : ''; ?>" 
                            data-periodo="4semanas">
                        Últimas 4 semanas
                    </button>
                    <button class="btn-periodo <?php echo $periodo === '6meses' ? 'activo' : ''; ?>" 
                            data-periodo="6meses">
                        Últimos 6 meses
                    </button>
                    <button class="btn-periodo <?php echo $periodo === '12meses' ? 'activo' : ''; ?>" 
                            data-periodo="12meses">
                        Últimos 12 meses
                    </button>
                </div>
            </div>

            <!-- Contenedor de la tabla con diseño redondeado -->
            <div class="contenedor-tabla-ranking">
                <table class="tabla-ranking">
                    <thead>
                        <tr>
                            <th class="columna-ordenable">
                                Concepto
                                <span class="icono-orden">↕</span>
                            </th>
                            <th class="columna-ordenable">
                                Categoría
                                <span class="icono-orden">↕</span>
                            </th>
                            <th class="columna-ordenable">
                                Costo
                                <span class="icono-orden">↕</span>
                            </th>
                            <th class="columna-ordenable">
                                Subido por
                                <span class="icono-orden">↕</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($rankingFiltrado && count($rankingFiltrado) > 0): ?>
                            <?php foreach ($rankingFiltrado as $item): ?>
                                <tr class="fila-ranking">
                                    <td><?php echo htmlspecialchars($item['concepto']); ?></td>
                                    <td><?php echo htmlspecialchars($item['categoria']); ?></td>
                                    <td>S/. <?php echo number_format($item['monto'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($item['usuario']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="celda-vacia">
                                    No hay datos para mostrar en este período
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<!-- JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Obtener los parámetros actuales de la URL
        const urlParams = new URLSearchParams(window.location.search);
        const tipoActual = urlParams.get('tipo') || 'egresos';
        const periodoActual = urlParams.get('periodo') || '4semanas';

        // Manejo de botones de tipo (Egresos/Ingresos)
        const btnsTipo = document.querySelectorAll('.btn-tipo');
        btnsTipo.forEach(btn => {
            btn.addEventListener('click', function() {
                const nuevoTipo = this.dataset.tipo;
                
                // Actualizar URL con el nuevo tipo, manteniendo el período
                window.location.href = `UI-11_VisualizarRanking.php?tipo=${nuevoTipo}&periodo=${periodoActual}`;
            });
        });

        // Manejo de botones de período
        const btnsPeriodo = document.querySelectorAll('.btn-periodo');
        btnsPeriodo.forEach(btn => {
            btn.addEventListener('click', function() {
                const nuevoPeriodo = this.dataset.periodo;
                
                // Actualizar URL con el nuevo período, manteniendo el tipo
                window.location.href = `UI-11_VisualizarRanking.php?tipo=${tipoActual}&periodo=${nuevoPeriodo}`;
            });
        });

        // Manejo de ordenamiento de columnas (opcional - para implementar después)
        const columnasOrdenables = document.querySelectorAll('.columna-ordenable');
        columnasOrdenables.forEach(columna => {
            columna.addEventListener('click', function() {
                console.log('Ordenar por:', this.textContent.trim());
                // Aquí podrías implementar ordenamiento con JavaScript o AJAX
            });
        });
    });
</script>

</body>
</html>