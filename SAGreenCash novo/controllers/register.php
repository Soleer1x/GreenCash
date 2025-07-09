<?php
require 'db.php'; // Conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validações
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['error' => 'Email inválido']);
        exit;
    }
    if (strlen($password) !== 8) {
        echo json_encode(['error' => 'A senha deve ter exatamente 8 caracteres']);
        exit;
    }

    // Criptografar a senha
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Inserir no banco
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $passwordHash]);

        echo json_encode(['success' => 'Cadastro realizado com sucesso!']);
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            echo json_encode(['error' => 'O email já está cadastrado']);
        } else {
            echo json_encode(['error' => 'Erro ao registrar usuário: ' . $e->getMessage()]);
        }
    }
}
?>