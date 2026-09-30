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
    <h1>Pague sua consulta</h1>
    <p>Escolha entre Pix ou cartão de crédito. Informe o valor combinado com a nutricionista e conclua o pagamento na hora.</p>
  </div>
</section>

<section class="section alt-bg">
  <div class="wrap">

    <div class="payment-methods-grid">

      <div class="payment-method-box">
        <h2 class="payment-method-title">Pix</h2>
        <div class="pix-key-display">
          <span>Chave Pix (e-mail)</span>
          <strong>contato@raizenutriente.com.br</strong>
        </div>

        <form id="pix-form">
          <div class="field light" style="margin-bottom:12px;">
            <label for="pix-valor-select">Valor da consulta</label>
            <select id="pix-valor-select">
              <option value="220">Consulta mensal — R$ 220</option>
              <option value="480">Acompanhamento trimestral — R$ 480</option>
              <option value="1290">Acompanhamento anual — R$ 1.290</option>
              <option value="outro">Outro valor</option>
            </select>
          </div>
         
          <button type="submit" class="submit-btn" style="max-width:260px;">Gerar QR Code Pix</button>
          <p class="form-error" id="pix-erro"></p>
        </form>

        <div id="pix-resultado">
          <p class="form-note" style="margin-top:0;">Escaneie o QR Code no app do seu banco ou copie o código abaixo:</p>
          <div class="qr-frame" style="margin-bottom:16px;">
            <div id="qrcode"></div>
          </div>
          <div class="pix-copy-row">
            <textarea id="pix-copia-cola" readonly></textarea>
            <button type="button" id="pix-copy-btn">Copiar código</button>
          </div>
        </div>

        <p class="form-note" style="margin-top:24px;">
          Gerado no servidor, no padrão oficial do Banco Central (Pix Copia e Cola).
        </p>
      </div>

      <div class="payment-method-box">
        <h2 class="payment-method-title">Cartão de crédito</h2>
        <form id="cartao-form">
          <div class="field light" style="margin-bottom:12px;">
            <label for="cartao-valor-select">Valor da consulta</label>
            <select id="cartao-valor-select">
              <option value="220">Consulta mensal — R$ 220</option>
              <option value="480">Acompanhamento trimestral — R$ 480</option>
              <option value="1290">Acompanhamento anual — R$ 1.290</option>
              <option value="outro">Outro valor</option>
            </select>
          </div>
        
          <div class="field light" style="margin-bottom:18px;">
            <label for="cartao-numero">Número do cartão</label>
            <input type="text" id="cartao-numero" name="numero" inputmode="numeric" maxlength="19" placeholder="0000 0000 0000 0000" required>
          </div>
          <div class="field light" style="margin-bottom:18px;">
            <label for="cartao-nome">Nome impresso no cartão</label>
            <input type="text" id="cartao-nome" name="nome" placeholder="Como está no cartão" required>
          </div>
          <div class="form-row" style="margin-bottom:18px;">
            <div class="field light">
              <label for="cartao-validade">Validade (MM/AA)</label>
              <input type="text" id="cartao-validade" name="validade" maxlength="5" placeholder="MM/AA" required>
            </div>
            <div class="field light">
              <label for="cartao-cvv">CVV</label>
              <input type="text" id="cartao-cvv" name="cvv" inputmode="numeric" maxlength="4" placeholder="000" required>
            </div>
          </div>
          <div class="field light" style="margin-bottom:22px;">
            <label for="cartao-parcelas">Parcelas</label>
            <select id="cartao-parcelas" name="parcelas">
              <option value="1">1x sem juros</option>
              <option value="2">2x sem juros</option>
              <option value="3">3x sem juros</option>
              <option value="6">6x sem juros</option>
            </select>
          </div>
          <button type="submit" class="submit-btn" style="max-width:260px;">Pagar com cartão</button>
          <p class="form-error" id="cartao-erro"></p>
          <div id="cartao-resultado" class="success-box"></div>
        </form>

        <p class="form-note" style="margin-top:24px;">
      Por segurança, o número completo e o CVV não ficam salvos no banco, só os 4 últimos dígitos.
        </p>
      </div>

    </div>

    <h3 style="font-family:var(--serif); font-weight:500; font-size:1.1rem; margin-top:44px; margin-bottom:6px;">Outras formas de pagamento</h3>
    <ul class="other-payments">
      <li>Transferência bancária</li>
      <li>Reembolso pelo plano de saúde, mediante recibo</li>
    </ul>

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

