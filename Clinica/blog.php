<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


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
function haQuantoTempo($data) {
    $dias = (int) floor((time() - strtotime($data)) / 86400);
    if ($dias <= 0) return 'hoje';
    return $dias === 1 ? 'há 1 dia' : "há $dias dias";
}

$resumo = $pdo->query("SELECT COUNT(*) total, COALESCE(SUM(nota),0) soma FROM avaliacoes")->fetch();
$avaliacoes = $pdo->query(
    "SELECT a.id, a.nota, a.comentario, a.criado_em, a.resposta, a.resposta_em, u.nome
     FROM avaliacoes a
     JOIN usuarios u ON u.id = a.usuario_id
     ORDER BY a.criado_em DESC"
)->fetchAll();

$logado = !empty($_SESSION['user_id']);
$ehAdmin = false;
if ($logado) {
    $s = $pdo->prepare("SELECT is_admin FROM usuarios WHERE id = ?");
    $s->execute([$_SESSION['user_id']]);
    $ehAdmin = (int)$s->fetchColumn() === 1;
}

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
<style>
.aval-box{max-width:800px;margin:0 auto;padding:20px;background:#111;border-radius:16px;color:#fff}
.aval-box h2{margin:0 0 6px}
.aval-media{color:#aaa;margin-bottom:16px}
.estrelas-input{display:flex;gap:6px;font-size:2.2rem;cursor:pointer;user-select:none}
.estrelas-input span{color:#555;transition:color .15s}
.estrelas-input span.ativa{color:#f5c518}
.aval-box textarea{width:100%;min-height:90px;margin-top:12px;padding:12px;border-radius:12px;border:1px solid #333;background:#1a1a1a;color:#fff;resize:vertical;box-sizing:border-box}
.aval-box textarea.obrigatorio{border-color:#e74c3c}
.aval-box button{margin-top:12px;padding:12px 28px;border:0;border-radius:999px;background:#f5c518;color:#000;font-weight:700;cursor:pointer}
.aval-aviso{display:none;margin-top:12px;padding:12px 14px;border-radius:12px;background:#3a1a1a;border-left:4px solid #e74c3c;color:#ffb3ab}
.aval-item{padding:16px 0;border-top:1px solid #2a2a2a}
.aval-item strong{display:block;font-size:1.05rem}
.aval-item .est{color:#f5c518;letter-spacing:2px}
.aval-item .est i{color:#555;font-style:normal}
.aval-item small{color:#999;margin-left:6px}
.aval-item p{margin:6px 0 0;color:#ccc}
.aval-resposta{margin-top:10px;padding:10px 14px;background:#1a1a1a;border-left:3px solid #f5c518;border-radius:8px}
.aval-resposta strong{display:inline;font-size:.95rem;color:#f5c518}
.aval-resposta p{margin:4px 0 0}
.aval-admin{margin-top:12px;padding-top:10px;border-top:1px dashed #333}
.aval-admin textarea{min-height:60px;margin-top:0}
.aval-admin-btns{display:flex;gap:8px}
.aval-admin button{margin-top:8px;padding:8px 16px;font-size:.9rem}
.aval-admin .btn-apagar{background:#e74c3c;color:#fff}
</style>

<section class="section">
  <div class="wrap">
    <div class="aval-box" id="avaliacoes">
      <h2>Avaliações</h2>

      <div class="aval-media" id="avalMedia"
           data-total="<?= (int)$resumo['total'] ?>" data-soma="<?= (int)$resumo['soma'] ?>">
        <?php if ($resumo['total'] > 0): ?>
          ⭐ <?= number_format($resumo['soma'] / $resumo['total'], 1, ',', '') ?> de 5 · <?= (int)$resumo['total'] ?> avaliações
        <?php else: ?>
          Ainda não há avaliações. Seja o primeiro!
        <?php endif; ?>
      </div>

      <?php if ($logado): ?>
        <div class="estrelas-input" id="estrelasInput">
          <span data-v="1">★</span><span data-v="2">★</span><span data-v="3">★</span><span data-v="4">★</span><span data-v="5">★</span>
        </div>
        <div class="aval-aviso" id="avalAviso"></div>
        <textarea id="avalComentario" maxlength="500" placeholder="Deixe seu comentário (opcional)"></textarea>
        <button id="avalEnviar">Enviar avaliação</button>
      <?php else: ?>
        <p>Entre na sua conta para avaliar.</p>
      <?php endif; ?>

      <div class="aval-lista" id="avalLista">
        <?php foreach ($avaliacoes as $a): ?>
          <div class="aval-item" id="aval-<?= (int)$a['id'] ?>" data-id="<?= (int)$a['id'] ?>" data-nota="<?= (int)$a['nota'] ?>">
            <strong><?= htmlspecialchars($a['nome']) ?></strong>
            <span class="est"><?= str_repeat('★', $a['nota']) ?><i><?= str_repeat('★', 5 - $a['nota']) ?></i></span>
            <small>• <?= haQuantoTempo($a['criado_em']) ?></small>
            <?php if ($a['comentario']): ?>
              <p><?= nl2br(htmlspecialchars($a['comentario'])) ?></p>
            <?php endif; ?>
            <?php if (!empty($a['resposta'])): ?>
              <div class="aval-resposta">
                <strong>Resposta da Raiz&Nutriente</strong>
                <small><?= haQuantoTempo($a['resposta_em']) ?></small>
                <p><?= nl2br(htmlspecialchars($a['resposta'])) ?></p>
              </div>
            <?php endif; ?>

            <?php if ($ehAdmin): ?>
              <div class="aval-admin">
                <textarea maxlength="500" placeholder="Resposta da clínica (deixe vazio e salve para remover)"><?= htmlspecialchars($a['resposta'] ?? '') ?></textarea>
                <div class="aval-admin-btns">
                  <button class="btn-responder"><?= $a['resposta'] ? 'Atualizar resposta' : 'Responder' ?></button>
                  <button class="btn-apagar">Apagar avaliação</button>
                </div>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<script>
(function () {
  const lista = document.getElementById('avalLista');
  const media = document.getElementById('avalMedia');

  function atualizarMedia(dTotal, dSoma) {
    let total = (+media.dataset.total) + dTotal;
    let soma = (+media.dataset.soma) + dSoma;
    media.dataset.total = total;
    media.dataset.soma = soma;
    media.textContent = total > 0
      ? '⭐ ' + (soma / total).toFixed(1).replace('.', ',') + ' de 5 · ' + total + ' avaliações'
      : 'Ainda não há avaliações. Seja o primeiro!';
  }

  /* ---------- Paciente: enviar avaliação ---------- */
  const estrelas = document.querySelectorAll('#estrelasInput span');
  if (estrelas.length) {
    const aviso = document.getElementById('avalAviso');
    const campo = document.getElementById('avalComentario');
    const btnEnviar = document.getElementById('avalEnviar');
    let nota = 0;

    function pintar(n) {
      estrelas.forEach(e => e.classList.toggle('ativa', +e.dataset.v <= n));
    }
    function mostrarAviso(txt) {
      aviso.textContent = txt;
      aviso.style.display = 'block';
    }

    estrelas.forEach(e => {
      e.addEventListener('mouseover', () => pintar(+e.dataset.v));
      e.addEventListener('mouseout', () => pintar(nota));
      e.addEventListener('click', () => {
        nota = +e.dataset.v;
        pintar(nota);
        if (nota < 3) {
          mostrarAviso('Como podemos melhorar?');
          campo.classList.add('obrigatorio');
          campo.placeholder = 'Conte pra gente o que não foi bom (obrigatório)';
          campo.focus();
        } else {
          aviso.style.display = 'none';
          campo.classList.remove('obrigatorio');
          campo.placeholder = 'Deixe seu comentário (opcional)';
        }
      });
    });

    btnEnviar.addEventListener('click', async () => {
      if (!nota) return mostrarAviso('Escolha uma nota de 1 a 5 estrelas.');
      if (nota < 3 && campo.value.trim() === '') {
        campo.classList.add('obrigatorio');
        return mostrarAviso('Como podemos melhorar? Deixe uma mensagem para enviar.');
      }

      btnEnviar.disabled = true;
      const fd = new FormData();
      fd.append('nota', nota);
      fd.append('comentario', campo.value);

      try {
        const r = await fetch('avaliar_process.php', { method: 'POST', body: fd });
        const j = await r.json();
        if (!j.ok) { mostrarAviso(j.msg); btnEnviar.disabled = false; return; }

        const item = document.createElement('div');
        item.className = 'aval-item';
        item.dataset.nota = j.nota;

        const nome = document.createElement('strong');
        nome.textContent = j.nome;

        const est = document.createElement('span');
        est.className = 'est';
        est.append('★'.repeat(j.nota));
        const vazias = document.createElement('i');
        vazias.textContent = '★'.repeat(5 - j.nota);
        est.append(vazias);

        const quando = document.createElement('small');
        quando.textContent = '• hoje';

        item.append(nome, est, quando);
        if (j.comentario) {
          const p = document.createElement('p');
          p.textContent = j.comentario;
          item.append(p);
        }
        lista.prepend(item);

        atualizarMedia(1, j.nota);

        nota = 0;
        pintar(0);
        campo.value = '';
        campo.classList.remove('obrigatorio');
        campo.placeholder = 'Deixe seu comentário (opcional)';
        aviso.style.display = 'none';
      } catch (err) {
        mostrarAviso('Erro ao enviar. Tente novamente.');
      }
      btnEnviar.disabled = false;
    });
  }

  /* ---------- Admin: apagar e responder ---------- */
  lista.addEventListener('click', async (ev) => {
    const item = ev.target.closest('.aval-item');
    if (!item) return;

    if (ev.target.classList.contains('btn-apagar')) {
      if (!confirm('Apagar esta avaliação?')) return;
      const fd = new FormData();
      fd.append('id', item.dataset.id);
      const r = await fetch('admin_avaliacao_delete.php', { method: 'POST', body: fd });
      const j = await r.json();
      if (j.ok) {
        atualizarMedia(-1, -(+item.dataset.nota));
        item.remove();
      } else {
        alert(j.msg || 'Erro ao apagar.');
      }
    }

    if (ev.target.classList.contains('btn-responder')) {
      const campoResp = item.querySelector('.aval-admin textarea');
      const texto = campoResp.value.trim();
      const fd = new FormData();
      fd.append('id', item.dataset.id);
      fd.append('resposta', texto);
      const r = await fetch('admin_avaliacao_responder.php', { method: 'POST', body: fd });
      const j = await r.json();
      if (!j.ok) return alert(j.msg || 'Erro ao salvar.');

      const antiga = item.querySelector('.aval-resposta');
      if (antiga) antiga.remove();

      if (texto) {
        const box = document.createElement('div');
        box.className = 'aval-resposta';
        const t = document.createElement('strong');
        t.textContent = 'Resposta da Raiz&Nutriente';
        const q = document.createElement('small');
        q.textContent = ' hoje';
        const p = document.createElement('p');
        p.textContent = texto;
        box.append(t, q, p);
        item.querySelector('.aval-admin').before(box);
      }
      ev.target.textContent = texto ? 'Atualizar resposta' : 'Responder';
    }
  });
})();
</script>


</body>
</html>
