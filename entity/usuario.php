<?php
require_once '../DatabaseConnection.php';

// TAB-02 Usuario
class Usuario {

    public $idUsuario;
    public $usuario;
    public $nombre;
    public $contrasena;
    public $rol;
    public $estado;
    public $idFamilia;

    public function __construct($idUsuario = null, $usuario = null, $nombre = null, $contrasena = null, $rol = null, $estado = true, $idFamilia = null) {
        $this->idUsuario = $idUsuario;
        $this->usuario = $usuario;
        $this->nombre = $nombre;
        $this->contrasena = $contrasena;
        $this->rol = $rol;
        $this->estado = $estado;
        $this->idFamilia = $idFamilia;
    }


    /* FUN-40 obtenerUsuarios
        Extrae la informacion de todos los usuarios de la base de datos */
   
    public static function obtenerUsuarios($familia_id) {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerUsuarios($1);";
        $params = array($familia_id);
        $result = pg_query_params($conn, $query, $params);
        $rows = pg_fetch_all($result);
        
        $usuarios = [];
        if ($rows) {
            foreach ($rows as $row) {
                $usuarios[] = new Usuario(
                    $row['id_usuario'],
                    $row['usuario'],
                    $row['nombre'],
                    $row['contrasena'] ?? null,
                    $row['rol'],
                    $row['estado'],
                    $row['familia_id']
                );
            }
        }
        return $usuarios;

    }

    /* FUN-41 validarUsuario
        Verifica si el usuario ingresado existe en la base de datos */
    public static function validarUsuario($usuario) {
        $conn = Database::connect();
        $query = "SELECT consultarExistenciaUsuario($1)";
        $params = array($usuario);
        $result = pg_query_params($conn, $query, $params);
        $val = pg_fetch_result($result, 0, 0);
        return $val === 't';
    }

    /* FUN-42 validarCredenciales
        Verifica si la contraseña ingresada coincide con la del usuario ingresado */
    public static function validarCredenciales($usuario, $contrasena) {
        $conn = Database::connect();
        $query = "SELECT validarCredenciales($1,$2)";
        $params = array($usuario, $contrasena);
        $result = pg_query_params($conn, $query, $params);
        $val = pg_fetch_result($result, 0, 0);
        return $val === 't';
    }

    /* FUN-43 usuarioDisponible
        Verifica si el usuario(nombre de usuario) no esta en uso */
    public static function usuarioDisponible($usuario) {
        $conn = Database::connect();
        $query = "SELECT usuariodisponible($1)";
        $params = array($usuario);
        $result = pg_query_params($conn, $query, $params);
        $val = pg_fetch_result($result, 0, 0);
        return $val === 't';
    }

    /* FUN-44 crearUsuario
        Inserta un nuevo usuario en la base de datos */
    public static function crearUsuario($usuario, $nombre, $contrasena, $rol, $familia_id) {
        $conn = Database::connect();
        $query = "SELECT crearUsuario($1, $2, $3, $4, $5);";
        $params = array($usuario, $nombre, $contrasena, $rol, $familia_id);
        pg_query_params($conn, $query, $params);
    }

    /* FUN-45 obtenerUsuario
        Extrae los datos de un usuario especifico segun su id */
    public static function obtenerUsuario($usuario) {
        $conn = Database::connect();
        $query = "SELECT * FROM obtenerUsuario($1)";
        $params = array($usuario);
        $result = pg_query_params($conn, $query, $params);
        $row = pg_fetch_assoc($result);
        
        
        return new Usuario(
            $row['id_usuario'],
            $row['usuario'],
            $row['nombre'],
            $row['contrasena'] ?? null,
            $row['rol'],
            "Habilitado",
            $row['familia_id']
        );
        
    }

    /* FUN-46 actualizarDatosUsuario
        Editar los datos de un usuario existente */

    public static function actualizarDatosUsuario($usuario, $nombre, $contrasena, $rol) {
        $conn = Database::connect();
        $query = "SELECT actualizarDatosUsuario($1, $2, $3, $4);";
        $params = array($usuario, $nombre, $contrasena, $rol);
        $result = pg_query_params($conn, $query, $params);
    }

    /* FUN-47 cambiarEstadoUsuario
        Permite modificar el estado de un usario, para habilitarlo/deshabilitarlo */
    public static function cambiarEstadoUsuario($id, $estado) {
        $conn = Database::connect();
        $estadoBool = $estado ? 't' : 'f';
        $query = "SELECT editarEstadoUsuario($1, $2);";
        $params = array($id, $estadoBool);
        return pg_query_params($conn, $query, $params);
    }

}
?>
