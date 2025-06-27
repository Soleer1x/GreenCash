<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if(!isset($_SESSION["usuario"])) {
  header("Location: login.php");
  exit;
}
$nomeUsuario = $_SESSION["usuario"]["nome"] ?? '';
$usuarioId = $_SESSION["usuario"]["id"] ?? 0;

require "db.php";

// Mensagens
$mensagemSucesso = $_SESSION['mensagemSucesso'] ?? '';
$mensagemErro = $_SESSION['mensagemErro'] ?? '';
unset($_SESSION['mensagemSucesso'], $_SESSION['mensagemErro']);

// Processa envio do formulário (PRG Pattern)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titulo']) && isset($_POST['descricao'])) {
    $titulo = trim($_POST['titulo']);
    $descricao = trim($_POST['descricao']);
    if ($titulo && $descricao) {
        $stmt = $conn->prepare("INSERT INTO suporte (usuario_id, titulo, descricao, data_hora) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $usuarioId, $titulo, $descricao);
        if ($stmt->execute()) {
            $_SESSION['mensagemSucesso'] = "Solicitação enviada com sucesso!";
        } else {
            $_SESSION['mensagemErro'] = "Erro ao enviar solicitação. Tente novamente.";
        }
        $stmt->close();
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        $mensagemErro = "Preencha todos os campos.";
    }
}

// Buscar todos os chamados do usuário
$stmt = $conn->prepare("SELECT id, titulo, descricao, data_hora FROM suporte WHERE usuario_id = ? ORDER BY data_hora DESC");
$stmt->bind_param("i", $usuarioId);
$stmt->execute();
$res = $stmt->get_result();
$suportes = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$respostaSuporte = [];
foreach ($suportes as $s) {
    $resp = $conn->query("SELECT mensagem, data, observacao FROM suporte_resposta WHERE suporte_id = {$s['id']} ORDER BY data DESC LIMIT 1")->fetch_assoc();
    $respostaSuporte[] = [
        'id' => $s['id'],
        'mensagem' => $resp['mensagem'] ?? 'Aguardando resposta do suporte.',
        'data' => $resp['data'] ?? '',
        'observacao' => $resp['observacao'] ?? '',
    ];
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>GreenCash - Suporte</title>
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
  <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
  <style>
    html, body { height: 100%; margin: 0; padding: 0; }
    body { min-height: 100vh; display: flex; flex-direction: column; }
    .main-content { flex: 1 0 auto; }
    .page-header-suporte {
      min-height: 300px;
      border-radius: 24px;
      background-image: url('https://images.unsplash.com/photo-1531512073830-ba890ca4eba2?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80');
      background-size: cover;
      background-position: center;
      margin-top: 24px;
      margin-bottom: 0;
      position: relative;
      z-index: 1;
      overflow: visible;
    }
    .page-header-suporte .mask {
      position: absolute;
      left: 0; top: 0; right: 0; bottom: 0;
      width: 100%; height: 100%;
      background: linear-gradient(180deg, rgba(33, 150, 83, 0.55) 0%, rgba(0,0,0,0.45) 100%);
      opacity: .6;
      z-index: 2;
      border-radius: 24px;
    }
    .suporte-row {
      display: flex;
      flex-direction: row;
      gap: 32px;
      align-items: flex-start;
      margin-left: 32px;
      margin-right: auto;
      max-width: 1200px;
      margin-top: -110px;
      margin-bottom: 40px;
      position: relative;
      z-index: 10;
    }
    .support-modal-wrapper {
      max-width: 600px;
      width: 100%;
      background: transparent;
      position: relative;
      z-index: 11;
    }
    #main-support {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.10);
      padding: 24px;
      min-height: 400px;
      width: 100%;
      position: relative;
      z-index: 11;
    }
    .suporte-resposta-wrapper {
      flex: 1 1 0%;
      min-width: 300px;
      max-width: 500px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.08);
      padding: 24px 18px 20px 24px;
      margin-top: 0;
      margin-bottom: 0;
      position: relative;
      z-index: 11;
      display: flex;
      flex-direction: column;
      gap: 18px;
    }
    .resposta-suporte-card {
      border-left: 4px solid #1976D2;
      background: #F8FAFC;
      border-radius: 8px;
      padding: 16px 18px 16px 16px;
      margin-bottom: 10px;
      box-shadow: 0 2px 8px 0 #0001;
      position: relative;
    }
    .resposta-suporte-card .titulo {
      font-weight: 700;
      font-size: 1.05rem;
      margin-bottom: 4px;
      color: #1976D2;
    }
    .resposta-suporte-card .label {
      font-weight: 600;
      color: #1976D2;
      font-size: 0.97rem;
      margin-bottom: 2px;
      display: block;
    }
    .resposta-suporte-card .msg,
    .resposta-suporte-card .data,
    .resposta-suporte-card .observacao {
      font-size: 1rem;
      margin-bottom: 8px;
      color: #374151;
    }
    .resposta-suporte-card .data {
      font-size: .96rem;
      color: #7b809a;
      font-style: italic;
      margin-bottom: 8px;
    }
    .resposta-suporte-card .observacao {
      color: #388e3c;
      margin-bottom: 0;
      font-size: .97rem;
    }
    .support-form label {
      font-weight: 600;
      margin-bottom: 6px;
      color: #1976D2;
    }
    .support-form input, .support-form textarea {
      width: 100%;
      border-radius: 8px;
      border: 1px solid #cfd8dc;
      padding: 10px 12px;
      margin-bottom: 18px;
      font-size: 1em;
      background: #f8fafc;
      transition: border 0.2s;
    }
    .support-form input:focus, .support-form textarea:focus {
      border-color: #1976D2;
      background: #fff;
      outline: none;
    }
    .support-form textarea {
      min-height: 100px;
      resize: vertical;
    }
    @media (max-width: 1200px) {
      .suporte-row {
        flex-direction: column;
        gap: 28px;
        margin-left: 0;
        max-width: 100%;
        margin-top: -70px;
        padding: 0 8px;
      }
      .support-modal-wrapper, .suporte-resposta-wrapper {
        max-width: 100%;
        margin-left: 0;
        margin-right: 0;
      }
    }
  </style>
