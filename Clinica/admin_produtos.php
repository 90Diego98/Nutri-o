<?php
require_once 'config.php'; // define $pdo

// só admin entra
if (empty($_SESSION['user_id'])) { header('Location: entrar.php'); exit; }
$st = $pdo->prepare('SELECT is_admin FROM usuarios WHERE id = ?');
$st->execute([$_SESSION['user_id']]);
if (!$st->fetchColumn()) { http_response_code(403); exit('Acesso negado.'); }

// token CSRF simples
if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
$csrf = $_SESSION['csrf'];

$msg = ''; $erro = '';
$pasta = __DIR__ . '/imagens/produtos/';
if (!is_dir($pasta)) mkdir($pasta, 0775, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($csrf, $_POST['csrf'] ?? '')) {
        $erro = 'Sessão inválida. Recarregue a página.';
    } elseif (($_POST['acao'] ?? '') === 'adicionar') {
        $nome    = trim($_POST['nome'] ?? '');
        $desc    = trim($_POST['descricao'] ?? '');
        $preco   = (float) str_replace(',', '.', $_POST['preco'] ?? '0');
        $estoque = (int) ($_POST['estoque'] ?? 0);

        if ($nome === '' || $desc === '' || $preco <= 0) {
            $erro = 'Preencha nome, descrição e um valor válido.';
        } elseif ($estoque < 0) {
            $erro = 'O estoque não pode ser negativo.';
        } elseif (empty($_FILES['imagem']['tmp_name']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
            $erro = 'Envie a foto do produto.';
        } else {
            $permitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            $mime = mime_content_type($_FILES['imagem']['tmp_name']);
            if (!isset($permitidos[$mime])) {
                $erro = 'A foto precisa ser JPG, PNG ou WEBP.';
            } elseif ($_FILES['imagem']['size'] > 3 * 1024 * 1024) {
                $erro = 'A foto pode ter no máximo 3 MB.';
            } else {
                $arquivo = 'prod_' . bin2hex(random_bytes(6)) . '.' . $permitidos[$mime];
                if (move_uploaded_file($_FILES['imagem']['tmp_name'], $pasta . $arquivo)) {
                    $ins = $pdo->prepare('INSERT INTO produtos (nome, descricao, preco, imagem, estoque) VALUES (?,?,?,?,?)');
                    $ins->execute([$nome, $desc, $preco, 'imagens/produtos/' . $arquivo, $estoque]);
                    $msg = 'Produto adicionado! Já aparece na loja.';
                } else {
                    $erro = 'Não foi possível salvar a foto.';
                }
            }
        }
    } elseif (($_POST['acao'] ?? '') === 'estoque') {
        $id      = (int) ($_POST['id'] ?? 0);
        $estoque = (int) ($_POST['estoque'] ?? 0);
        if ($id <= 0) {
            $erro = 'Produto inválido.';
        } elseif ($estoque < 0) {
            $erro = 'O estoque não pode ser negativo.';
        } else {
            $pdo->prepare('UPDATE produtos SET estoque = ? WHERE id = ?')->execute([$estoque, $id]);
            $msg = 'Estoque atualizado.';
        }
    } elseif (($_POST['acao'] ?? '') === 'excluir') {
        $id = (int) ($_POST['id'] ?? 0);
        $q = $pdo->prepare('SELECT imagem FROM produtos WHERE id = ?');
        $q->execute([$id]);
        if ($img = $q->fetchColumn()) {
            $pdo->prepare('DELETE FROM produtos WHERE id = ?')->execute([$id]);
            $caminho = __DIR__ . '/' . $img;
            if (is_file($caminho)) @unlink($caminho);
            $msg = 'Produto excluído.';
        }
    }
}

