<?php
require_once '../DatabaseConnection.php';
require_once 'GTR-07_GestionarTransaccion.php';

// GTR-02 Gestionar concepto

class GestionarRegistroDiario {


    /* FUN-76 relacionarDatos 
        Relaciona los datos de para poder construir los datos para registro diario */
    public static function relacionarDatos($idFamilia, $fecha_inicio, $fecha_fin) {
        // Inicializamos el array de resultados
        $resultado = [];
        
        // Obtener las transacciones, balance, categorías, conceptos y usuarios
        $transacciones = GestionarTransaccion::obtenerTransaccionesRangoBD($idFamilia, $fecha_inicio, $fecha_fin);
        $categorias = GestionarTransaccion::solicitarCategorias($idFamilia);
        $usuarios = GestionarTransaccion::solicitarUsuarios($idFamilia);
        $conceptos = GestionarTransaccion::solicitarConceptos($idFamilia);
        
        //var_dump($transacciones);
        // Crear índices para búsquedas rápidas (indexar por id)
        $usuariosIndex = [];
        foreach ($usuarios as $u) {
            $usuariosIndex[$u->idUsuario] = $u->nombre;
        }

        $categoriasIndex = [];
        foreach ($categorias as $cat) {
            $categoriasIndex[$cat->idCategoria] = $cat->nombre;
        }

        $conceptosIndex = [];
        foreach ($conceptos as $c) {
            $conceptosIndex[$c->idConcepto] = $c;
        }

        // Relacionar las transacciones con sus conceptos, categorías y usuarios
        foreach ($transacciones as $t) {
            $transaccionRelacionada = [
                'idTransaccion' => $t->idTransaccion,
                'fecha'         => $t->fecha,
                'monto'         => $t->monto,
                'tipo'          => $t->tipo,
                'categoria'     => isset($categoriasIndex[$t->idConcepto]) ? $categoriasIndex[$t->idConcepto] : '',
                'concepto'      => isset($conceptosIndex[$t->idConcepto]) ? $conceptosIndex[$t->idConcepto]->nombre : '',
                'idConcepto'    => isset($conceptosIndex[$t->idConcepto]) ? $conceptosIndex[$t->idConcepto]->idConcepto : '',
                'usuario'       => isset($usuariosIndex[$t->idUsuario]) ? $usuariosIndex[$t->idUsuario] : '',
                'usuario_id'    => $t->idUsuario
            ];

            // Añadir la transacción con su relación
            $resultado[] = $transaccionRelacionada;
        }

        return $resultado; // Retorna el array de datos relacionados
    }

    /* FUN-77 vistaFamiliar 
        Filtra los datos para obtener los conceptos para la vista familiar */
    public static function vistaFamiliar($idFamilia, $fecha_inicio, $fecha_fin) {
        // Llamar a relacionarDatos sin ningún filtro adicional
        return self::relacionarDatos($idFamilia, $fecha_inicio, $fecha_fin);
    }

    /* FUN-78 vistaUsuario 
        Filtra los datos para obtener los conceptos para la vista de usuario */
    public static function vistaUsuario($idFamilia, $fecha_inicio, $fecha_fin, $idUsuario) {
        // Obtener todos los datos relacionados
        $datosRelacionados = self::relacionarDatos($idFamilia, $fecha_inicio, $fecha_fin);

        // Filtrar solo las transacciones que coinciden con el idUsuario
        $datosUsuario = array_filter($datosRelacionados, function($transaccion) use ($idUsuario) {
            return $transaccion['usuario_id'] == $idUsuario;
        });

        // Devolver los datos filtrados
        return $datosUsuario;
    }
}
?>
