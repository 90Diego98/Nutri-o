<?php
require_once 'config.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$nome    = trim($data['nome'] ?? '');
$usuario = trim($data['usuario'] ?? '');
$email   = trim($data['email'] ?? '');
$senha   = $data['senha'] ?? '';

if (!$nome || !$usuario || !$email || !$senha) {
    echo json_encode(['ok' => false, 'msg' => 'Preencha todos os campos.']);
    exit;
}
if (!preg_match('/^[A-Za-z0-9._]{3,30}$/', $usuario)) {
    echo json_encode(['ok' => false, 'msg' => 'O nome de usuário deve ter de 3 a 30 caracteres: letras, números, ponto e underline.']);
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

// A coluna usuario é utf8mb4_unicode_ci: "Ana" e "ana" contam como iguais
$stmt = $pdo->prepare('SELECT id FROM usuarios WHERE usuario = ?');
$stmt->execute([$usuario]);
if ($stmt->fetch()) {
    echo json_encode(['ok' => false, 'msg' => 'Esse nome de usuário já está em uso.']);
    exit;
}

$hash = password_hash($senha, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare('INSERT INTO usuarios (nome, usuario, email, senha_hash) VALUES (?, ?, ?, ?)');
    $stmt->execute([$nome, $usuario, $email, $hash]);
} catch (PDOException $e) {
    // Duas pessoas cadastrando ao mesmo tempo: a trava do banco barra a repetida
    if ((int) ($e->errorInfo[1] ?? 0) === 1062) {
        $msg = (strpos($e->getMessage(), 'email') !== false)
            ? 'Este e-mail já está cadastrado.'
            : 'Esse nome de usuário já está em uso.';
        echo json_encode(['ok' => false, 'msg' => $msg]);
        exit;
    }
    throw $e;
}

$_SESSION['user_id']   = (int) $pdo->lastInsertId();
$_SESSION['user_nome'] = $nome;

echo json_encode(['ok' => true, 'nome' => $nome]);