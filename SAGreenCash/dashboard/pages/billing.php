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
// Lógica de planos para upgrade de plano
$plano = $_SESSION["usuario"]["plano"] ?? 'basico';
$possiveis_planos = [];
if ($plano === 'basico') {
  $possiveis_planos = [
    [
      'nome' => 'intermediario',
      'titulo' => 'Intermediário',
      'preco' => 'R$19,90/mês',
      'desc' => 'Relatórios, gráficos e recursos avançados',
      'cor' => '#1976d2'
    ],
    [
      'nome' => 'avancado',
      'titulo' => 'Avançado',
      'preco' => 'R$29,90/mês',
      'desc' => 'Tudo do Intermediário + automações',
      'cor' => '#fbc02d'
    ]
  ];
} elseif ($plano === 'intermediario') {
  $possiveis_planos = [
    [
      'nome' => 'avancado',
      'titulo' => 'Avançado',
      'preco' => 'R$29,90/mês',
      'desc' => 'Tudo do Intermediário + automações',
      'cor' => '#fbc02d'
    ]
  ];
}
// Se for avancado, $possiveis_planos ficará vazio e não mostrará upgrade
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
    <!-- BLOCO DE UPGRADE DE PLANO ABAIXO DO "SAIR" -->
    <?php if (count($possiveis_planos)): ?>
    <div class="mt-4 px-2">
      <?php foreach($possiveis_planos as $planoNovo): ?>
        <div class="card shadow border-0 h-100 mb-3">
          <div class="card-body text-center pb-3 pt-3">
            <h6 class="fw-bold mb-1" style="color:<?=$planoNovo['cor']?>;"><?=$planoNovo['titulo']?></h6>
            <div class="mb-1" style="font-size:1.11em; color:#43a047;"><?=$planoNovo['preco']?></div>
            <div class="mb-2" style="color:#444; font-size:.96em;"><?=$planoNovo['desc']?></div>
            <button class="btn btn-success w-100 fw-bold btn-sm" onclick="abrirUpgradePlano('<?=$planoNovo['nome']?>','<?=$planoNovo['titulo']?>')">
              Assinar <?=$planoNovo['titulo']?>
            </button>
          </div>
        </div>
      <?php endforeach;?>
    </div>
    <!-- Modal de upgrade de plano -->
    <div class="modal fade" id="upgradePlanoModal" tabindex="-1" aria-labelledby="upgradePlanoModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header" style="background: #43a047; color: #fff;">
            <h5 class="modal-title" id="upgradePlanoModalLabel">Assinar novo plano</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body">
            <form id="upgradePlanoForm">
              <input type="hidden" name="novo_plano" id="novoPlanoHidden">
              <div class="mb-3">
                <span id="upgradePlanoDescricao"></span>
              </div>
              <div class="mb-3">
                <label class="form-label">Confirme sua senha para assinar o novo plano:</label>
                <input type="password" class="form-control" name="senha" required autocomplete="current-password">
              </div>
              <button type="submit" class="btn btn-success w-100 fw-bold">Confirmar Assinatura</button>
            </form>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
</aside>
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-3 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Página</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Conta Bancária</li>
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

    <div class="container-fluid py-2 min-vh-100 d-flex flex-column">
      <div class="row flex-grow-1">
        <div class="col-lg-8">
          <div class="row">
            <div class="col-xl-6 mb-xl-0 mb-4">
