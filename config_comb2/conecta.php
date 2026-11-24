<?php
// Conexão com o MySQL
$host = '10.17.0.5';
$dbname = 'bdfrota';
$user = 'blvp';
$pass = '@F3r7n6c2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar: " . $e->getMessage());
}
?>