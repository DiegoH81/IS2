<?php
// CU-009 Gestionar Categorías
// GTR-09 Clase para gestión de categorías
// TAB-05 Categoria

class GestionarCategoria
{
    private $conn;

    // FUN-010 Constructor
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // FUN-011 Obtener todas las categorías activas
    public function obtenerCategorias()
    {
        try {
            $stmt = $this->conn->prepare("SELECT c.id, c.nombre, c.padre_id, 
                                          cp.nombre as categoria_padre
                                          FROM categoria c
                                          LEFT JOIN categoria cp ON c.padre_id = cp.id
                                          WHERE c.estado = 'activa'
                                          ORDER BY c.nombre ASC");
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error en obtenerCategorias: " . $e->getMessage());
            return [];
        }
    }

    // FUN-012 Obtener categorías por tipo (ingreso/egreso)
    public function obtenerCategoriasPorTipo($tipo)
    {
        try {
            $stmt = $this->conn->prepare("SELECT id, nombre, padre_id 
                                          FROM categoria 
                                          WHERE estado = 'activa' AND tipo = :tipo
                                          ORDER BY nombre ASC");
            $stmt->execute(['tipo' => $tipo]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error en obtenerCategoriasPorTipo: " . $e->getMessage());
            return [];
        }
    }

    // FUN-013 Obtener categoría por ID
    public function obtenerCategoria($idCategoria)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM categoria WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $idCategoria]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error en obtenerCategoria: " . $e->getMessage());
            return null;
        }
    }

    // FUN-014 Validar campos de categoría
    public function validarCampos($nombre, $tipo)
    {
        $errores = [];

        if (empty(trim($nombre))) {
            $errores[] = "El nombre de la categoría es obligatorio.";
        } elseif (strlen($nombre) > 50) {
            $errores[] = "El nombre no debe exceder 50 caracteres.";
        }

        if (!in_array($tipo, ['ingreso', 'egreso'])) {
            $errores[] = "El tipo debe ser 'ingreso' o 'egreso'.";
        }

        return $errores;
    }

    // FUN-015 Crear categoría
    public function crearCategoria($nombre, $tipo, $padreid = null)
    {
        try {
            $sql = "INSERT INTO categoria (nombre, tipo, padre_id, estado, fecha_creacion)
                    VALUES (:nombre, :tipo, :padre_id, 'activa', NOW())
                    RETURNING id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':nombre', trim($nombre), \PDO::PARAM_STR);
            $stmt->bindValue(':tipo', $tipo, \PDO::PARAM_STR);
            $stmt->bindValue(':padre_id', $padreid, \PDO::PARAM_INT);
            
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            return ['success' => true, 'id' => $result['id'], 'mensaje' => 'Categoría creada exitosamente.'];
        } catch (\PDOException $e) {
            error_log("Error en crearCategoria: " . $e->getMessage());
            return ['success' => false, 'mensaje' => 'Error al crear la categoría.'];
        }
    }

    // FUN-016 Editar categoría
    public function editarCategoria($idCategoria, $nombre, $tipo, $padreid = null)
    {
        try {
            $sql = "UPDATE categoria 
                    SET nombre = :nombre, 
                        tipo = :tipo, 
                        padre_id = :padre_id,
                        fecha_actualizacion = NOW()
                    WHERE id = :id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':nombre', trim($nombre), \PDO::PARAM_STR);
            $stmt->bindValue(':tipo', $tipo, \PDO::PARAM_STR);
            $stmt->bindValue(':padre_id', $padreid, \PDO::PARAM_INT);
            $stmt->bindValue(':id', $idCategoria, \PDO::PARAM_INT);
            
            $stmt->execute();
            
            return ['success' => true, 'mensaje' => 'Categoría actualizada exitosamente.'];
        } catch (\PDOException $e) {
            error_log("Error en editarCategoria: " . $e->getMessage());
            return ['success' => false, 'mensaje' => 'Error al actualizar la categoría.'];
        }
    }

    // FUN-017 Cambiar estado de categoría
    public function cambiarEstadoCategoria($idCategoria, $estado)
    {
        try {
            if (!in_array($estado, ['activa', 'inactiva'])) {
                return ['success' => false, 'mensaje' => 'Estado no válido.'];
            }

            $stmt = $this->conn->prepare("UPDATE categoria 
                                          SET estado = :estado, fecha_actualizacion = NOW()
                                          WHERE id = :id");
            $stmt->execute(['estado' => $estado, 'id' => $idCategoria]);
            
            return ['success' => true, 'mensaje' => 'Estado actualizado.'];
        } catch (\PDOException $e) {
            error_log("Error en cambiarEstadoCategoria: " . $e->getMessage());
            return ['success' => false, 'mensaje' => 'Error al cambiar el estado.'];
        }
    }
}
?>