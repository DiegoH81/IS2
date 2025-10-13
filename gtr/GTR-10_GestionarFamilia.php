<?php
require_once 'Database.php';

// GTR-10 Gestionar familiar

class GestionarFamilia {

    /* FUN-20 existeContrasenaFamiliar 
        Verifica si ya esta en uso la contraseña familiar en la base de datos */
    public static function existeContrasenaFamiliar($contrasena) {
        $conn = Database::connect();
        $query = "SELECT existecontrasenafamiliar($1);";
        $params = array($contrasena);
        $result = pg_query_params($conn, $query, $params);
        $row = pg_fetch_row($result);
        return $row[0];
    }

    /* FUN-21 crearFamilia 
        Inserta un nuevo grupo familiar en la base de dato */
    public static function crearFamilia($apellido, $contrasena) {
        $conn = Database::connect();
        $query = "SELECT crearfamilia($1, $2);";
        $params = array($apellido, $contrasena);
        $result = pg_query_params($conn, $query, $params);
        $row = pg_fetch_row($result);
        return $row[0];
    }

}
?>
