<?php
require_once 'config.php';
header('Content-Type: application/json');

$nome     = trim($_POST['nome'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$email    = trim($_POST['email'] ?? '');
$tipo     = trim($_POST['tipo'] ?? '');
$formato  = trim($_POST['formato'] ?? '');
$data     = trim($_POST['data'] ?? '');
$horario  = trim($_POST['horario'] ?? '');
$obs      = trim($_POST['obs'] ?? '');

if (!$nome || !$telefone || !$email || !$tipo || !$formato || !$data || !$horario) {
    echo json_encode(['ok' => false, 'msg' => 'Preencha todos os campos obrigatórios.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['ok' => false, 'msg' => 'E-mail inválido.']);
    exit;
}

$usuarioId = $_SESSION['user_id'] ?? null;

$stmt = $pdo->prepare(
    'INSERT INTO agendamentos (usuario_id, nome, telefone, email, tipo, formato, data_consulta, horario, observacoes)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
);
$stmt->execute([$usuarioId, $nome, $telefone, $email, $tipo, $formato, $data, $horario, $obs]);

echo json_encode(['ok' => true, 'msg' => 'Solicitação recebida! Entraremos em contato em breve para confirmar o horário.']);
