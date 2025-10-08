<?php
class Database {
    private static $host = "localhost";
    private static $port = "5432";
    private static $dbname = "oab_test2";
    private static $user = "postgres";
    private static $password = "ucsp123";
    private static $connection = null;

    public static function connect() {
        if (self::$connection === null) {
            $conn_string = "host=" . self::$host .
                           " port=" . self::$port .
                           " dbname=" . self::$dbname .
                           " user=" . self::$user .
                           " password=" . self::$password;
            self::$connection = pg_connect($conn_string);
        }
        return self::$connection;
    }
}
?>
