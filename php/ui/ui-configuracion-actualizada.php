<?php
// UI-16 Visualizar conceptos (Configuración)
// CU-016 Gestionar conceptos desde configuración
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../clases/GestionarUsuario.php";
require_once __DIR__ . "/../clases/GestionarConcepto.php";
require_once __DIR__ . "/../clases/GestionarCategoria.php";

$gestionarUsuario = new GestionarUsuario($conn);
$gestionarConcepto = new GestionarConcepto($conn);
$gestionarCategoria = new GestionarCategoria($conn);

$usuarioId = $_SESSION['usuario_id'];
$usuarioNombre = $_SESSION['usuario_nombre'];
$usuarioRol = $_SESSION['usuario_rol'];
$familiaId = $_SESSION['familia_id'];

// Obtener usuarios de la familia
$usuariosFamilia = $gestionarUsuario->obtenerUsuariosFamilia($familiaId);

// Obtener conceptos del usuario
$conceptos = $gestionarConcepto->obtenerConceptos($usuarioId);

// Mensajes de sesión
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración - On a budget</title>
    <link rel="stylesheet" href="css/daily_input.css">
    <link rel="stylesheet" href="css/icons.css">
    <link rel="stylesheet" href="css/configuracion.css">
</head>
<body>
<div class="contenedor-principal">

    <!-- Cabecera -->
    <header class="barra-superior">
        <section class="seccion-izquierda">
            <h1 class="titulo-app">On a budget</h1>
        </section>

        <section class="seccion-derecha">
            <h2 class="subtitulo">Configuración</h2>
            <div class="info-usuario">
                <span class="nombre-usuario"><?php echo htmlspecialchars($usuarioNombre); ?></span>
                <span class="rol-usuario"><?php echo ucfirst($usuarioRol); ?></span>
            </div>
        </section>
    </header>

    <!-- Contenido principal -->
    <div class="contenedor-medio">
        <!-- Menu lateral -->
        <aside class="menu-lateral" id="menuLateral">
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

        <!-- Area principal con submenu -->
        <div class="area-con-submenu">
            <!-- Submenu de configuración -->
            <aside class="submenu-configuracion">
                <nav class="nav-submenu">
                    <a class="opcion-submenu" href="#usuarios" onclick="mostrarSeccion('usuarios')">Usuarios</a>
                    <a class="opcion-submenu activa" href="#conceptos" onclick="mostrarSeccion('conceptos')">Conceptos</a>
                </nav>
            </aside>

            <!-- Contenido de configuración -->
            <main class="contenido-configuracion">
                
                <?php if (!empty($success)): ?>
                    <div class="mensaje-exito">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="mensaje-error">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <!-- Sección de usuarios -->
                <section class="seccion-usuarios" id="usuarios" style="display: none;">
                    <div class="barra-herramientas">
                        <div class="contenedor-busqueda">
                            <input type="text" class="campo-busqueda" id="buscarUsuarios" placeholder="Buscar usuarios">
                            <i class="icono-buscar"></i>
                        </div>
                        <button class="boton-crear-usuario" onclick="window.location.href='registrar_usuario.php'">
                            Crear usuario
                        </button>
                    </div>

                    <div class="contenedor-tabla-usuarios">
                        <table class="tabla-usuarios">
                            <thead>
                            <tr>
                                <th class="columna-usuario">Usuario</th>
                                <th class="columna-nombre">Nombre</th>
                                <th class="columna-rol">Rol</th>
                                <th class="columna-accion">Acción</th>
                            </tr>
                            </thead>
                            <tbody id="tablaUsuariosBody">
                            <?php foreach ($usuariosFamilia as $usuario): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($usuario['nickname']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                    <td><?php echo ucfirst($usuario['rol']); ?></td>
                                    <td class="celda-accion">
                                        <?php if ($usuario['id'] != $usuarioId): ?>
                                            <a href="#" class="link-borrar" onclick="confirmarEliminarUsuario(<?php echo $usuario['id']; ?>, '<?php echo htmlspecialchars($usuario['nombre']); ?>')">Borrar</a>
                                        <?php else: ?>
                                            <span class="texto-actual">Usted</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Sección de conceptos -->
                <section class="seccion-conceptos" id="conceptos">
                    <div class="barra-herramientas">
                        <div class="contenedor-busqueda">
                            <input type="text" class="campo-busqueda" id="buscarConceptos" placeholder="Buscar conceptos">
                            <i class="icono-buscar"></i>
                        </div>
                        <button class="boton-crear-usuario" onclick="abrirModalConcepto()">
                            Crear Concepto
                        </button>
                    </div>

                    <div class="contenedor-tabla-usuarios">
                        <table class="tabla-usuarios tabla-conceptos">
                            <thead>
                            <tr>
                                <th class="columna-concepto">Concepto</th>
                                <th class="columna-tipo">Tipo</th>
                                <th class="columna-categoria">Categoría</th>
                                <th class="columna-periodo">Período</th>
                                <th class="columna-costo">Monto</th>
                                <th class="columna-accion">Acción</th>
                            </tr>
                            </thead>
                            <tbody id="tablaConceptosBody">
                            <?php foreach ($conceptos as $concepto): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($concepto['nombre']); ?></td>
                                    <td><?php echo ucfirst($concepto['tipo']); ?></td>
                                    <td><?php echo htmlspecialchars($concepto['categoria_nombre'] ?? 'Sin categoría'); ?></td>
                                    <td><?php echo ucfirst($concepto['periodo']); ?></td>
                                    <td>S/. <?php echo number_format($concepto['monto'], 2); ?></td>
                                    <td class="celda-accion">
                                        <a href="#" class="link-editar-concepto" onclick="editarConcepto(<?php echo $concepto['id']; ?>)">Editar</a>
                                        <a href="#" class="link-borrar" onclick="confirmarEliminarConcepto(<?php echo $concepto['id']; ?>, '<?php echo htmlspecialchars($concepto['nombre']); ?>')">Borrar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
    </div>
</div>

<!-- Modal para crear/editar concepto -->
<div id="modalConcepto" class="modal" style="display: none;">
    <div class="modal-contenido-grande">
        <span class="cerrar" onclick="cerrarModalConcepto()">&times;</span>
        <h2 id="modalConceptoTitulo">Crear Concepto</h2>
        <form id="formConcepto" action="procesar_concepto.php" method="POST">
            <input type="hidden" id="concepto_id" name="concepto_id">
            
            <div class="fila-campos">
                <div class="grupo-campo">
                    <label for="nombre_concepto">Nombre *</label>
                    <input type="text" id="nombre_concepto" name="nombre" maxlength="100" required>
                </div>

                <div class="grupo-campo">
                    <label for="tipo_concepto">Tipo *</label>
                    <select id="tipo_concepto" name="tipo" required onchange="cargarCategoriasPorTipo()">
                        <option value="">Seleccione</option>
                        <option value="ingreso">Ingreso</option>
                        <option value="egreso">Egreso</option>
                    </select>
                </div>
            </div>

            <div class="fila-campos">
                <div class="grupo-campo">
                    <label for="categoria_concepto">Categoría *</label>
                    <select id="categoria_concepto" name="categoria" required>
                        <option value="">Primero seleccione tipo</option>
                    </select>
                </div>

                <div class="grupo-campo">
                    <label for="periodo_concepto">Período *</label>
                    <select id="periodo_concepto" name="periodo" required onchange="mostrarCamposDias()">
                        <option value="">Seleccione</option>
                        <option value="diario">Diario</option>
                        <option value="semanal">Semanal</option>
                        <option value="mensual">Mensual</option>
                        <option value="eventual">Eventual</option>
                    </select>
                </div>
            </div>

            <div class="fila-campos" id="camposDias" style="display: none;">
                <div class="grupo-campo">
                    <label for="dia_inicio">Día de inicio</label>
                    <input type="number" id="dia_inicio" name="dia_inicio" min="1" max="31">
                    <small id="ayudaDiaInicio"></small>
                </div>

                <div class="grupo-campo">
                    <label for="dia_fin">Día de fin</label>
                    <input type="number" id="dia_fin" name="dia_fin" min="1" max="31">
                    <small>Opcional</small>
                </div>
            </div>

            <div class="grupo-campo">
                <label for="monto_concepto">Monto *</label>
                <input type="number" id="monto_concepto" name="monto" step="0.01" min="0.01" required>
            </div>

            <div class="botones-modal">
                <button type="button" class="boton-cancelar" onclick="cerrarModalConcepto()">Cancelar</button>
                <button type="submit" class="boton-guardar">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Mostrar secciones
    function mostrarSeccion(seccion) {
        document.getElementById('usuarios').style.display = 'none';
        document.getElementById('conceptos').style.display = 'none';
        document.getElementById(seccion).style.display = 'block';
        
        // Actualizar menú activo
        document.querySelectorAll('.opcion-submenu').forEach(op => op.classList.remove('activa'));
        document.querySelector(`[href="#${seccion}"]`).classList.add('activa');
    }

    // Búsqueda de usuarios
    document.getElementById('buscarUsuarios')?.addEventListener('input', function() {
        const valor = this.value.toLowerCase();
        const filas = document.querySelectorAll('#tablaUsuariosBody tr');
        
        filas.forEach(fila => {
            const texto = fila.textContent.toLowerCase();
            fila.style.display = texto.includes(valor) ? '' : 'none';
        });
    });

    // Búsqueda de conceptos
    document.getElementById('buscarConceptos').addEventListener('input', function() {
        const valor = this.value.toLowerCase();
        const filas = document.querySelectorAll('#tablaConceptosBody tr');
        
        filas.forEach(fila => {
            const texto = fila.textContent.toLowerCase();
            fila.style.display = texto.includes(valor) ? '' : 'none';
        });
    });

    // Abrir modal de concepto
    function abrirModalConcepto() {
        document.getElementById('modalConcepto').style.display = 'block';
        document.getElementById('modalConceptoTitulo').textContent = 'Crear Concepto';
        document.getElementById('formConcepto').reset();
        document.getElementById('concepto_id').value = '';
        document.getElementById('camposDias').style.display = 'none';
    }

    // Cerrar modal de concepto
    function cerrarModalConcepto() {
        document.getElementById('modalConcepto').style.display = 'none';
        document.getElementById('formConcepto').reset();
    }

    // Cargar categorías por tipo
    function cargarCategoriasPorTipo() {
        const tipo = document.getElementById('tipo_concepto').value;
        const selectCategoria = document.getElementById('categoria_concepto');
        
        if (!tipo) {
            selectCategoria.innerHTML = '<option value="">Primero seleccione tipo</option>';
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
        const periodo = document.getElementById('periodo_concepto').value;
        const camposDias = document.getElementById('camposDias');
        const ayuda = document.getElementById('ayudaDiaInicio');
        
        if (periodo === 'semanal') {
            camposDias.style.display = 'flex';
            ayuda.textContent = '1=Lunes, 7=Domingo';
            document.getElementById('dia_inicio').max = 7;
        } else if (periodo === 'mensual') {
            camposDias.style.display = 'flex';
            ayuda.textContent = 'Día del mes (1-31)';
            document.getElementById('dia_inicio').max = 31;
        } else {
            camposDias.style.display = 'none';
        }
    }

    // Editar concepto
    function editarConcepto(id) {
        fetch('obtener_concepto_detalle.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                
                document.getElementById('modalConcepto').style.display = 'block';
                document.getElementById('modalConceptoTitulo').textContent = 'Editar Concepto';
                document.getElementById('concepto_id').value = data.id;
                document.getElementById('nombre_concepto').value = data.nombre;
                document.getElementById('tipo_concepto').value = data.tipo;
                document.getElementById('periodo_concepto').value = data.periodo;
                document.getElementById('monto_concepto').value = data.monto;
                document.getElementById('dia_inicio').value = data.dia_inicio || '';
                document.getElementById('dia_fin').value = data.dia_fin || '';
                
                cargarCategoriasPorTipo();
                setTimeout(() => {
                    document.getElementById('categoria_concepto').value = data.categoria_id;
                }, 500);
                
                mostrarCamposDias();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar el concepto');
            });
    }

    // Confirmar eliminar usuario
    function confirmarEliminarUsuario(id, nombre) {
        if (confirm(`¿Está seguro de eliminar al usuario "${nombre}"?`)) {
            window.location.href = 'eliminar_usuario.php?id=' + id;
        }
        return false;
    }

    // Confirmar eliminar concepto
    function confirmarEliminarConcepto(id, nombre) {
        if (confirm(`¿Está seguro de eliminar el concepto "${nombre}"?`)) {
            window.location.href = 'eliminar_concepto.php?id=' + id;
        }
        return false;
    }

    // Validación del formulario
    document.getElementById('formConcepto').addEventListener('submit', function(e) {
        const nombre = document.getElementById('nombre_concepto').value.trim();
        const tipo = document.getElementById('tipo_concepto').value;
        const categoria = document.getElementById('categoria_concepto').value;
        const periodo = document.getElementById('periodo_concepto').value;
        const monto = document.getElementById('monto_concepto').value;

        if (!nombre || !tipo || !categoria || !periodo || !monto) {
            e.preventDefault();
            alert('Por favor, complete todos los campos obligatorios');
            return false;
        }

        if (monto <= 0) {
            e.preventDefault();
            alert('El monto debe ser mayor a 0');
            return false;
        }
    });
</script>

</body>
</html>
?>