<?php
require_once '../DatabaseConnection.php';

// GTR-09 Gestionar categoria

class GestionarCategoria {

    /* FUN-19 obtenerCategorias
        Extrae toda la informacion de todas las categorias de la base de datos */
    public static function obtenerCategoriasBD($familia_id) {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerCategorias($1);";
        $params = array($familia_id);
        $result = pg_query_params($conn, $query, $params);
        return pg_fetch_all($result);
    }
    
    /* FUN-24 crearCategoriaBD 
        Permite crear una categoria a la base de datos actual*/
    public static function crearCategoriaBD($nombre, $descripcion, $familia_id, $usuario_id) {
        $conn = Database::connect();
        $query = "SELECT crearCategoria($1, $2, $3, $4);";
        $params = array($nombre, $descripcion, $familia_id, $usuario_id);
        $result = pg_query_params($conn, $query, $params);
        return $result !== false;
    }

    /* FUN-24 actualizarCategoriaBD 
        Permite actualizar una categoria ya existente*/
    public static function actualizarCategoriaBD($id, $nombre, $descripcion) {
        $conn = Database::connect();
        $query = "SELECT actualizarCategoria($1, $2, $3);";
        $params = array($id, $nombre, $descripcion);
        $result = pg_query_params($conn, $query, $params);
        return $result !== false;
    }

    /* FUN-24 editarEstadoCategoriaBD 
        Permite eitar una categoría ya existente*/
    public static function editarEstadoCategoriaBD($id, $estado) {
        $conn = Database::connect();
        $estadoBool = $estado ? 't' : 'f';
        $query = "SELECT editarEstadoCategoria($1, $2);";
        $params = array($id, $estadoBool);
        return pg_query_params($conn, $query, $params);
    }

    /* FUN-25 obtenerCategoriaIdBD 
        Permite obtener una categoria por id*/
    public static function obtenerCategoriaIdBD($id_categoria) {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerCategoriaPorId($1);";
        $params = array($id_categoria);
        return pg_query_params($conn, $query, $params);
    }

}
?>
