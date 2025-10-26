<?php
require_once 'Database.php';

// GTR-10 Gestionar familiar

class GestionarFamilia {

    /* FUN-20 existeContrasenaFamiliarBD 
        Verifica si ya esta en uso la contraseña familiar en la base de datos */
    public static function existeContrasenaFamiliarBD($contrasena_familiar) {
        $conn = Database::connect();
        $query = "SELECT existeContrasenaFamiliar($1);";
        $params = array($contrasena_familiar);
        $result = pg_query_params($conn, $query, $params);
        $row = pg_fetch_row($result);
        return $row[0]; // Retorna true o false
    }


    /* FUN-21 crearFamilia 
        Inserta un nuevo grupo familiar en la base de dato */

    public static function crearFamiliaBD($nombre_familia, $codigo_familiar) {
        $conn = Database::connect();
        $query = "SELECT crearFamilia($1, $2);";
        $params = array($nombre_familia, $codigo_familiar);
        $result = pg_query_params($conn, $query, $params);
        $row = pg_fetch_row($result);
        return $row[0]; // Retorna el id de la nueva familia
    }

}
?>
