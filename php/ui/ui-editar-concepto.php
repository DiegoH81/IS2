<?php
// UI-18 Editar concepto
// CU-018 Editar concepto existente
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../clases/GestionarConcepto.php";
require_once __DIR__ . "/../clases/GestionarCategoria.php";

$gestionarConcepto = new GestionarConcepto($conn);
$gestionarCategoria = new GestionarCategoria($conn);

$usuarioId = $_SESSION['usuario_id'];
$usuarioNombre = $_SESSION['usuario_nombre'];
$usuarioRol = $_SESSION['usuario_rol'];
$conceptoId = intval($_GET['id'] ?? 0);

// Verificar que el concepto existe y pertenece al usuario
if ($conceptoId <= 0) {
    $_SESSION['error'] = 'ID de concepto no válido.';
    header("Location: configuracion.php");
    exit;
}

$concepto = $gestionarConcepto->obtenerConcepto($conceptoId);

if (!$concepto || $concepto['usuario_id'] != $usuarioId) {
    $_SESSION['error'] = 'Concepto no encontrado o no tiene permisos para editarlo.';
    header("Location: configuracion.php");
    exit;
}

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
    <title>Editar Concepto - On a budget</title>
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
            <h2 class="subtitulo">Editar Concepto</h2>
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
                    <h2>Editar Concepto: <?php echo htmlspecialchars($concepto['nombre']); ?></h2>
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

                <form method="POST" action="procesar_concepto.php" id="formEditarConcepto" class="formulario-concepto">
                    <input type="hidden" name="concepto_id" value="<?php echo $concepto['id']; ?>">
                    
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
                                    value="<?php echo htmlspecialchars($concepto['nombre']); ?>"
                                    required
                                >
                                <small>Máximo 100 caracteres</small>
                            </div>

                            <div class="grupo-campo">
                                <label for="tipo">Tipo *</label>
                                <select id="tipo" name="tipo" required onchange="filtrarCategorias()">
                                    <option value="">Seleccione un tipo</option>
                                    <option value="ingreso" <?php echo $concepto['tipo'] === 'ingreso' ? 'selected' : ''; ?>>Ingreso</option>
                                    <option value="egreso" <?php echo $concepto['tipo'] === 'egreso' ? 'selected' : ''; ?>>Egreso</option>
                                </select>
                            </div>
                        </div>

                        <div class="grupo-campo">
                            <label for="categoria">Categoría *</label>
                            <select id="categoria" name="categoria" required>
                                <option value="">Cargando categorías...</option>
                            </select>
                        </div>
                    </div>

                    <div class="seccion-form">
                        <h3>Periodicidad</h3>
                        
                        <div class="grupo-campo">
                            <label for="periodo">Período *</label>
                            <select id="periodo" name="periodo" required onchange="mostrarCamposDias()">
                                <option value="">Seleccione la periodicidad</option>
                                <option value="diario" <?php echo $concepto['periodo'] === 'diario' ? 'selected' : ''; ?>>Diario</option>
                                <option value="semanal" <?php echo $concepto['periodo'] === 'semanal' ? 'selected' : ''; ?>>Semanal</option>
                                <option value="mensual" <?php echo $concepto['periodo'] === 'mensual' ? 'selected' : ''; ?>>Mensual</option>
                                <option value="eventual" <?php echo $concepto['periodo'] === 'eventual' ? 'selected' : ''; ?>>Eventual</option>
                            </select>
                            <small>Define cada cuánto ocurre este concepto</small>
                        </div>

                        <div id="camposDias" style="display: <?php echo in_array($concepto['periodo'], ['semanal', 'mensual']) ? 'block' : 'none'; ?>;">
                            <div class="fila-campos">
                                <div class="grupo-campo">
                                    <label for="dia_inicio">Día de inicio</label>
                                    <input 
                                        type="number" 
                                        id="dia_inicio" 
                                        name="dia_inicio" 
                                        min="1" 
                                        max="31"
                                        value="<?php echo $concepto['dia_inicio'] ?? ''; ?>"
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
                                        value="<?php echo $concepto['dia_fin'] ?? ''; ?>"
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
                                    value="<?php echo $concepto['monto']; ?>"
                                    required
                                >
                            </div>
                            <small>Monto promedio o estimado para este concepto</small>
                        </div>
                    </div>

                    <div class="botones-form">
                        <a href="configuracion.php" class="boton-cancelar">Cancelar</a>
                        <button type="button" class="boton-eliminar" onclick="confirmarEliminar()">Eliminar Concepto</button>
                        <button type="submit" class="boton-guardar">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<script>
    const conceptoId = <?php echo $concepto['id']; ?>;
    const categoriaActual = <?php echo $concepto['categoria_id']; ?>;
    const tipoActual = '<?php echo $concepto['tipo']; ?>';

    // Filtrar categorías por tipo
    function filtrarCategorias() {
        const tipo = document.getElementById('tipo').value;
        const selectCategoria = document.getElementById('categoria');
        
        selectCategoria.innerHTML = '<option value="">Cargando...</option>';
        
        if (!tipo) {
            selectCategoria.innerHTML = '<option value="">Primero seleccione un tipo</option>';
            return;
        }

        fetch('obtener_categorias.php?tipo=' + tipo)
            .then(response => response.json())
            .then(data => {
                selectCategoria.innerHTML = '<option value="">Seleccione una categoría</option>';
                data.forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.id;
                    option.textContent = cat.nombre;
                    if (cat.id == categoriaActual) {
                        option.selected = true;
                    }
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

    // Confirmar eliminación
    function confirmarEliminar() {
        if (confirm('¿Está seguro de que desea eliminar este concepto? Esta acción no se puede deshacer.')) {
            window.location.href = 'eliminar_concepto.php?id=' + conceptoId;
        }
    }

    // Validación del formulario
    document.getElementById('formEditarConcepto').addEventListener('submit', function(e) {
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

    // Inicializar al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        filtrarCategorias();
        mostrarCamposDias();
    });
</script>

</body>
</html>
?>