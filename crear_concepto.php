<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear concepto</title>

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
                        <h2 class="titulo-tabla">Crear concepto</h2>
                        <div class="linea-separadora"></div>
                        <div class="linea-azul"></div>
                    </header>
                    <form class="form-crear-concepto" action="guardar_concepto.php" method="POST">
                        <!-- Categoría -->
                        <div class="campo-formulario">
                            <label for="categoria">Categoría:</label>
                            <select id="categoria" name="categoria" required>
                                <option value="">Seleccionar categoría</option>
                                <option value="Deuda">Deuda</option>
                                <option value="Ingreso">Ingreso</option>
                                <option value="Gasto">Gasto</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>

                        <!-- Nombre -->
                        <div class="campo-formulario">
                            <label for="nombre">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" placeholder="Ingrese nombre" required>
                        </div>

                        <!-- Tipo -->
                         
                        <div class="campo-formulario">
                            <label>Tipo:</label>
                            <div class="opciones-radio">
                                <label><input type="radio" name="tipo" value="Ingreso" required> Ingreso</label>
                                <label><input type="radio" name="tipo" value="Egreso" required> Egreso</label>
                            </div>
                        </div>

                        <!-- Monto -->
                        <div class="campo-formulario">
                            <label for="monto">Monto:</label>
                            <input type="number" id="monto" name="monto" step="0.01" placeholder="S/. 0.00" required>
                        </div>

                        <!-- Periodo -->
                        <div class="campo-formulario">
                            <label>Periodo:</label>
                            <div class="opciones-radio columna-vertical">
                                <label><input type="radio" name="periodo" value="Diario" required> Diario</label><br>
                                <label><input type="radio" name="periodo" value="Semanal"> Semanal</label><br>
                                <label><input type="radio" name="periodo" value="Quincenal"> Quincenal</label><br>
                                <label><input type="radio" name="periodo" value="Personalizado"> Personalizado</label><br>
                                <label><input type="radio" name="periodo" value="Eventual"> Eventual</label>
                            </div>
                        </div>

                        <!-- Campo adicional para Personalizado -->
                        <div class="periodicidad-personalizada" style="display:none; margin-top:10px;">
                            <label>Periodicidad:</label>
                            <input type="number" name="periodicidad" placeholder="Ingrese número">
                        </div>

                        <!-- Fechas -->
                        <div class="campo-formulario">
                            <label>Día de inicio / Día de fin:</label>
                            <div class="fechas">
                                <input type="date" name="fecha_inicio" required>
                                <input type="date" name="fecha_fin">
                            </div>
                        </div>

                        <!-- Botón -->
                        <div class="campo-formulario">
                            <button type="submit" class="boton-crear">Guardar concepto</button>
                        </div>
                    </form>

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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const radiosPeriodo = document.querySelectorAll('input[name="periodo"]');
    const campoPersonalizado = document.querySelector('.periodicidad-personalizada');

    radiosPeriodo.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === "Personalizado") {
                campoPersonalizado.style.display = "flex";
                campoPersonalizado.style.alignItems = "center";
                campoPersonalizado.style.gap = "10px";
            } else {
                campoPersonalizado.style.display = "none";
            }
        });
    });
});
</script>
