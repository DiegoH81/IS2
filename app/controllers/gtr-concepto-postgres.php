<?php
// CU-017 Gestionar Conceptos
// GTR-02 Clase para gestión de conceptos (Ingresos/Egresos)
// TAB-02 Concepto

class GestionarConcepto
{
    private $conn;

    // FUN-001 Constructor
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // FUN-002 Obtener todos los conceptos de un usuario
    public function obtenerConceptos($idUsuario, $tipo = null)
    {
        try {
            $sql = "SELECT c.id, c.nombre, c.categoria_id, c.tipo, c.periodo, 
                           c.monto, c.dia_inicio, c.dia_fin, cat.nombre as categoria_nombre,
                           c.fecha_creacion, c.usuario_id
                    FROM concepto c
                    LEFT JOIN categoria cat ON c.categoria_id = cat.id
                    WHERE c.usuario_id = :usuario_id 
                    AND c.estado = 'activo'";
            
            if ($tipo !== null) {
                $sql .= " AND c.tipo = :tipo";
            }
            
            $sql .= " ORDER BY c.fecha_creacion DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':usuario_id', $idUsuario, \PDO::PARAM_INT);
            
            if ($tipo !== null) {
                $stmt->bindValue(':tipo', $tipo, \PDO::PARAM_STR);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error en obtenerConceptos: " . $e->getMessage());
            return [];
        }
    }

    // FUN-003 Obtener un concepto específico por ID
    public function obtenerConcepto($idConcepto)
    {
        try {
            $stmt = $this->conn->prepare("SELECT c.*, cat.nombre as categoria_nombre 
                                          FROM concepto c
                                          LEFT JOIN categoria cat ON c.categoria_id = cat.id
                                          WHERE c.id = :id LIMIT 1");
            $stmt->execute(['id' => $idConcepto]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error en obtenerConcepto: " . $e->getMessage());
            return null;
        }
    }

    // FUN-004 Validar campos del concepto
    public function validarCampos($nombre, $categoria, $tipo, $periodo, $monto, $diaInicio, $diaFin)
    {
        $errores = [];

        // Validar nombre
        if (empty(trim($nombre))) {
            $errores[] = "El nombre del concepto es obligatorio.";
        } elseif (strlen($nombre) > 100) {
            $errores[] = "El nombre no debe exceder 100 caracteres.";
        }

        // Validar categoría
        if (empty($categoria) || !is_numeric($categoria)) {
            $errores[] = "Debe seleccionar una categoría válida.";
        }

        // Validar tipo
        if (!in_array($tipo, ['ingreso', 'egreso'])) {
            $errores[] = "El tipo debe ser 'ingreso' o 'egreso'.";
        }

        // Validar período
        if (!in_array($periodo, ['diario', 'semanal', 'mensual', 'eventual'])) {
            $errores[] = "El período seleccionado no es válido.";
        }

        // Validar monto
        if (!is_numeric($monto) || $monto <= 0) {
            $errores[] = "El monto debe ser un número mayor a 0.";
        }

        // Validar días para períodos específicos
        if ($periodo === 'semanal') {
            if (empty($diaInicio) || $diaInicio < 1 || $diaInicio > 7) {
                $errores[] = "Para período semanal, el día debe estar entre 1 (Lunes) y 7 (Domingo).";
            }
        }

        if ($periodo === 'mensual') {
            if (empty($diaInicio) || $diaInicio < 1 || $diaInicio > 31) {
                $errores[] = "Para período mensual, el día debe estar entre 1 y 31.";
            }
        }

        return $errores;
    }

    // FUN-005 Crear un nuevo concepto
    public function crearConcepto($nombre, $categoria, $tipo, $periodo, $monto, $diaInicio, $diaFin, $nombreUsuario, $idUsuario)
    {
        try {
            $sql = "INSERT INTO concepto (nombre, categoria_id, tipo, periodo, monto, 
                    dia_inicio, dia_fin, nombre_usuario, usuario_id, estado, fecha_creacion)
                    VALUES (:nombre, :categoria, :tipo, :periodo, :monto, 
                    :dia_inicio, :dia_fin, :nombre_usuario, :usuario_id, 'activo', NOW())
                    RETURNING id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':nombre', trim($nombre), \PDO::PARAM_STR);
            $stmt->bindValue(':categoria', $categoria, \PDO::PARAM_INT);
            $stmt->bindValue(':tipo', $tipo, \PDO::PARAM_STR);
            $stmt->bindValue(':periodo', $periodo, \PDO::PARAM_STR);
            $stmt->bindValue(':monto', $monto, \PDO::PARAM_STR);
            $stmt->bindValue(':dia_inicio', $diaInicio, \PDO::PARAM_INT);
            $stmt->bindValue(':dia_fin', $diaFin, \PDO::PARAM_INT);
            $stmt->bindValue(':nombre_usuario', $nombreUsuario, \PDO::PARAM_STR);
            $stmt->bindValue(':usuario_id', $idUsuario, \PDO::PARAM_INT);
            
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            return ['success' => true, 'id' => $result['id'], 'mensaje' => 'Concepto creado exitosamente.'];
        } catch (\PDOException $e) {
            error_log("Error en crearConcepto: " . $e->getMessage());
            return ['success' => false, 'mensaje' => 'Error al crear el concepto.'];
        }
    }

    // FUN-006 Editar un concepto existente
    public function editarConcepto($idConcepto, $nombre, $categoria, $tipo, $periodo, $monto, $diaInicio, $diaFin, $nombreUsuario, $idUsuario)
    {
        try {
            // Verificar que el concepto pertenece al usuario
            $conceptoActual = $this->obtenerConcepto($idConcepto);
            if (!$conceptoActual || $conceptoActual['usuario_id'] != $idUsuario) {
                return ['success' => false, 'mensaje' => 'No tiene permisos para editar este concepto.'];
            }

            $sql = "UPDATE concepto 
                    SET nombre = :nombre, 
                        categoria_id = :categoria, 
                        tipo = :tipo, 
                        periodo = :periodo, 
                        monto = :monto, 
                        dia_inicio = :dia_inicio, 
                        dia_fin = :dia_fin,
                        nombre_usuario = :nombre_usuario,
                        fecha_actualizacion = NOW()
                    WHERE id = :id AND usuario_id = :usuario_id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':nombre', trim($nombre), \PDO::PARAM_STR);
            $stmt->bindValue(':categoria', $categoria, \PDO::PARAM_INT);
            $stmt->bindValue(':tipo', $tipo, \PDO::PARAM_STR);
            $stmt->bindValue(':periodo', $periodo, \PDO::PARAM_STR);
            $stmt->bindValue(':monto', $monto, \PDO::PARAM_STR);
            $stmt->bindValue(':dia_inicio', $diaInicio, \PDO::PARAM_INT);
            $stmt->bindValue(':dia_fin', $diaFin, \PDO::PARAM_INT);
            $stmt->bindValue(':nombre_usuario', $nombreUsuario, \PDO::PARAM_STR);
            $stmt->bindValue(':id', $idConcepto, \PDO::PARAM_INT);
            $stmt->bindValue(':usuario_id', $idUsuario, \PDO::PARAM_INT);
            
            $stmt->execute();
            
            return ['success' => true, 'mensaje' => 'Concepto actualizado exitosamente.'];
        } catch (\PDOException $e) {
            error_log("Error en editarConcepto: " . $e->getMessage());
            return ['success' => false, 'mensaje' => 'Error al actualizar el concepto.'];
        }
    }

