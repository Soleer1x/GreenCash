<?php
session_start();
header('Content-Type: application/json');
require 'db.php';

if (!isset($_SESSION["usuario"])) {
  echo json_encode(['sucesso'=>false, 'msg'=>'Não logado']);
  exit;
}

$userId = $_SESSION["usuario"]["id"];
$novo_plano = $_POST['novo_plano'] ?? '';
$senha = $_POST['senha'] ?? '';
$planos_validos = ['intermediario', 'avancado'];
if (!in_array($novo_plano, $planos_validos)) {
  echo json_encode(['sucesso'=>false, 'msg'=>'Plano inválido']);
  exit;
}

// Busca senha hash do usuário
$res = $conn->query("SELECT senha FROM usuarios WHERE id=$userId LIMIT 1");
if (!$res || !$res->num_rows) {
  echo json_encode(['sucesso'=>false, 'msg'=>'Usuário não encontrado']);
  exit;
}
$row = $res->fetch_assoc();
$hash_banco = $row['senha'];

// Verifica senha (password_hash OU md5 retrocompatível)
$senha_correta = false;
if (password_verify($senha, $hash_banco)) {
  $senha_correta = true;
} elseif ($hash_banco === md5($senha)) {
  $senha_correta = true;
  // Atualiza para hash seguro se ainda estiver em md5
  $senha_hash_nova = password_hash($senha, PASSWORD_DEFAULT);
  $conn->query("UPDATE usuarios SET senha='$senha_hash_nova' WHERE id=$userId");
}

if (!$senha_correta) {
  echo json_encode(['sucesso'=>false, 'msg'=>'Senha incorreta']);
  exit;
}

// Confere se tem cartão cadastrado (opcional, mas seu sistema pede isso)
$cartaoRes = $conn->query("SELECT id FROM cartoes WHERE usuario_id=$userId LIMIT 1");
if (!$cartaoRes || !$cartaoRes->num_rows) {
  echo json_encode(['sucesso'=>false, 'msg'=>'Você precisa cadastrar um cartão para assinar um plano.']);
  exit;
}

$conn->query("UPDATE usuarios SET plano='$novo_plano' WHERE id=$userId");
$_SESSION["usuario"]["plano"] = $novo_plano;
echo json_encode(['sucesso'=>true]);
?>