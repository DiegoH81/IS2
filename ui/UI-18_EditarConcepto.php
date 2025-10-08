<?php

// ------------------------------------------------------------
// UI-18: Editar concepto
// Caso de uso asociado: CU-17 - Editar concepto
// ------------------------------------------------------------

session_start();
require_once '../gtr/GTR-02_GestionarConcepto.php';
require_once '../gtr/GTR-09_GestionarCategoria.php';

// Obtener id del concepto a editar (por GET)
if (!isset($_GET['id'])) {
    die("No se especificó el concepto");
}
$id_concepto = (int)$_GET['id'];


// Paso 1 del CU-18: La interfaz Editar concepto (UI-18) solicita al GTR-09 Gestionar categoría las categorías.
$categorias = GestionarCategoria::obtenerCategorias();
$concepto = GestionarConcepto::obtenerConcepto($id_concepto);
if (!$concepto) {
    die("Concepto no encontrado");
}

// Paso 13 del CU-18: El AC-02-Familiar selecciona la opción Guardar.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre        = $_POST['nombre'];
    $tipo          = $_POST['tipo'];
    $monto         = $_POST['monto'];
    $fecha_inicio  = $_POST['fecha_inicio'];
    $fecha_fin     = $_POST['fecha_fin'];
    $categoriaId   = $_POST['categoria'];
    $usuarioId = $_SESSION['id_usuario'];
    $descripcion   = '';

    // Paso 10 del CU-18: El AC-02-Familiar modifica el período (Diario, Semanal, Quincenal, Mensual, Personalizado, Eventual).
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

    // Guardar la cadena seleccionada
    $periodicidad = $periodo_sel;

    // Paso 17 del CU-18: El GTR-02 Gestionar concepto actualiza la información del concepto en TAB-02 Concepto.
    $resultado = GestionarConcepto::editarConcepto(
        $id_concepto,
        $nombre,
        $descripcion,
        $tipo,
        $monto,
        $periodo,
        $periodicidad,
        $fecha_inicio,
        $fecha_fin,
        $categoriaId,
        $usuarioId
    );

    if ($resultado) {
        // Paso 18 del CU-18: La interfaz muestra un mensaje de confirmación de cambios guardados.
        // Paso 19 del CU-18: La interfaz redirige al AC-02-Familiar a la interfaz Visualizar conceptos (UI-16).
        header("Location: UI-16_VisualizarConceptos.php");
        exit;
    } else {
        $error = "Ocurrió un error al actualizar el concepto.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar concepto</title>
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
        <!-- Paso 4 del CU-18: La interfaz muestra la opción de Crear categoría. -->
        <!-- Menú lateral -->
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
                        <h2 class="titulo-tabla">Editar concepto</h2>
                        <div class="linea-separadora"></div>
                        <div class="linea-azul"></div>
                    </header>

                    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

                    <form class="form-crear-concepto" method="POST">
                        <input type="hidden" name="id_concepto" value="<?= htmlspecialchars($concepto['id_concepto']) ?>">

                        <!-- Categoría -->
                        <div class="campo-formulario">
                            <label for="categoria">Categoría:</label>
                            <select id="categoria" name="categoria" required>
                                <option value="">Seleccionar categoría</option>
                                <?php foreach($categorias as $cat): ?>
                                    <option value="<?= $cat['id_categoria'] ?>" <?= $cat['id_categoria'] == $concepto['categoria_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Paso 5 y 7 del CU-18: El AC-02-Familiar modifica el nombre del concepto si es necesario. -->
                        <div class="campo-formulario">
                            <label for="nombre">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($concepto['nombre']) ?>" required>
                        </div>

                        <!-- Paso 8 del CU-18: El AC-02-Familiar modifica el tipo (Ingreso o Egreso). -->
                        <div class="campo-formulario">
                            <label>Tipo:</label>
                            <div class="opciones-radio">
                                <label><input type="radio" name="tipo" value="Ingreso" <?= $concepto['tipo'] == 'Ingreso' ? 'checked' : '' ?> required> Ingreso</label>
                                <label><input type="radio" name="tipo" value="Egreso" <?= $concepto['tipo'] == 'Egreso' ? 'checked' : '' ?> required> Egreso</label>
                            </div>
                        </div>

                        <!-- Paso 9 del CU-18: El AC-02-Familiar modifica el monto si es necesario. -->
                        <div class="campo-formulario">
                            <label for="monto">Monto:</label>
                            <input type="number" id="monto" name="monto" step="0.01" value="<?= $concepto['monto'] ?>" required>
                        </div>

                        <!-- Paso 10 del CU-18: El AC-02-Familiar modifica el período. -->
                        <div class="campo-formulario"><label>Periodo:</label>
                            <div class="opciones-radio columna-vertical">
                                <?php 
                                $periodos = ['Diario','Semanal','Quincenal','Mensual','Personalizado','Eventual'];
                                foreach($periodos as $p){
                                    $checked = $concepto['periodicidad'] == $p ? 'checked' : '';
                                    echo "<label><input type='radio' name='periodo' value='$p' $checked> $p</label><br>";
                                } ?> 
                            </div>
                        </div>

                        <div class="periodicidad-personalizada" style="display:<?= $concepto['periodicidad'] == 'Personalizado' ? 'flex' : 'none' ?>; margin-top:10px;">
                            <label>Periodicidad:</label>
                            <input type="number" name="periodicidad" value="<?= $concepto['periodo'] ?>" placeholder="Ingrese número">
                        </div>

                        <!-- Paso 11 y 12 del CU-18: El AC-02-Familiar modifica las fechas si es necesario. -->
                        <div class="campo-formulario">
                            <label>Día de inicio / Día de fin:</label>
                            <div class="fechas">
                                <input type="date" name="fecha_inicio" value="<?= $concepto['fecha_inicio'] ?>" required>
                                <input type="date" name="fecha_fin" value="<?= $concepto['fecha_fin'] ?>">
                            </div>
                        </div>

                        <!-- Paso 13 del CU-18: El AC-02-Familiar selecciona “Guardar”. -->
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
