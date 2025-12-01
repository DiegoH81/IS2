<?php

// ------------------------------------------------------------
// UI-25: Editar transacción
// Caso de uso asociado: CU-11-2 Editar transacción
// ------------------------------------------------------------

session_start();
require_once '../gtr/GTR-07_GestionarTransaccion.php';

if (!isset($_GET['idtransaccion'])) {
    die("No se especificó la transacción");
}

$idTransaccion = $_GET['idtransaccion'];
$familiaId = $_SESSION['familia_id'];

$origen = isset($_GET['origen']) ? $_GET['origen'] : 'visualizar_transacciones';


$transaccion = GestionarTransaccion::obtenerTransaccionPorIdBD($idTransaccion);

if (!$transaccion) {
    die("Transacción no encontrada");
}

//<!-- Paso 6-7 del CU-11-2: La UI-24 Empieza a validar los datos -->
$puedeEditar = ($_SESSION['rol'] === 'Administrador familiar') || ($_SESSION['id_usuario'] == $transaccion->idUsuario);

// Verificar que la transacción tenga menos de 30 días
$fechaTransaccion = new DateTime($transaccion->fecha);
$fechaActual = new DateTime();
$diferenciaDias = $fechaActual->diff($fechaTransaccion)->days;

if ($diferenciaDias > 30) {
    die("No se puede editar una transacción con más de 30 días de antigüedad");
}

if (!$puedeEditar) {
    die("No tiene permisos para editar esta transacción");
}

// Obtener datos necesarios para el formulario
$conceptos = GestionarTransaccion::solicitarConceptos($familiaId);
$usuarios = GestionarTransaccion::solicitarUsuarios($familiaId);

// Filtrar solo conceptos habilitados
$conceptos = array_filter($conceptos, function($c) {
    return $c->estado == true;
});

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'];
    $monto = $_POST['monto'];
    $tipo = $_POST['tipo'];
    $idConcepto = $_POST['concepto'];
    $origenPost = $_POST['origen'];

    $idUsuario = $transaccion->idUsuario;

    //<!-- Paso 8 del CU-11-2: Se actualiza la informacion de la transacción -->
    GestionarTransaccion::editarTransaccionBD(
        $idTransaccion,
        $fecha,
        $monto,
        $tipo,
        $familiaId,
        $idConcepto,
        $idUsuario
    );
    
    $_SESSION['mensaje_exito'] = 'Transacción editada exitosamente';

    //<!-- Paso 9 del CU-11-2: La UI-24 nos redirige a las interfaces pertinentes -->
    if ($origenPost === 'registro_diario') {
        header("Location: UI-04_RegistroDiario.php");
    } else {
        header("Location: UI-23_VisualizarTransaccion.php");
    }
    exit;
}

?>


<!-- Paso 1 del CU-11-2: La UI-24 se carga y presenta los campos -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar transacción</title>
    <link rel="stylesheet" href="../css/principal.css">
    <link rel="stylesheet" href="../css/configuracion.css">
    <link rel="stylesheet" href="../css/icons.css">
</head>
<body>
<div class="contenedor-principal">
    <header class="barra-superior">
        <section class="seccion-izquierda">
            <h1 class="titulo-app">On a budget</h1>
        </section>

        <section class="seccion-derecha">
            <h2 class="subtitulo">Transacciones</h2>

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
                <a class="opcion-menu activa" href="UI-23_VisualizarTransaccion.php">
                    <i class="icono icono-configuracion"></i>Transacciones
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
                    <a class="opcion-submenu" href="UI-16_VisualizarConceptos.php">
                        <i></i>Conceptos
                    </a>
                    <a class="opcion-submenu" href="UI-20_VisualizarCategoria.php">
                        <i></i>Categorías
                    </a>
                    <a class="opcion-submenu activa" href="UI-23_VisualizarTransaccion.php">
                        <i></i>Transacciones
                    </a>
                </nav>
            </aside>
            <section class="contenedor-tablas">
                <article class="tabla">
                    <header>
                        <h2 class="titulo-tabla">Editar transacción</h2>
                        <div class="linea-separadora"></div>
                        <div class="linea-azul"></div>
                    </header>

                    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

                    <!-- Paso 3-4 del CU-11-2: El AC-02 Familiar modifica los campos necesarios -->
                    <form class="form-crear-concepto" method="POST">
                        <input type="hidden" name="origen" value="<?= htmlspecialchars($origen) ?>">

                        <h1 style="text-align: center;">Editando transacción</h1>

                        <div class="campo-formulario">
                            <label for="fecha">Fecha:</label>
                            <input type="date" id="fecha" name="fecha" value="<?= htmlspecialchars($transaccion->fecha) ?>" required>
                        </div>

                        <div class="campo-formulario">
                            <label for="tipo">Tipo:</label>
                            <select id="tipo" name="tipo" required>
                                <option value="">Seleccionar tipo</option>
                                <option value="Ingreso" <?= $transaccion->tipo === 'Ingreso' ? 'selected' : '' ?>>Ingreso</option>
                                <option value="Egreso" <?= $transaccion->tipo === 'Egreso' ? 'selected' : '' ?>>Egreso</option>
                            </select>
                        </div>

                        <div class="campo-formulario">
                            <label for="concepto">Concepto:</label>
                            <select id="concepto" name="concepto" required>
                                <option value="">Seleccionar concepto</option>
                                <?php foreach ($conceptos as $c): ?>
                                    <option value="<?= $c->idConcepto ?>" <?= $transaccion->idConcepto == $c->idConcepto ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c->nombre) ?> (<?= $c->tipo ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="campo-formulario">
                            <label for="monto">Monto (S/):</label>
                            <input type="number" id="monto" name="monto" step="0.01" min="0.01" value="<?= htmlspecialchars($transaccion->monto) ?>" placeholder="0.00" required>
                        </div>

                        <div class="campo-formulario">
                            <label for="usuario">Usuario:</label>
                            <input type="text" id="usuario" name="usuario_display" 
                                   value="<?php 
                                       // Buscar el nombre del usuario
                                       foreach ($usuarios as $u) {
                                           if ($u->idUsuario == $transaccion->idUsuario) {
                                               echo htmlspecialchars($u->nombre);
                                               break;
                                           }
                                       }
                                   ?>" 
                                   readonly 
                                   style="background-color: #f0f0f0; cursor: not-allowed;">
                        </div>

                        <!-- Paso 2 del CU-11-2: La UI-24 se carga y presenta la opcion de Cancelar y Guardar -->
                        <!-- Paso 5 del CU-11-2: El AC-02 Selecciona la opcion guardar -->
                        <div style="text-align:center;">
                            <div class="grupo-botones">
                                <button type="button" class="boton-crear boton-cancelar" onclick="cancelarEdicion('<?= htmlspecialchars($origen) ?>')">Cancelar</button>
                                <button type="submit" class="boton-crear">Guardar</button>
                            </div>
                        </div>

                    </form>
                </article>
            </section>
        </main>
    </div>
</div>

</body><script>
function cancelarEdicion(origen) {
    if (origen === 'registro_diario') {
        window.location.href = 'UI-04_RegistroDiario.php';
    } else {
        window.location.href = 'UI-23_VisualizarTransaccion.php';
    }
}
</script>
</html>