<?php
session_start();
if (!isset($_SESSION["usuario"])) {
  header("Location: ../login.php");
  exit;
}
require "db.php";

$msgOk = $msgErro = "";

// Defina a variável pesquisa SEMPRE antes de usar!
$pesquisa = $_GET['pesquisa'] ?? '';

// Limpar histórico (marca como limpado, não apaga)
if (isset($_POST['limpar_historico'])) {
    $conn->query("UPDATE suporte SET status='limpo' WHERE id IN (SELECT suporte_id FROM suporte_resposta)");
    registrar_log($conn, $_SESSION['usuario']['id'], "Limpou histórico de chamados concluídos");
}

// Limpar log de atividades (deleta tudo)
if (isset($_GET['limpar_log'])) {
    $conn->query("DELETE FROM log_atividades");
    $msgOk = "Log de atividades limpo!";
}


// Atualiza prioridade de chamado aberto
if (isset($_POST['mudar_prioridade']) && isset($_POST['prioridade_edit'], $_POST['chamado_id'])) {
    $prioridade = $_POST['prioridade_edit'];
    $chamado_id = (int)$_POST['chamado_id'];
    if (in_array($prioridade, ["Alta","Média","Baixa"])) {
        $conn->query("UPDATE suporte SET prioridade='$prioridade' WHERE id=$chamado_id");
        registrar_log($conn, $_SESSION['usuario']['id'], "Alterou prioridade do chamado $chamado_id para $prioridade");
    }
}

// Responder chamado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['responder_id'], $_POST['mensagem'])) {
    $suporte_id = intval($_POST['responder_id']);
    $admin_id = $_SESSION['usuario']['id'];
    $mensagem = trim($_POST['mensagem']);
    $observacao = trim($_POST['observacao'] ?? '');
    $prioridade = $_POST['prioridade'] ?? '';
    if ($mensagem && in_array($prioridade, ["Alta","Média","Baixa"])) {
        $stmt = $conn->prepare("INSERT INTO suporte_resposta (suporte_id, admin_id, mensagem, observacao, prioridade) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $suporte_id, $admin_id, $mensagem, $observacao, $prioridade);
        $stmt->execute();
        $stmt->close();
        $conn->query("UPDATE suporte SET prioridade='$prioridade' WHERE id=$suporte_id");
        registrar_log($conn, $admin_id, "Respondeu ao chamado $suporte_id (prioridade $prioridade)");
        $msgOk = "Resposta enviada!";
    } else {
        $msgErro = "Preencha todos os campos obrigatórios e selecione a prioridade.";
    }
}

// ===============================
// FILTRO DE PESQUISA NA QUERY SQL
// ===============================

// Chamados abertos
$sqlAbertos = "SELECT s.*, u.nome as nome_usuario, u.email as email_usuario
        FROM suporte s
        JOIN usuarios u ON s.usuario_id = u.id
        WHERE NOT EXISTS (SELECT 1 FROM suporte_resposta sr WHERE sr.suporte_id = s.id)
          AND (s.status IS NULL OR s.status != 'limpo')";
if (!empty($pesquisa)) {
    $pesq = $conn->real_escape_string($pesquisa);
    $sqlAbertos .= " AND (s.titulo LIKE '%$pesq%' OR s.descricao LIKE '%$pesq%' OR u.nome LIKE '%$pesq%' OR u.email LIKE '%$pesq%')";
}
$sqlAbertos .= " ORDER BY FIELD(s.prioridade, 'Alta', 'Média', 'Baixa'), s.data_hora DESC";
$resAbertos = $conn->query($sqlAbertos);
$chamadosAbertos = $resAbertos->fetch_all(MYSQLI_ASSOC);

// Chamados concluídos
$sqlConcluidos = "SELECT s.*, u.nome as nome_usuario, u.email as email_usuario
        FROM suporte s
        JOIN usuarios u ON s.usuario_id = u.id
        WHERE EXISTS (SELECT 1 FROM suporte_resposta sr WHERE sr.suporte_id = s.id)
          AND (s.status IS NULL OR s.status != 'limpo')";
if (!empty($pesquisa)) {
    $pesq = $conn->real_escape_string($pesquisa);
    $sqlConcluidos .= " AND (s.titulo LIKE '%$pesq%' OR s.descricao LIKE '%$pesq%' OR u.nome LIKE '%$pesq%' OR u.email LIKE '%$pesq%')";
}
$sqlConcluidos .= " ORDER BY s.data_hora DESC";
$resConcluidos = $conn->query($sqlConcluidos);
$chamadosConcluidos = $resConcluidos->fetch_all(MYSQLI_ASSOC);

