<?php
// Obtener detalle de un concepto específico vía AJAX
// Usado para editar conceptos
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../clases/GestionarConcepto.php";

$gestionarConcepto = new GestionarConcepto($conn);
$usuarioId = $_SESSION['usuario_id'];
$conceptoId = intval($_GET['id'] ?? 0);

if ($conceptoId <= 0) {
    echo json_encode(['error' => 'ID no válido']);
    exit;
}

$concepto = $gestionarConcepto->obtenerConcepto($conceptoId);

if (!$concepto || $concepto['usuario_id'] != $usuarioId) {
    echo json_encode(['error' => 'Concepto no encontrado o sin permisos']);
    exit;
}

echo json_encode($concepto);
?>