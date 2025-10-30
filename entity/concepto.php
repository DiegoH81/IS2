<?php
require_once '../DatabaseConnection.php';

// TAB-04 Concepto
class Concepto {

    public $idConcepto;
    public $nombre;
    public $tipo;
    public $monto;
    public $estado;
    public $periodo;
    public $periodicidad;
    public $fechaInicio;
    public $fechaFin;
    public $idFamilia;
    public $idUsuario;
    public $idCategoria;

    public function __construct($idConcepto = null, $nombre = null, $tipo = null, $monto = null, $estado = true, $periodo = null, $periodicidad = null, $fechaInicio = null, $fechaFin = null, $idFamilia = null, $idUsuario = null, $idCategoria = null) {
        $this->idConcepto = $idConcepto;
        $this->nombre = $nombre;
        $this->tipo = $tipo;
        $this->monto = $monto;
        $this->estado = $estado;
        $this->periodo = $periodo;
        $this->periodicidad = $periodicidad;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->idFamilia = $idFamilia;
        $this->idUsuario = $idUsuario;
        $this->idCategoria = $idCategoria;
    }

    /* FUN-33 obtenerConceptosBD 
    Extrae la información de todos los conceptos de la base de datos */
    public static function obtenerConceptos($familia_id) {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerConceptos($1);";
        $params = array($familia_id);
        $result = pg_query_params($conn, $query, $params);
        $rows = pg_fetch_all($result);

        $conceptos = [];
        if ($rows) {
            foreach ($rows as $row) {
                $conceptos[] = new Concepto(
                    $row['id_concepto'],
                    $row['nombre'],
                    $row['tipo'],
                    $row['monto'],
                    $row['estado'],
                    $row['periodo'],
                    $row['periodicidad'],
                    null,
                    null,
                    null,
                    $row['usuario_id'],
                    $row['categoria_id']
                );
            }
        }

        return $conceptos;
    }

    /* FUN-34 crearConceptoBD 
        Inserta un nuevo concepto en la base de datos */
    public static function crearConcepto(
    $nombre, 
    $tipo, 
    $monto, 
    $periodo, 
    $periodicidad, 
    $fecha_inicio, 
    $fecha_fin, 
    $familia_id, 
    $usuario_id, 
    $categoria_id
    ) {
        // Conectar a la base de datos
        $conn = Database::connect();

        // Formatear fechas como YYYY-MM-DD
        $fecha_inicio = date('Y-m-d', strtotime($fecha_inicio));
        $fecha_fin    = date('Y-m-d', strtotime($fecha_fin));

        // Preparar la consulta
        $query = "SELECT crearConcepto($1, $2, $3, $4, $5, $6, $7, $8, $9, $10);";
        $params = [
            $nombre,
            $tipo,
            (float)$monto,
            (int)$periodo,
            $periodicidad,
            $fecha_inicio,
            $fecha_fin,
            (int)$familia_id,
            (int)$usuario_id,
            (int)$categoria_id
        ];

        // Ejecutar la consulta
        $result = pg_query_params($conn, $query, $params);
    }

    /* FUN-35 obtenerConceptoBD
   Extrae la información de un concepto específico según su id */
    public static function obtenerConcepto($idConcepto) {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerConceptoPorId($1)";
        $params = array($idConcepto);
        $result = pg_query_params($conn, $query, $params);
        $row = pg_fetch_assoc($result);

        if (!$row) {
            return null; // No se encontró el concepto
        }
        
        
        // Crear y retornar un objeto Concepto
        return new Concepto(
            $row['id_concepto'],
            $row['nombre'],
            $row['tipo'],
            $row['monto'],
                    null,
            $row['periodo'],
            $row['periodicidad'],
            $row['fechainicio'],
            $row['fechafin'],
            null,
            $row['usuario_id'],
            $row['categoria_id']
        );
    }

    /* FUN-36 editarConceptoBD
        Actualiza la informacion de un concepto en la base de datos segun su id */
    public static function editarConcepto($id_concepto, $nombre, $tipo, $monto, $periodo, $periodicidad, $fecha_inicio, $fecha_fin, $p_id_categoria ) {
        $conn = Database::connect();
        $fecha_inicio = date('Y-m-d', strtotime($fecha_inicio));
        $fecha_fin    = date('Y-m-d', strtotime($fecha_fin));

        $query = "SELECT editarConcepto($1, $2, $3, $4, $5, $6, $7, $8, $9);";
        $params = array($id_concepto, $nombre, $tipo, $monto, $periodo, $periodicidad, $fecha_inicio, $fecha_fin, $p_id_categoria);
        $result = pg_query_params($conn, $query, $params);
    
        if (!$result) {
            error_log("Error al editar concepto: " . pg_last_error($conn));
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
        $params = array($id_concepto, $estadoBool);

        return pg_query_params($conn, $query, $params);
    }

}
?>
