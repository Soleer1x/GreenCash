<?php
session_start();
if (!isset($_SESSION["usuario"])) {
  header("Location: ../login.php");
  exit;
}

require_once 'db.php';

$msg = "";
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $novaSenha = $_POST['nova_senha'] ?? '';
  $confirmaSenha = $_POST['confirma_senha'] ?? '';
  $id = $_SESSION["usuario"]["id"];

  // Buscar o hash da senha atual
  $stmt = $pdo->prepare("SELECT senha FROM usuarios WHERE id = :id");
  $stmt->execute(['id' => $id]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  $senha_atual_hash = $user['senha'] ?? '';

  if (strlen($novaSenha) < 6) {
    $msg = "A senha deve ter pelo menos 6 caracteres.";
  } elseif ($novaSenha !== $confirmaSenha) {
    $msg = "As senhas não conferem.";
  } elseif (password_verify($novaSenha, $senha_atual_hash)) {
    $msg = "A nova senha não pode ser igual à senha atual.";
  } else {
    $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
    try {
      $stmt = $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE id = :id");
      if ($stmt->execute(['senha' => $hash, 'id' => $id])) {
        $success = true;
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>GreenCash Alterar Senha</title>
  <style>
    :root {
      --primary-green: #00D4AA;
      --light-green: #4FFFDF;
      --dark-green: #00B894;
      --accent-green: #26DE81;
      --mint-green: #A8E6CF;
      --sage-green: #B8E994;
      
      --pure-white: #FFFFFF;
      --off-white: #FAFBFC;
      --light-gray: #F8F9FA;
      --medium-gray: #E9ECEF;
      --text-dark: #2D3748;
      --text-gray: #718096;
      
      --gradient-primary: linear-gradient(135deg, #00D4AA 0%, #4FFFDF 50%, #26DE81 100%);
      --gradient-secondary: linear-gradient(135deg, #A8E6CF 0%, #B8E994 100%);
      --gradient-overlay: linear-gradient(135deg, rgba(0, 212, 170, 0.1) 0%, rgba(79, 255, 223, 0.1) 100%);
      
      --shadow-soft: 0 8px 32px rgba(0, 212, 170, 0.1);
      --shadow-medium: 0 16px 48px rgba(0, 212, 170, 0.15);
      --shadow-strong: 0 24px 64px rgba(0, 212, 170, 0.2);
      --shadow-colored: 0 12px 40px rgba(0, 212, 170, 0.25);
      
      --border-radius: 20px;
      --border-radius-small: 12px;
      --border-radius-large: 32px;
      
      --animation-fast: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
      --animation-normal: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      --animation-slow: 0.5s cubic-bezier(0.4, 0, 0.2, 1);
      --animation-bounce: 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: var(--gradient-primary);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
      position: relative;
      overflow-x: hidden;
    }

    /* Animações de fundo dinâmicas */
    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: 
        radial-gradient(circle at 15% 25%, rgba(0, 212, 170, 0.3) 0%, transparent 45%),
        radial-gradient(circle at 85% 75%, rgba(79, 255, 223, 0.25) 0%, transparent 50%),
        radial-gradient(circle at 50% 10%, rgba(38, 222, 129, 0.2) 0%, transparent 40%),
        radial-gradient(circle at 25% 90%, rgba(168, 230, 207, 0.3) 0%, transparent 45%);
      animation: floatingBg 25s ease-in-out infinite;
      z-index: -2;
    }

    @keyframes floatingBg {
      0%, 100% { 
        opacity: 1;
        transform: scale(1) rotate(0deg);
      }
      33% { 
        opacity: 0.8;
        transform: scale(1.1) rotate(1deg);
      }
      66% { 
        opacity: 0.9;
        transform: scale(0.95) rotate(-1deg);
      }
    }

    /* Partículas flutuantes sofisticadas */
    .floating-particles {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: -1;
    }

    .particle {
      position: absolute;
      background: rgba(255, 255, 255, 0.6);
      border-radius: 50%;
      animation: float 15s infinite linear;
    }

    .particle:nth-child(1) { width: 6px; height: 6px; left: 10%; animation-delay: 0s; }
    .particle:nth-child(2) { width: 4px; height: 4px; left: 20%; animation-delay: 2s; }
    .particle:nth-child(3) { width: 8px; height: 8px; left: 30%; animation-delay: 4s; }
    .particle:nth-child(4) { width: 3px; height: 3px; left: 40%; animation-delay: 6s; }
    .particle:nth-child(5) { width: 5px; height: 5px; left: 50%; animation-delay: 8s; }
    .particle:nth-child(6) { width: 7px; height: 7px; left: 60%; animation-delay: 10s; }
    .particle:nth-child(7) { width: 4px; height: 4px; left: 70%; animation-delay: 12s; }
    .particle:nth-child(8) { width: 6px; height: 6px; left: 80%; animation-delay: 14s; }
    .particle:nth-child(9) { width: 3px; height: 3px; left: 90%; animation-delay: 16s; }

    @keyframes float {
      0% {
        transform: translateY(100vh) rotate(0deg);
        opacity: 0;
      }
      10% {
        opacity: 1;
      }
      90% {
        opacity: 1;
      }
      100% {
        transform: translateY(-100px) rotate(360deg);
        opacity: 0;
      }
    }

    /* Container principal com design premium */
    .password-container {
      width: 100%;
      max-width: 420px;
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(30px) saturate(180%);
      -webkit-backdrop-filter: blur(30px) saturate(180%);
      border: 1px solid rgba(255, 255, 255, 0.3);
      border-radius: var(--border-radius-large);
      box-shadow: var(--shadow-strong);
      padding: 2.5rem;
      position: relative;
      overflow: hidden;
      animation: slideInScale 0.8s var(--animation-bounce);
      margin: auto;
    }

    @keyframes slideInScale {
      0% {
        opacity: 0;
        transform: translateY(60px) scale(0.9);
      }
      100% {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    /* Borda animada superior */
    .password-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 6px;
      background: var(--gradient-primary);
      border-radius: var(--border-radius-large) var(--border-radius-large) 0 0;
    }

    /* Efeito de brilho rotativo */
    .password-container::after {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: conic-gradient(
        from 0deg,
        transparent,
        rgba(0, 212, 170, 0.03),
        transparent,
        rgba(79, 255, 223, 0.03),
        transparent
      );
      animation: shimmer 25s linear infinite;
      z-index: -1;
    }

    @keyframes shimmer {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    /* Cabeçalho com design moderno */
    .header-section {
      text-align: center;
      margin-bottom: 2rem;
      position: relative;
    }

    .security-icon {
      width: 60px;
      height: 60px;
      background: var(--gradient-primary);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1rem;
      box-shadow: var(--shadow-colored);
      animation: pulse 3s ease-in-out infinite;
      position: relative;
    }

    .security-icon::before {
      content: '';
      position: absolute;
      width: 100%;
      height: 100%;
      border: 2px solid var(--primary-green);
      border-radius: 50%;
      animation: ripple 2s ease-out infinite;
    }

    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.05); }
    }

    @keyframes ripple {
      0% {
        transform: scale(1);
        opacity: 1;
      }
      100% {
        transform: scale(1.3);
        opacity: 0;
      }
    }

    .security-icon i {
      font-size: 1.5rem;
      color: var(--pure-white);
    }

    .main-title {
      font-size: 1.8rem;
      font-weight: 700;
      background: var(--gradient-primary);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 0.5rem;
      letter-spacing: -0.5px;
    }-webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 0.5rem;
      letter-spacing: -0.5px;
    }

    .subtitle {
      color: var(--text-gray);
      font-size: 1rem;
      font-weight: 400;
      margin-bottom: 0;
    }

    /* Formulário com design premium */
    .form-section {
      position: relative;
    }

    .input-group {
      position: relative;
      margin-bottom: 2rem;
    }

    .input-label {
      display: block;
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 0.75rem;
      font-size: 0.95rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .input-label i {
      color: var(--primary-green);
      font-size: 1rem;
    }

    .input-field {
      width: 100%;
      padding: 1.25rem 1.5rem;
      border: 2px solid var(--medium-gray);
      border-radius: var(--border-radius);
      font-size: 1rem;
      font-weight: 400;
      background: var(--pure-white);
      transition: var(--animation-normal);
      position: relative;
      z-index: 1;
    }

    .input-field:focus {
      outline: none;
      border-color: var(--primary-green);
      box-shadow: 0 0 0 4px rgba(0, 212, 170, 0.1), var(--shadow-soft);
      transform: translateY(-2px);
    }

    .input-field:hover {
      border-color: var(--light-green);
      transform: translateY(-1px);
    }

    /* Indicador de força da senha */
    .password-strength {
      margin-top: 0.5rem;
      height: 4px;
      background: var(--medium-gray);
      border-radius: 2px;
      overflow: hidden;
      transition: var(--animation-normal);
    }

    .strength-bar {
      height: 100%;
      width: 0%;
      background: var(--gradient-primary);
      transition: var(--animation-normal);
      border-radius: 2px;
    }

    /* Botões com design premium */
    .btn-group {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      margin-top: 2.5rem;
    }

    .btn-primary {
      background: var(--gradient-primary);
      border: none;
      padding: 1.25rem 2rem;
      border-radius: var(--border-radius);
      font-size: 1rem;
      font-weight: 600;
      color: var(--pure-white);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      cursor: pointer;
      transition: var(--animation-normal);
      position: relative;
      overflow: hidden;
      box-shadow: var(--shadow-colored);
    }

    .btn-primary::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: var(--animation-slow);
    }

    .btn-primary:hover::before {
      left: 100%;
    }

    .btn-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 16px 48px rgba(0, 212, 170, 0.35);
    }

    .btn-primary:active {
      transform: translateY(-1px);
    }

    .btn-secondary {
      background: transparent;
      border: 2px solid var(--primary-green);
      padding: 1.25rem 2rem;
      border-radius: var(--border-radius);
      font-size: 1rem;
      font-weight: 600;
      color: var(--primary-green);
      text-decoration: none;
      text-align: center;
      cursor: pointer;
      transition: var(--animation-normal);
      position: relative;
      overflow: hidden;
    }

    .btn-secondary::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 0;
      height: 100%;
      background: var(--primary-green);
      transition: var(--animation-normal);
      z-index: -1;
    }

    .btn-secondary:hover::before {
      width: 100%;
    }

    .btn-secondary:hover {
      color: var(--pure-white);
      transform: translateY(-2px);
      box-shadow: var(--shadow-soft);
    }

    /* Alertas com design moderno */
    .alert {
      padding: 1.25rem 1.5rem;
      border-radius: var(--border-radius);
      margin-bottom: 2rem;
      border: none;
      position: relative;
      animation: slideInDown 0.5s var(--animation-bounce);
    }

    @keyframes slideInDown {
      0% {
        opacity: 0;
        transform: translateY(-30px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .alert-success {
      background: linear-gradient(135deg, rgba(38, 222, 129, 0.1) 0%, rgba(168, 230, 207, 0.1) 100%);
      color: var(--dark-green);
      border-left: 4px solid var(--accent-green);
    }

    .alert-danger {
      background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(252, 165, 165, 0.1) 100%);
      color: #B91C1C;
      border-left: 4px solid #EF4444;
    }

    /* Responsividade */
    @media (max-width: 576px) {
      body {
        padding: 1rem;
      }
      
      .password-container {
        padding: 2.5rem 2rem;
      }
      
      .main-title {
        font-size: 1.8rem;
      }
      
      .security-icon {
        width: 60px;
        height: 60px;
      }
      
      .security-icon i {
        font-size: 1.5rem;
      }
    }

    /* Estados de carregamento */
    .btn-primary.loading {
      pointer-events: none;
      opacity: 0.8;
    }

    .btn-primary.loading::after {
      content: '';
      position: absolute;
      width: 20px;
      height: 20px;
      margin: auto;
      border: 2px solid transparent;
      border-top-color: var(--pure-white);
      border-radius: 50%;
      animation: spin 1s linear infinite;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
    }

    @keyframes spin {
      0% { transform: translate(-50%, -50%) rotate(0deg); }
      100% { transform: translate(-50%, -50%) rotate(360deg); }
    }

    /* Efeitos especiais para interação */
    .password-container:hover {
      transform: translateY(-5px);
      box-shadow: 0 32px 80px rgba(0, 212, 170, 0.25);
    }

    /* Animação para validação */
    .input-field.valid {
      border-color: var(--accent-green);
      box-shadow: 0 0 0 4px rgba(38, 222, 129, 0.1);
    }

    .input-field.invalid {
      border-color: #EF4444;
      box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
    }
  </style>
</head>
<body>
<?php if ($success): ?>
  <!-- Apenas mensagem de sucesso e botão -->
  <div class="password-container" style="display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:70vh;">
    <div class="header-section" style="text-align:center;">
      <h1 class="main-title" style="margin-bottom:0.5em;">Senha Alterada com Sucesso <span style="font-size:1.2em;">👍</span></h1>
    </div>
    <a href="profile.php" class="btn-secondary">
          <i class="fas fa-arrow-left me-2"></i>
          Voltar ao Perfil
        </a>
  </div>
<?php else: ?>
  <!-- Partículas flutuantes -->
  <div class="floating-particles">
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
  </div>

  <div class="password-container">
    <!-- Cabeçalho -->
    <div class="header-section">
      <div class="security-icon">
        <i class="fas fa-shield-alt"></i>
      </div>
      <h1 class="main-title">Alterar Senha</h1>
      <p class="subtitle">Mantenha sua conta sempre segura</p>
    </div>

    <!-- Alertas -->
    <?php if ($msg): ?>
      <div class="alert alert-<?=(strpos($msg, 'sucesso')!==false?'success':'danger')?>">
        <i class="fas fa-<?=(strpos($msg, 'sucesso')!==false?'check-circle':'exclamation-triangle')?> me-2"></i>
        <?=$msg?>
      </div>
    <?php endif; ?>

    <!-- Formulário -->
    <form method="post" autocomplete="off" class="form-section" id="passwordForm">
      <div class="input-group" style="position:relative;">
        <label class="input-label" for="nova_senha">
          <i class="fas fa-key"></i>
          Nova Senha
        </label>
        <input 
          type="password" 
          class="input-field" 
          id="nova_senha" 
          name="nova_senha" 
          required 
          minlength="6" 
          autocomplete="new-password"
          placeholder="Digite sua nova senha"
        >
        <!-- Botão Olhinho Estilizado -->
        <button type="button" class="show-hide-btn shine-eye" tabindex="-1" aria-label="Mostrar senha" onclick="togglePassword('nova_senha', this)">
          <svg width="28" height="28" viewBox="0 0 20 20" fill="none">
            <defs>
              <linearGradient id="eyeGradient" x1="0" y1="0" x2="20" y2="20" gradientUnits="userSpaceOnUse">
                <stop stop-color="#00D4AA"/>
                <stop offset="1" stop-color="#26DE81"/>
              </linearGradient>
            </defs>
            <path d="M1.5 10C2.5 6.5 6 3.5 10 3.5C14 3.5 17.5 6.5 18.5 10C17.5 13.5 14 16.5 10 16.5C6 16.5 2.5 13.5 1.5 10Z" stroke="url(#eyeGradient)" stroke-width="1.5" fill="none"/>
            <circle cx="10" cy="10" r="3" stroke="url(#eyeGradient)" stroke-width="1.5" fill="none" />
            <circle cx="10" cy="10" r="1.2" fill="url(#eyeGradient)" class="eye-pupil"/>
          </svg>
        </button>
        <div class="password-strength">
          <div class="strength-bar" id="strengthBar"></div>
        </div>
      </div>

      <div class="input-group" style="position:relative;">
        <label class="input-label" for="confirma_senha">
          <i class="fas fa-lock"></i>
          Confirmar Nova Senha
        </label>
        <input 
          type="password" 
          class="input-field" 
          id="confirma_senha" 
          name="confirma_senha" 
          required 
          minlength="6" 
          autocomplete="new-password"
          placeholder="Confirme sua nova senha"
        >
        <!-- Botão Olhinho Estilizado -->
        <button type="button" class="show-hide-btn shine-eye" tabindex="-1" aria-label="Mostrar senha" onclick="togglePassword('confirma_senha', this)">
          <svg width="28" height="28" viewBox="0 0 20 20" fill="none">
            <defs>
              <linearGradient id="eyeGradient2" x1="0" y1="0" x2="20" y2="20" gradientUnits="userSpaceOnUse">
                <stop stop-color="#00D4AA"/>
                <stop offset="1" stop-color="#26DE81"/>
              </linearGradient>
            </defs>
            <path d="M1.5 10C2.5 6.5 6 3.5 10 3.5C14 3.5 17.5 6.5 18.5 10C17.5 13.5 14 16.5 10 16.5C6 16.5 2.5 13.5 1.5 10Z" stroke="url(#eyeGradient2)" stroke-width="1.5" fill="none"/>
            <circle cx="10" cy="10" r="3" stroke="url(#eyeGradient2)" stroke-width="1.5" fill="none" />
            <circle cx="10" cy="10" r="1.2" fill="url(#eyeGradient2)" class="eye-pupil"/>
          </svg>
        </button>
      </div>

      <div class="btn-group">
        <button type="submit" class="btn-primary" id="submitBtn">
          <i class="fas fa-save me-2"></i>
          Salvar Nova Senha
        </button>
        <a href="profile.php" class="btn-secondary">
          <i class="fas fa-arrow-left me-2"></i>
          Voltar ao Perfil
        </a>
      </div>
    </form>
  </div>
<?php endif; ?> 

  <style>
    .show-hide-btn.shine-eye {
      position: absolute;
      right: 1.1rem;
      top: 58%;
      transform: translateY(-50%);
      background: none;
      border: none;
      outline: none;
      cursor: pointer;
      padding: 0 0.15em;
      z-index: 2;
      transition: filter 0.2s, transform 0.18s;
      filter: drop-shadow(0 1px 5px #00d4aa22);
      border-radius: 50%;
    }
    .show-hide-btn.shine-eye:active {
      background: rgba(0,212,170,0.08);
      transform: scale(0.93) translateY(-50%);
    }
    .show-hide-btn.shine-eye:hover svg {
      filter: brightness(1.15) drop-shadow(0 2px 8px #00d4aa33);
      animation: eye-blink 0.45s;
    }
    @keyframes eye-blink {
      0% { transform: scaleY(1);}
      45% { transform: scaleY(0.35);}
      60% { transform: scaleY(0.35);}
      100% { transform: scaleY(1);}
    }
    .show-hide-btn.shine-eye svg .eye-pupil {
      transition: fill 0.18s;
    }
    .show-hide-btn.shine-eye.active svg .eye-pupil {
      fill: #EF4444 !important;
    }
  </style>

  <script>
    // Função visualizar senha estilizada com SVG animado
    function togglePassword(id, btn) {
      const input = document.getElementById(id);
      const svg = btn.querySelector('svg');
      const pupil = svg.querySelector('.eye-pupil');
      if (input.type === "password") {
        input.type = "text";
        btn.setAttribute('aria-label', 'Ocultar senha');
        btn.classList.add('active');
        if(pupil) pupil.setAttribute('fill', '#EF4444'); // cor de destaque para "visível"
      } else {
        input.type = "password";
        btn.setAttribute('aria-label', 'Mostrar senha');
        btn.classList.remove('active');
        if(pupil) pupil.setAttribute('fill', 'url(#eyeGradient)');
        if(svg.id === "eyeGradient2") pupil.setAttribute('fill', 'url(#eyeGradient2)');
      }
    }

    // Validação em tempo real e indicador de força da senha
    document.addEventListener('DOMContentLoaded', function() {
      const newPassword = document.getElementById('nova_senha');
      const confirmPassword = document.getElementById('confirma_senha');
      const strengthBar = document.getElementById('strengthBar');
      const submitBtn = document.getElementById('submitBtn');
      const form = document.getElementById('passwordForm');

      // Função para calcular força da senha
      function calculatePasswordStrength(password) {
        let strength = 0;
        if (password.length >= 6) strength += 20;
        if (password.length >= 8) strength += 20;
        if (/[a-z]/.test(password)) strength += 20;
        if (/[A-Z]/.test(password)) strength += 20;
        if (/[0-9]/.test(password)) strength += 20;
        return strength;
      }

      // Atualizar indicador de força
      newPassword.addEventListener('input', function() {
        const strength = calculatePasswordStrength(this.value);
        strengthBar.style.width = strength + '%';
        
        if (strength < 40) {
          strengthBar.style.background = 'linear-gradient(90deg, #EF4444, #F87171)';
        } else if (strength < 80) {
          strengthBar.style.background = 'linear-gradient(90deg, #F59E0B, #FCD34D)';
        } else {
          strengthBar.style.background = 'var(--gradient-primary)';
        }
      });

      // Validação de confirmação de senha
      function validatePasswords() {
        if (confirmPassword.value && newPassword.value !== confirmPassword.value) {
          confirmPassword.classList.add('invalid');
          confirmPassword.classList.remove('valid');
        } else if (confirmPassword.value) {
          confirmPassword.classList.add('valid');
          confirmPassword.classList.remove('invalid');
        }
      }

      newPassword.addEventListener('input', validatePasswords);
      confirmPassword.addEventListener('input', validatePasswords);

      // Efeito de carregamento no submit
      form.addEventListener('submit', function() {
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
      });

      // Animação de foco nos inputs
      const inputs = document.querySelectorAll('.input-field');
      inputs.forEach(input => {
        input.addEventListener('focus', function() {
          this.parentElement.style.transform = 'translateY(-2px)';
        });
        
        input.addEventListener('blur', function() {
          this.parentElement.style.transform = 'translateY(0)';
        });
      });
    });
  </script>
  
</body>

</html>