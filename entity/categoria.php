<?php
require_once '../DatabaseConnection.php';

// ------------------------------------------------------------
// TAB-05 Categoria
// ------------------------------------------------------------

class Categoria {

    public $idCategoria;
    public $nombre;
    public $descripcion;
    public $estado;
    public $idUsuario;
    public $idFamilia;

    public function __construct($idCategoria = null, $nombre = null, $descripcion = null, $estado = true, $idUsuario = null, $idFamilia = null) {
        $this->idCategoria = $idCategoria;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->estado = $estado;
        $this->idUsuario = $idUsuario;
        $this->idFamilia = $idFamilia;
    }

    /* FUN-28 obtenerCategorias
        Extrae toda la informacion de todas las categorias de la base de datos */
    public static function obtenerCategorias($familia_id) {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerCategorias($1);";

        // Relacionar datos a la query
        $params = array($familia_id);
        $result = pg_query_params($conn, $query, $params);

        $rows = pg_fetch_all($result);
        $categorias = [];

        // Crear cada categoria (objeto)
        if ($rows) {
            foreach ($rows as $r) {
                $categorias[] = new Categoria(
                    $r['idcategoria'],
                    $r['nombre'],
                    $r['descripcion'],
                    $r['estado'],
                    $r['idusuario'],
                );
            }
        }

        return $categorias;
    }
    
    /* FUN-29 crearCategoria
        Permite crear una categoria a la base de datos actual*/
    public static function crearCategoria($nombre, $descripcion, $familia_id, $usuario_id) {
        $conn = Database::connect();
        $query = "SELECT crearCategoria($1, $2, $3, $4);";

        // Relacionar datos a la query
        $params = array($nombre, $descripcion, $familia_id, $usuario_id);
        $result = pg_query_params($conn, $query, $params);
        
        return $result !== false;
    }
    /* FUN-30 actualizarCategoria
        Permite actualizar una categoria ya existente*/
    public static function actualizarCategoria($id, $nombre, $descripcion) {
        $conn = Database::connect();
        $query = "SELECT actualizarCategoria($1, $2, $3);";

        // Relacionar datos a la query
        $params = array($id, $nombre, $descripcion);
        $result = pg_query_params($conn, $query, $params);
        
        return $result !== false;
    }

    /* FUN-31 editarEstadoCategoria
        Permite eitar una categoría ya existente*/
    public static function editarEstadoCategoria($id, $estado) {
        $conn = Database::connect();
        $estadoBool = $estado ? 't' : 'f';
        $query = "SELECT editarEstadoCategoria($1, $2);";

        // Relacionar datos a la query
        $params = array($id, $estadoBool);
        return pg_query_params($conn, $query, $params);
    }


    /* FUN-32 obtenerCategoriaId
        Permite obtener una categoria por id*/
    public static function obtenerCategoriaId($id_categoria) {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerCategoriaPorId($1);";

        // Relacionar datos a la query
        $params = array($id_categoria);
        $result = pg_query_params($conn, $query, $params);


        $data = pg_fetch_assoc($result);
        if (!$data) {
            return null; // No se encontró la categoría
        }

        // Convertir estado (t o f)
        $estado = ($data['estado'] === 't' || $data['estado'] === true);

        // Retonar categoria (objeto)
        return new Categoria(
            $data['idcategoria'],
            $data['nombre'],
            $data['descripcion'],
            $estado,
            $data['idusuario'] ?? null,
            $data['idfamilia'] ?? null
        );
    }
}   
?>
