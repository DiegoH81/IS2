<?php
require_once '../DatabaseConnection.php';
require_once 'GTR-01_GestionarUsuario.php';
require_once 'GTR-07_GestionarTransaccion.php';
require_once 'GTR-02_GestionarConcepto.php';

// ------------------------------------------------------------
// GTR-06 ControladorAgenda
// ------------------------------------------------------------


class ControladorAgenda {

    
    /* FUN-64 obtenerProyeccionIngresos 
    Obtiene las proyecciones de ingresos de todo el año de una familia especifica */
    public static function obtenerProyeccionIngresos($idFamilia, $fecha) {
        return GestionarTransaccion::hallarProyeccionIngresosBD($idFamilia, $fecha);
    }
    
    /* FUN-65 obtenerProyeccionEgresos 
    Obtiene las proyecciones de egresos de todo el año de una familia especifica */
    public static function obtenerProyeccionEgresos($idFamilia, $fecha) {
        return GestionarTransaccion::obtenerProyeccionEgresosBD($idFamilia, $fecha);
    }


    /* FUN-78 solicitarConceptosPorFecha 
        Nos permite obtener los conceptos por una fecha, para poder ver cual es el siguiente
        concepto a ser cobrado */
    public static function solicitarConceptosPorFecha($fecha, $idFamilia) {
        
        return GestionarConcepto::obtenerConceptosPorFechaBD($fecha, $idFamilia);
    }
}
?>
