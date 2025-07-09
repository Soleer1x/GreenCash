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
  <title>GreenCash</title>
  <!-- Google Fonts: Inter -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <!-- Material Symbols Rounded -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
  <!-- Nucleo Icons -->
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Material Dashboard CSS -->
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
  </div>
</aside>
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-3 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Página</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Dashboard</li>
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
    <div class="container-fluid py-2">
      <div class="row">
        <div class="ms-3">
          <h3 class="mb-0 h4 font-weight-bolder">Dashboard</h3>
          <p class="mb-4">
  Olá, <?php echo htmlspecialchars($_SESSION["usuario"]["nome"]); ?> / <?php echo htmlspecialchars($_SESSION["usuario"]["email"]); ?>
</p>
        </div>
        <div class="row">
  <div class="col mb-4">
    <div class="card h-100">
      <div class="card-header p-2 ps-3">
        <div class="d-flex justify-content-between">
          <div>
            <p class="text-sm mb-0 text-capitalize">Saldo Total</p>
            <h4 class="mb-0" id="card-total-money">R$0,00</h4>
          </div>
          <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
            <i class="material-symbols-rounded opacity-10">weekend</i>
          </div>
        </div>
      </div>
      <hr class="dark horizontal my-0">
      <div class="card-footer p-2 ps-3">
        <p class="mb-0 text-sm"><span class="text-success font-weight-bolder">+55% </span>Semana Passada</p>
      </div>
    </div>
  </div>
  <div class="col mb-4">
    <div class="card h-100">
      <div class="card-header p-2 ps-3">
        <div class="d-flex justify-content-between">
          <div>
            <p class="text-sm mb-0 text-capitalize">Saldo em Banco</p>
            <h4 class="mb-0" id="card-bank-balance">R$0,00</h4>
          </div>
          <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
            <i class="material-symbols-rounded opacity-10">account_balance</i>
          </div>
        </div>
      </div>
      <hr class="dark horizontal my-0">
      <div class="card-footer p-2 ps-3">
        <p class="mb-0 text-sm"><span class="text-info font-weight-bolder">Banco</span> Não Vinculado</p>
      </div>
    </div>
  </div>
  <div class="col mb-4">
    <div class="card h-100">
      <div class="card-header p-2 ps-3">
        <div class="d-flex justify-content-between">
          <div>
            <p class="text-sm mb-0 text-capitalize">Contas Pagas</p>
            <h4 class="mb-0" id="card-paid-bills">0</h4>
          </div>
          <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
            <i class="material-symbols-rounded opacity-10">person</i>
          </div>
        </div>
      </div>
      <hr class="dark horizontal my-0">
      <div class="card-footer p-2 ps-3">
        <p class="mb-0 text-sm"><span class="text-success font-weight-bolder">+3% </span>Mês passado</p>
      </div>
    </div>
  </div>
<!-- Card de Percas com botões à esquerda do ícone, maiores e verde claro -->
<div class="col mb-4">
  <div class="card h-100">
    <div class="card-header p-2 ps-3">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <p class="text-sm mb-0 text-capitalize">Percas</p>
          <h4 class="mb-0" id="card-losses">R$0,00</h4>
        </div>
        <div class="d-flex align-items-start" style="gap:12px;">
          <!-- Botões à esquerda do ícone -->
          <div class="d-flex flex-column align-items-end" style="gap:8px;">
<button class="btn btn-black mb-1" onclick="mostrarLosses('total')">Saldo Total</button>
<button class="btn btn-black" onclick="mostrarLosses('banco')">Cartão</button>
          </div>
          <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
            <i class="material-symbols-rounded opacity-10">leaderboard</i>
          </div>
        </div>
      </div>
    </div>
    <hr class="dark horizontal my-0">
    <div class="card-footer p-2 ps-3">
      <p class="mb-0 text-sm"><span class="text-danger font-weight-bolder">-2% </span>De ontem</p>
    </div>
  </div>
</div>
  <div class="col mb-4">
    <div class="card h-100">
      <div class="card-header p-2 ps-3">
        <div class="d-flex justify-content-between">
          <div>
            <p class="text-sm mb-0 text-capitalize">Entradas Hoje</p>
            <h4 class="mb-0" id="card-entries-today">R$0,00</h4>
          </div>
          <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
            <i class="material-symbols-rounded opacity-10">weekend</i>
          </div>
        </div>
      </div>
      <hr class="dark horizontal my-0">
      <div class="card-footer p-2 ps-3">
        <p class="mb-0 text-sm"><span class="text-success font-weight-bolder">+5% </span>De ontem</p>
      </div>
    </div>
  </div>
</div>
 
      <div class="row">
        <div class="col-lg-4 col-md-6 mt-4 mb-4">
  <div class="card">
    <div class="card-body">
      <h6 class="mb-0 ">Saldo Total</h6>
      <p class="text-sm ">Desempenho da Semana</p>
      <div class="pe-2">
        <div class="chart">
          <canvas id="chart-bars" width="400" height="150"></canvas>
        </div>
      </div>
      <hr class="dark horizontal">
      <div class="d-flex ">
        <i class="material-symbols-rounded text-sm my-auto me-1">schedule</i>
<p class="mb-0 text-sm" id="updated-time-bars">Atualizado há 0 minutos 0 segundos</p>
      </div>
    </div>
  </div>
</div>
<div class="col-lg-4 mt-4 mb-3">
  <div class="card">
    <div class="card-body">
      <h6 class="mb-0 ">Percas</h6>
      <p class="text-sm ">Desempenho nos Ultimos mêses</p>
      <div class="pe-2">
        <div class="chart">
          <canvas id="chart-line-tasks" width="400" height="150"></canvas>
        </div>
      </div>
      <hr class="dark horizontal">
      <div class="d-flex ">
        <i class="material-symbols-rounded text-sm my-auto me-1">schedule</i>
        <p class="mb-0 text-sm" id="updated-time-losses">Atualizado há 0 minutos 0 segundos</p>
      </div>
    </div>
  </div>
</div>
<div class="col-lg-4 mt-4 mb-3">
  <div class="card ">
    <div class="card-body">
      <h6 class="mb-0 "> De Hoje </h6>
      <p class="text-sm "> (<span class="font-weight-bolder">+15%</span>) Aumento dos ganhos de hoje. </p>
      <div class="pe-2">
        <div class="chart">
          <canvas id="chart-pie-cards" width="400" height="150"></canvas>
        </div>
      </div>
      <hr class="dark horizontal">
      <div class="d-flex ">
        <i class="material-symbols-rounded text-sm my-auto me-1">schedule</i>
        <p class="mb-0 text-sm" id="updated-time-pie">Atualizado há 0 minutos 0 segundos</p>
      </div>
    </div>
  </div>
</div>

<style>
.chart canvas {
  display: block;
  margin: 0 auto;
  width: 100% !important;
  height: 300px !important;
  max-width: 100%;
  max-height: 300px;
}
</style>
      </div>
<!-- Inclua Chart.js uma única vez, antes deste script -->
<!-- Chart.js (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<div class="row">
  <!-- Card GERENCIAR FINANÇAS -->
  <div class="col-lg-8 col-md-6 mb-md-0 mb-4">
    <div class="card h-100">
      <div class="card-header pb-3">
        <div class="d-flex align-items-center justify-content-between">
          <!-- Título e barra de pesquisa lado a lado -->
          <div class="d-flex align-items-center" style="gap: 12px;">
            <h6 class="mb-0" style="white-space:nowrap;">Gerenciar Finanças</h6>
<div class="position-relative" style="width: 180px;">
  <input type="text" class="form-control ps-5" id="finance-search" placeholder="Pesquisar..." style="width: 180px; min-width: 80px; height:32px; font-size:14px; border-radius:7px;">
  <span class="d-flex align-items-center" style="position:absolute;left:10px;top:0;bottom:0;height:100%;color:#b1b7c4;font-size:19px;">
    <i class="material-symbols-rounded">search</i>
  </span>
</div>
          </div>
          <div class="dropdown">
            <a class="cursor-pointer" id="dropdownGerenciar" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="material-symbols-rounded">more_vert</i>
            </a>
            <ul class="dropdown-menu px-2 py-3" aria-labelledby="dropdownGerenciar">
              <li>
                <a class="dropdown-item border-radius-md" href="javascript:;" onclick="mostrarSubmenu('entrada')">
                  <i class="material-symbols-rounded me-2" style="vertical-align:middle;color:#28a745;font-size:1.3em;">trending_up</i>
                  ADD Receitas
                </a>
              </li>
              <li>
                <a class="dropdown-item border-radius-md" href="javascript:;" onclick="mostrarSubmenu('saida')">
                  <i class="material-symbols-rounded me-2" style="vertical-align:middle;color:#dc3545;font-size:1.3em;">trending_down</i>
                  ADD Despesas
                </a>
              </li>
              <li>
                <a class="dropdown-item border-radius-md" href="javascript:;" onclick="mostrarSubmenu('plano')">
                  <i class="material-symbols-rounded me-2" style="vertical-align:middle;color:#007bff;font-size:1.3em;">lightbulb</i>
                  ADD Planos
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <!-- Corpo em branco para os dados -->
      <div class="card-body" id="finance-results">
        <!-- Aqui entram os itens de finanças, se houver -->
        <div id="receitas-container"></div>
        <div id="despesas-container"></div>
        <div id="planos-container"></div>
      </div>
    </div>
  </div>
  <!-- Card CATEGORIA à direita -->
  <div class="col-lg-4 col-md-6 mb-md-0 mb-4">
    <div class="card h-100">
      <div class="card-header pb-3">
        <div class="d-flex justify-content-between align-items-center px-3">
          <h6 class="mb-0">Categoria</h6>
        </div>
      </div>
      <div class="card-body">
        <canvas id="doughnutChart" style="display: none; max-width: 400px; margin: auto;"></canvas>
      </div>
    </div>
  </div>
</div>

<style>
#finance-search {
  border-radius: 8px !important;
  margin-left: 0 !important;
  background: #f7fafc !important;
  border: 1.5px solid #e0e4ea !important;
  box-shadow: 0 2px 8px rgba(44, 62, 80, 0.05);
  color: #222 !important;
  transition: border-color 0.2s, box-shadow 0.2s;
  font-size: 15px;
  padding: 7px 14px;
}

#finance-search:focus {
  border-color: #2ecc71 !important;
  box-shadow: 0 0 0 2px #2ecc7140;
  background: #fff !important;
  outline: none !important;
}

#finance-search::placeholder {
  color: #b1b7c4 !important;
  font-style: italic;
  opacity: 1 !important;
}
</style>

<script>
function normalizarTexto(texto) {
  return texto
    .toLowerCase()
    .normalize("NFD").replace(/[\u0300-\u036f]/g, "") // remove acentos
    .replace(/,/g, "."); // troca vírgula por ponto
}

function filtrarGerenciarFinancas(termo) {
  termo = normalizarTexto(termo.trim());
  const ids = ["receitas-container", "despesas-container", "planos-container"];
  ids.forEach(id => {
    const container = document.getElementById(id);
    if (!container) return;
    const items = container.querySelectorAll(".result-item");
    items.forEach(item => {
      const texto = normalizarTexto(item.innerText);
      if (texto.includes(termo)) {
        item.style.display = "";
      } else {
        item.style.display = "none";
      }
    });
  });
}

document.getElementById("finance-search").addEventListener("input", function() {
  filtrarGerenciarFinancas(this.value);
});
</script>

<script>

let currentType = "";
let itemParaExcluir = null;
let itemParaEditar = null;

