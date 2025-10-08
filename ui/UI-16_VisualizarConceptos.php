<?php

// ------------------------------------------------------------
// UI-16: Visualizar conceptos
// Caso de uso asociado: CU-15 Gestionar conceptos
// ------------------------------------------------------------

session_start();
require_once '../gtr/GTR-02_GestionarConcepto.php';

// Paso 7 del CU-16: La interfaz presenta el campo de búsqueda.

// Capturar la búsqueda si existe
$cadena = isset($_GET['cadena']) ? $_GET['cadena'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_concepto'], $_POST['estado'])) {
    $idConcepto = intval($_POST['id_concepto']);
    // Asegurar conversión correcta: cualquier valor > 0 es true
    $estado = (intval($_POST['estado']) === 1);
    
    $resultado = GestionarConcepto::editarEstadoConcepto($idConcepto, $estado);
    
    if(isset($_POST['ajax'])) {
        // Verificar si la consulta fue exitosa
        if($resultado) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al actualizar']);
        }
        exit;
    }
}

// Si hay texto en la barra, filtrar (esto puedes implementarlo en tu función SQL más adelante)
if ($cadena !== '') {
    $conceptos = array_filter(GestionarConcepto::obtenerConceptos(), function ($c) use ($cadena) {
        return stripos($c['nombre'], $cadena) !== false ||
               stripos($c['categoria'], $cadena) !== false;
    });
} else {
    $usuarioId = $_SESSION['usuario_id'];
    $conceptos = GestionarConcepto::relacionarDatos($usuarioId);
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

        <!-- Área principal -->
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
                    <!-- Paso 8 del CU-16: Mostrar opción de Crear concepto. -->
                    <header>
                        <div class="encabezado-tabla-superior">
                            <a href="UI-17_CrearConcepto.php" class="boton-crear">Crear concepto</a>
                        </div>
                        <h2 class="titulo-tabla">Configuración conceptos</h2>
                        <div class="linea-separadora"></div>
                        <div class="linea-azul"></div>
                    </header>

                    <!-- Paso 10 del CU-16: Presentar la lista de conceptos con nombre, tipo, categoría, etc. -->
                    <table class="tabla-datos">
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
                                <tr class="fila-tabla" id="fila-<?= $c['id_concepto'] ?>">
                                    <td class="celda"><?= htmlspecialchars($c['nombre']) ?></td>
                                    <td class="celda"><?= htmlspecialchars($c['categoria']) ?></td>
                                    <td class="celda"><?= htmlspecialchars($c['tipo']) ?></td>
                                    <td class="celda"><?= htmlspecialchars($c['subido_por']) ?></td>
                                    <td class="celda">S/. <?= number_format($c['monto'], 2) ?></td>
                                    <td class="celda"><?= htmlspecialchars($c['periodicidad']) ?></td>

                                    <td class="celda celda-estado">
                                        <?php
                                            $estadoBool = ($c['estado'] === 't');
                                            $estadoTexto = $estadoBool ? 'Habilitado' : 'Deshabilitado';
                                            $puedeCambiarEstado = ($_SESSION['rol'] === 'Administrador familiar') 
                                                || ($_SESSION['rol'] === 'Familiar' && $_SESSION['id_usuario'] == $c['usuario_id']);
                                        ?>
                                        <button 
                                            type="button" 
                                            class="link-editar" 
                                            data-estado="<?= $estadoBool ? '1' : '0' ?>" 
                                            <?= !$puedeCambiarEstado ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : '' ?> 
                                            onclick="abrirModal(<?= $c['id_concepto'] ?>, '<?= $estadoBool ? '1' : '0' ?>')">
                                            <?= $estadoTexto ?>
                                        </button>
                                    </td>

                                    <!-- Paso 9 del CU-16: Mostrar opciones de gestión según el rol. -->
                                    <!-- Paso 9.1/9.2: Si es familiar, solo puede editar los suyos. -->

                                    <td class="celda">
                                        <?php
                                            $puedeEditar = false;
                                            if ($_SESSION['rol'] === 'Administrador familiar') {
                                                $puedeEditar = true;
                                            } elseif ($_SESSION['rol'] === 'Familiar' && $_SESSION['id_usuario'] == $c['usuario_id']) {
                                                $puedeEditar = true;
                                            }
                                        ?>
                                        <form action="UI-18_EditarConcepto.php" method="GET">
                                            <input type="hidden" name="id" value="<?= $c['id_concepto'] ?>">
                                            <button type="submit" class="link-editar" <?= !$puedeEditar ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : '' ?>>
                                                Editar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="celda">No hay conceptos registrados.</td></tr>
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
<!-- Modal de confirmación -->
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