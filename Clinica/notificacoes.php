<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    echo json_encode(['logado' => false, 'itens' => [], 'novas' => 0]);
    exit;
}
$uid = $_SESSION['user_id'];

if (($_POST['acao'] ?? '') === 'marcar_vistas') {
    $pdo->prepare("UPDATE agendamentos SET visto = 1 WHERE usuario_id = ?")->execute([$uid]);
    echo json_encode(['ok' => true]);
    exit;
}

$stmt = $pdo->prepare("SELECT id, status, visto FROM agendamentos WHERE usuario_id = ? ORDER BY id DESC LIMIT 10");
$stmt->execute([$uid]);
$itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

$novas = 0;
foreach ($itens as $i) { if ((int)$i['visto'] === 0) $novas++; }

echo json_encode(['logado' => true, 'itens' => $itens, 'novas' => $novas]);
