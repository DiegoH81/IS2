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
            $result = $stmt->fetch(\PD