<?php
// UI-04 Registro Diario
// CU-004 Gestionar registro diario de ingresos y egresos
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../clases/GestionarRegistroDiario.php";
require_once __DIR__ . "/../clases/GestionarConcepto.php";

$gestionarRegistro = new GestionarRegistroDiario($conn);
$gestionarConcepto = new GestionarConcepto($conn);

$usuarioId = $_SESSION['usuario_id'];
$usuarioNombre = $_SESSION['usuario_nombre'];
$usuarioRol = $_SESSION['usuario_rol'];
$esFamiliar = isset($_POST['vista_familiar']) ? (bool)$_POST['vista_familiar'] : true;
$diaHoy = date('Y-m-d');

// Obtener transacciones del día
$transaccionesIngresos = [];
$transaccionesEgresos = [];
$transacciones = $gestionarRegistro->obtenerTransaccionesDiarias($usuarioId, $esFamiliar, $diaHoy);

foreach ($transacciones as $trans) {
    if ($trans['tipo'] === 'ingreso') {
        $transaccionesIngresos[] = $trans;
    } else {
        $transaccionesEgresos[] = $trans;
    }
}

// Calcular balances
$balances = $gestionarRegistro->calcularBalances($usuarioId, $esFamiliar);

// Calcular totales para las tablas
$totalIngresos = array_sum(array_column($transaccionesIngresos, 'monto'));
$totalEgresos = array_sum(array_column($transaccionesEgresos, 'monto'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Diario - On a budget</title>
    <link rel="stylesheet" href="css/daily_input.css">
    <link rel="stylesheet" href="css/icons.css">
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
                <a class="opcion-menu activa" href="daily_input.php">
                    <i class="icono icono-documento"></i>Registro Diario
                </a>
                <a class="opcion-menu" href="balance.php">
                    <i class="icono icono-grafico"></i>Balance
                </a>
                <a class="opcion-menu" href="cuenta.php">
                    <i class="icono icono-persona"></i>Cuenta
                </a>
                <a class="opcion-menu" href="configuracion.php">
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

            <!-- Controles superiores -->
            <section class="controles-superiores">
                <div class="grupo-controles">
                    <!-- Switch familiar/personal -->
                    <div class="contenedor-switch">
                        <span class="texto-switch">FAMILIAR / PERSONAL</span>
                        <label class="boton-switch">
                            <input type="checkbox" id="switchVista" <?php echo $esFamiliar ? 'checked' : ''; ?>>
                            <span class="deslizador"></span>
                        </label>
                    </div>
                </div>
            </section>

            <!-- Las dos tablas -->
            <section class="contenedor-tablas">

                <!-- Tabla Ingresos -->
                <article class="caja-tabla">
                    <header>
                        <h2 class="titulo-tabla">Ingresos</h2>
                        <div class="linea-separadora"></div>
                        <div class="linea-azul"></div>
                    </header>

                    <table class="tabla-datos">
                        <thead>
                        <tr>
                            <th class="encabezado-tabla">Concepto</th>
                            <th class="encabezado-tabla">Costo</th>
                            <th class="encabezado-tabla">Subido por</th>
                            <th class="encabezado-tabla derecha">Acción</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (count($transaccionesIngresos) > 0): ?>
                            <?php foreach ($transaccionesIngresos as $ingreso): ?>
                                <tr class="fila-tabla">
                                    <td class="celda"><?php echo htmlspecialchars($ingreso['categoria_nombre'] . ' - ' . $ingreso['concepto_nombre']); ?></td>
                                    <td class="celda">S/. <?php echo number_format($ingreso['monto'], 2); ?></td>
                                    <td class="celda"><?php echo htmlspecialchars($ingreso['usuario_nombre']); ?></td>
                                    <td class="celda derecha">
                                        <span class="link-editar" onclick="editarTransaccion(<?php echo $ingreso['id']; ?>, 'ingreso')">Editar</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php for ($i = count($transaccionesIngresos); $i < 4; $i++): ?>
                                <tr class="fila-vacia">
                                    <td class="celda" colspan="4">&nbsp;</td>
                                </tr>
                            <?php endfor; ?>
                        <?php else: ?>
                            <?php for ($i = 0; $i < 4; $i++): ?>
                                <tr class="fila-vacia">
                                    <td class="celda" colspan="4">&nbsp;</td>
                                </tr>
                            <?php endfor; ?>
                        <?php endif; ?>
                        </tbody>
                        <tfoot>
                        <tr class="fila-total">
                            <td class="celda-total">Total</td>
                            <td class="celda-total" colspan="3">S/. <?php echo number_format($totalIngresos, 2); ?></td>
                        </tr>
                        </tfoot>
                    </table>

                    <button class="boton-mas" onclick="abrirModalTransaccion('ingreso')">+</button>
                </article>

                <!-- Tabla Egresos -->
                <article class="caja-tabla">
                    <header>
                        <h2 class="titulo-tabla">Egresos</h2>
                        <div class="linea-separadora"></div>
                        <div class="linea-azul"></div>
                    </header>

                    <table class="tabla-datos">
                        <thead>
                        <tr>
                            <th class="encabezado-tabla">Concepto</th>
                            <th class="encabezado-tabla">Costo</th>
                            <th class="encabezado-tabla">Subido por</th>
                            <th class="encabezado-tabla derecha">Acción</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (count($transaccionesEgresos) > 0): ?>
                            <?php foreach ($transaccionesEgresos as $egreso): ?>
                                <tr class="fila-tabla">
                                    <td class="celda"><?php echo htmlspecialchars($egreso['categoria_nombre'] . ' - ' . $egreso['concepto_nombre']); ?></td>
                                    <td class="celda">S/. <?php echo number_format($egreso['monto'], 2); ?></td>
                                    <td class="celda"><?php echo htmlspecialchars($egreso['usuario_nombre']); ?></td>
                                    <td class="celda derecha">
                                        <span class="link-editar" onclick="editarTransaccion(<?php echo $egreso['id']; ?>, 'egreso')">Editar</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php 
                            $filasFaltantes = max(0, 8 - count($transaccionesEgresos));
                            for ($i = 0; $i < $filasFaltantes; $i++): 
                            ?>
                                <tr class="fila-vacia">
                                    <td class="celda" colspan="4">&nbsp;</td>
                                </tr>
                            <?php endfor; ?>
                        <?php else: ?>
                            <?php for ($i = 0; $i < 8; $i++): ?>
                                <tr class="fila-vacia">
                                    <td class="celda" colspan="4">&nbsp;</td>
                                </tr>
                            <?php endfor; ?>
                        <?php endif; ?>
                        </tbody>
                        <tfoot>
                        <tr class="fila-total">
                            <td class="celda-total">Total</td>
                            <td class="celda-total" colspan="3">S/. <?php echo number_format($totalEgresos, 2); ?></td>
                        </tr>
                        </tfoot>
                    </table>

                    <button class="boton-mas" onclick="abrirModalTransaccion('egreso')">+</button>
                </article>
            </section>

            <!-- Parte de abajo -->
            <footer class="seccion-inferior">
                <button class="boton-balance-semanal" onclick="realizarCorteSemanal()">
                    Corte semanal
                </button>

                <aside class="caja-resumen">
                    <h4 class="titulo-resumen">Resumen del Balance</h4>
                    <div class="linea-resumen">
                        <span class="texto-resumen">Diario</span>
                        <span class="valor-resumen">S/. <?php echo number_format($balances['diario']['balance'], 2); ?></span>
                    </div>
                    <div class="linea-resumen">
                        <span class="texto-resumen">Mensual</span>
                        <span class="valor-resumen">S/. <?php echo number_format($balances['mensual']['balance'], 2); ?></span>
                    </div>
                </aside>
            </footer>

        </main>
    </div>
</div>

<!-- Modal para agregar/editar transacción -->
<div id="modalTransaccion" class="modal" style="display: none;">
    <div class="modal-contenido">
        <span class="cerrar" onclick="cerrarModal()">&times;</span>
        <h2 id="modalTitulo">Agregar Transacción</h2>
        <form id="formTransaccion" action="procesar_transaccion.php" method="POST">
            <input type="hidden" id="transaccion_id" name="transaccion_id">
            <input type="hidden" id="tipo_transaccion" name="tipo_transaccion">
            
            <div class="grupo-campo">
                <label for="concepto_id">Concepto *</label>
                <select id="concepto_id" name="concepto_id" required>
                    <option value="">Seleccione un concepto</option>
                </select>
            </div>

            <div class="grupo-campo">
                <label for="monto">Monto *</label>
                <input type="number" id="monto" name="monto" step="0.01" min="0.01" placeholder="0.00" required>
            </div>

            <div class="botones-modal">
                <button type="button" class="boton-cancelar" onclick="cerrarModal()">Cancelar</button>
                <button type="submit" class="boton-guardar">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Switch de vista familiar/personal
    document.getElementById('switchVista').addEventListener('change', function() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'daily_input.php';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'vista_familiar';
        input.value = this.checked ? '1' : '0';
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    });

    // Función para abrir modal de transacción
    function abrirModalTransaccion(tipo) {
        document.getElementById('modalTransaccion').style.display = 'block';
        document.getElementById('tipo_transaccion').value = tipo;
        document.getElementById('modalTitulo').textContent = 'Agregar ' + (tipo === 'ingreso' ? 'Ingreso' : 'Egreso');
        document.getElementById('transaccion_id').value = '';
        document.getElementById('monto').value = '';
        
        // Cargar conceptos según el tipo
        cargarConceptos(tipo);
    }

    // Función para cargar conceptos
    function cargarConceptos(tipo) {
        fetch('obtener_conceptos.php?tipo=' + tipo)
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('concepto_id');
                select.innerHTML = '<option value="">Seleccione un concepto</option>';
                
                data.forEach(concepto => {
                    const option = document.createElement('option');
                    option.value = concepto.id;
                    option.textContent = concepto.categoria_nombre + ' - ' + concepto.nombre;
                    select.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error al cargar conceptos:', error);
                alert('Error al cargar los conceptos');
            });
    }

    // Función para editar transacción
    function editarTransaccion(id, tipo) {
        // Implementar lógica de edición
        alert('Función de edición en desarrollo. ID: ' + id);
    }

    // Función para cerrar modal
    function cerrarModal() {
        document.getElementById('modalTransaccion').style.display = 'none';
        document.getElementById('formTransaccion').reset();
    }

    // Función para realizar corte semanal
    function realizarCorteSemanal() {
        if (confirm('¿Está seguro de realizar el corte semanal?')) {
            window.location.href = 'procesar_corte_semanal.php';
        }
    }

    // Validación del formulario
    document.getElementById('formTransaccion').addEventListener('submit', function(e) {
        const concepto = document.getElementById('concepto_id').value;
        const monto = document.getElementById('monto').value;

        if (!concepto) {
            e.preventDefault();
            alert('Debe seleccionar un concepto');
            return false;
        }

        if (!monto || monto <= 0) {
            e.preventDefault();
            alert('El monto debe ser mayor a 0');
            return false;
        }
    });
</script>

</body>
</html>
?>