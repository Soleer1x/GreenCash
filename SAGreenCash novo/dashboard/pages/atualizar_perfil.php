<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION["usuario"])) {
  http_response_code(401);
  echo json_encode(["erro" => "Não autenticado"]);
  exit;
}

$id = $_SESSION["usuario"]["id"];
$nome = $_POST["nome"] ?? '';
$email = $_POST["email"] ?? '';
$telefone = $_POST["telefone"] ?? '';
$localizacao = $_POST["localizacao"] ?? '';
$bio = $_POST["bio"] ?? '';

$fotoPath = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
  $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
  $fotoPath = 'uploads/perfis/' . uniqid('foto_') . '.' . $ext;
  if (!is_dir('uploads/perfis')) {
    mkdir('uploads/perfis', 0777, true);
  }
  move_uploaded_file($_FILES['foto']['tmp_name'], $fotoPath);
}

if ($fotoPath) {
  $sql = "UPDATE usuarios SET nome=?, email=?, telefone=?, localizacao=?, bio=?, foto=? WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssssssi", $nome, $email, $telefone, $localizacao, $bio, $fotoPath, $id);
} else {
  $sql = "UPDATE usuarios SET nome=?, email=?, telefone=?, localizacao=?, bio=? WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssssi", $nome, $email, $telefone, $localizacao, $bio, $id);
}

$sucesso = $stmt->execute();

if ($sucesso) {
  // Atualiza sessão
  $_SESSION["usuario"]["nome"] = $nome;
  $_SESSION["usuario"]["email"] = $email;
  $_SESSION["usuario"]["telefone"] = $telefone;
  $_SESSION["usuario"]["localizacao"] = $localizacao;
  $_SESSION["usuario"]["bio"] = $bio;
  if ($fotoPath) {
    $_SESSION["usuario"]["foto"] = $fotoPath;
  }
  // Sempre retorne a foto atual do usuário (nova ou antiga)
  $fotoAtual = $_SESSION["usuario"]["foto"] ?? null;
  echo json_encode(["sucesso" => true, "foto" => $fotoAtual]);
} else {
  echo json_encode(["erro" => "Erro ao atualizar"]);
}
?>