<!-- Cartão Principal -->
<div class="card bg-transparent shadow-xl" id="mainCard">
  <div class="overflow-hidden position-relative border-radius-xl card-bg-dark" style="background: #232323;">
    <img src="../assets/img/illustrations/pattern-tree.svg" class="position-absolute opacity-2 start-0 top-0 w-100 z-index-1 h-100" alt="pattern-tree">
    <span class="mask bg-gradient-dark opacity-10"></span>
    <div class="card-body position-relative z-index-1 p-3">
      <i class="material-symbols-rounded text-white p-2">wifi</i>
      <h5 class="main-card-number text-white mt-4 mb-3 pb-2" id="mainCardNumber">**** **** **** ****</h5>
      <div class="d-flex align-items-center">
        <div class="me-4">
          <p class="text-white text-sm opacity-8 mb-0">Titular do cartão</p>
          <h6 class="text-white mb-0" id="mainCardHolder">*****</h6>
        </div>
        <div class="me-4">
          <p class="text-white text-sm opacity-8 mb-0">Expira</p>
          <h6 class="text-white mb-0" id="mainCardExpiry">**/**</h6>
        </div>
        <div class="me-4">
          <p class="text-white text-sm opacity-8 mb-0">CVV</p>
          <h6 class="text-white mb-0" id="mainCardCVV">***</h6>
        </div>
        <div class="ms-auto">
          <img class="logo-small mt-2" id="mainCardLogo" src="" alt="logo">
        </div>
      </div>
    </div>
  </div>
</div>
            </div>
            <div class="col-xl-6">
              <div class="row">
                <div class="col-md-6 col-6">
                  <!-- Salário -->
                  <div class="card">
                    <div class="card-header mx-4 p-3 text-center">
                      <div class="icon icon-shape icon-lg bg-gradient-dark shadow text-center border-radius-lg">
                        <i class="material-symbols-rounded opacity-10">account_balance</i>
                      </div>
                    </div>
                    <div class="card-body pt-0 p-3 text-center">
                      <h6 class="text-center mb-0">Salário</h6>
                      <span class="text-xs">Cartão Atual</span>
                      <hr class="horizontal dark my-3">
                      <h5 class="mb-0" id="salaryValue">+$0</h5>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 col-6">
                  <!-- Tipo de Cartão -->
                  <div class="card">
                    <div class="card-header mx-4 p-3 text-center">
                      <div class="icon icon-shape icon-lg bg-gradient-dark shadow text-center border-radius-lg">
                        <i class="material-symbols-rounded opacity-10">account_balance_wallet</i>
                      </div>
                    </div>
                    <div class="card-body pt-0 p-3 text-center">
                      <h6 class="text-center mb-0" id="cardTypeLabel">Tipo do Cartão</h6>
                      <span class="text-xs" id="cardTypeDescription">Cartão não adicionado</span>
                      <hr class="horizontal dark my-3">
                      <h5 class="mb-0" id="cardTypeAmount">+$0</h5>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Método de pagamento -->
            <div class="col-md-12 mb-lg-0 mb-4">
              <div class="card mt-4">
                <div class="card-header pb-0 p-3 d-flex justify-content-between align-items-center">
                  <h6 class="mb-0">Método de pagamento</h6>
                  <button id="addCardButton" class="btn bg-gradient-dark mb-0" data-bs-toggle="modal" data-bs-target="#addCardModal">
                    <i class="material-symbols-rounded text-sm">add</i>&nbsp;&nbsp;Adicionar Novo Cartão
                  </button>
                  <button id="removeCardButton" class="btn bg-gradient-danger mb-0 d-none">
                    <i class="material-symbols-rounded text-sm">delete</i>&nbsp;&nbsp;Excluir Cartão
                  </button>
                </div>
                <hr class="horizontal dark my-3">
                <div class="card-body p-3">
                  <div class="row" id="cardList">
                    <!-- Cartão aparece aqui via JS -->
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12 mb-4">
          <div class="card mt-4">
            <div class="card-header pb-0 p-3 d-flex justify-content-between align-items-center">
              <h6 class="mb-0">Histórico de Finanças</h6>
              <div class="d-flex justify-content-end mb-2">
<!-- Botão Excluir Histórico -->
<button class="btn btn-danger" id="btn-excluir-historico" data-bs-toggle="modal" data-bs-target="#modalExcluirHistorico">
  <span class="material-symbols-rounded align-middle">delete</span>
  Excluir Histórico
</button>
</div>
            </div>
            <hr class="horizontal dark my-3">
            <div class="card-body p-3">
              <div class="table-responsive">
                <table class="table align-items-center mb-0">
                <div class="card-body p-3">
  <div id="historico-financas"></div>
</div>
                </table>
              </div>
            </div>
          </div>
        </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal para adicionar/editar novo cartão -->
    <div class="modal fade" id="addCardModal" tabindex="-1" aria-labelledby="addCardModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header" style="background: #43a047; color: #fff;">
            <h5 class="modal-title" id="addCardModalLabel">ADICIONAR NOVO CARTÃO</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="addCardForm">
              <input type="hidden" id="cartaoId" name="cartaoId" />
              <div class="mb-3">
                <label for="cardNumber" class="form-label" style="color:#43a047;">Número do Cartão</label>
                <input type="text" class="form-control" id="cardNumber" placeholder="**** **** **** ****" required>
              </div>
              <div class="mb-3">
                <label for="cardHolder" class="form-label" style="color:#43a047;">Nome do Titular</label>
                <input type="text" class="form-control" id="cardHolder" placeholder="Nome do Titular" required>
              </div>
              <div class="mb-3">
                <label for="cardExpiry" class="form-label" style="color:#43a047;">Data de Expiração</label>
                <input type="text" class="form-control" id="cardExpiry" placeholder="MM/AA" required>
              </div>
              <div class="mb-3">
                <label for="cardCVV" class="form-label" style="color:#43a047;">Código de Segurança (CVV)</label>
                <input type="text" class="form-control" id="cardCVV" placeholder="Ex.: 123" maxlength="4" required>
              </div>
              <div class="mb-3">
                <label for="salary" class="form-label" style="color:#43a047;">Seu Salário</label>
                <input type="number" class="form-control" id="salary" placeholder="Ex.: 2000" required>
              </div>
              <div class="mb-3">
                <label for="creditLimit" class="form-label" style="color:#43a047;">Limite do Cartão</label>
                <input type="number" class="form-control" id="creditLimit" placeholder="Ex.: 10000" required>
              </div>
              <div class="mb-3">
                <label for="cardType" class="form-label" style="color:#43a047;">Tipo do Cartão</label>
                <select class="form-select" id="cardType" required>
                  <option value="Mastercard" selected>MasterCard</option>
                  <option value="Visa">Visa</option>
                  <option value="AmericanExpress">American Express</option>
                  <option value="Discover">Discover</option>
                </select>
              </div>
              <button type="submit" class="btn btn-success w-100" style="font-weight:bold;">SALVAR</button>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal de confirmação de exclusão -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Exclusão</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Tem certeza de que deseja excluir este cartão?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" id="confirmDeleteButton" class="btn btn-danger">Excluir</button>
          </div>
        </div>
      </div>
    </div>
  </main>

<style>
/* ... seu CSS customizado ... (mantenha o que já está no seu arquivo) ... */
#cardList .card-info-container {
  margin-left: 32px;
}
@media (max-width: 600px) {
  #cardList .card-info-container {
    margin-left: 8px;
    margin-right: 8px;
  }
}
.logo-small {
  width: 70px;
  height: auto;
}
#mainCardLogo {
  width: 100px;
  height: auto;
}
.card-info-container {
  max-width: 380px;
  width: 100%;
}
.edit-icon {
  transition: color 0.2s;
  display: flex;
  align-items: center;
  cursor: pointer;
  margin-left: 16px;
}
.edit-icon:hover i {
  color: #4caf50 !important;
}
/* ... resto do seu CSS ... */
</style>

