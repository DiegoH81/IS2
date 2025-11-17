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
                <a class="opcion-menu activa" href="#">
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
        <!-- Barra de búsqueda -->
            <div class="busqueda">
                <input type="text" placeholder="Buscar...">
            </div>

            <!-- Proyección esperada -->
            <section class="proyecciones">
                <div class="proyeccion-card">
                    <h4>Ingresos esperados</h4>
                    <p>S/. 2700.00</p>
                </div>
                <div class="proyeccion-card">
                    <h4>Egresos Esperados</h4>
                    <p>S/. 500.00</p>
                </div>
                <div class="proyeccion-card">
                    <h4>Balance esperado</h4>
                    <p>S/. 2200.00</p>
                </div>
            </section>

            <!-- Filtros de selección (Todos/Ingesos/Egresos) -->
            <section class="filtros">
                <button>Todos</button>
                <button>Ingresos</button>
                <button>Egresos</button>
            </section>

            <!-- Lista de eventos -->
            <section class="lista-eventos">
                <div class="evento-card">
                    <div class="evento-info">
                        <p>20 de septiembre (3 días restantes)</p>
                        <p><strong>Ingreso:</strong> Negocio - Cevichería</p>
                        <p>S/. 1750.00</p>
                    </div>
                    <div class="evento-iconos">
                        <span>⚠️</span><p>Menos de 7 días</p>
                    </div>
                </div>

                <div class="evento-card">
                    <div class="evento-info">
                        <p>5 de octubre (15 días restantes)</p>
                        <p><strong>Egreso:</strong> Servicios Básicos - Luz</p>
                        <p>S/. 200.00</p>
                    </div>
                    <div class="evento-iconos">
                        <span>⏳</span><p>Próximo</p>
                    </div>
                </div>

                <div class="evento-card">
                    <div class="evento-info">
                        <p>15 de octubre (25 días restantes)</p>
                        <p><strong>Egreso:</strong> Deudas - Cuota Préstamo</p>
                        <p>S/. 100.00</p>
                    </div>
                    <div class="evento-iconos">
                        <span>⏳</span><p>Próximo</p>
                    </div>
                </div>

                <div class="evento-card">
                    <div class="evento-info">
                        <p>25 de octubre (35 días restantes)</p>
                        <p><strong>Ingreso:</strong> Rentas - Departamento A</p>
                        <p>S/. 250.00</p>
                    </div>
                    <div class="evento-iconos">
                        <span>⏳</span><p>Próximo</p>
                    </div>
                </div>
            </section><!-- Barra de búsqueda -->
            <div class="busqueda">
                <input type="text" placeholder="Buscar...">
            </div>

            <!-- Proyección esperada -->
            <section class="proyecciones">
                <div class="proyeccion-card">
                    <h4>Ingresos esperados</h4>
                    <p>S/. 2700.00</p>
                </div>
                <div class="proyeccion-card">
                    <h4>Egresos Esperados</h4>
                    <p>S/. 500.00</p>
                </div>
                <div class="proyeccion-card">
                    <h4>Balance esperado</h4>
                    <p>S/. 2200.00</p>
                </div>
            </section>

            <!-- Filtros de selección (Todos/Ingesos/Egresos) -->
            <section class="filtros">
                <button>Todos</button>
                <button>Ingresos</button>
                <button>Egresos</button>
            </section>

            <!-- Lista de eventos -->
            <section class="lista-eventos">
                <div class="evento-card">
                    <div class="evento-info">
                        <p>20 de septiembre (3 días restantes)</p>
                        <p><strong>Ingreso:</strong> Negocio - Cevichería</p>
                        <p>S/. 1750.00</p>
                    </div>
                    <div class="evento-iconos">
                        <span>⚠️</span><p>Menos de 7 días</p>
                    </div>
                </div>

                <div class="evento-card">
                    <div class="evento-info">
                        <p>5 de octubre (15 días restantes)</p>
                        <p><strong>Egreso:</strong> Servicios Básicos - Luz</p>
                        <p>S/. 200.00</p>
                    </div>
                    <div class="evento-iconos">
                        <span>⏳</span><p>Próximo</p>
                    </div>
                </div>

                <div class="evento-card">
                    <div class="evento-info">
                        <p>15 de octubre (25 días restantes)</p>
                        <p><strong>Egreso:</strong> Deudas - Cuota Préstamo</p>
                        <p>S/. 100.00</p>
                    </div>
                    <div class="evento-iconos">
                        <span>⏳</span><p>Próximo</p>
                    </div>
                </div>

                <div class="evento-card">
                    <div class="evento-info">
                        <p>25 de octubre (35 días restantes)</p>
                        <p><strong>Ingreso:</strong> Rentas - Departamento A</p>
                        <p>S/. 250.00</p>
                    </div>
                    <div class="evento-iconos">
                        <span>⏳</span><p>Próximo</p>
                    </div>
                </div>
            </section>
            

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

