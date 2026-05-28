<?php
// config/db.php

define('DB_HOST',    'sql311.infinityfree.com');
 define('DB_NAME',    'if0_42026129_minuano');
 define('DB_USER',    'if0_42026129');
 define('DB_PASS',    'Mari280404');
define('DB_CHARSET', 'utf8mb4');


function getDB(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST .
               ";dbname=" . DB_NAME .
               ";charset=" . DB_CHARSET;

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            error_log('[DB ERROR] ' . $e->getMessage());
            die('Erro de conexão com o banco de dados.');
        }
    }

    return $pdo;
}