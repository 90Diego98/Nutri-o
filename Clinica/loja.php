<?php
require_once 'config.php';

$produtos = [
    ['nome' => 'Whey Protein Concentrado', 'descricao' => 'Proteína em pó, 900g, sabor baunilha.', 'preco' => 129.90, 'imagem' => 'imagens/produtos/whey.jpg'],
    ['nome' => 'Creatina Monohidratada', 'descricao' => 'Creatina pura, 300g.', 'preco' => 69.90, 'imagem' => 'imagens/produtos/creatina.jpg'],
    ['nome' => 'Multivitamínico', 'descricao' => 'Complexo vitamínico diário, 60 cápsulas.', 'preco' => 49.90, 'imagem' => 'imagens/produtos/multivitaminico.jpg'],
    ['nome' => 'Ômega 3', 'descricao' => 'Óleo de peixe, 1000mg, 90 cápsulas.', 'preco' => 59.90, 'imagem' => 'imagens/produtos/omega3.jpg'],
    ['nome' => 'Barrinha Proteica (caixa)', 'descricao' => 'Caixa com 12 unidades, sabor chocolate.', 'preco' => 44.90, 'imagem' => 'imagens/produtos/barrinha.jpg'],
    ['nome' => 'Coqueteleira', 'descricao' => 'Coqueteleira 600ml com misturador.', 'preco' => 24.90, 'imagem' => 'imagens/produtos/coqueteleira.jpg'],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Loja | Raiz&Nutriente</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,500&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="estiloa.css">
</head>
<body data-page="loja">

<div id="site-header"></div>

<section class="page-hero">
  <div class="wrap">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:20px; flex-wrap:wrap;">
      <div>
        <div class="hero-eyebrow">Loja</div>
        <h1>Suplementos selecionados pela nutricionista</h1>
        <p>Produtos recomendados pra complementar seu acompanhamento — pagamento via Pix ou cartão, na hora.</p>
      </div>
      <button type="button" id="carrinho-btn" class="carrinho-icone-btn">
        🛒
        <span class="carrinho-contador" id="carrinho-contador" style="display:none;">0</span>
      </button>
    </div>
  </div>
</section>

<div class="carrinho-overlay" id="carrinho-overlay">
  <div class="carrinho-painel">
    <div class="carrinho-topo">
      <h2 style="font-family:var(--serif); font-weight:800; font-size:1.2rem;">Seu carrinho</h2>
      <button type="button" id="carrinho-fechar" class="modal-close" aria-label="Fechar">&times;</button>
    </div>
    <div id="carrinho-lista" class="carrinho-lista"></div>
    <div id="carrinho-vazio" class="form-note" style="display:none;">Seu carrinho está vazio.</div>
    <div class="carrinho-total">
      <span>Total</span>
      <strong id="carrinho-total-valor">R$ 0,00</strong>
    </div>
    <button type="button" id="carrinho-finalizar" class="submit-btn">Finalizar compra</button>
  </div>
</div>

<section class="section alt-bg">
  <div class="wrap">
    <div class="produto-grid">
      <?php foreach ($produtos as $p): ?>
        <div class="produto-card">
          <div class="produto-imagem">
            <img src="<?php echo htmlspecialchars($p['imagem']); ?>" alt="<?php echo htmlspecialchars($p['nome']); ?>" onerror="this.style.display='none'">
          </div>
          <div class="produto-info">
            <h3><?php echo htmlspecialchars($p['nome']); ?></h3>
            <p><?php echo htmlspecialchars($p['descricao']); ?></p>
            <div class="produto-preco">R$ <?php echo number_format($p['preco'], 2, ',', '.'); ?></div>
            <div style="display:grid; gap:8px;">
              <a href="checkout.php?valor=<?php echo $p['preco']; ?>&produto=<?php echo urlencode($p['nome']); ?>" class="btn-primary" style="width:100%; text-align:center; display:block;">Comprar agora</a>
              <button type="button" class="btn-primary add-carrinho-btn"
                data-nome="<?php echo htmlspecialchars($p['nome']); ?>"
                data-preco="<?php echo $p['preco']; ?>"
                data-imagem="<?php echo htmlspecialchars($p['imagem']); ?>"
                style="width:100%; text-align:center;">
                Adicionar ao carrinho
              </button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <p class="form-note" style="max-width:64ch; margin-top:36px;">
      As imagens dos produtos ficam na pasta <code>imagens/produtos</code>. Se uma foto não for encontrada, o espaço da imagem fica em branco automaticamente.
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