<script>
let idParaExcluir = null;
let windowIsEditingCard = false;

// Mapeamento das imagens das bandeiras
const logos = {
  Mastercard: "../assets/img/logos/mastercard.png",
  Visa: "../assets/img/logos/visa.webp",
  AmericanExpress: "../assets/img/logos/american.png",
  Discover: "../assets/img/logos/discover.webp",
  // Adicione outras bandeiras aqui, se desejar
};

// Carrega cartões e atualiza a área de cartão principal e lista
function carregarCartoes() {
  fetch('cartao_actions.php', {
    method: 'POST',
    body: new URLSearchParams({ acao: 'listar' })
  })
    .then(res => res.json())
    .then(cartoes => {
      const cardList = document.getElementById("cardList");
      cardList.innerHTML = "";
      let principal = cartoes.find(c => c.principal == 1) || cartoes[0];

      if (principal) preencherCartaoPrincipal(principal);
      else limparCartaoPrincipal();


// Renderização do método de pagamento (logo pequena e formato compacto) + botão de editar
if (cartoes && cartoes.length) {
const numeroFormatado = formatCardNumber(principal.numero);
cardList.innerHTML = `
  <div class="card-info-container card card-body border card-plain border-radius-lg d-flex align-items-center flex-row p-3 mb-0"
       style="max-width: 340px; margin-left: 20px;">
    <img class="logo-small me-3 mb-0" style="width:80px; height:auto;" src="${logos[principal.tipo] || '../assets/img/logos/default.png'}" alt="${principal.tipo}" onerror="this.style.display='none'">
    <span class="mb-0" style="flex:1;">${numeroFormatado}</span>
    <span class="edit-icon ms-2" title="Editar Cartão" onclick="editarCartao(${principal.id})">
      <i class="material-symbols-rounded text-secondary" style="font-size: 20px;">edit_square</i>
    </span>
  </div>
  `;
  document.getElementById("addCardButton").classList.add("d-none");
} else {
  document.getElementById("addCardButton").classList.remove("d-none");
}

      document.getElementById("removeCardButton").classList.toggle("d-none", !(cartoes && cartoes.length));

      // === CONTROLE DO MODAL DE BOAS-VINDAS ===
      // Atualiza localStorage para dashboard.php mostrar/ocultar modal
      if (cartoes.length > 0) {
        localStorage.setItem("greencash_has_payment_method", "true");
      } else {
        localStorage.removeItem("greencash_has_payment_method");
      }
    });
}

