<?php

session_start();
require_once 'auth.php';

$host = 'localhost'; 
$dbname = 'becco'; 
$user = 'jax'; 
$pass = 'jax';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

?>

<!DOCTYPE html>
<html lang = "en">
    <head>
    <title>Becco Church</title>
    <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <h1>Becco Church</h1>
        <p>Welcome to my starter website for my project in web development!</p>
    </body>
</html>
