<?php
require_once '../DatabaseConnection.php';

// GTR-01 Gestionar usuario

class GestionarUsuario {

    /* FUN-01 obtenerUsuariosBd
        Extrae la informacion de todos los usuarios de la base de datos */
   
    public static function obtenerUsuariosBD($familia_id) {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerUsuarios($1);";
        $params = array($familia_id);
        $result = pg_query_params($conn, $query, $params);
        return pg_fetch_all($result);
    }


    /* FUN-02 validarUsuarioBD
        Verifica si el usuario ingresado existe en la base de datos */
    public static function validarUsuarioBD($usuario) {
        $conn = Database::connect();
        $query = "SELECT consultarExistenciaUsuario($1)";
        $params = array($usuario);
        $result = pg_query_params($conn, $query, $params);
        $val = pg_fetch_result($result, 0, 0);
        return $val === 't';
    }


    /* FUN-03 validarCredencialesBD
        Verifica si la contraseña ingresada coincide con la del usuario ingresado */
    public static function validarCredencialesBD($usuario, $contrasena) {
        $conn = Database::connect();
        $query = "SELECT validarCredenciales($1,$2)";
        $params = array($usuario, $contrasena);
        $result = pg_query_params($conn, $query, $params);
        $val = pg_fetch_result($result, 0, 0);
        return $val === 't';
    }

    /* FUN-04 usuarioDisponibleBD
        Verifica si el usuario(nombre de usuario) no esta en uso */
    public static function usuarioDisponibleBD($usuario) {
        $conn = Database::connect();
        $query = "SELECT usuariodisponible($1)";
        $params = array($usuario);
        $result = pg_query_params($conn, $query, $params);
        $val = pg_fetch_result($result, 0, 0);
        return $val === 't';
    }

    /* FUN-05 crearUsuarioBD
        Inserta un nuevo usuario en la base de datos */
        public static function crearUsuarioBD($usuario, $nombre, $contrasena, $rol, $familia_id) {
        $conn = Database::connect();
        $query = "SELECT crearUsuario($1, $2, $3, $4, $5);";
        $params = array($usuario, $nombre, $contrasena, $rol, $familia_id);
        pg_query_params($conn, $query, $params);
    }


    /* FUN-06 obtenerUsuarioBD
        Extrae los datos de un usuario especifico segun su id */
    public static function obtenerUsuarioBD($usuario) {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerUsuario($1)";
        $params = array($usuario);
        $result = pg_query_params($conn, $query, $params);
        return pg_fetch_assoc($result);
    }


    /* FUN-22 actualizarDatosUsuarioBD 
        Editar los datos de un usuario existente */

    public static function actualizarDatosUsuarioBD($usuario, $nombre, $contrasena, $rol) {
        $conn = Database::connect();
        $query = "SELECT actualizarDatosUsuario($1, $2, $3, $4);";
        $params = array($usuario, $nombre, $contrasena, $rol);
        $result = pg_query_params($conn, $query, $params);
    }

    /* FUN-23 cambiarEstadoUsuarioBD 
        Permite modificar el estado de un usario, para habilitarlo/deshabilitarlo */
    public static function cambiarEstadoUsuarioBD($id, $nuevo_estado) {
        $conn = Database::connect();
        $query = "SELECT cambiarEstadoUsuario($1, $2);";
        $params = array($id, $nuevo_estado);
        pg_query_params($conn, $query, $params);
    }





    // EN DUDA
    public static function solicitarUsuariosEspecificos($ids) {
    $conn = Database::connect();
        // Convertir el array PHP a formato PostgreSQL: {1,2,3}
        $id_array = '{' . implode(',', $ids) . '}';

        $query = "SELECT * FROM obtenerUsuariosPorIds($1);";
        $params = array($id_array);
        $result = pg_query_params($conn, $query, $params);

        return pg_fetch_all($result);
    }

    //NUEVO
    public static function solicitarCategoriasEspecificas($ids) {
        $conn = Database::connect();

        // Convertir array PHP a formato PostgreSQL {1,2,3}
        $id_array = '{' . implode(',', $ids) . '}';

        $query = "SELECT * FROM obtenerCategoriasPorIds($1);";
        $params = array($id_array);
        $result = pg_query_params($conn, $query, $params);

        return pg_fetch_all($result);
    }

    

}
?>
