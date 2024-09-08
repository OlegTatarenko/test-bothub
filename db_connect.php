<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'bothub';
$charset = 'utf8';

$dsn = "mysql:host=$host; dbname=$dbname; charset=$charset";

$opt = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $opt);
} catch(PDOException $e) {
    echo "Ошибка подключения: " . $e->getMessage();
}

return $pdo;