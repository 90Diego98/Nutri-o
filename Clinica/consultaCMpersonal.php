<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Consulta Online | Raiz&Nutriente</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,500&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="estiloa.css">
</head>
<body data-page="consulta">

<div id="site-header"></div>

<section class="page-hero">
  <div class="wrap">
    <div class="hero-eyebrow">Consulta com a nutricionista</div>
    <h1>Sua consulta por videochamada</h1>
    <p>Entre com seu nome e inicie a chamada em vídeo direto pelo navegador — sem precisar instalar nenhum aplicativo.</p>
  </div>
</section>

<section class="section alt-bg">
  <div class="wrap">

    <div class="panel-light" id="video-start-box">
      <h2>Iniciar chamada</h2>
      <p>Ao entrar, seu navegador vai pedir permissão para usar câmera e microfone. A chamada é privada: só quem tiver o link da sala consegue entrar.</p>
      <form id="video-form">
        <div class="field light" style="margin-bottom:18px; max-width:360px;">
          <label for="video-nome">Seu nome</label>
          <input type="text" id="video-nome" placeholder="Como a nutricionista vai te chamar" required>
        </div>
        <button type="submit" class="submit-btn" style="max-width:260px;">Iniciar videochamada</button>
      </form>
      <p class="form-note">Se a nutricionista ainda não estiver na sala, você entra em uma sala de espera até ela se conectar.</p>
    </div>

    <div id="video-call-box">
      <iframe
        id="video-iframe"
        class="video-frame"
        allow="camera; microphone; fullscreen; display-capture; autoplay"
        allowfullscreen>
      </iframe>
      <div class="video-end-row">
        <button type="button" id="video-end-btn">Encerrar chamada</button>
      </div>
    </div>

  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="section-head">
      <h2>Como funciona</h2>
      <p>Poucos passos para a sua consulta online.</p>
    </div>
    <div class="cards-grid" style="grid-template-columns:repeat(3,1fr);">
      <div class="card">
        <h3>1. Agende</h3>
        <p>Marque o formato "Online" no <a href="index.php#agendar">formulário de agendamento</a>.</p>
      </div>
      <div class="card">
        <h3>2. Entre na sala</h3>
        <p>No horário combinado, volte a esta página e inicie a videochamada com seu nome.</p>
      </div>
      <div class="card">
        <h3>3. Converse com a nutricionista</h3>
        <p>A consulta acontece por vídeo, com a mesma qualidade de um atendimento presencial.</p>
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
