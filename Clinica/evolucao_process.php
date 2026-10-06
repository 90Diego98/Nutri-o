<?php
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['ok' => false, 'msg' => 'Você precisa estar logado para registrar sua evolução.']);
    exit;
}

$peso = isset($_POST['peso']) ? (float) str_replace(',', '.', $_POST['peso']) : 0;
$observacao = trim($_POST['observacao'] ?? '');

if ($peso <= 0 || $peso > 500) {
    echo json_encode(['ok' => false, 'msg' => 'Informe um peso válido.']);
    exit;
}

$stmt = $pdo->prepare('INSERT INTO evolucao (usuario_id, peso, observacao) VALUES (?, ?, ?)');
$stmt->execute([$_SESSION['user_id'], $peso, $observacao ?: null]);

echo json_encode(['ok' => true, 'msg' => 'Registro salvo com sucesso!']);

