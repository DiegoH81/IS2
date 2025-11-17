<!-- UI inactiva -->

<?php

// ------------------------------------------------------------
// UI-11: Visualizar Ranking
// Caso de uso asociado: FALTAFALTA
// ------------------------------------------------------------

session_start();
require_once '../gtr/GTR-01_GestionarUsuario.php';
require_once '../gtr/GTR-04_Validar.php';

$usuario = Validar::obtenerUsuarioActual();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deshabilitar']))
{
    GestionarUsuario::cambiarEstadoUsuarioBD($usuario->idUsuario, 0);
    header("Location: UI-01_InicioDeSesion.php");
}

// Aquí obtendrías los datos del ranking desde la BD
// $rankingData = GestionarRanking::obtenerRanking($periodo, $tipo);
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
                    <button class="btn-tipo activo" data-tipo="egresos">Egresos</button>
                    <button class="btn-tipo" data-tipo="ingresos">Ingresos</button>
                </div>

                <!-- Filtros de período -->
                <div class="grupo-periodo">
                    <button class="btn-periodo activo" data-periodo="4semanas">Últimas 4 semanas</button>
                    <button class="btn-periodo" data-periodo="6meses">Últimos 6 meses</button>
                    <button class="btn-periodo" data-periodo="12meses">Últimos 12 meses</button>
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
                        <!-- Ejemplo de datos estáticos - reemplazar con foreach de PHP -->
                        <tr class="fila-ranking">
                            <td>Universidad</td>
                            <td>Educación</td>
                            <td>S/. 1500</td>
                            <td>Erick Ramirez</td>
                        </tr>
                        <tr class="fila-ranking">
                            <td>Casa</td>
                            <td>Alquiler</td>
                            <td>S/. 1250</td>
                            <td>Erick Ramirez</td>
                        </tr>
                        <tr class="fila-ranking">
                            <td>Insumos</td>
                            <td>Alimentación</td>
                            <td>S/. 800</td>
                            <td>Rosa Ramirez</td>
                        </tr>
                        <tr class="fila-ranking">
                            <td>Cochera</td>
                            <td>Alquiler</td>
                            <td>S/. 200</td>
                            <td>Rosa Ramirez</td>
                        </tr>
                        <tr class="fila-ranking">
                            <td>Internet</td>
                            <td>Servicios básicos</td>
                            <td>S/. 100</td>
                            <td>Manuel Ramirez</td>
                        </tr>
                        <tr class="fila-ranking">
                            <td>Agua</td>
                            <td>Servicios básicos</td>
                            <td>S/. 25</td>
                            <td>Rosa Ramirez</td>
                        </tr>

                        <!-- Aquí iría el foreach con datos reales:
                        <?php if (isset($rankingData) && count($rankingData) > 0): ?>
                            <?php foreach ($rankingData as $item): ?>
                                <tr class="fila-ranking">
                                    <td><?php echo htmlspecialchars($item->concepto); ?></td>
                                    <td><?php echo htmlspecialchars($item->categoria); ?></td>
                                    <td>S/. <?php echo number_format($item->costo, 0); ?></td>
                                    <td><?php echo htmlspecialchars($item->usuario); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="celda-vacia">No hay datos para mostrar</td>
                            </tr>
                        <?php endif; ?>
                        -->
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<!-- JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Manejo de botones de tipo (Egresos/Ingresos)
        const btnsTipo = document.querySelectorAll('.btn-tipo');
        btnsTipo.forEach(btn => {
            btn.addEventListener('click', function() {
                btnsTipo.forEach(b => b.classList.remove('activo'));
                this.classList.add('activo');
                const tipo = this.dataset.tipo;
                console.log('Tipo seleccionado:', tipo);
                // Aquí harías la petición AJAX o recarga con el nuevo tipo
            });
        });

        // Manejo de botones de período
        const btnsPeriodo = document.querySelectorAll('.btn-periodo');
        btnsPeriodo.forEach(btn => {
            btn.addEventListener('click', function() {
                btnsPeriodo.forEach(b => b.classList.remove('activo'));
                this.classList.add('activo');
                const periodo = this.dataset.periodo;
                console.log('Período seleccionado:', periodo);
                // Aquí harías la petición AJAX o recarga con el nuevo período
            });
        });

        // Manejo de ordenamiento de columnas
        const columnasOrdenables = document.querySelectorAll('.columna-ordenable');
        columnasOrdenables.forEach(columna => {
            columna.addEventListener('click', function() {
                console.log('Ordenar por:', this.textContent.trim());
                // Aquí implementarías la lógica de ordenamiento
            });
        });
    });
</script>

</body>
</html>