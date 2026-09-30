<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forma de Pagamento | Raiz&Nutriente</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,500&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="estiloa.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body data-page="pagamento">

<div id="site-header"></div>

<section class="page-hero">
  <div class="wrap">
    <div class="hero-eyebrow">Pagamento</div>
    <h1>Pague sua consulta com Pix</h1>
    <p>Informe o valor combinado com a nutricionista e gere o QR Code na hora. Você também pode copiar o código e colar direto no app do seu banco.</p>
  </div>
</section>

<section class="section alt-bg">
  <div class="wrap">
    <div class="pix-box">
      <div>
        <div class="pix-key-display">
          <span>Chave Pix (e-mail)</span>
          <strong>contato@raizenutriente.com.br</strong>
        </div>

        <form id="pix-form">
          <div class="field light" style="margin-bottom:18px;">
            <label for="pix-valor">Valor da consulta (R$)</label>
            <input type="number" id="pix-valor" name="valor" min="1" step="0.01" placeholder="Ex: 220.00" required>
          </div>
          <button type="submit" class="submit-btn" style="max-width:260px;">Gerar QR Code Pix</button>
          <p class="form-error" id="pix-erro"></p>
        </form>

        <div id="pix-resultado">
          <p class="form-note" style="margin-top:0;">Escaneie o QR Code no app do seu banco ou copie o código abaixo:</p>
          <div class="pix-copy-row">
            <textarea id="pix-copia-cola" readonly></textarea>
            <button type="button" id="pix-copy-btn">Copiar código</button>
          </div>
        </div>

        <h3 style="font-family:var(--serif); font-weight:500; font-size:1.1rem; margin-top:36px; margin-bottom:6px;">Outras formas de pagamento</h3>
        <ul class="other-payments">
          <li>Cartão de crédito ou débito, na clínica</li>
          <li>Transferência bancária</li>
          <li>Reembolso pelo plano de saúde, mediante recibo</li>
        </ul>
      </div>

      <div class="qr-frame">
        <div id="qrcode"></div>
      </div>
    </div>

    <p class="form-note" style="max-width:64ch; margin-top:32px;">
      O código é gerado no servidor, segue o padrão oficial do Banco Central (Pix Copia e Cola / BR Code)
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