let chartInstance = null;
function atualizarGrafico() {
  const chartCanvas = document.getElementById("doughnutChart");
  if (!chartCanvas) return;
  const ctx = chartCanvas.getContext("2d");

  let data = [];
  let labels = [];
  let backgroundColors = [];
  const categorias = [
    { id: "receitas-container", label: "Receitas", color: "#28a745" },
    { id: "despesas-container", label: "Despesas", color: "#dc3545" },
    { id: "planos-container", label: "Planos", color: "#007bff" }
  ];

  categorias.forEach((categoria) => {
    const container = document.getElementById(categoria.id);
    if (!container) return;
    const items = container.querySelectorAll(".result-item");
    let totalCategoria = 0;
    items.forEach((item) => {
      const info = item.querySelector(".info").innerText;
      const partes = info.split(" - ");
      const valor = parseFloat(partes[1].replace("R$", "").replace(",", ".").trim());
      totalCategoria += isNaN(valor) ? 0 : valor;
    });
    if (totalCategoria > 0) {
      labels.push(categoria.label);
      data.push(totalCategoria);
      backgroundColors.push(categoria.color);
    }
  });

  if (data.length === 0) {
    chartCanvas.style.display = "none";
    if (chartInstance) chartInstance.destroy();
    return;
  }

  chartCanvas.style.display = "block";
  if (chartInstance) chartInstance.destroy();

  chartInstance = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: labels,
      datasets: [{
        data: data,
        backgroundColor: backgroundColors,
        hoverBackgroundColor: backgroundColors
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'top' },
        tooltip: {
          callbacks: {
            label: function(tooltipItem) {
              return `${tooltipItem.label}: R$${tooltipItem.raw.toFixed(2)}`;
            }
          }
        }
      }
    }
  });
}

function adicionarItem(tipo, descricao, valor, prazo = null) {
  const containerId = tipo === "entrada" ? "receitas-container" :
                      tipo === "saida" ? "despesas-container" : "planos-container";
  const container = document.getElementById(containerId);
  const resultItem = document.createElement("div");
  let classeTipo = tipo === "entrada" ? "receita" :
                   tipo === "saida" ? "despesa" : "plano";
  resultItem.classList.add("result-item", classeTipo);

  const valorNum = parseFloat(valor);

  // ATUALIZAÇÃO DOS VALORES DOS CARDS:
  if (tipo === "entrada") {
    totalMoney += valorNum;
    saldoBanco += valorNum; // <-- sempre igual ao saldo total
    entriesToday += valorNum;
  } else if (tipo === "saida") {
    losses += valorNum;
    totalMoney -= valorNum;
    saldoBanco -= valorNum; // <-- sempre igual ao saldo total
  } else if (tipo === "plano") {
    totalMoney -= valorNum;
    saldoBanco -= valorNum; // <-- sempre igual ao saldo total
  }

  // Criação visual
  if (tipo === "entrada") {
    resultItem.innerHTML = `<div class="info">
      <i class="material-symbols-rounded icon" style="color:#28a745;">trending_up</i>
      Receita: ${descricao} - R$${valorNum.toFixed(2)}
    </div>`;
  } else if (tipo === "saida") {
    resultItem.innerHTML = `<div class="info">
      <i class="material-symbols-rounded icon" style="color:#dc3545;">trending_down</i>
      Despesa: ${descricao} - R$${valorNum.toFixed(2)}
    </div>`;
  } else if (tipo === "plano") {
    resultItem.innerHTML = `<div class="info">
      <i class="material-symbols-rounded icon" style="color:#007bff;">lightbulb</i>
      Plano: ${descricao} - R$${valorNum.toFixed(2)} - ${prazo} meses
    </div>`;
  }

  adicionarBotoes(resultItem, tipo);
  container.appendChild(resultItem);

  updateDashboardCards();
  atualizarGrafico();
}

function mostrarSubmenu(tipo, item = null) {
  currentType = tipo;
  itemParaEditar = item;
  const submenu = document.getElementById("submenu");
  const title = document.getElementById("submenu-title");
  const formFields = document.getElementById("form-fields");

  formFields.innerHTML = "";

  let descricao = "", valor = "", prazo = "";

  if (item) {
    const info = item.querySelector(".info").innerText;
    const partes = info.split(":")[1].trim().split(" - ");
    descricao = partes[0];
    valor = partes[1].replace("R$", "").trim();
    if (tipo === "plano") prazo = partes[2]?.replace("meses", "").trim();
  }

  // Escolha de cores e ícones dinâmicos para o topo do modal
  let iconSvg = "";
  let bgColor = "#43a047";
  if (tipo === "entrada") {
    iconSvg = `<svg width="32" height="32" fill="none" viewBox="0 0 24 24">
      <rect x="3.5" y="6.5" rx="2.5" width="17" height="11" fill="#43a047"/>
      <path stroke="#fff" stroke-width="2" d="M6 12h12M8.5 15h7"/>
    </svg>`;
    bgColor = "linear-gradient(135deg,#43a047 60%,#1976d2 100%)";
  } else if (tipo === "saida") {
    iconSvg = `<svg width="32" height="32" fill="none" viewBox="0 0 24 24">
      <rect x="3.5" y="6.5" rx="2.5" width="17" height="11" fill="#e53935"/>
      <path stroke="#fff" stroke-width="2" d="M6 12h12M8.5 9h7"/>
    </svg>`;
    bgColor = "linear-gradient(135deg,#e53935 60%,#fb6340 100%)";
  } else if (tipo === "plano") {
    iconSvg = `<svg width="32" height="32" fill="none" viewBox="0 0 24 24">
      <circle cx="12" cy="12" r="8" fill="#1976d2"/>
      <path stroke="#fff" stroke-width="2" d="M12 8v5l3 3"/>
    </svg>`;
    bgColor = "linear-gradient(135deg,#1976d2 60%,#43a047 100%)";
  }

  // Modal título
  if (tipo === "entrada") {
    title.innerText = item ? "Editar Receita" : "Adicionar Receita";
  } else if (tipo === "saida") {
    title.innerText = item ? "Editar Despesa" : "Adicionar Despesa";
  } else if (tipo === "plano") {
    title.innerText = item ? "Editar Plano" : "Adicionar Plano";
  }

  // Adiciona o ícone moderno
  title.innerHTML = `<div class="modal-icon" style="background:${bgColor};">${iconSvg}</div>` + title.innerText;

  // Campos modernos
  if (tipo === "entrada") {
    formFields.innerHTML = `
      <label for="descricao" class="label-modern">Descrição:</label>
      <input type="text" id="descricao" class="input-modern" value="${descricao}" placeholder="Ex: Salário" required>
      <label for="valor" class="label-modern">Valor:</label>
      <input type="number" id="valor" class="input-modern" value="${valor}" placeholder="Ex: 1500" required>
    `;
  } else if (tipo === "saida") {
    formFields.innerHTML = `
      <label for="descricao" class="label-modern">Descrição:</label>
      <input type="text" id="descricao" class="input-modern" value="${descricao}" placeholder="Ex: Aluguel" required>
      <label for="valor" class="label-modern">Valor:</label>
      <input type="number" id="valor" class="input-modern" value="${valor}" placeholder="Ex: 500" required>
    `;
  } else if (tipo === "plano") {
    formFields.innerHTML = `
      <label for="descricao" class="label-modern">Descrição do Plano:</label>
      <input type="text" id="descricao" class="input-modern" value="${descricao}" placeholder="Ex: Viagem" required>
      <label for="valor" class="label-modern">Valor Estimado:</label>
      <input type="number" id="valor" class="input-modern" value="${valor}" placeholder="Ex: 3000" required>
      <label for="prazo" class="label-modern">Prazo (em meses):</label>
      <input type="number" id="prazo" class="input-modern" value="${prazo}" placeholder="Ex: 12" required>
    `;
  }

  submenu.style.display = "flex";
}

function adicionarBotoes(item, tipo) {
  const botoesDiv = document.createElement("div");
  botoesDiv.classList.add("action-buttons");

  const editarBtn = document.createElement("button");
  editarBtn.classList.add("btn", "btn-warning", "btn-sm");
  editarBtn.innerText = "Editar";
  editarBtn.onclick = () => mostrarSubmenu(tipo, item);
  botoesDiv.appendChild(editarBtn);

  const excluirBtn = document.createElement("button");
  excluirBtn.classList.add("btn", "btn-danger", "btn-sm");
  excluirBtn.innerText = "Excluir";
  excluirBtn.onclick = () => confirmarExcluirPersonalizado(item);
  botoesDiv.appendChild(excluirBtn);

  if (tipo === "saida") {
    const pagarBtn = document.createElement("button");
    pagarBtn.classList.add("btn", "btn-success", "btn-sm");
    pagarBtn.innerText = "Pagar";
    pagarBtn.onclick = () => pagarDespesa(item, editarBtn);
    botoesDiv.appendChild(pagarBtn);
  }

  if (tipo === "plano") {
    const realizarBtn = document.createElement("button");
    realizarBtn.classList.add("btn", "btn-success", "btn-sm");
    realizarBtn.innerText = "Realizar";
    realizarBtn.onclick = () => realizarPlano(item, realizarBtn, editarBtn);
    botoesDiv.appendChild(realizarBtn);
  }

  item.appendChild(botoesDiv);
}

function confirmarExcluirPersonalizado(item) {
  itemParaExcluir = item;
  document.getElementById("confirm-excluir").style.display = "flex";
}

function confirmarExclusao() {
  if (itemParaExcluir) itemParaExcluir.remove();
  itemParaExcluir = null;
  document.getElementById("confirm-excluir").style.display = "none";
  atualizarGrafico();
}

function cancelarExclusao() {
  itemParaExcluir = null;
  document.getElementById("confirm-excluir").style.display = "none";
}

function pagarDespesa(item, editarBtn) {
  item.style.textDecoration = "line-through";
  const pagarBtn = item.querySelector(".btn-success");
  if (pagarBtn) pagarBtn.disabled = true;
  editarBtn.disabled = true;
  editarBtn.title = "Este item já foi pago e não pode ser editado.";

  const info = item.querySelector(".info").innerText;
  const partes = info.split(" - ");
  const valor = parseFloat(partes[1].replace("R$", "").trim());
  paidBills += 1;
losses += valor;
totalMoney -= valor;
saldoBanco -= valor; // sempre igual!
updateDashboardCards();
}

function realizarPlano(item, realizarBtn, editarBtn) {
  item.style.textDecoration = "line-through";
  if (realizarBtn) realizarBtn.disabled = true;
  editarBtn.disabled = true;
  editarBtn.title = "Este plano já foi realizado e não pode ser editado.";

  const info = item.querySelector(".info").innerText;
  const partes = info.split(" - ");
  const valor = parseFloat(partes[1].replace("R$", "").trim());
  // Só agora atualiza losses e totalMoney!
  paidBills += 1;
losses += valor;
totalMoney -= valor;
saldoBanco -= valor; // sempre igual!
updateDashboardCards();
}

function salvarInformacoes() {
  const descricao = document.getElementById("descricao").value;
  const valor = document.getElementById("valor").value;
  const prazo = document.getElementById("prazo") ? document.getElementById("prazo").value : null;

  if (!descricao || !valor || (currentType === "plano" && !prazo)) {
    alert("Por favor, preencha todos os campos.");
    return;
  }

  if (currentType === "entrada") {
    adicionarFinanca('receita', descricao, valor);
  } else if (currentType === "saida") {
    adicionarFinanca('despesa', descricao, valor);
  } else if (currentType === "plano") {
    adicionarFinanca('plano', descricao, valor, prazo);
  }

  fecharSubmenu();
}

function fecharSubmenu() {
  document.getElementById("submenu").style.display = "none";
}
// === Script extra: método de pagamento para Pagar/Realizar ===
// Não altera nada do seu código atual!

