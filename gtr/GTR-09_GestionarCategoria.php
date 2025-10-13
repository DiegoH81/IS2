<?php
require_once '../DatabaseConnection.php';

// GTR-09 Gestionar categoria

class GestionarCategoria {

    /* FUN-19 obtenerCategorias
        Extrae toda la informacion de todas las categorias de la base de datos */
    public static function obtenerCategorias() {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenercategorias();";
        $result = pg_query($conn, $query);
        return pg_fetch_all($result);
    }
}
?>
