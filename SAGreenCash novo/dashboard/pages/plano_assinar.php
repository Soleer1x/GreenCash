<?php
session_start();
header('Content-Type: application/json');
require 'db.php';

if (!isset($_SESSION["usuario"])) {
    echo json_encode(['sucesso' => false, 'msg' => 'Não autenticado']);
    exit;
}

// Debug: Registrar os dados recebidos
error_log("Dados recebidos: " . print_r($_POST, true));

$userId   = $_SESSION["usuario"]["id"];
$plano    = $_POST['plano']    ?? '';
$numero   = $_POST['numero']   ?? '';
$titular  = $_POST['titular']  ?? '';
$validade = $_POST['validade'] ?? '';
$cvv      = $_POST['cvv']      ?? '';
$tipo     = $_POST['tipo']     ?? '';
$limite   = $_POST['limite']   ?? '';

// Validação dos dados
$camposObrigatorios = [
    'plano' => $plano,
    'numero' => $numero,
    'titular' => $titular,
    'validade' => $validade,
    'cvv' => $cvv,
    'tipo' => $tipo,
    'limite' => $limite
];

foreach ($camposObrigatorios as $campo => $valor) {
    if (empty($valor)) {
        echo json_encode(['sucesso' => false, 'msg' => "O campo $campo é obrigatório"]);
        exit;
    }
}

try {
    // Inicia transação
    $conn->begin_transaction();

    // Define todos os cartões como não principais
    $conn->query("UPDATE cartoes SET principal=0 WHERE usuario_id=$userId");

    // Insere o novo cartão
    $stmt = $conn->prepare("INSERT INTO cartoes (usuario_id, numero, titular, validade, cvv, tipo, limite, principal) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("isssssi", $userId, $numero, $titular, $validade, $cvv, $tipo, $limite);
    $stmt->execute();

    // Atualiza o plano do usuário
    $conn->query("UPDATE usuarios SET plano='$plano' WHERE id=$userId");

    // Atualiza a sessão
    $_SESSION["usuario"]["plano"] = $plano;

    // Confirma a transação
    $conn->commit();

    echo json_encode(['sucesso' => true]);
} catch (Exception $e) {
    $conn->rollback();
    error_log("Erro ao assinar plano: " . $e->getMessage());
    echo json_encode(['sucesso' => false, 'msg' => 'Erro no servidor: ' . $e->getMessage()]);
}