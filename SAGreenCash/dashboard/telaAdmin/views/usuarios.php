<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
require "db.php";

// Função para salvar foto
function uploadFoto($inputName, $fotoAtual = null) {
    if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION));
        $permitidos = ['jpg','jpeg','png','gif','bmp','webp'];
        if (!in_array($ext, $permitidos)) return $fotoAtual;
        $nome = uniqid('foto_') . '.' . $ext;
        $caminho = 'uploads/' . $nome;
        if (!is_dir('uploads')) mkdir('uploads', 0777, true);
        move_uploaded_file($_FILES[$inputName]['tmp_name'], $caminho);
        return $caminho;
    }
    return $fotoAtual;
}

// CRUD - Cadastro/Edição/Desativar/Reativar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Cadastrar
  if (isset($_POST['acao']) && $_POST['acao'] === 'cadastrar') {
      $nome = $_POST['nome'];
      $email = $_POST['email'];
      $senha = md5($_POST['senha']);
      $dataCadastro = date('Y-m-d H:i:s'); // Sempre agora!
      $foto = uploadFoto('foto');
      $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, dataCadastro, foto, ativo, plano) VALUES (?, ?, ?, ?, ?, 1, NULL)");
      $stmt->bind_param("sssss", $nome, $email, $senha, $dataCadastro, $foto);
      $stmt->execute();
      header("Location: ".$_SERVER['PHP_SELF']);
      exit;
  }

  // Editar
  if (isset($_POST['acao']) && $_POST['acao'] === 'editar') {
      $id = intval($_POST['id']);
      $nome = $_POST['nome'];
      $email = $_POST['email'];
      $senha = $_POST['senha'];
      $foto = uploadFoto('foto', $_POST['fotoAtual']);
      if ($senha) {
          $senhaHash = md5($senha); // Mantém compatibilidade com login
          $stmt = $conn->prepare("UPDATE usuarios SET nome=?, email=?, senha=?, foto=? WHERE id=?");
          $stmt->bind_param("ssssi", $nome, $email, $senhaHash, $foto, $id);
      } else {
          $stmt = $conn->prepare("UPDATE usuarios SET nome=?, email=?, foto=? WHERE id=?");
          $stmt->bind_param("sssi", $nome, $email, $foto, $id);
      }
      $stmt->execute();
      header("Location: ".$_SERVER['PHP_SELF']);
      exit;
  }
    // Desativar/Reativar
    if (isset($_POST['toggle_ativo'])) {
        $id = intval($_POST['id']);
        $novo = $_POST['toggle_ativo'] == 'ativar' ? 1 : 0;
        $stmt = $conn->prepare("UPDATE usuarios SET ativo=? WHERE id=?");
        $stmt->bind_param("ii", $novo, $id);
        $stmt->execute();
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }
}
// Busca/Filtro de usuários
$filtro = trim($_GET['filtro'] ?? '');

if ($filtro !== '') {
    $like = "%$filtro%";
    $sql = "SELECT * FROM usuarios 
            WHERE nome LIKE ? 
               OR email LIKE ? 
               OR DATE_FORMAT(dataCadastro, '%Y-%m-%d %H:%i:%s') LIKE ? 
            ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $like, $like, $like);
    $stmt->execute();
    $usuarios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $usuarios = $conn->query("SELECT * FROM usuarios ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
}

// Função para cor do plano
function planoColor($plano) {
    if ($plano === 'Básico') return '#4caf50';
    if ($plano === 'Intermediário') return '#ff9800';
    if ($plano === 'Avançado') return '#f44336';
    return '#232929';
}
// KPIs: Total, Ativos, Desativados
$totalUsuarios = count($usuarios);
$totalAtivos = 0;
$totalDesativados = 0;
foreach ($usuarios as $u) {
    if (isset($u['ativo']) && $u['ativo'] == 1) $totalAtivos++;
    if (isset($u['ativo']) && $u['ativo'] == 0) $totalDesativados++;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible=IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GreenCash - Usuários</title>
    <!-- ======= Estilos ====== -->
    <link rel="icon" type="image/png" href="../views/imgs/Login-Cadastro/loggoooo.png" />
    <link rel="stylesheet" href="../views/css/style.css">
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<style>
/* ============================= */
/* ======= KPI CARD AREA ======= */
/* ============================= */

.usuarios-kpi-area {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    flex-wrap: wrap;
    gap: 24px;
    align-items: stretch;
    padding: 32px 20px;
    margin-bottom: 40px;
}

.usuarios-kpi-card {
    background: #3a3a3a;
    color: #ffffff;
    border-radius: 16px;
    box-shadow: 
        0 1px 3px rgba(0, 0, 0, 0.12),
        0 1px 2px rgba(0, 0, 0, 0.08);
    padding: 32px 28px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex: 1;
    min-width: 280px;
    position: relative;
    transition: all 0.2s ease;
    border: 1px solid #4a4a4a;
    overflow: hidden;
}

.usuarios-kpi-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: #22c55e;
    opacity: 1;
}

