<?php
session_start();
$msg = "";
$login_sucesso = false;
$usuario_tipo = 0; // 0 = user, 1 = admin

require "db.php";

// Verificação de login já existente
if(isset($_SESSION["usuario"])) {
  // Se já está logado, redireciona para o local correto:
  if(isset($_SESSION["usuario"]["tipo"]) && $_SESSION["usuario"]["tipo"] == 1) {
    header("Location: /SAGreenCash/dashboard/telaAdmin/views/Painel.php");
    exit;
  }
  // Se não escolheu plano ainda, envia para escolher plano
  if(
    !isset($_SESSION["usuario"]["plano"]) ||
    $_SESSION["usuario"]["plano"] === null ||
    $_SESSION["usuario"]["plano"] === "" ||
    $_SESSION["usuario"]["plano"] === "0"
  ) {
    header("Location: escolher_plano.php");
    exit;
  }
  // Senão, vai para o dashboard
  header("Location: dashboard.php");
  exit;
}

// Cadastro de usuário
if(isset($_POST["cadastro"])) {
    $nome = $conn->real_escape_string($_POST["nome"]);
    $email = $conn->real_escape_string($_POST["email"]);
    $senha = $_POST["senha"];

    // Adição: agora salva com password_hash para novos usuários!
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Verifica se o e-mail já está cadastrado
    $check = $conn->query("SELECT id FROM usuarios WHERE email='$email'");
    $checkAdm = $conn->query("SELECT id FROM adm WHERE email='$email'");

    if($check->num_rows > 0 || $checkAdm->num_rows > 0) {
        $msg = "E-mail já cadastrado!";
    } else {
        $conn->query("INSERT INTO usuarios (nome, email, senha, tipo) VALUES ('$nome', '$email', '$senha_hash', 0)");
        // FAZ LOGIN AUTOMÁTICO APÓS CADASTRO!
        $user_id = $conn->insert_id;
        $_SESSION["usuario"] = [
          'id' => $user_id,
          'nome' => $nome,
          'email' => $email,
          'tipo' => 0,
          'plano' => null
      ];
        header("Location: escolher_plano.php");
        exit;
    }
}

// Esqueceu a senha - redefinir
if(isset($_POST["reset"])) {
  $email = $conn->real_escape_string($_POST["email"]);
  $novaSenha = $conn->real_escape_string($_POST["nova_senha"]);
  // Adição: usa password_hash para redefinição de senha!
  $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

  $check = $conn->query("SELECT id FROM usuarios WHERE email='$email'");
  if($check->num_rows) {
    $conn->query("UPDATE usuarios SET senha='$senhaHash' WHERE email='$email'");
    $msg = "Senha redefinida com sucesso! Faça login.";
  } else {
    $checkAdm = $conn->query("SELECT id FROM adm WHERE email='$email'");
    if($checkAdm->num_rows) {
      $conn->query("UPDATE adm SET senha='$senhaHash' WHERE email='$email'");
      $msg = "Senha de administrador redefinida com sucesso! Faça login.";
    } else {
      $msg = "E-mail não encontrado!";
    }
  }
}

