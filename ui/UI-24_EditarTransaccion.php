<?php

// ------------------------------------------------------------
// UI-25: Editar transacción
// Caso de uso asociado: Editar transacción
// ------------------------------------------------------------

session_start();
require_once '../gtr/GTR-07_GestionarTransaccion.php';

if (!isset($_GET['idtransaccion'])) {
    die("No se especificó la transacción");
}

$idTransaccion = $_GET['idtransaccion'];
$familiaId = $_SESSION['familia_id'];

// Paso 1: Obtener la transacción seleccionada
$transaccion = GestionarTransaccion::obtenerTransaccionPorIdBD($idTransaccion);

if (!$transaccion) {
    die("Transacción no encontrada");
}

// Verificar permisos: Admin familiar o usuario que creó la transacción
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
    // El usuario YA NO se cambia, se mantiene el original
    $idUsuario = $transaccion->idUsuario;

    // Paso 10: Actualizar la transacción
    GestionarTransaccion::editarTransaccionBD(
        $idTransaccion,
        $fecha,
        $monto,
        $tipo,
        $familiaId,
        $idConcepto,
        $idUsuario
    );
    
    // Paso 11: Redirigir con mensaje de éxito
    $_SESSION['mensaje_exito'] = 'Transacción editada exitosamente';
    header("Location: UI-23_VisualizarTransaccion.php");
    exit;
}

?>

<!-- Paso 2-3: La interfaz se carga y presenta los campos pertinentes -->

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
            <section class="contenedor-tablas">
                <article class="tabla">
                    <header>
                        <h2 class="titulo-tabla">Editar transacción</h2>
                        <div class="linea-separadora"></div>
                        <div class="linea-azul"></div>
                    </header>

                    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

                    <!-- Paso 8-9: Se validan los datos y se comprueba que no hayan campos vacíos -->
                    <form class="form-crear-concepto" method="POST">

                        <h1 style="text-align: center;">Editando transacción</h1>

                        <!-- Paso 4: Fecha de la transacción -->
                        <div class="campo-formulario">
                            <label for="fecha">Fecha:</label>
                            <input type="date" id="fecha" name="fecha" value="<?= htmlspecialchars($transaccion->fecha) ?>" required>
                        </div>

                        <!-- Paso 5: Tipo de transacción -->
                        <div class="campo-formulario">
                            <label for="tipo">Tipo:</label>
                            <select id="tipo" name="tipo" required>
                                <option value="">Seleccionar tipo</option>
                                <option value="Ingreso" <?= $transaccion->tipo === 'Ingreso' ? 'selected' : '' ?>>Ingreso</option>
                                <option value="Egreso" <?= $transaccion->tipo === 'Egreso' ? 'selected' : '' ?>>Egreso</option>
                            </select>
                        </div>

                        <!-- Paso 6: Concepto -->
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

                        <!-- Paso 7: Monto -->
                        <div class="campo-formulario">
                            <label for="monto">Monto (S/):</label>
                            <input type="number" id="monto" name="monto" step="0.01" min="0.01" value="<?= htmlspecialchars($transaccion->monto) ?>" placeholder="0.00" required>
                        </div>

                        <!-- Paso 8: Usuario responsable (NO EDITABLE) -->
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

                        <!-- Paso 9: Botones de acción -->
                        <div style="text-align:center;">
                            <div class="grupo-botones">
                                <button type="button" class="boton-crear boton-cancelar" onclick="window.location.href='UI-23_VisualizarTransaccion.php'">Cancelar</button>
                                <button type="submit" class="boton-crear">Guardar</button>
                            </div>
                        </div>

                    </form>
                </article>
            </section>
        </main>
    </div>
</div>

</body>
</html>