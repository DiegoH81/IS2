<?php
// =====================================================
// entities.php
// Contiene todas las clases del modelo de entidades
// para el sistema "On a Budget"
// =====================================================

/* =====================================================
   CLASE: USUARIO
   ===================================================== */
class Usuario {
    // Atributos privados
    private $idUsuario;
    private $idFamilia;
    private $nombreUsuario;
    private $nombre;
    private $apellido;
    private $rol;
    private $estado;
    private $contraseña;

    public function __construct($idUsuario = null, $idFamilia = null, $nombreUsuario = "", $nombre = "", $apellido = "", $rol = "miembro", $estado = "activo", $contraseña = "") {
        $this->idUsuario = $idUsuario;
        $this->idFamilia = $idFamilia;
        $this->nombreUsuario = $nombreUsuario;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->rol = $rol;
        $this->estado = $estado;
        $this->contraseña = $contraseña;
    }
}


/* =====================================================
   CLASE: CONCEPTO
   ===================================================== */
class Concepto {
    private $idConcepto;
    private $nombre;
    private $tipo;
    private $descripcion;
    private $monto;
    private $estado;
    private $periodicidad;
    private $fechaInicio;
    private $fechaFin;

    public function __construct($idConcepto = null, $nombre = "", $tipo = "", $descripcion = "", $monto = 0.0, $estado = "activo", $periodicidad = 0, $fechaInicio = null, $fechaFin = null) {
        $this->idConcepto = $idConcepto;
        $this->nombre = $nombre;
        $this->tipo = $tipo;
        $this->descripcion = $descripcion;
        $this->monto = $monto;
        $this->estado = $estado;
        $this->periodicidad = $periodicidad;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }
}


/* =====================================================
   CLASE: FAMILIA
   ===================================================== */
class Familia {
    private $idFamilia;
    private $nombre;
    private $estado;

    public function __construct($idFamilia = null, $nombre = "", $estado = "activo") {
        $this->idFamilia = $idFamilia;
        $this->nombre = $nombre;
        $this->estado = $estado;
    }
}


/* =====================================================
   CLASE: TRANSACCION
   ===================================================== */
class Transaccion {
    private $idTransaccion;
    private $nombre;
    private $monto;
    private $tipo;
    private $fecha;

    public function __construct($idTransaccion = null, $nombre = "", $monto = 0.0, $tipo = "", $fecha = null) {
        $this->idTransaccion = $idTransaccion;
        $this->nombre = $nombre;
        $this->monto = $monto;
        $this->tipo = $tipo;
        $this->fecha = $fecha;
    }
}


/* =====================================================
   CLASE: CATEGORIA
   ===================================================== */
class Categoria {
    private $idCategoria;
    private $nombre;
    private $descripcion;
    private $estado;
    private $idFamilia;
    public function __construct($idCategoria = null, $nombre = "", $descripcion = "", $estado = "activo", $idFamilia = null) {
        $this->idCategoria = $idCategoria;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->estado = $estado;
        $this->idFamilia = $idFamilia;
    }
}

?>
