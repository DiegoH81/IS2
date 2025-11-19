<?php
require_once '../DatabaseConnection.php';

// TAB-0 Familia
class Familia {

    public $idFamilia;
    public $codigoFamilia;
    public $nombreFamilia;
    public $estado;

    public function __construct($idFamilia = null, $codigoFamilia = null, $nombreFamilia = null, $estado = true) {
        $this->idFamilia = $idFamilia;
        $this->codigoFamilia = $codigoFamilia;
        $this->nombreFamilia = $nombreFamilia;
        $this->estado = $estado;
    }

     /* FUN-38 existeContrasenaFamiliar
        Verifica si ya esta en uso la contraseña familiar en la base de datos */
    public static function existeContrasenaFamiliar($contrasena_familiar) {
        $conn = Database::connect();
        $query = "SELECT existeContrasenaFamiliar($1);";
        $params = array($contrasena_familiar);
        $result = pg_query_params($conn, $query, $params);
        $row = pg_fetch_row($result);
        return $row[0]; // Retorna true o false
    }


    /* FUN-39 crearFamilia
        Inserta un nuevo grupo familiar en la base de dato */
    public static function crearFamilia($nombre_familia, $codigo_familiar) {
        $conn = Database::connect();

        $query = "SELECT crearFamilia($1, $2);";
        $params = array($codigo_familiar, $nombre_familia);
        $result = pg_query_params($conn, $query, $params);
        $row = pg_fetch_row($result);
        return $row[0]; // Retorna el id de la nueva familia
    }


    public static function obtenerFamiliaPorCodigo($codigo_familiar) {
        $conn = Database::connect();

        // Ejecutar la consulta SQL
        $query = "SELECT obtenerFamiliaPorCodigo($1);";
        $params = array($codigo_familiar);
        $result = pg_query_params($conn, $query, $params);
        
        // Verificar si la consulta devolvió alguna fila
        if ($result) {
            $row = pg_fetch_row($result);
            
            // Si no se encontró ninguna fila, retornar -1
            if (!$row || $row[0] === null) {
                return -1;  // Retorna -1 si no se encuentra ningún resultado
            }
            
            // Si se encontró un resultado, retornar el valor
            return $row[0];
        } else {
            // Si la consulta falló por alguna razón, retornar -1
            return -1;
        }
    }
}
?>
