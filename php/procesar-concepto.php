<?php
// Procesador de conceptos
// Maneja la creación y edición de conceptos desde configuración
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../clases/GestionarConcepto.php";

$gestionarConcepto = new GestionarConcepto($conn);
$usuarioId = $_SESSION['usuario_id'];
$usuarioNombre = $_SESSION['usuario_nombre'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conceptoId = intval($_POST['concepto_id'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $categoria = intval($_POST['categoria'] ?? 0);
    $tipo = $_POST['tipo'] ?? '';
    $periodo = $_POST['periodo'] ?? '';
    $monto = floatval($_POST['monto'] ?? 0);
    $diaInicio = !empty($_POST['dia_inicio']) ? intval($_POST['dia_inicio']) : null;
    $diaFin = !empty($_POST['dia_fin']) ? intval($_POST['dia_fin']) : null;

    // Validar campos
    $errores = $gestionarConcepto->validarCampos($nombre, $categoria, $tipo, $periodo, $monto, $diaInicio, $diaFin);

    if (!empty($errores)) {
        $_SESSION['error'] = implode('<br>', $errores);
        header("Location: configuracion.php");
        exit;
    }

    if ($conceptoId > 0) {
        // Editar concepto existente
        $resultado = $gestionarConcepto->editarConcepto(
            $conceptoId, $nombre, $categoria, $tipo, $periodo, 
            $monto, $diaInicio, $diaFin, $usuarioNombre, $usuarioId
        );
    } else {
        // Crear nuevo concepto
        $resultado = $gestionarConcepto->crearConcepto(
            $nombre, $categoria, $tipo, $periodo, 
            $monto, $diaInicio, $diaFin, $usuarioNombre, $usuarioId
        );
    }

    if ($resultado['success']) {
        $_SESSION['success'] = $resultado['mensaje'];
    } else {
        $_SESSION['error'] = $resultado['mensaje'];
    }

    header("Location: configuracion.php");
    exit;
} else {
    header("Location: configuracion.php");
    exit;
}
?>