.usuarios-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 
        0 4px 12px rgba(0, 0, 0, 0.25),
        0 2px 4px rgba(0, 0, 0, 0.15);
    border-color: #5a5a5a;
}

.usuarios-kpi-card:hover::before {
    opacity: 1;
}

.usuarios-kpi-content {
    display: flex;
    flex-direction: column;
    gap: 6px;
    flex: 1;
    align-items: flex-start;
}

.usuarios-kpi-numbers {
    font-size: 2.25rem;
    font-weight: 700;
    line-height: 1;
    letter-spacing: -0.025em;
    color: #ffffff;
    margin-bottom: 2px;
}

.usuarios-kpi-label {
    font-size: 0.95rem;
    font-weight: 500;
    color: #d1d5db;
    line-height: 1.3;
    letter-spacing: 0.005em;
    text-transform: uppercase;
    font-size: 0.875rem;
}

.usuarios-kpi-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 56px;
    height: 56px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.15);
    transition: all 0.2s ease;
    flex-shrink: 0;
    margin-left: auto;
}

.usuarios-kpi-icon ion-icon {
    font-size: 1.8rem;
    color: #d1d5db;
    transition: all 0.2s ease;
}

.usuarios-kpi-card:hover .usuarios-kpi-icon {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.25);
}

.usuarios-kpi-card:hover .usuarios-kpi-icon ion-icon {
    color: #ffffff;
}

/* Variações específicas para cada tipo de KPI */
.usuarios-kpi-card.total::before {
    background: #3b82f6;
}

.usuarios-kpi-card.total .usuarios-kpi-numbers {
    color: #ffffff;
}

.usuarios-kpi-card.total .usuarios-kpi-icon {
    background: rgba(59, 130, 246, 0.1);
    border-color: rgba(59, 130, 246, 0.2);
}

.usuarios-kpi-card.total .usuarios-kpi-icon ion-icon {
    color: #3b82f6;
}

.usuarios-kpi-card.ativos::before {
    background: #10b981;
}

.usuarios-kpi-card.ativos .usuarios-kpi-numbers {
    color: #ffffff;
}

.usuarios-kpi-card.ativos .usuarios-kpi-icon {
    background: rgba(16, 185, 129, 0.1);
    border-color: rgba(16, 185, 129, 0.2);
}

.usuarios-kpi-card.ativos .usuarios-kpi-icon ion-icon {
    color: #10b981;
}

.usuarios-kpi-card.desativados::before {
    background: #ef4444;
}

.usuarios-kpi-card.desativados .usuarios-kpi-numbers {
    color: #ffffff;
}

.usuarios-kpi-card.desativados .usuarios-kpi-icon {
    background: rgba(239, 68, 68, 0.1);
    border-color: rgba(239, 68, 68, 0.2);
}

.usuarios-kpi-card.desativados .usuarios-kpi-icon ion-icon {
    color: #ef4444;
}

/* Responsividade aprimorada */
@media (max-width: 768px) {
    .usuarios-kpi-area {
        flex-direction: column;
        padding: 24px 16px;
        gap: 16px;
        margin-bottom: 32px;
    }
    
    .usuarios-kpi-card {
        min-width: unset;
        padding: 24px;
    }
    
    .usuarios-kpi-numbers {
        font-size: 2rem;
    }
    
    .usuarios-kpi-label {
        font-size: 0.8rem;
    }
    
    .usuarios-kpi-icon {
        width: 48px;
        height: 48px;
    }
    
    .usuarios-kpi-icon ion-icon {
        font-size: 1.6rem;
    }
}

