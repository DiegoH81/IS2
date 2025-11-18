<!-- UI inactiva -->

<?php

// ------------------------------------------------------------
// UI-04: Registro Diario
// Caso de uso asociado: FALTAFALTA
// ------------------------------------------------------------

session_start();
require_once '../gtr/GTR-01_GestionarUsuario.php';
require_once '../gtr/GTR-04_Validar.php';
require_once '../gtr/GTR-07_GestionarTransaccion.php';
require_once '../gtr/GTR-08_GestionarRegistroDiario.php';

$usuario = Validar::obtenerUsuarioActual();

// Obtener la fecha de hoy (como fecha de inicio y fin)
$fecha_hoy = date('Y-m-d');  // Obtiene la fecha de hoy en formato YYYY-MM-DD
$fecha_hace_7_dias = date('Y-m-d', strtotime('-7 days'));  // Fecha de hace 7 días

$diaActual = date('l');
$modo = isset($_GET['modo']) ? $_GET['modo'] : 'familiar';  // Valor predeterminado es 'familiar'
//var_dump($diaActual);
//var_dump($usuario);
//var_dump($fecha_hoy);
//var_dump($usuario->idFamilia);

// Llamar a la función relacionarDatos con las fechas de hoy
if ($modo == 'familiar') {
    // Llamar a la función vistaFamiliar
    $datosRelacionados = GestionarRegistroDiario::vistaFamiliar($usuario->idFamilia, "2025-10-26", "2025-10-26");
    $ingresos = GestionarTransaccion::obtenerIngresoBD($usuario->idFamilia, "2025-10-26", "2025-10-26");
    $egresos = GestionarTransaccion::obtenerEgresoBD($usuario->idFamilia, "2025-10-26", "2025-10-26");

    $ingresos_7Dias = GestionarTransaccion::obtenerIngresoBD($usuario->idFamilia, $fecha_hace_7_dias, $fecha_hoy);
    $egresos_7Dias = GestionarTransaccion::obtenerEgresoBD($usuario->idFamilia, $fecha_hace_7_dias, $fecha_hoy);
} else {
    // Llamar a la función vistaUsuario
    $datosRelacionados = GestionarRegistroDiario::vistaUsuario($usuario->idFamilia, "2025-10-26", "2025-10-26", $usuario->idUsuario);
    $ingresos = GestionarTransaccion::obtenerIngresoPorUsuarioBD($usuario->idUsuario, "2025-10-26", "2025-10-26");
    $egresos = GestionarTransaccion::obtenerEgresoPorUsuarioBD($usuario->idUsuario, "2025-10-26", "2025-10-26");

    $ingresos_7Dias = GestionarTransaccion::obtenerIngresoBD($usuario->idUsuario, $fecha_hace_7_dias, $fecha_hoy);
    $egresos_7Dias = GestionarTransaccion::obtenerEgresoBD($usuario->idUsuario, $fecha_hace_7_dias, $fecha_hoy);
}

//$datosRelacionados = GestionarRegistroDiario::relacionarDatos($usuario->idFamilia, "2025-10-26", "2025-10-26");
$balanceCalculado = $ingresos - $egresos;
$balanceUltimos7Dias = $ingresos_7Dias - $egresos_7Dias;

//var_dump($datosRelacionados);
//var_dump($balanceCalculado);


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Diario</title>

    
    <!-- CSS principal -->
    <link rel="stylesheet" href="../css/daily_input.css">
     <link rel="stylesheet" href="../css/principal.css">
    <link rel="stylesheet" href="../css/configuracion.css">
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
            <h2 class="subtitulo">Registro Diario</h2>

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
                <a class="opcion-menu activa" href="UI-04_RegistroDiario.php">
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
                        <span class="texto-switch">PERSONAL / FAMILIAR</span>
                        <label class="boton-switch">
                            <input type="checkbox" id="switchFamilia" name="modo" <?php echo ($modo == 'familiar') ? 'checked' : ''; ?>>
                            <span class="deslizador"></span>
                        </label>
                    </div>                 
                </div>
            </section>

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
                <?php if ($diaActual == 'Sunday'): ?>
                    <article class="caja-resumen">
                        <h4 class="titulo-resumen">Corte Semanal</h4>
                        <div class="linea-resumen">
                            <span class="texto-resumen">Semanal</span>
                            <span class="valor-resumen">S/. <?php echo number_format($balanceUltimos7Dias, 2); ?></span>
                        </div>
                    </article>
                <?php else: ?>
                    <!-- Mostrar artículo vacío si no es domingo -->
                    <article>
                        <!-- No contenido aquí, solo un artículo vacío -->
                    </article>
                <?php endif; ?>

                <!-- Caja de resumen -->
                <aside class="caja-resumen">
                    <h4 class="titulo-resumen">Resumen del Balance</h4>
                    <div class="linea-resumen">
                        <span class="texto-resumen">Diario</span>
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
        // Obtener el valor del switch
        const switchBtn = document.querySelector('#switchFamilia');

        if (switchBtn) {
            switchBtn.addEventListener('change', function() {
                // Cuando se cambia el estado del switch, lo redirigimos a la página con el parámetro
                let modo = this.checked ? 'familiar' : 'personal'; // Si está checkeado es 'familiar', sino 'personal'
                
                // Redirigir con el parámetro de modo en la URL
                window.location.href = `UI-04_RegistroDiario.php?modo=${modo}`;
            });
        }
    });



</script>

</body>
</html>