function preencherCartaoPrincipal(cartao) {
  document.getElementById("mainCardNumber").textContent = cartao.numero.replace(/.(?=.{4})/g, '*');
  document.getElementById("mainCardHolder").textContent = cartao.titular;
  document.getElementById("mainCardExpiry").textContent = cartao.validade;
  document.getElementById("mainCardCVV").textContent = "***";
  const mainCardLogo = document.getElementById("mainCardLogo");
  mainCardLogo.src = logos[cartao.tipo] || "../assets/img/logos/default.png";
  mainCardLogo.alt = cartao.tipo;
  document.getElementById("salaryValue").textContent = `+$${cartao.salario}`;
  document.getElementById("cardTypeLabel").textContent = "Limite do Cartão";
  document.getElementById("cardTypeDescription").textContent = cartao.tipo;
  document.getElementById("cardTypeAmount").textContent = `+$${cartao.limite}`;
}

function limparCartaoPrincipal() {
  document.getElementById("mainCardNumber").textContent = "**** **** **** ****";
  document.getElementById("mainCardHolder").textContent = "*****";
  document.getElementById("mainCardExpiry").textContent = "*****";
  document.getElementById("mainCardCVV").textContent = "***";
  document.getElementById("mainCardLogo").src = "";
  document.getElementById("mainCardLogo").alt = "";
  document.getElementById("salaryValue").textContent = "+$0";
  document.getElementById("cardTypeLabel").textContent = "Tipo do Cartão";
  document.getElementById("cardTypeDescription").textContent = "Cartão não adicionado";
  document.getElementById("cardTypeAmount").textContent = "+$0";
}

// Edição
function editarCartao(id) {
  windowIsEditingCard = true;
  fetch('cartao_actions.php', {
    method: 'POST',
    body: new URLSearchParams({ acao: 'listar' })
  })
    .then(res => res.json())
    .then(cartoes => {
      const cartao = cartoes.find(c => c.id == id);
      if (cartao) {
        document.getElementById('cartaoId').value = cartao.id;
        document.getElementById('cardNumber').value = cartao.numero;
        document.getElementById('cardHolder').value = cartao.titular;
        document.getElementById('cardExpiry').value = cartao.validade;
        document.getElementById('cardCVV').value = cartao.cvv;
        document.getElementById('salary').value = cartao.salario;
        document.getElementById('creditLimit').value = cartao.limite;
        document.getElementById('cardType').value = cartao.tipo;
        document.getElementById("addCardModalLabel").textContent = "Editar Cartão";
        bootstrap.Modal.getOrCreateInstance(document.getElementById("addCardModal")).show();
      }
    });
}

// Ao abrir o modal de cartão, limpa ou preenche se edição
document.getElementById("addCardModal").addEventListener("show.bs.modal", function () {
  if (windowIsEditingCard) {
    document.getElementById("addCardModalLabel").textContent = "Editar Cartão";
    // O preenchimento já é feito na função editarCartao()
  } else {
    document.getElementById("addCardForm").reset();
    document.getElementById("cartaoId").value = "";
    document.getElementById("addCardModalLabel").textContent = "Adicionar Novo Cartão";
  }
});

