<?php
// Obtener categorías vía AJAX
// Retorna categorías filtradas por tipo
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../clases/GestionarCategoria.php";

$gestionarCategoria = new GestionarCategoria($conn);
$tipo = $_GET['tipo'] ?? '';

if (!in_array($tipo, ['ingreso', 'egreso'])) {
    echo json_encode(['error' => 'Tipo no válido']);
    exit;
}

$categorias = $gestionarCategoria->obtenerCategoriasPorTipo($tipo);
echo json_encode($categorias);
?>