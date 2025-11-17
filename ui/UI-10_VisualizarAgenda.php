<!-- UI inactiva -->

<?php

// ------------------------------------------------------------
// UI-10: Visualizar Agenda
// Caso de uso asociado: FALTAFALTA
// ------------------------------------------------------------

session_start();
require_once '../gtr/GTR-01_GestionarUsuario.php';
require_once '../gtr/GTR-04_Validar.php';

$usuario = Validar::obtenerUsuarioActual();

//$transaccionesProgramadas = // aquí llamarías a tu función que obtiene las transacciones de la BD

//var_dump($usuario)

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
                <a class="opcion-menu activa" href="UI-10_VisualizarAgenda.php">
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
                <div class="contenedor-busqueda">
                    <input type="text" class="input-busqueda" placeholder="Buscar">
                    <i class="icono-busqueda">🔍</i>
                </div>
                
                <div class="filtros-tabs">
                    <button class="tab-filtro activo">Todos</button>
                    <button class="tab-filtro">Ingresos</button>
                    <button class="tab-filtro">Egresos</button>
                </div>
            </div>

            <!-- Proyección esperada -->
            <div class="seccion-proyeccion">
                <h3 class="titulo-seccion">Proyección esperada - resto del año</h3>
                
                <div class="contenedor-proyeccion">
                    <div class="item-proyeccion">
                        <button class="btn-proyeccion btn-ingresos">Ingresos esperados</button>
                        <span class="valor-proyeccion">S/2700.00</span>
                    </div>
                    
                    <div class="item-proyeccion">
                        <button class="btn-proyeccion btn-egresos">Egresos Esperados</button>
                        <span class="valor-proyeccion">S/.500</span>
                    </div>
                </div>

                <div class="contenedor-balance">
                    <button class="btn-balance">Balance esperado</button>
                    <span class="valor-balance">S/2200.00</span>
                </div>
            </div>

            <!-- Lista de transacciones programadas -->
             <div class="lista-transacciones">
                <?php if ($transaccionesProgramadas && count($transaccionesProgramadas) > 0): ?>
                    <?php foreach ($transaccionesProgramadas as $transaccion): ?>
                        <?php 
                            // Calcular días restantes
                            $fechaActual = new DateTime();
                            $fechaTransaccion = new DateTime($transaccion->fecha);
                            $diasRestantes = $fechaActual->diff($fechaTransaccion)->days;
                            
                            // Determinar si es urgente (menos de 7 días)
                            $esUrgente = $diasRestantes < 7;
                        ?>
                        
                        <div class="item-transaccion <?php echo $esUrgente ? 'urgente' : ''; ?>">
                            <div class="fecha-transaccion">
                                <i class="icono-calendario">📅</i>
                                <span class="fecha"><?php echo date('d \d\e F', strtotime($transaccion->fecha)); ?></span>
                                <span class="dias-restantes">(<?php echo $diasRestantes; ?> días restantes)</span>
                            </div>
                            
                            <div class="detalle-transaccion">
                                <span class="icono-tipo">💰</span>
                                <span class="tipo-transaccion"><?php echo htmlspecialchars($transaccion->tipo); ?></span>
                                <span class="separador">-</span>
                                <span class="categoria"><?php echo htmlspecialchars($transaccion->categoria); ?></span>
                                <span class="separador">-</span>
                                <span class="concepto"><?php echo htmlspecialchars($transaccion->concepto); ?></span>
                            </div>
                            
                            <span class="monto">S/<?php echo number_format($transaccion->monto, 2); ?></span>
                            
                            <div class="estado-transaccion">
                                <?php if ($esUrgente): ?>
                                    <i class="icono-alerta">⚠️</i>
                                    <span class="texto-estado">Menos de 7 días</span>
                                <?php else: ?>
                                    <i class="icono-reloj">🕐</i>
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