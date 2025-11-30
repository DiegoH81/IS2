<?php

// ------------------------------------------------------------
// UI-04: Registro Diario
// Caso de uso asociado: CU-03 Visualizar registro diario
// ------------------------------------------------------------

session_start();
require_once '../gtr/GTR-04_Validar.php';
require_once '../gtr/GTR-07_GestionarTransaccion.php';
require_once '../gtr/GTR-08_GestionarRegistroDiario.php';
require_once '../gtr/GTR-02_GestionarConcepto.php';

$usuario = Validar::obtenerUsuarioActual();

$fecha_hoy = date('Y-m-d');
$fecha_hace_7_dias = date('Y-m-d', strtotime('-7 days'));

$diaActual = date('l');
$modo = isset($_GET['modo']) ? $_GET['modo'] : 'familiar';

// Procesar creación de transacción
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_transaccion'])) {
    $concepto_id = $_POST['concepto_id'];
    $monto = $_POST['monto'];
    $tipo = $_POST['tipo'];
    
    GestionarTransaccion::crearTransaccionBD(
        $fecha_hoy,
        $monto,
        $tipo,
        $usuario->idFamilia,
        $concepto_id,
        $usuario->idUsuario
    );
    
    //var_dump($usuario);

    // Recargar la página
    header("Location: UI-04_RegistroDiario.php?modo=" . $modo);
    exit;
}

$todosLosConceptos = GestionarConcepto::obtenerConceptosBD($usuario->idFamilia);

// Filtrar solo conceptos habilitados/activos
$conceptosHabilitados = array_filter($todosLosConceptos, function($concepto) {
    return (
        $concepto->estado === 't'
    );
});

// Obtener categorías para mostrar
$categorias = GestionarTransaccion::solicitarCategorias($usuario->idFamilia);

// Indexar categorías
$categoriasIndex = [];
foreach ($categorias as $cat) {
    $categoriasIndex[$cat->idCategoria] = $cat->nombre;
}

if ($modo == 'familiar') {
    $datosRelacionados = GestionarRegistroDiario::vistaFamiliarRegistroDiario($usuario->idFamilia, $fecha_hoy);
    $ingresos = GestionarTransaccion::obtenerIngresoBD($usuario->idFamilia, $fecha_hoy, $fecha_hoy);
    $egresos = GestionarTransaccion::obtenerEgresoBD($usuario->idFamilia, $fecha_hoy, $fecha_hoy);

    $ingresos_7Dias = GestionarTransaccion::obtenerIngresoBD($usuario->idFamilia, $fecha_hace_7_dias, $fecha_hoy);
    $egresos_7Dias = GestionarTransaccion::obtenerEgresoBD($usuario->idFamilia, $fecha_hace_7_dias, $fecha_hoy);
} else {
    $datosRelacionados = GestionarRegistroDiario::vistaUsuarioRegistroDiario($usuario->idFamilia, $fecha_hoy, $usuario->idUsuario);
    $ingresos = GestionarTransaccion::obtenerIngresoPorUsuarioBD($usuario->idUsuario, $fecha_hoy, $fecha_hoy);
    $egresos = GestionarTransaccion::obtenerEgresoPorUsuarioBD($usuario->idUsuario, $fecha_hoy, $fecha_hoy);

    $ingresos_7Dias = GestionarTransaccion::obtenerIngresoPorUsuarioBD($usuario->idUsuario, $fecha_hace_7_dias, $fecha_hoy);
    $egresos_7Dias = GestionarTransaccion::obtenerEgresoPorUsuarioBD($usuario->idUsuario, $fecha_hace_7_dias, $fecha_hoy);
}

//var_dump($datosRelacionados);

$balanceCalculado = $ingresos - $egresos;
$balanceUltimos7Dias = $ingresos_7Dias - $egresos_7Dias;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Diario</title>
    
    <link rel="stylesheet" href="../css/daily_input.css">
    <link rel="stylesheet" href="../css/principal.css">
    <link rel="stylesheet" href="../css/configuracion.css">
    <link rel="stylesheet" href="../css/icons.css">
    <link rel="stylesheet" href="../css/new_mas_form.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    
