<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['usuario']['id'])) {
    echo json_encode(['excluido' => false]);
    exit;
}
require_once 'db.php';
$usuario_id = intval($_SESSION['usuario']['id']);
$sql = "SELECT historico_financeiro_excluido FROM usuarios WHERE id = $usuario_id";
$res = $conn->query($sql);
$row = $res ? $res->fetch_assoc() : null;
echo json_encode(['excluido' => $row && $row['historico_financeiro_excluido'] == 1]);