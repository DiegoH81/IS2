<?php
// UI-17 Crear concepto
// CU-017 Crear nuevo concepto
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../clases/GestionarCategoria.php";

$gestionarCategoria = new GestionarCategoria($conn);
$usuarioId = $_SESSION['usuario_id'];
$usuarioNombre = $_SESSION['usuario_nombre'];
$usuarioRol = $_SESSION['usuario_rol'];

// Obtener todas las categorías
$categorias = $gestionarCategoria->obtenerCategorias();

// Mensajes
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Concepto - On a budget</title>
    <link rel="stylesheet" href="css/daily_input.css">
    <link rel="stylesheet" href="css/icons.css">
    <link rel="stylesheet" href="css/formularios.css">
</head>
<body>
<div class="contenedor-principal">

    <!-- Cabecera -->
    <header class="barra-superior">
        <section class="seccion-izquierda">
            <h1 class="titulo-app">On a budget</h1>
        </section>

        <section class="seccion-derecha">
            <h2 class="subtitulo">Crear Concepto</h2>
            <div class="info-usuario">
                <span class="nombre-usuario"><?php echo htmlspecialchars($usuarioNombre); ?></span>
                <span class="rol-usuario"><?php echo ucfirst($usuarioRol); ?></span>
            </div>
        </section>
    </header>

    <!-- Contenido principal -->
    <div class="contenedor-medio">
        <!-- Menu lateral -->
        <aside class="menu-lateral">
            <nav>
                <a class="opcion-menu" href="daily_input.php">
                    <i class="icono icono-documento"></i>Registro Diario
                </a>
                <a class="opcion-menu" href="balance.php">
                    <i class="icono icono-grafico"></i>Balance
                </a>
                <a class="opcion-menu" href="cuenta.php">
                    <i class="icono icono-persona"></i>Cuenta
                </a>
                <a class="opcion-menu activa" href="configuracion.php">
                    <i class="icono icono-configuracion"></i>Configuración
                </a>
            </nav>

            <footer class="parte-abajo">
                <a class="opcion-menu" href="logout.php">
                    <i class="icono icono-salir"></i>Cerrar sesión
                </a>
            </footer>
        </aside>

        <!-- Area principal -->
        <main class="area-trabajo">
            <div class="contenedor-formulario">
                
                <div class="header-formulario">
                    <h2>Nuevo Concepto</h2>
                    <a href="configuracion.php" class="boton-volver">← Volver</a>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="mensaje-error">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="mensaje-exito">
                        <?php echo $success; ?>
                        <a href="configuracion.php">Volver a configuración</a>
                    </div>
                <?php endif; ?>

                <form method="POST" action="procesar_concepto.php" id="formCrearConcepto" class="formulario-concepto">
                    
                    <div class="seccion-form">
                        <h3>Información básica</h3>
                        
                        <div class="fila-campos">
                            <div class="grupo-campo">
                                <label for="nombre">Nombre del concepto *</label>
                                <input 
                                    type="text" 
                                    id="nombre" 
                                    name="nombre" 
                                    placeholder="Ej: Salario mensual, Almuerzo diario"
                                    maxlength="100"
                                    value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>"
                                    required
                                >
                                <small>Máximo 100 caracteres</small>
                            </div>

                            <div class="grupo-campo">
                                <label for="tipo">Tipo *</label>
                                <select id="tipo" name="tipo" required onchange="filtrarCategorias()">
                                    <option value="">Seleccione un tipo</option>
                                    <option value="ingreso" <?php echo (isset($_POST['tipo']) && $_POST['tipo'] === 'ingreso') ? 'selected' : ''; ?>>Ingreso</option>
                                    <option value="egreso" <?php echo (isset($_POST['tipo']) && $_POST['tipo'] === 'egreso') ? 'selected' : ''; ?>>Egreso</option>
                                </select>
                            </div>
                        </div>

                        <div class="grupo-campo">
                            <label for="categoria">Categoría *</label>
                            <select id="categoria" name="categoria" required>
                                <option value="">Primero seleccione un tipo</option>
                            </select>
                        </div>
                    </div>

                    <div class="seccion-form">
                        <h3>Periodicidad</h3>
                        
                        <div class="grupo-campo">
                            <label for="periodo">Período *</label>
                            <select id="periodo" name="periodo" required onchange="mostrarCamposDias()">
                                <option value="">Seleccione la periodicidad</option>
                                <option value="diario" <?php echo (isset($_POST['periodo']) && $_POST['periodo'] === 'diario') ? 'selected' : ''; ?>>Diario</option>
                                <option value="semanal" <?php echo (isset($_POST['periodo']) && $_POST['periodo'] === 'semanal') ? 'selected' : ''; ?>>Semanal</option>
                                <option value="mensual" <?php echo (isset($_POST['periodo']) && $_POST['periodo'] === 'mensual') ? 'selected' : ''; ?>>Mensual</option>
                                <option value="eventual" <?php echo (isset($_POST['periodo']) && $_POST['periodo'] === 'eventual') ? 'selected' : ''; ?>>Eventual</option>
                            </select>
                            <small>Define cada cuánto ocurre este concepto</small>
                        </div>

                        <div id="camposDias" style="display: none;">
                            <div class="fila-campos">
                                <div class="grupo-campo">
                                    <label for="dia_inicio">Día de inicio</label>
                                    <input 
                                        type="number" 
                                        id="dia_inicio" 
                                        name="dia_inicio" 
                                        min="1" 
                                        max="31"
                                        value="<?php echo htmlspecialchars($_POST['dia_inicio'] ?? ''); ?>"
                                    >
                                    <small id="ayudaDiaInicio">Ingrese el día correspondiente</small>
                                </div>

                                <div class="grupo-campo">
                                    <label for="dia_fin">Día de fin (opcional)</label>
                                    <input 
                                        type="number" 
                                        id="dia_fin" 
                                        name="dia_fin" 
                                        min="1" 
                                        max="31"
                                        value="<?php echo htmlspecialchars($_POST['dia_fin'] ?? ''); ?>"
                                    >
                                    <small>Solo si aplica un rango</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="seccion-form">
                        <h3>Monto</h3>
                        
                        <div class="grupo-campo">
                            <label for="monto">Monto estimado *</label>
                            <div class="campo-con-prefijo">
                                <span class="prefijo">S/.</span>
                                <input 
                                    type="number" 
                                    id="monto" 
                                    name="monto" 
                                    step="0.01" 
                                    min="0.01"
                                    placeholder="0.00"
                                    value="<?php echo htmlspecialchars($_POST['monto'] ?? ''); ?>"
                                    required
                                >
                            </div>
                            <small>Monto promedio o estimado para este concepto</small>
                        </div>
                    </div>

                    <div class="botones-form">
                        <a href="configuracion.php" class="boton-cancelar">Cancelar</a>
                        <button type="submit" class="boton-guardar">Crear Concepto</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<script>
    // Datos de categorías desde PHP
    const todasCategorias = <?php echo json_encode($categorias); ?>;

    // Filtrar categorías por tipo
    function filtrarCategorias() {
        const tipo = document.getElementById('tipo').value;
        const selectCategoria = document.getElementById('categoria');
        
        selectCategoria.innerHTML = '<option value="">Seleccione una categoría</option>';
        
        if (!tipo) {
            selectCategoria.innerHTML = '<option value="">Primero seleccione un tipo</option>';
            return;
        }

        // Obtener categorías vía AJAX para asegurar que sean del tipo correcto
        fetch('obtener_categorias.php?tipo=' + tipo)
            .then(response => response.json())
            .then(data => {
                data.forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.id;
                    option.textContent = cat.nombre;
                    selectCategoria.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar categorías');
            });
    }

    // Mostrar campos de días según período
    function mostrarCamposDias() {
        const periodo = document.getElementById('periodo').value;
        const camposDias = document.getElementById('camposDias');
        const diaInicio = document.getElementById('dia_inicio');
        const ayuda = document.getElementById('ayudaDiaInicio');
        
        if (periodo === 'semanal') {
            camposDias.style.display = 'block';
            diaInicio.max = 7;
            diaInicio.required = true;
            ayuda.textContent = 'Día de la semana: 1 = Lunes, 7 = Domingo';
        } else if (periodo === 'mensual') {
            camposDias.style.display = 'block';
            diaInicio.max = 31;
            diaInicio.required = true;
            ayuda.textContent = 'Día del mes (1 al 31)';
        } else {
            camposDias.style.display = 'none';
            diaInicio.required = false;
        }
    }

    // Validación del formulario
    document.getElementById('formCrearConcepto').addEventListener('submit', function(e) {
        const nombre = document.getElementById('nombre').value.trim();
        const tipo = document.getElementById('tipo').value;
        const categoria = document.getElementById('categoria').value;
        const periodo = document.getElementById('periodo').value;
        const monto = document.getElementById('monto').value;
        const diaInicio = document.getElementById('dia_inicio').value;

        // Validar campos obligatorios
        if (!nombre || !tipo || !categoria || !periodo || !monto) {
            e.preventDefault();
            alert('Por favor, complete todos los campos obligatorios marcados con *');
            return false;
        }

        // Validar nombre
        if (nombre.length > 100) {
            e.preventDefault();
            alert('El nombre no debe exceder 100 caracteres');
            return false;
        }

        // Validar monto
        if (parseFloat(monto) <= 0) {
            e.preventDefault();
            alert('El monto debe ser mayor a 0');
            return false;
        }

        // Validar día de inicio para períodos que lo requieren
        if ((periodo === 'semanal' || periodo === 'mensual') && !diaInicio) {
            e.preventDefault();
            alert('Debe especificar el día de inicio para el período seleccionado');
            return false;
        }

        if (periodo === 'semanal' && (diaInicio < 1 || diaInicio > 7)) {
            e.preventDefault();
            alert('Para período semanal, el día debe estar entre 1 y 7');
            return false;
        }

        if (periodo === 'mensual' && (diaInicio < 1 || diaInicio > 31)) {
            e.preventDefault();
            alert('Para período mensual, el día debe estar entre 1 y 31');
            return false;
        }
    });

    // Inicializar si hay datos previos
    document.addEventListener('DOMContentLoaded', function() {
        const tipoActual = document.getElementById('tipo').value;
        if (tipoActual) {
            filtrarCategorias();
        }
        
        const periodoActual = document.getElementById('periodo').value;
        if (periodoActual) {
            mostrarCamposDias();
        }
    });
</script>

</body>
</html>
?>