</head>
<body>
<div class="contenedor-principal">
    <header class="barra-superior">
        <section class="seccion-izquierda">
            <h1 class="titulo-app">On a budget</h1>
        </section>

        <section class="seccion-derecha">
            <h2 class="subtitulo">Registro Diario</h2>

            <div class="info-usuario">
                <span class="nombre-usuario">
                    <?php echo htmlspecialchars($_SESSION['nombre']); ?>
                </span>
                <span class="rol-usuario">
                    <?php echo htmlspecialchars($_SESSION['rol']); ?>
                </span>
            </div>
        </section>
    </header>

    <div class="contenedor-medio">
        <aside class="menu-lateral" id="menuLateral">
            <nav>
                <a class="opcion-menu activa" href="UI-04_RegistroDiario.php">
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
                <a class="opcion-menu" href="UI-16_VisualizarConceptos.php">
                    <i class="icono icono-configuracion"></i>Configuración
                </a>
            </nav>

            <footer class="parte-abajo">
                <a class="opcion-menu" href="UI-01_InicioDeSesion.php">
                    <i class="icono icono-salir"></i>Cerrar sesión
                </a>
            </footer>
        </aside>

        <main class="area-trabajo">
            <section class="controles-superiores">
                <div class="grupo-controles">
                    <div class="contenedor-switch">
                        <span class="texto-switch">PERSONAL / FAMILIAR</span>
                        <label class="boton-switch">
                            <input type="checkbox" id="switchFamilia" name="modo" <?php echo ($modo == 'familiar') ? 'checked' : ''; ?>>
                            <span class="deslizador"></span>
                        </label>
                    </div>                 
                </div>
            </section>

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
                            <th class="encabezado-tabla">Categoría</th>
                            <th class="encabezado-tabla">Costo</th>
                            <th class="encabezado-tabla">Subido por</th>
                            <th class="encabezado-tabla derecha">Acción</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($datosRelacionados as $dato) {
                            if ($dato['tipo'] === 'Ingreso') {
                                $puedeEditar = (
                                    $dato['usuario_id'] == $usuario->idUsuario
                                );

                                echo "<tr class='fila-tabla'>
                                        <td class='celda'>{$dato['concepto']}</td>
                                        <td class='celda'>{$dato['categoria']}</td>
                                        <td class='celda'>S/. {$dato['monto']}</td>
                                        <td class='celda'>{$dato['usuario']}</td>
                                        <td class='celda derecha'>";
                                ?>
                                    <form action="UI-24_EditarTransaccion.php" method="GET">
                                        <input type="hidden" name="idtransaccion" value="<?= $dato['idTransaccion'] ?>">
                                        <input type="hidden" name="origen" value="registro_diario">
                                        <button type="submit" class="link-editar" <?= !$puedeEditar ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : '' ?>>
                                            Editar
                                        </button>
                                    </form>
                                <?php
                                echo "   </td></tr>";
                            }
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr class="fila-total">
                            <td class="celda-total">Total</td>
                            <td class="celda-total" colspan="3">S/. <?php echo number_format($ingresos, 2); ?></td>
                        </tr>
                        </tfoot>
                    </table>
                    
                    <button type="button" class="boton-mas" onclick="abrirModal('Ingreso')">+</button>
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
                            <th class="encabezado-tabla">Categoría</th>
                            <th class="encabezado-tabla">Costo</th>
                            <th class="encabezado-tabla">Subido por</th>
                            <th class="encabezado-tabla derecha">Acción</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($datosRelacionados as $dato) {
                            if ($dato['tipo'] === 'Egreso') {
                                $puedeEditar = (
                                    $dato['usuario_id'] == $usuario->idUsuario
                                );

                                echo "<tr class='fila-tabla'>
                                        <td class='celda'>{$dato['concepto']}</td>
                                        <td class='celda'>{$dato['categoria']}</td>
                                        <td class='celda'>S/. {$dato['monto']}</td>
                                        <td class='celda'>{$dato['usuario']}</td>
                                        <td class='celda derecha'>";
                                ?>
                                    <form action="UI-24_EditarTransaccion.php" method="GET">
                                        <input type="hidden" name="idtransaccion" value="<?= $dato['idTransaccion'] ?>">
                                        <input type="hidden" name="origen" value="registro_diario">
                                        <button type="submit" class="link-editar" <?= !$puedeEditar ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : '' ?>>
                                            Editar
                                        </button>
                                    </form>
                                <?php
                                echo "   </td></tr>";
                            }
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr class="fila-total">
                            <td class="celda-total">Total</td>
                            <td class="celda-total" colspan="3">S/. <?php echo number_format($egresos, 2); ?></td>
                        </tr>
                        </tfoot>
                    </table>

                    <button type="button" class="boton-mas" onclick="abrirModal('Egreso')">+</button>
                </article>
            </section>

            <footer class="seccion-inferior">
                <?php
                    $colorSemanal = ($balanceUltimos7Dias >= 0) ? "color: #00ff5a;" : "color: #ff4d4d;";
                    $colorDiario = ($balanceCalculado >= 0) ? "color: #00ff5a;" : "color: #d01b1bff;";
                ?>

                <?php if ($diaActual == 'Sunday'): ?>
                    <article class="caja-resumen" style="background-color: #88a5d5ff;">
                        <h4 class="titulo-resumen" style="font-weight: bold;">Corte Semanal</h4>
                        <div class="linea-resumen">
                            <span class="texto-resumen" style = "font-weight: bold; color: white;">Semanal</span>
                            <span class="valor-resumen" style="<?php echo $colorSemanal; ?>">S/. <?php echo number_format($balanceUltimos7Dias, 2); ?></span>
                        </div>
                    </article>
                <?php else: ?>
                    <article></article>
                <?php endif; ?>

                <aside class="caja-resumen" style="background-color: #88a5d5ff;">
                    <h4 class="titulo-resumen" style="font-weight: bold;">Resumen del Balance</h4>
                    <div class="linea-resumen">
                        <span class="texto-resumen" style = "font-weight: bold; color: white;">Diario</span>
                        <span class="valor-resumen" style="<?php echo $colorDiario; ?>">S/. <?php echo number_format($balanceCalculado, 2); ?></span>
                    </div>
                </aside>
            </footer>
        </main>
    </div>
</div>

<!--   BOTON MÁS    -->

<!-- Modal para crear transacción -->
<div class="modal-overlay" id="modalTransaccion">
    <div class="modal-contenido">
        <div class="modal-header">
            <h2 class="modal-titulo">
                <i class="fas fa-plus-circle"></i>
                <span id="tituloModal">Nueva Transacción</span>
            </h2>
            <button class="btn-cerrar" onclick="cerrarModal()">&times;</button>
        </div>

        <form method="POST" id="formTransaccion">
            <input type="hidden" name="crear_transaccion" value="1">
            <input type="hidden" name="tipo" id="tipoTransaccion">

            <div class="form-grupo">
                <label for="concepto_id">
                    <i class="fas fa-list"></i> Concepto
                </label>
                <select name="concepto_id" id="concepto_id" required onchange="mostrarCategoria()">
                    <option value="">Seleccione un concepto</option>
                </select>
                <div id="mensajeSinConceptos" class="mensaje-sin-conceptos" style="display: none;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>No hay conceptos disponibles para este tipo</span>
                </div>
            </div>

            <div class="form-grupo" id="contenedorCategoria" style="display: none;">
                <div class="info-categoria">
                    <i class="fas fa-tag"></i>
                    <span>Categoría: <strong id="nombreCategoria"></strong></span>
                </div>
            </div>

            <div class="form-grupo">
                <label for="monto">
                    <i class="fas fa-dollar-sign"></i> Monto (S/.)
                </label>
                <input type="number" name="monto" id="monto" step="0.01" min="0.01" placeholder="0.00" required>
            </div>

            <button type="submit" class="btn-crear-transaccion" id="btnCrear">
                <i class="fas fa-check-circle"></i> 
                <span>Crear Transacción</span>
            </button>
        </form>
    </div>
</div>



<script>
// Datos de conceptos desde PHP - OBTENIDOS DESDE GTR-02
const conceptosData = <?php echo json_encode(array_values($conceptosHabilitados)); ?>;
const categoriasData = <?php echo json_encode($categoriasIndex); ?>;

// Contar por tipo
const countIngresos = conceptosData.filter(c => c.tipo === 'Ingreso').length;
const countEgresos = conceptosData.filter(c => c.tipo === 'Egreso').length;

document.addEventListener('DOMContentLoaded', function() {
    const switchBtn = document.querySelector('#switchFamilia');
    if (switchBtn) {
        switchBtn.addEventListener('change', function() {
            let modo = this.checked ? 'familiar' : 'personal';
            window.location.href = `UI-04_RegistroDiario.php?modo=${modo}`;
        });
    }
});

function abrirModal(tipo) {
    const modal = document.getElementById('modalTransaccion');
    const titulo = document.getElementById('tituloModal');
    const selectConcepto = document.getElementById('concepto_id');
    const tipoInput = document.getElementById('tipoTransaccion');
    const mensajeSin = document.getElementById('mensajeSinConceptos');
    const btnCrear = document.getElementById('btnCrear');
    
    
    // Configurar título y tipo
    titulo.textContent = tipo === 'Ingreso' ? 'Nuevo Ingreso' : 'Nuevo Egreso';
    tipoInput.value = tipo;
    
    // Limpiar y llenar select de conceptos
    selectConcepto.innerHTML = '<option value="">Seleccione un concepto</option>';
    
    // Filtrar conceptos por tipo
    const conceptosFiltrados = conceptosData.filter(concepto => {
        return concepto.tipo === tipo;
    });
    
    
    if (conceptosFiltrados.length === 0) {
        // Mostrar mensaje de error
        mensajeSin.style.display = 'flex';
        selectConcepto.disabled = true;
        btnCrear.disabled = true;
        btnCrear.style.opacity = '0.5';
        btnCrear.style.cursor = 'not-allowed';
    } else {
        // Llenar el select con los conceptos
        mensajeSin.style.display = 'none';
        selectConcepto.disabled = false;
        btnCrear.disabled = false;
        btnCrear.style.opacity = '1';
        btnCrear.style.cursor = 'pointer';
        
        conceptosFiltrados.forEach(concepto => {
            const option = document.createElement('option');
            option.value = concepto.idConcepto;
            option.textContent = concepto.nombre;
            option.dataset.categoria = concepto.idCategoria;
            selectConcepto.appendChild(option);
        });
    }
    
    // Limpiar formulario
    document.getElementById('formTransaccion').reset();
    document.getElementById('contenedorCategoria').style.display = 'none';
    tipoInput.value = tipo;
    
    // Mostrar modal
    modal.classList.add('activo');
}

function cerrarModal() {
    const modal = document.getElementById('modalTransaccion');
    modal.classList.remove('activo');
}

function mostrarCategoria() {
    const select = document.getElementById('concepto_id');
    const contenedor = document.getElementById('contenedorCategoria');
    const nombreCategoria = document.getElementById('nombreCategoria');
    
    if (select.value) {
        const option = select.options[select.selectedIndex];
        const categoriaId = option.dataset.categoria;
        
        if (categoriasData[categoriaId]) {
            nombreCategoria.textContent = categoriasData[categoriaId];
            contenedor.style.display = 'block';
        } else {
            contenedor.style.display = 'none';
        }
    } else {
        contenedor.style.display = 'none';
    }
}

// Cerrar modal al hacer clic fuera
document.getElementById('modalTransaccion').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModal();
    }
});

// Cerrar modal con tecla ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModal();
    }
});
</script>

</body>
</html>