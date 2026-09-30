<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dieta e Calculadora de TMB | Raiz&Nutriente</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,500&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="estiloa.css">
</head>
<body data-page="dieta">

<div id="site-header"></div>

<section class="page-hero">
  <div class="wrap">
    <div class="hero-eyebrow">Dieta e metabolismo</div>
    <h1>Descubra seu gasto calórico</h1>
    <p>Use a calculadora abaixo para estimar sua Taxa de Metabolismo Basal (TMB) e seu Gasto Energético Total (GET) — o ponto de partida de qualquer plano alimentar.</p>
  </div>
</section>

<section class="section alt-bg">
  <div class="wrap">
    <div class="panel-light">
      <h2>Calculadora de TMB</h2>
      <p>A fórmula usada é a de Mifflin-St Jeor, uma das mais precisas para estimar o metabolismo basal. O resultado é uma estimativa — o acompanhamento com a nutricionista ajusta os valores ao seu caso.</p>

      <form id="tmb-form">
        <div class="form-row" style="margin-bottom:16px;">
          <div class="field light">
            <label for="tmb-sexo">Sexo biológico</label>
            <select id="tmb-sexo" required>
              <option value="feminino">Feminino</option>
              <option value="masculino">Masculino</option>
            </select>
          </div>
          <div class="field light">
            <label for="tmb-idade">Idade (anos)</label>
            <input type="number" id="tmb-idade" min="10" max="100" placeholder="Ex: 32" required>
          </div>
        </div>
        <div class="form-row" style="margin-bottom:16px;">
          <div class="field light">
            <label for="tmb-peso">Peso (kg)</label>
            <input type="number" id="tmb-peso" min="30" max="300" step="0.1" placeholder="Ex: 68.5" required>
          </div>
          <div class="field light">
            <label for="tmb-altura">Altura (cm)</label>
            <input type="number" id="tmb-altura" min="100" max="230" placeholder="Ex: 170" required>
          </div>
        </div>
        <div class="field light" style="margin-bottom:20px;">
          <label for="tmb-atividade">Nível de atividade física</label>
          <select id="tmb-atividade" required>
            <option value="1.2">Sedentário (pouco ou nenhum exercício)</option>
            <option value="1.375">Leve (exercício leve 1 a 3 dias/semana)</option>
            <option value="1.55" selected>Moderado (exercício moderado 3 a 5 dias/semana)</option>
            <option value="1.725">Intenso (exercício pesado 6 a 7 dias/semana)</option>
            <option value="1.9">Muito intenso (treino físico + trabalho físico)</option>
          </select>
        </div>
        <button type="submit" class="submit-btn" style="max-width:260px;">Calcular</button>
      </form>

      <div class="tmb-results" id="tmb-resultado">
        <div class="tmb-result-card">
          <div class="label">Taxa de Metabolismo Basal (TMB)</div>
          <div class="value" id="tmb-valor">—</div>
        </div>
        <div class="tmb-result-card">
          <div class="label">Gasto Energético Total (GET)</div>
          <div class="value" id="get-valor">—</div>
        </div>
        <div class="macros">
          <div class="macro-chip">
            <div class="m-label">Proteínas / dia</div>
            <div class="m-value" id="macro-proteina">—</div>
          </div>
          <div class="macro-chip">
            <div class="m-label">Carboidratos / dia</div>
            <div class="m-value" id="macro-carbo">—</div>
          </div>
          <div class="macro-chip">
            <div class="m-label">Gorduras / dia</div>
            <div class="m-value" id="macro-gordura">—</div>
          </div>
        </div>
      </div>
      <p class="form-note">Esses valores são uma estimativa educativa e não substituem uma avaliação nutricional individualizada. <a href="index.php#agendar" style="text-decoration:underline;">Agende uma consulta</a> para um plano feito sob medida.</p>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="section-head">
      <h2>Boas práticas para o dia a dia</h2>
      <p>Pequenos ajustes que sustentam qualquer plano alimentar a longo prazo.</p>
    </div>
    <div class="cards-grid cols-4">
      <div class="card">
        <h3>Hidratação</h3>
        <p>Distribua a ingestão de água ao longo do dia, em vez de concentrar tudo à noite.</p>
      </div>
      <div class="card">
        <h3>Fibras</h3>
        <p>Inclua vegetais, frutas e grãos integrais nas principais refeições.</p>
      </div>
      <div class="card">
        <h3>Regularidade</h3>
        <p>Manter horários parecidos de refeição ajuda a regular a fome e a saciedade.</p>
      </div>
      <div class="card">
        <h3>Sono</h3>
        <p>Uma boa noite de sono influencia diretamente o apetite e o metabolismo.</p>
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
