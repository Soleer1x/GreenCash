<!--
=========================================================
* Material Dashboard 3 - v3.2.0
=========================================================

* Product Page: https://www.creative-tim.com/product/material-dashboard
* Copyright 2024 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)

* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
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
  <title>
    GreenCash
  </title>
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <!-- Nucleo Icons -->
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  <!-- CSS Files -->
  <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
</head>
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
  <div class="main-content position-relative max-height-vh-100 h-100">
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-3 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Página</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Perfil</li>
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
              <img id="navbar-profile-img" src="../assets/img/usuario.png" alt="Perfil" style="width:36px;height:36px;object-fit:cover;border-radius:50%;border:2px solid #43a047;">
             </a>
           </li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="container-fluid px-2 px-md-4">
      <div class="page-header min-height-300 border-radius-xl mt-4" style="background-image: url('https://images.unsplash.com/photo-1531512073830-ba890ca4eba2?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80');">
        <span class="mask  bg-gradient-dark  opacity-6"></span>
      </div>
      <div class="card card-body mx-2 mx-md-2 mt-n6">
        <div class="row gx-4 mb-2">
          <div class="col-auto">
            <div class="avatar avatar-xl position-relative">
              <img src="../assets/img/usuario.png" alt="profile_image" class="w-100 border-radius-lg shadow-sm" id="profile-img-preview">
            </div>
          </div>
          <div class="col-auto my-auto">
            <div class="h-100">
              <h5 class="mb-1" id="profile-name">
                Nome
              </h5>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 my-sm-auto ms-sm-auto me-sm-0 mx-auto mt-3">
            <div class="nav-wrapper position-relative end-0">
              <ul class="nav nav-pills nav-fill p-1" role="tablist">
                <li class="nav-item">
                  <a class="nav-link mb-0 px-0 py-1 active " data-bs-toggle="tab" href="javascript:;" role="tab" aria-selected="true">
                    <i class="material-symbols-rounded text-lg position-relative">home</i>
                    <span class="ms-1">Aplicativo</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link mb-0 px-0 py-1 " data-bs-toggle="tab" href="javascript:;" role="tab" aria-selected="false">
                    <i class="material-symbols-rounded text-lg position-relative">settings</i>
                    <span class="ms-1">Configuração</span>
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div class="col-12 col-xl-4">
          <div class="card card-plain h-100">
            <div class="card-header pb-0 p-3">
              <div class="row">
                <div class="col-md-8 d-flex align-items-center">
                  <h6 class="mb-0">Informações do Perfil</h6>
                </div>
              </div>
            </div>
            <div class="card-body p-3">
              <p class="text-sm" id="profile-desc">
                texto sobre a pessoa
              </p>
              <hr class="horizontal gray-light my-4">
              <ul class="list-group" id="profile-list">
                <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Nome completo:</strong> &nbsp; <span id="profile-fullname">Nome da pessoa por completo</span></li>
                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Telefone:</strong> &nbsp; <span id="profile-phone">numero da pessoa</span></li>
                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Email:</strong> &nbsp; <span id="profile-email">email da pessoa</span></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <!-- Modal Editar Perfil -->
      <div id="custom-toast" class="custom-toast"></div>
       <script>
        
       </script>
      <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
              <h5 class="modal-title" id="editProfileModalLabel">Editar Perfil</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form id="edit-profile-form">
              <div class="modal-body">
                <div class="mb-3">
                  <label for="edit-profile-img" class="form-label">Foto de Perfil</label>
                  <input type="file" class="form-control custom-input" id="edit-profile-img" accept="image/*">
                </div>
                <div class="mb-3">
                  <label for="edit-profile-name" class="form-label">Nome</label>
                  <input type="text" class="form-control custom-input" id="edit-profile-name" value="">
                </div>
                <div class="mb-3">
                  <label for="edit-profile-desc" class="form-label">Descrição</label>
                  <textarea class="form-control custom-input" id="edit-profile-desc" rows="2"></textarea>
                </div>
                <div class="mb-3">
                  <label for="edit-profile-fullname" class="form-label">Nome completo</label>
                  <input type="text" class="form-control custom-input" id="edit-profile-fullname" value="">
                </div>
                <div class="mb-3">
                  <label for="edit-profile-phone" class="form-label">Telefone</label>
                  <input type="text" class="form-control custom-input" id="edit-profile-phone" value="">
                </div>
                <div class="mb-3">
                  <label for="edit-profile-email" class="form-label">Email</label>
                  <input type="email" class="form-control custom-input" id="edit-profile-email" value="">
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Salvar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <!-- Fim modal -->
      <style>
        /* CSS para estilizar as caixas de input do modal */
        .custom-input {
          background: #f8fafc !important;
          border: 2px solid #cfd8dc !important;
          border-radius: 7px !important;
          box-shadow: 0 2px 8px 0 rgba(60,72,88,0.08);
          padding: 12px 14px !important;
          font-size: 16px !important;
          margin-bottom: 0 !important;
          transition: border-color .2s, box-shadow .2s;
        }
        .custom-input:focus {
          border-color: #43a047 !important;
          box-shadow: 0 0 0 2px #a5d6a7 !important;
          background: #fff !important;
        }
        .modal-content {
          border-radius: 15px !important;
        }
        .modal-header {
          border-bottom: none;
        }
        .form-label {
          font-weight: 600;
          color: #374151;
        }

                .custom-toast {
          display: none;
          position: fixed;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
          background: #43a047;
          color: #fff;
          padding: 22px 46px;
          border-radius: 14px;
          font-size: 1.2rem;
          box-shadow: 0 8px 32px rgba(67,160,71,0.18);
          z-index: 9999;
          opacity: 0;
          transition: opacity .35s;
          text-align: center;
          max-width: 80vw;
        }
        .custom-toast.show { display: block; opacity: 1; }
        .custom-toast.error { background: #e53935; }
      </style>
    </div>
    <footer class="footer py-4  ">
      <div class="container-fluid">
        <div class="row align-items-center justify-content-lg-between">
          <div class="col-lg-6 mb-lg-0 mb-4">
            <div class="copyright text-center text-sm text-muted text-lg-start">
              © <script>
                document.write(new Date().getFullYear())
              </script>,
              GreenCash Financeiros | Licença criptografadas
            </div>
          </div>
        </div>
      </div>
    </footer>
  </div>
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
        <p class="text-sm d-xl-none d-block mt-2">You can change the sidenav type just on desktop view.</p>
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
  <!--   Core JS Files   -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script>
    // MODAL DE EDITAR PERFIL
    document.addEventListener('DOMContentLoaded', function() {
      var editProfileBtn = document.getElementById('edit-profile-btn');
      var editProfileModal = new bootstrap.Modal(document.getElementById('editProfileModal'));
      var editProfileForm = document.getElementById('edit-profile-form');

      // Dados iniciais de exemplo
      let profileData = {
        img: '../assets/img/usuario.png',
        name: 'Nome',
        desc: 'texto sobre a pessoa',
        fullname: 'Nome da pessoa por completo',
        phone: 'numero da pessoa',
        email: 'email da pessoa',
      };

     // Preencher perfil na tela
function renderProfile() {
  document.getElementById('profile-img-preview').src = profileData.img;
  document.getElementById('profile-name').innerText = profileData.name;
  document.getElementById('profile-desc').innerText = profileData.desc;
  document.getElementById('profile-fullname').innerText = profileData.fullname;
  document.getElementById('profile-phone').innerText = profileData.phone;
  document.getElementById('profile-email').innerText = profileData.email;

  // ATUALIZA A IMAGEM DO TOPO DIREITO (NAVBAR)
  const navbarImg = document.getElementById('navbar-profile-img');
  if (navbarImg) navbarImg.src = profileData.img;
}

      // Ao abrir o modal, limpar campos
      editProfileBtn.addEventListener('click', function() {
        document.getElementById('edit-profile-name').value = "";
        document.getElementById('edit-profile-desc').value = "";
        document.getElementById('edit-profile-fullname').value = "";
        document.getElementById('edit-profile-phone').value = "";
        document.getElementById('edit-profile-email').value = "";
        document.getElementById('edit-profile-img').value = "";
        editProfileModal.show();
      });

      // Preview da imagem no modal
      document.getElementById('edit-profile-img').addEventListener('change', function(e){
        if (e.target.files && e.target.files[0]) {
          let reader = new FileReader();
          reader.onload = function(ev) {
            document.getElementById('profile-img-preview').src = ev.target.result;
            profileData.img = ev.target.result;
          };
          reader.readAsDataURL(e.target.files[0]);
        }
      });

      // Submeter formulário
      editProfileForm.addEventListener('submit', function(e){
        e.preventDefault();

        // Só salva o campo se usuário preencheu (assim não apaga sem querer)
        const nameVal = document.getElementById('edit-profile-name').value.trim();
        if(descVal) profileData.desc = descVal;
        const fullnameVal = document.getElementById('edit-profile-fullname').value.trim();
        if(fullnameVal) profileData.fullname = fullnameVal;
        const phoneVal = document.getElementById('edit-profile-phone').value.trim();
        if(phoneVal) profileData.phone = phoneVal;
        const emailVal = document.getElementById('edit-profile-email').value.trim();
        if(emailVal) profileData.email = emailVal;

        renderProfile();
        editProfileModal.hide();
      });

      renderProfile();
    });
  </script>
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <script src="../assets/js/material-dashboard.min.js?v=3.2.0"></script>
  <!-- Adicione esse código dentro do <body> ou logo após o modal de perfil -->


<!-- Botão de configurações já existe no seu nav-pills:
<a class="nav-link mb-0 px-0 py-1 " data-bs-toggle="tab" href="javascript:;" role="tab" aria-selected="false">
  <i class="material-symbols-rounded text-lg position-relative">settings</i>
  <span class="ms-1">Configuração</span>
</a>
-->

<!-- Modal de Configurações de Perfil (visual moderno, só informações de perfil) -->
<div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg modern-bg">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" id="settingsModalLabel">
          <i class="fa fa-user-cog me-2 text-success"></i>Editar Informações do Perfil
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <form id="settings-form">
        <div class="modal-body pt-1 pb-0">
          <!-- Perfil -->
          <section class="mb-2">
            <div class="d-flex align-items-center mb-3 gap-3">
              <div class="position-relative">
                <img src="../assets/img/usuario.png" id="settings-profile-img-preview" alt="Avatar" width="82" height="82"
                     class="border border-3 border-success shadow avatar-config-img">
                <label for="settings-profile-img" class="avatar-config-edit rounded-circle shadow"
                       title="Trocar foto"><i class="fa fa-camera"></i></label>
                <input type="file" id="settings-profile-img" accept="image/*" class="d-none">
              </div>
              <div>
                <h6 class="mb-1 fw-bold" style="font-size:1.15rem;">Seu Perfil</h6>
                <span class="text-muted" style="font-size:.98rem;">Personalize suas informações pessoais</span>
              </div>
            </div>
            <div class="row g-3">
            <div class="col-12 col-md-6">
  <label class="form-label mb-1">Nome</label>
  <input type="text" id="settings-profile-name" class="form-control custom-input" autocomplete="off" maxlength="32" placeholder="Ex: João Silva" oninput="validateName(this)">
</div>
<div class="col-12 col-md-6">
  <label class="form-label mb-1">Telefone</label>
  <input type="text" id="settings-profile-phone" class="form-control custom-input" autocomplete="off" maxlength="20" placeholder="(99) 99999-9999" oninput="maskPhone(this)">
</div>
<script>
function validateName(input) {
  // Permite apenas letras (incluindo acentos) e espaços
  input.value = input.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, '');
}