// 1. Modal HTML: coloque este trecho antes do </body> do seu HTML (fora do <script>)
if (!document.getElementById("modal-metodo-pagamento")) {
  const modalHTML = `
    <div id="modal-metodo-pagamento" class="submenu-overlay" style="display:none;z-index:2000;">
      <div class="submenu-content">
        <h5 id="modal-metodo-title"></h5>
        <p>Escolha como deseja pagar/realizar:</p>
        <div style="display:flex;gap:15px;justify-content:center;">
          <button class="btn btn-primary" onclick="finalizarMetodoPagamento('banco')">Saldo em Banco (Cartão)</button>
          <button class="btn btn-dark" onclick="finalizarMetodoPagamento('total')">Saldo Total</button>
        </div>
        <button class="btn btn-outline-secondary mt-3" onclick="fecharModalMetodoPagamento()">Cancelar</button>
      </div>
    </div>
  `;
  document.body.insertAdjacentHTML('beforeend', modalHTML);
}

// 2. Funções extras para método de pagamento
function perguntarMetodoPagamento(item, tipoAcao) {
  window._itemPagamento = item;
  window._tipoAcaoPagamento = tipoAcao; // "pagar" ou "realizar"
  document.getElementById("modal-metodo-title").innerText =
    tipoAcao === "pagar" ? "Como deseja pagar essa despesa?" : "Como deseja realizar esse plano?";
  document.getElementById("modal-metodo-pagamento").style.display = "flex";
}

function finalizarMetodoPagamento(metodo) {
  const item = window._itemPagamento;
  const tipoAcao = window._tipoAcaoPagamento;
  if (!item || !tipoAcao) return fecharModalMetodoPagamento();

  const info = item.querySelector(".info").innerText;
  const partes = info.split(" - ");
  const valor = parseFloat(partes[1].replace("R$", "").trim());

  if (tipoAcao === "pagar") {
    paidBills += 1;
    losses += valor;
    if (metodo === 'banco') {
      saldoBanco -= valor;
    } else {
      totalMoney -= valor;
    }
    // Marca como pago visualmente
    item.style.textDecoration = "line-through";
    const editarBtn = item.querySelector(".btn-warning");
    const pagarBtn = item.querySelectorAll(".btn-success")[0];
    if (pagarBtn) pagarBtn.disabled = true;
    if (editarBtn) {
      editarBtn.disabled = true;
      editarBtn.title = "Este item já foi pago e não pode ser editado.";
    }
    updateDashboardCards();
  } else if (tipoAcao === "realizar") {
    paidBills += 1;
    losses += valor;
    if (metodo === 'banco') {
      saldoBanco -= valor;
    } else {
      totalMoney -= valor;
    }
    // Marca como realizado visualmente
    item.style.textDecoration = "line-through";
    const editarBtn = item.querySelector(".btn-warning");
    const realizarBtn = item.querySelectorAll(".btn-success")[0];
    if (realizarBtn) realizarBtn.disabled = true;
    if (editarBtn) {
      editarBtn.disabled = true;
      editarBtn.title = "Este plano já foi realizado e não pode ser editado.";
    }
    updateDashboardCards();
  }

  fecharModalMetodoPagamento();
}

function fecharModalMetodoPagamento() {
  document.getElementById("modal-metodo-pagamento").style.display = "none";
  window._itemPagamento = null;
  window._tipoAcaoPagamento = null;
}

// 3. Redefina só o onclick dos botões "Pagar" e "Realizar" para abrir a modal
// (executa após o DOM e funções já carregados)
setTimeout(() => {
  // Salva referência original
  const oldAdicionarBotoes = adicionarBotoes;
  window.adicionarBotoes = function(item, tipo) {
    oldAdicionarBotoes.apply(this, arguments);

    const botoesDiv = item.querySelector(".action-buttons");
    if (tipo === "saida") {
      const pagarBtn = Array.from(botoesDiv.children).find(btn => btn.innerText === "Pagar");
      if (pagarBtn) pagarBtn.onclick = () => perguntarMetodoPagamento(item, "pagar");
    }
    if (tipo === "plano") {
      const realizarBtn = Array.from(botoesDiv.children).find(btn => btn.innerText === "Realizar");
      if (realizarBtn) realizarBtn.onclick = () => perguntarMetodoPagamento(item, "realizar");
    }
  };
}, 1);

// Cole ao final do seu <script> principal ou em um novo <script> após 
let lossesBanco = typeof lossesBanco !== "undefined" ? lossesBanco : 0;
let lossesTotal = typeof lossesTotal !== "undefined" ? lossesTotal : 0;
let lossesUltimoTipo = typeof lossesUltimoTipo !== "undefined" ? lossesUltimoTipo : 'total';

function mostrarLosses(tipo) {
  lossesUltimoTipo = tipo;
  if (tipo === 'banco') {
    document.getElementById('card-losses').innerText = "R$" + (lossesBanco || 0).toFixed(2);
  } else {
    document.getElementById('card-losses').innerText = "R$" + (lossesTotal || 0).toFixed(2);
  }
}

document.addEventListener("DOMContentLoaded", function() {
  mostrarLosses('total');
});

// CSS opcional para visual dos botões pequenos
const style = document.createElement('style');
style.innerHTML = `
.btn-xs { font-size: 11px !important; padding: 2px 8px !important; line-height: 1 !important; border-radius: 8px !important; }
.btn-pink { background: #ff2079 !important; color: #fff !important; border: none !important; }
`;
document.head.appendChild(style);

// COLE ESSE BLOCO AO FINAL DO SEU SCRIPT

// Array para guardar objetos de perda com tipo
let listaDePercas = [];

// Substitua a chamada de adicionarPerca nas despesas para usar esta função
function adicionarPercaNaLista(valor, tipo) {
  valor = parseFloat(valor) || 0;
  if (valor <= 0) return;
  listaDePercas.push({ valor, tipo });
  mostrarLosses(lossesUltimoTipo);
}

// Monkey patch: intercepta a criação de despesas e insere tipo
// (Não altera visual, só adiciona um atributo data ao item criado)
const oldAdicionarItem = adicionarItem;
adicionarItem = function(tipo, descricao, valor, prazo = null) {
  // Chama função padrão para criar o visual
  oldAdicionarItem.apply(this, arguments);

  // Só marca despesas (tipo == "saida") para o controle
  if (tipo === "saida") {
    // Por padrão, tudo vai como "total". Se quiser mudar para "banco", edite aqui:
    let tipoPerca = lossesUltimoTipo; // Assume o último tipo clicado
    adicionarPercaNaLista(valor, tipoPerca);

    // Marca visualmente o item no DOM (opcional)
    const container = document.getElementById("despesas-container");
    const itens = container.querySelectorAll(".result-item");
    const ultimo = itens[itens.length - 1];
    if (ultimo) ultimo.setAttribute("data-tipo-perca", tipoPerca);
  }
}

// Ajuste mostrarLosses para somar só do tipo selecionado
function mostrarLosses(tipo) {
  lossesUltimoTipo = tipo;
  let total = listaDePercas
    .filter(p => p.tipo === tipo)
    .reduce((acc, p) => acc + p.valor, 0);
  document.getElementById('card-losses').innerText = "R$" + total.toFixed(2);
}

// Garante que ao carregar mostra saldo total
document.addEventListener("DOMContentLoaded", function() {
  mostrarLosses('total');
});
</script>

<style>
/* Botões pequenos, pretos e todos com a mesma largura */
.btn-black {
  background: #111 !important;
  color: #fff !important;
  border: none !important;
  font-size: 11px !important;
  padding: 2px 0 !important;
  border-radius: 7px !important;
  font-weight: 600 !important;
  min-width: 88px;
  width: 88px;
  max-width: 88px;
  text-align: center;
  transition: background 0.2s;
  box-shadow: 0 1px 3px rgba(0,0,0,0.10);
  margin-bottom: 3px;
  display: inline-block;
}
.btn-black:last-child { margin-bottom: 0; }
.btn-black:hover, .btn-black:focus {
  background: #232323 !important;
  color: #fff !important;
}
</style>

<style>
  /* Adicione ao seu CSS global ou dentro de <style> */
.btn-green {
  background: #6ee7b7 !important; /* verde claro */
  color: #055f36 !important;
  border: none !important;
  font-size: 14px !important;
  padding: 6px 18px !important;
  border-radius: 8px !important;
  font-weight: 600 !important;
  min-width: 100px;
  transition: background 0.2s;
}
.btn-green:hover, .btn-green:focus {
  background: #34d399 !important;
  color: #055f36 !important;
}
</style>

<style>
  /* Estilo dos itens */
  .result-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 5px;
    margin-bottom: 8px;
    border-radius: 6px;
    font-weight: bold;
    color: white;
  }

  .result-item.receita {
    background-color: #28a745;
  }

  .result-item.despesa {
    background-color: #dc3545;
  }

  .result-item.plano {
    background-color: #007bff;
  }

.result-item .icon {
  font-family: 'Material Symbols Rounded';
  font-size: 2rem;
  color: #fff !important;
  margin-right: 12px;
  vertical-align: middle;
  line-height: 1;
  background: none !important;
  border-radius: 0;
  padding: 0;
  display: inline-block;
}

  .result-item .info {
    flex-grow: 1;
  }

  /* Gráfico */
  canvas#doughnutChart {
    margin-top: 20px;
  }

  /* Submenu */
  .submenu-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1050;
  }

  .submenu-content {
    background: white;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    width: 300px;
  }

  .submenu-content h5 {
    margin-bottom: 10px;
  }

  .submenu-content form {
    margin-top: 20px;
  }

  .submenu-content .btn {
    width: 100px;
  }

  /* Estilo para inputs de formulário */
  .submenu-content input {
    background-color: white;
    border: 1px solid #ced4da;
    border-radius: 4px;
    padding: 8px;
    box-shadow: none;
    outline: none;
  }
  .submenu-content input:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
  }

  /* Botões de ação */
  .action-buttons {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    margin-top: 10px;
  }

  .action-buttons button {
    padding: 5px 10px;
  }
</style>

<!-- Submenu -->
<div id="submenu" class="submenu-overlay" style="display: none;">
  <div class="submenu-content">
    <h5 id="submenu-title"></h5>
    <form id="submenu-form">
      <div id="form-fields"></div>
      <button type="button" class="btn btn-success mt-3" onclick="salvarInformacoes()">Salvar</button>
      <button type="button" class="btn btn-danger mt-3" onclick="fecharSubmenu()">Fechar</button>
    </form>
  </div>
</div>

<!-- Confirmação de Exclusão -->
<div id="confirm-excluir" class="submenu-overlay" style="display: none;">
  <div class="submenu-content">
    <h5>Tem certeza que deseja excluir?</h5>
    <button class="btn btn-danger mt-2" onclick="confirmarExclusao()">Sim</button>
    <button class="btn btn-secondary mt-2" onclick="cancelarExclusao()">Cancelar</button>
  </div>
</div>
           
<!-- Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header">
        <h5 class="modal-title" id="addModalLabel">Adicionar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body" id="modalContent">
        <!-- Conteúdo será carregado dinamicamente -->
      </div>
    </div>
  </div>
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
  </main>
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
        <!-- End Toggle Button -->
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
  <script src="../assets/js/plugins/chartjs.min.js"></script>

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

