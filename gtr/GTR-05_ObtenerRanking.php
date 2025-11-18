<?php
require_once '../DatabaseConnection.php';
require_once 'GTR-01_GestionarUsuario.php';
require_once 'GTR-07_GestionarTransaccion.php';

// GTR-05 ObtenerRanking

class ObtenerRanking {

    public static function relacionarDatos($idFamilia) {
        // Inicializamos el array de resultados
        $resultado = [];
        
        // Obtener las transacciones, balance, categorías, conceptos y usuarios
        $transacciones = GestionarTransaccion::obtenerTransaccionesPorFamiliaBD($idFamilia);
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

    public static function obtenerIngresos($idFamilia) {
        // Obtener todas las transacciones relacionadas
        $transacciones = self::relacionarDatos($idFamilia);
        
        // Filtrar solo los ingresos
        $ingresos = array_filter($transacciones, function($transaccion) {
            return $transaccion['tipo'] === 'Ingreso';
        });

        return $ingresos; // Retorna solo los ingresos
    }

    public static function obtenerEgresos($idFamilia) {
        // Obtener todas las transacciones relacionadas
        $transacciones = self::relacionarDatos($idFamilia);
        
        // Filtrar solo los ingresos
        $ingresos = array_filter($transacciones, function($transaccion) {
            return $transaccion['tipo'] === 'Egreso';
        });

        return $ingresos; // Retorna solo los ingresos
    }

    public static function filtrarPorUltimas4Semanas($transacciones) {
        $fechaLimite = date('Y-m-d', strtotime('-4 weeks'));  // Calcula la fecha de hace 4 semanas
        return array_filter($transacciones, function($transaccion) use ($fechaLimite) {
            return $transaccion['fecha'] >= $fechaLimite;
        });
    }

    // Función para filtrar transacciones de los últimos 6 meses
    public static function filtrarPorUltimos6Meses($transacciones) {
        $fechaLimite = date('Y-m-d', strtotime('-6 months'));  // Calcula la fecha de hace 6 meses
        return array_filter($transacciones, function($transaccion) use ($fechaLimite) {
            return $transaccion['fecha'] >= $fechaLimite;
        });
    }

    // Función para filtrar transacciones de los últimos 12 meses
    public static function filtrarPorUltimos12Meses($transacciones) {
        $fechaLimite = date('Y-m-d', strtotime('-12 months'));  // Calcula la fecha de hace 12 meses
        return array_filter($transacciones, function($transaccion) use ($fechaLimite) {
            return $transaccion['fecha'] >= $fechaLimite;
        });
    }
}
?>
