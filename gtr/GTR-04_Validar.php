<?php
require_once '../DatabaseConnection.php';
require_once 'GTR-01_GestionarUsuario.php';

// ------------------------------------------------------------
// GTR-04 Validar
// ------------------------------------------------------------

class Validar {

    /* FUN-15 solicitarValidacionUsuario
        Invoca al GTR-01 Gestionar usuario para verificar si el usaurio ingresado existe */
    public static function solicitarValidacionUsuario($usuario) {
        return GestionarUsuario::validarUsuarioBD($usuario);
    }

    /* FUN-16 solicitarValidacionCredenciales 
        Invoca al GTR-01 Gestionar usuario para verificar si la contraseña ingresada coincide
        con la del usuario ingresado */
    public static function solicitarValidacionCredenciales($usuario, $contrasena) {
        return GestionarUsuario::validarCredencialesBD($usuario, $contrasena);
    }

    /* FUN-17 solicitarUsuarioDisponible 
        Invoca al GTR-01 Gestionar usuario para verificar si el usuario(nombre de usuario)
        esta en uso */
    public static function solicitarUsuarioDisponible($usuario) {
        return GestionarUsuario::usuarioDisponibleBD($usuario);
    }

    /* FUN-18 solicitarUsuario 
        Invoca al GTR-01 Gestionar usuario para extraer los datos de un usuario especifico 
        segun su id */
    public static function solicitarUsuario($usuario) {
        return GestionarUsuario::obtenerUsuarioBD($usuario);
    }

    /* FUN-51 obtenerFamiliaActual 
        Se obtiene el id de la familia actual */
    public static function obtenerFamiliaActual() {
        return $_SESSION['familia_id'];
    }

    /* FUN-52 obtenerUsuarioActual 
        Se obtienen los datos del usuario actual */
    public static function obtenerUsuarioActual() {
        // Crea nuevo usuario
        return new Usuario(
            $_SESSION['id_usuario'] ?? null,
            $_SESSION['usuario'] ?? null,     
            $_SESSION['nombre'] ?? null,       
            $_SESSION['contrasena'] ?? null,
            $_SESSION['rol'] ?? null,            
            true,                             
            $_SESSION['familia_id'] ?? null
        );
    }

    /* FUN-83 validarTransacciones
        Se encarga de verificar todas las transacciones*/

    public static function validarTransacciones()
    {
        $conn = Database::connect();
        
        $sql = "SELECT * FROM generar_transacciones_periodicas()";
        pg_query($conn, $sql);
    }
}
?>
