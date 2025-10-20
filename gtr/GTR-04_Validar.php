<?php
require_once '../DatabaseConnection.php';
require_once 'GTR-01_GestionarUsuario.php';

// GTR-04 Validar

class Validar {

    /* FUN-15 solicitarValidacionUsuario
        Invoca al GTR-01 Gestionar usuario para verificar si el usaurio ingresado existe */
    public static function solicitarValidacionUsuario($usuario) {
        return GestionarUsuario::validarUsuario($usuario);
    }

    /* FUN-16 solicitarValidacionCredenciales 
        Invoca al GTR-01 Gestionar usuario para verificar si la contraseña ingresada coincide
        con la del usuario ingresado */
    public static function solicitarValidacionCredenciales($usuario, $contrasena) {
        return GestionarUsuario::validarCredenciales($usuario, $contrasena);
    }

    /* FUN-17 solicitarUsuarioDisponible 
        Invoca al GTR-01 Gestionar usuario para verificar si el usuario(nombre de usuario)
        esta en uso */
    public static function solicitarUsuarioDisponible($usuario) {
        return GestionarUsuario::usuarioDisponible($usuario);
    }

    /* FUN-18 solicitarUsuario 
        Invoca al GTR-01 Gestionar usuario para extraer los datos de un usuario especifico 
        segun su id */
    public static function solicitarUsuario($usuario) {
        return GestionarUsuario::obtenerUsuario($usuario);
    }
}
?>
