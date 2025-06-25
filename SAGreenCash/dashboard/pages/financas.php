<?php
session_start();
if (!isset($_SESSION["usuario"]["id"])) {
  http_response_code(403); exit("Usuário não autenticado.");
}
$usuario_id = $_SESSION["usuario"]["id"];
$mysqli = new mysqli('localhost', 'root', '', 'greencash');
if ($mysqli->connect_errno) {
  http_response_code(500); exit("Erro ao conectar ao banco.");
}
header("Content-Type: application/json");

function get_cartao_principal($usuario_id, $mysqli) {
  $stmt = $mysqli->prepare("SELECT id, numero FROM cartoes WHERE usuario_id=? AND principal=1 LIMIT 1");
  $stmt->bind_param("i", $usuario_id);
  $stmt->execute();
  $result = $stmt->get_result();
  return $result->fetch_assoc();
}

// --- PAGAR DESPESA ---
if (($_GET["action"] ?? '') === "pagar_despesa" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_GET["id"] ?? 0);
    $origem = $_GET["origem"] ?? "total"; // 'banco' ou 'total'
    $stmt = $mysqli->prepare("UPDATE despesas SET pago=1, origem_pagamento=? WHERE id=? AND usuario_id=?");
    $stmt->bind_param("sii", $origem, $id, $usuario_id);
    $stmt->execute();
    echo json_encode(["success" => true]);
    exit;
}

// --- REALIZAR PLANO ---
if (($_GET["action"] ?? '') === "realizar_plano" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_GET["id"] ?? 0);
    $origem = $_GET["origem"] ?? "total";
    $stmt = $mysqli->prepare("UPDATE planos SET realizado=1, origem_pagamento=? WHERE id=? AND usuario_id=?");
    $stmt->bind_param("sii", $origem, $id, $usuario_id);
    $stmt->execute();
    echo json_encode(["success" => true]);
    exit;
}

// --- INSERIR NOVO ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && empty($_POST["action"])) {
  $tipo = $_POST["tipo"] ?? '';
  $descricao = $_POST["descricao"] ?? '';
  $valor = floatval($_POST["valor"] ?? 0);

  // Pega o cartão principal e seus dados
  $cartao = get_cartao_principal($usuario_id, $mysqli);
  $cartao_id = $cartao['id'] ?? null;
  $cartao_numero = isset($cartao['numero']) ? substr($cartao['numero'], -4) : null;

  if (!$cartao_id) {
    http_response_code(400); echo json_encode(["success" => false, "msg" => "Cadastre um cartão antes."]); exit;
  }

  if ($tipo === "receita") {
    $stmt = $mysqli->prepare("INSERT INTO receitas (usuario_id, descricao, valor, data, cartao_id, cartao_numero, excluido) VALUES (?, ?, ?, NOW(), ?, ?, 0)");
    $stmt->bind_param("isdss", $usuario_id, $descricao, $valor, $cartao_id, $cartao_numero);
    $stmt->execute();
    echo json_encode(["success" => true, "id" => $stmt->insert_id]);
  } elseif ($tipo === "despesa") {
    $stmt = $mysqli->prepare("INSERT INTO despesas (usuario_id, descricao, valor, data, pago, cartao_id, cartao_numero, excluido) VALUES (?, ?, ?, NOW(), 0, ?, ?, 0)");
    $stmt->bind_param("isdss", $usuario_id, $descricao, $valor, $cartao_id, $cartao_numero);
    $stmt->execute();
    echo json_encode(["success" => true, "id" => $stmt->insert_id]);
  } elseif ($tipo === "plano") {
    $prazo = intval($_POST["prazo"] ?? 0);
    $stmt = $mysqli->prepare("INSERT INTO planos (usuario_id, descricao, valor, prazo, data, realizado, cartao_id, cartao_numero, excluido) VALUES (?, ?, ?, ?, NOW(), 0, ?, ?, 0)");
    $stmt->bind_param("isdiis", $usuario_id, $descricao, $valor, $prazo, $cartao_id, $cartao_numero);
    $stmt->execute();
    echo json_encode(["success" => true, "id" => $stmt->insert_id]);
  } else {
    http_response_code(400); echo json_encode(["success" => false, "msg" => "Tipo inválido"]);
  }
  exit;
}

