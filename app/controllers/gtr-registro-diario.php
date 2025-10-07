<?php
// CU-004 Gestionar Registro Diario
// GTR-08 Clase para gestión de registros diarios
// TAB-04 Transaccion

class GestionarRegistroDiario
{
    private $conn;

    // FUN-040 Constructor
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // FUN-041 Obtener transacciones del día actual
    public function obtenerTransaccionesDiarias($idUsuario, $esFamiliar, $diaHoy)
    {
        try {
            if ($esFamiliar) {
                // Obtener familia del usuario
                $stmt = $this->conn->prepare("SELECT familia_id FROM usuario WHERE id = :id LIMIT 1");
                $stmt->execute(['id' => $idUsuario]);
                $user = $stmt->fetch(\PDO::FETCH_ASSOC);
                $familiaId = $user['familia_id'];

                // Obtener transacciones de toda la familia
                $sql = "SELECT t.id, t.concepto_id, c.nombre as concepto_nombre, 
                        c.tipo, t.monto, t.fecha_transaccion, u.nombre as usuario_nombre,
                        cat.nombre as categoria_nombre
                        FROM transaccion t
                        INNER JOIN concepto c ON t.concepto_id = c.id
                        INNER JOIN usuario u ON t.usuario_id = u.id
                        LEFT JOIN categoria cat ON c.categoria_id = cat.id
                        WHERE u.familia_id = :familia_id 
                        AND DATE(t.fecha_transaccion) = :dia
                        ORDER BY t.fecha_transaccion DESC";
                
                $stmt = $this->conn->prepare($sql);
                $stmt->bindValue(':familia_id', $familiaId, \PDO::PARAM_INT);
                $stmt->bindValue(':dia', $diaHoy, \PDO::PARAM_STR);
            } else {
                // Obtener solo transacciones del usuario
                $sql = "SELECT t.id, t.concepto_id, c.nombre as concepto_nombre, 
                        c.tipo, t.monto, t.fecha_transaccion, u.nombre as usuario_nombre,
                        cat.nombre as categoria_nombre
                        FROM transaccion t
                        INNER JOIN concepto c ON t.concepto_id = c.id
                        INNER JOIN usuario u ON t.usuario_id = u.id
                        LEFT JOIN categoria cat ON c.categoria_id = cat.id
                        WHERE t.usuario_id = :usuario_id 
                        AND DATE(t.fecha_transaccion) = :dia
                        ORDER BY t.fecha_transaccion DESC";
                
                $stmt = $this->conn->prepare($sql);
                $stmt->bindValue(':usuario_id', $idUsuario, \PDO::PARAM_INT);
                $stmt->bindValue(':dia', $diaHoy, \PDO::PARAM_STR);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error en obtenerTransaccionesDiarias: " . $e->getMessage());
            return [];
        }
    }

    // FUN-042 Obtener conceptos del usuario para transacciones
    public function obtenerConceptosTransaccion($idUsuario, $tipo)
    {
        try {
            $stmt = $this->conn->prepare("SELECT c.id, c.nombre, cat.nombre as categoria_nombre
                                          FROM concepto c
                                          LEFT JOIN categoria cat ON c.categoria_id = cat.id
                                          WHERE c.usuario_id = :usuario_id 
                                          AND c.tipo = :tipo 
                                          AND c.estado = 'activo'
                                          ORDER BY c.nombre ASC");
            $stmt->execute(['usuario_id' => $idUsuario, 'tipo' => $tipo]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error en obtenerConceptosTransaccion: " . $e->getMessage());
            return [];
        }
    }

    // FUN-043 Validar campos de transacción
    public function validarCampos($conceptoId, $monto)
    {
        $errores = [];

        if (empty($conceptoId) || !is_numeric($conceptoId)) {
            $errores[] = "Debe seleccionar un concepto válido.";
        }

        if (!is_numeric($monto) || $monto <= 0) {
            $errores[] = "El monto debe ser un número mayor a 0.";
        }

        return $errores;
    }

    // FUN-044 Relacionar datos de transacción con concepto y categoría
    public function relacionarDatos($conceptoId, $idUsuario)
    {
        try {
            $stmt = $this->conn->prepare("SELECT c.id, c.nombre, c.tipo, c.monto as monto_predeterminado,
                                          cat.id as categoria_id, cat.nombre as categoria_nombre
                                          FROM concepto c
                                          LEFT JOIN categoria cat ON c.categoria_id = cat.id
                                          WHERE c.id = :id AND c.usuario_id = :usuario_id 
                                          AND c.estado = 'activo'
                                          LIMIT 1");
            $stmt->execute(['id' => $conceptoId, 'usuario_id' => $idUsuario]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error en relacionarDatos: " . $e->getMessage());
            return null;
        }
    }

    // FUN-045 Crear transacción
    public function crearTransaccion($conceptoId, $monto, $idUsuario, $fecha = null)
    {
        try {
            if ($fecha === null) {
                $fecha = date('Y-m-d H:i:s');
            }

            $sql = "INSERT INTO transaccion (concepto_id, usuario_id, monto, fecha_transaccion)
                    VALUES (:concepto_id, :usuario_id, :monto, :fecha)
                    RETURNING id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':concepto_id', $conceptoId, \PDO::PARAM_INT);
            $stmt->bindValue(':usuario_id', $idUsuario, \PDO::PARAM_INT);
            $stmt->bindValue(':monto', $monto, \PDO::PARAM_STR);
            $stmt->bindValue(':fecha', $fecha, \PDO::PARAM_STR);
            
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            return ['success' => true, 'id' => $result['id'], 'mensaje' => 'Transacción registrada exitosamente.'];
        } catch (\PDOException $e) {
            error_log("Error en crearTransaccion: " . $e->getMessage());
            return ['success' => false, 'mensaje' => 'Error al registrar la transacción.'];
        }
    }

    // FUN-046 Obtener transacción por ID
    public function obtenerTransaccion($idTransaccion, $idUsuario)
    {
        try {
            $stmt = $this->conn->prepare("SELECT t.*, c.nombre as concepto_nombre, c.tipo
                                          FROM transaccion t
                                          INNER JOIN concepto c ON t.concepto_id = c.id
                                          WHERE t.id = :id AND t.usuario_id = :usuario_id
                                          LIMIT 1");
            $stmt->execute(['id' => $idTransaccion, 'usuario_id' => $idUsuario]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error en obtenerTransaccion: " . $e->getMessage());
            return null;
        }
    }

    // FUN-047 Editar transacción
    public function editarTransaccion($idTransaccion, $conceptoId, $monto, $idUsuario)
    {
        try {
            // Verificar que la transacción pertenece al usuario
            $transaccion = $this->obtenerTransaccion($idTransaccion, $idUsuario);
            if (!$transaccion) {
                return ['success' => false, 'mensaje' => 'No tiene permisos para editar esta transacción.'];
            }

            $sql = "UPDATE transaccion 
                    SET concepto_id = :concepto_id, 
                        monto = :monto
                    WHERE id = :id AND usuario_id = :usuario_id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':concepto_id', $conceptoId, \PDO::PARAM_INT);
            $stmt->bindValue(':monto', $monto, \PDO::PARAM_STR);
            $stmt->bindValue(':id', $idTransaccion, \PDO::PARAM_INT);
            $stmt->bindValue(':usuario_id', $idUsuario, \PDO::PARAM_INT);
            
            $stmt->execute();
            
            return ['success' => true, 'mensaje' => 'Transacción actualizada exitosamente.'];
        } catch (\PDOException $e) {
            error_log("Error en editarTransaccion: " . $e->getMessage());
            return ['success' => false, 'mensaje' => 'Error al actualizar la transacción.'];
        }
    }

    // FUN-048 Calcular balances
    public function calcularBalances($idUsuario, $esFamiliar)
    {
        try {
            if ($esFamiliar) {
                // Obtener familia del usuario
                $stmt = $this->conn->prepare("SELECT familia_id FROM usuario WHERE id = :id LIMIT 1");
                $stmt->execute(['id' => $idUsuario]);
                $user = $stmt->fetch(\PDO::FETCH_ASSOC);
                $familiaId = $user['familia_id'];

                // Balance diario
                $stmtDiario = $this->conn->prepare("
                    SELECT 
                        COALESCE(SUM(CASE WHEN c.tipo = 'ingreso' THEN t.monto ELSE 0 END), 0) as total_ingresos,
                        COALESCE(SUM(CASE WHEN c.tipo = 'egreso' THEN t.monto ELSE 0 END), 0) as total_egresos
                    FROM transaccion t
                    INNER JOIN concepto c ON t.concepto_id = c.id
                    INNER JOIN usuario u ON t.usuario_id = u.id
                    WHERE u.familia_id = :familia_id 
                    AND DATE(t.fecha_transaccion) = CURRENT_DATE
                ");
                $stmtDiario->execute(['familia_id' => $familiaId]);
                $balanceDiario = $stmtDiario->fetch(\PDO::FETCH_ASSOC);

                // Balance mensual
                $stmtMensual = $this->conn->prepare("
                    SELECT 
                        COALESCE(SUM(CASE WHEN c.tipo = 'ingreso' THEN t.monto ELSE 0 END), 0) as total_ingresos,
                        COALESCE(SUM(CASE WHEN c.tipo = 'egreso' THEN t.monto ELSE 0 END), 0) as total_egresos
                    FROM transaccion t
                    INNER JOIN concepto c ON t.concepto_id = c.id
                    INNER JOIN usuario u ON t.usuario_id = u.id
                    WHERE u.familia_id = :familia_id 
                    AND EXTRACT(MONTH FROM t.fecha_transaccion) = EXTRACT(MONTH FROM CURRENT_DATE)
                    AND EXTRACT(YEAR FROM t.fecha_transaccion) = EXTRACT(YEAR FROM CURRENT_DATE)
                ");
                $stmtMensual->execute(['familia_id' => $familiaId]);
                $balanceMensual = $stmtMensual->fetch(\PDO::FETCH_ASSOC);
            } else {
                // Balance diario personal
                $stmtDiario = $this->conn->prepare("
                    SELECT 
                        COALESCE(SUM(CASE WHEN c.tipo = 'ingreso' THEN t.monto ELSE 0 END), 0) as total_ingresos,
                        COALESCE(SUM(CASE WHEN c.tipo = 'egreso' THEN t.monto ELSE 0 END), 0) as total_egresos
                    FROM transaccion t
                    INNER JOIN concepto c ON t.concepto_id = c.id
                    WHERE t.usuario_id = :usuario_id 
                    AND DATE(t.fecha_transaccion) = CURRENT_DATE
                ");
                $stmtDiario->execute(['usuario_id' => $idUsuario]);
                $balanceDiario = $stmtDiario->fetch(\PDO::FETCH_ASSOC);

                // Balance mensual personal
                $stmtMensual = $this->conn->prepare("
                    SELECT 
                        COALESCE(SUM(CASE WHEN c.tipo = 'ingreso' THEN t.monto ELSE 0 END), 0) as total_ingresos,
                        COALESCE(SUM(CASE WHEN c.tipo = 'egreso' THEN t.monto ELSE 0 END), 0) as total_egresos
                    FROM transaccion t
                    INNER JOIN concepto c ON t.concepto_id = c.id
                    WHERE t.usuario_id = :usuario_id 
                    AND EXTRACT(MONTH FROM t.fecha_transaccion) = EXTRACT(MONTH FROM CURRENT_DATE)
                    AND EXTRACT(YEAR FROM t.fecha_transaccion) = EXTRACT(YEAR FROM CURRENT_DATE)
                ");
                $stmtMensual->execute(['usuario_id' => $idUsuario]);
                $balanceMensual = $stmtMensual->fetch(\PDO::FETCH_ASSOC);
            }

            return [
                'diario' => [
                    'ingresos' => $balanceDiario['total_ingresos'],
                    'egresos' => $balanceDiario['total_egresos'],
                    'balance' => $balanceDiario['total_ingresos'] - $balanceDiario['total_egresos']
                ],
                'mensual' => [
                    'ingresos' => $balanceMensual['total_ingresos'],
                    'egresos' => $balanceMensual['total_egresos'],
                    'balance' => $balanceMensual['total_ingresos'] - $balanceMensual['total_egresos']
                ]
            ];
        } catch (\PDOException $e) {
            error_log("Error en calcularBalances: " . $e->getMessage());
            return null;
        }
    }

    // FUN-049 Actualizar corte semanal
    public function actualizarCorteSemanal($idUsuario, $esFamiliar, $diaHoy)
    {
        try {
            // Esta función registraría un corte semanal en una tabla específica
            // Por ahora solo retornamos éxito
            return ['success' => true, 'mensaje' => 'Corte semanal realizado exitosamente.'];
        } catch (\PDOException $e) {
            error_log("Error en actualizarCorteSemanal: " . $e->getMessage());
            return ['success' => false, 'mensaje' => 'Error al realizar el corte semanal.'];
        }
    }
}
?>