// Login de usuário ou admin
if(isset($_POST["login"])) {
  $email = $conn->real_escape_string($_POST["email"]);
  $senha = $_POST['senha'];

  // Busca usuário comum
  $sql_check = "SELECT id, senha, ativo, email, nome, tipo, plano FROM usuarios WHERE email='$email' LIMIT 1";
  $res_check = $conn->query($sql_check);

  if($res_check->num_rows) {
    $user = $res_check->fetch_assoc();
    if($user['ativo'] == 0) {
      $msg = "Usuário desativado!";
    }
    // Adição: verifica com password_verify OU md5 para retrocompatibilidade
    elseif (
      (isset($user['senha']) && password_verify($senha, $user['senha']))
      || (isset($user['senha']) && $user['senha'] === md5($senha))
    ) {
      // Se logou com md5, atualiza imediatamente para hash seguro
      if ($user['senha'] === md5($senha)) {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $conn->query("UPDATE usuarios SET senha='$senha_hash' WHERE id='{$user['id']}'");
      }
      $_SESSION["usuario"] = [
        'id' => $user['id'],
        'nome' => $user['nome'],
        'email' => $user['email'],
        'tipo' => $user['tipo'],
        'plano' => $user['plano']
      ];
      $login_sucesso = true;
      $usuario_tipo = 0;
    } else {
      $msg = "Login ou senha inválidos!";
    }
  } else {
    // Busca admin
    $sql_adm = "SELECT id, email, nome, senha, tipo FROM adm WHERE email='$email' LIMIT 1";
    $res_adm = $conn->query($sql_adm);
    if($res_adm->num_rows) {
      $admin = $res_adm->fetch_assoc();
      // Adição: verifica com password_verify OU md5 para retrocompatibilidade
      if (
        (isset($admin['senha']) && password_verify($senha, $admin['senha']))
        || (isset($admin['senha']) && $admin['senha'] === md5($senha))
      ) {
        // Se logou com md5, atualiza imediatamente para hash seguro
        if ($admin['senha'] === md5($senha)) {
          $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
          $conn->query("UPDATE adm SET senha='$senha_hash' WHERE id='{$admin['id']}'");
        }
        $_SESSION["usuario"] = $admin;
        $_SESSION["usuario"]["tipo"] = 1;
        $login_sucesso = true;
        $usuario_tipo = 1;
      } else {
        $msg = "Login ou senha inválidos!";
      }
    } else {
      $msg = "Login ou senha inválidos!";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Login - GreenCash</title>
  <meta name="viewport" content="width=1024, initial-scale=1, maximum-scale=1">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
  <style>
body, html {
  height: 100%;
  margin: 0;
  font-family: 'Inter', Arial, sans-serif;
  background: #f7f9fa;
}
.main-container {
  display: flex;
  min-height: 100vh;
}
.left-side {
  background: #16d463;
  color: #fff;
  flex: 1.2;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-width: 320px;
  position: relative;
}
.left-side .logo {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 40px;
}
.left-side .logo-icon {
  font-size: 3.5em;
  margin-bottom: 10px;
}
.left-side .logo-text {
  font-size: 2em;
  font-weight: bold;
  letter-spacing: 0.5px;
}
.left-side .logo-desc {
  font-size: 1.1em;
  opacity: 0.92;
  margin-top: 6px;
  text-align: center;
}
.left-side .valemobi {
  position: absolute;
  bottom: 40px;
  left: 0;
  width: 100%;
  text-align: center;
  font-weight: bold;
  font-size: 1.2em;
  opacity: 0.93;
  letter-spacing: 0.5px;
}
.left-side .back-arrow {
  position: absolute;
  top: 24px;
  left: 32px;
  background: none;
  border: none;
  cursor: pointer;
  z-index: 10;
  padding: 0;
  display: flex;
  align-items: center;
  transition: opacity 0.18s;
}
.left-side .back-arrow:hover {
  opacity: 0.8;
}
.left-side .back-arrow .material-symbols-rounded {
  color: #fff;
  font-size: 2.3em;
  font-variation-settings: 'FILL' 1;
  filter: drop-shadow(0 2px 8px #0006);
}

@media (max-width: 900px) {
  .main-container {
    flex-direction: column;
  }
  .left-side, .right-side {
    min-width: 100vw;
    min-height: 40vh;
  }
  .right-side {
    min-height: 60vh;
  }
  .left-side .back-arrow {
    left: 16px;
    top: 16px;
  }
}

/* NOVO CSS PROFISSIONAL PARA OS FORMULÁRIOS */
.right-side {
  flex: 2;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #fff;
  min-width: 340px;
  min-height: 100vh;
}
.loginbox {
  box-sizing: border-box;
  background: #fff;
  box-shadow: 0 8px 32px 0 rgba(34,28,6,0.10), 0 2px 16px #d4a10711;
  border-radius: 18px;
  border: 1.5px solid #e3e8ee;
  max-width: 480px;
  width: 100%;
  margin: 4vh auto;
  padding: 3.5em 2.5em 2.5em 2.5em;
  position: relative;
  animation: fadeInUp .8s cubic-bezier(.4,0,.2,1);
  transition: box-shadow .3s, transform .3s, background .3s;
  opacity: 1;
  pointer-events: all;
}

.loginbox h2 {
  font-size: 1.8em;
  font-weight: 700;
  margin-bottom: 2em;
  color: #222;
  letter-spacing: 0.2px;
  text-align: left;
  display: flex;
  align-items: center;
  gap: 8px;
}

.input-group {
  display: flex;
  align-items: center;
  background: #f5f7fa;
  border-radius: 8px;
  border: 1.5px solid #e3e8ee;
  margin-bottom: 1.2em;
  padding: 0.5em 1em;
  transition: border 0.2s;
}
.input-group:focus-within {
  border: 1.5px solid #16d463;
  background: #f0fff5;
}
.input-group input {
  border: none;
  background: transparent;
  outline: none;
  flex: 1;
  font-size: 1.15em;
  padding: 1em 0;
  color: #222;
}
.input-group .material-symbols-rounded {
  color: #16d463;
  margin-left: 0.5em;
  font-size: 1.3em;
}

button[type="submit"], .loginbox .toggle-link {
  width: 100%;
  padding: 0.85em 0;
  border: none;
  border-radius: 8px;
  font-size: 1.08em;
  font-weight: 600;
  margin-bottom: 0.7em;
  cursor: pointer;
  transition: background 0.2s, color 0.2s, box-shadow 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5em;
}

button[type="submit"] {
  background: linear-gradient(90deg, #16d463 60%, #43a047 100%);
  color: #fff;
  box-shadow: 0 2px 8px #16d46322;
}
button[type="submit"]:hover {
  background: linear-gradient(90deg, #13b856 60%, #388e3c 100%);
}

.loginbox .toggle-link {
  background: #f5f7fa;
  color: #16d463;
  border: 1.5px solid #e3e8ee;
  font-weight: 500;
}
.loginbox .toggle-link:hover {
  background: #e6f9ee;
  color: #188f4c;
}

.msg {
  background: #ffe6e6;
  color: #c62828;
  border-radius: 6px;
  padding: 0.7em 1em;
  margin-bottom: 1.2em;
  font-size: 1em;
  border: 1px solid #ffcdd2;
  text-align: center;
}

@media (max-width: 500px) {
  .loginbox {
    padding: 1.5em 0.7em 1.2em 0.7em;
    max-width: 98vw;
  }
  .left-side .logo-text {
    font-size: 1.3em;
  }
}

/* Animação suave */
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(30px);}
  to { opacity: 1; transform: translateY(0);}
}
.hide {
  opacity: 0;
  pointer-events: none;
  transition: opacity .3s;
}
/* Modal de sucesso */
#modal-sucesso {
  display: none;
  position: fixed;
  top: 0; left: 0; width: 100vw; height: 100vh;
  z-index: 9999;
  background: rgba(0,0,0,0.25);
  align-items: center;
  justify-content: center;
}
#modal-sucesso .modal-content {
  background: #fff;
  padding: 2em 2.5em;
  border-radius: 18px;
  box-shadow: 0 4px 32px #16d46333;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1em;
  max-width: 90vw;
}
#modal-sucesso .modal-content .material-symbols-rounded {
  font-size: 3em;
  color: #16d463;
}
#modal-sucesso .modal-content .msg-sucesso {
  font-size: 1.25em;
  font-weight: 600;
  color: #222;
}
  </style>
  <script>
    function toggleForm(form) {
      document.getElementById('login-form').classList.add('hide');
      document.getElementById('cadastro-form').classList.add('hide');
      document.getElementById('reset-form').classList.add('hide');
      setTimeout(function() {
        document.getElementById('login-form').style.display = 'none';
        document.getElementById('cadastro-form').style.display = 'none';
        document.getElementById('reset-form').style.display = 'none';
        document.getElementById(form).style.display = 'block';
        setTimeout(function() {
          document.getElementById(form).classList.remove('hide');
        }, 30);
      }, 350);
    }
  </script>
