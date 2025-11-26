<?php

// ------------------------------------------------------------
// UI-04: Registro Diario
// Caso de uso asociado: CU-03 Visualizar registro diario
// ------------------------------------------------------------

session_start();
require_once '../gtr/GTR-04_Validar.php';
require_once '../gtr/GTR-07_GestionarTransaccion.php';
require_once '../gtr/GTR-08_GestionarRegistroDiario.php';

$usuario = Validar::obtenerUsuarioActual();

$fecha_hoy = date('Y-m-d');
$fecha_hace_7_dias = date('Y-m-d', strtotime('-7 days'));

$diaActual = date('l');
$modo = isset($_GET['modo']) ? $_GET['modo'] : 'familiar';



//$diaActual = "Sunday";
//$fecha_hoy = "2025-11-20";
//var_dump($fecha_hoy);



//<!-- Paso 4-8 del CU-03: Se relacionan los datos para la transaccion -->
//<!-- Paso 9 del CU-03: EL GTR-08 Calcula los ingresos y egresos para hallar el balance -->
if ($modo == 'familiar') {
    $datosRelacionados = GestionarRegistroDiario::vistaFamiliarRegistroDiario($usuario->idFamilia, $fecha_hoy);
    $ingresos = GestionarTransaccion::obtenerIngresoBD($usuario->idFamilia, $fecha_hoy, $fecha_hoy);
    $egresos = GestionarTransaccion::obtenerEgresoBD($usuario->idFamilia, $fecha_hoy, $fecha_hoy);

    $ingresos_7Dias = GestionarTransaccion::obtenerIngresoBD($usuario->idFamilia, $fecha_hace_7_dias, $fecha_hoy);
    $egresos_7Dias = GestionarTransaccion::obtenerEgresoBD($usuario->idFamilia, $fecha_hace_7_dias, $fecha_hoy);
} else {
    $datosRelacionados = GestionarRegistroDiario::vistaUsuarioRegistroDiario($usuario->idFamilia, $fecha_hoy, $usuario->idUsuario);
    $ingresos = GestionarTransaccion::obtenerIngresoPorUsuarioBD($usuario->idUsuario, $fecha_hoy, $fecha_hoy);
    $egresos = GestionarTransaccion::obtenerEgresoPorUsuarioBD($usuario->idUsuario, $fecha_hoy, $fecha_hoy);

    $ingresos_7Dias = GestionarTransaccion::obtenerIngresoPorUsuarioBD($usuario->idUsuario, $fecha_hace_7_dias, $fecha_hoy);
    $egresos_7Dias = GestionarTransaccion::obtenerEgresoPorUsuarioBD($usuario->idUsuario, $fecha_hace_7_dias, $fecha_hoy);
}


$balanceCalculado = $ingresos - $egresos;
$balanceUltimos7Dias = $ingresos_7Dias - $egresos_7Dias;
?>


