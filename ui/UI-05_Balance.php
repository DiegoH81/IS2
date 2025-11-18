<!-- UI inactiva -->

<?php

// ------------------------------------------------------------
// UI-05: Balance
// Caso de uso asociado: FALTAFALTA
// ------------------------------------------------------------

session_start();
require_once '../gtr/GTR-01_GestionarUsuario.php';
require_once '../gtr/GTR-04_Validar.php';
require_once '../gtr/GTR-07_GestionarTransaccion.php';
require_once '../gtr/GTR-03_GestionarBalance.php';

$usuario = Validar::obtenerUsuarioActual();

// Obtener la fecha de hoy (como fecha de inicio y fin)
$fecha_hoy = date('Y-m-d');
$fecha_inicio = isset($_GET['fecha_inicio']) && !empty($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : $fecha_hoy;
$fecha_fin = isset($_GET['fecha_fin']) && !empty($_GET['fecha_fin']) ? $_GET['fecha_fin'] : $fecha_hoy;

// Asegúrate de que las fechas sean válidas
if (!$fecha_inicio || !$fecha_fin) {
    $fecha_inicio = $fecha_hoy;
    $fecha_fin = $fecha_hoy;
}

$modo = isset($_GET['modo']) ? $_GET['modo'] : 'familiar';  // Valor predeterminado es 'familiar'
//var_dump($diaActual);
//var_dump($usuario);
//var_dump($fecha_hoy);
//var_dump($usuario->idFamilia);

// Llamar a la función relacionarDatos con las fechas de hoy
if ($modo == 'familiar') {
    $datosRelacionados = GestionarBalance::vistaFamiliar($usuario->idFamilia, $fecha_inicio, $fecha_fin);
    $ingresos = GestionarTransaccion::obtenerIngresoBD($usuario->idFamilia, $fecha_inicio, $fecha_fin);
    $egresos = GestionarTransaccion::obtenerEgresoBD($usuario->idFamilia, $fecha_inicio, $fecha_fin);
} else {
    $datosRelacionados = GestionarBalance::vistaUsuario($usuario->idFamilia, $fecha_inicio, $fecha_fin, $usuario->idUsuario);
    $ingresos = GestionarTransaccion::obtenerIngresoPorUsuarioBD($usuario->idUsuario, $fecha_inicio, $fecha_fin);
    $egresos = GestionarTransaccion::obtenerEgresoPorUsuarioBD($usuario->idUsuario, $fecha_inicio, $fecha_fin);
}

//$datosRelacionados = GestionarBalance::relacionarDatos($usuario->idFamilia, "2025-10-26", "2025-10-26");
$balanceCalculado = $ingresos - $egresos;

//var_dump($datosRelacionados);
//var_dump($balanceCalculado);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking</title>

    
    <!-- CSS principal -->
    <link rel="stylesheet" href="../css/daily_input.css">
     <link rel="stylesheet" href="../css/principal.css">
    <link rel="stylesheet" href="../css/configuracion.css">
    <!-- CSS de íconos -->
    <link rel="stylesheet" href="../css/icons.css">

    <script src="../js/filtro_semanal.js"></script>

</head>
<body>
<div class="contenedor-principal">

    <!-- Cabecera -->
    <header class="barra-superior">
        <section class="seccion-izquierda">
            <h1 class="titulo-app">On a budget</h1>
        </section>

        <section class="seccion-derecha">
            <h2 class="subtitulo">Balance</h2>

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
        <!-- Menu lateral - ACTUALIZADO -->
        <aside class="menu-lateral" id="menuLateral">
            <nav>
                <a class="opcion-menu" href="UI-04_RegistroDiario.php">
                    <i class="icono icono-documento"></i>Registro Diario
                </a>
                <a class="opcion-menu activa" href="UI-05_Balance.php">
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

            <!-- Controles de arriba -->
            <section class="controles-superiores">
                <!-- Toggle y calendario -->
                <div class="grupo-controles">
                    <!-- Switch familiar/personal -->
                    <div class="contenedor-switch">
                        <span class="texto-switch">FAMILIAR / PERSONAL</span>
                        <label class="boton-switch">
                            <input type="checkbox" checked>
                            <span class="deslizador"></span>
                        </label>
                    </div> 
                        <button class="boton-balance-semanal" id="filtro-semanal">
                            Balance por rango
                        </button>

                        <!-- Contenedor de los calendarios -->
                        <div id="filtro-fechas" style="display: none; margin-top: 10px;">
                            <label for="fecha-inicio">Fecha de inicio:</label>
                            <input type="date" id="fecha-inicio">

                            <label for="fecha-fin">Fecha de fin:</label>
                            <input type="date" id="fecha-fin">

                            <button id="aplicar-fechas" class="boton-crear">Aplicar fechas</button>
                        </div>                   
                </div>
            </section>

            <!-- Las dos tablas -->
            <!-- Las dos tablas -->
            <section class="contenedor-tablas">

                <!-- Tabla Ingresos -->
                <article class="caja-tabla">
                    <header>
                        <h2 class="titulo-tabla">Ingresos</h2>
                        <div class="linea-separadora"></div>
                        <div class="linea-azul"></div>
                    </header>

                    <table class="tabla-datos">
                        <thead>
                        <tr>
                            <th class="encabezado-tabla">Concepto</th>
                            <th class="encabezado-tabla">Categoría</th>
                            <th class="encabezado-tabla">Costo</th>
                            <th class="encabezado-tabla">Subido por</th>
                            <th class="encabezado-tabla derecha">Acción</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        // Filtrar ingresos
                        foreach ($datosRelacionados as $dato) {
                            if ($dato['tipo'] === 'Ingreso') {

                                $puedeEditar = ($dato['usuario_id'] == $usuario->idUsuario);

                                echo "<tr class='fila-tabla'>
                                        <td class='celda'>{$dato['concepto']}</td>
                                        <td class='celda'>{$dato['categoria']}</td>
                                        <td class='celda'>S/. {$dato['monto']}</td>
                                        <td class='celda'>{$dato['usuario']}</td>
                                        <td class='celda derecha'>
                                            <a href='UI-18_EditarConcepto.php?id={$dato['idConcepto']}' class='link-editar' " . (!$puedeEditar ? 'style="opacity:0.5;cursor:not-allowed;"' : '') . ">
                                                Editar
                                            </a>
                                        </td>
                                    </tr>";
                            }
                        }
                        ?>
                        <tr class="fila-vacia">
                            <td class="celda" colspan="4">&nbsp;</td>
                        </tr>

                        </tbody>
                        <tfoot>
                        <tr class="fila-total">
                            <td class="celda-total">Total</td>
                            <td class="celda-total" colspan="3">S/. <?php echo number_format($ingresos, 2); ?></td>
                        </tr>
                        </tfoot>
                    </table>

                    <!-- Boton mas -->
                     <form action="UI-17_CrearConcepto.php" method="GET">
                        <button type="submit" class="boton-mas">+</button>
                    </form>
                </article>

                <!-- Tabla Egresos -->
                <article class="caja-tabla">
                    <header>
                        <h2 class="titulo-tabla">Egresos</h2>
                        <div class="linea-separadora"></div>
                        <div class="linea-azul"></div>
                    </header>

                    <table class="tabla-datos">
                        <thead>
                        <tr>
                            <th class="encabezado-tabla">Concepto</th>
                            <th class="encabezado-tabla">Categoría</th>
                            <th class="encabezado-tabla">Costo</th>
                            <th class="encabezado-tabla">Subido por</th>
                            <th class="encabezado-tabla derecha">Acción</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        // Filtrar egresos
                        foreach ($datosRelacionados as $dato) {
                            if ($dato['tipo'] === 'Egreso') {
                                $puedeEditar = ($dato['usuario_id'] == $usuario->idUsuario);
                                echo "<tr class='fila-tabla'>
                                        <td class='celda'>{$dato['concepto']}</td>
                                        <td class='celda'>{$dato['categoria']}</td>
                                        <td class='celda'>S/. {$dato['monto']}</td>
                                        <td class='celda'>{$dato['usuario']}</td>
                                        <td class='celda derecha'>
                                            <a href='UI-18_EditarConcepto.php?id={$dato['idConcepto']}' class='link-editar' " . (!$puedeEditar ? 'style="opacity:0.5;cursor:not-allowed;"' : '') . ">
                                                Editar
                                            </a>
                                        </td>
                                    </tr>";
                            }
                        }
                        ?>

                        </tbody>
                        <tfoot>
                        <tr class="fila-total">
                            <td class="celda-total">Total</td>
                            <td class="celda-total" colspan="3">S/. <?php echo number_format($egresos, 2); ?></td>
                        </tr>
                        </tfoot>
                    </table>

                    <!-- Boton mas -->
                    <form action="UI-17_CrearConcepto.php" method="GET">
                        <button type="submit" class="boton-mas">+</button>
                    </form>
                </article>
            </section>

            <!-- Parte de abajo -->
            <footer class="seccion-inferior">

                <!-- Verificar si es domingo y mostrar la caja de Corte Semanal -->
                
                <!-- Mostrar artículo vacío si no es domingo -->
                <article>
                    <!-- No contenido aquí, solo un artículo vacío -->
                </article>

                <!-- Caja de resumen -->
                <aside class="caja-resumen">
                    <h4 class="titulo-resumen">Resumen del Balance</h4>
                    <div class="linea-resumen">
                        <span class="texto-resumen">Rango</span>
                        <span class="valor-resumen">S/. <?php echo number_format($balanceCalculado, 2); ?></span>
                    </div>
                </aside>
            </footer>

        </main>
    </div>
</div>

<!-- JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Obtener el switch
        const switchBtn = document.querySelector('.boton-switch input');

        if (switchBtn) {
            // Verificar el valor actual de "modo" y marcar el switch correctamente
            const urlParams = new URLSearchParams(window.location.search);
            const modoActual = urlParams.get('modo') || 'familiar'; // 'familiar' es el valor por defecto
            switchBtn.checked = (modoActual === 'familiar');

            // Detectar cuando el estado del switch cambia
            switchBtn.addEventListener('change', function() {
                const nuevoModo = this.checked ? 'familiar' : 'personal'; // Determinar el nuevo modo
                console.log('Modo:', nuevoModo);

                // Redirigir con el parámetro de modo correspondiente en la URL
                window.location.href = `UI-05_Balance.php?modo=${nuevoModo}&fecha_inicio=${urlParams.get('fecha_inicio') || ''}&fecha_fin=${urlParams.get('fecha_fin') || ''}`;
            });
        }
    });
</script>

</body>
</html>