function buscarResposta($conn, $suporte_id) {
    $r = $conn->query("SELECT sr.*, a.nome as admin_nome FROM suporte_resposta sr LEFT JOIN adm a ON sr.admin_id = a.id WHERE suporte_id = $suporte_id ORDER BY sr.data DESC LIMIT 1");
    return $r->fetch_assoc();
}

function registrar_log($conn, $admin_id, $acao) {
    $stmt = $conn->prepare("INSERT INTO log_atividades (admin_id, acao) VALUES (?, ?)");
    $stmt->bind_param("is", $admin_id, $acao);
    $stmt->execute();
    $stmt->close();
}

$logs = $conn->query("SELECT l.*, a.nome as admin_nome FROM log_atividades l LEFT JOIN adm a ON l.admin_id = a.id ORDER BY l.data_hora DESC")->fetch_all(MYSQLI_ASSOC);

$kpiAbertos = count($chamadosAbertos);
$kpiConcluidos = count($chamadosConcluidos);
$kpiHoje = $conn->query("SELECT COUNT(*) FROM suporte WHERE DATE(data_hora) = CURDATE()")->fetch_row()[0];
$kpiAlta = $conn->query("
    SELECT COUNT(*) FROM suporte 
    WHERE (prioridade='Alta')
      AND NOT EXISTS (SELECT 1 FROM suporte_resposta sr WHERE sr.suporte_id = suporte.id)
      AND (status IS NULL OR status != 'limpo')
")->fetch_row()[0];

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>GreenCash - Gerenciamento de Chamados</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/adicionais.css">
    <link rel="stylesheet" href="css/botoes-acoes.css">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
    <style>
        .close-modal-x {
  position: absolute;
  top: 6px;
  right: 14px;
  background: none;
  border: none;
  font-size: 2em;
  color: #555;
  cursor: pointer;
  z-index: 10;
  transition: color 0.2s;
  line-height: 1;
  font-weight: bold;
}
.close-modal-x:hover {
  color: #f44336;
}
.btn1 {
    background-color: #4CAF50;
    color: #222; /* cor escura no estado normal */
    border: none;
    padding: 10px 20px;
    border-radius: 10px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s ease, color 0.3s ease, transform 0.2s ease;
    box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
    text-align: center;
    text-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    letter-spacing: 0.5px;
}

.btn1:hover {
    background-color: #43A047;
    color: white; /* letra branca só no hover */
    transform: scale(1.05);
    box-shadow: 0 6px 16px rgba(67, 160, 71, 0.5);
}

.btn1:active {
    background-color: #388E3C;
    transform: scale(0.97);
    box-shadow: 0 2px 8px rgba(56, 142, 60, 0.4);
}

/* Toast Notification */
.toast {
  visibility: hidden;
  min-width: 220px;
  margin-left: -110px;
  background-color: #4caf50;
  color: #fff;
  text-align: center;
  border-radius: 8px;
  padding: 16px;
  position: fixed;
  z-index: 9999;
  left: 50%;
  bottom: 30px;
  font-size: 1.1em;
  box-shadow: 0 4px 16px #4caf5030;
  opacity: 0;
  transition: opacity 0.4s, visibility 0.4s;
}
.toast.show {
  visibility: visible;
  opacity: 1;
}

/* ============================= */
/* ======= KPI CARD AREA ======= */
/* ============================= */

.kpi-bar,
.kpi-row {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    flex-wrap: wrap;
    gap: 18px;
    align-items: stretch;
    padding: 20px 16px;
    margin-bottom: 30px;
}

.kpi {
    background: #3a3a3a;
    color: #ffffff;
    border-radius: 16px;
    box-shadow: 
        0 1px 3px rgba(0, 0, 0, 0.12),
        0 1px 2px rgba(0, 0, 0, 0.08);
    padding: 20px 22px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
    flex: 1 1 auto;
    min-width: 200px;
    max-width: 280px;
    min-height: 120px;
    position: relative;
    border: 1px solid #4a4a4a;
    overflow: hidden;
}

.kpi::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: #22c55e;
    opacity: 1;
}

