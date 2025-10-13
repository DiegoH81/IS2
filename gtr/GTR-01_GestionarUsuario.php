<?php
require_once '../DatabaseConnection.php';

// GTR-01 Gestionar usuario

class GestionarUsuario {

    /* FUN-01 obtenerUsuarios
        Extrae la informacion de todos los usuarios de la base de datos */
    public static function obtenerUsuarios() {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerusuarios();";
        $result = pg_query($conn, $query);
        return pg_fetch_all($result);
    }

    /* FUN-02 validarUsuario
        Verifica si el usuario ingresado existe en la base de datos */
    public static function validarUsuario($usuario) {
        $conn = Database::connect();
        $query = "SELECT validarusuario($1)";
        $params = array($usuario);
        $result = pg_query_params($conn, $query, $params);
        $val = pg_fetch_result($result, 0, 0);
        return $val === 't';
    }

    /* FUN-03 validarCredenciales
        Verifica si la contraseña ingresada coincide con la del usuario ingresado */
    public static function validarCredenciales($usuario, $contrasena) {
        $conn = Database::connect();
        $query = "SELECT validarcredenciales($1,$2)";
        $params = array($usuario, $contrasena);
        $result = pg_query_params($conn, $query, $params);
        $val = pg_fetch_result($result, 0, 0);
        return $val === 't';
    }

    /* FUN-04 usuarioDisponible
        Verifica si el usuario(nombre de usuario) no esta en uso */
    public static function usuarioDisponible($usuario) {
        $conn = Database::connect();
        $query = "SELECT usuariodisponible($1)";
        $params = array($usuario);
        $result = pg_query_params($conn, $query, $params);
        $val = pg_fetch_result($result, 0, 0);
        return $val === 't';
    }

    /* FUN-05 crearUsuario
        Inserta un nuevo usuario en la base de datos */
    public static function crearUsuario($usuario, $nombre, $contrasena, $contrasena_familiar) {
        $conn = Database::connect();
        $query = "SELECT crearusuario($1,$2,$3,$4)";
        $params = array($usuario, $nombre, $contrasena, $contrasena_familiar);
        $result = pg_query_params($conn, $query, $params);
        return pg_fetch_result($result, 0, 0);
    }

    /* FUN-06 obtenerUsuario
        Extrae los datos de un usuario especifico segun su id */
    public static function obtenerUsuario($usuario) {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerusuario($1)";
        $params = array($usuario);
        $result = pg_query_params($conn, $query, $params);
        return pg_fetch_assoc($result);
    }
}
?>
