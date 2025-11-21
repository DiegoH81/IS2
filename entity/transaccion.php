<?php
require_once '../DatabaseConnection.php';

// ------------------------------------------------------------
// TAB-04 Transaccion
// ------------------------------------------------------------

class Transaccion {

    public $idTransaccion;
    public $fecha;
    public $monto;
    public $tipo;
    public $idConcepto;
    public $idFamilia;
    public $idUsuario;

    public function __construct($idTransaccion = null, $fecha = null, $monto = null, $tipo = null, $idConcepto = null, $idFamilia = null, $idUsuario = null) {
        $this->idTransaccion = $idTransaccion;
        $this->fecha = $fecha;
        $this->monto = $monto;
        $this->tipo = $tipo;
        $this->idConcepto = $idConcepto;
        $this->idFamilia = $idFamilia;
        $this->idUsuario = $idUsuario;
    }

    
    /* FUN-52 obtenerTransaccionesRango
        Obtiene todas las transacciones en un rango especifico */
    public static function obtenerTransaccionesRango($idFamilia, $fecha_inicio, $fecha_fin) {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerTransaccionesRango($1, $2, $3);";
        $params = array($idFamilia, $fecha_inicio, $fecha_fin);
        $result = pg_query_params($conn, $query, $params);
        
        $transacciones = array();
        while ($row = pg_fetch_assoc($result)) {
            $transaccion = new Transaccion(
                $row['idtransaccion'],
                $row['fecha'],
                $row['monto'],
                $row['tipo'],
                $row['idconcepto'],
                            $idFamilia,
                $row['idusuario']
            );
            
            $transacciones[] = $transaccion;
        }

        return $transacciones; // Retorna el array de instancias de Transaccion
    }

    /* FUN-53 obtenerTransaccionesPorFamilia
        Obtiene todas las transacciones de una familia */
    public static function obtenerTransaccionesPorFamilia($idFamilia) {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerTransaccionesPorFamilia($1);";
        $params = array($idFamilia);
        $result = pg_query_params($conn, $query, $params);
        
        $transacciones = array();
        

        while ($row = pg_fetch_assoc($result)) {
            $transaccion = new Transaccion(
                $row['idtransaccion'],
                $row['fecha'],
                $row['monto'],
                $row['tipo'], 
                $row['idconcepto'],
                $idFamilia,
                $row['idusuario']
            );
            
            $transacciones[] = $transaccion;
        }

        return $transacciones;
    }

    /* FUN-54 obtenerBalance
        Obtiene el balance entre un rango de fechas */
    public static function obtenerBalance($idFamilia, $fecha_inicio, $fecha_fin) {
        $conn = Database::connect();
        $query = "SELECT obtenerBalance($1, $2, $3);";  // Añadimos idFamilia como parámetro
        $params = array($idFamilia, $fecha_inicio, $fecha_fin);  // Añadimos idFamilia al array de parámetros
        $result = pg_query_params($conn, $query, $params);
        
        // Extraemos el balance neto de la respuesta
        $row = pg_fetch_row($result);
        
        return $row[0]; // Retorna el balance neto calculado (ingresos - egresos)
    }

    /* FUN-55 obtenerIngreso
        Obtiene los ingresos entre un rango de fechas */
    public static function obtenerIngreso($idFamilia, $fecha_inicio, $fecha_fin) {
        $conn = Database::connect();
        $query = "SELECT obtenerIngreso($1, $2, $3);";  // Llamamos a la función obtenerIngreso en la base de datos
        $params = array($idFamilia, $fecha_inicio, $fecha_fin);  // Añadimos los parámetros idFamilia, fecha_inicio, fecha_fin
        $result = pg_query_params($conn, $query, $params);
        
        $row = pg_fetch_row($result);
        
        return $row[0];
    }

    /* FUN-56 obtenerEgreso
        Obtiene el egreso entre un rango de fechas */
    public static function obtenerEgreso($idFamilia, $fecha_inicio, $fecha_fin) {
        $conn = Database::connect();
        $query = "SELECT obtenerEgreso($1, $2, $3);";
        $params = array($idFamilia, $fecha_inicio, $fecha_fin);
        $result = pg_query_params($conn, $query, $params);
        
        $row = pg_fetch_row($result);
        
        return $row[0];
    }

    /* FUN-57 obtenerIngresoPorUsuario
        Obtiene los ingresos de un usuario especifico en un rango de fechas*/
    public static function obtenerIngresoPorUsuario($idUsuario, $fecha_inicio, $fecha_fin) {
        $conn = Database::connect();
        $query = "SELECT obtenerIngresoPorUsuario($1, $2, $3);";
        $params = array($idUsuario, $fecha_inicio, $fecha_fin);
        $result = pg_query_params($conn, $query, $params);
        
        $row = pg_fetch_row($result);
        
        return $row[0];
    }

    /* FUN-58 obtenerEgresoPorUsuario
        Obtiene los egresos de un usuario especifico en un rango de fechas*/
    public static function obtenerEgresoPorUsuario($idUsuario, $fecha_inicio, $fecha_fin) {
        $conn = Database::connect();
        $query = "SELECT obtenerEgresoPorUsuario($1, $2, $3);";
        $params = array($idUsuario, $fecha_inicio, $fecha_fin);
        $result = pg_query_params($conn, $query, $params);
        
        $row = pg_fetch_row($result);
        
        return $row[0];
    }


}
?>
