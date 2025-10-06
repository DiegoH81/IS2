<?php
// Procesador de transacciones
// Maneja la creación y edición de transacciones desde daily_input.php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../clases/GestionarRegistroDiario.php";

$gestionarRegistro = new GestionarRegistroDiario($conn);
$usuarioId = $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conceptoId = intval($_POST['concepto_id'] ?? 0);
    $monto = floatval($_POST['monto'] ?? 0);
    $tipoTransaccion = $_POST['tipo_transaccion'] ?? '';
    $transaccionId = intval($_POST['transaccion_id'] ?? 0);

    // Validar campos
    $errores = $gestionarRegistro->validarCampos($conceptoId, $monto);

    if (!empty($errores)) {
        $_SESSION['error'] = implode('<br>', $errores);
        header("Location: daily_input.php");
        exit;
    }

    if ($transaccionId > 0) {
        // Editar transacción existente
        $resultado = $gestionarRegistro->editarTransaccion($transaccionId, $conceptoId, $monto, $usuarioId);
    } else {
        // Crear nueva transacción
        $resultado = $gestionarRegistro->crearTransaccion($conceptoId, $monto, $usuarioId);
    }

    if ($resultado['success']) {
        $_SESSION['success'] = $resultado['mensaje'];
    } else {
        $_SESSION['error'] = $resultado['mensaje'];
    }

    header("Location: daily_input.php");
    exit;
} else {
    header("Location: daily_input.php");
    exit;
}
?>