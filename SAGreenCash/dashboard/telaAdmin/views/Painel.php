<?php
// Inicia a sessão para manter o usuário logado entre as páginas
session_start();

// Verifica se existe um usuário autenticado na sessão.
// Caso não exista, redireciona para a tela de login e encerra o script atual.
// Isso garante que apenas usuários autenticados possam acessar esse painel.
if (!isset($_SESSION["usuario"])) {
  header("Location: ../login.php");
  exit;
}

// Importa o arquivo responsável pela conexão com o banco de dados MySQL.
// A variável $conn será usada para executar queries SQL.
require "db.php";

// ------------ INDICADORES DO PAINEL ------------

// 1. Busca o número total de usuários cadastrados no banco.
// - Executa uma consulta SQL que retorna o total de linhas na tabela 'usuarios'.
// - fetch_row()[0] pega o primeiro campo do resultado, que é o número total.
$totalUsuarios = $conn->query("SELECT COUNT(*) FROM usuarios")->fetch_row()[0];

// 2. Monta um array associativo $usuariosPorData, onde cada chave é uma data (ex: '2025-06-27')
//    e o valor é o total de usuários cadastrados naquela data.
// - Executa uma consulta SQL agrupando por data de cadastro.
// - Utilizado para gerar o gráfico de evolução de cadastros.
$usuariosPorData = [];
$result = $conn->query("SELECT DATE(dataCadastro) as dia, COUNT(*) as total FROM usuarios GROUP BY dia ORDER BY dia ASC");
while ($row = $result->fetch_assoc()) {
    $usuariosPorData[$row['dia']] = (int)$row['total'];
}

