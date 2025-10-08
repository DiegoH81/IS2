<?php
require_once '../DatabaseConnection.php';
require_once 'GTR-01_GestionarUsuario.php';
require_once 'GTR-09_GestionarCategoria.php';

class GestionarConcepto {

    // FUN- obtenerConceptos
    public static function obtenerrConceptos() {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerrconceptos();";
        $result = pg_query($conn, $query);
        return pg_fetch_all($result);
    }

    // FUN- obtenerUsuarios
    public static function obtenerrUsuarios() {
        return GestionarUsuario::obtenerrUsuarios();
    }

    // FUN- obtenerCategorias
    public static function obtenerCategorias() {
        return GestionarCategoria::obtenerCategorias();
    }

    // FUN- relacionarDatos
    public static function relacionarDatos($usuarioId) {
        $conn = Database::connect();

        $conceptos = self::obtenerrConceptos();
        $usuarios = self::obtenerrUsuarios();
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
                continue; // solo conceptos de la misma familia
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


    // FUN-06 crearConcepto
    public static function crearConcepto($nombre, $descripcion, $tipo, $monto, $periodo, $periodicidad, $diaInicio, $diaFin, $categoriaId, $usuarioId) {
        $conn = Database::connect();
        $query = "SELECT crearconcepto($1,$2,$3,$4,$5,$6,$7,$8,$9,$10)";
        $params = array($nombre, $descripcion, $tipo, $monto, $periodo, $periodicidad, $diaInicio, $diaFin, $categoriaId, $usuarioId);
        return pg_query_params($conn, $query, $params);
    }

    // FUN-07 obtenerConceptos
    public static function obtenerConceptos($usuarioId) {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerconceptos($1);";
        $params = array($usuarioId);
        $result = pg_query_params($conn, $query, $params);
        return pg_fetch_all($result);
    }

    // FUN-08 obtenerConcepto
    public static function obtenerConcepto($idConcepto) {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerconcepto($1)";
        $params = array($idConcepto);
        $result = pg_query_params($conn, $query, $params);
        return pg_fetch_assoc($result);
    }

    // FUN-09 editarConcepto
    public static function editarConcepto($idConcepto, $nombre, $descripcion, $tipo, $monto, $periodo, $periodicidad, $diaInicio, $diaFin, $categoriaId, $usuarioId) {
        $conn = Database::connect();
        $query = "SELECT editarconcepto($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11)";
        $params = array($idConcepto, $nombre, $descripcion, $tipo, $monto, $periodo, $periodicidad, $diaInicio, $diaFin, $categoriaId, $usuarioId);
        return pg_query_params($conn, $query, $params);
    }

    // FUN-10 editarEstadoConcepto
    public static function editarEstadoConcepto($idConcepto, $estado) {
        $conn = Database::connect();
        $query = "SELECT editarestadoconcepto($1::int, $2::boolean)";
        // Convertir explícitamente a 't' o 'f' que PostgreSQL entiende como boolean
        $estadoBool = $estado ? 't' : 'f';
        $params = array($idConcepto, $estadoBool);
        return pg_query_params($conn, $query, $params);
    }
}
?>
