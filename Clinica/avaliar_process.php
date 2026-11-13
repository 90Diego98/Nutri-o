<?php
ob_start();
ini_set('display_errors', '0');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';

function responder($dados) {
    while (ob_get_level() > 0) { ob_end_clean(); }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($dados);
    exit;
}

if (empty($_SESSION['user_id'])) {
    responder(['ok' => false, 'msg' => 'Faça login para avaliar.']);
}

$nota = (int)($_POST['nota'] ?? 0);
$comentario = trim($_POST['comentario'] ?? '');

if ($nota < 1 || $nota > 5) {
    responder(['ok' => false, 'msg' => 'Escolha uma nota de 1 a 5 estrelas.']);
}
if ($nota < 3 && $comentario === '') {
    responder(['ok' => false, 'msg' => 'Como podemos melhorar? Deixe uma mensagem.']);
}
if (mb_strlen($comentario) > 500) {
    responder(['ok' => false, 'msg' => 'O comentário pode ter no máximo 500 caracteres.']);
}

$stmt = $pdo->prepare("INSERT INTO avaliacoes (usuario_id, nota, comentario) VALUES (?, ?, ?)");
$stmt->execute([$_SESSION['user_id'], $nota, $comentario !== '' ? $comentario : null]);

responder([
    'ok' => true,
    'nome' => $_SESSION['user_nome'] ?? 'Você',
    'nota' => $nota,
    'comentario' => $comentario
]);



