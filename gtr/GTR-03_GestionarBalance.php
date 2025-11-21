<?php
require_once '../DatabaseConnection.php';
require_once 'GTR-07_GestionarTransaccion.php';

// ------------------------------------------------------------
// GTR-03 Gestionar balance
// ------------------------------------------------------------

class GestionarBalance
{
    /* FUN-81 relacionarDatosBalance
        Se van a relacionar todos los datos necesarios para construir el balance*/
    public static function relacionarDatosBalance($idFamilia, $fecha_inicio, $fecha_fin) {
        $resultado = [];
        
        $transacciones = GestionarTransaccion::obtenerTransaccionesRangoBD($idFamilia, $fecha_inicio, $fecha_fin);
        $categorias = GestionarTransaccion::solicitarCategorias($idFamilia);
        $usuarios = GestionarTransaccion::solicitarUsuarios($idFamilia);
        $conceptos = GestionarTransaccion::solicitarConceptos($idFamilia);
        
        //var_dump($transacciones);
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

        foreach ($transacciones as $t) {
            
            $conceptoObj = isset($conceptosIndex[$t->idConcepto]) 
                    ? $conceptosIndex[$t->idConcepto] 
                    : null;

            $nombreCategoria = '';
            if ($conceptoObj && isset($categoriasIndex[$conceptoObj->idCategoria])) {
                $nombreCategoria = $categoriasIndex[$conceptoObj->idCategoria];
            }

            $transaccionRelacionada = [
                'idTransaccion' => $t->idTransaccion,
                'fecha'         => $t->fecha,
                'monto'         => $t->monto,
                'tipo'          => $t->tipo,
                'categoria'     => $nombreCategoria,
                'concepto'      => isset($conceptosIndex[$t->idConcepto]) ? $conceptosIndex[$t->idConcepto]->nombre : '',
                'idConcepto'    => isset($conceptosIndex[$t->idConcepto]) ? $conceptosIndex[$t->idConcepto]->idConcepto : '',
                'usuario'       => isset($usuariosIndex[$t->idUsuario]) ? $usuariosIndex[$t->idUsuario] : '',
                'usuario_id'    => $t->idUsuario
            ];

            $resultado[] = $transaccionRelacionada;
        }

        return $resultado;
    }

    /* FUN-82 vistaFamiliarBalance
        Se van a filtrar los datos para devolver la vista familiar de balance, tomando un rango de fechas como parametros*/
    public static function vistaFamiliarBalance($idFamilia, $fecha_inicio, $fecha_fin) {
        return self::relacionarDatosBalance($idFamilia, $fecha_inicio, $fecha_fin);
    }

    /* FUN-83 vistaUsuarioBalance
        Se van a filtrar los datos para devolver la vista de usuario de balance, tomando un rango de fechas como parametros*/
    public static function vistaUsuarioBalance($idFamilia, $fecha_inicio, $fecha_fin, $idUsuario) {
        $datosRelacionados = self::relacionarDatosBalance($idFamilia, $fecha_inicio, $fecha_fin);

        $datosUsuario = array_filter($datosRelacionados, function($transaccion) use ($idUsuario) {
            return $transaccion['usuario_id'] == $idUsuario;
        });

        return $datosUsuario;
    }
}
?>
