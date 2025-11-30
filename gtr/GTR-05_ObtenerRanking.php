<?php
require_once '../DatabaseConnection.php';
require_once 'GTR-01_GestionarUsuario.php';
require_once 'GTR-07_GestionarTransaccion.php';

// ------------------------------------------------------------
// GTR-05 ObtenerRanking
// ------------------------------------------------------------


class ObtenerRanking {

    /* FUN-59 relacionarDatosRanking
        Obtiene el ranking de una familia, relacionando diversos datos*/
    public static function relacionarDatosRanking($idFamilia) {
        // Inicializamos el array de resultados
        $resultado = [];
        
        // Obtener las transacciones, balance, categorías, conceptos y usuarios
        $transacciones = GestionarTransaccion::obtenerTransaccionesPorFamiliaBD($idFamilia);
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

        // Iterando transacciones
        foreach ($transacciones as $t) {
            $conceptoObj = isset($conceptosIndex[$t->idConcepto]) 
                    ? $conceptosIndex[$t->idConcepto] 
                    : null;

            $nombreCategoria = '';
            if ($conceptoObj && isset($categoriasIndex[$conceptoObj->idCategoria])) {
                $nombreCategoria = $categoriasIndex[$conceptoObj->idCategoria];
            }

            // Crear la transaccion relacionada
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

    /* FUN-60 obtenerIngresos 
        Obtiene el ranking de una familia, relacionando a los ingresos */
    public static function obtenerIngresos($idFamilia) {
        $transacciones = self::relacionarDatosRanking($idFamilia);
        $ingresos = array_filter($transacciones, function($transaccion) {
            return $transaccion['tipo'] === 'Ingreso';
        });

        return $ingresos;
    }

    /* FUN-60 obtenerEgresos 
        Obtiene el ranking de una familia, relacionando a los egresos */
    public static function obtenerEgresos($idFamilia) {
        $transacciones = self::relacionarDatosRanking($idFamilia);
        
        $ingresos = array_filter($transacciones, function($transaccion) {
            return $transaccion['tipo'] === 'Egreso';
        });

        return $ingresos;
    }

    /* FUN-61 filtrarPorUltimas4Semanas 
        Filtrar transacciones por las ultimas 4 semanas */
    public static function filtrarPorUltimas4Semanas($transacciones) {
        $fechaLimite = date('Y-m-d', strtotime('-4 weeks'));
        return array_filter($transacciones, function($transaccion) use ($fechaLimite) {
            return $transaccion['fecha'] >= $fechaLimite;
        });
    }

    /* FUN-62 filtrarPorUltimos6Meses 
        Filtrar transacciones por l0s ultimas 6 meses */
    public static function filtrarPorUltimos6Meses($transacciones) {
        $fechaLimite = date('Y-m-d', strtotime('-6 months'));
        return array_filter($transacciones, function($transaccion) use ($fechaLimite) {
            return $transaccion['fecha'] >= $fechaLimite;
        });
    }

    /* FUN-63 filtrarPorUltimos12Meses 
        Filtrar transacciones por l0s ultimas 12 meses */
    public static function filtrarPorUltimos12Meses($transacciones) {
        $fechaLimite = date('Y-m-d', strtotime('-12 months'));
        return array_filter($transacciones, function($transaccion) use ($fechaLimite) {
            return $transaccion['fecha'] >= $fechaLimite;
        });
    }
}
?>
