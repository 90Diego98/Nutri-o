<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Raiz&Nutriente | Clínica de Nutrição</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,500&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="estiloa.css">
</head>
<body data-page="index">

<div id="site-header"></div>

<section class="hero">
<div class="wrap" style="display:grid; grid-template-columns:1.2fr 1fr 1fr; gap:48px; align-items:center;">
    <div>
      <div class="hero-eyebrow">Nutrição clínica e comportamental</div>
      <h1>Um plano alimentar que <em>faz sentido</em> pra sua rotina</h1>
      <p>Atendimento individualizado, baseado em evidências e sem dietas prontas. Marque sua primeira consulta, presencial ou online, em poucos minutos.</p>
      <div class="hero-actions">
        <a href="#agendar" class="btn-primary">Agendar consulta</a>
        <a href="#explorar" class="btn-secondary">Ver o que oferecemos</a>
      </div>
    </div>
    <div class="hero-visual">
      <img src="imagens/hero.jpg" alt="Representando a nutrição saudável" class="hero-visual-img">
      <div class="hero-visual-note">
        <div class="num">+900</div>
        <div class="label">pacientes acompanhados nos últimos 6 anos</div>
      </div>
    </div>
    <div class="hero-side">
      <h3>Benefícios do acompanhamento nutricional</h3>
      <p>
          Mais energia, sono melhor, digestão leve e peso sob controle.
          Plano personalizado baseado em ciência, feito pra sua rotina real.
          Resultados duradouros, sem dietas da moda.
          Cuide da saúde com acompanhamento de verdade.</p>
    </div>
  </div>
</section>

<section class="section alt-bg" id="explorar">
  <div class="wrap">
    <div class="section-head">
      <h2>Tudo o que você precisa, num só lugar</h2>
      <p>Calcule seu gasto calórico, fale com a nutricionista por vídeo, pague sua consulta e acompanhe sua evolução.</p>
    </div>
    <div class="cards-grid cols-4">
      <a href="dieta.php" class="card">
        <h3>Calculadora de TMB e GET</h3>
        <p>Descubra seu gasto calórico diário e sua distribuição de macros.</p>
        <span class="go">Calcular →</span>
      </a>
      <a href="consultaCMpersonal.php" class="card">
        <h3>Consulta online</h3>
        <p>Fale com a nutricionista por videochamada, de onde você estiver.</p>
        <span class="go">Iniciar chamada →</span>
      </a>
      <a href="formaDepagamento.php" class="card">
        <h3>Pagamento via Pix</h3>
        <p>Gere o QR Code da sua consulta e pague em segundos.</p>
        <span class="go">Pagar →</span>
      </a>
      <a href="planosDeProjetiho.php" class="card">
        <h3>Planos</h3>
        <p>Compare os pacotes de acompanhamento e escolha o seu.</p>
        <span class="go">Ver planos →</span>
      </a>
    </div>
  </div>
</section>

<section class="section" id="agendar">
  <div class="wrap">
    <div class="panel-dark">
      <div>
        <h2>Agende sua consulta</h2>
        <p>Preencha o formulário abaixo. Nossa equipe confirma o horário por e-mail ou WhatsApp em até 24h úteis.</p>
        <ul class="panel-steps">
          <li><span class="dot"></span> Escolha o tipo de atendimento e a data preferida</li>
          <li><span class="dot"></span> Receba a confirmação do horário</li>
          <li><span class="dot"></span> Compareça à consulta (presencial ou online)</li>
        </ul>
      </div>
      <div>
        <form id="schedule-form">
          <div class="form-row" style="margin-bottom:16px;">
            <div class="field">
              <label for="nome">Nome completo</label>
              <input type="text" id="nome" name="nome" placeholder="Seu nome" required>
            </div>
            <div class="field">
              <label for="telefone">Telefone / WhatsApp</label>
              <input type="tel" id="telefone" name="telefone" placeholder="(00) 00000-0000" required>
            </div>
          </div>
          <div class="field" style="margin-bottom:16px;">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" placeholder="voce@email.com" required>
          </div>
          <div class="form-row" style="margin-bottom:16px;">
            <div class="field">
              <label for="tipo">Tipo de atendimento</label>
              <select id="tipo" name="tipo" required>
                <option value="">Selecione</option>
                <option value="Primeira consulta">Primeira consulta</option>
                <option value="Retorno">Retorno</option>
                <option value="Pacote mensal">Pacote mensal</option>
              </select>
            </div>
            <div class="field">
              <label for="formato">Formato</label>
              <select id="formato" name="formato" required>
                <option value="">Selecione</option>
                <option value="Presencial">Presencial</option>
                <option value="Online">Online</option>
              </select>
            </div>
          </div>
          <div class="form-row" style="margin-bottom:16px;">
            <div class="field">
              <label for="data">Data preferida</label>
              <input type="date" id="data" name="data" required>
            </div>
            <div class="field">
              <label for="horario">Horário preferido</label>
              <input type="time" id="horario" name="horario" required>
            </div>
          </div>
          <div class="field" style="margin-bottom:16px;">
            <label for="obs">Observações (opcional)</label>
            <textarea id="obs" name="obs" placeholder="Conte um pouco sobre o motivo da consulta"></textarea>
          </div>
          <button type="submit" class="submit-btn">Solicitar agendamento</button>
          <div id="form-success" class="success-box"></div>
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
