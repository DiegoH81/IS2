<?php
require_once '../DatabaseConnection.php';
require_once '../entity/transaccion.php';
require_once 'GTR-01_GestionarUsuario.php';
require_once 'GTR-02_GestionarConcepto.php';
require_once 'GTR-09_GestionarCategoria.php';

// ------------------------------------------------------------
// GTR-07 Gestionar transaccion
// ------------------------------------------------------------


class GestionarTransaccion {

    
    /* FUN-66 solicitarCategorias 
        Solicita las categorias de una familia al gestor de categorias */
    public static function solicitarCategorias($idFamilia) {
        return GestionarCategoria::obtenerCategoriasBD($idFamilia);
    }

    /* FUN-67 solicitarConceptos 
        Solicita los conceptos de una familia al gestor de conceptos */
    public static function solicitarConceptos($idFamilia) {
        return GestionarConcepto::obtenerConceptosBD($idFamilia);
    }

    /* FUN-68 solicitarUsuarios 
        Solicita los usuarios de una familia al gestor de usuarios */
    public static function solicitarUsuarios($idFamilia) {
        return GestionarUsuario::obtenerUsuariosBD($idFamilia);
    }

    /* FUN-69 obtenerTransaccionesRangoBD 
        Obtiene las transacciones de una familia, dentro de un rango */
    public static function obtenerTransaccionesRangoBD($idFamilia, $fecha_inicio, $fecha_fin) {
        return Transaccion::obtenerTransaccionesRango($idFamilia, $fecha_inicio, $fecha_fin);
    }

    /* FUN-70 obtenerTransaccionesPorFamiliaBD 
        Obtiene las transacciones de una familia sin importar un rango */
    public static function obtenerTransaccionesPorFamiliaBD($idFamilia) {
        return Transaccion::obtenerTransaccionesPorFamilia($idFamilia);
    }

    /* FUN-71 obtenerBalanceBD 
        Obtiene el balance de una familia dentro de un rango de fechas */
    public static function obtenerBalanceBD($idFamilia, $fecha_inicio, $fecha_fin) {
        return Transaccion::obtenerBalance($idFamilia, $fecha_inicio, $fecha_fin);
    }

    /* FUN-72 obtenerIngresoBD 
        Obtiene el ingreso de una familia dentro de un rango de fechas */
    public static function obtenerIngresoBD($idFamilia, $fecha_inicio, $fecha_fin) {
        return Transaccion::obtenerIngreso($idFamilia, $fecha_inicio, $fecha_fin);
    }

    /* FUN-73 obtenerEgresoBD 
        Obtiene el egreso de una familia dentro de un rango de fechas */
    public static function obtenerEgresoBD($idFamilia, $fecha_inicio, $fecha_fin) {
        return Transaccion::obtenerEgreso($idFamilia, $fecha_inicio, $fecha_fin);
    }

    /* FUN-74 obtenerIngresoPorUsuarioBD 
        Obtiene el ingreso de un usuario especifico dentro de un rango de fechas */
    public static function obtenerIngresoPorUsuarioBD($idUsuario, $fecha_inicio, $fecha_fin) {
        return Transaccion::obtenerIngresoPorUsuario($idUsuario, $fecha_inicio, $fecha_fin);
    }

    /* FUN-75 obtenerEgresoPorUsuarioBD 
        Obtiene el egreso de un usuario especifico dentro de un rango de fechas */
    public static function obtenerEgresoPorUsuarioBD($idUsuario, $fecha_inicio, $fecha_fin) {
        return Transaccion::obtenerEgresoPorUsuario($idUsuario, $fecha_inicio, $fecha_fin);
    }



    /* FUN-76 hallarProyeccionIngresosBD 
        Nos permite hallar la proyeccion esperada de ingresos a partir  de una fecha,
        para lo que resta del año para una familia */
    public static function hallarProyeccionIngresosBD($idFamilia, $fecha)
    {
        return Transaccion::hallarProyeccionIngresos($idFamilia, $fecha);
    }

    /* FUN-77 obtenerProyeccionEgresosBD 
        Nos permite hallar la proyeccion esperada de egresos a partir  de una fecha,
        para lo que resta del año para una familia */
    public static function obtenerProyeccionEgresosBD($idFamilia, $fecha)
    {
        return Transaccion::obtenerProyeccionEgresos($idFamilia, $fecha);
    }

    /* FUN-84 crearTransacciónBD
        Se creara una transacción en la base de datos*/

    public static function crearTransaccionBD(
    $fecha,
    $monto,
    $tipo,
    $familia_id,
    $concepto_id,
    $usuario_id )
    {
        Transaccion::crearTransaccion($fecha, $monto, $tipo, $familia_id, $concepto_id, $usuario_id);
    }
}   
?>