// --- EDITAR ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? '') === "editar") {
  $tipo = $_POST["tipo"] ?? '';
  $id = intval($_POST["id"] ?? 0);
  $descricao = $_POST["descricao"] ?? '';
  $valor = floatval($_POST["valor"] ?? 0);

  // Pega o cartão principal e seus dados
  $cartao = get_cartao_principal($usuario_id, $mysqli);
  $cartao_id = $cartao['id'] ?? null;
  $cartao_numero = isset($cartao['numero']) ? substr($cartao['numero'], -4) : null;

  if ($tipo === "receita") {
    $stmt = $mysqli->prepare("UPDATE receitas SET descricao=?, valor=?, cartao_id=?, cartao_numero=? WHERE id=? AND usuario_id=?");
    $stmt->bind_param("sdissi", $descricao, $valor, $cartao_id, $cartao_numero, $id, $usuario_id);
    $stmt->execute();
    echo json_encode(["success" => true]);
  } elseif ($tipo === "despesa") {
    $stmt = $mysqli->prepare("UPDATE despesas SET descricao=?, valor=?, cartao_id=?, cartao_numero=? WHERE id=? AND usuario_id=?");
    $stmt->bind_param("sdissi", $descricao, $valor, $cartao_id, $cartao_numero, $id, $usuario_id);
    $stmt->execute();
    echo json_encode(["success" => true]);
  } elseif ($tipo === "plano") {
    $prazo = intval($_POST["prazo"] ?? 0);
    $stmt = $mysqli->prepare("UPDATE planos SET descricao=?, valor=?, prazo=?, cartao_id=?, cartao_numero=? WHERE id=? AND usuario_id=?");
    $stmt->bind_param("sdissii", $descricao, $valor, $prazo, $cartao_id, $cartao_numero, $id, $usuario_id);
    $stmt->execute();
    echo json_encode(["success" => true]);
  } else {
    http_response_code(400); echo json_encode(["success" => false, "msg" => "Tipo inválido"]);
  }
  exit;
}

// --- EXCLUIR (SOFT DELETE) ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? '') === "excluir") {
  $tipo = $_POST["tipo"] ?? '';
  $id = intval($_POST["id"] ?? 0);

  if ($tipo === "receita") {
    $stmt = $mysqli->prepare("UPDATE receitas SET excluido=1 WHERE id=? AND usuario_id=?");
    $stmt->bind_param("ii", $id, $usuario_id);
    $stmt->execute();
    echo json_encode(["success" => true]);
  } elseif ($tipo === "despesa") {
    $stmt = $mysqli->prepare("UPDATE despesas SET excluido=1 WHERE id=? AND usuario_id=?");
    $stmt->bind_param("ii", $id, $usuario_id);
    $stmt->execute();
    echo json_encode(["success" => true]);
  } elseif ($tipo === "plano") {
    $stmt = $mysqli->prepare("UPDATE planos SET excluido=1 WHERE id=? AND usuario_id=?");
    $stmt->bind_param("ii", $id, $usuario_id);
    $stmt->execute();
    echo json_encode(["success" => true]);
  } else {
    http_response_code(400); echo json_encode(["success" => false, "msg" => "Tipo inválido"]);
  }
  exit;
}