    // FUN-007 Cambiar estado de un concepto (soft delete)
    public function editarEstadoConcepto($idConcepto, $estado, $idUsuario)
    {
        try {
            // Verificar que el concepto pertenece al usuario
            $conceptoActual = $this->obtenerConcepto($idConcepto);
            if (!$conceptoActual || $conceptoActual['usuario_id'] != $idUsuario) {
                return ['success' => false, 'mensaje' => 'No tiene permisos para modificar este concepto.'];
            }

            if (!in_array($estado, ['activo', 'inactivo', 'eliminado'])) {
                return ['success' => false, 'mensaje' => 'Estado no válido.'];
            }

            $stmt = $this->conn->prepare("UPDATE concepto 
                                          SET estado = :estado, fecha_actualizacion = NOW()
                                          WHERE id = :id AND usuario_id = :usuario_id");
            $stmt->execute([
                'estado' => $estado,
                'id' => $idConcepto,
                'usuario_id' => $idUsuario
            ]);
            
            return ['success' => true, 'mensaje' => 'Estado del concepto actualizado.'];
        } catch (\PDOException $e) {
            error_log("Error en editarEstadoConcepto: " . $e->getMessage());
            return ['success' => false, 'mensaje' => 'Error al cambiar el estado.'];
        }
    }
}
?>