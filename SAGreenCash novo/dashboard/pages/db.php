<?php
$conn = new mysqli("localhost", "root", "", "greencash");
if($conn->connect_errno){
    die("Erro ao conectar: ".$conn->connect_error);
}
$conn->set_charset("utf8mb4");
$pdo = new PDO('mysql:host=localhost;dbname=greencash;charset=utf8mb4', 'root', ''); // ajuste usuário/senha/banco
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
