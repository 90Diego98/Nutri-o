<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: entrar.php?voltar=admin_blog.php');
    exit;
}

$stmt = $pdo->prepare('SELECT is_admin FROM usuarios WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$eu = $stmt->fetch();

if (!$eu || empty($eu['is_admin'])) {
    http_response_code(403);
    echo 'Acesso negado.';
    exit;
}

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

$categoriasValidas = ['Receita', 'Artigo', 'Dica'];
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        $erro = 'Sessão expirada. Recarregue a página e tente de novo.';
    } else {
        $acao = $_POST['acao'] ?? '';

        if ($acao === 'criar') {
            $titulo    = trim($_POST['titulo'] ?? '');
            $categoria = trim($_POST['categoria'] ?? '');
            $resumo    = trim($_POST['resumo'] ?? '');
            $conteudo  = trim($_POST['conteudo'] ?? '');
            $publicado = isset($_POST['publicado']) ? 1 : 0;
            $imagemCaminho = null;

            if ($titulo === '' || mb_strlen($titulo) > 150) {
                $erro = 'Informe um título (até 150 caracteres).';
            } elseif (!in_array($categoria, $categoriasValidas, true)) {
                $erro = 'Escolha uma categoria.';
            } elseif ($resumo === '' || mb_strlen($resumo) > 300) {
                $erro = 'Informe um resumo (até 300 caracteres).';
            } elseif ($conteudo === '') {
                $erro = 'Escreva o conteúdo do post.';
            }

            // Upload opcional da imagem
            if ($erro === '' && !empty($_FILES['imagem']['name'])) {
                if ($_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
                    $erro = 'Não foi possível enviar a imagem.';
                } elseif ($_FILES['imagem']['size'] > 3 * 1024 * 1024) {
                    $erro = 'A imagem deve ter no máximo 3 MB.';
                } else {
                    $info = @getimagesize($_FILES['imagem']['tmp_name']);
                    $permitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
                    if (!$info || !isset($permitidos[$info['mime']])) {
                        $erro = 'Use uma imagem JPG, PNG ou WEBP.';
                    } else {
                        $pasta = __DIR__ . '/imagens/blog';
                        if (!is_dir($pasta)) {
                            mkdir($pasta, 0755, true);
                        }
                        $nomeArquivo = bin2hex(random_bytes(8)) . '.' . $permitidos[$info['mime']];
                        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $pasta . '/' . $nomeArquivo)) {
                            $imagemCaminho = 'imagens/blog/' . $nomeArquivo;
                        } else {
                            $erro = 'Falha ao salvar a imagem.';
                        }
                    }
                }
            }

            if ($erro === '') {
                $stmt = $pdo->prepare(
                    'INSERT INTO posts (titulo, categoria, resumo, conteudo, imagem, publicado)
                     VALUES (?, ?, ?, ?, ?, ?)'
                );
                $stmt->execute([$titulo, $categoria, $resumo, $conteudo, $imagemCaminho, $publicado]);
                $_SESSION['flash'] = 'Post criado com sucesso.';
                header('Location: admin_blog.php');
                exit;
            }
        }

        if ($acao === 'alternar') {
            $id = (int) ($_POST['id'] ?? 0);
            $pdo->prepare('UPDATE posts SET publicado = 1 - publicado WHERE id = ?')->execute([$id]);
            header('Location: admin_blog.php');
            exit;
        }

        if ($acao === 'excluir') {
            $id = (int) ($_POST['id'] ?? 0);
            $stmt = $pdo->prepare('SELECT imagem FROM posts WHERE id = ?');
            $stmt->execute([$id]);
            $img = $stmt->fetchColumn();
            if ($img && strpos($img, 'imagens/blog/') === 0 && is_file(__DIR__ . '/' . $img)) {
                unlink(__DIR__ . '/' . $img);
            }
            $pdo->prepare('DELETE FROM posts WHERE id = ?')->execute([$id]);
            $_SESSION['flash'] = 'Post excluído.';
            header('Location: admin_blog.php');
            exit;
        }
    }
}

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

