<?php
require_once '../DatabaseConnection.php';

class GestionarUsuario {

    // ✅ Validar si un usuario existe
    public static function validarUsuario($usuario) {
        $conn = Database::connect();
        $query = "SELECT validarusuario($1)";
        $params = array($usuario);
        $result = pg_query_params($conn, $query, $params);
        return pg_fetch_result($result, 0, 0); // Devuelve true/false
    }

    // ✅ Validar credenciales de usuario
    public static function validarCredenciales($usuario, $contrasena) {
        $conn = Database::connect();
        $query = "SELECT validarcredenciales($1,$2)";
        $params = array($usuario, $contrasena);
        $result = pg_query_params($conn, $query, $params);
        return pg_fetch_result($result, 0, 0); // Devuelve true/false
    }

    // ✅ Verificar si un nombre de usuario está disponible
    public static function usuarioDisponible($usuario) {
        $conn = Database::connect();
        $query = "SELECT usuariodisponible($1)";
        $params = array($usuario);
        $result = pg_query_params($conn, $query, $params);
        return pg_fetch_result($result, 0, 0); // Devuelve true/false
    }

    // ✅ Crear un nuevo usuario familiar
    public static function crearUsuario($usuario, $nombre, $contrasena, $contrasena_familiar) {
        $conn = Database::connect();
        $query = "SELECT crearusuario($1,$2,$3,$4)";
        $params = array($usuario, $nombre, $contrasena, $contrasena_familiar);
        $result = pg_query_params($conn, $query, $params);
        return pg_fetch_result($result, 0, 0); // Devuelve ID nuevo o -1 si falla
    }

    // ✅ Obtener datos completos del usuario
    public static function obtenerUsuario($usuario) {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerusuario($1)";
        $params = array($usuario);
        $result = pg_query_params($conn, $query, $params);
        return pg_fetch_assoc($result); // Devuelve array asociativo con datos
    }
}
?>