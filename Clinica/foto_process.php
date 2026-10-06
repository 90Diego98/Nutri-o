<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['ok' => false, 'msg' => 'Você precisa estar logado.']);
    exit;
}

if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['ok' => false, 'msg' => 'Selecione uma imagem válida.']);
    exit;
}

$arquivo = $_FILES['foto'];
$tiposPermitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
$tipo = mime_content_type($arquivo['tmp_name']);

if (!isset($tiposPermitidos[$tipo])) {
    echo json_encode(['ok' => false, 'msg' => 'Formato não suportado. Use JPG, PNG ou WEBP.']);
    exit;
}

if ($arquivo['size'] > 3 * 1024 * 1024) {
    echo json_encode(['ok' => false, 'msg' => 'A imagem deve ter até 3MB.']);
    exit;
}

$pastaDestino = __DIR__ . '/imagens/perfis';
if (!is_dir($pastaDestino)) {
    mkdir($pastaDestino, 0755, true);
}

$nomeArquivo = 'perfil_' . $_SESSION['user_id'] . '_' . time() . '.' . $tiposPermitidos[$tipo];
$caminhoCompleto = $pastaDestino . '/' . $nomeArquivo;
$caminhoRelativo = 'imagens/perfis/' . $nomeArquivo;

if (!move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
    echo json_encode(['ok' => false, 'msg' => 'Não foi possível salvar a imagem.']);
    exit;
}

$stmt = $pdo->prepare('UPDATE usuarios SET foto = ?, foto_aprovada = 0 WHERE id = ?');
$stmt->execute([$caminhoRelativo, $_SESSION['user_id']]);

echo json_encode([
    'ok' => true,
    'msg' => 'Foto enviada! Ela ficará em análise até ser aprovada pela equipe.',
    'caminho' => $caminhoRelativo,
    'aprovada' => false
]);
