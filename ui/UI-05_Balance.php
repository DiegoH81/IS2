<?php

// ------------------------------------------------------------
// UI-05: Balance
// Caso de uso asociado: CU-04 Visualizar balance
// ------------------------------------------------------------

session_start();
require_once '../gtr/GTR-04_Validar.php';
require_once '../gtr/GTR-07_GestionarTransaccion.php';
require_once '../gtr/GTR-03_GestionarBalance.php';

$usuario = Validar::obtenerUsuarioActual();
$fecha_hoy = date('Y-m-d');
$primer_dia_mes = date('Y-m-01');

$fecha_inicio = isset($_GET['fecha_inicio']) && !empty($_GET['fecha_inicio'])
                ? $_GET['fecha_inicio']
                : $primer_dia_mes;

$fecha_fin = isset($_GET['fecha_fin']) && !empty($_GET['fecha_fin'])
             ? $_GET['fecha_fin']
             : $fecha_hoy;

if (!$fecha_inicio || !$fecha_fin) {
    $fecha_inicio = $fecha_hoy;
    $fecha_fin = $fecha_hoy;
}

$modo = isset($_GET['modo']) ? $_GET['modo'] : 'familiar';

//<!-- Paso 4-8 del CU-04: Se relacionan los datos para la transaccion -->
//<!-- Paso 9-10 del CU-04: El GTR-03 Solicita al GTR-10 y GTR-01 filtrar las vistas segun usuario o familiar -->
//<!-- Paso 12 del CU-04: El GTR-07 solicita las transacciones del periodo filtrado -->
//<!-- Paso 13 del CU-04: El GTR-07 calcula los totales basados en los conceptos obtenidos en el periodo filtrado -->
if ($modo == 'familiar') {
    $datosRelacionados = GestionarBalance::vistaFamiliarBalance($usuario->idFamilia, $fecha_inicio, $fecha_fin);
    $ingresos = GestionarTransaccion::obtenerIngresoBD($usuario->idFamilia, $fecha_inicio, $fecha_fin);
    $egresos = GestionarTransaccion::obtenerEgresoBD($usuario->idFamilia, $fecha_inicio, $fecha_fin);
} else {
    $datosRelacionados = GestionarBalance::vistaUsuarioBalance($usuario->idFamilia, $fecha_inicio, $fecha_fin, $usuario->idUsuario);

    $ingresos = GestionarTransaccion::obtenerIngresoPorUsuarioBD($usuario->idUsuario, $fecha_inicio, $fecha_fin);
    $egresos = GestionarTransaccion::obtenerEgresoPorUsuarioBD($usuario->idUsuario, $fecha_inicio, $fecha_fin);
}

//var_dump($datosRelacionados);


//$datosRelacionados = GestionarBalance::relacionarDatos($usuario->idFamilia, "2025-10-26", "2025-10-26");
$balanceCalculado = $ingresos - $egresos;

//var_dump($datosRelacionados);
//var_dump($balanceCalculado);
?>

<!-- Paso 11 del CU-04: La UI-05 muestra los campos pertinentes -->
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
    <link rel="stylesheet" href="../css/calendar.css">
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
            <section class="controles-superiores" style="height: 10px;">
                <div class="grupo-controles">

                    <!-- Paso 1 del CU-04: La UI-05 se carga y presenta el switch -->
                    <div class="contenedor-switch">
                        <span class="texto-switch">Personal / Familiar</span>
                        <label class="boton-switch">
                            <input type="checkbox" checked>
                            <span class="deslizador"></span>
                        </label>
                    </div>
                    
                    <!-- Contenedor derecho: botón + calendarios -->
                    <div class="contenedor-filtro-derecha" style="margin-left: auto;">
                        <button class="boton-balance-semanal" id="filtro-semanal">
                            Balance por rango
                        </button>
                        
                        <!-- Paso 12 del CU-04: El GTR-07 solicita las transacciones del periodo filtrado -->
                        <div id="filtro-fechas" style="display: none;">
                            <div class="grupo-fecha">
                                <label for="fecha-inicio">Desde:</label>
                                <input type="date" id="fecha-inicio" value="<?php echo $fecha_inicio; ?>">
                            </div>
                            
                            <span class="separador-vertical"></span>
                            
                            <div class="grupo-fecha">
                                <label for="fecha-fin">Hasta:</label>
                                <input type="date" id="fecha-fin" value="<?php echo $fecha_fin; ?>">
                            </div>
                            
                            <button id="aplicar-fechas" style="background-color: #4A7BA7;">
                                Aplicar fechas
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Las dos tablas -->
            <!-- Las dos tablas -->

            <!-- Paso 2 del CU-04: La UI-05 muestra las secciones de ingresos y egresos -->
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

                    <!-- Paso 3 del CU-04: La UI presenta la opcion para agregar nuevos conceptos y editar conceptos existentes -->
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
                        //<!-- Paso 3 del CU-04: La UI presenta la opcion para agregar nuevos conceptos y editar conceptos existentes -->
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

                </article>
            </section>

            <!-- Parte de abajo -->
            <footer class="seccion-inferior">

                <article>
                </article>

                
                <!-- Paso 14 del CU-04: La UI-05 presenta los totales de ingresos, egresos y balances -->
                <aside class="caja-resumen" style="background-color: #3862AA;">
                    <h4 class="titulo-resumen" style="font-weight: bold;" >Resumen del Balance</h4>
                    <div class="linea-resumen">
                        <span class="texto-resumen" style = "font-weight: bold; color: white;">Rango</span>
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