function maskPhone(input) {
  // Remove tudo que não for número
  let numbers = input.value.replace(/\D/g, '');

  // Limita para no máximo 11 dígitos (celular BR)
  if (numbers.length > 11) numbers = numbers.slice(0, 11);

  // Aplica a máscara: (99) 99999-9999 ou (99) 9999-9999
  let masked = numbers;
  if (numbers.length > 0) masked = '(' + numbers.slice(0,2);
  if (numbers.length >= 3) masked += ') ' + numbers.slice(2, numbers.length > 10 ? 7 : 6);
  if (numbers.length > 6)  masked += '-' + numbers.slice(numbers.length > 10 ? 7 : 6, 11);

  input.value = masked;
}
</script>
              <div class="col-12 col-md-6">
                <label class="form-label mb-1">E-mail</label>
                <input type="email" id="settings-profile-email" class="form-control custom-input" autocomplete="off" maxlength="48" placeholder="seu@email.com">
              </div>
              <div class="col-12">
                <label class="form-label mb-1">Bio</label>
                <textarea class="form-control custom-input" id="settings-profile-bio" rows="2" maxlength="120" placeholder="Conte um pouco sobre você..."></textarea>
              </div>
              <!-- ...outros campos do formulário... -->
               <!-- ... dentro do <form id="settings-form">, após os campos de perfil ... -->
              <div class="d-flex justify-content-end mb-2 mt-3">
                <a href="alterar_senha.php" class="btn btn-outline-success fw-bold" style="min-width:180px;">
                  <i class="material-symbols-rounded align-middle me-1" style="font-size:1.2em;">lock_reset</i>
                  Alterar Senha
                </a>
              </div>
             
            </div>
          </section>
        </div>
        <div class="modal-footer border-0 pt-0 pb-4">
          <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success px-4 fw-bold">Salvar <i class="fa fa-save ms-1"></i></button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
