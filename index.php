<?php
header("Location: ui/UI-01_InicioDeSesion.php");
exit;
$host = "localhost";
$port = "5432";
$dbname = "oab_test2";
$user = "postgres";
$password = "ucsp123";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if ($conn) {
    echo "✅ Conexión exitosa a la base de datos.";
} else {
    echo "❌ Error al conectar a la base de datos.";
}
?>
