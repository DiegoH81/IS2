<?php
/* FUN-49 filtrarConceptosPorBusqueda
        Permite filtrar conceptos por la barra de búsqueda*/
function filtrarConceptosPorBusqueda($familiaId, $cadena) {
    $conceptos = GestionarConcepto::relacionarDatos($familiaId);

    // Línea A: Validación de condiciones iniciales
    if (empty($cadena) || empty($conceptos)) {
        // C0: No hay cadena o no hay conceptos → retornar array vacío
        return [];
    }

    // Línea B y C: Aplicar filtro con predicado OR sobre los campos
    return array_filter($conceptos, function ($c) use ($cadena) {
        return stripos($c['concepto'], $cadena) !== false ||
               stripos($c['categoria'], $cadena) !== false ||
               stripos($c['tipo'], $cadena) !== false ||
               stripos($c['subido_por'], $cadena) !== false;
    });
}
?>

<?php

// ------------------------------------------------------------
// UI-16: Visualizar conceptos
// Caso de uso asociado: CU-09 Gestionar conceptos
// ------------------------------------------------------------

session_start();
require_once '../gtr/GTR-02_GestionarConcepto.php';

// Paso 7 del CU-09: La interfaz presenta el campo de búsqueda.

// Capturar la búsqueda si existe
$cadena = isset($_GET['cadena']) ? $_GET['cadena'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['concepto_id'], $_POST['estado'])) {
    $idConcepto = intval($_POST['concepto_id']);
    // Asegurar conversión correcta: cualquier valor > 0 es true
    $estado = (intval($_POST['estado']) === 1);
    /*  Invoca la funcion editarEstadoConcepto del GTR-02 Gestionar concepto para actualizar
        el estado de un concepto segun su id */
    
    //var_dump($idConcepto);

    $resultado = GestionarConcepto::editarEstadoConceptoBD($idConcepto, $estado);

    //var_dump($idConcepto);

    if(isset($_POST['ajax'])) {
        // Verificar si la consulta fue exitosa
        header('Content-Type: application/json');
        if($resultado) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al actualizar']);
        }
        exit;
    }
}