.modern-bg {
  background: linear-gradient(135deg, #f8fafc 60%, #e0f2f1 100%);
}
.avatar-config-img {
  object-fit: cover;
  background: #fff;
  transition: box-shadow .25s;
  width: 82px;
  height: 82px;
}
.avatar-config-img:hover {
  box-shadow: 0 0 0 4px #43a04744;
}
.avatar-config-edit {
  position: absolute;
  bottom: 3px;
  right: -9px;
  background: #43a047;
  color: #fff;
  padding: 7px 10px;
  cursor: pointer;
  font-size: 1.1rem;
  border: 2px solid #fff;
  box-shadow: 0 2px 8px 0 rgba(67,160,71,0.12);
  transition: background .2s;
  z-index: 2;
}
.avatar-config-edit:hover {
  background: #388e3c;
}
.custom-input, .form-select.custom-input {
  background: #f8fafc !important;
  border: 2px solid #cfd8dc !important;
  border-radius: 7px !important;
  box-shadow: 0 2px 8px 0 rgba(60,72,88,0.08);
  padding: 12px 14px !important;
  font-size: 15px !important;
  margin-bottom: 0 !important;
  transition: border-color .2s, box-shadow .2s;
}
.custom-input:focus, .form-select.custom-input:focus {
  border-color: #43a047 !important;
  box-shadow: 0 0 0 2px #a5d6a7 !important;
  background: #fff !important;
}
.form-label {
  font-weight: 600;
  color: #374151;
}
#profile-img-preview,
#settings-profile-img-preview,
.avatar.avatar-xl img,
.avatar-config-img {
  border-radius: 50% !important;
  object-fit: cover !important;
  aspect-ratio: 1 / 1;
}
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
  if (document.getElementById('settings-form')) {
    document.getElementById('settings-form').addEventListener('submit', function(e) {
      e.preventDefault();

      // Dados normais do perfil
      const formData = new FormData();
      formData.append('nome', document.getElementById('settings-profile-name').value);
      formData.append('email', document.getElementById('settings-profile-email').value);
      formData.append('telefone', document.getElementById('settings-profile-phone').value);
      formData.append('bio', document.getElementById('settings-profile-bio').value);

      // Campos de senha
      const senhaAtual = document.getElementById('settings-current-password').value;
      const novaSenha = document.getElementById('settings-new-password').value;
      const confirmarSenha = document.getElementById('settings-confirm-password').value;

      // Se algum campo de senha foi preenchido, faz validação
      if (senhaAtual || novaSenha || confirmarSenha) {
        if (!senhaAtual || !novaSenha || !confirmarSenha) {
          showCustomToast('Preencha todos os campos de senha para alterar!', true);
          return;
        }
        if (novaSenha.length < 6) {
          showCustomToast('A nova senha deve ter pelo menos 6 caracteres!', true);
          return;
        }
        if (novaSenha !== confirmarSenha) {
          showCustomToast('Nova senha e confirmação não coincidem!', true);
          return;
        }
        formData.append('senha_atual', senhaAtual);
        formData.append('nova_senha', novaSenha);
      }

      // Foto, se houver
      const imgInput = document.getElementById('settings-profile-img');
      if (imgInput && imgInput.files[0]) {
        formData.append('foto', imgInput.files[0]);
      }

      fetch('atualizar_perfil.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
          if (data.sucesso) {
            // Atualiza campos visuais/foto
            const foto = data.foto && data.foto !== "" ? data.foto : '../assets/img/usuario.png';
            document.getElementById('profile-img-preview').src = foto;
            const previewModal = document.getElementById('settings-profile-img-preview');
            if (previewModal) previewModal.src = foto;
            // Limpa campos de senha
            document.getElementById('settings-current-password').value = '';
            document.getElementById('settings-new-password').value = '';
            document.getElementById('settings-confirm-password').value = '';
            carregaPerfil();
            bootstrap.Modal.getInstance(document.getElementById('settingsModal')).hide();
            showCustomToast('Perfil atualizado com sucesso!');
          } else {
            showCustomToast('Erro ao atualizar perfil: ' + (data.erro || ''), true);
          }
        });
    });
  }
});
</script>