<!-- Paso 10 del CU-03: La UI-04 Muestra los campos pertinentes -->
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
            <!-- Paso 1 del CU-03: La interfaz se carga y presenta el switch -->

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

            <!-- Paso 2 del CU-03: Muestra ingresos y egresos -->
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
                        //<!-- Paso 3 del CU-03: La UI presenta la opcion para agregar nuevos conceptos y editar conceptos existentes -->
                        foreach ($datosRelacionados as $dato) {
                            if ($dato['tipo'] === 'Ingreso') {

                                $puedeEditar = (
                                    $dato['usuario_id'] == $usuario->idUsuario
                                    || $usuario->rol === "Administrador familiar"
                                );

                                echo "<tr class='fila-tabla'>
                                        <td class='celda'>{$dato['concepto']}</td>
                                        <td class='celda'>{$dato['categoria']}</td>
                                        <td class='celda'>S/. {$dato['monto']}</td>
                                        <td class='celda'>{$dato['usuario']}</td>
                                        <td class='celda derecha'>";

                                    ?>
                                        <form action="UI-18_EditarConcepto.php" method="GET">
                                            <input type="hidden" name="id" value="<?= $dato['idConcepto'] ?>">
                                            <button type="submit" class="link-editar" <?= !$puedeEditar ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : '' ?>>
                                            
                                                Editar
                                            </button>
                                        </form>
                                    <?php

                                echo "   </td>
                                    </tr>";
                            }
                        }
                        ?>

                        </tbody>
                        <tfoot>
                        <tr class="fila-total">
                            <td class="celda-total">Total</td>
                            <td class="celda-total" colspan="3">S/. <?php echo number_format($ingresos, 2); ?></td>
                        </tr>
                        </tfoot>
                    </table>
                    
                    <!-- Paso 3 del CU-03: La UI presenta la opcion para agregar nuevos conceptos y editar conceptos existentes -->
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
                        //<!-- Paso 3 del CU-03: La UI presenta la opcion para agregar nuevos conceptos y editar conceptos existentes -->
                        foreach ($datosRelacionados as $dato) {
                            if ($dato['tipo'] === 'Egreso') {

                                $puedeEditar = (
                                    $dato['usuario_id'] == $usuario->idUsuario
                                    || $usuario->rol === "Administrador familiar"
                                );

                                echo "<tr class='fila-tabla'>
                                        <td class='celda'>{$dato['concepto']}</td>
                                        <td class='celda'>{$dato['categoria']}</td>
                                        <td class='celda'>S/. {$dato['monto']}</td>
                                        <td class='celda'>{$dato['usuario']}</td>
                                        <td class='celda derecha'>";

                                    ?>
                                        <form action="UI-18_EditarConcepto.php" method="GET">
                                            <input type="hidden" name="id" value="<?= $dato['idConcepto'] ?>">
                                            <button type="submit" class="link-editar" <?= !$puedeEditar ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : '' ?>>
                                            
                                                Editar
                                            </button>
                                        </form>
                                    <?php

                                echo "   </td>
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

                    <!-- Paso 3 del CU-03: La UI presenta la opcion para agregar nuevos conceptos y editar conceptos existentes -->
                    <form action="UI-17_CrearConcepto.php" method="GET">
                        <button type="submit" class="boton-mas">+</button>
                    </form>
                </article>
            </section>

            <!-- Parte de abajo -->
            <footer class="seccion-inferior">
                
                <?php
                    // Color para el balance semanal
                    $colorSemanal = ($balanceUltimos7Dias >= 0) ? "color: #00ff5a;" : "color: #ff4d4d;";

                    // Color para el balance diario
                    $colorDiario = ($balanceCalculado >= 0) ? "color: #00ff5a;" : "color: #ff4d4d;";
                ?>

                <!-- Verificar si es domingo y mostrar la caja de Corte Semanal -->
                <?php if ($diaActual == 'Sunday'): ?>
                    <article class="caja-resumen">
                        <h4 class="titulo-resumen" style="font-weight: bold;">Corte Semanal</h4>
                        <div class="linea-resumen">
                            <span class="texto-resumen" style = "font-weight: bold; color: white;">Semanal</span>
                            <span class="valor-resumen" style="<?php echo $colorDiario; ?>">S/. <?php echo number_format($balanceUltimos7Dias, 2); ?></span>
                        </div>
                    </article>
                <?php else: ?>
                    <!-- Mostrar artículo vacío si no es domingo -->
                    <article>
                        <!-- No contenido aquí, solo un artículo vacío -->
                    </article>
                <?php endif; ?>

                <!-- Caja de resumen -->
                <aside class="caja-resumen" style="background-color: #3862AA;">
                    <h4 class="titulo-resumen" style="font-weight: bold;">Resumen del Balance</h4>
                    <div class="linea-resumen">
                        <span class="texto-resumen" style = "font-weight: bold; color: white;">Diario</span>
                        <span class="valor-resumen" style="<?php echo $colorSemanal; ?>">S/. <?php echo number_format($balanceCalculado, 2); ?></span>
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

