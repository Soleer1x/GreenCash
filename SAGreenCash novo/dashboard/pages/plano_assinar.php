<?php
session_start();
header('Content-Type: application/json');
require 'db.php';

if (!isset($_SESSION["usuario"])) {
    echo json_encode(['sucesso' => false, 'msg' => 'não logado']);
    exit;
}

$userId   = $_SESSION["usuario"]["id"];
$plano    = $_POST['plano']    ?? '';
$numero   = $_POST['numero']   ?? '';
$titular  = $_POST['titular']  ?? '';
$validade = $_POST['validade'] ?? '';
$cvv      = $_POST['cvv']      ?? '';
$tipo     = $_POST['tipo']     ?? '';
$salario  = $_POST['salario']  ?? '';
$limite   = $_POST['limite']   ?? '';

// Validação simples dos dados
if (!$plano || !$numero || !$titular || !$validade || !$cvv || !$tipo || !$salario || !$limite) {
    echo json_encode(['sucesso' => false, 'msg' => 'Dados incompletos']);
    exit;
}

// (Opcional) Verifica se o usuário já tem um cartão principal, e define principal=0 para os outros
$conn->query("UPDATE cartoes SET principal=0 WHERE usuario_id=$userId");

// Salva o cartão
$stmt = $conn->prepare("INSERT INTO cartoes (usuario_id, numero, titular, validade, cvv, tipo, salario, limite, principal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
$stmt->bind_param("issssssi", $userId, $numero, $titular, $validade, $cvv, $tipo, $salario, $limite);
$stmt->execute();

// Atualiza o plano do usuário
$conn->query("UPDATE usuarios SET plano='$plano' WHERE id=$userId");

// Atualiza o plano na SESSION para efeito imediato no menu e controle de acesso
$_SESSION["usuario"]["plano"] = $plano;

echo json_encode(['sucesso' => true]);