<script>
function showCustomToast(message, isError = false, duration = 2200) {
  const toast = document.getElementById('custom-toast');
  toast.textContent = message;
  toast.classList.remove('error');
  if (isError) toast.classList.add('error');
  toast.classList.add('show');
  setTimeout(() => {
    toast.classList.remove('show');
  }, duration);
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Initial simulated data
  let profileData = {
    img: '../assets/img/usuario.png',
    name: 'Nome',
    email: 'email@exemplo.com',
    phone: '00 00000-0000',
    bio: 'texto sobre a pessoa'
  };

  // Atualiza o perfil na tela principal E na tela de edição
  function renderProfile() {
    // Elementos da tela principal
    const elements = {
      img: document.getElementById('profile-img-preview'),
      name: document.getElementById('profile-name'),
      desc: document.getElementById('profile-desc'),
      fullname: document.getElementById('profile-fullname'),
      phone: document.getElementById('profile-phone'),
      email: document.getElementById('profile-email'),
    };

    if (elements.img) elements.img.src = profileData.img;
    if (elements.name) elements.name.innerText = profileData.name;
    if (elements.desc) elements.desc.innerText = profileData.bio;
    if (elements.fullname) elements.fullname.innerText = profileData.name;
    if (elements.phone) elements.phone.innerText = profileData.phone;
    if (elements.email) elements.email.innerText = profileData.email;

    // Elementos da tela de edição
    if (document.getElementById('settings-profile-img-preview')) document.getElementById('settings-profile-img-preview').src = profileData.img;
    if (document.getElementById('settings-profile-name')) document.getElementById('settings-profile-name').value = profileData.name;
    if (document.getElementById('settings-profile-email')) document.getElementById('settings-profile-email').value = profileData.email;
    if (document.getElementById('settings-profile-phone')) document.getElementById('settings-profile-phone').value = profileData.phone;
    if (document.getElementById('settings-profile-bio')) document.getElementById('settings-profile-bio').value = profileData.bio;
  }

  // Preenche o modal com os dados atuais (redundante, mas mantido para chamada manual)
  function fillSettingsModal() {
    if (document.getElementById('settings-profile-img-preview')) document.getElementById('settings-profile-img-preview').src = profileData.img;
    if (document.getElementById('settings-profile-name')) document.getElementById('settings-profile-name').value = profileData.name;
    if (document.getElementById('settings-profile-email')) document.getElementById('settings-profile-email').value = profileData.email;
    if (document.getElementById('settings-profile-phone')) document.getElementById('settings-profile-phone').value = profileData.phone;
    if (document.getElementById('settings-profile-bio')) document.getElementById('settings-profile-bio').value = profileData.bio;
  }

  // Abrir modal e preencher dados
  document.querySelectorAll('.nav-link').forEach(function(link) {
    if (link.textContent.includes('Configuração')) {
      link.addEventListener('click', function() {
        renderProfile(); // já preenche tudo, inclusive modal
        var settingsModal = new bootstrap.Modal(document.getElementById('settingsModal'));
        settingsModal.show();
      });
    }
  });

  // Preview da imagem no modal de edição
  if (document.getElementById('settings-profile-img')) {
    document.getElementById('settings-profile-img').addEventListener('change', function(e) {
      if (e.target.files && e.target.files[0]) {
        let reader = new FileReader();
        reader.onload = function(ev) {
          document.getElementById('settings-profile-img-preview').src = ev.target.result;
        };
        reader.readAsDataURL(e.target.files[0]);
      }
    });
  }

  // Click no ícone de editar foto
  if (document.querySelector('.avatar-config-edit')) {
    document.querySelector('.avatar-config-edit').addEventListener('click', function() {
      document.getElementById('settings-profile-img').click();
    });
  }

  // Salvar configurações e atualizar perfil na página
  if (document.getElementById('settings-form')) {
    document.getElementById('settings-form').addEventListener('submit', function(e) {
      e.preventDefault();

      // Atualiza os dados do objeto local
      profileData.name = document.getElementById('settings-profile-name').value || profileData.name;
      profileData.email = document.getElementById('settings-profile-email').value || profileData.email;
      profileData.phone = document.getElementById('settings-profile-phone').value || profileData.phone;
      profileData.bio = document.getElementById('settings-profile-bio').value || profileData.bio;

      // Atualiza imagem se mudou
      const imgInput = document.getElementById('settings-profile-img');
      if (imgInput.files && imgInput.files[0]) {
        let reader = new FileReader();
        reader.onload = function(ev) {
          profileData.img = ev.target.result;
          renderProfile();
        };
        reader.readAsDataURL(imgInput.files[0]);
      } else {
        renderProfile();
      }

      // Envia para o servidor
      const formData = new FormData();
      formData.append('nome', profileData.name);
      formData.append('email', profileData.email);
      formData.append('telefone', profileData.phone);
      formData.append('bio', profileData.bio);
      if (imgInput && imgInput.files[0]) {
        formData.append('foto', imgInput.files[0]);
      }
      fetch('atualizar_perfil.php', { method: 'POST', body: formData })
  .then(res => res.json())
  .then(data => {
    if (data.sucesso) {
      if (data.foto && document.getElementById('profile-img-preview')) {
        document.getElementById('profile-img-preview').src = '../' + data.foto;
        profileData.img = '../' + data.foto;
      }
      renderProfile();
      bootstrap.Modal.getInstance(document.getElementById('settingsModal')).hide();
      showCustomToast('Perfil atualizado com sucesso!');
    } else {
      showCustomToast('Erro ao atualizar perfil: ' + (data.erro || ''), true);
    }
        });
    });
  }

  // Initial render
  renderProfile();

  // Carrega dados do perfil do servidor
  // Carrega dados do perfil do servidor
fetch('carregar_perfil.php')
  .then(res => res.json())
  .then(data => {
    if (data.erro) return;

    // Atualiza objeto local
    profileData.img = data.foto ? ('../' + data.foto) : '../assets/img/usuario.png';
    profileData.name = data.nome || profileData.name;
    profileData.email = data.email || profileData.email;
    profileData.phone = data.telefone || profileData.phone;
    profileData.bio = data.bio || profileData.bio;

    renderProfile();
  });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Função para carregar perfil do backend e atualizar tela
  function carregaPerfil() {
    fetch('carregar_perfil.php')
      .then(res => res.json())
      .then(data => {
        // Atualiza a imagem
        const fotoPerfil = data.foto && data.foto !== "" ? data.foto : '../assets/img/usuario.png';
        document.getElementById('profile-img-preview').src = fotoPerfil;
        // Atualiza nome, bio e outros campos
        document.getElementById('profile-name').innerText = data.nome || '';
        document.getElementById('profile-desc').innerText = data.bio || '';
        document.getElementById('profile-fullname').innerText = data.nome || '';
        document.getElementById('profile-phone').innerText = data.telefone || '';
        document.getElementById('profile-email').innerText = data.email || '';
        // Também atualiza o preview do modal, se existir
        const previewModal = document.getElementById('settings-profile-img-preview');
        if (previewModal) previewModal.src = fotoPerfil;
      });
  }
  carregaPerfil();

  // Abrir modal de edição e preencher os campos
  document.querySelectorAll('.nav-link').forEach(function(link) {
    if (link.textContent.includes('Configuração')) {
      link.addEventListener('click', function() {
        fetch('carregar_perfil.php')
          .then(res => res.json())
          .then(data => {
            document.getElementById('settings-profile-name').value = data.nome || '';
            document.getElementById('settings-profile-email').value = data.email || '';
            document.getElementById('settings-profile-phone').value = data.telefone || '';
            document.getElementById('settings-profile-bio').value = data.bio || '';
            const previewModal = document.getElementById('settings-profile-img-preview');
            if (previewModal) previewModal.src = (data.foto && data.foto !== "" ? data.foto : '../assets/img/usuario.png');
          });
      });
    }
  });

  // Preview da imagem no modal ao selecionar novo arquivo
  if (document.getElementById('settings-profile-img')) {
    document.getElementById('settings-profile-img').addEventListener('change', function(e) {
      if (e.target.files && e.target.files[0]) {
        let reader = new FileReader();
        reader.onload = function(ev) {
          document.getElementById('settings-profile-img-preview').src = ev.target.result;
        };
        reader.readAsDataURL(e.target.files[0]);
      }
    });
  }

  // Salvar perfil
  if (document.getElementById('settings-form')) {
    document.getElementById('settings-form').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData();
      formData.append('nome', document.getElementById('settings-profile-name').value);
      formData.append('email', document.getElementById('settings-profile-email').value);
      formData.append('telefone', document.getElementById('settings-profile-phone').value);
      formData.append('bio', document.getElementById('settings-profile-bio').value);
      const imgInput = document.getElementById('settings-profile-img');
      if (imgInput && imgInput.files[0]) {
        formData.append('foto', imgInput.files[0]);
      }
      fetch('atualizar_perfil.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
  if (data.sucesso) {
    // Atualiza a imagem do perfil na tela principal (e no modal também)
    const foto = data.foto && data.foto !== "" ? data.foto : '../assets/img/usuario.png';
    document.getElementById('profile-img-preview').src = foto;
    const previewModal = document.getElementById('settings-profile-img-preview');
    if (previewModal) previewModal.src = foto;
    carregaPerfil(); // Para garantir que todos campos estão atualizados
    bootstrap.Modal.getInstance(document.getElementById('settingsModal')).hide();
    showCustomToast('Perfil atualizado com sucesso!');
  } else {
    showCustomToast('Erro ao atualizar perfil: ' + (data.erro || ''), true);
  }
});
    });
  }
});
</script>

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


</body>
</html>
