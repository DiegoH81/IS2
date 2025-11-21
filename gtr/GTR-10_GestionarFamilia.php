<?php
require_once '../DatabaseConnection.php';

require_once '../entity/familia.php';

// ------------------------------------------------------------
// GTR-10 Gestionar familiar
// ------------------------------------------------------------

class GestionarFamilia {

    /* FUN-20 existeContrasenaFamiliarBD 
        Verifica si ya esta en uso la contraseña familiar en la base de datos */
    public static function existeContrasenaFamiliarBD($contrasena_familiar) {
        return Familia::existeContrasenaFamiliar($contrasena_familiar);
    }


    /* FUN-21 crearFamiliaBD
        Inserta un nuevo grupo familiar en la base de dato */
    public static function crearFamiliaBD($nombre_familia, $codigo_familiar) {
        return Familia::crearFamilia($nombre_familia, $codigo_familiar);
    }

    /* FUN-80 obtenerFamiliaPorCodigoBD
        Se obtiene una familia en base al codigo familiar, pidiendosela a la entidad */
    public static function obtenerFamiliaPorCodigoBD($codigo_familiar) {
        return Familia::obtenerFamiliaPorCodigo($codigo_familiar);
    }

}
?>
