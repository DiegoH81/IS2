<?php
require_once '../DatabaseConnection.php';

class GestionarCategoria {

    public static function obtenerCategorias() {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenercategorias();";
        $result = pg_query($conn, $query);
        return pg_fetch_all($result);
    }
}
?>