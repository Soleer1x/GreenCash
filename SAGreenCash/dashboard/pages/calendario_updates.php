<?php
session_start();
if (!isset($_SESSION["usuario"])) {
  http_response_code(401);
  exit(json_encode(["error" => "Não autenticado"]));
}

require_once "db.php"; // ou o caminho correto para seu arquivo de conexão

$usuarioId = intval($_SESSION["usuario"]["id"]);

// Coleta as receitas do usuário
$receitas = [];
$sql = "SELECT id, descricao, valor, DATE(data) as data FROM receitas WHERE usuario_id = $usuarioId AND excluido = 0";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
  $receitas[] = $row;
}

// Coleta as despesas do usuário
$despesas = [];
$sql = "SELECT id, descricao, valor, DATE(data) as data FROM despesas WHERE usuario_id = $usuarioId AND excluido = 0";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
  $despesas[] = $row;
}

// Coleta os planos do usuário
$planos = [];
$sql = "SELECT id, descricao, valor, prazo, DATE(data) as data FROM planos WHERE usuario_id = $usuarioId AND excluido = 0";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
  $planos[] = $row;
}

// Junta por data
$datas = [];
foreach ($receitas as $r) {
  $datas[$r['data']]['receitas'][] = $r;
}
foreach ($despesas as $d) {
  $datas[$d['data']]['despesas'][] = $d;
}
foreach ($planos as $p) {
  $datas[$p['data']]['planos'][] = $p;
}

// Formata para o calendário (apenas dias com updates)
// NÃO COLOQUE "color" => ...
$eventos = [];
foreach ($datas as $data => $tipo) {
  $qtde = (count($tipo['receitas']??[])+count($tipo['despesas']??[])+count($tipo['planos']??[]));
  if ($qtde > 0) {
    $eventos[] = [
      "title" => "Atualizações",
      "date" => $data,
      "extendedProps" => [
        "receitas" => $tipo['receitas']??[],
        "despesas" => $tipo['despesas']??[],
        "planos" => $tipo['planos']??[]
      ]
    ];
  }
}

header("Content-Type: application/json");
echo json_encode($eventos);