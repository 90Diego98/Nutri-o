<?php
require_once 'config.php';
header('Content-Type: application/json');

$nome     = trim($_POST['nome'] ?? '');
$email    = trim($_POST['email'] ?? '');
$mensagem = trim($_POST['mensagem'] ?? '');

if (!$nome || !$email || !$mensagem) {
    echo json_encode(['ok' => false, 'msg' => 'Preencha todos os campos.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['ok' => false, 'msg' => 'E-mail inválido.']);
    exit;
}

$stmt = $pdo->prepare('INSERT INTO contatos (nome, email, mensagem) VALUES (?, ?, ?)');
$stmt->execute([$nome, $email, $mensagem]);

echo json_encode(['ok' => true, 'msg' => 'Mensagem enviada! Em breve entraremos em contato.']);
