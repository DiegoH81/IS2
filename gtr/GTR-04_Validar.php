<?php
require_once '../DatabaseConnection.php';
require_once 'GTR-01_GestionarUsuario.php';

class Validar {

    // FUN-16 validarUsuario
    public static function validarUsuario($usuario) {
        return GestionarUsuario::validarUsuario($usuario);
    }

    // FUN-17 validarCredenciales
    public static function validarCredenciales($usuario, $contrasena) {
        return GestionarUsuario::validarCredenciales($usuario, $contrasena);
    }

    // FUN-18 usuarioDisponible
    public static function usuarioDisponible($usuario) {
        return GestionarUsuario::usuarioDisponible($usuario);
    }

    // FUN-19 obtenerUsuario
    public static function obtenerUsuario($usuario) {
        return GestionarUsuario::obtenerUsuario($usuario);
    }
}
?>
