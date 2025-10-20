<?php
require_once '../DatabaseConnection.php';
require_once 'GTR-01_GestionarUsuario.php';
require_once 'GTR-09_GestionarCategoria.php';

// GTR-02 Gestionar concepto

class GestionarConcepto {

    /* FUN-07 obtenerConceptos 
        Extrae la información de todos los conceptos de la base de datos */
    public static function obtenerConceptos() {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerconceptos();";
        $result = pg_query($conn, $query);
        return pg_fetch_all($result);
    }

    /* FUN-08 solicitarUsuarios
        Invoca al GTR-01 Gestionar usuario para obtener la informacion de todos los usuarios */
    public static function solicitarUsuarios() {
        return GestionarUsuario::obtenerUsuarios();
    }

    /* FUN-09 solicitarCategorias
        Invoca al GTR-09 Gestionar categoria para obtener la informacion de todas las categorias */
    public static function solicitarCategorias() {
        return GestionarCategoria::obtenerCategorias();
    }

    /* FUN-10 relacionarDatos
        Filtra los datos de los usuario, categorias y conceptos para obtener una lista de conceptos
        asociados a un grupo familiar */
    public static function relacionarDatos($usuarioId) {
        $conn = Database::connect();

        $conceptos = self::obtenerConceptos();
        $usuarios = self::obtenerUsuarios();
        $categorias = self::obtenerCategorias();

        $categoriasIndex = [];
        foreach ($categorias as $cat) {
            $categoriasIndex[$cat['id_categoria']] = $cat['nombre'];
        }

        $usuariosIndex = [];
        foreach ($usuarios as $u) {
            $usuariosIndex[$u['id_usuario']] = $u;
        }

        $resultado = [];
        foreach ($conceptos as $c) {
            $usuario_subio = $usuariosIndex[$c['usuario_id']];
            if ($usuario_subio['familia_id'] != $usuariosIndex[$usuarioId]['familia_id']) {
                continue;
            }

            $resultado[] = [
                'id_concepto' => $c['id_concepto'],
                'nombre' => $c['nombre'],
                'tipo' => $c['tipo'],
                'categoria' => $categoriasIndex[$c['categoria_id']] ?? 'Desconocida',
                'subido_por' => $usuario_subio['nombre'],
                'monto' => $c['monto'],
                'periodo' => $c['periodo'],
                'periodicidad' => $c['periodicidad'],
                'estado' => $c['estado'],
                'usuario_id' => $c['usuario_id']
            ];
        }

        return $resultado;
    }

    /* FUN-11 crearConcepto 
        Inserta un nuevo concepto en la base de datos */
    public static function crearConcepto($nombre, $descripcion, $tipo, $monto, $periodo, $periodicidad, $diaInicio, $diaFin, $categoriaId, $usuarioId) {
        $conn = Database::connect();
        $query = "SELECT crearconcepto($1,$2,$3,$4,$5,$6,$7,$8,$9,$10)";
        $params = array($nombre, $descripcion, $tipo, $monto, $periodo, $periodicidad, $diaInicio, $diaFin, $categoriaId, $usuarioId);
        return pg_query_params($conn, $query, $params);
    }

    /* FUN-12 obtenerConcepto
        Extra la informacion de un concepto en especifico segun su id */
    public static function obtenerConcepto($idConcepto) {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerconcepto($1)";
        $params = array($idConcepto);
        $result = pg_query_params($conn, $query, $params);
        return pg_fetch_assoc($result);
    }

    /* FUN-13 editarConcepto
        Actualiza la informacion de un concepto en la base de datos segun su id */
    public static function editarConcepto($idConcepto, $nombre, $descripcion, $tipo, $monto, $periodo, $periodicidad, $diaInicio, $diaFin, $categoriaId, $usuarioId) {
        $conn = Database::connect();
        $query = "SELECT editarconcepto($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11)";
        $params = array($idConcepto, $nombre, $descripcion, $tipo, $monto, $periodo, $periodicidad, $diaInicio, $diaFin, $categoriaId, $usuarioId);
        return pg_query_params($conn, $query, $params);
    }

    /* FUN-14 editarEstadoConcepto
        Actualiza el estado de un concepto en la base de datos segun su id */
    public static function editarEstadoConcepto($idConcepto, $estado) {
        $conn = Database::connect();
        $query = "SELECT editarestadoconcepto($1::int, $2::boolean)";
        $estadoBool = $estado ? 't' : 'f';
        $params = array($idConcepto, $estadoBool);
        return pg_query_params($conn, $query, $params);
    }
}
?>