// Salvar/adicionar cartão
document.getElementById("addCardForm").addEventListener("submit", function (e) {
  e.preventDefault();
  const form = this;
  const dados = {
    acao: form.cartaoId.value ? 'editar' : 'adicionar',
    id: form.cartaoId.value || '',
    numero: form.cardNumber.value.replace(/\s/g, ''),
    titular: form.cardHolder.value,
    validade: form.cardExpiry.value,
    cvv: form.cardCVV.value,
    salario: form.salary.value,
    limite: form.creditLimit.value,
    tipo: form.cardType.value,
    principal: 1
  };
  fetch('cartao_actions.php', {
    method: 'POST',
    body: new URLSearchParams(dados)
  })
    .then(res => res.json())
    .then(res => {
      if (res.sucesso) {
        carregarCartoes();
        bootstrap.Modal.getInstance(document.getElementById("addCardModal")).hide();
        form.reset();
        form.cartaoId.value = '';
        windowIsEditingCard = false;
        // Atualiza localStorage
        localStorage.setItem("greencash_has_payment_method", "true");
        // >>>>>>> RECARREGUE O HISTÓRICO DE FINANÇAS <<<<<<<
        if (typeof carregarHistoricoFinancas === "function") {
          carregarHistoricoFinancas();
        }
        // ou se estiver na dashboard:
        if (typeof carregarFinancas === "function") {
          carregarFinancas();
        }
      } else {
        alert("Erro ao salvar cartão");
      }
    });
});

// Excluir cartão
document.getElementById("removeCardButton").addEventListener("click", function () {
  const confirmDeleteModal = new bootstrap.Modal(document.getElementById("confirmDeleteModal"));
  confirmDeleteModal.show();
});

document.getElementById("confirmDeleteButton").addEventListener("click", function () {
  fetch('cartao_actions.php', {
    method: 'POST',
    body: new URLSearchParams({ acao: 'listar' })
  })
    .then(res => res.json())
    .then(cartoes => {
      if (cartoes.length) {
        fetch('cartao_actions.php', {
          method: 'POST',
          body: new URLSearchParams({ acao: 'remover', id: cartoes[0].id })
        })
          .then(res => res.json())
          .then(res => {
            if (res.sucesso) {
              carregarCartoes();
              // Recarrega histórico de finanças SE existir função
              if (typeof carregarHistoricoFinancas === "function") {
                carregarHistoricoFinancas();
              }
              // OU recarrega a página para garantir atualização completa:
              // window.location.reload();
            } else {
              alert("Erro ao remover cartão");
            }
            bootstrap.Modal.getInstance(document.getElementById("confirmDeleteModal")).hide();
          });
      }
    });
});

// Máscaras e validação
document.getElementById("cardHolder").addEventListener("input", function (e) {
  this.value = this.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, "");
});
document.getElementById("cardNumber").addEventListener("input", function (e) {
  let val = this.value.replace(/\D/g, "").slice(0, 16);
  let formatted = "";
  for (let i = 0; i < val.length; i += 4) {
    if (i > 0) formatted += " ";
    formatted += val.substr(i, 4);
  }
  this.value = formatted;
});
document.getElementById("cardExpiry").addEventListener("input", function (e) {
  let val = this.value.replace(/\D/g, "").slice(0, 4);
  if (val.length > 2) {
    this.value = val.slice(0, 2) + "/" + val.slice(2);
  } else {
    this.value = val;
  }
});
document.getElementById("cardCVV").addEventListener("input", function (e) {
  let val = this.value.replace(/\D/g, "");
  this.value = val.slice(0, 3);
});
document.getElementById("salary").addEventListener("input", function (e) {
  this.value = this.value.replace(/\D/g, "");
});
document.getElementById("creditLimit").addEventListener("input", function (e) {
  this.value = this.value.replace(/\D/g, "");
});

// Inicialização
carregarCartoes();
</script>

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
        <!-- Sidebar Backgrounds -->
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
        <!-- Sidenav Type -->
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
        <!-- Navbar Fixed -->
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
  <!--   Core JS Files   -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/material-dashboard.min.js?v=3.2.0"></script>
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

  <style>
    /* Linhas verdes modernas nos campos do formulário (à la Material Design) */
#addCardModal .form-control,
#addCardModal .form-select {
  border: none !important;
  border-bottom: 2px solid #43a047 !important;
  border-radius: 0 !important;
  background: transparent !important;
  box-shadow: none !important;
  outline: none !important;
  font-size: 1.09em;
  transition: border-color .21s, box-shadow .19s;
  padding: 8px 0 6px 0;
  color: #222;
}

#addCardModal .form-control:focus,
#addCardModal .form-select:focus {
  border-bottom: 2.5px solid #1976d2 !important;
  background: transparent !important;
  outline: none !important;
  box-shadow: 0 1px 0 0 #1976d2;
}

#addCardModal .form-control::placeholder {
  color: #b8c2cc;
  font-style: italic;
  opacity: 1;
}

