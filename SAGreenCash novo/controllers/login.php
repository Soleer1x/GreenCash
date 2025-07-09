<?php
// Ativar exibição de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configurações do banco de dados
$host = 'localhost';
$db = 'SAGreenCash';
$user = 'root'; // Troque pelo seu usuário do MySQL
$pass = ''; // Troque pela sua senha do MySQL

// Conexão com o banco de dados
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()]);
    exit;
}

// Verificar se a solicitação HTTP é do tipo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar os dados enviados pelo formulário
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validações básicas
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['error' => 'Email inválido']);
        exit;
    }

    if (empty($password)) {
        echo json_encode(['error' => 'Senha obrigatória']);
        exit;
    }

    try {
        // Consultar o banco de dados para encontrar o usuário pelo email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verificar se a senha fornecida corresponde à armazenada
            if (password_verify($password, $user['password_hash'])) { // Usar password_verify para comparar hashes
                echo json_encode(['success' => 'Login realizado com sucesso']);
            } else {
                echo json_encode(['error' => 'Senha incorreta']);
            }
        } else {
            echo json_encode(['error' => 'Usuário não encontrado']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Erro ao processar a consulta: ' . $e->getMessage()]);
    }
} else {
    // Retornar erro se o método HTTP não for POST
    echo json_encode(['error' => 'Método HTTP inválido']);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>GreenCash - Cadastro</title>
  <link rel="stylesheet" href="../views/css/login.css" />
  <link rel="icon" type="image/png" href="../views/imgs/Login-Cadastro/loggoooo.png" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <style>
    .error {
      color: red;
      font-size: 12px;
      font-weight: bold;
      display: none;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="form-box login">
      <form>
        <h1>Login</h1>
        <div class="input-box">
          <div id="email-required-error" class="error">Email é obrigatório</div>
          <div id="email-invalid-error" class="error">Email inválido</div>
          <input type="email" id="login-email" placeholder="Email" required />
          <i class='bx bxs-envelope'></i>
        </div>
        <div class="input-box">
          <input type="password" id="login-password" placeholder="Senha" required />
          <i class='bx bxs-lock-alt'></i>
        </div>
        <div class="forgot-link">
          <a href="#">Esqueceu a senha?</a>
        </div>
        <button type="button" class="btn" onclick="login()">Login</button>
      </form>
    </div>

    <div class="form-box register">
      <form>
        <h1>Cadastro</h1>
        <div class="input-box">
          <input type="text" id="username" placeholder="Nome de usuário" required />
          <i class='bx bxs-user'></i>
        </div>
        <div class="input-box">
          <div id="email-required-error-register" class="error">Email é obrigatório</div>
          <div id="email-invalid-error-register" class="error">Email inválido</div>
          <input type="email" id="email" placeholder="Email" onchange="onChangeEmail()" required />
          <i class='bx bxs-envelope'></i>
        </div>
        <div class="input-box">
          <div id="password-required-error" class="error">Senha obrigatória</div>
          <div id="password-min-length-error" class="error">A senha deve ter exatamente 8 caracteres</div>
          <input type="password" id="password" placeholder="Senha" onchange="onChangePassword()" required maxlength="8" />
          <i class='bx bxs-lock-alt'></i>
        </div>
        <div class="input-box">
          <div id="password-doesnt-match-error" class="error">As senhas devem ser iguais</div>
          <input type="password" id="confirmPassword" placeholder="Confirmar senha" onchange="onChangeConfirmPassword()" required maxlength="8" />
          <i class='bx bxs-lock-alt'></i>
        </div>
        <button type="button" class="btn" id="register-button" onclick="register()" disabled>Cadastrar</button>
      </form>
    </div>

    <div class="toggle-box">
      <div class="toggle-panel toggle-left">
        <h1>Olá, bem-vindo!</h1>
        <p>Não tem uma conta?</p>
        <button class="btn register-btn">Cadastre-se</button>
      </div>
      <div class="toggle-panel toggle-right">
        <h1>Bem-vindo de volta!</h1>
        <p>Já tem uma conta?</p>
        <button class="btn login-btn">Entrar</button>
      </div>
    </div>
  </div>

  <!-- Firebase -->
  <script src="https://www.gstatic.com/firebasejs/10.12.5/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/10.12.5/firebase-auth-compat.js"></script>
  <script src="../controllers/firebase-init.env"></script>

  <!-- Funções -->
  <script>
    const emailInput = document.getElementById("email");
    const passwordInput = document.getElementById("password");
    const confirmPasswordInput = document.getElementById("confirmPassword");
    const registerButton = document.getElementById("register-button");

    function validateEmail(email) {
      return /\S+@\S+\.\S+/.test(email);
    }

    function onChangeEmail() {
      const email = emailInput.value;
      document.getElementById("email-required-error-register").style.display = email ? "none" : "block";
      document.getElementById("email-invalid-error-register").style.display = validateEmail(email) ? "none" : "block";
      toggleRegisterButton();
    }

    function onChangePassword() {
      const password = passwordInput.value;
      document.getElementById("password-required-error").style.display = password ? "none" : "block";
      document.getElementById("password-min-length-error").style.display = password.length === 8 ? "none" : "block";
      toggleRegisterButton();
    }

    function onChangeConfirmPassword() {
      const password = passwordInput.value;
      const confirmPassword = confirmPasswordInput.value;
      document.getElementById("password-doesnt-match-error").style.display =
        password === confirmPassword ? "none" : "block";
      toggleRegisterButton();
    }

    function toggleRegisterButton() {
      const emailValid = emailInput.value && validateEmail(emailInput.value);
      const passwordValid = passwordInput.value && passwordInput.value.length === 8;
      const passwordsMatch = passwordInput.value === confirmPasswordInput.value;
      registerButton.disabled = !(emailValid && passwordValid && passwordsMatch);
    }

    function register() {
      firebase.auth()
        .createUserWithEmailAndPassword(emailInput.value, passwordInput.value)
        .then(() => {
          alert("Cadastro realizado com sucesso!");
          window.location.href = "../index.html";
        })
        .catch(error => {
          alert("Erro ao cadastrar: " + error.message);
        });
    }

    function login() {
  const email = document.getElementById("login-email").value;
  const password = document.getElementById("login-password").value;

  if (!validateEmail(email)) {
    alert("Email inválido");
    return;
  }

  // Enviar dados ao backend PHP
  fetch('login.php', { // Certifique-se de que login.php está no mesmo diretório ou ajuste o caminho
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: new URLSearchParams({
      email: email,
      password: password
    })
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert(data.success);

        // Redirecionar para a página do dashboard
        window.location.href = "dashboard/pages/dashboard.html";
      } else {
        alert(data.error);
      }
    })
    .catch(error => console.error("Erro ao fazer login: ", error));
}
  </script>
</body>
</html>