<!-- Modal de Boas-Vindas GreenCash (adicione antes de </body>) -->
<div id="welcome-overlay">
  <div class="welcome-modal">
    <div class="welcome-icon">
      <!-- Ícone SVG animado -->
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#43a047" stroke-width="1.3">
        <rect x="3" y="6" width="18" height="12" rx="3" fill="#21232b" fill-opacity="0.13"/>
        <rect x="3" y="6" width="18" height="12" rx="3" />
        <rect x="6.8" y="16" width="3.4" height="1.2" rx="0.6" fill="#fff" />
        <rect x="12.5" y="16" width="2.7" height="1.2" rx="0.6" fill="#fff" />
        <rect x="3" y="9" width="18" height="2.2" rx="0.6" fill="#43a047"/>
      </svg>
    </div>
    <h2 id="welcome-title">Bem-vindo ao <span style="color:#43a047">GreenCash</span>!</h2>
    <p>
      O GreenCash é seu sistema de <b>controle financeiro pessoal</b>: registre entradas, despesas, planos e tome controle da sua vida financeira.<br><br>
      <span style="color:#43a047;font-weight:600;">Comece cadastrando um método de pagamento para gerenciar melhor seus gastos.</span>
    </p>
    <div class="welcome-actions">
      <a href="billing.php" id="btn-add-payment" class="btn btn-success">Cadastrar Método de Pagamento</a>
      <button id="btn-later" class="btn btn-outline-secondary">Mais tarde</button>
    </div>
  </div>
</div>
<style>
/* BOAS-VINDAS GREEN CASH — FUNDO ESCURO & MODAL MODERNO */

#welcome-overlay {
  position: fixed;
  inset: 0;
  z-index: 2000;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(10, 12, 20, 0.97); /* preto quase puro */
  opacity: 0;
  pointer-events: none;
  transition: opacity .5s cubic-bezier(.4,0,.2,1);
}
#welcome-overlay.active {
  opacity: 1;
  pointer-events: all;
}
body.welcome-blur main,
body.welcome-blur .sidenav,
body.welcome-blur .fixed-plugin,
body.welcome-blur footer,
body.welcome-blur .navbar,
body.welcome-blur .row,
body.welcome-blur .container-fluid {
  filter: blur(10px) brightness(0.75) grayscale(0.10) contrast(1.01) !important;
  pointer-events: none !important;
  user-select: none;
  transition: filter .28s cubic-bezier(.4,0,.2,1);
}

.welcome-modal {
  position: relative;
  background: rgba(28,30,38,0.60);
  border-radius: 24px;
  max-width: 430px;
  width: 97vw;
  padding: 48px 30px 34px 30px;
  margin: auto;
  box-shadow: 0 24px 64px 0 #11181fbb, 0 1px 20px 0 #43a04744;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 20px;
  text-align: center;
  border: 1.5px solid rgba(100,255,180,0.13);
  backdrop-filter: blur(16px) saturate(1.3);
  /* animação de entrada */
  transform: translateY(-120px) scale(.97);
  opacity: 0;
  transition: transform .8s cubic-bezier(.16,1,.3,1), opacity .8s cubic-bezier(.16,1,.3,1);
}
#welcome-overlay.active .welcome-modal {
  opacity: 1;
  transform: translateY(0) scale(1);
}
.welcome-modal:after {
  content: "";
  display: block;
  position: absolute;
  left: 24px; right: 24px; bottom: -17px; height: 18px;
  border-radius: 100px;
  background: linear-gradient(90deg, #43a04733 20%, #1976d233 80%);
  filter: blur(10px) opacity(0.7);
  pointer-events: none;
}
.welcome-icon {
  width: 74px; height: 74px;
  background: linear-gradient(135deg,#43a047 50%,#1976d2 100%);
  border-radius: 50%;
  box-shadow: 0 8px 32px 0 #1976d2aa;
  display: flex; align-items: center; justify-content: center;
  margin: -56px auto 10px auto;
  animation: icon-bounce-in .92s cubic-bezier(.8,-.1,.3,1.1);
}
@keyframes icon-bounce-in {
  0% { transform: scale(.5) translateY(-50px); opacity: 0;}
  60% { transform: scale(1.1) translateY(10px);}
  100% { transform: scale(1) translateY(0); opacity: 1;}
}
.welcome-icon svg {
  width: 45px; height: 45px; color: #fff;
}
.welcome-modal h2 {
  font-size: 2.1rem;
  font-weight: 800;
  color: #fff;
  margin-bottom: 6px;
  letter-spacing: -1px;
  text-shadow: 0 2px 24px #1976d299;
}
.welcome-modal p {
  color: #e9f1f5;
  font-size: 1.13em;
  font-weight: 400;
  margin-bottom: 0;
  line-height: 1.67;
  text-shadow: 0 2px 10px #181a1e60;
}
.welcome-actions {
  display: flex;
  flex-direction: column;
  gap: 15px;
  margin-top: 8px;
  width: 100%;
}
#welcome-overlay #btn-add-payment {
  background: linear-gradient(90deg, #43a047 0%, #1976d2 100%);
  background-size: 200% 100%;
  border: none;
  color: #fff;
  font-weight: 700;
  font-size: 1.09em;
  border-radius: 11px;
  padding: 15px 0 13px 0;
  box-shadow: 0 2px 18px 0 #43a04744, 0 0px 0px #fff;
  transition: background-position .35s, box-shadow .18s, transform .16s;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  outline: none;
}
#welcome-overlay #btn-add-payment::before {
  content: "";
  position: absolute;
  left: -60%; top: 0;
  width: 220%; height: 100%;
  background: linear-gradient(120deg, transparent 40%, #fff9 53%, transparent 60%);
  opacity: 0.6;
  pointer-events: none;
  transform: skewX(-16deg);
  transition: left .31s cubic-bezier(.7,-1,.5,2);
}
#welcome-overlay #btn-add-payment:hover {
  background-position: 100% 0;
  transform: translateY(-2px) scale(1.045);
  box-shadow: 0 4px 28px 0 #43a04744;
}
#welcome-overlay #btn-add-payment:hover::before {
  left: 70%;
}
#welcome-overlay #btn-later {
  border: 2px solid #43a047;
  background: transparent;
  color: #43a047;
  font-weight: 700;
  font-size: 1em;
  border-radius: 9px;
  padding: 12px 0 11px 0;
  transition: background 0.19s, color 0.18s, border 0.14s, box-shadow .13s;
  cursor: pointer;
  outline: none;
}
#welcome-overlay #btn-later:hover {
  background: #1c1e26;
  border-color: #1976d2;
  color: #fff;
  box-shadow: 0 2px 10px #1976d22b;
}
@media (max-width: 550px) {
  .welcome-modal {
    padding: 18px 4vw 17px 4vw;
    max-width: 99vw;
    font-size: 0.97em;
  }
  .welcome-modal h2 {
    font-size: 1.19rem;
  }
  .welcome-icon {
    width: 50px; height: 50px;
    margin-top: -24px;
  }
  .welcome-icon svg {
    width: 28px; height: 28px;
  }
}
</style>
<style>
 #welcome-overlay {
  display: none;
  position: fixed;
  z-index: 1200;
  left: 0; top: 0;
  width: 100vw; height: 100vh;
  background: radial-gradient(ellipse at 60% 40%, #e3f2fd 0%, #1976d2 100%) no-repeat, rgba(15,23,42,0.94);
  background-color: rgba(30,41,59,0.90);
  overflow: auto;
  align-items: center;
  justify-content: center;
}

/* Glassmorphic modal */
#welcome-overlay .welcome-modal {
  background: rgba(255,255,255,0.85);
  border-radius: 22px;
  max-width: 410px;
  width: 92vw;
  margin: auto;
  padding: 38px 28px 30px 28px;
  box-shadow: 0 12px 36px 5px rgba(25, 118, 210, 0.11), 0 2px 16px 0 rgba(30,41,59,0.10);
  text-align: center;
  display: flex;
  flex-direction: column;
  gap: 28px;
  position: relative;
  backdrop-filter: blur(7px);
  border: 2px solid rgba(25, 118, 210, 0.09);
  animation: welcome-fadein .8s cubic-bezier(.4,0,.2,1);
}

@keyframes welcome-fadein {
  0% { opacity: 0; transform: translateY(50px) scale(.96);}
  80% { opacity: 1;}
  100% {transform: translateY(0) scale(1);}
}

/* Ícone animado no topo */
#welcome-overlay .welcome-icon {
  width: 74px;
  height: 74px;
  margin: -36px auto 8px auto;
  background: linear-gradient(135deg,#43a047 60%,#1976d2 100%);
  border-radius: 50%;
  box-shadow: 0 4px 32px 0 rgba(25, 118, 210, 0.12);
  display: flex;
  align-items: center;
  justify-content: center;
  animation: icon-bounce-in .8s cubic-bezier(.8,-.1,.3,1.1);
}
@keyframes icon-bounce-in {
  0% { transform: scale(0.4); opacity: 0;}
  60% { transform: scale(1.2);}
  100% { transform: scale(1); opacity: 1;}
}
#welcome-overlay .welcome-icon svg {
  width: 40px;
  height: 40px;
  color: #fff;
  filter: drop-shadow(0 2px 8px #1976d2b3);
}

/* Título */
#welcome-overlay h2 {
  font-size: 2rem;
  font-weight: 800;
  color: #1976d2;
  margin-bottom: 10px;
  letter-spacing: -1px;
}

/* Texto */
#welcome-overlay p {
  color: #384150;
  font-size: 1.11em;
  font-weight: 400;
  margin-bottom: 0;
  line-height: 1.65;
}

#welcome-overlay .welcome-actions {
  display: flex;
  flex-direction: column;
  gap: 14px;
  margin-top: 10px;
}

/* Botão principal com gradiente animado e brilho */
#welcome-overlay #btn-add-payment {
  background: linear-gradient(90deg, #43a047 0%, #1976d2 100%);
  background-size: 200% 100%;
  border: none;
  color: #fff;
  font-weight: 700;
  font-size: 1.09em;
  border-radius: 9px;
  padding: 13px 0 12px 0;
  box-shadow: 0 2px 16px 0 rgba(30, 41, 59, 0.10), 0 0px 0px #fff;
  transition: background-position .32s, box-shadow .18s, transform .16s;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}

#welcome-overlay #btn-add-payment::before {
  content: "";
  position: absolute;
  left: -60%; top: 0;
  width: 220%; height: 100%;
  background: linear-gradient(120deg, transparent 40%, #fff9 53%, transparent 60%);
  opacity: 0.6;
  pointer-events: none;
  transform: skewX(-16deg);
  transition: left .31s cubic-bezier(.7,-1,.5,2);
}

#welcome-overlay #btn-add-payment:hover {
  background-position: 100% 0;
  transform: translateY(-2px) scale(1.04);
  box-shadow: 0 4px 22px 0 #43a04744;
}
#welcome-overlay #btn-add-payment:hover::before {
  left: 70%;
}

/* Botão secundário */
#welcome-overlay #btn-later {
  border: 1.5px solid #bfcad6;
  background: #f9fbfc;
  color: #1976d2;
  font-weight: 600;
  font-size: 1em;
  border-radius: 8px;
  padding: 11px 0 10px 0;
  transition: background 0.18s, color 0.18s, border 0.14s, box-shadow .13s;
  cursor: pointer;
}

#welcome-overlay #btn-later:hover {
  background: #e3ecfa;
  border-color: #1976d2;
  color: #1565c0;
  box-shadow: 0 2px 10px #1976d22b;
}

/* Detalhes decorativos */
#welcome-overlay .welcome-modal:after {
  content: "";
  display: block;
  position: absolute;
  z-index: 1;
  left: 24px; right: 24px; bottom: -20px; height: 18px;
  border-radius: 100px;
  background: linear-gradient(90deg, #43a04722 20%, #1976d222 80%);
  filter: blur(10px) opacity(0.7);
  pointer-events: none;
}

