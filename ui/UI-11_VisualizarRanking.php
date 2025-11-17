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
    //var_dump($usuario->idUsuario);
    header("Location: UI-01_InicioDeSesion.php");
}
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
                <a class="opcion-menu" href="UI-04_RegistroDiario.php">
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
                <a class="opcion-menu activa" href="#">
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

        <section class="contenedor-tablas">
                <article class="tabla">
                    <!-- Paso 8 del CU-15: Mostrar opción de Crear concepto. -->
                    <header>
                        <div class="encabezado-tabla-superior">
                            <!-- Contenedor para los botones de Egresos/Ingresos -->
                            <div class="grupo-controles">
                                <div class="botones-principales">
                                    <!-- Botones Egresos/Ingresos -->
                                    <button id="egresos-btn" class="boton-crear">Egresos</button>
                                    <button id="ingresos-btn" class="boton-balance">Ingresos</button>
                                </div>

                                <!-- Filtro por rango (Últimas 4 semanas, 6 meses, 12 meses) -->
                                <div class="filtro-rango">
                                    <button class="filtro-btn" id="ultimas-semanas-btn">Últimas 4 semanas</button>
                                    <button class="filtro-btn" id="ultimos-6-meses-btn">Últimos 6 meses</button>
                                    <button class="filtro-btn" id="ultimos-12-meses-btn">Últimos 12 meses</button>
                                </div>
                            </div>
                        </div>

                        <h2 class="titulo-tabla">Configuración usuarios</h2>
                        <div class="linea-separadora"></div>
                        <div class="linea-azul"></div>
                    </header>

                    <table class="tabla-datos">
                        <thead>
                            <tr>
                                <th class="encabezado-tabla">Concepto</th>
                                <th class="encabezado-tabla">Categoría</th>
                                <th class="encabezado-tabla">Costo</th>
                                <th class="encabezado-tabla">Subid por</th>
                            </tr>
                        </thead>
                        <tbody>

                        <!-- -->
                        <!--<?php if ($usuarios && count($usuarios) > 0): ?>-->
                            <!--<?php foreach ($usuarios as $u): ?>-->
                                <tr class="fila-tabla" id="fila-<?= $u->idUsuario ?>">
                                    <td class="celda">
                                        CONCEPTO
                                    </td>
                                    <td class="celda">
                                        CATEGORIA
                                    </td>
                                    <td class="celda">
                                        COSTO
                                    </td>

                                    <td class="celda">
                                        SUBIDO POR
                                    </td>
                                </tr>
                            <!--<?php endforeach; ?>-->
                                
                            <!--
                        <?php else: ?>
                            <tr><td colspan="8" class="celda">No se encontraron usuarios.</td></tr>
                        <?php endif; ?>
                            -->
                        </tbody>
                    </table>
                </article>
            </section>
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

