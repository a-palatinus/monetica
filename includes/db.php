<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'monetica');

if (!defined('BASE_URL')) {
    $docRoot  = realpath($_SERVER['DOCUMENT_ROOT']);
    $projRoot = realpath(dirname(__DIR__));
    $relative = substr($projRoot, strlen($docRoot));
    define('BASE_URL', rtrim(str_replace('\\', '/', $relative), '/'));
}

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die("<p style='color:red; padding:20px;'>Greška pri spajanju na bazu: " . $e->getMessage() . "<br>Pokreni <a href='" . BASE_URL . "/setup.php'>setup.php</a> za inicijalizaciju.</p>");
}
