<?php
require_once 'config.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare(
    'SELECT id, titulo, categoria, resumo, conteudo, imagem, criado_em
       FROM posts WHERE id = ? AND publicado = 1'
);
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    http_response_code(404);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $post ? htmlspecialchars($post['titulo']) : 'Post não encontrado'; ?> | Raiz&Nutriente</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,500&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="estiloa.css">
</head>
<body data-page="blog">

<div id="site-header"></div>

<div style="padding: 24px 28px 0; text-align: left;">
    <a href="blog.php" class="btn-primary">Voltar ao blog</a>
</div>

<?php if (!$post): ?>

  <section class="page-hero">
    <div class="wrap">
      <h1>Post não encontrado</h1>
      <p>Esse conteúdo não existe ou não está mais disponível.</p>
    </div>
  </section>

<?php else: ?>

  <section class="page-hero">
    <div class="wrap">
      <div class="hero-eyebrow"><?php echo htmlspecialchars($post['categoria']); ?> · <?php echo date('d/m/Y', strtotime($post['criado_em'])); ?></div>
      <h1><?php echo htmlspecialchars($post['titulo']); ?></h1>
      <p><?php echo htmlspecialchars($post['resumo']); ?></p>
    </div>
  </section>

  <section class="section alt-bg">
    <div class="wrap">
      <div class="payment-method-box blog-post">
        <?php if ($post['imagem']): ?>
          <img src="<?php echo htmlspecialchars($post['imagem']); ?>" alt="<?php echo htmlspecialchars($post['titulo']); ?>" class="blog-post-img">
        <?php endif; ?>
        <div class="blog-post-texto">
          <?php echo nl2br(htmlspecialchars($post['conteudo'])); ?>
        </div>
        <a href="index.php#agendar" class="btn-primary" style="margin-top:24px; display:inline-block;">Agendar consulta</a>
      </div>
    </div>
  </section>

<?php endif; ?>
<script>
  window.SITE_SESSION = {
    logged: <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>,
    nome: "<?php echo htmlspecialchars($_SESSION['user_nome'] ?? '', ENT_QUOTES); ?>"
  };
</script>
<script src="script.js"></script>

</body>
</html>
