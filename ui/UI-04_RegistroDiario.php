<!-- UI inactiva -->

<?php

// ------------------------------------------------------------
// UI-04: Registro Diario
// Caso de uso asociado: FALTAFALTA
// ------------------------------------------------------------

session_start();
require_once '../gtr/GTR-01_GestionarUsuario.php';
require_once '../gtr/GTR-04_Validar.php';

$usuario = Validar::obtenerUsuarioActual();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deshabilitar']))
{
    GestionarUsuario::cambiarEstadoUsuarioBD($usuario->idUsuario, 0);
    //var_dump($usuario->idUsuario);
    header("Location: UI-01_InicioDeSesion.php");
}
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
                <a class="opcion-menu" href="#">
                    <i class="icono icono-grafico"></i>Agenda
                </a>
                <a class="opcion-menu" href="#">
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
                            <th class="encabezado-tabla">Costo</th>
                            <th class="encabezado-tabla">Subido por</th>
                            <th class="encabezado-tabla derecha">Acción</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="fila-tabla">
                            <td class="celda">Otros - Pago deuda</td>
                            <td class="celda">S/. 50.00</td>
                            <td class="celda">Pepe Grillo</td>
                            <td class="celda derecha">
                                <span class="link-editar">Editar</span>
                            </td>
                        </tr>
                        <tr class="fila-tabla">
                            <td class="celda">Otros - Carreras</td>
                            <td class="celda">S/. 25.00</td>
                            <td class="celda">Pepe Grillo</td>
                            <td class="celda derecha">
                                <span class="link-editar">Editar</span>
                            </td>
                        </tr>
                        <tr class="fila-vacia">
                            <td class="celda" colspan="4">&nbsp;</td>
                        </tr>
                        <tr class="fila-vacia">
                            <td class="celda" colspan="4">&nbsp;</td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr class="fila-total">
                            <td class="celda-total">Total</td>
                            <td class="celda-total" colspan="3">S/. 75.00</td>
                        </tr>
                        </tfoot>
                    </table>

                    <!-- Boton mas -->
                    <button class="boton-mas">+</button>
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
                            <th class="encabezado-tabla">Costo</th>
                            <th class="encabezado-tabla">Subido por</th>
                            <th class="encabezado-tabla derecha">Acción</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="fila-tabla">
                            <td class="celda">Movilidad - Taxi</td>
                            <td class="celda">S/. 10.00</td>
                            <td class="celda">Pepe Grillo</td>
                            <td class="celda derecha">
                                <span class="link-editar">Editar</span>
                            </td>
                        </tr>
                        <tr class="fila-tabla">
                            <td class="celda">Movilidad - Omnibus</td>
                            <td class="celda">S/. 6.00</td>
                            <td class="celda">Pepe Grillo</td>
                            <td class="celda derecha">
                                <span class="link-editar">Editar</span>
                            </td>
                        </tr>
                        <tr class="fila-tabla">
                            <td class="celda">Comida - Almuerzo</td>
                            <td class="celda">S/. 24.00</td>
                            <td class="celda">Pepe Grillo</td>
                            <td class="celda derecha">
                                <span class="link-editar">Editar</span>
                            </td>
                        </tr>
                        <tr class="fila-tabla">
                            <td class="celda">Compras - Tienda</td>
                            <td class="celda">S/. 3.00</td>
                            <td class="celda">Pepe Grillo</td>
                            <td class="celda derecha">
                                <span class="link-editar">Editar</span>
                            </td>
                        </tr>

                        <tr class="fila-tabla">
                            <td class="celda">Movilidad - Taxi</td>
                            <td class="celda">S/. 10.00</td>
                            <td class="celda">Pepe Grillo</td>
                            <td class="celda derecha">
                                <span class="link-editar">Editar</span>
                            </td>
                        </tr>
                        <tr class="fila-tabla">
                            <td class="celda">Movilidad - Taxi</td>
                            <td class="celda">S/. 10.00</td>
                            <td class="celda">Pepe Grillo</td>
                            <td class="celda derecha">
                                <span class="link-editar">Editar</span>
                            </td>
                        </tr>
                        <tr class="fila-tabla">
                            <td class="celda">Movilidad - Taxi</td>
                            <td class="celda">S/. 10.00</td>
                            <td class="celda">Pepe Grillo</td>
                            <td class="celda derecha">
                                <span class="link-editar">Editar</span>
                            </td>
                        </tr>
                        <tr class="fila-tabla">
                            <td class="celda">Movilidad - Taxi</td>
                            <td class="celda">S/. 10.00</td>
                            <td class="celda">Pepe Grillo</td>
                            <td class="celda derecha">
                                <span class="link-editar">Editar</span>
                            </td>
                        </tr>

                        </tbody>
                        <tfoot>
                        <tr class="fila-total">
                            <td class="celda-total">Total</td>
                            <td class="celda-total" colspan="3">S/. 83.00</td>
                        </tr>
                        </tfoot>
                    </table>

                    <!-- Boton mas -->
                    <button class="boton-mas">+</button>
                </article>
            </section>

            <!-- Parte de abajo -->
            <footer class="seccion-inferior">
                <!-- Boton de balance semanal -->
                <button class="boton-balance-semanal">
                    Corte semanal
                </button>

                <!-- Caja de resumen -->
                <aside class="caja-resumen">
                    <h4 class="titulo-resumen">Resumen del Balance</h4>
                    <div class="linea-resumen">
                        <span class="texto-resumen">Diario</span>
                        <span class="valor-resumen">S/. -12.00</span>
                    </div>
                    <div class="linea-resumen">
                        <span class="texto-resumen">Mensual</span>
                        <span class="valor-resumen">S/. 350.00</span>
                    </div>
                </aside>
            </footer>

        </main>
    </div>
</div>

<!-- JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Switch on/off
        const switchBtn = document.querySelector('.boton-switch input');
        if (switchBtn) {
            switchBtn.addEventListener('change', function() {
                console.log('Modo:', this.checked ? 'Personal' : 'Familiar');
            });
        }
    });
</script>

</body>
</html>