@media (max-width: 500px) {
  #welcome-overlay .welcome-modal {
    padding: 23px 6vw 18px 6vw;
    max-width: 98vw;
    font-size: 0.97em;
  }
  #welcome-overlay h2 {
    font-size: 1.32rem;
  }
  #welcome-overlay .welcome-icon {
    width: 50px; height: 50px;
    margin-top: -23px;
  }
  #welcome-overlay .welcome-icon svg {
    width: 28px; height: 28px;
  }
}
</style>
<script>
  // Recebe o nome do usuário da sessão PHP
  var nomeUsuario = <?php echo json_encode($nomeUsuario); ?>;

  function showWelcomeIfNeeded() {
    if (!localStorage.getItem("greencash_has_payment_method")) {
      document.getElementById("welcome-overlay").classList.add("active");
      document.body.style.overflow = "hidden";
      document.body.classList.add("welcome-blur");
      // Adiciona o nome no título do modal
      if(nomeUsuario) {
        document.getElementById("welcome-title").innerHTML = "Bem-vindo, <span style='color:#43a047'>" + nomeUsuario + "</span>!";
      }
    } else {
      hideWelcome();
    }
  }
  function hideWelcome() {
    document.getElementById("welcome-overlay").classList.remove("active");
    document.body.style.overflow = "";
    document.body.classList.remove("welcome-blur");
  }
  document.getElementById("btn-later").onclick = hideWelcome;
  window.addEventListener("DOMContentLoaded", function() {
    setTimeout(showWelcomeIfNeeded, 10);
  });
</script>



<script>
  // Função para ajustar o canvas para telas retina (alta densidade)
  function retinaFix(canvasId) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    const dpr = window.devicePixelRatio || 1;
    // Pega o tamanho real exibido do canvas
    const rect = canvas.getBoundingClientRect();
    // Só faz o ajuste se necessário (evita redimensionamento constante)
    if (canvas.width !== rect.width * dpr || canvas.height !== rect.height * dpr) {
      canvas.width = rect.width * dpr;
      canvas.height = rect.height * dpr;
      canvas.getContext('2d').setTransform(dpr, 0, 0, dpr, 0, 0);
    }
  }
</script>
<script>
  // Variáveis globais para os cards
let totalMoney = 0;
let saldoBanco = 0;
let paidBills = 0;
let losses = 0;
let entriesToday = 0;

// Última atualização de cada gráfico
let lastUpdateBars = new Date();
let lastUpdateLosses = new Date();
let lastUpdatePie = new Date();

// Instâncias dos gráficos
let moneyBarChart = null;
let pieCardsChart = null;
let lossesBarChart = null;

// Atualiza o tempo "Atualizado há X minutos Y segundos"
function setUpdatedTimeText(id, lastUpdate) {
  const now = new Date();
  const diffMs = now - lastUpdate;
  const diffSec = Math.floor(diffMs / 1000);
  const min = Math.floor(diffSec / 60);
  const sec = diffSec % 60;
  document.getElementById(id).innerText = `Atualizado há ${min} minutos ${sec} segundos`;
}

// Atualiza o texto dos cards e os gráficos
function updateDashboardCards() {
  document.getElementById('card-total-money').innerText = `R$${totalMoney.toFixed(2)}`;
  document.getElementById('card-bank-balance').innerText = `R$${saldoBanco.toFixed(2)}`;
  document.getElementById('card-paid-bills').innerText = paidBills;
  document.getElementById('card-losses').innerText = `R$${losses.toFixed(2)}`;
  document.getElementById('card-entries-today').innerText = `R$${entriesToday.toFixed(2)}`;
  updateMoneyBarChart();
  updatePieCardsChart();
  updateLossesBarChart();
}

// Corrige o canvas para telas retina/alta densidade
function retinaFix(canvasId) {
  const canvas = document.getElementById(canvasId);
  if (!canvas) return;
  const dpr = window.devicePixelRatio || 1;
  const rect = canvas.getBoundingClientRect();
  if (canvas.width !== rect.width * dpr || canvas.height !== rect.height * dpr) {
    canvas.width = rect.width * dpr;
    canvas.height = rect.height * dpr;
    canvas.getContext('2d').setTransform(dpr, 0, 0, dpr, 0, 0);
  }
}

// Gráfico de linha (saldo total na semana)
function updateMoneyBarChart() {
  retinaFix('chart-bars');
  const ctx = document.getElementById('chart-bars').getContext('2d');
  const data = [totalMoney, totalMoney * 0.8, totalMoney * 0.6, totalMoney * 0.9, totalMoney];
  const labels = ["Seg", "Ter", "Qua", "Qui", "Sex"];
  if (moneyBarChart) {
    moneyBarChart.data.datasets[0].data = data;
    moneyBarChart.update();
  } else {
    const gradient = ctx.createLinearGradient(0, 0, 0, 150);
    gradient.addColorStop(0, "rgba(67, 160, 71, 0.8)");
    gradient.addColorStop(1, "rgba(67, 160, 71, 0.1)");
    moneyBarChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: "Saldo Total",
          data: data,
          fill: true,
          backgroundColor: gradient,
          borderColor: "#43A047",
          pointRadius: 3,
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
          x: { ticks: { font: { size: 10 } } },
          y: { beginAtZero: true, ticks: { font: { size: 10 } } }
        }
      }
    });
  }
  lastUpdateBars = new Date();
  setUpdatedTimeText('updated-time-bars', lastUpdateBars);
}

// Gráfico de pizza (saldo, entradas hoje, perdas)
function updatePieCardsChart() {
  retinaFix('chart-pie-cards');
  const ctx = document.getElementById('chart-pie-cards').getContext('2d');
  const data = [
    Math.max(totalMoney, 0),
    Math.max(entriesToday, 0),
    Math.max(losses, 0)
  ];
  const labels = [
    "Saldo Total",
    "Entradas Hoje",
    "Percas"
  ];
  const backgroundColors = [
    "#43A047",
    "#1976D2",
    "#E53935"
  ];
  if (pieCardsChart) {
    pieCardsChart.data.datasets[0].data = data;
    pieCardsChart.update();
  } else {
    pieCardsChart = new Chart(ctx, {
      type: 'pie',
      data: {
        labels: labels,
        datasets: [{
          data: data,
          backgroundColor: backgroundColors
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'top' }
        }
      }
    });
  }
  lastUpdatePie = new Date();
  setUpdatedTimeText('updated-time-pie', lastUpdatePie);
}

// Gráfico de barras horizontal (perdas hoje, mês passado, média)
function updateLossesBarChart() {
  retinaFix('chart-line-tasks');
  const ctx = document.getElementById('chart-line-tasks').getContext('2d');
  const data = [losses, losses * 0.6, losses * 0.8];
  const labels = ["Hoje", "Mês passado", "Média"];
  if (lossesBarChart) {
    lossesBarChart.data.datasets[0].data = data;
    lossesBarChart.update();
  } else {
    lossesBarChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: "Percas",
          backgroundColor: ["#E53935", "#FF7043", "#FFB300"],
          data: data,
          borderRadius: 12,
          barPercentage: 0.6,
          categoryPercentage: 0.5
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
          x: { beginAtZero: true, ticks: { font: { size: 10 } } },
          y: { ticks: { font: { size: 10 } } }
        }
      }
    });
  }
  lastUpdateLosses = new Date();
  setUpdatedTimeText('updated-time-losses', lastUpdateLosses);
}

// Atualização dos tempos a cada segundo
setInterval(function() {
  setUpdatedTimeText('updated-time-bars', lastUpdateBars);
  setUpdatedTimeText('updated-time-losses', lastUpdateLosses);
  setUpdatedTimeText('updated-time-pie', lastUpdatePie);
}, 1000);

// Inicializa valores e gráficos ao carregar a página
window.onload = function() {
  updateDashboardCards();
};

/**
 * Funções para manipular os valores dos cards:
 * Chame essas funções nos handlers dos botões de ação!
 */

// Adicionar receita
function adicionarReceita(valor) {
  valor = parseFloat(valor);
  totalMoney += valor;
  saldoBanco += valor;
  entriesToday += valor;
  updateDashboardCards();
}

// Pagar despesa (valor, já foi validado no backend)
function pagarDespesaCard(valor) {
  valor = parseFloat(valor);
  paidBills += 1;
  losses += valor;
  totalMoney -= valor;
  saldoBanco -= valor;
  updateDashboardCards();
}

// Realizar plano
function realizarPlanoCard(valor) {
  valor = parseFloat(valor);
  losses += valor;
  totalMoney -= valor;
  saldoBanco -= valor;
  updateDashboardCards();
}

// Excluir receita
function excluirReceita(valor) {
  valor = parseFloat(valor);
  totalMoney -= valor;
  saldoBanco -= valor;
  entriesToday -= valor;
  updateDashboardCards();
}

// Excluir despesa (se não paga)
function excluirDespesa(valor) {
  // Não altera os cards se ainda não paga
  updateDashboardCards();
}

// Excluir despesa (se já paga, volta os valores)
function excluirDespesaPaga(valor) {
  valor = parseFloat(valor);
  paidBills -= 1;
  losses -= valor;
  totalMoney += valor;
  saldoBanco += valor;
  updateDashboardCards();
}

// Excluir plano (se não realizado)
function excluirPlano(valor) {
  // Não altera os cards se ainda não realizado
  updateDashboardCards();
}

// Excluir plano já realizado
function excluirPlanoRealizado(valor) {
  valor = parseFloat(valor);
  losses -= valor;
  totalMoney += valor;
  saldoBanco += valor;
  updateDashboardCards();
}
</script>

<style>
  /* === GreenCash WELCOME MODAL OVERLAY & BLUR EFFECT (fundo bem escurecido) === */

/* Blur e escurecimento do fundo principal quando o overlay está ativo */
body.welcome-blur main,
body.welcome-blur .sidenav,
body.welcome-blur .fixed-plugin,
body.welcome-blur footer,
body.welcome-blur .navbar,
body.welcome-blur .row,
body.welcome-blur .container-fluid {
  filter: blur(10px) brightness(0.70) grayscale(0.15) contrast(0.98) !important;
  pointer-events: none !important;
  user-select: none;
  transition: filter .28s cubic-bezier(.4,0,.2,1);
}

/* Overlay tela de boas-vindas (fundo escurecido) */
#welcome-overlay {
  /* Remova o display: none; */
  position: fixed;
  inset: 0;
  z-index: 2000;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(10, 12, 20, 0.97);
  opacity: 0;
  pointer-events: none;
  transition: opacity .5s cubic-bezier(.4,0,.2,1);
}
#welcome-overlay.active {
  opacity: 1;
  pointer-events: all;
}

/* Modal */
#welcome-overlay .welcome-modal {
  background: rgba(255,255,255,0.93);
  border-radius: 22px;
  max-width: 410px;
  width: 92vw;
  margin: auto;
  padding: 38px 28px 30px 28px;
  box-shadow: 0 16px 40px 10px rgba(25, 118, 210, 0.21), 0 2px 16px 0 rgba(30,41,59,0.18);
  text-align: center;
  display: flex;
  flex-direction: column;
  gap: 28px;
  position: relative;
  backdrop-filter: blur(7px);
  border: 2px solid rgba(25, 118, 210, 0.11);
  animation: welcome-fadein .8s cubic-bezier(.4,0,.2,1);
}

@keyframes welcome-fadein {
  0% { opacity: 0; transform: translateY(50px) scale(.96);}
  80% { opacity: 1;}
  100% {transform: translateY(0) scale(1);}
}

