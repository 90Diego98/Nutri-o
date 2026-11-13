<?php
ob_start();
ini_set('display_errors', '0');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';

function responder($dados, $codigo = 200) {
    while (ob_get_level() > 0) { ob_end_clean(); }
    http_response_code($codigo);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($dados);
    exit;
}

$stmt = $pdo->prepare("SELECT is_admin FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['user_id'] ?? 0]);
if (!(int)$stmt->fetchColumn()) {
    responder(['ok' => false, 'msg' => 'Acesso negado.'], 403);
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    responder(['ok' => false, 'msg' => 'ID inválido.']);
}

$stmt = $pdo->prepare("DELETE FROM avaliacoes WHERE id = ?");
$stmt->execute([$id]);

responder(['ok' => true]);

