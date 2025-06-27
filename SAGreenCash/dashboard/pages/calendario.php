<?php
session_start();
if (!isset($_SESSION["usuario"])) {
  header("Location: ../login.php");
  exit;
}
// Se for admin, redireciona para o painel de admin
if (isset($_SESSION["usuario"]["tipo"]) && $_SESSION["usuario"]["tipo"] == 1) {
  header("Location: /SAGreenCash/dashboard/telaAdmin/views/Painel.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>GreenCash - Calendário</title>
  <!-- Google Fonts: Inter -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <!-- Material Symbols Rounded -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
  <!-- Nucleo Icons -->
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- FullCalendar CSS -->
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />
  <!-- Material Dashboard CSS -->
  <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
  <style>
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
    }
    body {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    .main-content {
      flex: 1 0 auto;
    }
    #main-calendar {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
      padding: 24px;
      max-width: 1200px;
      min-height: 700px;
      width: 100%;
      margin-left: 32px;
      margin-right: auto;
    }
    @media (max-width: 991px) {
      #main-calendar {
        padding: 8px;
        min-height: 350px;
        margin-left: 0;
      }
    }
    .fc-toolbar-title {
      font-size: 2rem !important;
      font-weight: 700;
      color: #388e3c;
    }
    .fc-button-primary {
      background: #43A047;
      border: none;
      box-shadow: none;
    }
    .fc-button-primary:hover,
    .fc-button-primary:focus {
      background: #388e3c;
    }
  </style>
</head>

<body class="g-sidenav-show  bg-gray-100">
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
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-3 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Página</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Calendário</li>
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
  
    <!-- End Navbar -->

    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div id="main-calendar"></div>
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

  <!-- Ajustes de Interface -->
  <div class="fixed-plugin">
    <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
      <i class="material-symbols-rounded py-2">settings</i>
    </a>
    <div class="card shadow-lg">
      <div class="card-header pb-0 pt-3">
        <div class="float-start">
          <h5 class="mt-3 mb-0">Ajustes de Interface</h5>
          <p>Veja nossas opções de painel</p>
        </div>
        <div class="float-end mt-4">
          <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
            <i class="material-symbols-rounded">clear</i>
          </button>
        </div>
      </div>
      <hr class="horizontal dark my-1">
      <div class="card-body pt-sm-3 pt-0">
        <div>
          <h6 class="mb-0">Temas da Barra Lateral</h6>
        </div>
        <a href="javascript:void(0)" class="switch-trigger background-color">
          <div class="badge-colors my-2 text-start">
            <span class="badge filter bg-gradient-primary" data-color="primary" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-dark active" data-color="dark" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-info" data-color="info" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-success" data-color="success" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-warning" data-color="warning" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-danger" data-color="danger" onclick="sidebarColor(this)"></span>
          </div>
        </a>
        <div class="mt-3">
          <h6 class="mb-0">Estilo De Navegação Lateral</h6>
          <p class="text-sm">Escolha entre diferentes tipos de navegação lateral.</p>
        </div>
        <div class="d-flex">
          <button class="btn bg-gradient-dark px-3 mb-2" data-class="bg-gradient-dark" onclick="sidebarType(this)">Escuro</button>
          <button class="btn bg-gradient-dark px-3 mb-2 ms-2" data-class="bg-transparent" onclick="sidebarType(this)">Transparente</button>
          <button class="btn bg-gradient-dark px-3 mb-2  active ms-2" data-class="bg-white" onclick="sidebarType(this)">Branco</button>
        </div>
        <p class="text-sm d-xl-none d-block mt-2">Você só pode mudar o tipo da barra lateral no desktop.</p>
        <div class="mt-3 d-flex">
          <h6 class="mb-0">Barra de navegação</h6>
          <div class="form-check form-switch ps-0 ms-auto my-auto">
            <input class="form-check-input mt-1 ms-auto" type="checkbox" id="navbarFixed" onclick="navbarFixed(this)">
          </div>
        </div>
        <hr class="horizontal dark my-3">
        <div class="mt-2 d-flex">
          <h6 class="mb-0">Claro / Escuro</h6>
          <div class="form-check form-switch ps-0 ms-auto my-auto">
            <input class="form-check-input mt-1 ms-auto" type="checkbox" id="dark-version" onclick="darkMode(this)">
          </div>
        </div>
      </div>
    </div>
  </div>

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

  <!-- JS Scripts -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
  <script src="../assets/js/material-dashboard.min.js?v=3.2.0"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var calendarEl = document.getElementById('main-calendar');
      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'pt-br',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        height: 'auto',
        selectable: true,
        editable: false,
        events: [], // Para popular do backend, se desejar
      });
      calendar.render();
    });
  </script>
</body>
</html>