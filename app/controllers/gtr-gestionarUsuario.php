<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../entities.php';

class GestionarUsuarioController {

    // =====================================================
    // Atributo: listaUsuarios
    // =====================================================
    private $listaUsuarios = [];

    // =====================================================
    // Método: cambiarEstadoUsuario
    // =====================================================
    public function cambiarEstadoUsuario($idUsuario) {
        global $conn;

        try {
            $stmt = $conn->prepare("UPDATE usuario SET estado = 
                CASE WHEN estado = 'activo' THEN 'inactivo' ELSE 'activo' END
                WHERE idusuario = :id");
            $stmt->bindParam(':id', $idUsuario);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error cambiarEstadoUsuario: " . $e->getMessage());
            return false;
        }
    }

    // =====================================================
    // Método: obtenerUsuarios
    // =====================================================
    public function obtenerUsuarios() {
        global $conn;

        try {
            $stmt = $conn->query("SELECT * FROM usuario");
            $usuarios = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $usuarios[] = new Usuario(
                    $row['idusuario'],
                    $row['idfamilia'],
                    $row['nombreusuario'],
                    $row['nombre'],
                    $row['apellido'],
                    $row['rol'],
                    $row['estado'],
                    $row['contraseña']
                );
            }
            $this->listaUsuarios = $usuarios;
            return $usuarios;
        } catch (PDOException $e) {
            error_log("Error obtenerUsuarios: " . $e->getMessage());
            return [];
        }
    }

    // =====================================================
    // Método: obtenerUsuario
    // =====================================================
    public function obtenerUsuario($nombreUsuario) {
        global $conn;

        try {
            $stmt = $conn->prepare("SELECT * FROM usuario WHERE nombreusuario = :nombre");
            $stmt->bindParam(':nombre', $nombreUsuario);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return new Usuario(
                    $row['idusuario'],
                    $row['idfamilia'],
                    $row['nombreusuario'],
                    $row['nombre'],
                    $row['apellido'],
                    $row['rol'],
                    $row['estado'],
                    $row['contraseña']
                );
            }
            return null;
        } catch (PDOException $e) {
            error_log("Error obtenerUsuario: " . $e->getMessage());
            return null;
        }
    }

    // =====================================================
    // Método: validarInformacion
    // =====================================================
    public function validarInformacion($usuario, $contraseña) {
        global $conn;

        try {
            $stmt = $conn->prepare("SELECT * FROM usuario WHERE nombreusuario = :nombre AND contraseña = :pass");
            $stmt->bindParam(':nombre', $usuario);
            $stmt->bindParam(':pass', $contraseña);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error validarInformacion: " . $e->getMessage());
            return false;
        }
    }

    // =====================================================
    // Método: consultarUsuario
    // =====================================================
    public function consultarUsuario($nombreUsuario) {
        global $conn;

        try {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM usuario WHERE nombreusuario = :nombre");
            $stmt->bindParam(':nombre', $nombreUsuario);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error consultarUsuario: " . $e->getMessage());
            return false;
        }
    }

    // =====================================================
    // Método: solicitarCrearNuevoUsuario
    // =====================================================
    public function solicitarCrearNuevoUsuario($usuario, $nombre, $rol, $contraseña, $confirmarContraseña) {
        global $conn;

        if ($contraseña !== $confirmarContraseña) {
            return false;
        }

        try {
            $stmt = $conn->prepare("INSERT INTO usuario (nombreusuario, nombre, rol, contraseña, estado)
                                    VALUES (:nombreusuario, :nombre, :rol, :contraseña, 'activo')");
            $stmt->bindParam(':nombreusuario', $usuario);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':rol', $rol);
            $stmt->bindParam(':contraseña', $contraseña);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error solicitarCrearNuevoUsuario: " . $e->getMessage());
            return false;
        }
    }

    // =====================================================
    // Método: filtrarUsuarios
    // =====================================================
    public function filtrarUsuarios($criterio) {
        global $conn;

        try {
            $stmt = $conn->prepare("SELECT * FROM usuario 
                                    WHERE nombre ILIKE :criterio OR nombreusuario ILIKE :criterio");
            $like = '%' . $criterio . '%';
            $stmt->bindParam(':criterio', $like);
            $stmt->execute();

            $usuarios = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $usuarios[] = new Usuario(
                    $row['idusuario'],
                    $row['idfamilia'],
                    $row['nombreusuario'],
                    $row['nombre'],
                    $row['apellido'],
                    $row['rol'],
                    $row['estado'],
                    $row['contraseña']
                );
            }
            return $usuarios;
        } catch (PDOException $e) {
            error_log("Error filtrarUsuarios: " . $e->getMessage());
            return [];
        }
    }
}
?>
