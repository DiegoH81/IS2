<?php

// ------------------------------------------------------------
// UI-10: Visualizar Agenda
// Caso de uso asociado: CU-06 Visualizar agenda
// ------------------------------------------------------------

session_start();
require_once '../gtr/GTR-01_GestionarUsuario.php';
require_once '../gtr/GTR-04_Validar.php';
require_once '../gtr/GTR-06_ControladorDeAgenda.php';


//<!-- Paso 1-5 del CU-06: El GTR-06 obtiene los datos para mostrar de la agenda,
//                         relacionando los datos relacionados -->


$fecha_hoy = date('Y-m-d');  // Obtiene la fecha de hoy en formato YYYY-MM-DD
$usuario = Validar::obtenerUsuarioActual();
$agenda = ControladorAgenda::obtenerConceptosPorFecha($fecha_hoy, $usuario->idFamilia);

//<!-- Paso 6-7 del CU-06: El GTR-06 obtiene la proyeccion esperada y los ordena -->
$start_year = date('Y') . '-01-01';  // Esto dará la fecha en formato "YYYY-01-01", es decir, el 1 de enero del año actual.
$proyeccion_ingresos = ControladorAgenda::obtenerProyeccionIngresos($usuario->idFamilia, $start_year);
$proyeccion_egresos = ControladorAgenda::obtenerProyeccionEgresos($usuario->idFamilia, $start_year);
$balance_esperado = $proyeccion_ingresos - $proyeccion_egresos;