//<!-- Paso 2-5 del CU-09: Se relacionan los datos, el GTR-02 va a llamar a GTR-01 Gestionar Usuario,
//                         GTR-10 Gestionar Familia, GTR-09 Gestionar Categoria. -->
$usuarioId = $_SESSION['id_usuario'];
$familiaId = $_SESSION['familia_id'];
if ($cadena !== '') {
    $conceptos = filtrarConceptosPorBusqueda($familiaId, $cadena);
} else {
    $conceptos = GestionarConcepto::relacionarDatos($familiaId);
    // var_dump($conceptos);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuracion</title>

    <!-- CSS principal -->
    <link rel="stylesheet" href="../css/principal.css">
    <link rel="stylesheet" href="../css/configuracion.css">
    <!-- CSS de íconos -->
    <link rel="stylesheet" href="../css/icons.css">

    <link rel="stylesheet" href="../css/modal.css">
    <link rel="stylesheet" href="../css/busqueda.css">
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
                <span class="nombre-usuario"><?= htmlspecialchars($_SESSION['nombre']) ?></span>
                <span class="rol-usuario"><?= htmlspecialchars($_SESSION['rol']) ?></span>
            </div>
        </section>
    </header>

    <div class="contenedor-medio">
        <!-- Menú lateral -->
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

        <!-- Área principal -->
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
                </nav>
            </aside>
            

            <!-- Paso 6 del CU-09: La UI-16 presenta la lista de conceptos. -->
            <section class="contenedor-tablas">
                <article class="tabla">
                    <!-- Paso 8 del CU-09: Mostrar opción de Crear concepto. -->
                    <header>
                        <div class="encabezado-tabla-superior">
                            <form method="GET" action="UI-16_VisualizarConceptos.php" class="form-busqueda">
                                <input 
                                    type="text" 
                                    name="cadena" 
                                    placeholder="Buscar..." 
                                    value="<?= htmlspecialchars($cadena) ?>"
                                    class="input-busqueda">
                                <button type="submit" class="boton-buscar" style="background-color: #4A5568;">Buscar</button>
                                <?php if ($cadena !== ''): ?>
                                    <a href="UI-16_VisualizarConceptos.php" class="boton-limpiar">Limpiar</a>
                                <?php endif; ?>
                            </form>

                            <a href="UI-17_CrearConcepto.php" class="boton-crear">Crear concepto</a>
                        </div>
                        <h2 class="titulo-tabla">Configuración conceptos</h2>
                        <div class="linea-separadora"></div>
                        <div class="linea-azul"></div>
                    </header>

                    <table class="tabla-datos" >
                        <thead>
                            <tr>
                                <th class="encabezado-tabla">Concepto</th>
                                <th class="encabezado-tabla">Categoría</th>
                                <th class="encabezado-tabla">Tipo</th>
                                <th class="encabezado-tabla">Subido por</th>
                                <th class="encabezado-tabla">Costo</th>
                                <th class="encabezado-tabla">Periodo</th>
                                <th class="encabezado-tabla">Estado</th>
                                <th class="encabezado-tabla">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($conceptos && count($conceptos) > 0): ?>
                            <?php foreach ($conceptos as $c): ?>
                                <tr class="fila-tabla" id="fila-<?= $c['concepto_id'] ?>">
                                    <td class="celda"><?= htmlspecialchars($c['concepto']) ?></td>
                                    <td class="celda"><?= htmlspecialchars($c['categoria']) ?></td>
                                    <td class="celda"><?= htmlspecialchars($c['tipo']) ?></td>
                                    <td class="celda"><?= htmlspecialchars($c['subido_por']) ?></td>
                                    <td class="celda">S/. <?= number_format($c['costo'], 2) ?></td>
                                    <td class="celda"><?= htmlspecialchars($c['periodicidad']) ?></td>

                                    <td class="celda celda-estado">
                                        <?php
                                            // Convertimos el estado a booleano
                                            $estadoBool = ($c['estado'] === 'Habilitado'); 
                                            $estadoTexto = $estadoBool ? 'Habilitado' : 'Deshabilitado';
                                            $estadoValor = $estadoBool ? '1' : '0';

                                            // Permiso: admin familiar o creador
                                            $puedeCambiarEstado = ($_SESSION['rol'] === 'Administrador familiar') 
                                                || ($_SESSION['id_usuario'] == $c['usuario_id']);
                                        ?>
                                        <button 
                                            type="button" 
                                            class="link-editar"
                                            data-estado="<?= $estadoValor ?>" 
                                            <?= !$puedeCambiarEstado ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : '' ?> 
                                            onclick="abrirModal(<?= $c['concepto_id'] ?>, '<?= $estadoValor ?>', 'concepto')">
                                            <?= $estadoTexto ?>
                                        </button>
                                    </td>


                                    <!-- Paso 10 del CU-09: Mostrar opciones de gestión según el rol. -->
                                    <!-- Paso 10.1/10.2: Si es familiar, solo puede editar los suyos. -->

                                    <td class="celda">
                                        <?php
                                            $puedeEditar = false;
                                            if ($_SESSION['rol'] === 'Administrador familiar' || $_SESSION['id_usuario'] == $c['usuario_id']) {
                                                $puedeEditar = true;
                                            }
                                        ?>
                                        <form action="UI-18_EditarConcepto.php" method="GET">
                                            <input type="hidden" name="id" value="<?= $c['concepto_id'] ?>">
                                            <button type="submit" class="link-editar" <?= !$puedeEditar ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : '' ?>>
                                                Editar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="celda">No se encontraron conceptos.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </article>
            </section>
        </main>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const switchBtn = document.querySelector('.boton-switch input');
        if (switchBtn) {
            switchBtn.addEventListener('change', function() {
                console.log('Modo:', this.checked ? 'Personal' : 'Familiar');
            });
        }
    });
</script>

<!-- UI-19 Modificar Estado del Concepto -->
<div id="modalConfirmar" class="modal" style="display:none;">
    <div class="modal-contenido">
        <p>¿Seguro que desea cambiar el estado del concepto?</p>
        <div class="modal-botones">
            <button id="btnSi">Sí</button>
            <button id="btnNo">No</button>
        </div>
    </div>
</div>
<script src="../js/popup_estado.js"></script>
</body>
</html>
