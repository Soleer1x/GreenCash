<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION["usuario"])) {
  http_response_code(401);
  echo json_encode(["erro" => "Não autenticado"]);
  exit;
}

$id = $_SESSION["usuario"]["id"];
$sql = "SELECT nome, email, telefone, localizacao, bio, foto FROM usuarios WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($nome, $email, $telefone, $localizacao, $bio, $foto);
$stmt->fetch();
$stmt->close();

// Retorne só a foto personalizada. Se não houver, retorna null/vazio (frontend usa a padrão)
echo json_encode([
  "nome" => $nome,
  "email" => $email,
  "telefone" => $telefone,
  "localizacao" => $localizacao,
  "bio" => $bio,
  "foto" => $foto ?: null
]);
?>

