<?php
require_once '../DatabaseConnection.php';

// TAB-05 Transaccion
class Transaccion {

    public $idTransaccion;
    public $fecha;
    public $monto;
    public $tipo;
    public $idConcepto;
    public $idFamilia;
    public $idUsuario;  // Añadido el campo idUsuario

    // El constructor ahora incluye el idUsuario
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
        
        // Crear un array para almacenar las instancias de Transaccion
        $transacciones = array();
        while ($row = pg_fetch_assoc($result)) {
            // Crear una nueva instancia de Transaccion con los datos obtenidos
            $transaccion = new Transaccion(
                $row['idtransaccion'],  // idTransaccion
                $row['fecha'],          // fecha
                $row['monto'],          // monto
                $row['tipo'],           // tipo
                $row['idconcepto'],     // idConcepto
                            $idFamilia,
                $row['idusuario']       // idUsuario
            );
            
            // Añadir la instancia al array
            $transacciones[] = $transaccion;
        }

        return $transacciones; // Retorna el array de instancias de Transaccion
    }

    /* FUN-53 obtenerTransaccionesPorFamilia
        Obtiene todas las transacciones de una familia */
    public static function obtenerTransaccionesPorFamilia($idFamilia) {
        // Conectar a la base de datos
        $conn = Database::connect();
        
        // Consulta SQL para llamar a la función SQL que devuelve las transacciones por familia, ordenadas por costo
        $query = "SELECT * FROM obtenerTransaccionesPorFamilia($1);";
        $params = array($idFamilia); // El parámetro es solo el idFamilia
        $result = pg_query_params($conn, $query, $params);
        
        // Crear un array para almacenar las instancias de Transaccion
        $transacciones = array();
        
        // Recorrer el resultado de la consulta
        while ($row = pg_fetch_assoc($result)) {
            // Crear una nueva instancia de Transaccion con los datos obtenidos
            $transaccion = new Transaccion(
                $row['idtransaccion'],  // idTransaccion
                $row['fecha'],          // fecha
                $row['monto'],          // monto
                $row['tipo'],           // tipo
                $row['idconcepto'],     // idConcepto
                $idFamilia,             // idFamilia
                $row['idusuario']       // idUsuario
            );
            
            // Añadir la instancia al array
            $transacciones[] = $transaccion;
        }

        // Retorna el array de instancias de Transaccion
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
        
        // Extraemos el total de ingresos de la respuesta
        $row = pg_fetch_row($result);
        
        return $row[0];  // Retorna el total de ingresos
    }

    /* FUN-56 obtenerEgreso
        Obtiene el egreso entre un rango de fechas */
    public static function obtenerEgreso($idFamilia, $fecha_inicio, $fecha_fin) {
        $conn = Database::connect();
        $query = "SELECT obtenerEgreso($1, $2, $3);";  // Llamamos a la función obtenerEgreso en la base de datos
        $params = array($idFamilia, $fecha_inicio, $fecha_fin);  // Añadimos los parámetros idFamilia, fecha_inicio, fecha_fin
        $result = pg_query_params($conn, $query, $params);
        
        // Extraemos el total de egresos de la respuesta
        $row = pg_fetch_row($result);
        
        return $row[0];  // Retorna el total de egresos
    }

    /* FUN-57 obtenerIngresoPorUsuario
        Obtiene los ingresos de un usuario especifico en un rango de fechas*/
    public static function obtenerIngresoPorUsuario($idUsuario, $fecha_inicio, $fecha_fin) {
        $conn = Database::connect();
        $query = "SELECT obtenerIngresoPorUsuario($1, $2, $3);";  // Llamamos a la función obtenerIngresoPorUsuario en la base de datos
        $params = array($idUsuario, $fecha_inicio, $fecha_fin);  // Añadimos los parámetros idUsuario, fecha_inicio, fecha_fin
        $result = pg_query_params($conn, $query, $params);
        
        // Extraemos el total de ingresos de la respuesta
        $row = pg_fetch_row($result);
        
        return $row[0];  // Retorna el total de ingresos
    }

    /* FUN-58 obtenerEgresoPorUsuario
        Obtiene los egresos de un usuario especifico en un rango de fechas*/
    public static function obtenerEgresoPorUsuario($idUsuario, $fecha_inicio, $fecha_fin) {
        $conn = Database::connect();
        $query = "SELECT obtenerEgresoPorUsuario($1, $2, $3);";  // Llamamos a la función obtenerEgresoPorUsuario en la base de datos
        $params = array($idUsuario, $fecha_inicio, $fecha_fin);  // Añadimos los parámetros idUsuario, fecha_inicio, fecha_fin
        $result = pg_query_params($conn, $query, $params);
        
        // Extraemos el total de egresos de la respuesta
        $row = pg_fetch_row($result);
        
        return $row[0];  // Retorna el total de egresos
    }


}
?>
