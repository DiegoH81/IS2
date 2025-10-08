<?php

// ------------------------------------------------------------
// UI-17: Crear concepto
// Caso de uso asociado: CU-16 - Crear concepto
// ------------------------------------------------------------

session_start();
require_once '../gtr/GTR-02_GestionarConcepto.php';
require_once '../gtr/GTR-09_GestionarCategoria.php';

// Paso 1 del CU-16: El gestor GTR-02 consulta a TAB-05 Categoría para obtener las categorías disponibles.
$categorias = GestionarCategoria::obtenerCategorias();

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = $_POST['nombre'];
    $tipo     = $_POST['tipo'];
    $monto    = $_POST['monto'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin    = $_POST['fecha_fin'];
    $categoriaId = $_POST['categoria'];
    $usuarioId   = $_SESSION['id_usuario'];
    $descripcion = '';

    // Paso 9 del CU-16: Determinar la periodicidad seleccionada por el usuario.
    $periodo_sel = $_POST['periodo'];
    switch ($periodo_sel) {
        case 'Diario': $periodo = 1; break;
        case 'Semanal': $periodo = 7; break;
        case 'Quincenal': $periodo = 15; break;
        case 'Mensual': $periodo = 30; break;
        case 'Eventual': $periodo = 0; break;
        case 'Personalizado':
            $periodo = isset($_POST['periodicidad']) ? (int)$_POST['periodicidad'] : 1;
            break;
        default: $periodo = 1;
    }

    // Paso 13-17 del CU-16: La interfaz valida los datos.

    $resultado = GestionarConcepto::crearConcepto(
        $nombre,
        $descripcion,
        $tipo,
        $monto,
        $periodo,
        $periodo_sel,
        $fecha_inicio,
        $fecha_fin,
        $categoriaId,
        $usuarioId
    );

    // Paso 18 del CU-16: Mostrar mensaje de confirmación.
    // Paso 19 del CU-16: Redirigir a la interfaz de Visualizar conceptos (UI-16).
    if ($resultado) {
        header("Location: UI-16_VisualizarConceptos.php");
        exit;
    } else {
        $error = "Ocurrió un error al crear el concepto.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear concepto</title>
    <link rel="stylesheet" href="../css/principal.css">
    <link rel="stylesheet" href="../css/configuracion.css">
    <link rel="stylesheet" href="../css/icons.css">
</head>
<body>
<div class="contenedor-principal">
    <header class="barra-superior">
        <section class="seccion-izquierda">
            <h1 class="titulo-app">On a budget</h1>
        </section>
        <section class="seccion-derecha">
            <h2 class="subtitulo">Configuración</h2>
            <div class="info-usuario">
                <span class="nombre-usuario"><?= htmlspecialchars($_SESSION['nombre']) ?></span>
                <span class="rol-usuario"><?= htmlspecialchars($_SESSION['rol']) ?></span>
            </div>
        </section>
    </header>

    <div class="contenedor-medio">
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
                <a class="opcion-menu activa" href="UI-16_VisualizarConceptos.php">
                    <i class="icono icono-configuracion"></i>Configuración
                </a>
            </nav>

            <footer class="parte-abajo">
                <a class="opcion-menu" href="UI-01_InicioDeSesion.php">
                    <i class="icono icono-salir"></i>Cerrar sesión
                </a>
            </footer>
        </aside>

        <main class="contenedor-medio">
            <aside class="submenu-configuracion" id="Sub_menuConfig">
                <nav>
                    <a class="opcion-submenu" href="#">
                        <i></i>Usuarios
                    </a>
                    <a class="opcion-submenu activa" href="UI-16_VisualizarConceptos.php">
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

                    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

                    <!-- Paso 3 del CU-16: La interfaz muestra la opción de Crear categoría. -->
                    <!-- Paso 4 del CU-16: El AC-02 ingresa el nombre del concepto y selecciona la categoría. -->
                    <!-- Paso 5 del CU-16: La interfaz muestra las categorías disponibles. -->
                    <!-- Paso 6 del CU-16: El AC-02 selecciona una categoría. -->

                    <form class="form-crear-concepto" method="POST">

                        <!-- Categoría -->
                        <div class="campo-formulario">
                            <label for="categoria">Categoría:</label>
                            <select id="categoria" name="categoria" required>
                                <option value="">Seleccionar categoría</option>
                                <?php foreach($categorias as $cat): ?>
                                    <option value="<?= $cat['id_categoria'] ?>">
                                        <?= htmlspecialchars($cat['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Paso 7 del CU-16: El AC-02 selecciona el tipo de concepto (Ingreso o Egreso). -->
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

                        <!-- Paso 8 del CU-16: El AC-02 ingresa el monto. -->
                        <div class="campo-formulario">
                            <label for="monto">Monto:</label>
                            <input type="number" id="monto" name="monto" step="0.01" placeholder="S/. 0.00" required>
                        </div>

                        <!-- Paso 9 del CU-16: El AC-02 selecciona el período. -->
                        <div class="campo-formulario">
                            <label>Periodo:</label>
                            <div class="opciones-radio columna-vertical">
                                <label><input type="radio" name="periodo" value="Diario" required> Diario</label><br>
                                <label><input type="radio" name="periodo" value="Semanal"> Semanal</label><br>
                                <label><input type="radio" name="periodo" value="Quincenal"> Quincenal</label><br>
                                <label><input type="radio" name="periodo" value="Mensual"> Mensual</label><br>
                                <label><input type="radio" name="periodo" value="Personalizado"> Personalizado</label><br>
                                <label><input type="radio" name="periodo" value="Eventual"> Eventual</label>
                            </div>
                        </div>

                        <div class="periodicidad-personalizada" style="display:none; margin-top:10px;">
                            <label>Periodicidad:</label>
                            <input type="number" name="periodicidad" placeholder="Ingrese número">
                        </div>

                        <!-- Paso 10 y 11 del CU-16: El AC-02 selecciona las fechas de inicio y fin. -->
                        <div class="campo-formulario">
                            <label>Día de inicio / Día de fin:</label>
                            <div class="fechas">
                                <input type="date" name="fecha_inicio" required>
                                <input type="date" name="fecha_fin">
                            </div>
                        </div>

                        <!-- Paso 12 del CU-16: El AC-02 selecciona la opción Crear. -->
                        <div class="campo-formulario">
                            <button type="submit" class="boton-crear">Guardar concepto</button>
                        </div>
                    </form>
                </article>
            </section>
        </main>
    </div>
</div>

<script>
// Paso 9 del CU-17: Mostrar campo de periodicidad si se selecciona "Personalizado".
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
</body>
</html>
