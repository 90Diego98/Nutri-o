<?php
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['foto' => null]);
    exit;
}

$stmt = $pdo->prepare('SELECT foto, foto_aprovada FROM usuarios WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$u = $stmt->fetch();

echo json_encode(['foto' => ($u && $u['foto_aprovada']) ? $u['foto'] : null]);
