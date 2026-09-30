<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Antes e Depois | Raiz&Nutriente</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,500&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="estiloa.css">
</head>
<body data-page="antes">

<div id="site-header"></div>

<section class="page-hero">
  <div class="wrap">
    <div class="hero-eyebrow">Resultados reais</div>
    <h1>Antes e depois</h1>
    <p>Histórias de pacientes que passaram pelo acompanhamento nutricional. Fotos publicadas com autorização de cada paciente.</p>
  </div>
</section>

<section class="section alt-bg">
  <div class="wrap">
    <div class="case-grid">

      <div class="case-card">
        <div class="case-compare">
          <div class="case-panel before"><span class="tag">Antes</span></div>
          <div class="case-panel after"><span class="tag">Depois</span></div>
        </div>
        <div class="case-info">
          <h3>Mariana, 34 anos</h3>
          <p>8 meses de acompanhamento — <span class="stat">reeducação alimentar</span> e retomada da rotina de exercícios.</p>
        </div>
      </div>

      <div class="case-card">
        <div class="case-compare">
          <div class="case-panel before"><span class="tag">Antes</span></div>
          <div class="case-panel after"><span class="tag">Depois</span></div>
        </div>
        <div class="case-info">
          <h3>Rafael, 41 anos</h3>
          <p>6 meses de acompanhamento — foco em <span class="stat">saúde metabólica</span> e ajuste de exames.</p>
        </div>
      </div>

      <div class="case-card">
        <div class="case-compare">
          <div class="case-panel before"><span class="tag">Antes</span></div>
          <div class="case-panel after"><span class="tag">Depois</span></div>
        </div>
        <div class="case-info">
          <h3>Beatriz, 27 anos</h3>
          <p>12 meses de acompanhamento — plano voltado para <span class="stat">performance esportiva</span>.</p>
        </div>
      </div>

      <div class="case-card">
        <div class="case-compare">
          <div class="case-panel before"><span class="tag">Antes</span></div>
          <div class="case-panel after"><span class="tag">Depois</span></div>
        </div>
        <div class="case-info">
          <h3>João, 52 anos</h3>
          <p>10 meses de acompanhamento — controle alimentar aliado ao tratamento médico.</p>
        </div>
      </div>

    </div>

    <p class="form-note" style="max-width:64ch; margin-top:32px;">
      Os blocos coloridos acima são espaços reservados para as fotos reais. Para usar fotos de pacientes, coloque os arquivos na pasta <code>imagens/</code> e troque cada <code>&lt;div class="case-panel"&gt;</code> por uma tag <code>&lt;img src="imagens/nome-do-arquivo.jpg"&gt;</code> — sempre com a autorização por escrito do paciente.
    </p>
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
