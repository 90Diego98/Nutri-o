<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Entrar | Raiz&Nutriente</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,500&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="estiloa.css">
</head>
<body data-page="entrar">

<div id="site-header"></div>

<section class="section">
  <div class="wrap" style="max-width:460px;">

    <a href="index.php" class="btn-secondary" style="margin-bottom:28px; display:inline-block;">&larr; Voltar para o início</a>

    <div class="payment-method-box">

      <div id="login-box">
        <h2 style="font-family:var(--serif); font-weight:800; font-size:1.5rem; margin-bottom:6px;">Entrar na sua conta</h2>
        <p class="form-note" style="margin-bottom:26px;">Acesse para ver seus planos e histórico de consultas.</p>
        <form id="login-form">
          <div class="form-grid">
            <div class="field light">
              <label for="login-email">E-mail</label>
              <input type="email" id="login-email" placeholder="voce@email.com" required>
            </div>
            <div class="field light">
              <label for="login-senha">Senha</label>
              <input type="password" id="login-senha" placeholder="********" required>
            </div>
          </div>
          <button type="submit" class="submit-btn">Entrar</button>
          <p class="form-error" id="login-erro"></p>
        </form>
        <p class="auth-toggle">Ainda não tem conta? <a id="show-register">Cadastre-se</a></p>
      </div>

      <div id="register-box" style="display:none;">
        <h2 style="font-family:var(--serif); font-weight:800; font-size:1.5rem; margin-bottom:6px;">Criar conta</h2>
        <p class="form-note" style="margin-bottom:26px;">Leva menos de um minuto.</p>
        <form id="register-form">
          <div class="form-grid">
            <div class="field light">
              <label for="register-nome">Nome completo</label>
              <input type="text" id="register-nome" placeholder="Seu nome" required>
            </div>
            <div class="field light">
              <label for="register-email">E-mail</label>
              <input type="email" id="register-email" placeholder="voce@email.com" required>
            </div>
            <div class="field light">
              <label for="register-senha">Senha</label>
              <input type="password" id="register-senha" placeholder="Mínimo 6 caracteres" required>
            </div>
          </div>
          <button type="submit" class="submit-btn">Criar conta</button>
          <p class="form-error" id="register-erro"></p>
        </form>
        <p class="auth-toggle">Já tem conta? <a id="show-login">Entrar</a></p>
      </div>

    </div>
  </div>
</section>

<div id="site-footer"></div>

<script>
  window.SITE_SESSION = {
    logged: <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>,
    nome: "<?php echo htmlspecialchars($_SESSION['user_nome'] ?? '', ENT_QUOTES); ?>"
  };
</script>
<script src="script.js"></script>
</body>
</html>
