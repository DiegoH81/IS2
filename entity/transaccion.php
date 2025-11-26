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

        // Relacionar datos a la query
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

        return $transacciones;
    }

    /* FUN-53 obtenerTransaccionesPorFamilia
        Obtiene todas las transacciones de una familia */
    public static function obtenerTransaccionesPorFamilia($idFamilia) {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerTransaccionesPorFamilia($1);";

        // Relacionar datos a la query
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
        $query = "SELECT obtenerBalance($1, $2, $3);";

        // Relacionar datos a la query
        $params = array($idFamilia, $fecha_inicio, $fecha_fin);
        $result = pg_query_params($conn, $query, $params);
        
        $row = pg_fetch_row($result);
        
        return $row[0]; // (ingresos - egresos)
    }

    /* FUN-55 obtenerIngreso
        Obtiene los ingresos entre un rango de fechas */
    public static function obtenerIngreso($idFamilia, $fecha_inicio, $fecha_fin) {
        $conn = Database::connect();
        $query = "SELECT obtenerIngreso($1, $2, $3);";

        // Relacionar datos a la query
        $params = array($idFamilia, $fecha_inicio, $fecha_fin);
        $result = pg_query_params($conn, $query, $params);
        
        $row = pg_fetch_row($result);
        
        return $row[0];
    }

    /* FUN-56 obtenerEgreso
        Obtiene el egreso entre un rango de fechas */
    public static function obtenerEgreso($idFamilia, $fecha_inicio, $fecha_fin) {
        $conn = Database::connect();
        $query = "SELECT obtenerEgreso($1, $2, $3);";

        // Relacionar datos a la query
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

        // Relacionar datos a la query
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

        // Relacionar datos a la query
        $params = array($idUsuario, $fecha_inicio, $fecha_fin);
        $result = pg_query_params($conn, $query, $params);
        
        $row = pg_fetch_row($result);
        
        return $row[0];
    }


    /* FUN-81 hallarProyeccionIngresos
        Nos permite hallar la proyeccion esperada de ingresos a partir  de una fecha,
        para lo que resta del año para una familia */
    public static function hallarProyeccionIngresos($idFamilia, $fecha)
    {
        $conn = Database::connect();

        $query = "SELECT hallar_proyeccion_ingresos($1, $2);";
        $params = array($idFamilia, $fecha);
        $result = pg_query_params($conn, $query, $params);

        $row = pg_fetch_assoc($result);
        return $row['hallar_proyeccion_ingresos'];
    }

    /* FUN-82 obtenerProyeccionEgresos
        Nos permite hallar la proyeccion esperada de egresos a partir  de una fecha,
        para lo que resta del año para una familia */
    public static function obtenerProyeccionEgresos($idFamilia, $fecha)
    {
        $conn = Database::connect();

        $query = "SELECT hallar_proyeccion_egresos($1, $2);";
        $params = array($idFamilia, $fecha);
        $result = pg_query_params($conn, $query, $params);

        $row = pg_fetch_assoc($result);
        return $row['hallar_proyeccion_egresos'];
    }
    
}
?>
