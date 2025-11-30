<?php

// ------------------------------------------------------------
// UI-17: Crear concepto
// Caso de uso asociado: CU-09-1 - Crear concepto
// ------------------------------------------------------------

session_start();
require_once '../gtr/GTR-02_GestionarConcepto.php';
require_once '../gtr/GTR-09_GestionarCategoria.php';

$categorias = GestionarCategoria::obtenerCategoriasBD($_SESSION['familia_id']);

$from = $_GET['from'] ?? 'visualizar_conceptos';

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre        = $_POST['nombre'];
    $tipo          = $_POST['tipo'];
    $categoria_id  = $_POST['categoria'];
    
    // Si se activó la periodicidad
    if (isset($_POST['activar_periodicidad']) && $_POST['activar_periodicidad'] === '1') {
        $periodo = $_POST['periodo'];
        $fecha_inicio = $_POST['fechaInicio'];
        $fecha_fin = $_POST['fechaFin'];
        
    } else {
        // Por defecto: Eventual (0) y fechas actuales
        $periodo = 0;
        $fecha_inicio = date('Y-m-d');
        $fecha_fin = date('Y-m-d');
    }

    GestionarConcepto::crearConceptoBD(
        $nombre,
        $tipo,
        $periodo,
        $fecha_inicio,
        $_SESSION['familia_id'],
        $_SESSION['id_usuario'],
        $categoria_id
    );

    $from = $_POST['from'] ?? 'visualizar_conceptos';

    if ($from === 'registro_diario') {
        header("Location: UI-04_RegistroDiario.php");
        exit;
    } else {
        header("Location: UI-16_VisualizarConceptos.php");
        exit;
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
    <link rel="stylesheet" href="../css/editar_concepto.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
                <a class="opcion-menu" href="UI-04_RegistroDiario.php">
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
                    <?php if ($_SESSION['rol'] === 'Administrador familiar'): ?>
                        <a class="opcion-submenu" href="UI-12_VisualizarUsuarios.php">
                            <i></i>Usuarios
                        </a>
                    <?php endif; ?>
                    <a class="opcion-submenu activa" href="UI-16_VisualizarConceptos.php">
                        <i></i>Conceptos
                    </a>
                    <a class="opcion-submenu" href="UI-20_VisualizarCategoria.php">
                        <i></i>Categorías
                    </a>
                    <a class="opcion-submenu" href="UI-23_VisualizarTransaccion.php">
                        <i></i>Transacciones
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

                    <form class="form-crear-concepto" method="POST">
                        <input type="hidden" name="activar_periodicidad" id="activarPeriodicidad" value="0">
                        <input type="hidden" name="from" value="<?= htmlspecialchars($from) ?>">

                        <!-- Categoría -->
                        <div class="campo-formulario">
                            <label for="categoria">Categoría:</label>
                            <select id="categoria" name="categoria" required>
                                <option value="">Seleccionar categoría</option>
                                <?php foreach($categorias as $cat): ?>
                                    <option value="<?= $cat->idCategoria ?>">
                                        <?= htmlspecialchars($cat->nombre) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Nombre -->
                        <div class="campo-formulario">
                            <label for="nombre">Nombre:</label>
                            <input type="text" id="nombre" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" title="Ingrese solo letras" name="nombre" placeholder="Ingrese nombre del concepto" required>
                        </div>

                        <!-- Tipo -->
                        <div class="campo-formulario">
                            <label>Tipo:</label>
                            <div class="opciones-tipo-moderno">
                                <div class="tarjeta-tipo">
                                    <input type="radio" name="tipo" value="Ingreso" id="tipoIngreso" required>
                                    <label for="tipoIngreso">
                                        <div class="icono-tipo"><i class="fa-solid fa-piggy-bank"></i></div>
                                        <div class="nombre-tipo">Ingreso</div>
                                    </label>
                                </div>
                                <div class="tarjeta-tipo">
                                    <input type="radio" name="tipo" value="Egreso" id="tipoEgreso" required>
                                    <label for="tipoEgreso">
                                        <div class="icono-tipo"><i class="fa-solid fa-money-bill-1"></i></div>
                                        <div class="nombre-tipo">Egreso</div>
                                    </label>
                                </div>
                            </div>
                        </div>


                        <!-- Botón para activar periodicidad -->
                        <div class="campo-formulario centro">
                            <button type="button" class="boton-toggle" id="btnTogglePeriodicidad">
                                <span id="textoBoton">Configurar periodicidad</span>
                                <span class="icono-flecha">▼</span>
                            </button>
                        </div>

                        <!-- Sección de Periodicidad (oculta por defecto) -->
                        <div class="seccion-periodicidad" id="seccionPeriodicidad">
                            <h3>Configuración de Periodicidad</h3>
                            
                            <!-- Período -->
                            <div class="campo-formulario">
                                <label>Periodo:</label>
                                <div class="opciones-periodo-moderno">
                                    <?php 
                                    $periodos = [
                                        'Diario'    => ['valor' => 1, 'icono' => '<i class="fa-solid fa-sun"></i>'],
                                        'Semanal'   => ['valor' => 7, 'icono' => '<i class="fa-solid fa-calendar-week"></i>'],
                                        'Quincenal' => ['valor' => 15, 'icono' => '<i class="fa-solid fa-calendar-days"></i>'],
                                        'Mensual'   => ['valor' => 30, 'icono' => '<i class="fa-solid fa-calendar"></i>'],
                                        'Eventual'  => ['valor' => 0, 'icono' => '<i class="fa-solid fa-check"></i>']
                                    ];

                                    foreach ($periodos as $nombre => $info) {
                                        $valor = $info['valor'];
                                        $icono = $info['icono'];
                                        $id = "periodo_" . strtolower($nombre);
                                        // Por defecto seleccionar Eventual
                                        $checked = ($nombre === 'Eventual') ? 'checked' : '';
                                        echo "<div class='tarjeta-periodo'>
                                                <input type='radio' name='periodo' value='$valor' id='$id' $checked> 
                                                <label for='$id'>$icono ‎ $nombre</label>
                                            </div>";
                                    }
                                    ?>
                                </div>
                            </div>

                            <!-- Fechas -->
                            <div class="campo-formulario">
                                <label>Día de inicio</label>
                                <div class="fechas">
                                    <input type="date" name="fechaInicio" id="fechaInicio" value="<?= date('Y-m-d') ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Botón Guardar -->
                        <div class="campo-formulario">
                            <button type="submit" class="boton-crear">Crear concepto</button>
                        </div>
                    </form>
                </article>
            </section>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnToggle = document.getElementById('btnTogglePeriodicidad');
    const seccionPeriodicidad = document.getElementById('seccionPeriodicidad');
    const inputActivar = document.getElementById('activarPeriodicidad');
    const textoBoton = document.getElementById('textoBoton');
    const fechaInicio = document.getElementById('fechaInicio');
    const fechaFin = document.getElementById('fechaFin');

    btnToggle.addEventListener('click', function() {
        if (seccionPeriodicidad.classList.contains('activa')) {
            desactivarPeriodicidad();
        } else {
            activarPeriodicidad();
        }
    });

    function activarPeriodicidad() {
        seccionPeriodicidad.classList.add('activa');
        btnToggle.classList.add('activo');
        inputActivar.value = '1';
        textoBoton.textContent = 'Ocultar periodicidad';
        fechaInicio.required = true;
        fechaFin.required = true;
    }

    function desactivarPeriodicidad() {
        seccionPeriodicidad.classList.remove('activa');
        btnToggle.classList.remove('activo');
        inputActivar.value = '0';
        textoBoton.textContent = 'Configurar periodicidad';
        fechaInicio.required = false;
        fechaFin.required = false;
    }
});
</script>
</body>
</html>