#addCardModal .form-label {
  color: #43a047;
  font-weight: 600;
  margin-bottom: 4px;
}

#addCardModal .form-select {
  background-image: none !important;
  padding-right: 0 !important;
}

/* Deixa o botão igual ao da imagem */
#addCardModal button[type="submit"] {
  background: #43a047 !important;
  color: #fff !important;
  font-weight: 700;
  font-size: 1.07em;
  border-radius: 6px;
  border: none;
  box-shadow: none;
  margin-top: 16px;
  padding: 12px 0 10px 0;
  letter-spacing: .5px;
  text-transform: uppercase;
  transition: background .18s;
}

#addCardModal button[type="submit"]:hover {
  background: #388e3c !important;
}

/* Remove setinha do select (opcional para estilo flat) */
#addCardModal .form-select {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
}
  </style>

  <script>
function formatCardNumber(cardNumber) {
  cardNumber = (cardNumber || '').replace(/\D/g, '');
  let masked = '';
  if (cardNumber.length >= 4) {
    let maskedPart = cardNumber.slice(0, -4).replace(/\d/g, '*');
    let last4 = cardNumber.slice(-4);
    masked = maskedPart + last4;
  } else {
    masked = cardNumber.replace(/\d/g, '*');
  }
  // Preenche até 16 posições com *
  masked = masked.padStart(16, '*');
  // Adiciona espaços a cada 4 dígitos
  return masked.replace(/(.{4})/g, '$1 ').trim();
}

// No seu preencherCartaoPrincipal:
function preencherCartaoPrincipal(cartao) {
  // Formata para **** **** **** 1234
  const numeroFormatado = formatCardNumber(cartao.numero);
  document.getElementById("mainCardNumber").textContent = numeroFormatado;
  document.getElementById("mainCardHolder").textContent = cartao.titular;
  document.getElementById("mainCardExpiry").textContent = cartao.validade;
  document.getElementById("mainCardCVV").textContent = "***";
  const mainCardLogo = document.getElementById("mainCardLogo");
  mainCardLogo.src = logos[cartao.tipo] || "../assets/img/logos/default.png";
  mainCardLogo.alt = cartao.tipo;
  document.getElementById("salaryValue").textContent = `+$${cartao.salario}`;
  document.getElementById("cardTypeLabel").textContent = "Limite do Cartão";
  document.getElementById("cardTypeDescription").textContent = cartao.tipo;
  document.getElementById("cardTypeAmount").textContent = `+$${cartao.limite}`;
}

// Função para formatar o número do cartão: **** **** **** 1234
function formatCardNumber(cardNumber) {
  // Remove tudo que não é número
  cardNumber = (cardNumber || '').replace(/\D/g, '');
  let masked = '';
  if (cardNumber.length >= 4) {
    let maskedPart = cardNumber.slice(0, -4).replace(/\d/g, '*');
    let last4 = cardNumber.slice(-4);
    masked = maskedPart + last4;
  } else {
    masked = cardNumber.replace(/\d/g, '*');
  }
  // Preenche até 16 posições com *
  masked = masked.padStart(16, '*');
  // Adiciona espaços a cada 4 dígitos
  return masked.replace(/(.{4})/g, '$1 ').trim();
}
    </script>

<script>
// Função para mostrar o modal de erro de cartão necessário
function mostrarModalCartaoNecessario() {
  var modal = new bootstrap.Modal(document.getElementById('modalCartaoNecessarioErro'));
  modal.show();
}

// Função para abrir o modal de upgrade de plano, mas só se o usuário tiver um cartão cadastrado
function abrirUpgradePlano(nome, titulo) {
  // Verifica se o usuário tem cartão antes de permitir o upgrade
  fetch('cartao_actions.php', {
    method: 'POST',
    body: new URLSearchParams({ acao: 'listar' })
  })
  .then(res => res.json())
  .then(cartoes => {
    if (!cartoes || !cartoes.length) {
      mostrarModalCartaoNecessario();
      return;
    }
    // Usuário tem cartão: mostra modal de upgrade normalmente
    document.getElementById('novoPlanoHidden').value = nome;
    document.getElementById('upgradePlanoDescricao').innerHTML = `
      <b>Você está assinando o plano <span style="color:#43a047;">${titulo}</span>.</b><br>
      Seu acesso será liberado imediatamente após confirmação.
    `;
    var modal = new bootstrap.Modal(document.getElementById('upgradePlanoModal'));
    modal.show();
  });
}

