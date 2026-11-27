<?php
session_start();

$host    = 'localhost';
$db      = 'clinica_nutricao';
$user    = 'root';
$pass    = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    http_response_code(500);
    die('Erro de conexão com o banco de dados.');
}

// ===== Bloqueio de contas banidas (vale para o site inteiro) =====
if (!empty($_SESSION['user_id'])) {
    $q = $pdo->prepare('SELECT banido, motivo_ban, is_admin FROM usuarios WHERE id = ?');
    $q->execute([$_SESSION['user_id']]);
    $conta = $q->fetch();

    // Admin nunca é bloqueado
    if ($conta && (int)$conta['banido'] === 1 && (int)$conta['is_admin'] !== 1) {

        // Único movimento permitido: sair da conta
        if (isset($_GET['sair_banido'])) {
            $_SESSION = [];
            session_destroy();
            header('Location: entrar.php');
            exit;
        }

        $motivo = trim((string)$conta['motivo_ban']);
        if ($motivo === '') $motivo = 'Não informado.';
        $motivo = htmlspecialchars($motivo, ENT_QUOTES, 'UTF-8');

        http_response_code(403);
        ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Conta banida | Raiz&amp;Nutriente</title>
<style>
  body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;
       background:#16141f;font-family:Arial,sans-serif;color:#fff;padding:20px}
  .ban{max-width:520px;width:100%;background:#1f1d2b;border:3px solid #d9232d;
       border-radius:16px;padding:32px;text-align:center;box-shadow:0 0 30px rgba(217,35,45,.35)}
  .ban h1{margin:0 0 12px;color:#ff4d57;font-size:1.6rem}
  .ban p{margin:8px 0;line-height:1.5}
  .motivo{background:#2a1618;border:1px solid #d9232d;border-radius:10px;padding:14px;margin:16px 0;word-break:break-word}
  .ban a{color:#9a98b0;font-size:.85rem}
</style>
</head>
<body>
  <div class="ban">
    <h1>Você foi banido</h1>
    <p>Sua conta foi bloqueada devido a:</p>
    <div class="motivo"><?= $motivo ?></div>
    <p><a href="?sair_banido=1">Sair da conta</a></p>
  </div>
</body>
</html>
        <?php
        exit;
    }
}
