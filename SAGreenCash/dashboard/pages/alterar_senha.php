<?php
session_start();
if (!isset($_SESSION["usuario"])) {
  header("Location: ../login.php");
  exit;
}

require_once 'db.php'; // arquivo de conexão PDO

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $novaSenha = $_POST['nova_senha'] ?? '';
  $confirmaSenha = $_POST['confirma_senha'] ?? '';
  
  if (strlen($novaSenha) < 6) {
    $msg = "A senha deve ter pelo menos 6 caracteres.";
  } elseif ($novaSenha !== $confirmaSenha) {
    $msg = "As senhas não conferem.";
  } else {
    $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
    $id = $_SESSION["usuario"]["id"];
    try {
      $stmt = $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE id = :id");
      if ($stmt->execute(['senha' => $hash, 'id' => $id])) {
        $msg = "Senha alterada com sucesso!";
      } else {
        $msg = "Erro ao alterar senha. Tente novamente.";
      }
    } catch (Exception $e) {
      $msg = "Erro ao alterar senha. Tente novamente.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Alterar Senha</title>
  <link rel="stylesheet" href="../assets/css/material-dashboard.css?v=3.2.0" />
  <style>
    body { background: #f8fafc; }
    .senha-container { max-width: 420px; margin:80px auto; background: #fff; border-radius:14px; box-shadow:0 2px 32px #0001; padding:32px 28px;}
    .senha-container h3 { margin-bottom: 16px;}
    .form-label { font-weight: 600; }
  </style>
</head>
<body>
  <div class="senha-container">
    <h3>Alterar Senha</h3>
    <?php if ($msg): ?>
      <div class="alert alert-<?=(strpos($msg, 'sucesso')!==false?'success':'danger')?>"><?=$msg?></div>
    <?php endif; ?>
    <form method="post" autocomplete="off">
      <div class="mb-3">
        <label class="form-label" for="nova_senha">Nova Senha</label>
        <input type="password" class="form-control" id="nova_senha" name="nova_senha" required minlength="6" autocomplete="new-password">
      </div>
      <div class="mb-3">
        <label class="form-label" for="confirma_senha">Confirmar Nova Senha</label>
        <input type="password" class="form-control" id="confirma_senha" name="confirma_senha" required minlength="6" autocomplete="new-password">
      </div>
      <button type="submit" class="btn btn-success w-100">Salvar Nova Senha</button>
    </form>
    <!-- Botão voltar para o profile -->
    <button type="button" class="btn btn-outline-secondary w-100 mt-2" onclick="window.location.href='profile.php'">
      Voltar para o perfil
    </button>
  </div>
</body>
</html>