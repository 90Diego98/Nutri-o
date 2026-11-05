<?php
require_once 'config.php';

$categoriasValidas = ['Receita', 'Artigo', 'Dica'];
$cat = $_GET['categoria'] ?? '';
if ($cat !== '' && !in_array($cat, $categoriasValidas, true)) {
    $cat = '';
}

if ($cat !== '') {
    $stmt = $pdo->prepare(
        'SELECT id, titulo, categoria, resumo, imagem, criado_em
           FROM posts WHERE publicado = 1 AND categoria = ?
          ORDER BY criado_em DESC'
    );
    $stmt->execute([$cat]);
} else {
    $stmt = $pdo->query(
        'SELECT id, titulo, categoria, resumo, imagem, criado_em
           FROM posts WHERE publicado = 1
          ORDER BY criado_em DESC'
    );
}
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Blog | Raiz&Nutriente</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,500&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="estiloa.css">
</head>
<body data-page="blog">

<a href="index.php" class="btn-primary" style="position:fixed; top:20px; left:20px; z-index:100;">Voltar</a>


<div id="site-header"></div>


<section class="page-hero">
  <div class="wrap">
    <div class="hero-eyebrow">Blog</div>
    <h1>Receitas e dicas da nutricionista</h1>
    <p>Receitas práticas, artigos e dicas para o seu dia a dia.</p>
  </div>
</section>

<section class="section alt-bg">
  <div class="wrap">

    <div class="blog-filtros">
      <a href="blog.php" class="blog-chip <?php echo $cat === '' ? 'ativo' : ''; ?>">Todos</a>
      <?php foreach ($categoriasValidas as $c): ?>
        <a href="blog.php?categoria=<?php echo urlencode($c); ?>" class="blog-chip <?php echo $cat === $c ? 'ativo' : ''; ?>"><?php echo $c; ?></a>
      <?php endforeach; ?>
    </div>

    <?php if (count($posts) === 0): ?>
      <p class="form-note">Ainda não há posts publicados por aqui. Volte em breve!</p>
    <?php else: ?>
      <div class="blog-grid">
        <?php foreach ($posts as $p): ?>
          <a class="blog-card" href="post.php?id=<?php echo (int) $p['id']; ?>">
            <?php if ($p['imagem']): ?>
              <img src="<?php echo htmlspecialchars($p['imagem']); ?>" alt="<?php echo htmlspecialchars($p['titulo']); ?>" class="blog-card-img">
            <?php else: ?>
              <div class="blog-card-img blog-card-img-vazio"></div>
            <?php endif; ?>
            <div class="blog-card-corpo">
              <span class="blog-tag"><?php echo htmlspecialchars($p['categoria']); ?></span>
              <h3><?php echo htmlspecialchars($p['titulo']); ?></h3>
              <p><?php echo htmlspecialchars($p['resumo']); ?></p>
              <small><?php echo date('d/m/Y', strtotime($p['criado_em'])); ?> · Ler mais →</small>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>
</section>

</body>
</html>