.kpi-label {
    font-size: 0.95rem;
    font-weight: 500;
    color: #d1d5db;
    line-height: 1.3;
    letter-spacing: 0.005em;
    text-transform: uppercase;
    font-size: 0.875rem;
    margin: 0;
    margin-bottom: 8px;
}

.kpi-value {
    font-size: 2.25rem;
    font-weight: 700;
    line-height: 1;
    letter-spacing: -0.025em;
    color: #ffffff;
    margin: 0;
}

/* Variações específicas para diferentes tipos de KPI */
.kpi.primary::before {
    background: #3b82f6;
}

.kpi.success::before {
    background: #10b981;
}

.kpi.warning::before {
    background: #f59e0b;
}

.kpi.danger::before {
    background: #ef4444;
}

.kpi.info::before {
    background: #06b6d4;
}

/* Responsividade aprimorada */
@media (max-width: 768px) {
    .kpi-bar,
    .kpi-row {
        flex-direction: column;
        padding: 24px 16px;
        gap: 16px;
        margin-bottom: 32px;
    }
    
    .kpi {
        min-width: unset;
        padding: 24px;
        min-height: 100px;
    }
    
    .kpi-value {
        font-size: 2rem;
    }
    
    .kpi-label {
        font-size: 0.8rem;
    }
}

@media (max-width: 480px) {
    .kpi-bar,
    .kpi-row {
        padding: 20px 16px;
        gap: 12px;
    }
    
    .kpi {
        padding: 20px;
        min-height: 90px;
    }
    
    .kpi-value {
        font-size: 1.875rem;
    }
    
    .kpi-label {
        font-size: 0.75rem;
    }
}

/* Estilos para sistema administrativo profissional */
.activity-log {
  background: #383735;
  border-radius: 10px;
  padding: 16px;
  margin: 30px auto 0 auto;    /* Aumenta o espaçamento do topo para baixar (ajuste a seu gosto) */
  box-shadow: 0 2px 12px #1a1e24a0;
  font-size: 0.97em;
  max-height: 150px;
  overflow-y: auto;
  color: #fff;
  width: 100%;
  max-width: 480px;            /* Limita a largura para centralizar */
  display: block;
  position: relative;          /* Normal, sem absoluto */
}
.activity-log h3 {
  font-size: 1.3em;
  font-weight: bold;
}
.activity-log ul {
  padding-left: 18px;
}
.activity-log ul li {
  margin-bottom: 7px;
  line-height: 1.6;
}
/* Ajuste para o botão Limpar Log dentro do .activity-log */
.activity-log .btn1 {
    padding: 6px 16px;
    font-size: 14px;
    border-radius: 7px;
    margin-left: 16px;
    float: right;
    margin-top: -4px;
}
/* Modal Box */
.modal {
  background: #F8F8F8;
  border-radius: 18px;
  box-shadow: 0 8px 40px #0004;
  max-width: 420px;
  width: 95%;
  padding: 30px 28px 20px 28px;
  position: relative;
  font-family: 'Segoe UI', 'Arial', sans-serif;
  color: #232323;
  animation: slideModal .2s;
}

/* Título */
.modal h2 {
  margin-top: 0;
  margin-bottom: 16px;
  font-size: 1.6em;
  font-weight: bold;
  letter-spacing: 0.5px;
}

/* Fechar (X) */
.close-modal-x {
  position: absolute;
  top: 13px;
  right: 18px;
  background: none;
  border: none;
  font-size: 2em;
  color: #666;
  cursor: pointer;
  z-index: 10;
  transition: color 0.2s;
  line-height: 1;
  font-weight: bold;
}
.close-modal-x:hover {
  color: #e53935;
}

/* Infos do chamado */
.modal-responder-info {
  margin-bottom: 14px;
  font-size: 1.05em;
}
.modal-responder-info b {
  font-weight: 600;
}

/* Inputs */
.modal select,
.modal textarea,
.modal input[type=text] {
  border-radius: 8px;
  border: 1.5px solid #CCC;
  font-size: 1em;
  padding: 6px 10px;
  margin-bottom: 6px;
}

.modal textarea {
  width: 100%;
  min-height: 90px;
  resize: vertical;
  font-family: inherit;
  margin-bottom: 14px;
}

.modal input[type=text] {
  width: 100%;
  margin-bottom: 14px;
}

/* Label */
.modal label {
  display: inline-block;
  margin-bottom: 4px;
  font-weight: 500;
}

