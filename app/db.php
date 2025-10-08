<?php
// Aqui manejamos la conexion a la base de datos
$host = "localhost";
$dbname = "oabdb"; // <- ESTE debe coincidir con el nombre de tu base en pgAdmin
$user = "postgres";
$password = "0";

try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    die();
}
?>