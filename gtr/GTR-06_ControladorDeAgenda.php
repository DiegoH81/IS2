<?php
require_once '../DatabaseConnection.php';
require_once 'GTR-01_GestionarUsuario.php';
require_once 'GTR-07_GestionarTransaccion.php';

// GTR-06 ControladorAgenda

class ControladorAgenda {

    public static function obtenerConceptosPorFecha($fecha, $idFamilia) {
        // Conexión a la base de datos
        $conn = Database::connect();

        $query = "SELECT * FROM obtenerConceptosPorFecha($1, $2);";
        $params = array($fecha, $idFamilia); // Pasamos los parámetros a la consulta
        $result = pg_query_params($conn, $query, $params); // Ejecutamos la consulta con parámetros

        // Array para almacenar los resultados
        $conceptos = array();

        // Verificamos si la consulta tiene resultados
        while ($row = pg_fetch_assoc($result)) {
            // Cada fila de la consulta es un concepto
            $concepto = (object) [
                'tipo' => $row['tipo'],
                'categoria' => $row['categoria'],
                'nombre' => $row['nombre'],
                'monto' => $row['monto'],
                'dias_restantes' => $row['dias_restantes'],
                'proxima_fecha' => $row['proxima_fecha']
            ];

            // Añadimos el concepto al array
            $conceptos[] = $concepto;
        }

        // Devolvemos el array de conceptos obtenidos
        return $conceptos;
    }

    public static function obtenerProyeccionIngresos($idFamilia, $fecha) {
        // Conexión a la base de datos
        $conn = Database::connect();

        $query = "SELECT hallar_proyeccion_ingresos($1, $2);";
        $params = array($idFamilia, $fecha); // Pasamos los parámetros a la consulta
        $result = pg_query_params($conn, $query, $params); // Ejecutamos la consulta con parámetros

        // Obtener el resultado (proyección de ingresos)
        $row = pg_fetch_assoc($result);
        
        // Retornar el valor de la proyección de ingresos
        return $row['hallar_proyeccion_ingresos'];
    }

    // Función para obtener la proyección de egresos
    public static function obtenerProyeccionEgresos($idFamilia, $fecha) {
        // Conexión a la base de datos
        $conn = Database::connect();

        $query = "SELECT hallar_proyeccion_egresos($1, $2);";
        $params = array($idFamilia, $fecha); // Pasamos los parámetros a la consulta
        $result = pg_query_params($conn, $query, $params); // Ejecutamos la consulta con parámetros

        // Obtener el resultado (proyección de egresos)
        $row = pg_fetch_assoc($result);
        
        // Retornar el valor de la proyección de egresos
        return $row['hallar_proyeccion_egresos'];
    }
}
?>
