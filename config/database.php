<?php

//Conexion PDO a MySQL con manejo de errores
define('DB_HOST', 'localhost');
define('DB_NAME', 'velora_shop');
define('DB_USER', 'root');
define('DB_PASS', ''); // En XAMPP por defecto esta vacio
define('DB_CHARSET','utf8mb4');
function getConnection(): PDO {
    static $pdo = null;

    if ($pdo === null) {
    $dsn = "mysql:host=" . DB_HOST .
            ";dbname=" . DB_NAME .
            ";charset=" . DB_CHARSET;
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
         ]);
    } catch (PDOException $e) {
        // En produccion nunca mostrar el mensaje de error
     die("Error de conexion a la base de datos.");
     }
 }
 return $pdo;
}
