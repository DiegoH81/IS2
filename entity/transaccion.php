<?php
require_once '../DatabaseConnection.php';

// TAB-05 Transaccion
class Transaccion {

    public $idTransaccion;
    public $fecha;
    public $monto;
    public $tipo;
    public $idConcepto;
    public $idFamilia;

    public function __construct($idTransaccion = null, $fecha = null, $monto = null, $tipo = null, $idConcepto = null, $idFamilia = null) {
        $this->idTransaccion = $idTransaccion;
        $this->fecha = $fecha;
        $this->monto = $monto;
        $this->tipo = $tipo;
        $this->idConcepto = $idConcepto;
        $this->idFamilia = $idFamilia;
    }
}
?>