</head>
<body class="g-sidenav-show bg-gray-100">
<body class="g-sidenav-show bg-gray-100">
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2  bg-white my-2" id="sidenav-main">
<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$plano = $_SESSION["usuario"]["plano"] ?? 'basico';
$current = basename($_SERVER['SCRIPT_NAME']);
function navActive($file) {
  return (basename($_SERVER['SCRIPT_NAME']) == $file) ? 'active bg-gradient-dark text-white' : 'text-dark';
}
?>
  <a class="navbar-brand px-4 py-3 m-0" href="#" onclick="return false;">
    <img src="../assets/img/logo-ct-dark.png" alt="GreenCash Logo" class="ms-1" style="height: 32px;">
  </a>
  <hr class="horizontal dark mt-0 mb-2">
  <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link <?=navActive('dashboard.php')?>" href="dashboard.php">
          <i class="material-symbols-rounded opacity-5">dashboard</i>
          <span class="nav-link-text ms-1">Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?=navActive('billing.php')?>" href="billing.php">
          <i class="material-symbols-rounded opacity-5">receipt_long</i>
          <span class="nav-link-text ms-1">Conta Bancária</span>
        </a>
      </li>
      <?php if ($plano === 'intermediario' || $plano === 'avancado'): ?>
      <li class="nav-item">
        <a class="nav-link <?=navActive('calendario.php')?>" href="calendario.php">
          <i class="material-symbols-rounded opacity-5">calendar_month</i>
          <span class="nav-link-text ms-1">Calendário</span>
        </a>
      </li>
      <?php endif; ?>
      <?php if ($plano === 'avancado'): ?>
      <li class="nav-item">
        <a class="nav-link <?=navActive('suporte.php')?>" href="suporte.php">
          <i class="material-symbols-rounded opacity-5">support_agent</i>
          <span class="nav-link-text ms-1">Suporte</span>
        </a>
      </li>
      <?php endif; ?>
      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs text-dark font-weight-bolder opacity-5">Páginas da conta</h6>
      </li>
      <li class="nav-item">
        <a class="nav-link <?=navActive('profile.php')?>" href="profile.php">
          <i class="material-symbols-rounded opacity-5">person</i>
          <span class="nav-link-text ms-1">Perfil</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-dark" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#logoutConfirmModal">
          <i class="material-symbols-rounded opacity-5">assignment</i>
          <span class="nav-link-text ms-1">Sair</span>
        </a>
      </li>
    </ul>
  </div>