/* Ícone animado no topo */
#welcome-overlay .welcome-icon {
  width: 74px;
  height: 74px;
  margin: -36px auto 8px auto;
  background: linear-gradient(135deg,#43a047 60%,#1976d2 100%);
  border-radius: 50%;
  box-shadow: 0 4px 32px 0 rgba(25, 118, 210, 0.12);
  display: flex;
  align-items: center;
  justify-content: center;
  animation: icon-bounce-in .8s cubic-bezier(.8,-.1,.3,1.1);
}
@keyframes icon-bounce-in {
  0% { transform: scale(0.4); opacity: 0;}
  60% { transform: scale(1.2);}
  100% { transform: scale(1); opacity: 1;}
}
#welcome-overlay .welcome-icon svg {
  width: 40px;
  height: 40px;
  color: #fff;
  filter: drop-shadow(0 2px 8px #1976d2b3);
}

/* Título */
#welcome-overlay h2 {
  font-size: 2rem;
  font-weight: 800;
  color: #1976d2;
  margin-bottom: 10px;
  letter-spacing: -1px;
}

/* Texto */
#welcome-overlay p {
  color: #384150;
  font-size: 1.11em;
  font-weight: 400;
  margin-bottom: 0;
  line-height: 1.65;
}

#welcome-overlay .welcome-actions {
  display: flex;
  flex-direction: column;
  gap: 14px;
  margin-top: 10px;
}

/* Botão principal com gradiente animado e brilho */
#welcome-overlay #btn-add-payment {
  background: linear-gradient(90deg, #43a047 0%, #1976d2 100%);
  background-size: 200% 100%;
  border: none;
  color: #fff;
  font-weight: 700;
  font-size: 1.09em;
  border-radius: 9px;
  padding: 13px 0 12px 0;
  box-shadow: 0 2px 16px 0 rgba(30, 41, 59, 0.10), 0 0px 0px #fff;
  transition: background-position .32s, box-shadow .18s, transform .16s;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}

#welcome-overlay #btn-add-payment::before {
  content: "";
  position: absolute;
  left: -60%; top: 0;
  width: 220%; height: 100%;
  background: linear-gradient(120deg, transparent 40%, #fff9 53%, transparent 60%);
  opacity: 0.6;
  pointer-events: none;
  transform: skewX(-16deg);
  transition: left .31s cubic-bezier(.7,-1,.5,2);
}

#welcome-overlay #btn-add-payment:hover {
  background-position: 100% 0;
  transform: translateY(-2px) scale(1.04);
  box-shadow: 0 4px 22px 0 #43a04744;
}
#welcome-overlay #btn-add-payment:hover::before {
  left: 70%;
}

/* Botão secundário */
#welcome-overlay #btn-later {
  border: 1.5px solid #bfcad6;
  background: #f9fbfc;
  color: #1976d2;
  font-weight: 600;
  font-size: 1em;
  border-radius: 8px;
  padding: 11px 0 10px 0;
  transition: background 0.18s, color 0.18s, border 0.14s, box-shadow .13s;
  cursor: pointer;
}

#welcome-overlay #btn-later:hover {
  background: #e3ecfa;
  border-color: #1976d2;
  color: #1565c0;
  box-shadow: 0 2px 10px #1976d22b;
}

/* Detalhes decorativos */
#welcome-overlay .welcome-modal:after {
  content: "";
  display: block;
  position: absolute;
  z-index: 1;
  left: 24px; right: 24px; bottom: -20px; height: 18px;
  border-radius: 100px;
  background: linear-gradient(90deg, #43a04722 20%, #1976d222 80%);
  filter: blur(10px) opacity(0.7);
  pointer-events: none;
}

@media (max-width: 500px) {
  #welcome-overlay .welcome-modal {
    padding: 23px 6vw 18px 6vw;
    max-width: 98vw;
    font-size: 0.97em;
  }
  #welcome-overlay h2 {
    font-size: 1.32rem;
  }
  #welcome-overlay .welcome-icon {
    width: 50px; height: 50px;
    margin-top: -23px;
  }
  #welcome-overlay .welcome-icon svg {
    width: 28px; height: 28px;
  }
}
</style>

<script>
  function showWelcomeIfNeeded() {
  if (!localStorage.getItem("greencash_has_payment_method")) {
    document.getElementById("welcome-overlay").classList.add("active");
    document.body.style.overflow = "hidden";
    document.body.classList.add("welcome-blur");
  } else {
    hideWelcome();
  }
}
function hideWelcome() {
  document.getElementById("welcome-overlay").classList.remove("active");
  document.body.style.overflow = "";
  document.body.classList.remove("welcome-blur");
}
document.getElementById("btn-later").onclick = hideWelcome;
window.addEventListener("DOMContentLoaded", function() {
  setTimeout(showWelcomeIfNeeded, 10);
});
</script>

<script>
  // Carrega receitas, despesas e planos do banco ao abrir a página
function carregarFinancas() {
  fetch("financas.php")
    .then(resp => resp.json())
    .then(data => {
      // Limpa containers
      document.getElementById("receitas-container").innerHTML = "";
      document.getElementById("despesas-container").innerHTML = "";
      document.getElementById("planos-container").innerHTML = "";

      // Zera totais antes de somar novamente
      totalMoney = 0;
      saldoBanco = 0;
      paidBills = 0;
      losses = 0;
      entriesToday = 0;

      // --- RECEITAS ---
      data.receitas.forEach(r => {
        const valor = parseFloat(r.valor);
        totalMoney += valor;
        saldoBanco += valor;
        // Considera receita de hoje (você pode adicionar um campo data e comparar com hoje para maior precisão)
        entriesToday += valor; // Ajuste se quiser filtrar só as de hoje

        const div = document.createElement("div");
        div.className = "result-item receita";
        div.innerHTML = `
          <div class="info">
            <i class="material-symbols-rounded icon" style="color:#28a745;">trending_up</i>
            Receita: ${r.descricao} - R$${valor.toFixed(2)}
          </div>
          <div class="action-buttons">
            <button class="btn btn-warning btn-sm" onclick="editarFinanca('receita', ${r.id}, ${valor})">Editar</button>
            <button class="btn btn-danger btn-sm" onclick="excluirFinanca('receita', ${r.id}, ${valor})">Excluir</button>
          </div>
        `;
        document.getElementById("receitas-container").appendChild(div);
      });

      // --- DESPESAS ---
data.despesas.forEach(d => {
  const valor = parseFloat(d.valor);
  let pago = d.pago == 1 ? 'text-decoration:line-through;' : '';
  if (d.pago == 1) {
    paidBills += 1;
    losses += valor;
    if (d.origem_pagamento === 'banco') {
      saldoBanco -= valor;
    } else if (d.origem_pagamento === 'total' || !d.origem_pagamento) {
      totalMoney -= valor;
    }
  }

        const div = document.createElement("div");
        div.className = "result-item despesa";
        div.innerHTML = `
          <div class="info" style="${pago}">
            <i class="material-symbols-rounded icon" style="color:#dc3545;">trending_down</i>
            Despesa: ${d.descricao} - R$${valor.toFixed(2)}
          </div>
          <div class="action-buttons">
            <button class="btn btn-warning btn-sm" onclick="editarFinanca('despesa', ${d.id}, ${valor}, ${d.pago})" ${d.pago == 1 ? 'disabled title="Já paga"' : ''}>Editar</button>
            <button class="btn btn-danger btn-sm" onclick="excluirFinanca('despesa', ${d.id}, ${valor}, ${d.pago})">Excluir</button>
            <button class="btn btn-success btn-sm" onclick="pagarDespesa(${d.id}, ${valor})" ${d.pago == 1 ? 'disabled title="Já paga"' : ''}>Pagar</button>
          </div>
        `;
        document.getElementById("despesas-container").appendChild(div);
      });

      // --- PLANOS ---
data.planos.forEach(p => {
  const valor = parseFloat(p.valor);
  let realizado = p.realizado == 1 ? 'text-decoration:line-through;' : '';
  if (p.realizado == 1) {
    losses += valor;
    if (p.origem_pagamento === 'banco') {
      saldoBanco -= valor;
    } else if (p.origem_pagamento === 'total' || !p.origem_pagamento) {
      totalMoney -= valor;
    }
  }

        const div = document.createElement("div");
        div.className = "result-item plano";
        div.innerHTML = `
          <div class="info" style="${realizado}">
            <i class="material-symbols-rounded icon" style="color:#007bff;">lightbulb</i>
            Plano: ${p.descricao} - R$${valor.toFixed(2)} - ${p.prazo} meses
          </div>
          <div class="action-buttons">
            <button class="btn btn-warning btn-sm" onclick="editarFinanca('plano', ${p.id}, ${valor}, ${p.realizado})" ${p.realizado == 1 ? 'disabled title="Já realizado"' : ''}>Editar</button>
            <button class="btn btn-danger btn-sm" onclick="excluirFinanca('plano', ${p.id}, ${valor}, ${p.realizado})">Excluir</button>
            <button class="btn btn-success btn-sm" onclick="realizarPlano(${p.id}, ${valor})" ${p.realizado == 1 ? 'disabled title="Já realizado"' : ''}>Realizar</button>
          </div>
        `;
        document.getElementById("planos-container").appendChild(div);
      });

      // Atualiza os cards e gráficos
      updateDashboardCards();
atualizarGrafico();
    });
}
carregarFinancas();

// --- ADICIONAR ---
function adicionarFinanca(tipo, descricao, valor, prazo = null) {
  const formData = new FormData();
  formData.append("tipo", tipo);
  formData.append("descricao", descricao);
  formData.append("valor", valor);
  if (tipo === "plano") formData.append("prazo", prazo);
  fetch("financas.php", {
    method: "POST",
    body: formData
  })
  .then(resp => resp.json())
  .then(res => {
    if (res.success) {
      carregarFinancas(); // Atualiza a tela com os dados do banco
    } else {
      alert("Erro ao adicionar: " + (res.msg || ""));
    }
  });
}

// --- PAGAR DESPESA ---
function pagarDespesa(id, valor) {
  fetch(`financas.php?action=pagar_despesa&id=${id}`, { method: "POST" })
    .then(resp => resp.json())
    .then(res => {
      if (res.success) {
        // Atualiza os valores dos cards (evita duplicidade pois carregarFinancas já recalcula)
        carregarFinancas();
      } else {
        alert("Erro ao pagar: " + (res.msg || ""));
      }
    });
}

// --- REALIZAR PLANO ---
function realizarPlano(id, valor) {
  fetch(`financas.php?action=realizar_plano&id=${id}`, { method: "POST" })
    .then(resp => resp.json())
    .then(res => {
      if (res.success) {
        carregarFinancas();
      } else {
        alert("Erro ao realizar plano: " + (res.msg || ""));
      }
    });
}

// --- EXCLUIR FINANÇA (Receita, Despesa ou Plano) ---
function excluirFinanca(tipo, id, valor, status = 0) {
  // status pode ser pago (despesa) ou realizado (plano), para eventual ajuste dos cards
  if (!confirm('Tem certeza que deseja excluir este item?')) return;
  fetch(`financas.php?action=excluir_${tipo}&id=${id}`, { method: "POST" })
    .then(resp => resp.json())
    .then(res => {
      if (res.success) {
        carregarFinancas();
      } else {
        alert("Erro ao excluir: " + (res.msg || ""));
      }
    });
}

// --- EDITAR FINANÇA (Função exemplo, implemente o modal conforme seu padrão) ---
function editarFinanca(tipo, id, valor, status = 0) {
  // Abra seu modal de edição, recupere os dados do backend e ao salvar chame carregarFinancas()
  // Para simplificar, não implementamos o modal aqui.
}



