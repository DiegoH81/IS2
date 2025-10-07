<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Diario</title>

    <!-- CSS principal -->
    <link rel="stylesheet" href="css/principal.css">
    <link rel="stylesheet" href="css/configuracion.css">
    <!-- CSS de íconos -->
    <link rel="stylesheet" href="css/icons.css">

</head>
<body>

<div class="contenedor-principal">
    <!-- Cabecera -->
    <header class="barra-superior">
        <!-- Parte izquierda oscura con el título -->
        <section class="seccion-izquierda">
            <h1 class="titulo-app">On a budget</h1>
        </section>

        <!-- Parte derecha blanca -->
        <section class="seccion-derecha">
            <h2 class="subtitulo">Configuración</h2>

            <!-- Info Usuario -->
            <div class="info-usuario">
                <span class="nombre-usuario">Pepe Grillo</span>
                <span class="rol-usuario">Papa / Mama</span>
            </div>
        </section>
    </header>

    
    <!-- Contenido principal -->
    <div class="contenedor-medio">
        <!-- Menu lateral - ACTUALIZADO -->
        <aside class="menu-lateral" id="menuLateral">
            <nav>
                <a class="opcion-menu" href="daily_input.php">
                    <i class="icono icono-documento"></i>Registro Diario
                </a>
                <a class="opcion-menu" href="#">
                    <i class="icono icono-grafico"></i>Balance
                </a>
                <a class="opcion-menu" href="#">
                    <i class="icono icono-persona"></i>Cuenta
                </a>
                <a class="opcion-menu" href="#">
                    <i class="icono icono-grafico"></i>Agenda
                </a>
                <a class="opcion-menu" href="#">
                    <i class="icono icono-grafico"></i>Ranking
                </a>
                <a class="opcion-menu activa" href="visualizar_conceptos.php">
                    <i class="icono icono-configuracion"></i>Configuración
                </a>
            </nav>

            <!-- Cerrar sesión abajo -->
            <footer class="parte-abajo">
                <a class="opcion-menu" href="#">
                    <i class="icono icono-salir"></i>Cerrar sesión
                </a>
            </footer>
        </aside>

        

        <!-- Area principal -->
        <main class="contenedor-medio">
            
            <aside class="submenu-configuracion" id="Sub_menuConfig">
                <nav>
                    <a class="opcion-submenu" href="#">
                        <i></i>Usuarios
                    </a>
                    <a class="opcion-submenu activa" href="visualizar_conceptos.php">
                        <i></i>Conceptos
                    </a>
                    <a class="opcion-submenu" href="#">
                        <i></i>Categorías
                    </a>
                </nav>
            </aside>


            <section class="contenedor-tablas">

                
                <article class="tabla">
                    <header>
                        <div class="encabezado-tabla-superior">
                            <a href="crear_concepto.php" class="boton-crear">Crear concepto</a>
                        </div>
                        <h2 class="titulo-tabla">Configuración conceptos</h2>
                        <div class="linea-separadora"></div>
                        <div class="linea-azul"></div>
                    </header>

                    <table class="tabla-datos">
                        <thead>
                        <tr>
                            <th class="encabezado-tabla">Concepto</th>
                            <th class="encabezado-tabla">Categoría</th>
                            <th class="encabezado-tabla">Tipo</th>
                            <th class="encabezado-tabla">Subido por</th>
                            <th class="encabezado-tabla">Costo</th>
                            <th class="encabezado-tabla">Periodo</th>
                            <th class="encabezado-tabla">Estado</th>
                            <th class="encabezado-tabla">Accion</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="fila-tabla">
                            <td class="celda">Pago Deuda</td>
                            <td class="celda">Deuda</td>
                            <td class="celda">Ingreso</td>
                            <td class="celda">Manuel Rammirez</td>
                            <td class="celda">S/. 50.00</td>
                            <td class="celda">Eventual</td>
                            <td class="celda">Habilitado</td>
                            <td class="celda">
                                <a href="editar_concepto.php" class="link-editar">Editar</a>
                            </td>
                        </tr>

                        <tr class="fila-tabla">
                            <td class="celda">Pago Deuda</td>
                            <td class="celda">Deuda</td>
                            <td class="celda">Ingreso</td>
                            <td class="celda">Manuel Rammirez</td>
                            <td class="celda">S/. 50.00</td>
                            <td class="celda">Eventual</td>
                            <td class="celda">Habilitado</td>
                            <td class="celda">
                                <span class="link-editar">Editar</span>
                            </td>
                        </tr>

                        <tr class="fila-tabla">
                            <td class="celda">Pago Deuda</td>
                            <td class="celda">Deuda</td>
                            <td class="celda">Ingreso</td>
                            <td class="celda">Manuel Rammirez</td>
                            <td class="celda">S/. 50.00</td>
                            <td class="celda">Eventual</td>
                            <td class="celda">Habilitado</td>
                            <td class="celda">
                                <span class="link-editar">Editar</span>
                            </td>
                        </tr>

                        <tr class="fila-tabla">
                            <td class="celda">Pago Deuda</td>
                            <td class="celda">Deuda</td>
                            <td class="celda">Ingreso</td>
                            <td class="celda">Manuel Rammirez</td>
                            <td class="celda">S/. 50.00</td>
                            <td class="celda">Eventual</td>
                            <td class="celda">Habilitado</td>
                            <td class="celda">
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
                        </tfoot>
                    </table>

                </article>

                
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