/* Priorização + resposta em linha */
.modal-row {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 6px;
}
@media (max-width:480px) {
  .modal-row {
    flex-direction: column;
    align-items: stretch;
    gap: 0;
  }
}

/* Botões */
.modal .actions {
  display: flex;
  gap: 18px;
  margin-top: 10px;
  justify-content: flex-start;
}
.btn-modal {
  border: none;
  border-radius: 10px;
  font-size: 1.15em;
  font-weight: bold;
  padding: 10px 28px;
  cursor: pointer;
  transition: background 0.18s, color 0.18s, filter 0.12s;
  box-shadow: 0 4px 14px #0001;
  letter-spacing: 0.5px;
}
.btn-modal.confirm {
  background: #36b14a;
  color: #fff;
}
.btn-modal.confirm:hover {
  background: #259336;
}
.btn-modal.cancel {
  background: #bdbdbd;
  color: #222;
}
.btn-modal.cancel:hover {
  background: #9e9e9e;
}

/* Animação */
@keyframes slideModal {
  from { opacity: 0; transform: translateY(-22px);}
  to   { opacity: 1; transform: translateY(0);}
}
    </style>
</head>
<body>
  
    <div class="container">
        <!-- Menu lateral: apenas UMA VEZ -->
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
                    <a href="Painel.php" class="active">
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
        <!-- Conteúdo principal -->
        <div class="main">
            <div class="topbar">
                <div class="toggle">
                    <ion-icon name="menu-outline"></ion-icon>
                </div>
            </div>
            <!-- KPIs centralizados -->
            <div class="kpi-bar">
                <div class="kpi">
                    <span class="kpi-label">Chamados Hoje</span>
                    <span class="kpi-value"><?= $kpiHoje ?></span>
                </div>
                <div class="kpi">
                    <span class="kpi-label">Chamados Abertos</span>
                    <span class="kpi-value"><?= $kpiAbertos ?></span>
                </div>
                <div class="kpi">
                    <span class="kpi-label">Chamados Concluídos</span>
                    <span class="kpi-value"><?= $kpiConcluidos ?></span>
                </div>
                <div class="kpi">
                    <span class="kpi-label">Alta Prioridade</span>
                    <span class="kpi-value"><?= $kpiAlta ?></span>
                </div>
            </div>
            <!-- Painel superior: log + pesquisa -->
            <div class="superior-panel" style="width:100%;max-width:1100px;margin:0 auto 24px auto;display:flex;flex-direction:column;gap:16px;">
                <div class="activity-log">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <h3 style="margin:0">Log de Atividades</h3>
                        <button onclick="location.href='?limpar_log=1'" class="btn1">Limpar Log</button>
                    </div>
                    <ul>
                        <?php if (!empty($logs)): ?>
                            <?php foreach ($logs as $log): ?>
                                <li><?= htmlspecialchars($log['acao']) ?>
                                    <small>
                                        - <?= htmlspecialchars($log['admin_nome'] ?? 'Admin') ?>
                                        em <?= date('d/m/Y H:i', strtotime($log['data_hora'])) ?>
                                    </small>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>Nenhuma atividade registrada.</li>
                        <?php endif; ?>
                    </ul>
                </div>
                <form method="get" style="text-align:center;">
                    <input type="text" name="pesquisa" placeholder="Pesquisar chamado ou usuário..." value="<?= htmlspecialchars($pesquisa) ?>" style="padding:8px;border-radius:6px;border:1px solid #ccc;">
                    <button type="submit" class="btn1">Buscar</button>
                </form>
            </div>
            <div class="details">
                <!-- Chamados Abertos -->
                <div class="recentOrders">
                    <div class="cardHeader">
                        <h2>Chamados Abertos</h2>
                        <?php if ($msgOk) echo "<div class='msg-ok'>$msgOk</div>"; ?>
                        <?php if ($msgErro) echo "<div class='msg-erro'>$msgErro</div>"; ?>
                    </div>
                    <table id="chamados-abertos">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Usuário</th>
                                <th>Descrição</th>
                                <th>Data</th>
                                <th>Prioridade</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($chamadosAbertos as $c): ?>
                            <tr>
                                <td><?= htmlspecialchars($c['titulo']) ?></td>
                                <td><?= htmlspecialchars($c['nome_usuario']) ?></td>
                                <td><?= htmlspecialchars($c['descricao']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($c['data_hora'])) ?></td>
                                <td>
                                    <span class="prioridade-<?= strtolower($c['prioridade']) ?>">
                                        <?= htmlspecialchars($c['prioridade']) ?>
                                    </span>
                                    <form class="edit-prio-form" method="post" style="margin-top:4px;">
                                        <input type="hidden" name="chamado_id" value="<?= $c['id'] ?>">
                                        <select name="prioridade_edit" style="font-size:12px;padding:2px 6px;">
                                            <option value="Alta" <?= $c['prioridade']=='Alta'?'selected':'' ?>>Alta</option>
                                            <option value="Média" <?= $c['prioridade']=='Média'?'selected':'' ?>>Média</option>
                                            <option value="Baixa" <?= $c['prioridade']=='Baixa'?'selected':'' ?>>Baixa</option>
                                        </select>
                                        <button type="submit" name="mudar_prioridade" class="btn1" style="padding:3px 8px;font-size:12px;">Alterar</button>
                                    </form>
                                </td>
                                <td>
                                    <button class="btn1" onclick="abrirModalResponder(<?= $c['id'] ?>, '<?= htmlspecialchars(addslashes($c['titulo'])) ?>', '<?= htmlspecialchars(addslashes($c['descricao'])) ?>', '<?= htmlspecialchars(addslashes($c['nome_usuario'])) ?>', '<?= $c['prioridade'] ?>')">Responder</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Chamados Concluídos -->
                <div class="recentOrders">
                    <div class="cardHeader" style="display:flex; justify-content:space-between;">
                        <h2>Chamados Concluídos</h2>
                        <form method="post" style="margin:0;">
                            <button type="submit" name="limpar_historico" class="btn1">Limpar Histórico</button>
                        </form>
                        <button class="btn1" onclick="exportarPDF()">Exportar PDF</button>
                    </div>
                    <table id="chamados-concluidos">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Usuário</th>
                                <th>Descrição</th>
                                <th>Data</th>
                                <th>Prioridade</th>
                                <th>Resposta</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($chamadosConcluidos as $c): 
                            $resposta = buscarResposta($conn, $c['id']);
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($c['titulo']) ?></td>
                                <td><?= htmlspecialchars($c['nome_usuario']) ?></td>
                                <td><?= htmlspecialchars($c['descricao']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($c['data_hora'])) ?></td>
                                <td>
                                    <span class="prioridade-<?= strtolower($resposta['prioridade'] ?? $c['prioridade']) ?>">
                                        <?= htmlspecialchars($resposta['prioridade'] ?? $c['prioridade']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="white-space:pre-line; color: #388e3c">
                                        <?= htmlspecialchars($resposta['mensagem'] ?? '') ?>
                                        <?php if (!empty($resposta['observacao'])): ?>
                                            <br><b>Obs:</b> <?= htmlspecialchars($resposta['observacao']) ?>
                                        <?php endif; ?>
                                        <br><small><i>Por <?= htmlspecialchars($resposta['admin_nome'] ?? 'Admin') ?> em <?= date('d/m/Y H:i', strtotime($resposta['data'])) ?></i></small>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<!-- HTML da Modal -->
<div class="modal-overlay" id="modalResponder">
  <div class="modal">
    <button class="close-modal-x" onclick="fecharModalResponder()" title="Fechar">&times;</button>
    <h2>Responder Chamado</h2>
    <form method="post">
      <input type="hidden" id="responder_id" name="responder_id" value="">
      <div><b>Título:</b> <span id="responder_titulo"></span></div>
      <div><b>Usuário:</b> <span id="responder_usuario"></span></div>
      <div style="margin-bottom:7px;"><b>Descrição:</b> <span id="responder_descricao"></span></div>
      <label for="responder_prioridade">Prioridade:</label>
<select id="responder_prioridade" name="prioridade" required style="width:100%;margin-bottom:10px; border-radius:8px;">
    <option value="">Selecione</option>
    <option value="Alta">Alta</option>
    <option value="Média">Média</option>
    <option value="Baixa">Baixa</option>
</select>
      <label>Resposta ao usuário:</label>
      <textarea name="mensagem" id="responder_mensagem" required style="width:100%; min-height:100px; border-radius:8px; margin-bottom:10px;"></textarea>
      <label>Observação (opcional):</label>
      <input type="text" name="observacao" maxlength="255" style="width:100%; border-radius:8px;" placeholder="Observação ao usuário">
      <div class="actions">
        <button type="submit" class="btn1">Enviar resposta</button>
        <button type="button" class="btn1 btn-cancel" onclick="fecharModalResponder()">Cancelar</button>
      </div>
    </form>
  </div>
</div>
    
<script>
function abrirModalResponder(id, titulo, descricao, usuario, prioridade){
  const modal = document.getElementById('modalResponder');
  modal.classList.add('active');
  document.getElementById('responder_id').value = id;
  document.getElementById('responder_titulo').textContent = titulo;
  document.getElementById('responder_descricao').textContent = descricao;
  document.getElementById('responder_usuario').textContent = usuario;
  document.getElementById('responder_prioridade').value = prioridade; // Preenche o select!
  setTimeout(function(){ document.getElementById('responder_mensagem').focus(); }, 100);
}
function fecharModalResponder() {
  document.getElementById('modalResponder').classList.remove('active');
}
// Fecha ao apertar ESC
document.addEventListener('keydown', function(e) {
  if(e.key === "Escape") fecharModalResponder();
});
// Fecha ao clicar fora da modal
document.getElementById('modalResponder').addEventListener('mousedown', function(e) {
  if(e.target === this) fecharModalResponder();
});
function exportarPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({
        orientation: 'portrait',
        unit: 'mm',
        format: 'a4'
    });

    // Paleta GreenCash
    const greenMain = [34, 139, 34];
    const greenLight = [219, 245, 219];
    const greenSoft = [240, 250, 240];
    const greyText = [80, 80, 80];
    const greyMid = [130, 130, 130];
    const white = [255, 255, 255];

    const pageWidth = doc.internal.pageSize.getWidth();
    const pageHeight = doc.internal.pageSize.getHeight();
    const usuario = "<?= htmlspecialchars($_SESSION['usuario']['nome'] ?? 'Administrador') ?>";
    const dataAtual = new Date();
    const dataFormatada = dataAtual.toLocaleDateString() + " " + dataAtual.toLocaleTimeString().slice(0, 5);

    // --- BANNER SUPERIOR ---
    doc.setFillColor(...greenMain);
    doc.rect(0, 0, pageWidth, 22, 'F');

    doc.setFont('helvetica', 'bold');
    doc.setFontSize(22);
    doc.setTextColor(...white);
    doc.text("GreenCash", 12, 15);

    doc.setFont('helvetica', 'italic');
    doc.setFontSize(11);
    doc.setTextColor(220, 255, 220);
    doc.text("Tecnologia e Eficiência para o seu financeiro", pageWidth - 12, 15, { align: "right" });

    // --- BLOCO DE INFORMAÇÕES DO RELATÓRIO ---
    const lateralMargin = 10;
    const infoBlockTop = 27;
    const infoBlockHeight = 16;

    doc.setFillColor(...greenLight);
    doc.roundedRect(
        lateralMargin,                        // x
        infoBlockTop,                         // y
        pageWidth - 2 * lateralMargin,        // width
        infoBlockHeight,                      // height
        2.5, 2.5, 'F'
    );

    doc.setFont('helvetica', 'bold');
    doc.setFontSize(14);
    doc.setTextColor(...greenMain);
    doc.text(
        "Relatório de Chamados Concluídos",
        pageWidth / 2,
        infoBlockTop + 6.5,
        { align: "center" }
    );

    doc.setFont('helvetica', 'normal');
    doc.setFontSize(10.5);
    doc.setTextColor(...greyText);
    doc.text(`Emitido por: ${usuario}`, lateralMargin + 2, infoBlockTop + infoBlockHeight - 2);
    doc.text(`Data: ${dataFormatada}`, pageWidth - lateralMargin - 2, infoBlockTop + infoBlockHeight - 2, { align: "right" });

    // --- TABELA DE CHAMADOS ---
    doc.autoTable({
        head: [[
            'Título', 'Usuário', 'Descrição', 'Data', 'Prioridade', 'Resposta'
        ]],
        body: [
            <?php foreach ($chamadosConcluidos as $c): 
                $resposta = buscarResposta($conn, $c['id']); ?>
                [
                    "<?= addslashes($c['titulo']) ?>",
                    "<?= addslashes($c['nome_usuario']) ?>",
                    "<?= addslashes($c['descricao']) ?>",
                    "<?= date('d/m/Y H:i', strtotime($c['data_hora'])) ?>",
                    "<?= addslashes($resposta['prioridade'] ?? $c['prioridade']) ?>",
                    "<?= addslashes($resposta['mensagem'] ?? '') ?>"
                ],
            <?php endforeach; ?>
        ],
        startY: infoBlockTop + infoBlockHeight + 6,
        margin: { left: lateralMargin, right: lateralMargin },
        tableWidth: 'auto',
        styles: {
            font: 'helvetica',
            fontSize: 10,
            cellPadding: 2.5,
            valign: 'middle',
            halign: 'center',
            textColor: [40, 40, 40],
            lineColor: [210, 210, 210],
            lineWidth: 0.3,
            overflow: 'linebreak',
        },
        headStyles: {
            fillColor: greenMain,
            textColor: white,
            fontStyle: 'bold',
            fontSize: 11,
            halign: 'center',
            cellPadding: 3.2,
            lineWidth: 0,
        },
        alternateRowStyles: {
            fillColor: greenSoft
        },
        rowStyles: {
            fillColor: white
        },
        columnStyles: {
            0: { cellWidth: 26 },
            1: { cellWidth: 28 },
            2: { cellWidth: 48, halign: 'left' },
            3: { cellWidth: 28 },
            4: { cellWidth: 22 },
            5: { cellWidth: 38, halign: 'left' }
        },
        didDrawPage: function (data) {
            // --- RODAPÉ ---
            const pageCount = doc.internal.getNumberOfPages();

            // Linha separadora
            doc.setDrawColor(...greenMain);
            doc.setLineWidth(0.7);
            doc.line(10, pageHeight-15, pageWidth-10, pageHeight-15);

            // Slogan GreenCash
            doc.setFont('helvetica', 'bolditalic');
            doc.setFontSize(10);
            doc.setTextColor(...greenMain);
            doc.text("GreenCash • Simplificando sua gestão financeira", 12, pageHeight-9);

            // Observação confidencialidade
            doc.setFont('helvetica', 'normal');
            doc.setFontSize(8);
            doc.setTextColor(...greyMid);
            doc.text("Documento confidencial. Proibida a reprodução parcial ou total sem autorização.", pageWidth/2, pageHeight-4, { align: "center" });

            // Paginação dinâmica
            doc.setFont('helvetica', 'italic');
            doc.setFontSize(9);
            doc.setTextColor(160, 160, 160);
            doc.text(
                `Página ${doc.internal.getCurrentPageInfo().pageNumber}/${pageCount}`,
                pageWidth - 12, pageHeight-4, { align: "right" }
            );
        }
    });

    doc.save("relatorio_chamados_concluidos_GreenCash.pdf");
}
    document.querySelector('.toggle').addEventListener('click', function () {
        const navigation = document.querySelector('.navigation');
        const main = document.querySelector('.main');
        navigation.classList.toggle('active');
        main.classList.toggle('active');
    });
    function abrirModalLogout() { document.getElementById('logoutModal').style.display = 'flex'; }
    function fecharModalLogout() { document.getElementById('logoutModal').style.display = 'none'; }
    function confirmarLogout() { window.location.href = '../../../dashboard/pages/logout.php'; }
    </script>
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

