<?php
require_once '../DatabaseConnection.php';
require_once 'GTR-01_GestionarUsuario.php';

class Validar {

    // FUN- validarUsuario
    public static function validarUsuario($usuario) {
        return GestionarUsuario::validarUsuario($usuario);
    }

    // FUN- validarCredenciales
    public static function validarCredenciales($usuario, $contrasena) {
        return GestionarUsuario::validarCredenciales($usuario, $contrasena);
    }

    // FUN- usuarioDisponible
    public static function usuarioDisponible($usuario) {
        return GestionarUsuario::usuarioDisponible($usuario);
    }

    public static function obtenerUsuario($usuario) {
        return GestionarUsuario::obtenerUsuario($usuario);
    }
}
?>