// --- GET ITEM INDIVIDUAL (para editar) ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? '') === "get") {
  $tipo = $_POST["tipo"] ?? '';
  $id = intval($_POST["id"] ?? 0);

  if ($tipo === "receita") {
    $stmt = $mysqli->prepare("SELECT id, descricao, valor FROM receitas WHERE id=? AND usuario_id=?");
    $stmt->bind_param("ii", $id, $usuario_id);
    $stmt->execute();
    $res = $stmt->get_result();
    echo json_encode($res->fetch_assoc());
  } elseif ($tipo === "despesa") {
    $stmt = $mysqli->prepare("SELECT id, descricao, valor, pago, origem_pagamento FROM despesas WHERE id=? AND usuario_id=?");
    $stmt->bind_param("ii", $id, $usuario_id);
    $stmt->execute();
    $res = $stmt->get_result();
    echo json_encode($res->fetch_assoc());
  } elseif ($tipo === "plano") {
    $stmt = $mysqli->prepare("SELECT id, descricao, valor, prazo, realizado, origem_pagamento FROM planos WHERE id=? AND usuario_id=?");
    $stmt->bind_param("ii", $id, $usuario_id);
    $stmt->execute();
    $res = $stmt->get_result();
    echo json_encode($res->fetch_assoc());
  } else {
    http_response_code(400); echo json_encode(["success" => false, "msg" => "Tipo inválido"]);
  }
  exit;
}

// --- LISTAGEM PADRÃO: só do cartão principal E NÃO EXCLUÍDOS (para dashboard) ---
if ($_SERVER["REQUEST_METHOD"] === "GET" && !isset($_GET["historico"])) {
  $cartao = get_cartao_principal($usuario_id, $mysqli);
  $cartao_id = $cartao["id"] ?? 0;

  $dados = [];
  $res = $mysqli->query("SELECT id, descricao, valor FROM receitas WHERE usuario_id=$usuario_id AND cartao_id=$cartao_id AND excluido=0 ORDER BY data DESC");
  $dados["receitas"] = [];
  while($row = $res->fetch_assoc()) $dados["receitas"][] = $row;
  $res = $mysqli->query("SELECT id, descricao, valor, pago, origem_pagamento FROM despesas WHERE usuario_id=$usuario_id AND cartao_id=$cartao_id AND excluido=0 ORDER BY data DESC");
  $dados["despesas"] = [];
  while($row = $res->fetch_assoc()) $dados["despesas"][] = $row;
  $res = $mysqli->query("SELECT id, descricao, valor, prazo, realizado, origem_pagamento FROM planos WHERE usuario_id=$usuario_id AND cartao_id=$cartao_id AND excluido=0 ORDER BY data DESC");
  $dados["planos"] = [];
  while($row = $res->fetch_assoc()) $dados["planos"][] = $row;
  echo json_encode($dados); exit;
}

// --- LISTAGEM PARA HISTÓRICO: tudo, inclusive excluídos (para billing/histórico) ---
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["historico"])) {
  $cartao = get_cartao_principal($usuario_id, $mysqli);
  $cartao_id = $cartao["id"] ?? 0;

  $dados = [];
  $res = $mysqli->query("SELECT id, descricao, valor, excluido FROM receitas WHERE usuario_id=$usuario_id AND cartao_id=$cartao_id ORDER BY data DESC");
  $dados["receitas"] = [];
  while($row = $res->fetch_assoc()) $dados["receitas"][] = $row;
  $res = $mysqli->query("SELECT id, descricao, valor, pago, origem_pagamento, excluido FROM despesas WHERE usuario_id=$usuario_id AND cartao_id=$cartao_id ORDER BY data DESC");
  $dados["despesas"] = [];
  while($row = $res->fetch_assoc()) $dados["despesas"][] = $row;
  $res = $mysqli->query("SELECT id, descricao, valor, prazo, realizado, origem_pagamento, excluido FROM planos WHERE usuario_id=$usuario_id AND cartao_id=$cartao_id ORDER BY data DESC");
  $dados["planos"] = [];
  while($row = $res->fetch_assoc()) $dados["planos"][] = $row;
  echo json_encode($dados); exit;
}
?>