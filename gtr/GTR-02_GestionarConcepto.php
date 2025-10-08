<?php
require_once '../DatabaseConnection.php';

class GestionarConcepto {

    // FUN-06 crearConcepto
    public static function crearConcepto($nombre, $descripcion, $tipo, $monto, $periodo, $periodicidad, $diaInicio, $diaFin, $categoriaId, $usuarioId) {
        $conn = Database::connect();
        $query = "SELECT crearconcepto($1,$2,$3,$4,$5,$6,$7,$8,$9,$10)";
        $params = array($nombre, $descripcion, $tipo, $monto, $periodo, $periodicidad, $diaInicio, $diaFin, $categoriaId, $usuarioId);
        return pg_query_params($conn, $query, $params);
    }

    // FUN-07 obtenerConceptos
    public static function obtenerConceptos($usuarioId) {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerconceptos($1);";
        $params = array($usuarioId);
        $result = pg_query_params($conn, $query, $params);
        return pg_fetch_all($result);
    }

    // FUN-08 obtenerConcepto
    public static function obtenerConcepto($idConcepto) {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerconcepto($1)";
        $params = array($idConcepto);
        $result = pg_query_params($conn, $query, $params);
        return pg_fetch_assoc($result);
    }

    // FUN-09 editarConcepto
    public static function editarConcepto($idConcepto, $nombre, $descripcion, $tipo, $monto, $periodo, $periodicidad, $diaInicio, $diaFin, $categoriaId, $usuarioId) {
        $conn = Database::connect();
        $query = "SELECT editarconcepto($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11)";
        $params = array($idConcepto, $nombre, $descripcion, $tipo, $monto, $periodo, $periodicidad, $diaInicio, $diaFin, $categoriaId, $usuarioId);
        return pg_query_params($conn, $query, $params);
    }

    // FUN-10 editarEstadoConcepto
    public static function editarEstadoConcepto($idConcepto, $estado) {
        $conn = Database::connect();
        $query = "SELECT editarestadoconcepto($1, $2)";
        $params = array($idConcepto, $estado);
        return pg_query_params($conn, $query, $params);
    }
}
?>