$posts = $pdo->query('SELECT id, titulo, categoria, publicado, criado_em FROM posts ORDER BY criado_em DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gerenciar blog | Raiz&Nutriente</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,500&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="estiloa.css">
</head>
<body>

<div style="padding: 24px 28px 0; text-align: left;">
    <a href="perfil.php" class="btn-primary">Voltar</a>
</div>

<section class="page-hero">
  <div class="wrap">
    <div class="hero-eyebrow">Área da nutricionista</div>
    <h1>Gerenciar blog</h1>
    <p>Crie receitas, artigos e dicas para o site.</p>
  </div>
</section>

<section class="section alt-bg">
  <div class="wrap">

    <?php if ($flash): ?>
      <div class="success-box" style="display:block; margin-bottom:20px;"><?php echo htmlspecialchars($flash); ?></div>
    <?php endif; ?>

    <div class="payment-method-box" style="margin-bottom:28px;">
      <h2 class="payment-method-title">Novo post</h2>

      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf']); ?>">
        <input type="hidden" name="acao" value="criar">

        <div class="form-row" style="margin-bottom:16px;">
          <div class="field light">
            <label for="titulo">Título</label>
            <input type="text" id="titulo" name="titulo" maxlength="150" required value="<?php echo htmlspecialchars($_POST['titulo'] ?? ''); ?>">
          </div>
          <div class="field light">
            <label for="categoria">Categoria</label>
            <select id="categoria" name="categoria" required>
              <option value="">Selecione</option>
              <?php foreach ($categoriasValidas as $c): ?>
                <option <?php echo (($_POST['categoria'] ?? '') === $c) ? 'selected' : ''; ?>><?php echo $c; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="field light" style="margin-bottom:16px;">
          <label for="resumo">Resumo (aparece no card, até 300 caracteres)</label>
          <input type="text" id="resumo" name="resumo" maxlength="300" required value="<?php echo htmlspecialchars($_POST['resumo'] ?? ''); ?>">
        </div>

        <div class="field light" style="margin-bottom:16px;">
          <label for="conteudo">Conteúdo (as quebras de linha são mantidas)</label>
          <textarea id="conteudo" name="conteudo" rows="12" required><?php echo htmlspecialchars($_POST['conteudo'] ?? ''); ?></textarea>
        </div>

        <div class="field light" style="margin-bottom:16px;">
          <label for="imagem">Imagem (opcional, JPG, PNG ou WEBP, até 3 MB)</label>
          <input type="file" id="imagem" name="imagem" accept="image/png, image/jpeg, image/webp">
        </div>

        <label style="display:flex; gap:8px; align-items:center; margin-bottom:20px; color:#e5e7eb;">
          <input type="checkbox" name="publicado" checked> Publicar agora
        </label>

        <button type="submit" class="submit-btn" style="max-width:260px;">Salvar post</button>
        <?php if ($erro): ?>
          <p class="form-error" style="display:block;"><?php echo htmlspecialchars($erro); ?></p>
        <?php endif; ?>
      </form>
    </div>

    <div class="payment-method-box">
      <h2 class="payment-method-title">Posts</h2>
      <?php if (count($posts) === 0): ?>
        <p class="form-note" style="margin-top:0;">Nenhum post criado ainda.</p>
      <?php else: ?>
        <table class="evolucao-tabela">
          <thead><tr><th>Data</th><th>Título</th><th>Categoria</th><th>Status</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($posts as $p): ?>
              <tr>
                <td><?php echo date('d/m/Y', strtotime($p['criado_em'])); ?></td>
                <td><a href="post.php?id=<?php echo (int) $p['id']; ?>" style="text-decoration:underline;"><?php echo htmlspecialchars($p['titulo']); ?></a></td>
                <td><?php echo htmlspecialchars($p['categoria']); ?></td>
                <td><?php echo $p['publicado'] ? 'Publicado' : 'Rascunho'; ?></td>
                <td style="white-space:nowrap;">
                  <form method="post" style="display:inline;">
                    <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf']); ?>">
                    <input type="hidden" name="acao" value="alternar">
                    <input type="hidden" name="id" value="<?php echo (int) $p['id']; ?>">
                    <button type="submit" class="diario-excluir" style="color:#facc15;"><?php echo $p['publicado'] ? 'Despublicar' : 'Publicar'; ?></button>
                  </form>
                  <form method="post" style="display:inline;" onsubmit="return confirm('Excluir este post?');">
                    <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf']); ?>">
                    <input type="hidden" name="acao" value="excluir">
                    <input type="hidden" name="id" value="<?php echo (int) $p['id']; ?>">
                    <button type="submit" class="diario-excluir">Excluir</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

  </div>
</section>

</body>
</html>
