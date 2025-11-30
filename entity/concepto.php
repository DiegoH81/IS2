<?php
require_once '../DatabaseConnection.php';

// ------------------------------------------------------------
// TAB-02 Concepto
// ------------------------------------------------------------

class Concepto {

    public $idConcepto;
    public $nombre;
    public $tipo;
    public $estado;
    public $periodo;
    public $fechaInicio;
    public $idFamilia;
    public $idUsuario;
    public $idCategoria;

    public function __construct($idConcepto = null, $nombre = null, $tipo = null, $estado = true,
                                $periodo = null, $fechaInicio = null,
                                $idFamilia = null, $idUsuario = null, $idCategoria = null) {
        $this->idConcepto = $idConcepto;
        $this->nombre = $nombre;
        $this->tipo = $tipo;
        $this->estado = $estado;
        $this->periodo = $periodo;
        $this->fechaInicio = $fechaInicio;
        $this->idFamilia = $idFamilia;
        $this->idUsuario = $idUsuario;
        $this->idCategoria = $idCategoria;
    }

    /* FUN-33 obtenerConceptos
    Extrae la información de todos los conceptos de la base de datos */
    public static function obtenerConceptos($familia_id) {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerConceptos($1);";

        // Relacionar datos a la query
        $params = array($familia_id);
        $result = pg_query_params($conn, $query, $params);
        $rows = pg_fetch_all($result);


        // Crear cada concepto (objeto)
        $conceptos = [];
        if ($rows) {
            foreach ($rows as $row) {
                $conceptos[] = new Concepto(
                    $row['id_concepto'],
                    $row['nombre'],
                    $row['tipo'],
                    $row['estado'],
                    $row['periodo'],
                    null,
                    null,
                    $row['usuario_id'],
                    $row['categoria_id']
                );
            }
        }

        return $conceptos;
    }

    /* FUN-34 crearConcepto
        Inserta un nuevo concepto en la base de datos */
    public static function crearConcepto(
    $nombre, 
    $tipo, 
    $periodo, 
    $fecha_inicio, 
    $familia_id, 
    $usuario_id, 
    $categoria_id
    ) {
        $conn = Database::connect();
        $fecha_inicio = date('Y-m-d', strtotime($fecha_inicio));

        $query = "SELECT crearConcepto($1, $2, $3, $4, $5, $6, $7);";
        $params = [
            $nombre,
            $tipo,
            (int)$periodo,
            $fecha_inicio,
            (int)$familia_id,
            (int)$usuario_id,
            (int)$categoria_id
        ];
        // Relacionar datos a la query
        $result = pg_query_params($conn, $query, $params);
    }

    /* FUN-35 obtenerConcepto
   Extrae la información de un concepto específico según su id */
    public static function obtenerConcepto($idConcepto) {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerConceptoPorId($1)";

        // Relacionar datos a la query
        $params = array($idConcepto);
        $result = pg_query_params($conn, $query, $params);
        $row = pg_fetch_assoc($result);

        if (!$row) {
            return null; // No se encontró el concepto
        }
        
        // Retornar un nuevo concepto (objeto)
        return new Concepto(
            $row['id_concepto'],
            $row['nombre'],
            $row['tipo'],
            $row['estado'],
            $row['periodo'],
            $row['fechainicio'],
            null,
            $row['usuario_id'],
            $row['categoria_id']
        );
    }

    /* FUN-36 editarConcepto
        Actualiza la informacion de un concepto en la base de datos segun su id */
    public static function editarConcepto($id_concepto, $nombre, $tipo, $periodo, $fecha_inicio, $p_id_categoria ) {
        $conn = Database::connect();
        $fecha_inicio = date('Y-m-d', strtotime($fecha_inicio));

        $query = "SELECT editarConcepto($1, $2, $3, $4, $5, $6);";

        // Relacionar datos a la query
        $params = array($id_concepto, $nombre, $tipo, $periodo, $fecha_inicio, $p_id_categoria);
        $result = pg_query_params($conn, $query, $params);
    
        if (!$result)
        {
            return false;
        }
        return $result;
    }

    /* FUN-37 editarEstadoConcepto
        Actualiza el estado de un concepto en la base de datos segun su id */
    public static function editarEstadoConcepto($id_concepto, $estado) {
        $conn = Database::connect();
        $estadoBool = $estado ? 't' : 'f';
        $query = "SELECT editarEstadoConcepto($1, $2);";

        // Relacionar datos a la query
        $params = array($id_concepto, $estadoBool);

        return pg_query_params($conn, $query, $params);
    }


    /* FUN-80 obtenerConceptosPorFecha 
        Nos permite obtener los conceptos por una fecha, para poder ver cual es el siguiente
        concepto a ser cobrado */
    public static function obtenerConceptosPorFecha($fecha, $idFamilia)
    {
        $conn = Database::connect();

        $query = "SELECT * FROM obtenerConceptosPorFecha($1, $2);";

        // Relacionar datos a la query
        $params = array($fecha, $idFamilia);
        $result = pg_query_params($conn, $query, $params);

        $conceptos = array();
        
        // Relacionar la consulta
        while ($row = pg_fetch_assoc($result)) {
            $concepto = (object) [
                'tipo' => $row['tipo'],
                'categoria' => $row['categoria'],
                'nombre' => $row['nombre'],
                'monto' => $row['monto_promedio'],
                'dias_restantes' => $row['dias_restantes'],
                'proxima_fecha' => $row['proxima_fecha']
            ];

            $conceptos[] = $concepto;
        }

        return $conceptos;
    }
}
?>
