<?php
require_once '../DatabaseConnection.php';
require_once 'GTR-07_GestionarTransaccion.php';

// ------------------------------------------------------------
// GTR-08 Gestionar Registro Diario
// ------------------------------------------------------------

class GestionarRegistroDiario {


    /* FUN-76 relacionarDatosRegistroDiario
        Relaciona los datos de para poder construir los datos para registro diario */
    public static function relacionarDatosRegistroDiario($idFamilia, $fecha_inicio) {
        // Inicializamos el array de resultados
        $resultado = [];
        
        
        $transacciones = GestionarTransaccion::obtenerTransaccionesRangoBD($idFamilia, $fecha_inicio, $fecha_inicio);
        $categorias = GestionarTransaccion::solicitarCategorias($idFamilia);
        $usuarios = GestionarTransaccion::solicitarUsuarios($idFamilia);
        $conceptos = GestionarTransaccion::solicitarConceptos($idFamilia);
        
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

    /* FUN-77 vistaFamiliarRegistroDiario 
        Filtra los datos para obtener los conceptos para la vista familiar */
    public static function vistaFamiliarRegistroDiario($idFamilia, $fecha_inicio) {
        return self::relacionarDatosRegistroDiario($idFamilia, $fecha_inicio);
    }

    /* FUN-78 vistaUsuarioRegistroDiario 
        Filtra los datos para obtener los conceptos para la vista de usuario */
    public static function vistaUsuarioRegistroDiario($idFamilia, $fecha_inicio, $idUsuario) {
        $datosRelacionados = self::relacionarDatosRegistroDiario($idFamilia, $fecha_inicio);

        $datosUsuario = array_filter($datosRelacionados, function($transaccion) use ($idUsuario) {
            return $transaccion['usuario_id'] == $idUsuario;
        });

        return $datosUsuario;
    }
}
?>
