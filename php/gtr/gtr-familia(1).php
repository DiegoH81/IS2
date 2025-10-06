<?php
// CU-003 Gestionar Familia
// GTR-10 Clase para gestión de familias
// TAB-03 Familia

class GestionarFamilia
{
    private $conn;

    // FUN-030 Constructor
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // FUN-031 Validar campos de familia
    public function validarCampos($nombreFamilia, $contrasenaFamiliar)
    {
        $errores = [];

        if (empty(trim($nombreFamilia))) {
            $errores[] = "El nombre de la familia es obligatorio.";
        } elseif (strlen($nombreFamilia) > 100) {
            $errores[] = "El nombre no debe exceder 100 caracteres.";
        }

        if (empty($contrasenaFamiliar)) {
            $errores[] = "La contraseña familiar es obligatoria.";
        } elseif (strlen($contrasenaFamiliar) < 6) {
            $errores[] = "La contraseña familiar debe tener al menos 6 caracteres.";
        }

        return $errores;
    }

    // FUN-032 Crear grupo familiar
    public function crearGrupoFamiliar($nombreFamilia, $contrasenaFamiliar)
    {
        try {
            // Verificar si ya existe una familia con ese nombre
            $stmt = $this->conn->prepare("SELECT id FROM familia WHERE nombre ILIKE :nombre LIMIT 1");
            $stmt->execute(['nombre' => trim($nombreFamilia)]);
            if ($stmt->fetch()) {
                return ['success' => false, 'mensaje' => 'Ya existe una familia con ese nombre.'];
            }

            // Hash de la contraseña familiar
            $hashedPassword = password_hash($contrasenaFamiliar, PASSWORD_BCRYPT);

            $sql = "INSERT INTO familia (nombre, contrasena_familiar, fecha_creacion)
                    VALUES (:nombre, :contrasena, NOW())
                    RETURNING id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':nombre', trim($nombreFamilia), \PDO::PARAM_STR);
            $stmt->bindValue(':contrasena', $hashedPassword, \PDO::PARAM_STR);
            
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            return ['success' => true, 'id' => $result['id'], 'mensaje' => 'Familia creada exitosamente.'];
        } catch (\PDOException $e) {
            error_log("Error en crearGrupoFamiliar: " . $e->getMessage());
            return ['success' => false, 'mensaje' => 'Error al crear la familia.'];
        }
    }

    // FUN-033 Obtener familia por ID
    public function obtenerFamilia($idFamilia)
    {
        try {
            $stmt = $this->conn->prepare("SELECT id, nombre, fecha_creacion 
                                          FROM familia 
                                          WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $idFamilia]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error en obtenerFamilia: " . $e->getMessage());
            return null;
        }
    }

    // FUN-034 Validar contraseña familiar
    public function validarContrasenaFamiliar($idFamilia, $contrasena)
    {
        try {
            $stmt = $this->conn->prepare("SELECT contrasena_familiar FROM familia WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $idFamilia]);
            $familia = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($familia && password_verify($contrasena, $familia['contrasena_familiar'])) {
                return ['valido' => true];
            } else {
                return ['valido' => false, 'mensaje' => 'Contraseña familiar incorrecta.'];
            }
        } catch (\PDOException $e) {
            error_log("Error en validarContrasenaFamiliar: " . $e->getMessage());
            return ['valido' => false, 'mensaje' => 'Error al validar la contraseña.'];
        }
    }

    // FUN-035 Obtener cambio de vista del usuario
    public function obtenerCambioVista($idUsuario, $idFamilia)
    {
        try {
            // Verificar si el usuario pertenece a la familia
            $stmt = $this->conn->prepare("SELECT id FROM usuario 
                                          WHERE id = :usuario_id AND familia_id = :familia_id 
                                          LIMIT 1");
            $stmt->execute(['usuario_id' => $idUsuario, 'familia_id' => $idFamilia]);
            
            if ($stmt->fetch()) {
                return ['tiene_acceso' => true];
            } else {
                return ['tiene_acceso' => false, 'mensaje' => 'No tiene acceso a esta vista.'];
            }
        } catch (\PDOException $e) {
            error_log("Error en obtenerCambioVista: " . $e->getMessage());
            return ['tiene_acceso' => false, 'mensaje' => 'Error al verificar acceso.'];
        }
    }
}
?>