$produtos = $pdo->query('SELECT * FROM produtos ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin • Produtos | Raiz&amp;Nutriente</title>
<style>
  body{font-family:Arial,sans-serif;background:#16141f;color:#fff;margin:0;padding:24px}
  .box{max-width:900px;margin:0 auto}
  h1{margin:8px 0 20px}
  a{color:#f5c518}
  form.novo{background:#1f1d2b;border:1px solid #2e2b3f;padding:20px;border-radius:14px;display:grid;gap:12px}
  label{font-size:.9rem;color:#cfcfe0}
  input[type=text], input[type=number]{padding:12px;border-radius:10px;border:1px solid #3a3750;background:#16141f;color:#fff;font-size:15px}
  input[type=file]{color:#cfcfe0}
  button{padding:12px 20px;border:0;border-radius:999px;background:#f5c518;color:#111;font-weight:bold;cursor:pointer;font-size:15px}
  button.del{background:#c0392b;color:#fff;padding:8px 14px}
  button.salvar{padding:8px 14px;font-size:13px}
  .dica{font-size:.8rem;color:#9a98b0;margin:-6px 0 0}
  .ok{background:#1e7e34;padding:10px 14px;border-radius:10px;margin:12px 0}
  .err{background:#a52a2a;padding:10px 14px;border-radius:10px;margin:12px 0}
  table{width:100%;border-collapse:collapse;margin-top:24px}
  td,th{padding:10px;border-bottom:1px solid #2e2b3f;text-align:left;vertical-align:middle}
  td img{width:56px;height:56px;object-fit:cover;border-radius:8px;background:#fff}
  form.est{display:flex;gap:6px;align-items:center}
  form.est input[type=number]{width:80px;padding:8px}
  .zero{color:#ff6b6b;font-size:.75rem;display:block;margin-top:4px}
</style>
</head>
<body>
<div class="box">
  <p><a href="admin.php">← Painel admin</a> · <a href="loja.php">Ver loja</a></p>
  <h1>Adicionar produto na loja</h1>

  <?php if ($msg): ?><div class="ok"><?= e($msg) ?></div><?php endif; ?>
  <?php if ($erro): ?><div class="err"><?= e($erro) ?></div><?php endif; ?>

  <form class="novo" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
    <input type="hidden" name="acao" value="adicionar">

    <label>Nome do produto</label>
    <input type="text" name="nome" placeholder="Ex.: Luva de Treino" maxlength="120" required>

    <label>Descrição (aparece embaixo do nome)</label>
    <input type="text" name="descricao" placeholder="Ex.: Luvas para musculação, com proteção na palma." maxlength="255" required>

    <label>Valor (R$)</label>
    <input type="text" name="preco" placeholder="Ex.: 59,90" required>

    <label>Quantidade em estoque</label>
    <input type="number" name="estoque" min="0" step="1" value="0" placeholder="Ex.: 20" required>

    <label>Foto</label>
    <input type="file" name="imagem" accept="image/jpeg,image/png,image/webp" required>
    <p class="dica">Dica: use foto com fundo branco ou claro e o produto centralizado, igual às outras.</p>

    <button type="submit">Adicionar produto</button>
  </form>

  <table>
    <tr><th>Foto</th><th>Produto</th><th>Valor</th><th>Estoque</th><th></th></tr>
    <?php foreach ($produtos as $p): ?>
    <tr>
      <td><img src="<?= e($p['imagem']) ?>" alt=""></td>
      <td><?= e($p['nome']) ?><br><small style="color:#9a98b0"><?= e($p['descricao']) ?></small></td>
      <td>R$ <?= number_format((float)$p['preco'], 2, ',', '.') ?></td>
      <td>
        <form class="est" method="post">
          <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
          <input type="hidden" name="acao" value="estoque">
          <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
          <input type="number" name="estoque" min="0" step="1" value="<?= (int)$p['estoque'] ?>">
          <button class="salvar" type="submit">Salvar</button>
        </form>
        <?php if ((int)$p['estoque'] === 0): ?><span class="zero">Esgotado</span><?php endif; ?>
      </td>
      <td>
        <form method="post" onsubmit="return confirm('Excluir este produto?')">
          <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
          <input type="hidden" name="acao" value="excluir">
          <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
          <button class="del" type="submit">Excluir</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$produtos): ?><tr><td colspan="5">Nenhum produto cadastrado ainda.</td></tr><?php endif; ?>
  </table>
</div>
</body>
</html>
