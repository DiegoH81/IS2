<?php
require_once '../DatabaseConnection.php';
require_once 'GTR-01_GestionarUsuario.php';
require_once 'GTR-09_GestionarCategoria.php';
require_once '../entity/concepto.php';

// ------------------------------------------------------------
// GTR-02 Gestionar concepto
// ------------------------------------------------------------

class GestionarConcepto {

    /* FUN-07 obtenerConceptosBD 
    Extrae la información de todos los conceptos de la base de datos */
    public static function obtenerConceptosBD($familia_id) {
        
        return concepto::obtenerConceptos($familia_id);
    }

    /* FUN-08 solicitarUsuarios
        Invoca al GTR-01 Gestionar usuario para obtener la informacion de todos los usuarios */
    public static function solicitarUsuarios($familia_id) {
        return GestionarUsuario::obtenerUsuariosBD($familia_id);
    }

    /* FUN-09 solicitarCategorias
        Invoca al GTR-09 Gestionar categoria para obtener la informacion de todas las categorias */
    public static function solicitarCategorias($familia_id) {
        return GestionarCategoria::obtenerCategoriasBD($familia_id);
    }

    /* FUN-10 relacionarDatos
        Filtra los datos de los usuario, categorias y conceptos para obtener una lista de conceptos
        asociados a un grupo familiar */
    public static function relacionarDatos($familia_id) {
        $conn = Database::connect();

        $conceptos = self::obtenerConceptosBD($familia_id);
        $resultado = [];
        
        if (!empty($conceptos)) {
            

            // Obtener usuarios y categorias pertinentes
            $usuarios = self::solicitarUsuarios($familia_id);
            $categorias = self::solicitarCategorias($familia_id);
            
            //var_dump($conceptos);
            //var_dump($usuarios);
            //var_dump($categorias);
    
            $usuariosIndex = [];
            foreach ($usuarios as $u) {
                $usuariosIndex[$u->idUsuario] = $u->nombre;
            }
    
            $categoriasIndex = [];
            foreach ($categorias as $cat) {
                $categoriasIndex[$cat->idCategoria] = $cat->nombre;
            }
            
            // Relacionar datos hasta obtener un concepto mostrable
            foreach ($conceptos as $c) {
                $resultado[] = [
                    'concepto_id' => $c->idConcepto,
                    'concepto'    => $c->nombre,
                    'categoria'   => $categoriasIndex[$c->idCategoria] ?? '',
                    'tipo'        => $c->tipo,
                    'subido_por'  => $usuariosIndex[$c->idUsuario] ?? '',
                    'usuario_id'  => $c->idUsuario,
                    'periodo'     => $c->periodo,
                    'estado'      => ($c->estado === 't') ? 'Habilitado' : 'Deshabilitado'
                ];
            }
        }


        return $resultado;
    }

    /* FUN-11 crearConceptoBD 
        Inserta un nuevo concepto en la base de datos */
    public static function crearConceptoBD(
    $nombre, 
    $tipo, 
    $periodo, 
    $fecha_inicio, 
    $familia_id, 
    $usuario_id, 
    $categoria_id
    ) {
        Concepto::crearConcepto($nombre, $tipo, $periodo, $fecha_inicio, $familia_id, $usuario_id, $categoria_id);
    }



    /* FUN-12 obtenerConceptoBD
        Extra la informacion de un concepto en especifico segun su id */
    public static function obtenerConceptoBD($idConcepto) {
        return Concepto::obtenerConcepto($idConcepto);
    }

    /* FUN-13 editarConceptoBD
        Actualiza la informacion de un concepto en la base de datos segun su id */
    public static function editarConceptoBD($id_concepto, $nombre, $tipo, $periodo, $fecha_inicio, $p_id_categoria ) {
        return $result = Concepto::editarConcepto($id_concepto, $nombre, $tipo, $periodo, $fecha_inicio, $p_id_categoria );
    }


    /* FUN-14 editarEstadoConceptoBD
        Actualiza el estado de un concepto en la base de datos segun su id */
    public static function editarEstadoConceptoBD($id_concepto, $estado) {
        return Concepto::editarEstadoConcepto($id_concepto, $estado);
    }
    
    
    /* FUN-79 obtenerConceptosPorFechaBD 
        Nos permite obtener los conceptos por una fecha, para poder ver cual es el siguiente
        concepto a ser cobrado */
    public static function obtenerConceptosPorFechaBD($fecha, $idFamilia)
    {
        return Concepto::obtenerConceptosPorFecha($fecha, $idFamilia);
    }

}
?>
