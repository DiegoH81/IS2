<?php
require_once '../DatabaseConnection.php';

class GestionarUsuario {

    // FUN-01 obtenerUsuarios
    public static function obtenerrUsuarios() {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerrusuarios();";
        $result = pg_query($conn, $query);
        return pg_fetch_all($result);
    }

    // FUN-02 validarUsuario
    public static function validarUsuario($usuario) {
        $conn = Database::connect();
        $query = "SELECT validarusuario($1)";
        $params = array($usuario);
        $result = pg_query_params($conn, $query, $params);
        $val = pg_fetch_result($result, 0, 0);
        return $val === 't';
    }

    // FUN-03 validarCredenciales
    public static function validarCredenciales($usuario, $contrasena) {
        $conn = Database::connect();
        $query = "SELECT validarcredenciales($1,$2)";
        $params = array($usuario, $contrasena);
        $result = pg_query_params($conn, $query, $params);
        $val = pg_fetch_result($result, 0, 0);
        return $val === 't'; // Devuelve true si es 't', false si es 'f'
    }

    // FUN-04 usuarioDisponible
    public static function usuarioDisponible($usuario) {
        $conn = Database::connect();
        $query = "SELECT usuariodisponible($1)";
        $params = array($usuario);
        $result = pg_query_params($conn, $query, $params);
        $val = pg_fetch_result($result, 0, 0);
        return $val === 't';
    }

    // FUN-05 crearUsuario
    public static function crearUsuario($usuario, $nombre, $contrasena, $contrasena_familiar) {
        $conn = Database::connect();
        $query = "SELECT crearusuario($1,$2,$3,$4)";
        $params = array($usuario, $nombre, $contrasena, $contrasena_familiar);
        $result = pg_query_params($conn, $query, $params);
        return pg_fetch_result($result, 0, 0); // Devuelve ID nuevo o -1 si falla
    }

    // FUN-06 obtenerUsuario
    public static function obtenerUsuario($usuario) {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerusuario($1)";
        $params = array($usuario);
        $result = pg_query_params($conn, $query, $params);
        return pg_fetch_assoc($result); // Devuelve array asociativo con datos
    }
}
?>