// Função para abrir o modal de edição
function editarFinanca(tipo, id, valor = 0, status = 0) {
  // Buscar dados atuais do item (idealmente via backend, mas aqui um exemplo simples)
  fetch(`financas.php?action=get_${tipo}&id=${id}`)
    .then(resp => resp.json())
    .then(item => {
      // Monta o formulário dinâmico
      let html = `
        <label>Descrição:</label>
        <input type="text" id="edit-descricao" class="form-control mb-2" value="${item.descricao || ''}" required>
        <label>Valor:</label>
        <input type="number" id="edit-valor" class="form-control mb-2" value="${item.valor ? item.valor : ''}" required>
      `;
      if (tipo === "plano") {
        html += `
          <label>Prazo (meses):</label>
          <input type="number" id="edit-prazo" class="form-control mb-2" value="${item.prazo || ''}" required>
        `;
      }
      html += `<button class="btn btn-success mt-2" onclick="salvarEdicao('${tipo}', ${id})">Salvar</button>
               <button class="btn btn-secondary mt-2" onclick="fecharModalEdicao()">Cancelar</button>`;

      // Exibe em um modal simples (pode adaptar para seu modal)
      const modal = document.createElement('div');
      modal.id = "modal-edicao";
      modal.style.position = "fixed";
      modal.style.top = 0;
      modal.style.left = 0;
      modal.style.width = "100vw";
      modal.style.height = "100vh";
      modal.style.background = "rgba(0,0,0,0.4)";
      modal.style.display = "flex";
      modal.style.alignItems = "center";
      modal.style.justifyContent = "center";
      modal.innerHTML = `<div style="background:#fff;padding:24px;border-radius:10px;min-width:300px;">${html}</div>`;
      document.body.appendChild(modal);
    });
}

// Função para salvar a edição
function salvarEdicao(tipo, id) {
  const descricao = document.getElementById("edit-descricao").value;
  const valor = document.getElementById("edit-valor").value;
  let prazo = "";
  if (tipo === "plano") {
    prazo = document.getElementById("edit-prazo").value;
  }

  // Envia para o backend atualizar
  const formData = new FormData();
  formData.append("action", "editar");
  formData.append("tipo", tipo);
  formData.append("id", id);
  formData.append("descricao", descricao);
  formData.append("valor", valor);
  if (tipo === "plano") formData.append("prazo", prazo);

  fetch("financas.php", {
    method: "POST",
    body: formData
  })
  .then(resp => resp.json())
  .then(res => {
    if (res.success) {
      fecharModalEdicao();
      carregarFinancas();
    } else {
      alert("Erro ao editar: " + (res.msg || ""));
    }
  });
}

function fecharModalEdicao() {
  const modal = document.getElementById("modal-edicao");
  if (modal) modal.remove();
}
</script>


<!-- MODAL EDIÇÃO -->
<div id="modal-edicao" class="modal-overlay" style="display:none;">
  <div class="modal-box">
    <h4 id="modal-edicao-titulo">Editar</h4>
    <form id="modal-edicao-form" autocomplete="off">
      <div class="mb-2">
        <label>Descrição:</label>
        <input type="text" id="edit-descricao" class="form-control" required>
      </div>
      <div class="mb-2">
        <label>Valor (R$):</label>
        <input type="number" id="edit-valor" class="form-control" step="0.01" required>
      </div>
      <div class="mb-2" id="edit-prazo-group" style="display:none;">
        <label>Prazo (meses):</label>
        <input type="number" id="edit-prazo" class="form-control" min="1">
      </div>
      <div class="d-flex justify-content-between mt-3 gap-2" style="gap:14px;">
        <button type="submit" class="btn btn-success w-50">Salvar</button>
        <button type="button" class="btn btn-outline-secondary w-50" onclick="fecharModalEdicao()">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL EXCLUIR -->
<div id="modal-excluir" class="modal-overlay" style="display:none;">
  <div class="modal-box">
    <h4>Excluir</h4>
    <p>Tem certeza que deseja excluir este item?</p>
    <div class="d-flex justify-content-between mt-3 gap-2">
      <button class="btn btn-danger w-50" onclick="confirmarExcluirFinanca()">Sim, Excluir</button>
      <button class="btn btn-outline-secondary w-50" onclick="fecharModalExcluir()">Cancelar</button>
    </div>
  </div>
</div>

