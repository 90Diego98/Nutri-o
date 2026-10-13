<?php
require_once 'config.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$nome  = trim($data['nome'] ?? '');
$email = trim($data['email'] ?? '');
$senha = $data['senha'] ?? '';

if (!$nome || !$email || !$senha) {
    echo json_encode(['ok' => false, 'msg' => 'Preencha todos os campos.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['ok' => false, 'msg' => 'E-mail inválido.']);
    exit;
}
if (strlen($senha) < 6 || !preg_match('/[A-Z]/', $senha) || !preg_match('/[0-9]/', $senha)) {
    echo json_encode(['ok' => false, 'msg' => 'A senha precisa ter pelo menos 6 caracteres, 1 letra maiúscula e 1 número.']);
    exit;
}


$stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    echo json_encode(['ok' => false, 'msg' => 'Este e-mail já está cadastrado.']);
    exit;
}

$hash = password_hash($senha, PASSWORD_DEFAULT);

$stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, senha_hash) VALUES (?, ?, ?)');
$stmt->execute([$nome, $email, $hash]);

$_SESSION['user_id']   = (int) $pdo->lastInsertId();
$_SESSION['user_nome'] = $nome;

echo json_encode(['ok' => true, 'nome' => $nome]);