/* ====================== Modal ========================== */

/* Overlay escurecido */
.modal-overlay {
  display: none;
  align-items: center;
  justify-content: center;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.4);
  z-index: 1000;
  transition: opacity 0.25s;
}
.modal-overlay.active {
  display: flex;
  opacity: 1;
}

/* Modal Box */
.modal {
  background: #F8F8F8;
  border-radius: 18px;
  box-shadow: 0 8px 40px #0004;
  max-width: 420px;
  width: 95%;
  padding: 30px 28px 20px 28px;
  position: relative;
  font-family: 'Segoe UI', 'Arial', sans-serif;
  color: #232323;
  animation: slideModal .2s;
  box-sizing: border-box;
}

/* Título */
.modal h2 {
  margin-top: 0;
  margin-bottom: 16px;
  font-size: 1.6em;
  font-weight: bold;
  letter-spacing: 0.5px;
}

/* Fechar (X) */
.close-modal-x {
  position: absolute;
  top: 13px;
  right: 18px;
  background: none;
  border: none;
  font-size: 2em;
  color: #666;
  cursor: pointer;
  z-index: 10;
  transition: color 0.2s;
  line-height: 1;
  font-weight: bold;
}
.close-modal-x:hover {
  color: #e53935;
}

/* Infos do chamado */
.modal-responder-info {
  margin-bottom: 14px;
  font-size: 1.05em;
}
.modal-responder-info b {
  font-weight: 600;
}