@media (max-width: 480px) {
    .usuarios-kpi-area {
        padding: 20px 16px;
        gap: 12px;
    }
    
    .usuarios-kpi-card {
        padding: 20px;
    }
    
    .usuarios-kpi-numbers {
        font-size: 1.875rem;
    }
    
    .usuarios-kpi-label {
        font-size: 0.75rem;
    }
    
    .usuarios-kpi-icon {
        width: 44px;
        height: 44px;
    }
    
    .usuarios-kpi-icon ion-icon {
        font-size: 1.5rem;
    }
}
/* Estilos para sistema administrativo profissional */
/* Estilos para sistema administrativo profissional */

/* ============================= */
/* ======= BLOCO PRINCIPAL ===== */
/* ============================= */

.details {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 0;
}

.recentOrders {
    width: 100%;
    max-width: 1100px;
    margin: 0 auto 30px auto;
    background: #fff;
    border-radius: 18px;
    padding: 18px 22px 28px 22px;
    box-shadow: 0 4px 26px #b6e2c725;
    border: 2.5px solid #d4eed9;
    margin-top: 0;
}

/* ============================= */
/* ========= TABELA ============ */
/* ============================= */

#usuarios-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 12px;
    min-width: 800px;
}
#usuarios-table th, #usuarios-table td {
    padding: 10px 12px;
    text-align: left;
    white-space: nowrap;
}
#usuarios-table th {
    background: #f5f5f5;
    font-size: 1.05em;
    font-weight: 700;
    border-bottom: 2px solid #d4eed9;
}
#usuarios-table tr {
    border-bottom: 1px solid #e5f5eb;
}
#usuarios-table td img {
    display: block;
    margin: 0 auto;
}

/* ============================= */
/* == FORMULÁRIO E BOTÕES ====== */
/* ============================= */

.input-group {
  margin-top: 20px;
}

.input-group label {
  display: block;
  font-size: 20px;
  font-weight: 500;
  color: #222;
}

