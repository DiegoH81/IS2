<?php
require_once '../DatabaseConnection.php';
require_once '../entity/usuario.php';

// ------------------------------------------------------------
// GTR-01 Gestionar usuario
// ------------------------------------------------------------


class GestionarUsuario {

    /* FUN-01 obtenerUsuariosBD
        Extrae la informacion de todos los usuarios de la base de datos */
   
    public static function obtenerUsuariosBD($familia_id) {
        return Usuario::obtenerUsuarios($familia_id);
    }


    /* FUN-02 validarUsuarioBD
        Verifica si el usuario ingresado existe en la base de datos */
    public static function validarUsuarioBD($usuario) {
        return Usuario::validarUsuario($usuario);
    }


    /* FUN-03 validarCredencialesBD
        Verifica si la contraseña ingresada coincide con la del usuario ingresado */
    public static function validarCredencialesBD($usuario, $contrasena) {
        return Usuario::validarCredenciales($usuario, $contrasena);
    }

    /* FUN-04 usuarioDisponibleBD
        Verifica si el usuario(nombre de usuario) no esta en uso */
    public static function usuarioDisponibleBD($usuario) {
        return Usuario::usuarioDisponible($usuario);
    }

    /* FUN-05 crearUsuarioBD
        Inserta un nuevo usuario en la base de datos */
    public static function crearUsuarioBD($usuario, $nombre, $contrasena, $rol, $familia_id) {
        Usuario::crearUsuario($usuario, $nombre, $contrasena, $rol, $familia_id);
    }


    /* FUN-06 obtenerUsuarioBD
        Extrae los datos de un usuario especifico segun su id */
    public static function obtenerUsuarioBD($usuario) {
        return Usuario::obtenerUsuario($usuario);
    }


    /* FUN-22 actualizarDatosUsuarioBD 
        Editar los datos de un usuario existente */

    public static function actualizarDatosUsuarioBD($usuario, $nombre, $contrasena, $rol) {
        Usuario::actualizarDatosUsuario($usuario, $nombre, $contrasena, $rol);
    }

    /* FUN-23 cambiarEstadoUsuarioBD 
        Permite modificar el estado de un usario, para habilitarlo/deshabilitarlo */
    public static function cambiarEstadoUsuarioBD($id, $estado) {
        return Usuario::cambiarEstadoUsuario($id, $estado);
    }

}
?>
