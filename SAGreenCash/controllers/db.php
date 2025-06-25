<?php
$host = 'localhost';
$db = 'SAGreenCash';
$user = 'root'; // Substitua pelo seu usuário do banco
$pass = ''; // Substitua pela sua senha do banco

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>