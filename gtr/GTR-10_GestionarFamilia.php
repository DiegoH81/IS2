<?php
require_once 'Database.php';

class GestionarFamilia {

    // FUN-12 existeContrasenaFamiliar
    public static function existeContrasenaFamiliar($contrasena) {
        $conn = Database::connect();
        $query = "SELECT existecontrasenafamiliar($1);";
        $params = array($contrasena);
        $result = pg_query_params($conn, $query, $params);
        $row = pg_fetch_row($result);
        return $row[0]; // true o false
    }

    // FUN-11 crearFamilia
    public static function crearFamilia($apellido, $contrasena) {
        $conn = Database::connect();
        $query = "SELECT crearfamilia($1, $2);";
        $params = array($apellido, $contrasena);
        $result = pg_query_params($conn, $query, $params);
        $row = pg_fetch_row($result);
        return $row[0]; // devuelve el nuevo id de la familia
    }

}
?>
