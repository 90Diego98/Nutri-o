<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Planos | Raiz&Nutriente</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,500&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="estiloa.css">
</head>
<body data-page="planos">

<div id="site-header"></div>

<section class="page-hero">
  <div class="wrap">
    <div class="hero-eyebrow">Planos de acompanhamento</div>
    <h1>Escolha o plano ideal para você</h1>
    <p>Do atendimento pontual ao acompanhamento contínuo — Planos totalmente individualizados.</p>
  </div>
</section>

<section class="section alt-bg">
  <div class="wrap">
    <div class="cards-grid" style="grid-template-columns:repeat(3,1fr);">
      <div class="card">
        <div class="price">R$ 220</div>
        <h3>Mensal</h3>
        <p>Ideal para quem quer uma primeira avaliação sem compromisso mensal.</p>
        
        
        
        <a href="formaDepagamento.php" class="go" style="display:inline-block; margin-top:16px;">Contratar →</a>
      </div>

      <div class="card" style="border:2px solid var(--berry);">
        <div class="price">R$ 480<span style="font-size:0.9rem; opacity:0.6;"> </span></div>
        <h3>Trimestral</h3>
        <p>O plano mais escolhido — acompanhamento próximo com ajustes frequentes.</p>
      
        <a href="formaDepagamento.php" class="go" style="display:inline-block; margin-top:16px;">Contratar →</a>
      </div>

      <div class="card">
        <div class="price">R$ 1.290<span style="font-size:0.9rem; opacity:0.6;"> </span></div>
        <h3>Anual</h3>
        <p>Para quem busca resultados consistentes com o menor custo por consulta.</p>
  
        <a href="formaDepagamento.php" class="go" style="display:inline-block; margin-top:16px;">Contratar →</a>
      </div>
    </div>

    <p class="form-note" style="max-width:64ch; margin-top:32px;">
      Todos os planos podem ser realizados de forma presencial ou por <a href="consultaCMpersonal.php" style="text-decoration:underline;">videochamada</a>, e pagos via <a href="formaDepagamento.php" style="text-decoration:underline;">Pix</a>, cartão ou reembolso pelo plano de saúde.
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