// Envia upgrade via AJAX
document.getElementById('upgradePlanoForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const form = this;
  const dados = {
    novo_plano: form.novo_plano.value,
    senha: form.senha.value
  };
  fetch('upgrade_plano.php', {
    method: 'POST',
    body: new URLSearchParams(dados)
  })
  .then(res=>res.json())
  .then(resp=>{
    if(resp.sucesso){
      alert('Plano atualizado com sucesso! Recarregando...');
      location.reload();
    } else if(resp.msg && resp.msg.includes('cadastrar um cartão')) {
      mostrarModalCartaoNecessario();
    } else {
      alert('Erro: ' + (resp.msg || 'Não foi possível atualizar o plano.'));
    }
  });
});

// Botão "Cadastrar Cartão" dentro do modal de erro de cartão (opcional)
document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('btnAbrirCadastroCartao');
  if(btn) {
    btn.addEventListener('click', function() {
      bootstrap.Modal.getOrCreateInstance(document.getElementById('modalCartaoNecessarioErro')).hide();
      setTimeout(() => {
        if(document.getElementById('addCardModal')) {
          bootstrap.Modal.getOrCreateInstance(document.getElementById('addCardModal')).show();
        }
      }, 400);
    });
  }
});
</script>

<!-- Modal de erro moderno: Cartão necessário -->
<div class="modal fade" id="modalCartaoNecessarioErro" tabindex="-1" aria-labelledby="modalCartaoNecessarioErroLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content error-modal-content">
      <div class="modal-header error-modal-header">
        <span class="material-symbols-rounded error-modal-icon">credit_card_off</span>
        <h5 class="modal-title" id="modalCartaoNecessarioErroLabel">Cartão Necessário</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body text-center">
        <span class="material-symbols-rounded error-modal-icon-lg">error</span>
        <h6 class="mb-3 fw-bold" style="color:#e53935;">Você precisa cadastrar um cartão para assinar um plano.</h6>
        <p class="text-muted mb-2">Cadastre um cartão de crédito no menu <b>Conta Bancária</b> para prosseguir com a assinatura.</p>
        <button id="btnAbrirCadastroCartao" class="btn btn-success fw-bold px-4 mt-3" type="button">
          <span class="material-symbols-rounded align-middle me-1">add_card</span>
          Cadastrar Cartão
        </button>
      </div>
    </div>
  </div>
</div>
<style>
.error-modal-content {
  border: none;
  border-radius: 16px;
  background: #fff;
  box-shadow: 0 8px 40px rgba(229, 57, 53, 0.17), 0 1.5px 4px rgba(0,0,0,0.08);
  overflow: hidden;
}
.error-modal-header {
  background: linear-gradient(90deg, #e53935 70%, #ff6f00 100%);
  color: #fff;
  border-bottom: none;
  align-items: center;
}
.error-modal-header .modal-title {
  font-weight: 700;
  font-size: 1.15rem;
  letter-spacing: 1px;
}
.error-modal-icon {
  font-size: 2.3rem;
  margin-right: 12px;
  color: #fff;
}
.error-modal-icon-lg {
  color: #e53935;
  font-size: 3.5rem;
  margin-bottom: 10px;
}
#modalCartaoNecessarioErro .btn-success {
  border-radius: 7px;
  font-weight: 600;
  font-size: 1.07em;
  letter-spacing: .4px;
  transition: background .17s;
}
#modalCartaoNecessarioErro .btn-success:hover {
  background: #388e3c !important;
}
</style>
<script>
// Função para mostrar o modal de erro de cartão necessário
function mostrarModalCartaoNecessario() {
  var modal = new bootstrap.Modal(document.getElementById('modalCartaoNecessarioErro'));
  modal.show();
}
// Botão "Cadastrar Cartão" dentro do modal de erro
document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('btnAbrirCadastroCartao');
  if(btn) {
    btn.addEventListener('click', function() {
      bootstrap.Modal.getOrCreateInstance(document.getElementById('modalCartaoNecessarioErro')).hide();
      setTimeout(() => {
        if(document.getElementById('addCardModal')) {
          bootstrap.Modal.getOrCreateInstance(document.getElementById('addCardModal')).show();
        }
      }, 400);
    });
  }
});
</script>