// 3. Conta o número total de chamados de suporte que já foram respondidos.
// - Considera respondido se existir ao menos um registro relacionado na tabela 'suporte_resposta'.
// - Utiliza subquery EXISTS para checar se há resposta para o chamado.
$totalRealizados = $conn->query("
    SELECT COUNT(*) FROM suporte 
    WHERE EXISTS (SELECT 1 FROM suporte_resposta sr WHERE sr.suporte_id = suporte.id)
")->fetch_row()[0];

// 4. Conta o número total de chamados de suporte que ainda não foram respondidos.
// - Considera não respondido se NÃO existe nenhum registro na tabela 'suporte_resposta' para aquele chamado.
$totalAndamento = $conn->query("
    SELECT COUNT(*) FROM suporte 
    WHERE NOT EXISTS (SELECT 1 FROM suporte_resposta sr WHERE sr.suporte_id = suporte.id)
")->fetch_row()[0];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GreenCash - Painel</title>
  <link rel="icon" type="image/png" href="../views/imgs/Login-Cadastro/loggoooo.png" />
  <link rel="stylesheet" href="../views/css/style.css">
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <div class="container">
    <div class="navigation">
      <ul>
        <li>
          <a href="#">
            <span class="icon">
              <img src="../views/imgs/pages/loggoooo.png" alt="Logo">
            </span>
            <span class="title">GreenCash</span>
          </a>
        </li>
        <li>
          <a href="#" class="active">
            <span class="icon">
              <ion-icon name="speedometer-outline"></ion-icon>
            </span>
            <span class="title">Dashboard</span>
          </a>
        </li>
        <li>
          <a href="usuarios.php">
            <span class="icon">
              <ion-icon name="people-outline"></ion-icon>
            </span>
            <span class="title">Usuarios</span>
          </a>
        </li>
        <li>
          <a href="suporte.php">
            <span class="icon">
              <ion-icon name="mail-outline"></ion-icon>
            </span>
            <span class="title">Suporte</span>
          </a>
        </li>
        <li>
          <a href="#" onclick="abrirModalLogout(); return false;">
            <span class="icon">
              <ion-icon name="log-out-outline"></ion-icon>
            </span>
            <span class="title">Sair</span>
          </a>
        </li>
      </ul>
    </div>

    <div class="main">
      <div class="topbar">
        <div class="toggle">
          <ion-icon name="menu-outline"></ion-icon>
        </div>
      </div>

      <p class="mb-4" style="font-size:1.1em; font-weight:500; margin: 16px 0 0 18px;">
  Olá,
  <?php
    if (isset($_SESSION["usuario"]) && is_array($_SESSION["usuario"])) {
      echo htmlspecialchars($_SESSION["usuario"]["nome"]) . " / " . htmlspecialchars($_SESSION["usuario"]["email"]);
    } elseif (isset($_SESSION["usuario"]) && is_string($_SESSION["usuario"])) {
      echo htmlspecialchars($_SESSION["usuario"]);
    } else {
      echo "Usuário";
    }
  ?>
</p>

      <div class="cardBox">
        <div class="card">
          <div>
            <div class="numbers"><p id="totalUsuariosDisplay"><?php echo $totalUsuarios; ?></p></div>
            <div class="cardName">Total de Usuários</div>
          </div>
          <div class="iconBx">
            <ion-icon name="person-outline"></ion-icon>
          </div>
        </div>

        <div class="card">
          <div>
            <div class="numbers"><p id="totalAtendimentosRealizados"><?php echo $totalRealizados; ?></p></div>
            <div class="cardName">Total de Suportes Realizados</div>
          </div>
          <div class="iconBx">
            <ion-icon name="construct-outline"></ion-icon>
          </div>
        </div>

        <div class="card">
          <div>
            <div class="numbers"><p id="totalAtendimentosAndamento"><?php echo $totalAndamento; ?></p></div>
            <div class="cardName">Total de Suportes em Andamento</div>
          </div>
          <div class="iconBx">
            <ion-icon name="construct-outline"></ion-icon>
          </div>
        </div>
      </div>

      <div class="grafico-container" style="display:flex;gap:32px;justify-content:center;margin:32px 0;flex-wrap:wrap;">
        <!-- Gráfico de Usuários (linha elegante) -->
        <div class="grafico grafico-linha" style="background:#fff;border-radius:12px;padding:18px 28px 16px 28px;box-shadow:0 2px 12px #b6e2c739;min-width:360px;">
          <h2 style="font-size:1.1em;">Usuários Cadastrados por Data</h2>
          <canvas id="grafico-usuarios-data" width="400" height="200"></canvas>
        </div>
        <!-- Gráfico de Suporte -->
        <div class="grafico grafico-rosca" style="background:#fff;border-radius:12px;padding:18px 28px 16px 28px;box-shadow:0 2px 12px #b6e2c739;min-width:360px;">
          <h2 style="font-size:1.1em;">Suporte Realizados x Em Andamento</h2>
          <canvas id="grafico-atendimentos" width="400" height="200"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal de confirmação de logout -->
  <div id="logoutModal" class="modal-logout-overlay" style="display:none;">
    <div class="modal-logout-content">
      <h3>Deseja realmente sair da sua conta?</h3>
      <div style="margin-top:18px; display:flex; gap:12px; justify-content:center;">
        <button onclick="confirmarLogout()" class="btn-logout-confirmar">Sim, sair</button>
        <button onclick="fecharModalLogout()" class="btn-logout-cancelar">Cancelar</button>
      </div>
    </div>
  </div>

  <style>
    .modal-logout-overlay {
      position: fixed; left:0; top:0; width:100vw; height:100vh;
      background: rgba(0,0,0,0.20);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 9999;
    }
    .modal-logout-content {
      background: #fff;
      padding: 28px 32px;
      border-radius: 12px;
      box-shadow: 0 4px 24px #0002;
      text-align: center;
      min-width: 240px;
    }
    .btn-logout-confirmar {
      background: #16d463; color: #fff; border: none; border-radius: 7px; font-weight: 600; padding: 8px 24px; cursor: pointer;
    }
    .btn-logout-confirmar:hover { background: #0fb96d; }
    .btn-logout-cancelar {
      background: #e9e9e9; color: #444; border: none; border-radius: 7px; font-weight: 500; padding: 8px 24px; cursor: pointer;
    }
    .btn-logout-cancelar:hover { background: #bbb; }
  </style>
  <script>
// Seleciona os elementos do DOM para o menu lateral e o conteúdo principal
const toggle = document.querySelector('.toggle');
const navigation = document.querySelector('.navigation');
const main = document.querySelector('.main');

// Função para alternar a classe 'active' no menu lateral e no conteúdo principal
// Isso permite que o menu seja "escondido" ou "mostrado" em telas menores (responsividade)
toggle.onclick = function () {
  navigation.classList.toggle('active');
  main.classList.toggle('active');
};

// Função para exibir o modal de confirmação de logout
function abrirModalLogout() {
  document.getElementById('logoutModal').style.display = 'flex';
}
// Função para fechar o modal de logout
function fecharModalLogout() {
  document.getElementById('logoutModal').style.display = 'none';
}
// Função para efetivar o logout, redirecionando para o script de logout do backend
function confirmarLogout() {
  window.location.href = '../../../dashboard/pages/logout.php';
}

// ------------ GRÁFICOS (Chart.js) ------------

// Recebe do PHP os dados em formato JSON para o gráfico de evolução de usuários
const usuariosPorData = <?php echo json_encode($usuariosPorData); ?>;
// Recebe do PHP os totais de chamados realizados e em andamento
const totalRealizados = <?php echo (int)$totalRealizados; ?>;
const totalAndamento = <?php echo (int)$totalAndamento; ?>;

// GRÁFICO DE LINHA - Evolução dos cadastros de usuários
(function() {
  // Extrai as datas e os valores do array associativo
  const datas = Object.keys(usuariosPorData);
  const valores = datas.map(data => usuariosPorData[data]);
  // Seleciona o canvas do gráfico
  const ctx = document.getElementById('grafico-usuarios-data').getContext('2d');
  // Cria o gráfico do tipo 'line' com as datas no eixo X e os totais no eixo Y
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: datas,
      datasets: [{
        label: 'Usuários Cadastrados',
        data: valores,
        borderColor: '#4caf50',
        backgroundColor: 'rgba(76,175,80,0.12)',
        borderWidth: 2,
        pointRadius: 4,
        pointBackgroundColor: '#4caf50',
        fill: true,
        tension: 0.35 // Deixa a linha do gráfico mais suave (curva)
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true } }
    }
  });
})();

// GRÁFICO DE ROSCA - Proporção de chamados realizados x em andamento
(function() {
  const ctx = document.getElementById('grafico-atendimentos').getContext('2d');
  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Realizados', 'Em Andamento'],
      datasets: [{
        label: 'Atendimentos',
        data: [totalRealizados, totalAndamento],
        backgroundColor: ['#1E88E5', '#8E24AA'],
        hoverOffset: 4
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'top' },
        tooltip: {
          callbacks: {
            // Exibe no tooltip o valor absoluto e a porcentagem de cada fatia do gráfico
            label: function (context) {
              const total = totalRealizados + totalAndamento;
              const value = context.raw;
              const pct = total > 0 ? (value / total * 100).toFixed(1) : 0;
              return context.label + ': ' + value + ' (' + pct + '%)';
            }
          }
        }
      }
    }
  });
})();
  </script>
</body>
</html>