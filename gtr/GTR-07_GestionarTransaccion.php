<?php
require_once '../DatabaseConnection.php';
require_once '../entity/transaccion.php';
require_once 'GTR-01_GestionarUsuario.php';
require_once 'GTR-02_GestionarConcepto.php';
require_once 'GTR-09_GestionarCategoria.php';

// GTR-02 Gestionar concepto

class GestionarTransaccion {


    public static function solicitarCategorias($idFamilia) {
        return GestionarCategoria::obtenerCategoriasBD($idFamilia);
    }

    public static function solicitarConceptos($idFamilia) {
        return GestionarConcepto::obtenerConceptosBD($idFamilia);
    }

    public static function solicitarUsuarios($idFamilia) {
        return GestionarUsuario::obtenerUsuariosBD($idFamilia);
    }

    public static function obtenerTransaccionesRangoBD($idFamilia, $fecha_inicio, $fecha_fin) {
        // Llamamos a la función de la entidad Transaccion
        return Transaccion::obtenerTransaccionesRango($idFamilia, $fecha_inicio, $fecha_fin);
    }

    public static function obtenerTransaccionesPorFamiliaBD($idFamilia) {
        return Transaccion::obtenerTransaccionesPorFamilia($idFamilia);
    }

    
    public static function obtenerBalanceBD($idFamilia, $fecha_inicio, $fecha_fin) {
        // Llamamos a la función de la entidad Transaccion
        return Transaccion::obtenerBalance($idFamilia, $fecha_inicio, $fecha_fin);
    }

    public static function obtenerIngresoBD($idFamilia, $fecha_inicio, $fecha_fin) {
        // Llamamos a la función de la entidad Transaccion
        return Transaccion::obtenerIngreso($idFamilia, $fecha_inicio, $fecha_fin);
    }

    public static function obtenerEgresoBD($idFamilia, $fecha_inicio, $fecha_fin) {
        // Llamamos a la función de la entidad Transaccion
        return Transaccion::obtenerEgreso($idFamilia, $fecha_inicio, $fecha_fin);
    }

    public static function obtenerIngresoPorUsuarioBD($idUsuario, $fecha_inicio, $fecha_fin) {
        // Llamamos a la función de la entidad Transaccion
        return Transaccion::obtenerIngresoPorUsuario($idUsuario, $fecha_inicio, $fecha_fin);
    }

    public static function obtenerEgresoPorUsuarioBD($idUsuario, $fecha_inicio, $fecha_fin) {
        // Llamamos a la función de la entidad Transaccion
        return Transaccion::obtenerEgresoPorUsuario($idUsuario, $fecha_inicio, $fecha_fin);
    }
}
?>
