<?php
require_once 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Endereço base do site (trocar para https://raizenutriente.com.br quando for pro ar)
$baseUrl = 'http://localhost/Clinica';

// E-mail que vai enviar (exemplo com Gmail + senha de app)
$smtpUsuario = 'seuemail@gmail.com';
$smtpSenha   = 'SENHA_DE_APP_AQUI';

$mensagem  = '';
$linkTeste = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario) {
            $token  = bin2hex(random_bytes(32));
            $expira = date('Y-m-d H:i:s', time() + 3600);

            $up = $pdo->prepare("UPDATE usuarios SET reset_token = ?, reset_expira = ? WHERE id = ?");
            $up->execute([$token, $expira, $usuario['id']]);

            $link = $baseUrl . '/redefinir_senha.php?token=' . $token;

            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = $smtpUsuario;
                $mail->Password   = $smtpSenha;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                $mail->CharSet    = 'UTF-8';

                $mail->setFrom($smtpUsuario, 'Raiz&Nutriente');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Redefinir senha - Raiz&Nutriente';
                $mail->Body    = "<p>Olá!</p>
                    <p>Clique no botão abaixo para criar uma nova senha. O link vale por 1 hora.</p>
                    <p><a href='$link' style='background:#ffd600;color:#000;padding:12px 24px;border-radius:30px;text-decoration:none;font-weight:bold;'>Redefinir senha</a></p>
                    <p>Se você não pediu isso, ignore este e-mail.</p>";
                $mail->AltBody = "Acesse para redefinir sua senha (vale 1 hora): $link";
                $mail->send();
            } catch (Exception $e) {
                // Se o envio falhar, o link de teste abaixo ainda permite testar
            }

            $linkTeste = $link; // REMOVER esta linha quando o site for pro ar
        }
    }

    // Mesma mensagem sempre, para não revelar quais e-mails existem
    $mensagem = "Se esse e-mail estiver cadastrado, enviamos um link para redefinir a senha.";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Esqueci minha senha - Raiz&Nutriente</title>
  <link rel="stylesheet" href="estiloa.css">
</head>
<body>
  <a href="entrar.php" class="btn">← Voltar para o login</a>

  <div class="login-box">
    <h2>Esqueci minha senha</h2>
    <p>Digite seu e-mail para receber o link de redefinição.</p>

    <?php if ($mensagem): ?>
      <p style="color:#ffd600;"><?= htmlspecialchars($mensagem) ?></p>
    <?php endif; ?>

    <?php if ($linkTeste): ?>
      <p style="word-break:break-all;">
        <small>(Modo teste) <a href="<?= htmlspecialchars($linkTeste) ?>">Clique aqui para redefinir</a></small>
      </p>
    <?php endif; ?>

    <form method="POST">
      <label>E-mail</label>
      <input type="email" name="email" placeholder="voce@email.com" required>
      <button type="submit" class="btn">Enviar link</button>
    </form>
  </div>
</body>
</html>
