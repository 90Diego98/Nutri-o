<?php
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($texto) {
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}

$produtos = [
    // Suplementos
    ['nome' => 'Whey Protein Concentrado', 'descricao' => 'Proteína em pó, 900g, sabor baunilha.', 'preco' => 129.90, 'imagem' => 'imagens/produtos/whey.jpg'],
    ['nome' => 'Creatina Monohidratada', 'descricao' => 'Creatina pura, 250g.', 'preco' => 69.90, 'imagem' => 'imagens/produtos/creatina.jpg'],
    ['nome' => 'Multivitamínico', 'descricao' => 'Complexo vitamínico diário, 120 cápsulas.', 'preco' => 49.90, 'imagem' => 'imagens/produtos/multivitaminico.jpg'],
    ['nome' => 'Ômega 3', 'descricao' => 'Óleo de peixe, 120 cápsulas.', 'preco' => 59.90, 'imagem' => 'imagens/produtos/omega3.jpg'],
    ['nome' => 'Barrinha Proteica (caixa)', 'descricao' => 'Caixa com 12 unidades, sabor chocolate.', 'preco' => 44.90, 'imagem' => 'imagens/produtos/barrinha.jpg'],

    // Acessórios
    ['nome' => 'Coqueteleira', 'descricao' => 'Coqueteleira 600ml com misturador.', 'preco' => 24.90, 'imagem' => 'imagens/produtos/coqueteleira.jpg'],
    ['nome' => 'Luvas de Treino', 'descricao' => 'Luvas para musculação, com proteção na palma.', 'preco' => 39.90, 'imagem' => 'imagens/produtos/luvas.jpg'],
    ['nome' => 'Munhequeira', 'descricao' => 'Par de munhequeiras com ajuste em velcro.', 'preco' => 29.90, 'imagem' => 'imagens/produtos/munhequeira.jpg'],
    ['nome' => 'Cinto de Musculação', 'descricao' => 'Cinto de apoio lombar para treinos pesados.', 'preco' => 79.90, 'imagem' => 'imagens/produtos/cinto.jpg'],
    ['nome' => 'Corda de Pular', 'descricao' => 'Corda ajustável com cabo antiderrapante.', 'preco' => 19.90, 'imagem' => 'imagens/produtos/corda.jpg'],
    ['nome' => 'Faixa Elástica', 'descricao' => 'Mini band de resistência para treinos funcionais.', 'preco' => 24.90, 'imagem' => 'imagens/produtos/faixa-elastica.jpg'],
    ['nome' => 'Toalha de Academia', 'descricao' => 'Toalha de microfibra, secagem rápida.', 'preco' => 22.90, 'imagem' => 'imagens/produtos/toalha.jpg'],
    ['nome' => 'Garrafa Squeeze 1L', 'descricao' => 'Garrafa esportiva de 1 litro com bico retrátil.', 'preco' => 27.90, 'imagem' => 'imagens/produtos/garrafa.jpg'],
    ['nome' => 'Strap de Punho', 'descricao' => 'Par de straps para levantamento, reforça a pegada.', 'preco' => 18.90, 'imagem' => 'imagens/produtos/strap.jpg'],
    ['nome' => 'Mochila de Treino', 'descricao' => 'Mochila esportiva com compartimento para tênis.', 'preco' => 89.90, 'imagem' => 'imagens/produtos/mochila.jpg'],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Loja | Raiz&amp;Nutriente</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,500&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="estiloa.css">
<script>document.documentElement.dataset.theme = localStorage.getItem('tema') || 'dark';</script>
</head>
<body data-page="loja">

<div id="site-header"></div>

<section class="page-hero">
  <div class="wrap">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:20px; flex-wrap:wrap;">
      <div>
        <div class="hero-eyebrow">Loja</div>
        <h1>Suplementos e acessórios selecionados pela nutricionista</h1>
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
        <?php $precoNum = number_format($p['preco'], 2, '.', ''); ?>
        <div class="produto-card">
          <div class="produto-imagem">
            <img src="<?php echo e($p['imagem']); ?>" alt="<?php echo e($p['nome']); ?>" loading="lazy" onerror="this.style.display='none'">
          </div>
          <div class="produto-info">
            <h3><?php echo e($p['nome']); ?></h3>
            <p><?php echo e($p['descricao']); ?></p>
            <div class="produto-preco">R$ <?php echo number_format($p['preco'], 2, ',', '.'); ?></div>
            <div style="display:grid; gap:8px;">
              <a href="checkout.php?valor=<?php echo $precoNum; ?>&produto=<?php echo urlencode($p['nome']); ?>" class="btn-primary" style="width:100%; text-align:center; display:block;">Comprar agora</a>
              <button type="button" class="btn-primary add-carrinho-btn"
                data-nome="<?php echo e($p['nome']); ?>"
                data-preco="<?php echo $precoNum; ?>"
                data-imagem="<?php echo e($p['imagem']); ?>"
                style="width:100%; text-align:center;">
                Adicionar ao carrinho
              </button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <!-- As imagens dos produtos ficam na pasta imagens/produtos. Se uma foto não for encontrada, o espaço da imagem fica em branco automaticamente. -->
  </div>
</section>

<div id="site-footer"></div>

<script>
  window.SITE_SESSION = {
    logged: <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>,
    nome: "<?php echo e($_SESSION['user_nome'] ?? ''); ?>"
  };
</script>
<script src="script.js"></script>
</body>
</html>
