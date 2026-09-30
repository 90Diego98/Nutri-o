<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contato | Raiz&Nutriente</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,500&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="estiloa.css">
</head>
<body data-page="contato">

<div id="site-header"></div>

<section class="page-hero">
  <div class="wrap">
    <div class="hero-eyebrow">Fale com a gente</div>
    <h1>Contato</h1>
    <p>Dúvidas sobre consultas, planos ou pagamento? Preencha o formulário ou fale direto pelo WhatsApp.</p>
  </div>
</section>

<section class="section alt-bg">
  <div class="wrap">
    <div class="contact-grid">

      <div>
        <ul class="contact-info-list">
          <li>
            <h4>Telefone / WhatsApp</h4>
            <a href="tel:+5500000000000">(00) 0000-0000</a>
          </li>
          <li>
            <h4>E-mail</h4>
            <a href="mailto:contato@raizenutriente.com.br">contato@raizenutriente.com.br</a>
          </li>
          <li>
            <h4>Endereço</h4>
            <p>Rua Exemplo, 123 — Centro</p>
          </li>
          <li>
            <h4>Horário de atendimento</h4>
            <p>Seg a sex — 8h às 18h · Sáb — 8h às 12h</p>
          </li>
        </ul>
        <iframe
          class="map-frame"
          src="https://www.google.com/maps?q=Avenida%20Paulista,%20Sao%20Paulo&output=embed"
          loading="lazy">
        </iframe>
      </div>

      <div class="panel-light" style="padding:36px;">
        <h2 style="font-size:1.4rem; margin-bottom:6px;">Envie uma mensagem</h2>
        <p style="margin-bottom:22px;">Respondemos em até 1 dia útil.</p>
        <form id="contact-form">
          <div class="field light" style="margin-bottom:16px;">
            <label for="contact-nome">Nome</label>
            <input type="text" id="contact-nome" name="nome" placeholder="Seu nome" required>
          </div>
          <div class="field light" style="margin-bottom:16px;">
            <label for="contact-email">E-mail</label>
            <input type="email" id="contact-email" name="email" placeholder="voce@email.com" required>
          </div>
          <div class="field light" style="margin-bottom:16px;">
            <label for="contact-mensagem">Mensagem</label>
            <textarea id="contact-mensagem" name="mensagem" placeholder="Como podemos ajudar?" required></textarea>
          </div>
          <button type="submit" class="submit-btn">Enviar mensagem</button>
          <div id="contact-success" class="success-box"></div>
        </form>
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
