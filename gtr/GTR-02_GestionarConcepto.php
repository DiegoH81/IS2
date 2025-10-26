<?php
require_once '../DatabaseConnection.php';
require_once 'GTR-01_GestionarUsuario.php';
require_once 'GTR-09_GestionarCategoria.php';

// GTR-02 Gestionar concepto

class GestionarConcepto {

    /* FUN-07 obtenerConceptosBD 
    Extrae la información de todos los conceptos de la base de datos */
    /*
    public static function obtenerConceptosBD() {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerconceptos();";
        $result = pg_query($conn, $query);
        return pg_fetch_all($result);
    }*/
    public static function obtenerConceptosBD($familia_id) {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerConceptos($1);";
        $params = array($familia_id);
        $result = pg_query_params($conn, $query, $params);
        return pg_fetch_all($result);
    }

    /* FUN-08 solicitarUsuarios
        Invoca al GTR-01 Gestionar usuario para obtener la informacion de todos los usuarios */
    public static function solicitarUsuarios($familia_id) {
        return GestionarUsuario::obtenerUsuariosBD($familia_id);
    }

    /* FUN-09 solicitarCategorias
        Invoca al GTR-09 Gestionar categoria para obtener la informacion de todas las categorias */
    public static function solicitarCategorias($familia_id) {
        return GestionarCategoria::obtenerCategoriasBD($familia_id);
    }

    /* FUN-10 relacionarDatos
        Filtra los datos de los usuario, categorias y conceptos para obtener una lista de conceptos
        asociados a un grupo familiar */
    public static function relacionarDatos($familia_id) {
        $conn = Database::connect();

        $conceptos = self::obtenerConceptosBD($familia_id);
        $resultado = [];
        
        if (!empty($conceptos)) {

            $usuarios = self::solicitarUsuarios($familia_id);
            $categorias = self::solicitarCategorias($familia_id);
            
            //var_dump($conceptos);
            //var_dump($usuarios[0]);
            //var_dump($categorias[0]);
    
             // Crear índices para búsquedas rápidas
            $usuariosIndex = [];
            foreach ($usuarios as $u) {
                $usuariosIndex[$u['id_usuario']] = $u['nombre'];
            }
    
            $categoriasIndex = [];
            foreach ($categorias as $cat) {
                $categoriasIndex[$cat['idcategoria']] = $cat['nombre'];
            }
            
            // Construir array resultado
            foreach ($conceptos as $c) {
                $resultado[] = [
                    'concepto_id' => $c['id_concepto'],
                    'concepto'    => $c['nombre'],
                    'categoria'   => $categoriasIndex[$c['categoria_id']] ?? '',
                    'tipo'        => $c['tipo'],
                    'subido_por'  => $usuariosIndex[$c['usuario_id']] ?? '',
                    'usuario_id'  => $c['usuario_id'],
                    'costo'       => $c['monto'],
                    'periodo'     => $c['periodo'],
                    'periodicidad'=> $c['periodicidad'],
                    'estado'      => $c['estado'] ? 'Habilitado' : 'Deshabilitado'
                ];
            }
        }


        return $resultado;
    }

    /* FUN-11 crearConceptoBD 
        Inserta un nuevo concepto en la base de datos */
        /*
    public static function crearConceptoBD($nombre, $descripcion, $tipo, $monto, $periodo, $periodicidad, $diaInicio, $diaFin, $categoriaId, $usuarioId) {
        $conn = Database::connect();
        $query = "SELECT crearconcepto($1,$2,$3,$4,$5,$6,$7,$8,$9,$10)";
        $params = array($nombre, $descripcion, $tipo, $monto, $periodo, $periodicidad, $diaInicio, $diaFin, $categoriaId, $usuarioId);
        return pg_query_params($conn, $query, $params);
    }*/

    public static function crearConceptoBD(
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



    /* FUN-12 obtenerConceptoBD
        Extra la informacion de un concepto en especifico segun su id */
    public static function obtenerConceptoBD($idConcepto) {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerConceptoPorId($1)";
        $params = array($idConcepto);
        $result = pg_query_params($conn, $query, $params);
        return pg_fetch_assoc($result);
    }

    /* FUN-13 editarConceptoBD
        Actualiza la informacion de un concepto en la base de datos segun su id */
        /*
    public static function editarConceptoBD($idConcepto, $nombre, $descripcion, $tipo, $monto, $periodo, $periodicidad, $diaInicio, $diaFin, $categoriaId, $usuarioId) {
        $conn = Database::connect();
        $query = "SELECT editarconcepto($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11)";
        $params = array($idConcepto, $nombre, $descripcion, $tipo, $monto, $periodo, $periodicidad, $diaInicio, $diaFin, $categoriaId, $usuarioId);
        return pg_query_params($conn, $query, $params);
    }*/

    public static function editarConceptoBD($id_concepto, $nombre, $tipo, $monto, $periodo, $periodicidad, $fecha_inicio, $fecha_fin, $p_id_categoria ) {
        $conn = Database::connect();
        $query = "SELECT editarConcepto($1, $2, $3, $4, $5, $6, $7, $8, $9);";
        $params = array($id_concepto, $nombre, $tipo, $monto, $periodo, $periodicidad, $fecha_inicio, $fecha_fin, $p_id_categoria);
        pg_query_params($conn, $query, $params);
    }


    /* FUN-14 editarEstadoConcepto
        Actualiza el estado de un concepto en la base de datos segun su id */
        /*
    public static function editarEstadoConceptoBD($idConcepto, $estado) {
        $conn = Database::connect();
        $query = "SELECT editarestadoconcepto($1::int, $2::boolean)";
        $estadoBool = $estado ? 't' : 'f';
        $params = array($idConcepto, $estadoBool);
        return pg_query_params($conn, $query, $params);
    }
*/
    public static function editarEstadoConceptoBD($id_concepto, $estado) {
        $conn = Database::connect();
        $query = "SELECT editarEstadoConcepto($1, $2);";
        $params = array($id_concepto, $estado);
        pg_query_params($conn, $query, $params);
    }
}
?>
