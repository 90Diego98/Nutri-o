<?php
require_once 'config.php';
header('Content-Type: application/json');

$data  = json_decode(file_get_contents('php://input'), true);
$email = trim($data['email'] ?? '');
$senha = $data['senha'] ?? '';

if (!$email || !$senha) {
    echo json_encode(['ok' => false, 'msg' => 'Preencha e-mail e senha.']);
    exit;
}

$stmt = $pdo->prepare('SELECT id, nome, senha_hash FROM usuarios WHERE email = ?');
$stmt->execute([$email]);
$usuario = $stmt->fetch();

if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {
    echo json_encode(['ok' => false, 'msg' => 'E-mail ou senha incorretos.']);
    exit;
}

$_SESSION['user_id']   = (int) $usuario['id'];
$_SESSION['user_nome'] = $usuario['nome'];

echo json_encode(['ok' => true, 'nome' => $usuario['nome']]);
