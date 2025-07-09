<?php
session_start();
require 'db.php'; // conexão PDO

// Garante que só usuários logados possam acessar
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$mensagem = '';
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['usuario_id'];
    $senha_atual = $_POST['senha_atual'] ?? '';
    $nova_senha = $_POST['nova_senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';

    // Validação
    if (empty($senha_atual) || empty($nova_senha) || empty($confirmar_senha)) {
        $mensagem = "Preencha todos os campos.";
    } elseif ($nova_senha !== $confirmar_senha) {
        $mensagem = "A nova senha e a confirmação não coincidem.";
    } else {
        // Busca senha atual do banco
        $stmt = $pdo->prepare('SELECT senha FROM usuarios WHERE id = ?');
        $stmt->execute([$usuario_id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario || !password_verify($senha_atual, $usuario['senha'])) {
            $mensagem = "Senha atual incorreta.";
        } else {
            // Atualiza senha
            $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE usuarios SET senha = ? WHERE id = ?');
            if ($stmt->execute([$nova_senha_hash, $usuario_id])) {
                $mensagem = "Senha alterada com sucesso!";
                $sucesso = true;
            } else {
                $mensagem = "Erro ao atualizar a senha.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Alterar Senha - Painel de Edição</title>
    <style>
        body {
            background: #f7f7f7;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 350px;
            margin: 60px auto;
            background: #fff;
            padding: 30px 25px 25px 25px;
            box-shadow: 0 0 8px #aaa;
            border-radius: 6px;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 24px;
        }
        label {
            font-weight: bold;
            color: #333;
        }
        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin: 6px 0 16px 0;
            border: 1px solid #bbb;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            background: #0066cc;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            margin-bottom: 10px;
        }
        button:hover {
            background: #004080;
        }
        .mensagem {
            background: #f8e1e1;
            color: #a00;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 16px;
            text-align: center;
        }
        .mensagem.sucesso {
            background: #e1f7e1;
            color: #007d21;
            border: 1px solid #a6e6a6;
        }
        a {
            display: block;
            text-align: center;
            color: #0066cc;
            text-decoration: none;
            margin-top: 18px;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Alterar Senha</h2>
        <?php if ($mensagem): ?>
            <div class="mensagem<?= $sucesso ? ' sucesso' : '' ?>">
                <?= htmlspecialchars($mensagem) ?>
            </div>
        <?php endif; ?>
        <form method="post" autocomplete="off">
            <label>Senha atual:</label>
            <input type="password" name="senha_atual" required>
            <label>Nova senha:</label>
            <input type="password" name="nova_senha" required>
            <label>Confirmar nova senha:</label>
            <input type="password" name="confirmar_senha" required>
            <button type="submit">Alterar Senha</button>
        </form>
        <a href="painel.php">Voltar ao painel</a>
    </div>
</body>
</html>