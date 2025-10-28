<?php
require_once '../DatabaseConnection.php';
require_once '../entity/categoria.php';

// GTR-09 Gestionar categoria

class GestionarCategoria {

    /* FUN-19 obtenerCategorias
        Extrae toda la informacion de todas las categorias de la base de datos */
    public static function obtenerCategoriasBD($familia_id) {
        return Categoria::obtenerCategorias($familia_id);
    }
    
    /* FUN-24 crearCategoriaBD 
        Permite crear una categoria a la base de datos actual*/
    public static function crearCategoriaBD($nombre, $descripcion, $familia_id, $usuario_id) {
        return Categoria::crearCategoria($nombre, $descripcion, $familia_id, $usuario_id);
    }

    /* FUN-24 actualizarCategoriaBD 
        Permite actualizar una categoria ya existente*/
    public static function actualizarCategoriaBD($id, $nombre, $descripcion) {
        return Categoria::actualizarCategoria($id, $nombre, $descripcion);
    }

    /* FUN-24 editarEstadoCategoriaBD 
        Permite eitar una categoría ya existente*/
    public static function editarEstadoCategoriaBD($id, $estado) {
        return Categoria::editarEstadoCategoria($id, $estado);
    }

    /* FUN-25 obtenerCategoriaIdBD 
        Permite obtener una categoria por id*/
    public static function obtenerCategoriaIdBD($id_categoria) {
        return Categoria::obtenerCategoriaId($id_categoria);
    }

}
?>