<script>
function carregarHistoricoFinancas() {
  fetch('financas.php?historico=1')
    .then(resp => resp.json())
    .then(data => {
      const container = document.getElementById('historico-financas');
      container.innerHTML = '';

      // RECEITAS
      data.receitas.forEach(r => {
        const div = document.createElement('div');
        div.className = 'result-item receita';
        if (r.excluido == 1) {
          div.style.opacity = 0.4;
          div.style.textDecoration = "line-through";
          div.title = "Excluído do Gerenciar Finanças";
        }
        div.innerHTML = `
          <div class="info">
            <i class="material-symbols-rounded icon">trending_up</i>
            Receita: ${r.descricao} - R$${parseFloat(r.valor).toFixed(2)}
          </div>
        `;
        container.appendChild(div);
      });

      // DESPESAS
      data.despesas.forEach(d => {
        const div = document.createElement('div');
        div.className = 'result-item despesa';
        if (d.excluido == 1) {
          div.style.opacity = 0.4;
          div.style.textDecoration = "line-through";
          div.title = "Excluído do Gerenciar Finanças";
        }
        div.innerHTML = `
          <div class="info">
            <i class="material-symbols-rounded icon">trending_down</i>
            Despesa: ${d.descricao} - R$${parseFloat(d.valor).toFixed(2)}
          </div>
        `;
        container.appendChild(div);
      });

      // PLANOS
      data.planos.forEach(p => {
        const div = document.createElement('div');
        div.className = 'result-item plano';
        if (p.excluido == 1) {
          div.style.opacity = 0.4;
          div.style.textDecoration = "line-through";
          div.title = "Excluído do Gerenciar Finanças";
        }
        div.innerHTML = `
          <div class="info">
            <i class="material-symbols-rounded icon">lightbulb</i>
            Plano: ${p.descricao} - R$${parseFloat(p.valor).toFixed(2)} - ${p.prazo} meses
          </div>
        `;
        container.appendChild(div);
      });
    });
}

document.addEventListener('DOMContentLoaded', carregarHistoricoFinancas);
</script>

<style>
.result-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 18px;
  margin-bottom: 10px;
  border-radius: 8px;
  font-weight: bold;
  color: white;
  font-size: 1.07em;
}
.result-item.receita { background: #28a745; }
.result-item.despesa { background: #dc3545; }
.result-item.plano { background: #007bff; }
.result-item .icon {
  font-family: 'Material Symbols Rounded';
  font-size: 2rem;
  margin-right: 15px;
  color: #fff !important;
}
.result-item .info {
  display: flex;
  align-items: center;
  gap: 10px;
}
</style>
<!-- Google Material Symbols (se não tiver no billing.php) -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />

<!-- Modal de Confirmação -->
<div class="modal fade" id="modalExcluirHistorico" tabindex="-1" aria-labelledby="modalExcluirHistoricoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="modalExcluirHistoricoLabel">Excluir Histórico</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        Tem certeza que deseja excluir todo o histórico de finanças? Esta ação não poderá ser desfeita.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" id="btnConfirmarExcluirHistorico" class="btn btn-danger">Excluir</button>
      </div>
    </div>
  </div>
</div>

<script>
// Ao carregar a página, verifica no banco se o histórico já foi excluído
document.addEventListener('DOMContentLoaded', function () {
  fetch('consultar_historico_excluido.php')
    .then(res => res.json())
    .then(data => {
      if (data.excluido) {
        document.getElementById('historico-financas').innerHTML = `
          <div class="alert alert-info text-center">
            Seu histórico de finanças foi removido e não aparecerá mais nesta conta.<br>
            <small>Esta ação é permanente.</small>
          </div>
        `;
      } else {
        carregarHistoricoFinancas();
      }
    });
});

// Quando clicar em excluir, salva no banco
document.getElementById('btnConfirmarExcluirHistorico').addEventListener('click', function () {
  fetch('excluir_historico.php', { method: 'POST' })
    .then(res => res.json())
    .then(resp => {
      if (resp.sucesso) {
        document.getElementById('historico-financas').innerHTML = `
          <div class="alert alert-info text-center">
            Seu histórico de finanças foi removido. Não aparecerá mais nesta conta.<br>
            <small>Esta ação é permanente.</small>
          </div>
        `;
        bootstrap.Modal.getInstance(document.getElementById('modalExcluirHistorico')).hide();
      } else {
        alert('Erro ao excluir histórico: ' + (resp.msg || ''));
      }
    })
    .catch(() => alert('Erro ao excluir histórico!'));
});
</script>
</body>
</html>