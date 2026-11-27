<?php
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($texto) {
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}

// Produtos agora vêm do banco (cadastrados em admin_produtos.php)
$produtos = $pdo->query(
    'SELECT id, nome, descricao, preco, imagem FROM produtos WHERE ativo = 1 ORDER BY id'
)->fetchAll(PDO::FETCH_ASSOC);
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
<style>
  /* Foto clicável nos cards */
  .produto-imagem { cursor: zoom-in; }

  /* Tela grande do produto */
  .lb-overlay {
    position: fixed; inset: 0; z-index: 900;
    display: none; align-items: center; justify-content: center;
    gap: 16px; padding: 16px;
    background: rgba(0, 0, 0, .88);
  }
  .lb-overlay.aberto { display: flex; }

  .lb-conteudo {
    display: grid; grid-template-columns: minmax(0, 1.3fr) minmax(0, 1fr);
    width: min(960px, 100%); max-height: 90vh;
    background: #fff; color: #1a1a1a;
    border-radius: 18px; overflow: hidden;
  }
  .lb-conteudo img {
    width: 100%; height: 100%; max-height: 90vh;
    object-fit: contain; background: #fff; padding: 16px;
  }
  .lb-info {
    display: flex; flex-direction: column; justify-content: center;
    gap: 12px; padding: 28px;
  }
  .lb-info h3 { font-size: 1.5rem; margin: 0; color: #1a1a1a; }
  .lb-info p { margin: 0; color: #555; }
  .lb-preco { font-size: 1.6rem; font-weight: 800; color: #7a2e1d; }
  .lb-contador { font-size: .85rem; color: #777; }
  .lb-info .btn-primary { width: 100%; text-align: center; display: block; cursor: pointer; }

  .lb-seta, .lb-fechar {
    border: 0; cursor: pointer; color: #fff;
    background: rgba(255, 255, 255, .15);
    border-radius: 50%; line-height: 1; flex-shrink: 0;
    transition: background .2s;
  }
  .lb-seta { width: 52px; height: 52px; font-size: 2rem; }
  .lb-fechar {
    position: absolute; top: 16px; right: 16px;
    width: 44px; height: 44px; font-size: 1.8rem;
  }
  .lb-seta:hover, .lb-fechar:hover { background: rgba(255, 255, 255, .3); }

  @media (max-width: 700px) {
    .lb-overlay { padding: 12px; }
    .lb-conteudo { grid-template-columns: 1fr; overflow-y: auto; }
    .lb-conteudo img { max-height: 42vh; }
    .lb-info { padding: 20px; }
    .lb-seta {
      position: absolute; top: 22%;
      width: 42px; height: 42px; font-size: 1.6rem;
      background: rgba(0, 0, 0, .45);
    }
    .lb-prev { left: 16px; }
    .lb-next { right: 16px; }
  }
</style>
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
    <?php if (empty($produtos)): ?>
      <div class="form-note">Nenhum produto disponível no momento.</div>
    <?php endif; ?>

    <div class="produto-grid">
      <?php foreach ($produtos as $p): ?>
        <?php $precoNum = number_format((float)$p['preco'], 2, '.', ''); ?>
        <div class="produto-card">
          <div class="produto-imagem" role="button" tabindex="0" aria-label="Ampliar <?php echo e($p['nome']); ?>">
            <img src="<?php echo e($p['imagem']); ?>" alt="<?php echo e($p['nome']); ?>" loading="lazy" onerror="this.style.display='none'">
          </div>
          <div class="produto-info">
            <h3><?php echo e($p['nome']); ?></h3>
            <p><?php echo e($p['descricao']); ?></p>
            <div class="produto-preco">R$ <?php echo number_format((float)$p['preco'], 2, ',', '.'); ?></div>
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

<!-- Tela grande do produto -->
<div class="lb-overlay" id="lb-overlay" aria-hidden="true">
  <button type="button" class="lb-fechar" id="lb-fechar" aria-label="Fechar">&times;</button>
  <button type="button" class="lb-seta lb-prev" id="lb-prev" aria-label="Produto anterior">&#8249;</button>

  <div class="lb-conteudo" role="dialog" aria-modal="true" aria-label="Detalhes do produto">
    <img id="lb-img" src="" alt="">
    <div class="lb-info">
      <div class="lb-contador" id="lb-contador"></div>
      <h3 id="lb-nome"></h3>
      <p id="lb-desc"></p>
      <div class="lb-preco" id="lb-preco"></div>
      <a id="lb-comprar" class="btn-primary" href="#">Comprar agora</a>
      <button type="button" id="lb-add" class="btn-primary">Adicionar ao carrinho</button>
    </div>
  </div>

  <button type="button" class="lb-seta lb-next" id="lb-next" aria-label="Próximo produto">&#8250;</button>
</div>

<div id="site-footer"></div>

<script>
  window.SITE_SESSION = {
    logged: <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>,
    nome: "<?php echo e($_SESSION['user_nome'] ?? ''); ?>"
  };
</script>
<script src="script.js"></script>
<script>
(function () {
  var cards = Array.prototype.slice.call(document.querySelectorAll('.produto-card'));
  if (!cards.length) return; // loja vazia: nada a configurar

  var overlay = document.getElementById('lb-overlay');
  var elImg = document.getElementById('lb-img');
  var elNome = document.getElementById('lb-nome');
  var elDesc = document.getElementById('lb-desc');
  var elPreco = document.getElementById('lb-preco');
  var elContador = document.getElementById('lb-contador');
  var elComprar = document.getElementById('lb-comprar');
  var elAdd = document.getElementById('lb-add');
  var atual = 0;
  var timerAdd = null;

  function mostrar(i) {
    atual = (i + cards.length) % cards.length;
    var card = cards[atual];
    var img = card.querySelector('.produto-imagem img');

    elImg.style.display = '';
    elImg.onerror = function () { elImg.style.display = 'none'; };
    elImg.src = img.getAttribute('src');
    elImg.alt = img.getAttribute('alt') || '';
    elNome.textContent = card.querySelector('h3').textContent;
    elDesc.textContent = card.querySelector('.produto-info p').textContent;
    elPreco.textContent = card.querySelector('.produto-preco').textContent;
    elComprar.href = card.querySelector('a.btn-primary').getAttribute('href');
    elContador.textContent = (atual + 1) + ' de ' + cards.length;

    clearTimeout(timerAdd);
    elAdd.textContent = 'Adicionar ao carrinho';
  }

  function abrir(i) {
    mostrar(i);
    overlay.classList.add('aberto');
    overlay.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    document.getElementById('lb-fechar').focus();
  }

  function fechar() {
    overlay.classList.remove('aberto');
    overlay.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  cards.forEach(function (card, i) {
    var area = card.querySelector('.produto-imagem');
    area.addEventListener('click', function () { abrir(i); });
    area.addEventListener('keydown', function (ev) {
      if (ev.key === 'Enter' || ev.key === ' ') {
        ev.preventDefault();
        abrir(i);
      }
    });
  });

  document.getElementById('lb-prev').addEventListener('click', function () { mostrar(atual - 1); });
  document.getElementById('lb-next').addEventListener('click', function () { mostrar(atual + 1); });
  document.getElementById('lb-fechar').addEventListener('click', fechar);

  // Clicar no fundo escuro fecha
  overlay.addEventListener('click', function (ev) {
    if (ev.target === overlay) fechar();
  });

  // Adicionar ao carrinho usa o botão do próprio card
  elAdd.addEventListener('click', function () {
    var botao = cards[atual].querySelector('.add-carrinho-btn');
    if (botao) botao.click();
    elAdd.textContent = 'Adicionado ✓';
    clearTimeout(timerAdd);
    timerAdd = setTimeout(function () {
      elAdd.textContent = 'Adicionar ao carrinho';
    }, 1500);
  });

  // Teclado
  document.addEventListener('keydown', function (ev) {
    if (!overlay.classList.contains('aberto')) return;
    if (ev.key === 'Escape') fechar();
    if (ev.key === 'ArrowLeft') mostrar(atual - 1);
    if (ev.key === 'ArrowRight') mostrar(atual + 1);
  });

  // Deslizar o dedo no celular
  var inicioX = null;
  overlay.addEventListener('touchstart', function (ev) {
    inicioX = ev.touches[0].clientX;
  }, { passive: true });
  overlay.addEventListener('touchend', function (ev) {
    if (inicioX === null) return;
    var diff = ev.changedTouches[0].clientX - inicioX;
    inicioX = null;
    if (Math.abs(diff) > 50) mostrar(diff > 0 ? atual - 1 : atual + 1);
  }, { passive: true });
})();
</script>
</body>
</html>
