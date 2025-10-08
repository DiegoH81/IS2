<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../entities.php';

class ValidarController {

    // =====================================================
    // Método: validarLogin
    // =====================================================
    public function validarLogin($usuario, $contraseña) {
        global $conn;

        try {
            $stmt = $conn->prepare("SELECT * FROM usuario WHERE nombreusuario = :usuario AND contraseña = :pass");
            $stmt->bindParam(':usuario', $usuario);
            $stmt->bindParam(':pass', $contraseña);
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
            error_log("Error validarLogin: " . $e->getMessage());
            return null;
        }
    }

    // =====================================================
    // Método: existeUsuario
    // =====================================================
    public function existeUsuario($usuario) {
        global $conn;

        try {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM usuario WHERE nombreusuario = :usuario");
            $stmt->bindParam(':usuario', $usuario);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error existeUsuario: " . $e->getMessage());
            return false;
        }
    }
}
?>