</head>
<body>
  <div class="main-container">
    <div class="left-side">
      <!-- Minimalista flecha branca para voltar ao index -->
      <button class="back-arrow" onclick="window.location.href='index.html'" title="Voltar à página inicial">
        <span class="material-symbols-rounded">arrow_back</span>
      </button>
      <div class="logo">
        <span class="material-symbols-rounded logo-icon" style="font-size:3.5em;">account_balance_wallet</span>
        <span class="logo-text">GreenCash</span>
        <span class="logo-desc">Você no controle das suas finanças</span>
      </div>
      <div class="valemobi">GreenCash Financeiros | Licença criptografadas</div>
    </div>
    <div class="right-side">
      <div style="width:100%;">
        <form class="loginbox" id="login-form" method="post" autocomplete="off" style="display:block;">
          <h2>
            <span class="material-symbols-rounded" style="font-size:1.3em;vertical-align:middle;color:#43a047;margin-right:7px;">account_balance_wallet</span>
            GreenCash Login
          </h2>
          <?php if($msg): ?>
            <div class="msg"><?=htmlspecialchars($msg)?></div>
          <?php endif;?>
          <div class="input-group">
            <input type="email" name="email" placeholder="E-mail" required autofocus>
            <span class="material-symbols-rounded">mail</span>
          </div>
          <div class="input-group">
            <input type="password" name="senha" placeholder="Senha" required>
            <span class="material-symbols-rounded">lock</span>
          </div>
          <button type="submit" name="login">
            <span class="material-symbols-rounded" style="vertical-align:middle;font-size:1.1em;">login</span>
            Entrar
          </button>
          <button type="button" class="toggle-link" onclick="toggleForm('cadastro-form')">
            <span class="material-symbols-rounded" style="vertical-align:middle;font-size:1.1em;">person_add</span>
            Criar nova conta
          </button>
          <button type="button" class="toggle-link" onclick="toggleForm('reset-form')">
            <span class="material-symbols-rounded" style="vertical-align:middle;font-size:1.1em;">lock_reset</span>
            Esqueceu a senha?
          </button>
        </form>
        <form class="loginbox hide" id="cadastro-form" method="post" autocomplete="off" style="display:none;">
          <h2>
            <span class="material-symbols-rounded" style="font-size:1.3em;vertical-align:middle;color:#43a047;margin-right:7px;">person_add</span>
            Criar Conta
          </h2>
          <?php if($msg): ?>
            <div class="msg"><?=htmlspecialchars($msg)?></div>
          <?php endif;?>
          <div class="input-group">
  <input type="text" name="nome" placeholder="Nome completo" required pattern="[A-Za-zÀ-ÿ\s]+">
  <span class="material-symbols-rounded">person</span>
