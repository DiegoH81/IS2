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


// Si hay texto en la barra, filtrar (esto puedes implementarlo en tu función SQL más adelante)
if ($cadena !== '') {
    $conceptos = array_filter(GestionarConcepto::obtenerConceptos(), function ($c) use ($cadena) {
        return stripos($c['nombre'], $cadena) !== false ||
               stripos($c['categoria'], $cadena) !== false;
    });
} else {
    $usuarioId = $_SESSION['usuario_id'];
    $conceptos = GestionarConcepto::obtenerConceptos($usuarioId);
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
                                <tr class="fila-tabla">
                                    <td class="celda"><?= htmlspecialchars($c['nombre']) ?></td>
                                    <td class="celda"><?= htmlspecialchars($c['categoria']) ?></td>
                                    <td class="celda"><?= htmlspecialchars($c['tipo']) ?></td>
                                    <td class="celda"><?= htmlspecialchars($c['subido_por']) ?></td>
                                    <td class="celda">S/. <?= number_format($c['monto'], 2) ?></td>
                                    <td class="celda"><?= htmlspecialchars($c['periodicidad']) ?></td>
                                    <td class="celda"><?= $c['estado'] ?></td>
                                    <td class="celda">

                                        <form action="UI-18_EditarConcepto.php" method="GET">
                                             <!-- Paso 9 del CU-16: Mostrar opciones de gestión según el rol. -->
                                             <!-- Paso 9.1/9.2: Si es familiar, solo puede editar los suyos. -->
                                            <input type="hidden" name="id" value="<?= $c['id_concepto'] ?>">
                                            <button type="submit" class="link-editar">Editar</button>
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

</body>
</html>