<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['usuario']['id'])) {
    echo json_encode(['sucesso' => false, 'msg' => 'Não autenticado']);
    exit;
}
require_once 'db.php';

$usuario_id = intval($_SESSION['usuario']['id']);

// Inicia transação para garantir atomicidade
$conn->begin_transaction();

try {
    // Apaga receitas, despesas e planos do usuário
    $conn->query("DELETE FROM receitas WHERE usuario_id = $usuario_id");
    $conn->query("DELETE FROM despesas WHERE usuario_id = $usuario_id");
    $conn->query("DELETE FROM planos WHERE usuario_id = $usuario_id");

    // Marca no usuário que o histórico foi excluído (permanente)
    $conn->query("UPDATE usuarios SET historico_financeiro_excluido = 1 WHERE id = $usuario_id");

    $conn->commit();
    echo json_encode(['sucesso' => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['sucesso' => false, 'msg' => 'Erro ao excluir histórico: ' . $e->getMessage()]);
}