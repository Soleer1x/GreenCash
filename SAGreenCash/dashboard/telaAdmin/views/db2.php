<?php
$conn = new mysqli("localhost", "root", "", "greencash");
if($conn->connect_errno){
    die("Erro ao conectar: ".$conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>
