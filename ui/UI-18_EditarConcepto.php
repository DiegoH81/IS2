<?php
session_start();
require_once '../gtr/GTR-02_GestionarConcepto.php';
require_once '../gtr/GTR-09_GestionarCategoria.php';

// Obtener id del concepto a editar (por GET)
if (!isset($_GET['id'])) {
    die("No se especificó el concepto");
}
$id_concepto = (int)$_GET['id'];

// Cargar datos existentes
$categorias = GestionarCategoria::obtenerCategorias();
$concepto = GestionarConcepto::obtenerConcepto($id_concepto);
if (!$concepto) {
    die("Concepto no encontrado");
}

// Procesar formulario POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = $_POST['nombre'];
    $categoria = $_POST['categoria'];
    $tipo     = $_POST['tipo'];
    $monto    = $_POST['monto'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin    = $_POST['fecha_fin'];

    // Determinar periodicidad según la opción
    $periodo_sel = $_POST['periodo'];
    switch ($periodo_sel) {
        case 'Diario': $periodo = 1; break;
        case 'Semanal': $periodo = 7; break;
        case 'Quincenal': $periodo = 15; break;
        case 'Mensual': $periodo = 30; break;
        case 'Eventual': $periodo = 30; break;
        case 'Personalizado':
            $periodo = isset($_POST['periodicidad']) ? (int)$_POST['periodicidad'] : 1;
            break;
        default: $periodo = 1;
    }

    // Llamar al gestor para actualizar
    $resultado = GestionarConcepto::editarConcepto($id_concepto, $nombre, $categoria, $tipo, $periodo, $monto, $fecha_inicio, $fecha_fin);

    if ($resultado) {
        header("Location: visualizar_conceptos.php");
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
    <!-- Cabecera y menú lateral igual que UI de crear -->

    <main class="contenedor-medio">
        <aside class="submenu-configuracion" id="Sub_menuConfig">
            <!-- submenu igual -->
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
                    <input type="hidden" name="id_concepto" value="<?= $concepto['id'] ?>">

                    <!-- Categoría -->
                    <div class="campo-formulario">
                        <label for="categoria">Categoría:</label>
                        <select id="categoria" name="categoria" required>
                            <option value="">Seleccionar categoría</option>
                            <?php foreach($categorias as $cat): ?>
                                <option value="<?= htmlspecialchars($cat['nombre']) ?>" 
                                    <?= $cat['nombre'] == $concepto['categoria'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Nombre -->
                    <div class="campo-formulario">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($concepto['nombre']) ?>" required>
                    </div>

                    <!-- Tipo -->
                    <div class="campo-formulario">
                        <label>Tipo:</label>
                        <div class="opciones-radio">
                            <label><input type="radio" name="tipo" value="Ingreso" <?= $concepto['tipo'] == 'Ingreso' ? 'checked' : '' ?> required> Ingreso</label>
                            <label><input type="radio" name="tipo" value="Egreso" <?= $concepto['tipo'] == 'Egreso' ? 'checked' : '' ?> required> Egreso</label>
                        </div>
                    </div>

                    <!-- Monto -->
                    <div class="campo-formulario">
                        <label for="monto">Monto:</label>
                        <input type="number" id="monto" name="monto" step="0.01" value="<?= $concepto['monto'] ?>" required>
                    </div>

                    <!-- Periodo -->
                    <div class="campo-formulario">
                        <label>Periodo:</label>
                        <div class="opciones-radio columna-vertical">
                            <?php
                            $periodos = ['Diario','Semanal','Quincenal','Mensual','Personalizado','Eventual'];
                            foreach($periodos as $p){
                                $checked = $concepto['periodo_texto'] == $p ? 'checked' : '';
                                echo "<label><input type='radio' name='periodo' value='$p' $checked> $p</label><br>";
                            }
                            ?>
                        </div>
                    </div>

                    <div class="periodicidad-personalizada" style="display:<?= $concepto['periodo_texto'] == 'Personalizado' ? 'flex' : 'none' ?>; margin-top:10px;">
                        <label>Periodicidad:</label>
                        <input type="number" name="periodicidad" value="<?= $concepto['periodo'] ?>" placeholder="Ingrese número">
                    </div>

                    <!-- Fechas -->
                    <div class="campo-formulario">
                        <label>Día de inicio / Día de fin:</label>
                        <div class="fechas">
                            <input type="date" name="fecha_inicio" value="<?= $concepto['fecha_inicio'] ?>" required>
                            <input type="date" name="fecha_fin" value="<?= $concepto['fecha_fin'] ?>">
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