<style>
.modal-overlay {
  position: fixed; z-index: 3000; left: 0; top: 0; width: 100vw; height: 100vh;
  background: rgba(10,20,40,0.23); display: flex; align-items: center; justify-content: center;
  backdrop-filter: blur(2px);
}
.modal-box {
  background: #fff; border-radius: 16px; max-width: 360px; width: 92vw;
  padding: 30px 24px 20px 24px; box-shadow: 0 8px 32px #1976d2aa;
  animation: modalShow .22s cubic-bezier(.22,1,.36,1);
}
@keyframes modalShow { from {opacity: 0; transform: translateY(-30px) scale(.98);} to {opacity: 1; transform: translateY(0) scale(1);} }
.modal-box h4 { font-weight: 800; color: #1976d2; margin-bottom: 14px; text-align: center;}
.modal-box p { color: #333; text-align: center;}
.d-flex { display: flex; }
.gap-2 { gap: 12px; }
.w-50 { width:47%; }
</style>

<!-- MODAL PAGAR/REALIZAR MODERNA -->
<div id="modal-pagar" class="modal-overlay" style="display:none;">
  <div class="modal-box modern-pay-modal">
    <h4 id="modal-pagar-titulo" class="pay-title">Como deseja pagar?</h4>
    <p class="pay-subtitle">Escolha a origem do valor:</p>
    <div class="pay-btn-row">
      <button class="btn-pay btn-pay-bank" onclick="finalizarPagamento('banco')">Saldo em Banco (Cartão)</button>
      <button class="btn-pay btn-pay-total" onclick="finalizarPagamento('total')">Saldo Total</button>
    </div>
    <button class="btn-pay-cancel" onclick="fecharModalPagar()">Cancelar</button>
  </div>
</div>

<style>
.modal-overlay {
  position: fixed; z-index: 3000; left: 0; top: 0; width: 100vw; height: 100vh;
  background: rgba(10,20,40,0.23); display: flex; align-items: center; justify-content: center;
  backdrop-filter: blur(2px);
}
.modal-box {
  background: #fff; border-radius: 16px; max-width: 360px; width: 92vw;
  padding: 30px 24px 20px 24px; box-shadow: 0 8px 32px #1976d2aa;
  animation: modalShow .22s cubic-bezier(.22,1,.36,1);
}
@keyframes modalShow { from {opacity: 0; transform: translateY(-30px) scale(.98);} to {opacity: 1; transform: translateY(0) scale(1);} }
.modal-box h4 { font-weight: 800; color: #1976d2; margin-bottom: 14px; text-align: center;}
.modal-box p { color: #333; text-align: center;}
.modal-box label { font-weight: 600; color: #222; }
.modal-box input { margin-top: 3px; margin-bottom: 2px;}
.gap-2 { gap: 12px; }
.w-50 { width:47%; }
.w-100 { width:100%; }


/* MODAL PAGAR/REALIZAR - MODERNO */
.modern-pay-modal {
  padding: 34px 26px 24px 26px !important;
  background: #fff;
  border-radius: 18px;
  max-width: 340px;
  width: 94vw;
  box-shadow: 0 8px 32px #1976d244, 0 1.5px 8px #43a04723;
  display: flex;
  flex-direction: column;
  align-items: stretch;
  gap: 20px;
  position: relative;
  animation: modalShow .24s cubic-bezier(.22,1,.36,1);
}
.pay-title {
  text-align: center;
  color: #1976d2;
  font-size: 1.34rem;
  font-weight: 900;
  margin-bottom: 8px;
  letter-spacing: -0.5px;
}
.pay-subtitle {
  color: #222;
  font-size: 1.06em;
  text-align: center;
  margin-bottom: 4px;
  margin-top: -8px;
  font-weight: 500;
}
.pay-btn-row {
  display: flex;
  gap: 12px;
  margin: 0 0 6px 0;
}
.btn-pay {
  flex: 1 1 0;
  border: none;
  font-weight: 700;
  font-size: 1.07em;
  border-radius: 8px;
  padding: 13px 0 12px 0;
  cursor: pointer;
  transition: background .17s, box-shadow .15s, transform .10s, color .13s;
  box-shadow: 0 2px 14px #1976d21c;
  letter-spacing: 0.2px;
  margin: 0;
}
.btn-pay-bank {
  background: linear-gradient(94deg, #43a047 60%, #20c997 100%);
  color: #fff;
}
.btn-pay-bank:hover, .btn-pay-bank:focus {
  background: linear-gradient(94deg, #20c997 10%, #43a047 80%);
  color: #fff;
  transform: translateY(-2px) scale(1.03);
  box-shadow: 0 4px 18px #43a04733;
}
.btn-pay-total {
  background: linear-gradient(94deg, #1976d2 60%, #1565c0 100%);
  color: #fff;
}
.btn-pay-total:hover, .btn-pay-total:focus {
  background: linear-gradient(94deg, #1565c0 20%, #1976d2 100%);
  color: #fff;
  transform: translateY(-2px) scale(1.03);
  box-shadow: 0 4px 18px #1976d233;
}
.btn-pay-cancel {
  margin-top: 2px;
  background: #f9fbfc;
  color: #1976d2;
  border: 1.5px solid #bfcad6;
  font-weight: 700;
  border-radius: 8px;
  padding: 12px 0 11px 0;
  font-size: 1.08em;
  transition: background 0.18s, color 0.18s, border 0.13s;
}
.btn-pay-cancel:hover, .btn-pay-cancel:focus {
  background: #e3ecfa;
  color: #1565c0;
  border-color: #1976d2;
}
@media (max-width: 540px) {
  .modern-pay-modal {
    padding: 14px 3vw 16px 3vw !important;
    max-width: 98vw;
    font-size: 0.98em;
  }
  .pay-title { font-size: 1.07rem;}
}
</style>

<script>
  // --- EDIÇÃO ---
let edicaoTipo = '', edicaoId = '';

// Abrir modal de edição preenchido
function editarFinanca(tipo, id) {
  fetch(`financas.php?action=get_${tipo}&id=${id}`)
    .then(resp => resp.json())
    .then(item => {
      edicaoTipo = tipo;
      edicaoId = id;
      document.getElementById("edit-descricao").value = item.descricao ?? '';

      // Valor: vazio se nulo, undefined, "" ou 0
      document.getElementById("edit-valor").value =
        item.valor && Number(item.valor) !== 0 ? item.valor : '';

      if (tipo === "plano") {
        document.getElementById("edit-prazo-group").style.display = "";
        // Prazo: vazio se nulo, undefined, "" ou 0
        document.getElementById("edit-prazo").value =
          item.prazo && Number(item.prazo) !== 0 ? item.prazo : '';
      } else {
        document.getElementById("edit-prazo-group").style.display = "none";
        document.getElementById("edit-prazo").value = '';
      }

      document.getElementById("modal-edicao-titulo").innerText = "Editar " + (
        tipo === "receita" ? "Receita" :
        tipo === "despesa" ? "Despesa" : "Plano"
      );
      document.getElementById("modal-edicao").style.display = "flex";
    });
}
function fecharModalEdicao() {
  document.getElementById("modal-edicao").style.display = "none";
}
document.getElementById("modal-edicao-form").onsubmit = function(e) {
  e.preventDefault();
  const descricao = document.getElementById("edit-descricao").value;
  const valor = document.getElementById("edit-valor").value;
  let prazo = '';
  if (edicaoTipo === "plano") prazo = document.getElementById("edit-prazo").value;
  const formData = new FormData();
  formData.append("action", "editar");
  formData.append("tipo", edicaoTipo);
  formData.append("id", edicaoId);
  formData.append("descricao", descricao);
  formData.append("valor", valor);
  if (edicaoTipo === "plano") formData.append("prazo", prazo);
  fetch("financas.php", { method: "POST", body: formData })
    .then(resp => resp.json())
    .then(res => {
      if (res.success) {
        fecharModalEdicao();
        carregarFinancas();
      } else {
        alert("Erro ao editar: " + (res.msg || ""));
      }
    });
};

// --- EXCLUIR ---
let excluirTipo = '', excluirId = '';
function excluirFinanca(tipo, id) {
  excluirTipo = tipo;
  excluirId = id;
  document.getElementById("modal-excluir").style.display = "flex";
}
function fecharModalExcluir() {
  document.getElementById("modal-excluir").style.display = "none";
}
function confirmarExcluirFinanca() {
  const formData = new FormData();
  formData.append("action", "excluir");
  formData.append("tipo", excluirTipo);
  formData.append("id", excluirId);
  fetch("financas.php", {
    method: "POST",
    body: formData
  })
  .then(resp => resp.json())
  .then(res => {
    if (res.success) {
      fecharModalExcluir();
      carregarFinancas();
    } else {
      alert("Erro ao excluir: " + (res.msg || ""));
    }
  });
}

// --- PAGAR / REALIZAR ---
let pagarTipo = '', pagarId = '';
function pagarDespesa(id) {
  pagarTipo = 'despesa';
  pagarId = id;
  document.getElementById("modal-pagar-titulo").innerText = "Como deseja pagar?";
  document.getElementById("modal-pagar").style.display = "flex";
}
function realizarPlano(id) {
  pagarTipo = 'plano';
  pagarId = id;
  document.getElementById("modal-pagar-titulo").innerText = "Como deseja realizar o plano?";
  document.getElementById("modal-pagar").style.display = "flex";
}
function fecharModalPagar() {
  document.getElementById("modal-pagar").style.display = "none";
}
function finalizarPagamento(origem) {
  // origem: 'banco' ou 'total'
  fetch(`financas.php?action=${pagarTipo==='despesa'?'pagar_despesa':'realizar_plano'}&id=${pagarId}&origem=${origem}`, { method: "POST" })
    .then(resp => resp.json())
    .then(res => {
      if (res.success) {
        fecharModalPagar();
        carregarFinancas();
      } else {
        alert("Erro ao concluir: " + (res.msg || ""));
      }
    });
}




// Adiciona itens nos containers com botões que abrem os modais corretos
function renderReceita(r) {
  const valor = parseFloat(r.valor);
const div = document.createElement("div");
div.className = "result-item receita";
div.innerHTML = `
  <div class="info">
    <i class="material-symbols-rounded icon" style="color:#28a745;">trending_up</i>
    Receita: ${r.descricao} - R$${valor.toFixed(2)}
  </div>
  <div class="action-buttons">
    <button class="btn btn-warning btn-sm" onclick="editarFinanca('receita', ${r.id})">Editar</button>
    <button class="btn btn-danger btn-sm" onclick="excluirFinanca('receita', ${r.id})">Excluir</button>
  </div>
`;
document.getElementById("receitas-container").appendChild(div);
}

function renderDespesa(d) {
  const valor = parseFloat(d.valor);
  let pago = d.pago == 1 ? 'text-decoration:line-through;' : '';
  const disabled = d.pago == 1 ? 'disabled title="Já paga"' : '';
const div = document.createElement("div");
div.className = "result-item despesa";
div.innerHTML = `
  <div class="info" style="${pago}">
    <i class="material-symbols-rounded icon" style="color:#dc3545;">trending_down</i>
    Despesa: ${d.descricao} - R$${valor.toFixed(2)}
  </div>
  <div class="action-buttons">
    <button class="btn btn-warning btn-sm" onclick="editarFinanca('despesa', ${d.id})" ${d.pago == 1 ? 'disabled title="Já paga"' : ''}>Editar</button>
    <button class="btn btn-danger btn-sm" onclick="excluirFinanca('despesa', ${d.id})">Excluir</button>
    <button class="btn btn-success btn-sm" onclick="pagarDespesa(${d.id})" ${d.pago == 1 ? 'disabled title="Já paga"' : ''}>Pagar</button>
  </div>
`;
document.getElementById("despesas-container").appendChild(div);
}

function renderPlano(p) {
  const valor = parseFloat(p.valor);
  let realizado = p.realizado == 1 ? 'text-decoration:line-through;' : '';
  const disabled = p.realizado == 1 ? 'disabled title="Já realizado"' : '';
const div = document.createElement("div");
div.className = "result-item plano";
div.innerHTML = `
  <div class="info" style="${realizado}">
    <i class="material-symbols-rounded icon" style="color:#007bff;">lightbulb</i>
    Plano: ${p.descricao} - R$${valor.toFixed(2)} - ${p.prazo} meses
  </div>
  <div class="action-buttons">
    <button class="btn btn-warning btn-sm" onclick="editarFinanca('plano', ${p.id})" ${p.realizado == 1 ? 'disabled title="Já realizado"' : ''}>Editar</button>
    <button class="btn btn-danger btn-sm" onclick="excluirFinanca('plano', ${p.id})">Excluir</button>
    <button class="btn btn-success btn-sm" onclick="realizarPlano(${p.id})" ${p.realizado == 1 ? 'disabled title="Já realizado"' : ''}>Realizar</button>
  </div>
`;
document.getElementById("planos-container").appendChild(div);
}
</script>


<style>

/* MODAL EDIÇÃO RECEITA - MODERNO E ORGANIZADO */

#modal-edicao .modal-box {
  background: #fff;
  border-radius: 18px;
  max-width: 360px;
  width: 96vw;
  padding: 32px 24px 24px 24px;
  box-shadow: 0 8px 36px #1976d244, 0 1.5px 8px #43a04723;
  display: flex;
  flex-direction: column;
  gap: 16px;
  align-items: stretch;
}
#modal-edicao h4 {
  text-align: center;
  color: #1976d2;
  font-size: 1.38rem;
  font-weight: 900;
  letter-spacing: -0.5px;
  margin-bottom: 16px;
}
#modal-edicao label {
  font-weight: 600;
  color: #25304e;
  font-size: 1em;
  margin-bottom: 6px;
  margin-top: 0;
}
#modal-edicao input[type="text"],
#modal-edicao input[type="number"] {
  background: #f6f8fb;
  border: 1.5px solid #d7e1f0;
  border-radius: 9px;
  padding: 10px 12px;
  font-size: 1.08em;
  color: #222;
  width: 100%;
  margin-bottom: 12px;
  outline: none;
  transition: border-color 0.18s, box-shadow 0.13s;
}
#modal-edicao input[type="text"]:focus,
#modal-edicao input[type="number"]:focus {
  border-color: #43a047;
  box-shadow: 0 0 0 2px #43a04722;
}
#modal-edicao .d-flex {
  display: flex;
  gap: 14px;
  align-items: center;
  margin-top: 0;
}
#modal-edicao .btn-success {
  background: linear-gradient(90deg, #43a047 60%, #20c997 100%);
  color: #fff;
  font-weight: 700;
  border: none;
  border-radius: 8px;
  box-shadow: 0 2px 14px #43a04718;
  padding: 11px 0 11px 0;
  font-size: 1.08em;
  width: 100%;
  transition: background 0.16s;
}
#modal-edicao .btn-success:hover, #modal-edicao .btn-success:focus {
  background: linear-gradient(90deg, #20c997 0%, #43a047 80%);
  color: #fff;
}
#modal-edicao .btn-outline-secondary {
  background: #f9fbfc;
  color: #1976d2;
  border: 1.5px solid #bfcad6;
  font-weight: 700;
  border-radius: 8px;
  padding: 11px 0 11px 0;
  font-size: 1.08em;
  width: 100%;
  transition: background 0.18s, color 0.18s, border 0.13s;
}
#modal-edicao .btn-outline-secondary:hover, #modal-edicao .btn-outline-secondary:focus {
  background: #e3ecfa;
  color: #1565c0;
  border-color: #1976d2;
}
@media (max-width: 540px) {
  #modal-edicao .modal-box {
    padding: 16px 5vw 16px 5vw;
    max-width: 99vw;
    font-size: 0.99em;
  }
  #modal-edicao h4 {
    font-size: 1.09rem;
  }
}



#submenu {
  position: fixed; z-index: 3100; left: 0; top: 0; width: 100vw; height: 100vh;
  background: rgba(17, 30, 50, 0.55);
  backdrop-filter: blur(5px);
  display: none; align-items: center; justify-content: center;
}

#submenu .submenu-content, #submenu .modal-box {
  background: rgba(255,255,255,0.92);
  border-radius: 18px;
  max-width: 350px; width: 94vw;
  box-shadow: 0 8px 36px #1976d244, 0 1.5px 8px #43a04723;
  display: flex; flex-direction: column; align-items: stretch;
  padding: 38px 24px 26px 24px;
  gap: 12px;
  position: relative;
  animation: modalShow .28s cubic-bezier(.22,1,.36,1);
}

@keyframes modalShow { from {opacity: 0; transform: translateY(30px) scale(.97);} to {opacity: 1; transform: translateY(0) scale(1);} }

#submenu .modal-icon {
  width: 56px; height: 56px;
  border-radius: 50%; box-shadow: 0 4px 24px #43a04733;
  display: flex; align-items: center; justify-content: center;
  margin: -36px auto 8px auto;
}
#submenu .modal-icon svg {
  display: block;
}

#submenu #submenu-title {
  text-align: center;
  font-size: 1.38rem;
  font-weight: 900;
  color: #1976d2;
  margin-bottom: 12px;
  letter-spacing: -0.5px;
}

.label-modern {
  font-weight: 600;
  color: #25304e;
  font-size: 1em;
  margin-top: 6px;
  margin-bottom: 2px;
}

.input-modern {
  background: #f6f8fb;
  border: 1.5px solid #d7e1f0;
  border-radius: 9px;
  padding: 11px 13px;
  font-size: 1.08em;
  color: #222;
  width: 100%;
  margin-bottom: 8px;
  outline: none;
  transition: border-color .18s, box-shadow .13s;
}
.input-modern:focus {
  border-color: #43a047;
  box-shadow: 0 0 0 2px #43a04722;
}

.submenu-actions {
  display: flex;
  gap: 14px;
  justify-content: center;
  margin-top: 10px;
}
.btn-modern {
  flex: 1 1 0;
  border: none;
  font-weight: 700;
  font-size: 1.07em;
  border-radius: 8px;
  padding: 12px 0 11px 0;
  cursor: pointer;
  transition: background .17s, box-shadow .15s, transform .12s;
  box-shadow: 0 2px 14px #1976d21c;
  letter-spacing: 0.2px;
}
.btn-green {
  background: linear-gradient(92deg, #43a047 60%, #20c997 100%);
  color: #fff;
}
.btn-green:hover {
  background: linear-gradient(92deg, #20c997 10%, #43a047 80%);
  box-shadow: 0 4px 18px #43a04733;
  transform: translateY(-1px) scale(1.03);
}
.btn-red {
  background: linear-gradient(92deg, #e53935 70%, #fb6340 100%);
  color: #fff;
}
.btn-red:hover {
  background: linear-gradient(92deg, #fb6340 10%, #e53935 80%);
  box-shadow: 0 4px 18px #e5393533;
  transform: translateY(-1px) scale(1.03);
}
</style>

<div id="submenu">
  <div class="modal-box submenu-content">
    <div id="submenu-title"></div>
    <form id="form-fields"></form>
    <div class="submenu-actions">
      <button type="submit" form="form-fields" class="btn-modern btn-green">Salvar</button>
      <button type="button" class="btn-modern btn-red" onclick="document.getElementById('submenu').style.display='none'">Fechar</button>
    </div>
  </div>
</div>
  </body>

</html>