.input-group input, .input-group select {
  width: 100%;
  font-size: 18px;
  padding: 10px;
  border-radius: 10px;
  border: 1px solid #ccc;
  margin-top: 5px;
  color: var(--black2, #333);
}

/* ===== Actions ===== */
.actions {
  display: flex;
  align-items: center;
  justify-content: space-between;
  cursor: pointer;
  margin-top: 20px;
}
.actions .save,
.actions .cancel {
  width: 48%;
  font-size: 18px;
  padding: 10px;
  border-radius: 10px;
  color: var(--white, #fff);
  cursor: pointer;
  border: 1px solid var(--red, #e53935);
  background-color: var(--red, #e53935);
  transition: background-color 0.3s, color 0.3s, border 0.3s;
  text-align: center;
}
.actions .save:hover,
.actions .cancel:hover {
  background-color: var(--white, #fff);
  color: var(--black1, #111);
  border: 1px solid var(--black2, #333);
}
.actions .cancel {
  background-color: var(--green, #36b14a);
  color: var(--white, #fff);
  border: 1px solid var(--green, #36b14a);
  text-align: center;
}
.actions .cancel:hover {
  background-color: var(--light-red, #e57373);
  border: 1px solid var(--black2, #333);
  color: #111;
  text-align: center;
}

/* ===== Botão principal (verde) ===== */
.btn1 {
  background-color: transparent;
  color: #4CAF50;
  border: 2px solid #4CAF50;
  padding: 10px 20px;
  border-radius: 10px;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s, color 0.3s, transform 0.2s;
  box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
  text-align: center;
  text-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
  letter-spacing: 0.5px;
}
.btn1:hover {
  background-color: #43A047;
  color: white;
  transform: scale(1.05);
  box-shadow: 0 6px 16px rgba(67, 160, 71, 0.5);
}
.btn1:active {
  background-color: #388E3C;
  transform: scale(0.97);
  box-shadow: 0 2px 8px rgba(56, 142, 60, 0.4);
}

/* ===== Ícones ===== */
.btn-edit ion-icon,
.btn-delete ion-icon {
  font-size: 24px;
  vertical-align: middle;
}

/* ===== Botões Editar e Deletar ===== */
.btn-edit,
.btn-delete {
  background: none;
  border: none;
  cursor: pointer;
  padding: 8px;
  font-size: 20px;
  transition: color 0.3s, transform 0.2s;
}
.btn-edit {
  color: #4CAF50;
}
.btn-edit:hover {
  color: #388E3C;
  transform: scale(1.1);
}
.btn-edit:hover {
  color: #007bff;
}
.btn-delete {
  color: #ff6b6b;
}
.btn-delete:hover {
  color: #dc3545;
  transform: scale(1.1);
}
.btn-delete:hover {
  color: #dc3545;
}

/* ===== Custom File Upload ===== */
.custom-file-upload {
  display: inline-block;
  padding: 10px 20px;
  cursor: pointer;
  background-color: var(--green, #36b14a);
  color: var(--white, #fff);
  border: 2px solid var(--green, #36b14a);
  border-radius: 8px;
  font-size: 16px;
  text-align: center;
  transition: background-color 0.3s, color 0.3s, border 0.3s;
  margin-top: 8px;
}
.custom-file-upload:hover {
  background-color: var(--light-red, #e57373);
  color: black;
  border-color: var(--black2, #333);
}

/* ===== Modal (padrão) ===== */
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
.modal h2 {
  margin-top: 0;
  margin-bottom: 16px;
  font-size: 1.6em;
  font-weight: bold;
  letter-spacing: 0.5px;
}
@keyframes slideModal {
  from { opacity: 0; transform: translateY(-22px);}
  to   { opacity: 1; transform: translateY(0);}
}
</style>
<body>
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
                    <a href="Painel.php">
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
            <div class="usuarios-kpi-area">
    <div class="usuarios-kpi-card">
        <div>
            <div class="usuarios-kpi-numbers"><?= $totalUsuarios ?></div>
            <div class="usuarios-kpi-label">Total de Usuários</div>
        </div>
        <div class="usuarios-kpi-icon">
            <ion-icon name="person-outline"></ion-icon>
        </div>
    </div>
    <div class="usuarios-kpi-card kpi-ativos">
        <div>
            <div class="usuarios-kpi-numbers"><?= $totalAtivos ?></div>
            <div class="usuarios-kpi-label">Usuários Ativos</div>
        </div>
        <div class="usuarios-kpi-icon">
            <ion-icon name="person-circle-outline"></ion-icon>
        </div>
    </div>
    <div class="usuarios-kpi-card kpi-desativados">
        <div>
            <div class="usuarios-kpi-numbers"><?= $totalDesativados ?></div>
            <div class="usuarios-kpi-label">Usuários Desativados</div>
        </div>
        <div class="usuarios-kpi-icon">
            <ion-icon name="person-remove-outline"></ion-icon>
        </div>
    </div>
</div>
            
            <!-- Bloco de usuários colado no card -->
            <div class="details">
                <div class="recentOrders">
                    <div class="cardHeader" style="display:flex;justify-content:space-between;align-items:center;">
                        <h2>Usuários</h2>
                        <button onclick="openCadastroModal()" class="botaocadastro">+ Cadastrar Usuário</button>
                    </div>
                    <style>
                        /* Botão da esquerda - com efeito hover */
.botaocadastro {
  background-color: #4CAF50;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 14px;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s, transform 0.2s;
  text-align: center;
  letter-spacing: 0.3px;
}

/* Botão da direita - normal sem efeito */
.botao-normal {
  background-color: #4CAF50;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 14px;
  font-weight: bold;
  cursor: pointer;
  text-align: center;
  letter-spacing: 0.3px;
}

.botaocadastro:hover {
  background-color: #43A047;
  transform: scale(1.05);
}

.botaocadastro:active {
  background-color: #388E3C;
  transform: scale(0.98);
}
                    </style>
<form method="get" style="margin: 15px 0;">
    <label style="font-size:1.2em;margin-bottom:4px;display:block;">Filtrar Usuários:</label>
    <input type="text" id="filter-input" name="filtro" value="<?=htmlspecialchars($filtro)?>" placeholder="Digite o nome, e-mail ou Data para filtrar" style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid #aaa;" />
    <button type="submit" style="display:none"></button> <!-- permite Enter funcionar -->
</form>
                    <div style="width:100%;overflow-x:auto;">
                        <table id="usuarios-table">
                            <thead>
                                <tr>
                                    <th>Imagem</th>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Data de Cadastro</th>
                                    <th>Plano</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
<?php foreach ($usuarios as $u): ?>
    <tr class="<?=(isset($u['ativo']) && !$u['ativo']) ? 'user-inactive' : ''?>">
        <td>
            <?php if (!empty($u['foto']) && file_exists($u['foto'])): ?>
                <img src="<?=htmlspecialchars($u['foto'])?>" style="width:40px;height:40px;border-radius:50%;object-fit:cover;<?=((isset($u['ativo']) && !$u['ativo'])?'opacity:0.3;filter:grayscale(60%);':'')?>">
            <?php else: ?>
                <span style="color:#b0b0b0;">Foto</span>
            <?php endif; ?>
        </td>
        <td>
            <?php if(isset($u['ativo']) && !$u['ativo']): ?>
                <span style="color:#b0b0b0; opacity:0.5; font-style:italic; text-decoration: line-through;"><?=htmlspecialchars($u['nome']??'')?></span>
            <?php else: ?>
                <span style="color:#232929; font-weight:600;"><?=htmlspecialchars($u['nome']??'')?></span>
            <?php endif; ?>
        </td>
        <td><?=htmlspecialchars($u['email']??'')?></td>
        <td><?=htmlspecialchars($u['dataCadastro']??'')?></td>
        <td style="color:<?=planoColor($u['plano']??'')?>; font-weight: bold;">
            <?=(!empty($u['plano']) ? htmlspecialchars($u['plano']) : '<span>Basico</span>')?>
        </td>
        <td>
            <button class="btn-edit" onclick='openCadastroModal(<?=json_encode($u)?>)' title="Editar" type="button">
                <ion-icon name="create-outline"></ion-icon>
            </button>
            <form method="post" style="display:inline;">
                <input type="hidden" name="id" value="<?=htmlspecialchars($u['id']??'')?>" />
                <input type="hidden" name="toggle_ativo" value="<?=(isset($u['ativo']) && $u['ativo']) ? 'desativar':'ativar'?>" />
                <button class="<?=(isset($u['ativo']) && $u['ativo']) ? 'btn-delete' : 'btn-edit'?>" title="<?=(isset($u['ativo']) && $u['ativo']) ? 'Desativar' : 'Ativar'?>">
                    <ion-icon name="<?=(isset($u['ativo']) && $u['ativo']) ? 'ban-outline' : 'checkmark-circle-outline'?>"></ion-icon>
                </button>
            </form>
        </td>
    </tr>
<?php endforeach;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Modal de cadastro/edição de Usuário -->
<div class="modal-overlay" id="cadastro-modal">
    <div class="modal">
        <h2 id="modal-title">Cadastrar Usuário</h2>
        <form method="post" enctype="multipart/form-data" id="formCadastro">
            <input type="hidden" name="acao" value="cadastrar" id="acao" />
            <input type="hidden" name="id" id="usuario_id" />
            <input type="hidden" name="fotoAtual" id="fotoAtual" />
            <div class="input-group">
                <label for="nome">Nome</label>
                <input type="text" name="nome" id="cadastro-nome" required>
            </div>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="cadastro-email" required>
            </div>
            <div class="input-group" style="position: relative;">
                <label for="cadastro-senha">Senha</label>
                <input type="password" name="senha" id="cadastro-senha" maxlength="32" autocomplete="new-password" style="padding-right: 40px;"/>
                <span id="toggleSenha" style="position: absolute; top: 38px; right: 10px; cursor: pointer;">
                    <i id="olhoSenha" class="fa-solid fa-eye"></i>
                </span>
            </div>
            <div class="input-group">
                <label for="foto">Foto</label>
                <label for="cadastro-foto" class="custom-file-upload">
                    Selecionar Foto
                </label>
                <input type="file" name="foto" id="cadastro-foto" style="display: none;">
            </div>
            <div class="actions">
                <button type="button" class="cancel" onclick="closeModal()">Cancelar</button>
                <button type="submit" class="save">Salvar</button>
            </div>
        </form>
    </div>
</div>
<script>
    function openCadastroModal(usuario = null) {
        document.getElementById('cadastro-modal').classList.add('active');
        document.getElementById('modal-title').innerText = usuario ? "Editar Usuário" : "Cadastrar Usuário";
        document.getElementById('acao').value = usuario ? "editar" : "cadastrar";
        document.getElementById('usuario_id').value = usuario ? usuario.id : "";
        document.getElementById('cadastro-nome').value = usuario ? usuario.nome : "";
        document.getElementById('cadastro-email').value = usuario ? usuario.email : "";
        document.getElementById('cadastro-senha').value = "";
// Supondo que usuario.dataCadastro vem do banco formato "YYYY-MM-DD HH:MM:SS"
        document.getElementById('cadastro-data').value = usuario && usuario.dataCadastro ? usuario.dataCadastro.replace(' ', 'T').slice(0, 16) : "";
        document.getElementById('fotoAtual').value = usuario ? usuario.foto : "";
    }
    function closeModal() {
        document.getElementById('cadastro-modal').classList.remove('active');
    }
    document.getElementById('toggleSenha').addEventListener('click', function () {
        const senhaInput = document.getElementById('cadastro-senha');
        const icon = document.getElementById('olhoSenha');
        if (senhaInput.type === 'password') {
            senhaInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            senhaInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
</script>
<style>
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
.modal h2 {
  margin-top: 0;
  margin-bottom: 16px;
  font-size: 1.6em;
  font-weight: bold;
  letter-spacing: 0.5px;
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
.input-group input, .input-group select {
  width: 100%;
  font-size: 18px;
  padding: 10px;
  border-radius: 10px;
  border: 1px solid #ccc;
  margin-top: 5px;
  color: var(--black2, #333);
}
.actions {
  display: flex;
  align-items: center;
  justify-content: space-between;
  cursor: pointer;
  margin-top: 20px;
}
.actions .save,
.actions .cancel {
  width: 48%;
  font-size: 18px;
  padding: 10px;
  border-radius: 10px;
  color: var(--white, #fff);
  cursor: pointer;
  border: 1px solid var(--red, #e53935);
  background-color: var(--red, #e53935);
  transition: background-color 0.3s, color 0.3s, border 0.3s;
  text-align: center;
}
.actions .save:hover,
.actions .cancel:hover {
  background-color: var(--white, #fff);
  color: var(--black1, #111);
  border: 1px solid var(--black2, #333);
}
.actions .cancel {
  background-color: var(--green, #36b14a);
  color: var(--white, #fff);
  border: 1px solid var(--green, #36b14a);
  text-align: center;
}
.actions .cancel:hover {
  background-color: var(--light-red, #e57373);
  border: 1px solid var(--black2, #333);
  color: #111;
  text-align: center;
}
.custom-file-upload {
  display: inline-block;
  padding: 10px 20px;
  cursor: pointer;
  background-color: var(--green, #36b14a);
  color: var(--white, #fff);
  border: 2px solid var(--green, #36b14a);
  border-radius: 8px;
  font-size: 16px;
  text-align: center;
  transition: background-color 0.3s, color 0.3s, border 0.3s;
  margin-top: 8px;
}
.custom-file-upload:hover {
  background-color: var(--light-red, #e57373);
  color: black;
  border-color: var(--black2, #333);
}
@keyframes slideModal {
  from { opacity: 0; transform: translateY(-22px);}
  to   { opacity: 1; transform: translateY(0);}
}
</style>
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
        // Menu toggle
        const toggle = document.querySelector('.toggle');
    const navigation = document.querySelector('.navigation');
    const main = document.querySelector('.main');
    toggle.onclick = function () {
      navigation.classList.toggle('active');
      main.classList.toggle('active');
    };
    function abrirModalLogout() {
      document.getElementById('logoutModal').style.display = 'flex';
    }
    function fecharModalLogout() {
      document.getElementById('logoutModal').style.display = 'none';
    }
    function confirmarLogout() {
      window.location.href = '../../../dashboard/pages/logout.php';
    }
    </script>
</body>
</html>