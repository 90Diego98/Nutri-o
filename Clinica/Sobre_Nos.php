<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sobre Nós | Raiz&Nutriente</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,500&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="estiloa.css">
</head>
<body data-page="sobre">

<div id="site-header"></div>

<section class="page-hero">
  <div class="wrap">
    <div class="hero-eyebrow">Quem somos</div>
    <h1>Sobre a Raiz&Nutriente</h1>
    <p>Nutrição clínica e comportamental pensada pra caber na sua rotina — sem dietas da moda, sem promessas mágicas.</p>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="panel-light">
      <h2>Nossa história</h2>
      <p>A Raiz&Nutriente nasceu com um propósito simples: ajudar pessoas a alcançarem suas metas.
         Ao longo dos últimos anos, já acompanhamos centenas de pacientes, sempre com atendimento individualizado e baseado em evidências científicas.</p>
      <p>Acreditamos que não existe fórmula única — cada corpo, cada rotina e cada história pedem um plano diferente. Por isso,
         cada acompanhamento aqui começa com escuta, não com uma dieta pronta.</p>
    </div>
  </div>
</section>

<section class="section alt-bg">
  <div class="wrap">
    <div class="section-head">
      <h2>Nossos valores</h2>
      <p>O que guia cada consulta e cada plano alimentar que montamos.</p>
    </div>
    <div class="cards-grid cols-4">
      <div class="card">
        <h3>Ciência</h3>
        <p>Orientações baseadas em evidências, sem modismos ou dietas restritivas sem fundamento.</p>
      </div>
      <div class="card">
        <h3>Individualidade</h3>
        <p>Cada plano é montado a partir da sua rotina, seus gostos e seus objetivos reais.</p>
      </div>
      <div class="card">
        <h3>Acolhimento</h3>
        <p>Um espaço sem julgamentos para falar sobre comida, corpo e hábitos.</p>
      </div>
      <div class="card">
        <h3>Constância</h3>
        <p>Acompanhamento contínuo, não só uma consulta isolada — evolução de verdade leva tempo.</p>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="panel-dark">
      <div>
        <h2>Como trabalhamos</h2>
        <p>Um processo simples, pensado pra você entender exatamente onde está e pra onde está indo.</p>
      </div>
      <ul class="panel-steps">
        <li><span class="dot"></span> Primeira consulta: avaliação completa do seu histórico, rotina e objetivos.</li>
        <li><span class="dot"></span> Plano alimentar individualizado, com metas realistas.</li>
        <li><span class="dot"></span> Acompanhamento próximo, com ajustes ao longo do caminho.</li>
        <li><span class="dot"></span> Resultados sustentáveis, sem efeito sanfona.</li>
      </ul>
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