</aside>
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-3 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Página</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Suporte</li>
          </ol>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <div class="input-group input-group-outline">
              <label class="form-label">Digite aqui...</label>
              <input type="text" class="form-control">
            </div>
          </div>
          <ul class="navbar-nav d-flex align-items-center  justify-content-end">
            <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                <div class="sidenav-toggler-inner">
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                </div>
              </a>
            </li>
            <li class="nav-item d-flex align-items-center">
            <a href="profile.php" style="display:inline-block;">
              <img id="navbar-profile-img" src="../assets/img/usuario.png" alt="Perfil" style="width:36px;height:36px;object-fit:cover;border-radius:50%;border:2px solid #43a047;cursor:pointer;">
            </a>
          </li>
          </ul>
        </div>
      </div>
    </nav>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
  function atualizaNavbarFoto(foto) {
    const navbarImg = document.getElementById('navbar-profile-img');
    if (navbarImg) {
      navbarImg.src = foto && foto !== "" ? foto : '../assets/img/usuario.png';
    }
  }
  function carregaPerfilAtualizaNavbar() {
    fetch('carregar_perfil.php')
      .then(res => res.json())
      .then(data => {
        const fotoPerfil = data.foto && data.foto !== "" ? data.foto : '../assets/img/usuario.png';
        atualizaNavbarFoto(fotoPerfil);
      });
  }
  document.querySelectorAll('.nav-link').forEach(function(link) {
    if (link.textContent.includes('Configuração')) {
      link.addEventListener('click', carregaPerfilAtualizaNavbar);
    }
  });
  if (document.getElementById('settings-form')) {
    document.getElementById('settings-form').addEventListener('submit', function(e) {
      setTimeout(carregaPerfilAtualizaNavbar, 900);
    });
  }
  carregaPerfilAtualizaNavbar();
});
  </script>

<div class="container-fluid px-2 px-md-4">
  <div class="page-header-suporte">
    <span class="mask bg-gradient-dark opacity-6"></span>
  </div>
  <div class="suporte-row">
    <div class="support-modal-wrapper">
      <div id="main-support">
        <div class="support-form">
          <div class="mb-3">
            <i class="material-symbols-rounded" style="font-size:56px; color:#1976D2;">support_agent</i>
            <h2 class="mt-2 mb-3">Solicitar Suporte</h2>
            <p class="mb-3">Descreva o problema ou dúvida. Nossa equipe responderá em breve!</p>
          </div>
          <?php if ($mensagemSucesso): ?>
            <div class="alert alert-success"><?=htmlspecialchars($mensagemSucesso)?></div>
          <?php elseif ($mensagemErro): ?>
            <div class="alert alert-danger"><?=htmlspecialchars($mensagemErro)?></div>
          <?php endif; ?>
          <form method="post" action="">
            <label for="titulo">Título do Suporte</label>
            <input type="text" id="titulo" name="titulo" placeholder="Ex: Não consigo acessar minha conta" required>
            <label for="descricao">Descrição do Suporte</label>
            <textarea id="descricao" name="descricao" placeholder="Descreva sua dúvida ou problema com detalhes..." required></textarea>
            <button type="submit" class="btn btn-success w-100 mt-2">Enviar Solicitação</button>
          </form>
        </div>
      </div>
    </div>
    <div class="suporte-resposta-wrapper">
      <h5 class="mb-3" style="color:#1976D2; font-weight:700; letter-spacing:.2px;">
        <span class="material-symbols-rounded align-middle" style="font-size:28px; color:#1976D2;">mark_email_read</span>
        Resposta do Suporte
      </h5>
      <?php foreach ($respostaSuporte as $resposta): ?>
        <div class="resposta-suporte-card">
          <span class="label">Nº do suporte:</span>
          <div class="titulo"><?=htmlspecialchars($resposta['id'])?></div>
          <span class="label">Descrição:</span>
          <div class="msg"><?=htmlspecialchars($resposta['mensagem'])?></div>
          <span class="label">Data:</span>
          <div class="data"><?=htmlspecialchars($resposta['data'])?></div>
          <span class="label">Observação:</span>
          <div class="observacao"><?=htmlspecialchars($resposta['observacao'])?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<footer class="footer py-4">
      <div class="container-fluid">
        <div class="row align-items-center justify-content-lg-between">
          <div class="col-lg-6 mb-lg-0 mb-4">
            <div class="copyright text-center text-sm text-muted text-lg-start">
              © <script>document.write(new Date().getFullYear())</script>,
              GreenCash Financeiros | Licença criptografadas
            </div>
          </div>
        </div>
      </div>
    </footer>
  </main>
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="../assets/js/material-dashboard.min.js?v=3.2.0"></script>

    <!-- Modal de Confirmação de Logout -->
  <div class="modal fade" id="logoutConfirmModal" tabindex="-1" aria-labelledby="logoutConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header">
          <h5 class="modal-title" id="logoutConfirmModalLabel">Sair da Conta</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          Tem certeza de que deseja sair da sua conta?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <a href="logout.php" class="btn btn-danger">Sair</a>
        </div>
      </div>
    </div>
  </div>
</main>
</body>
</html>