</div>
<script>
document.querySelector('input[name="nome"]').addEventListener('input', function (e) {
  this.value = this.value.replace(/[0-9]/g, '');
});
</script>
          <div class="input-group">
            <input type="email" name="email" placeholder="E-mail" required>
            <span class="material-symbols-rounded">mail</span>
          </div>
          <div class="input-group">
            <input type="password" name="senha" placeholder="Senha" required>
            <span class="material-symbols-rounded">lock</span>
          </div>
          <button type="submit" name="cadastro">
            <span class="material-symbols-rounded" style="vertical-align:middle;font-size:1.1em;">check_circle</span>
            Cadastrar
          </button>
          <button type="button" class="toggle-link" onclick="toggleForm('login-form')">
            <span class="material-symbols-rounded" style="vertical-align:middle;font-size:1.1em;">arrow_back</span>
            Já tenho conta
          </button>
        </form>
        <form class="loginbox hide" id="reset-form" method="post" autocomplete="off" style="display:none;">
          <h2>
            <span class="material-symbols-rounded" style="font-size:1.3em;vertical-align:middle;color:#43a047;margin-right:7px;">lock_reset</span>
            Redefinir Senha
          </h2>
          <?php if($msg): ?>
            <div class="msg"><?=htmlspecialchars($msg)?></div>
          <?php endif;?>
          <div class="input-group">
            <input type="email" name="email" placeholder="E-mail cadastrado" required>
            <span class="material-symbols-rounded">mail</span>
          </div>
          <div class="input-group">
            <input type="password" name="nova_senha" placeholder="Nova senha" required>
            <span class="material-symbols-rounded">lock</span>
          </div>
          <button type="submit" name="reset">
            <span class="material-symbols-rounded" style="vertical-align:middle;font-size:1.1em;">check_circle</span>
            Redefinir Senha
          </button>
          <button type="button" class="toggle-link" onclick="toggleForm('login-form')">
            <span class="material-symbols-rounded" style="vertical-align:middle;font-size:1.1em;">arrow_back</span>
            Voltar ao login
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal de sucesso -->
  <div id="modal-sucesso">
    <div class="modal-content">
      <span class="material-symbols-rounded">check_circle</span>
      <div class="msg-sucesso">Login realizado com sucesso!</div>
    </div>
  </div>

  <script>
  <?php if($login_sucesso): ?>
    document.getElementById('modal-sucesso').style.display = 'flex';
    setTimeout(function(){
      <?php if($usuario_tipo == 1): ?>
        window.location.href = '/SAGreenCash/dashboard/telaAdmin/views/Painel.php';
      <?php else: ?>
        window.location.href = 'dashboard.php';
      <?php endif; ?>
    }, 1500);
  <?php endif; ?>
  </script>
</body>
</html>