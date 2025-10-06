<?php
// Obtener conceptos vía AJAX
// Retorna conceptos filtrados por tipo para el modal de transacciones
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../clases/GestionarRegistroDiario.php";

$gestionarRegistro = new GestionarRegistroDiario($conn);
$usuarioId = $_SESSION['usuario_id'];
$tipo = $_GET['tipo'] ?? '';

if (!in_array($tipo, ['ingreso', 'egreso'])) {
    echo json_encode(['error' => 'Tipo no válido']);
    exit;
}

$conceptos = $gestionarRegistro->obtenerConceptosTransaccion($usuarioId, $tipo);
echo json_encode($conceptos);
?>