<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) session_start();
require "db.php";

if (!isset($_SESSION["usuario"]["id"])) {
    http_response_code(403);
    echo json_encode(["erro" => "Não autenticado"]);
    exit;
}

$usuario_id = $_SESSION["usuario"]["id"];
$acao = $_POST['acao'] ?? '';

if ($acao === 'adicionar' || $acao === 'editar') {
    $id = $_POST['id'] ?? null;
    $numero = $_POST['numero'] ?? '';
    $numero_mascarado = substr($numero, -4); // Apenas os 4 últimos dígitos para associar
    $titular = $_POST['titular'] ?? '';
    $validade = $_POST['validade'] ?? '';
    $cvv = $_POST['cvv'] ?? '';
    $salario = $_POST['salario'] ?? 0;
    $limite = $_POST['limite'] ?? 0;
    $tipo = $_POST['tipo'] ?? '';
    $principal = $_POST['principal'] ?? 1;

    if ($principal) {
        $pdo->prepare("UPDATE cartoes SET principal=0 WHERE usuario_id=?")->execute([$usuario_id]);
    }

    if ($acao === 'adicionar') {
        $stmt = $pdo->prepare("INSERT INTO cartoes (usuario_id, numero, titular, validade, cvv, salario, limite, tipo, principal)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $ok = $stmt->execute([$usuario_id, $numero, $titular, $validade, $cvv, $salario, $limite, $tipo, $principal]);
        $id = $pdo->lastInsertId();

        // Reassocia finanças pelo número do cartão (últimos 4 dígitos)
        foreach (['receitas', 'despesas', 'planos'] as $tabela) {
            $pdo->prepare(
                "UPDATE $tabela SET cartao_id=?, cartao_numero=?
                 WHERE usuario_id=? AND cartao_numero=?"
            )->execute([$id, $numero_mascarado, $usuario_id, $numero_mascarado]);
        }
    } else {
        $stmt = $pdo->prepare("UPDATE cartoes SET numero=?, titular=?, validade=?, cvv=?, salario=?, limite=?, tipo=?, principal=?
            WHERE id=? AND usuario_id=?");
        $ok = $stmt->execute([$numero, $titular, $validade, $cvv, $salario, $limite, $tipo, $principal, $id, $usuario_id]);
    }
    echo json_encode(["sucesso" => $ok, "id" => $id]);

    // Força atualização instantânea nas telas, se possível (via header extra, pode ser lido via JS)
    header("X-Greencash-Update: 1");
    exit;
}

if ($acao === 'listar') {
    $stmt = $pdo->prepare("SELECT * FROM cartoes WHERE usuario_id=?");
    $stmt->execute([$usuario_id]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

if ($acao === 'remover') {
    $id = $_POST['id'] ?? null;
    // Ao remover cartão, as finanças associadas ficam sem cartao_id
    foreach (['receitas', 'despesas', 'planos'] as $tabela) {
        $pdo->prepare("UPDATE $tabela SET cartao_id=NULL WHERE usuario_id=? AND cartao_id=?")->execute([$usuario_id, $id]);
    }
    $stmt = $pdo->prepare("DELETE FROM cartoes WHERE id=? AND usuario_id=?");
    $ok = $stmt->execute([$id, $usuario_id]);
    echo json_encode(["sucesso" => $ok]);

    // Força atualização instantânea nas telas, se possível (via header extra, pode ser lido via JS)
    header("X-Greencash-Update: 1");
    exit;
}

if ($acao === 'principal') {
    $id = $_POST['id'] ?? null;
    $pdo->prepare("UPDATE cartoes SET principal=0 WHERE usuario_id=?")->execute([$usuario_id]);
    $ok = $pdo->prepare("UPDATE cartoes SET principal=1 WHERE id=? AND usuario_id=?")->execute([$id, $usuario_id]);
    echo json_encode(["sucesso" => $ok]);
    exit;
}

echo json_encode(["erro" => "Ação inválida"]);
exit;
?>