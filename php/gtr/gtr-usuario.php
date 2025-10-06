<?php
// CU-001 Gestionar Usuarios
// GTR-01 Clase para gestión de usuarios
// TAB-01 Usuario

class GestionarUsuario
{
    private $conn;

    // FUN-020 Constructor
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // FUN-021 Validar datos de usuario
    public function validarDatos($usuario, $nombre, $contrasena, $confirmarContrasena, $correo, $rol)
    {
        $errores = [];

        // Validar usuario
        if (empty(trim($usuario))) {
            $errores[] = "El nombre de usuario es obligatorio.";
        } elseif (strlen($usuario) < 3 || strlen($usuario) > 50) {
            $errores[] = "El usuario debe tener entre 3 y 50 caracteres.";
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $usuario)) {
            $errores[] = "El usuario solo puede contener letras, números y guiones bajos.";
        }

        // Validar nombre
        if (empty(trim($nombre))) {
            $errores[] = "El nombre es obligatorio.";
        } elseif (strlen($nombre) > 100) {
            $errores[] = "El nombre no debe exceder 100 caracteres.";
        }

        // Validar contraseña
        if (empty($contrasena)) {
            $errores[] = "La contraseña es obligatoria.";
        } elseif (strlen($contrasena) < 6) {
            $errores[] = "La contraseña debe tener al menos 6 caracteres.";
        }

        // Validar confirmación de contraseña
        if ($contrasena !== $confirmarContrasena) {
            $errores[] = "Las contraseñas no coinciden.";
        }

        // Validar correo
        if (empty(trim($correo))) {
            $errores[] = "El correo electrónico es obligatorio.";
        } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El correo electrónico no es válido.";
        }

        // Validar rol
        if (!in_array($rol, ['papa', 'mama', 'nino', 'adulto'])) {
            $errores[] = "El rol seleccionado no es válido.";
        }

        return $errores;
    }

    // FUN-022 Validar ingreso (login)
    public function validarIngreso($usuario, $contrasena)
    {
        $errores = [];

        if (empty(trim($usuario))) {
            $errores[] = "El usuario es obligatorio.";
        }

        if (empty($contrasena)) {
            $errores[] = "La contraseña es obligatoria.";
        }

        if (!empty($errores)) {
            return ['valido' => false, 'errores' => $errores];
        }

        try {
            $stmt = $this->conn->prepare("SELECT id, nickname, nombre, contrasena, correo_electronico, 
                                          rol, familia_id, foto
                                          FROM usuario 
                                          WHERE nickname = :usuario AND estado = 'activo' 
                                          LIMIT 1");
            $stmt->execute(['usuario' => $usuario]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($user && password_verify($contrasena, $user['contrasena'])) {
                return ['valido' => true, 'usuario' => $user];
            } else {
                return ['valido' => false, 'errores' => ['Usuario o contraseña incorrectos.']];
            }
        } catch (\PDOException $e) {
            error_log("Error en validarIngreso: " . $e->getMessage());
            return ['valido' => false, 'errores' => ['Error al validar las credenciales.']];
        }
    }

    // FUN-023 Crear usuario
    public function crearUsuario($usuario, $nombre, $contrasena, $correo, $rol, $familiaId)
    {
        try {
            // Verificar si el usuario ya existe
            $stmt = $this->conn->prepare("SELECT id FROM usuario WHERE nickname = :usuario LIMIT 1");
            $stmt->execute(['usuario' => $usuario]);
            if ($stmt->fetch()) {
                return ['success' => false, 'mensaje' => 'El nombre de usuario ya está en uso.'];
            }

            // Verificar si el correo ya existe
            $stmt = $this->conn->prepare("SELECT id FROM usuario WHERE correo_electronico = :correo LIMIT 1");
            $stmt->execute(['correo' => $correo]);
            if ($stmt->fetch()) {
                return ['success' => false, 'mensaje' => 'El correo electrónico ya está registrado.'];
            }

            // Hash de la contraseña
            $hashedPassword = password_hash($contrasena, PASSWORD_BCRYPT);

            $sql = "INSERT INTO usuario (nickname, nombre, contrasena, correo_electronico, 
                    rol, familia_id, estado, fecha_creacion)
                    VALUES (:nickname, :nombre, :contrasena, :correo, :rol, :familia_id, 
                    'activo', NOW())
                    RETURNING id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':nickname', trim($usuario), \PDO::PARAM_STR);
            $stmt->bindValue(':nombre', trim($nombre), \PDO::PARAM_STR);
            $stmt->bindValue(':contrasena', $hashedPassword, \PDO::PARAM_STR);
            $stmt->bindValue(':correo', trim($correo), \PDO::PARAM_STR);
            $stmt->bindValue(':rol', $rol, \PDO::PARAM_STR);
            $stmt->bindValue(':familia_id', $familiaId, \PDO::PARAM_INT);
            
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            return ['success' => true, 'id' => $result['id'], 'mensaje' => 'Usuario creado exitosamente.'];
        } catch (\PDOException $e) {
            error_log("Error en crearUsuario: " . $e->getMessage());
            return ['success' => false, 'mensaje' => 'Error al crear el usuario.'];
        }
    }

    // FUN-024 Obtener usuario por ID
    public function obtenerUsuario($idUsuario)
    {
        try {
            $stmt = $this->conn->prepare("SELECT id, nickname, nombre, correo_electronico, 
                                          rol, familia_id, foto, fecha_creacion
                                          FROM usuario 
                                          WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $idUsuario]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error en obtenerUsuario: " . $e->getMessage());
            return null;
        }
    }

    // FUN-025 Obtener usuarios de una familia
    public function obtenerUsuariosFamilia($familiaId)
    {
        try {
            $stmt = $this->conn->prepare("SELECT id, nickname, nombre, correo_electronico, rol, foto
                                          FROM usuario 
                                          WHERE familia_id = :familia_id AND estado = 'activo'
                                          ORDER BY rol, nombre");
            $stmt->execute(['familia_id' => $familiaId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error en obtenerUsuariosFamilia: " . $e->getMessage());
            return [];
        }
    }

    // FUN-026 Eliminar usuario (soft delete)
    public function eliminarUsuario($idUsuario, $familiaId)
    {
        try {
            // Verificar que el usuario pertenece a la familia
            $usuario = $this->obtenerUsuario($idUsuario);
            if (!$usuario || $usuario['familia_id'] != $familiaId) {
                return ['success' => false, 'mensaje' => 'No tiene permisos para eliminar este usuario.'];
            }

            $stmt = $this->conn->prepare("UPDATE usuario 
                                          SET estado = 'inactivo', fecha_actualizacion = NOW()
                                          WHERE id = :id AND familia_id = :familia_id");
            $stmt->execute(['id' => $idUsuario, 'familia_id' => $familiaId]);
            
            return ['success' => true, 'mensaje' => 'Usuario eliminado exitosamente.'];
        } catch (\PDOException $e) {
            error_log("Error en eliminarUsuario: " . $e->getMessage());
            return ['success' => false, 'mensaje' => 'Error al eliminar el usuario.'];
        }
    }
}
?>