<?php
require_once '../DatabaseConnection.php';

// ------------------------------------------------------------
// TAB-03 Familia
// ------------------------------------------------------------
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
        
        // Relacionar datos a la query
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

        // Relacionar datos a la query
        $params = array($codigo_familiar, $nombre_familia);
        $result = pg_query_params($conn, $query, $params);
        $row = pg_fetch_row($result);
        return $row[0]; // Retorna el id de la nueva familia
    }

    /* FUN-79 obtenerFamiliaPorCodigo 
        Se obtiene una familia en base al codigo familiar */
    public static function obtenerFamiliaPorCodigo($codigo_familiar) {
        $conn = Database::connect();

        $query = "SELECT obtenerFamiliaPorCodigo($1);";

        // Relacionar datos a la query
        $params = array($codigo_familiar);
        $result = pg_query_params($conn, $query, $params);
        

        if ($result) {
            $row = pg_fetch_row($result);
            
            if (!$row || $row[0] === null)
            {
                return -1;  // Retorna -1 si no se encuentra ningún resultado
            }
            
            return $row[0];
        } else {
            return -1;
        }
    }
}
?>
