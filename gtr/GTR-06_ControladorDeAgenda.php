<?php
require_once '../DatabaseConnection.php';
require_once 'GTR-01_GestionarUsuario.php';
require_once 'GTR-07_GestionarTransaccion.php';

// ------------------------------------------------------------
// GTR-06 ControladorAgenda
// ------------------------------------------------------------


class ControladorAgenda {

    public static function obtenerConceptosPorFecha($fecha, $idFamilia) {
        $conn = Database::connect();

        $query = "SELECT * FROM obtenerConceptosPorFecha($1, $2);";
        $params = array($fecha, $idFamilia);
        $result = pg_query_params($conn, $query, $params);

        $conceptos = array();

        while ($row = pg_fetch_assoc($result)) {
            $concepto = (object) [
                'tipo' => $row['tipo'],
                'categoria' => $row['categoria'],
                'nombre' => $row['nombre'],
                'monto' => $row['monto'],
                'dias_restantes' => $row['dias_restantes'],
                'proxima_fecha' => $row['proxima_fecha']
            ];

            $conceptos[] = $concepto;
        }

        return $conceptos;
    }

    /* FUN-64 obtenerProyeccionIngresos 
        Obtiene las proyecciones de ingresos de todo el año de una familia especifica */
    public static function obtenerProyeccionIngresos($idFamilia, $fecha) {
        $conn = Database::connect();

        $query = "SELECT hallar_proyeccion_ingresos($1, $2);";
        $params = array($idFamilia, $fecha);
        $result = pg_query_params($conn, $query, $params);

        $row = pg_fetch_assoc($result);
        return $row['hallar_proyeccion_ingresos'];
    }

    /* FUN-65 obtenerProyeccionEgresos 
        Obtiene las proyecciones de egresos de todo el año de una familia especifica */
    public static function obtenerProyeccionEgresos($idFamilia, $fecha) {
        $conn = Database::connect();

        $query = "SELECT hallar_proyeccion_egresos($1, $2);";
        $params = array($idFamilia, $fecha);
        $result = pg_query_params($conn, $query, $params);

        $row = pg_fetch_assoc($result);
        return $row['hallar_proyeccion_egresos'];
    }
}
?>
