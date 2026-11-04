<?php
require_once 'config.php';

$token   = $_GET['token'] ?? ($_POST['token'] ?? '');
$erro    = '';
$sucesso = false;
$usuario = null;

if ($token !== '') {
    $stmt = $pdo->prepare("SELECT id, email FROM usuarios WHERE reset_token = ? AND reset_expira > NOW()");
    $stmt->execute([$token]);
    $usuario = $stmt->fetch();
}
$valido = (bool) $usuario;

if ($valido && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $senha     = $_POST['senha'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';

    if ($senha !== $confirmar) {
        $erro = "As senhas não conferem.";
    } elseif (strlen($senha) < 6 || !preg_match('/[A-Z]/', $senha) || !preg_match('/[0-9]/', $senha)) {
        $erro = "A senha deve ter no mínimo 6 caracteres, uma letra maiúscula e um número.";
    } else {
        $hash = password_hash($senha, PASSWORD_DEFAULT);

        $up = $pdo->prepare("UPDATE usuarios SET senha = ?, reset_token = NULL, reset_expira = NULL WHERE id = ?");
        $up->execute([$hash, $usuario['id']]);

        // Libera o bloqueio de tentativas desse e-mail (tabela do seu login.php)
        $lim = $pdo->prepare("DELETE FROM login_tentativas WHERE email = ?");
        $lim->execute([mb_strtolower(mb_substr($usuario['email'], 0, 150))]);

        $sucesso = true;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Redefinir senha - Raiz&Nutriente</title>
  <link rel="stylesheet" href="estiloa.css">
</head>
<body>
  <div class="login-box">
    <h2>Criar nova senha</h2>

    <?php if ($sucesso): ?>
      <p style="color:#ffd600;">Senha alterada com sucesso!</p>
      <a href="entrar.php" class="btn">Ir para o login</a>

    <?php elseif (!$valido): ?>
      <p style="color:#ff4d4d;">Link inválido ou expirado.</p>
      <a href="esqueci_senha.php" class="btn">Pedir novo link</a>

    <?php else: ?>
      <?php if ($erro): ?>
        <p style="color:#ff4d4d;"><?= htmlspecialchars($erro) ?></p>
      <?php endif; ?>

      <form method="POST">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

        <label>Nova senha</label>
        <input type="password" name="senha" required>

        <label>Confirmar nova senha</label>
        <input type="password" name="confirmar" required>

        <button type="submit" class="btn">Salvar nova senha</button>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>