/* Inputs */
.modal select,
.modal textarea,
.modal input[type="text"] {
  border-radius: 8px;
  border: 1.5px solid #CCC;
  font-size: 1em;
  padding: 8px 12px;
  margin-bottom: 12px;
  background: #f9f9f9;
  outline: none;
  transition: border 0.2s, box-shadow 0.2s;
  box-sizing: border-box;
}
.modal select:focus,
.modal textarea:focus,
.modal input[type="text"]:focus {
  border: 1.5px solid #36b14a;
  box-shadow: 0 0 0 2px #36b14a22;
  background: #fff;
}

.modal textarea {
  width: 100%;
  min-height: 90px;
  resize: vertical;
  font-family: inherit;
  margin-bottom: 14px;
}

.modal input[type="text"] {
  width: 100%;
  margin-bottom: 14px;
}

/* Label */
.modal label {
  display: inline-block;
  margin-bottom: 4px;
  font-weight: 500;
  color: #1b5e20;
  letter-spacing: 0.1px;
}

/* Priorização + resposta em linha */
.modal-row {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 10px;
}
@media (max-width:480px) {
  .modal-row {
    flex-direction: column;
    align-items: stretch;
    gap: 0;
  }
}

/* Botões */
.modal .actions {
  display: flex;
  gap: 18px;
  margin-top: 14px;
  justify-content: flex-start;
}
.btn-modal,
.btn1 {
  border: none;
  border-radius: 10px;
  font-size: 1.08em;
  font-weight: bold;
  padding: 10px 28px;
  cursor: pointer;
  transition: background 0.18s, color 0.18s, filter 0.12s, box-shadow 0.12s;
  box-shadow: 0 4px 14px #0001;
  letter-spacing: 0.5px;
  outline: none;
  margin-bottom: 0;
}
.btn-modal.confirm,
.btn1:not(.btn-cancel) {
  background: #36b14a;
  color: #fff;
}
.btn-modal.confirm:hover,
.btn1:not(.btn-cancel):hover {
  background: #259336;
}
.btn-modal.cancel,
.btn1.btn-cancel {
  background: #bdbdbd;
  color: #222;
}
.btn-modal.cancel:hover,
.btn1.btn-cancel:hover {
  background: #9e9e9e;
  color: #111;
}
.btn-modal:active,
.btn1:active {
  filter: brightness(0.95);
}

