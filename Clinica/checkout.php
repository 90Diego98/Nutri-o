<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Finalizar Compra | Raiz&Nutriente</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,500&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="estiloa.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body data-page="checkout">

<div id="site-header"></div>

<section class="page-hero">
  <div class="wrap">
    <div class="hero-eyebrow">Finalizar compra</div>
    <h1>Resumo do pedido</h1>
  </div>
</section>

<section class="section alt-bg">
  <div class="wrap">

    <div class="payment-method-box" style="max-width:560px; margin-bottom:28px;">
      <h2 class="payment-method-title">Seu pedido</h2>
      <div id="checkout-itens"></div>
      <div id="checkout-vazio" class="form-note" style="display:none;">
        Nenhum item selecionado. <a href="loja.php" style="color:var(--accent);">Voltar pra loja</a>.
      </div>
      <div class="carrinho-total" style="border-top:1px solid var(--line); margin-top:14px; padding-top:14px;">
        <span>Total</span>
        <strong id="checkout-total">R$ 0,00</strong>
      </div>
    </div>

    <div id="checkout-pagamento" class="payment-methods-grid">

      <div class="payment-method-box">
        <h2 class="payment-method-title">Pix</h2>
        <div class="pix-key-display">
          <span>Chave Pix (e-mail)</span>
          <strong>contato@raizenutriente.com.br</strong>
        </div>

        <form id="pix-form">
          <input type="hidden" id="pix-valor" name="valor" value="">
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
      </div>

      <div class="payment-method-box">
        <h2 class="payment-method-title">Cartão de crédito</h2>
        <form id="cartao-form">
          <input type="hidden" id="cartao-valor" name="valor" value="">
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