$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : 'todos';
// Filtrar la agenda según el filtro seleccionado
$agendaFiltrada = $agenda;
if ($filtro === 'ingresos') {
    $agendaFiltrada = array_filter($agenda, function($item) {
        return strtolower($item->tipo) === 'ingreso';
    });
} elseif ($filtro === 'egresos') {
    $agendaFiltrada = array_filter($agenda, function($item) {
        return strtolower($item->tipo) === 'egreso';
    });
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda</title>

    
    <!-- CSS principal -->
    <link rel="stylesheet" href="../css/daily_input.css">
    <link rel="stylesheet" href="../css/principal.css">
    <link rel="stylesheet" href="../css/configuracion.css">
    <link rel="stylesheet" href="../css/agenda.css">
    <!-- CSS de íconos -->
    <link rel="stylesheet" href="../css/icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


</head>
<body>
<div class="contenedor-principal">

    <!-- Cabecera -->
    <header class="barra-superior">
        <section class="seccion-izquierda">
            <h1 class="titulo-app">On a budget</h1>
        </section>

        <section class="seccion-derecha">
            <h2 class="subtitulo">Agenda</h2>

            <div class="info-usuario">
                <span class="nombre-usuario">
                        <?php echo htmlspecialchars($usuario->nombre); ?>
                </span>
                <span class="rol-usuario">
                        <?php echo htmlspecialchars($usuario->rol); ?>
                </span>
            </div>
        </section>
    </header>

    <!-- Contenido principal -->
    <div class="contenedor-medio">
        <!-- Menu lateral - ACTUALIZADO -->
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
                <a class="opcion-menu activa" href="#">
                    <i class="icono icono-grafico"></i>Agenda
                </a>
                <a class="opcion-menu" href="UI-11_VisualizarRanking.php">
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

        <!-- Area principal -->
        <main class="area-trabajo">
            <!-- Barra de búsqueda y filtros -->
            <div class="barra-filtros">
                <!-- Paso 8 del CU-06: La UI-10 muestra las opciones de filtro como (todos, ingresos y egresos) -->
                <div class="filtros-tabs">
                    <a href="?filtro=todos" class="tab-filtro <?php echo $filtro === 'todos' ? 'activo' : ''; ?>" style="text-decoration: none;">
                        Todos
                    </a>
                    <a href="?filtro=ingresos" class="tab-filtro <?php echo $filtro === 'ingresos' ? 'activo' : ''; ?>"style="text-decoration: none;">
                        Ingresos
                    </a>
                    <a href="?filtro=egresos" class="tab-filtro <?php echo $filtro === 'egresos' ? 'activo' : ''; ?>"style="text-decoration: none;">
                        Egresos
                    </a>
                </div>
            </div>

            <!-- Paso 11 del CU-06: La UI-10 Presenta la proyeccion calculada del resto del año -->
            <div class="seccion-proyeccion">
                <h3 class="titulo-seccion">Proyección esperada - resto del año</h3>
                

                <!-- Paso 9 del CU-06: La UI-10 presenta el balance esperado, asi como ingresos y egresos esperados-->
                <div class="contenedor-proyeccion">
                    <div class="item-proyeccion">
                        <button class="btn-proyeccion btn-ingresos">Ingresos esperados</button>
                        <span class="valor-proyeccion">
                            <?php echo 'S/ ' . number_format($proyeccion_ingresos, 2); ?>
                        </span>
                    </div>
                    
                    <div class="item-proyeccion">
                        <button class="btn-proyeccion btn-egresos">Egresos Esperados</button>
                        <span class="valor-proyeccion">
                            <?php echo 'S/ ' . number_format($proyeccion_egresos, 2); ?>
                        </span>
                    </div>
                </div>

                <div class="contenedor-balance">
                    <button class="btn-balance">Balance esperado</button>
                    <span class="valor-balance">
                        <?php echo 'S/ ' . number_format($balance_esperado, 2); ?>
                    </span>
                </div>
            </div>

            <!-- Paso 10 del CU-06: La UI-10 muestra los proximos pagos con fecha, dias restantes, tipo, concepto,
                                    categoria y estado de proximidad -->
            <div class="lista-transacciones">
                <?php if ($agendaFiltrada && count($agendaFiltrada) > 0): ?>
                    <?php foreach ($agendaFiltrada as $ag): ?>
                        <?php 
                            // Obtener días restantes
                            $diasRestantes = (int)$ag->dias_restantes;
                            
                            // Determinar si es urgente (menos de 7 días)
                            $esUrgente = $diasRestantes < 7;
                            
                            // Formatear la fecha en español
                            $fecha = new DateTime($ag->proxima_fecha);
                            $mesesES = [
                                'January' => 'enero', 'February' => 'febrero', 'March' => 'marzo',
                                'April' => 'abril', 'May' => 'mayo', 'June' => 'junio',
                                'July' => 'julio', 'August' => 'agosto', 'September' => 'septiembre',
                                'October' => 'octubre', 'November' => 'noviembre', 'December' => 'diciembre'
                            ];
                            $fechaFormateada = $fecha->format('d \d\e F');
                            foreach ($mesesES as $en => $es) {
                                $fechaFormateada = str_replace($en, $es, $fechaFormateada);
                            }
                        ?>
                        
                        <div class="item-transaccion <?php echo $esUrgente ? 'urgente' : ''; ?>">
                            <div class="fecha-transaccion">
                                <i class="icono-calendario"><i class="fa-solid fa-calendar"></i></i>
                                <span class="fecha"><?php echo $fechaFormateada; ?></span>
                                <span class="dias-restantes">(<?php echo $diasRestantes; ?> días restantes)</span>
                            </div>
                            
                            <div class="detalle-transaccion">
                                <span class="icono-tipo"><i class="fa-solid fa-sack-dollar"></i></span>
                                <span class="tipo-transaccion"><?php echo htmlspecialchars($ag->tipo); ?></span>
                                <span class="separador">-</span>
                                <span class="categoria"><?php echo htmlspecialchars($ag->categoria); ?></span>
                                <span class="separador">-</span>
                                <span class="concepto"><?php echo htmlspecialchars($ag->nombre); ?></span>
                            </div>
                            
                            <span class="monto">S/<?php echo number_format($ag->monto, 2); ?></span>
                            
                            <div class="estado-transaccion">
                                <?php if ($esUrgente): ?>
                                    <i class="icono-alerta"><i class="fa-solid fa-triangle-exclamation"></i></i>
                                    <span class="texto-estado">Menos de 7 días</span>
                                <?php else: ?>
                                    <i class="icono-reloj"><i class="fa-regular fa-clock"></i></i>
                                    <span class="texto-estado">Próximo</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="mensaje-vacio">
                        <p>No hay transacciones programadas.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Paginación -->
            <div class="paginacion">
                <span class="pagina-actual">1-2</span>
                <button class="btn-paginacion">&lt;</button>
                <button class="btn-paginacion">&gt;</button>
            </div>
        </main>
    </div>
</div>

<!-- JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filtros de tabs
        const tabs = document.querySelectorAll('.tab-filtro');
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                tabs.forEach(t => t.classList.remove('activo'));
                this.classList.add('activo');
                console.log('Filtro activo:', this.textContent);
            });
        });

        // Búsqueda
        const inputBusqueda = document.querySelector('.input-busqueda');
        if (inputBusqueda) {
            inputBusqueda.addEventListener('input', function() {
                console.log('Buscando:', this.value);
            });
        }

        // Paginación
        const btnsPaginacion = document.querySelectorAll('.btn-paginacion');
        btnsPaginacion.forEach(btn => {
            btn.addEventListener('click', function() {
                console.log('Cambio de página');
            });
        });
    });
</script>

</body>
</html>