<?php
require_once '../DatabaseConnection.php';
require_once 'GTR-01_GestionarUsuario.php';

// GTR-04 Validar

class Validar {

    /* FUN-15 validarUsuario 
        Invoca al GTR-01 Gestionar usuario para verificar si el usaurio ingresado existe */
    public static function validarUsuario($usuario) {
        return GestionarUsuario::validarUsuario($usuario);
    }

    /* FUN-16 validarCredenciales 
        Invoca al GTR-01 Gestionar usuario para verificar si la contraseña ingresada coincide
        con la del usuario ingresado */
    public static function validarCredenciales($usuario, $contrasena) {
        return GestionarUsuario::validarCredenciales($usuario, $contrasena);
    }

    /* FUN-17 usuarioDisponible 
        Invoca al GTR-01 Gestionar usuario para verificar si el usuario(nombre de usuario)
        esta en uso */
    public static function usuarioDisponible($usuario) {
        return GestionarUsuario::usuarioDisponible($usuario);
    }

    /* FUN-18 obtenerUsuario 
        Invoca al GTR-01 Gestionar usuario para extraer los datos de un usuario especifico 
        segun su id */
    public static function obtenerUsuario($usuario) {
        return GestionarUsuario::obtenerUsuario($usuario);
    }
}
?>