/* Animação */
@keyframes slideModal {
  from { opacity: 0; transform: translateY(-22px);}
  to   { opacity: 1; transform: translateY(0);}
}

/* ===== Formulário externo/adicional ===== */
#form {
  max-width: 100%;
}

#form h2 {
  margin-top: 10px;
  font-size: 30px;
}

.input-group {
  margin-top: 20px;
}

.input-group label {
  display: block;
  font-size: 20px;
  font-weight: 500;
  color: #222;
}

.input-group input {
  width: 100%;
  font-size: 18px;
  padding: 10px;
  border-radius: 10px;
  border: 1px solid #ccc;
  margin-top: 5px;
  color: var(--black2, #333);
}

.input-group.actions {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}

.input-group.actions .button,
.input-group.actions button {
  width: 48%;
  font-size: 18px;
  padding: 10px;
  border-radius: 10px;
  background-color: var(--red, #e53935);
  border: 1px solid var(--red, #e53935);
  color: var(--white, #fff);
  cursor: pointer;
  transition: background .18s, color .18s;
} 

.input-group.actions button:hover,
.input-group.actions .button:hover{
  background-color: var(--white, #fff);
  border: 1px solid var(--black2, #333);
  color: var(--black1, #111);
} 

.input-group.actions .cancel {
  background-color: var(--green, #36b14a);
  color: var(--white, #fff);
  border: 1px solid var(--green, #36b14a);
  text-align: center;
}

.input-group.actions .cancel:hover {
  background-color: var(--light-red, #e57373);
  border: 1px solid var(--black2, #333);
  color: #111;
  text-align: center;
}
</style>

<script>
function abrirModalLogout() {
  document.getElementById('logoutModal').style.display = 'flex';
}
function fecharModalLogout() {
  document.getElementById('logoutModal').style.display = 'none';
}
function confirmarLogout() {
  // Redireciona para o PHP de logout (ou troque pelo seu backend)
  window.location.href = '../../../dashboard/pages/logout.php';
}
</script>
</body>
</html>