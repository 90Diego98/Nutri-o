<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: entrar.php?voltar=admin.php');
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
$csrf = $_SESSION['csrf'];

$abas = [
    'resumo'       => 'Resumo',
    'pagamentos'   => 'Pagamentos',
    'agendamentos' => 'Agendamentos',
    'fotos'        => 'Fotos de perfil',
    'mensagens'    => 'Mensagens',
];
$aba = $_GET['aba'] ?? 'resumo';
if (!isset($abas[$aba])) $aba = 'resumo';

/* ---------------- AÇÕES (POST) ---------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $voltar = $_POST['aba'] ?? 'resumo';
    if (!isset($abas[$voltar])) $voltar = 'resumo';

    if (!hash_equals($csrf, $_POST['csrf'] ?? '')) {
        $_SESSION['flash'] = 'Sessão expirada. Tente de novo.';
        header('Location: admin.php?aba=' . $voltar);
        exit;
    }

    $acao = $_POST['acao'] ?? '';
    $id   = (int) ($_POST['id'] ?? 0);
    $novo = $_POST['novo'] ?? '';

    if ($acao === 'pagamento_status' && in_array($novo, ['pago', 'pendente', 'cancelado'], true)) {
        $pdo->prepare('UPDATE pagamentos SET status = ? WHERE id = ?')->execute([$novo, $id]);
        $_SESSION['flash'] = 'Pagamento atualizado para "' . $novo . '".';
    }

    if ($acao === 'agendamento_status' && in_array($novo, ['pendente', 'confirmado', 'cancelado'], true)) {
        $pdo->prepare('UPDATE agendamentos SET status = ? WHERE id = ?')->execute([$novo, $id]);
        $_SESSION['flash'] = 'Agendamento atualizado para "' . $novo . '".';
    }

    if ($acao === 'foto_aprovar') {
        $pdo->prepare('UPDATE usuarios SET foto_aprovada = 1 WHERE id = ?')->execute([$id]);
        $_SESSION['flash'] = 'Foto aprovada.';
    }

    if ($acao === 'foto_rejeitar') {
        $stmt = $pdo->prepare('SELECT foto FROM usuarios WHERE id = ?');
        $stmt->execute([$id]);
        $f = $stmt->fetchColumn();
        if ($f && strpos($f, 'imagens/perfis/') === 0 && strpos($f, '..') === false && is_file(__DIR__ . '/' . $f)) {
            @unlink(__DIR__ . '/' . $f);
        }
        $pdo->prepare('UPDATE usuarios SET foto = NULL, foto_aprovada = 0 WHERE id = ?')->execute([$id]);
        $_SESSION['flash'] = 'Foto rejeitada e removida.';
    }

    if ($acao === 'msg_alternar') {
        $pdo->prepare('UPDATE contatos SET respondido = 1 - respondido WHERE id = ?')->execute([$id]);
        $_SESSION['flash'] = 'Mensagem atualizada.';
    }

    header('Location: admin.php?aba=' . $voltar);
    exit;
}

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

/* ---------------- FUNÇÕES AUXILIARES ---------------- */
function btn($csrf, $aba, $acao, $id, $label, $extra = [], $confirmar = '') {
    $onsubmit = $confirmar ? ' onsubmit="return confirm(\'' . htmlspecialchars($confirmar, ENT_QUOTES) . '\');"' : '';
    echo '<form method="post" style="display:inline;"' . $onsubmit . '>';
    echo '<input type="hidden" name="csrf" value="' . htmlspecialchars($csrf) . '">';
    echo '<input type="hidden" name="aba" value="' . htmlspecialchars($aba) . '">';
    echo '<input type="hidden" name="acao" value="' . htmlspecialchars($acao) . '">';
    echo '<input type="hidden" name="id" value="' . (int) $id . '">';
    foreach ($extra as $k => $v) {
        echo '<input type="hidden" name="' . htmlspecialchars($k) . '" value="' . htmlspecialchars($v) . '">';
    }
    echo '<button type="submit" class="admin-btn">' . htmlspecialchars($label) . '</button></form> ';
}

function waLink($tel) {
    $d = preg_replace('/\D/', '', (string) $tel);
    if ($d === '') return '';
    if (strlen($d) <= 11) $d = '55' . $d;
    return 'https://wa.me/' . $d;
}

function badge($status) {
    $s = strtolower((string) $status);
    return '<span class="admin-badge st-' . htmlspecialchars($s) . '">' . htmlspecialchars(ucfirst($s)) . '</span>';
}

/* ---------------- DADOS DA ABA ---------------- */
$cont = [
    'pagamentos'   => (int) $pdo->query("SELECT COUNT(*) FROM pagamentos WHERE status = 'pendente'")->fetchColumn(),
    'agendamentos' => (int) $pdo->query("SELECT COUNT(*) FROM agendamentos WHERE status = 'pendente'")->fetchColumn(),
    'fotos'        => (int) $pdo->query("SELECT COUNT(*) FROM usuarios WHERE foto IS NOT NULL AND foto <> '' AND foto_aprovada = 0")->fetchColumn(),
    'mensagens'    => (int) $pdo->query("SELECT COUNT(*) FROM contatos WHERE respondido = 0")->fetchColumn(),
];

$dados = [];
if ($aba === 'pagamentos') {
    $dados = $pdo->query(
        "SELECT p.id, p.valor, p.forma, p.status, p.cartao_final, p.criado_em, u.nome, u.email
           FROM pagamentos p
           LEFT JOIN usuarios u ON u.id = p.usuario_id
          ORDER BY (p.status = 'pendente') DESC, p.criado_em DESC
          LIMIT 100"
    )->fetchAll();
}
if ($aba === 'agendamentos') {
    $dados = $pdo->query(
        "SELECT id, nome, telefone, email, tipo, formato, data_consulta, horario, observacoes, status
           FROM agendamentos
          ORDER BY (status = 'pendente') DESC, data_consulta ASC, horario ASC
          LIMIT 100"
    )->fetchAll();
}
if ($aba === 'fotos') {
    $dados = $pdo->query(
        "SELECT id, nome, email, foto
           FROM usuarios
          WHERE foto IS NOT NULL AND foto <> '' AND foto_aprovada = 0
          ORDER BY id DESC"
    )->fetchAll();
}
if ($aba === 'mensagens') {
    $dados = $pdo->query(
        "SELECT id, nome, email, mensagem, criado_em, respondido
           FROM contatos
          ORDER BY respondido ASC, criado_em DESC
          LIMIT 100"
    )->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel admin | Raiz&Nutriente</title>
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
    <h1>Painel admin</h1>
    <p>Pagamentos, agendamentos, fotos e mensagens em um só lugar.</p>
  </div>
</section>

<section class="section alt-bg">
  <div class="wrap">

    <?php if ($flash): ?>
      <div class="success-box" style="display:block; margin-bottom:20px;"><?php echo htmlspecialchars($flash); ?></div>
    <?php endif; ?>

    <div class="admin-abas">
      <?php foreach ($abas as $k => $nome): ?>
        <a href="admin.php?aba=<?php echo $k; ?>" class="admin-aba <?php echo $aba === $k ? 'ativo' : ''; ?>">
          <?php echo $nome; ?>
          <?php if (isset($cont[$k]) && $cont[$k] > 0): ?><span class="admin-contador"><?php echo $cont[$k]; ?></span><?php endif; ?>
        </a>
      <?php endforeach; ?>
      <a href="admin_blog.php" class="admin-aba">Blog</a>
    </div>

    <?php if ($aba === 'resumo'): ?>

      <div class="admin-stats">
        <a class="admin-stat" href="admin.php?aba=pagamentos"><strong><?php echo $cont['pagamentos']; ?></strong><span>Pagamentos pendentes</span></a>
        <a class="admin-stat" href="admin.php?aba=agendamentos"><strong><?php echo $cont['agendamentos']; ?></strong><span>Agendamentos a confirmar</span></a>
        <a class="admin-stat" href="admin.php?aba=fotos"><strong><?php echo $cont['fotos']; ?></strong><span>Fotos aguardando aprovação</span></a>
        <a class="admin-stat" href="admin.php?aba=mensagens"><strong><?php echo $cont['mensagens']; ?></strong><span>Mensagens sem resposta</span></a>
      </div>

    <?php elseif ($aba === 'pagamentos'): ?>

      <div class="payment-method-box">
        <h2 class="payment-method-title">Pagamentos</h2>
        <p class="form-note" style="margin-top:0;">Ao marcar como <strong>pago</strong>, o paciente libera a Dieta e a Consulta online no perfil (quando o valor é de um plano).</p>
        <?php if (count($dados) === 0): ?>
          <p class="form-note">Nenhum pagamento registrado.</p>
        <?php else: ?>
          <div class="admin-scroll">
          <table class="evolucao-tabela">
            <thead><tr><th>Data</th><th>Paciente</th><th>Valor</th><th>Forma</th><th>Status</th><th>Ações</th></tr></thead>
            <tbody>
              <?php foreach ($dados as $p): ?>
                <tr>
                  <td><?php echo date('d/m/Y H:i', strtotime($p['criado_em'])); ?></td>
                  <td><?php echo htmlspecialchars($p['nome'] ?? '(usuário removido)'); ?><br><small><?php echo htmlspecialchars($p['email'] ?? ''); ?></small></td>
                  <td>R$ <?php echo number_format($p['valor'], 2, ',', '.'); ?></td>
                  <td><?php echo $p['forma'] === 'cartao' ? 'Cartão final ' . htmlspecialchars((string) $p['cartao_final']) : 'Pix'; ?></td>
                  <td><?php echo badge($p['status']); ?></td>
                  <td style="white-space:nowrap;">
                    <?php if ($p['status'] !== 'pago')      btn($csrf, 'pagamentos', 'pagamento_status', $p['id'], 'Confirmar', ['novo' => 'pago']); ?>
                    <?php if ($p['status'] !== 'pendente')  btn($csrf, 'pagamentos', 'pagamento_status', $p['id'], 'Pendente', ['novo' => 'pendente']); ?>
                    <?php if ($p['status'] !== 'cancelado') btn($csrf, 'pagamentos', 'pagamento_status', $p['id'], 'Cancelar', ['novo' => 'cancelado'], 'Cancelar este pagamento?'); ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          </div>
        <?php endif; ?>
      </div>

    <?php elseif ($aba === 'agendamentos'): ?>

      <div class="payment-method-box">
        <h2 class="payment-method-title">Agendamentos</h2>
        <?php if (count($dados) === 0): ?>
          <p class="form-note" style="margin-top:0;">Nenhum agendamento recebido.</p>
        <?php else: ?>
          <div class="admin-scroll">
          <table class="evolucao-tabela">
            <thead><tr><th>Data</th><th>Paciente</th><th>Tipo / formato</th><th>Observações</th><th>Status</th><th>Ações</th></tr></thead>
            <tbody>
              <?php foreach ($dados as $a): $wa = waLink($a['telefone']); ?>
                <tr>
                  <td><?php echo date('d/m/Y', strtotime($a['data_consulta'])); ?><br><small><?php echo substr($a['horario'], 0, 5); ?></small></td>
                  <td>
                    <?php echo htmlspecialchars($a['nome']); ?><br>
                    <small><?php echo htmlspecialchars($a['telefone']); ?> · <?php echo htmlspecialchars($a['email']); ?></small>
                  </td>
                  <td><?php echo htmlspecialchars($a['tipo']); ?><br><small><?php echo htmlspecialchars($a['formato']); ?></small></td>
                  <td><?php echo $a['observacoes'] ? nl2br(htmlspecialchars($a['observacoes'])) : '—'; ?></td>
                  <td><?php echo badge($a['status']); ?></td>
                  <td style="white-space:nowrap;">
                    <?php if ($a['status'] !== 'confirmado') btn($csrf, 'agendamentos', 'agendamento_status', $a['id'], 'Confirmar', ['novo' => 'confirmado']); ?>
                    <?php if ($a['status'] !== 'cancelado')  btn($csrf, 'agendamentos', 'agendamento_status', $a['id'], 'Cancelar', ['novo' => 'cancelado'], 'Cancelar este agendamento?'); ?>
                    <?php if ($a['status'] !== 'pendente')   btn($csrf, 'agendamentos', 'agendamento_status', $a['id'], 'Pendente', ['novo' => 'pendente']); ?>
                    <?php if ($wa): ?><a class="admin-btn" href="<?php echo htmlspecialchars($wa); ?>" target="_blank" rel="noopener">WhatsApp</a><?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          </div>
        <?php endif; ?>
      </div>

    <?php elseif ($aba === 'fotos'): ?>

      <div class="payment-method-box">
        <h2 class="payment-method-title">Fotos de perfil aguardando aprovação</h2>
        <?php if (count($dados) === 0): ?>
          <p class="form-note" style="margin-top:0;">Nenhuma foto pendente.</p>
        <?php else: ?>
          <div class="admin-fotos">
            <?php foreach ($dados as $f): ?>
              <div class="admin-foto-card">
                <img src="<?php echo htmlspecialchars($f['foto']); ?>" alt="Foto de <?php echo htmlspecialchars($f['nome']); ?>">
                <strong><?php echo htmlspecialchars($f['nome']); ?></strong>
                <small><?php echo htmlspecialchars($f['email']); ?></small>
                <div>
                  <?php btn($csrf, 'fotos', 'foto_aprovar', $f['id'], 'Aprovar'); ?>
                  <?php btn($csrf, 'fotos', 'foto_rejeitar', $f['id'], 'Rejeitar', [], 'Rejeitar e apagar esta foto?'); ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

    <?php elseif ($aba === 'mensagens'): ?>

      <div class="payment-method-box">
        <h2 class="payment-method-title">Mensagens de contato</h2>
        <?php if (count($dados) === 0): ?>
          <p class="form-note" style="margin-top:0;">Nenhuma mensagem recebida.</p>
        <?php else: ?>
          <?php foreach ($dados as $m):
            $assunto = rawurlencode('Re: Contato Raiz&Nutriente');
          ?>
            <div class="admin-msg <?php echo $m['respondido'] ? 'respondida' : ''; ?>">
              <div class="admin-msg-topo">
                <strong><?php echo htmlspecialchars($m['nome']); ?></strong>
                <small><?php echo htmlspecialchars($m['email']); ?> · <?php echo date('d/m/Y H:i', strtotime($m['criado_em'])); ?></small>
                <?php echo badge($m['respondido'] ? 'respondida' : 'pendente'); ?>
              </div>
              <p><?php echo nl2br(htmlspecialchars($m['mensagem'])); ?></p>
              <a class="admin-btn" href="mailto:<?php echo htmlspecialchars($m['email']); ?>?subject=<?php echo $assunto; ?>">Responder por e-mail</a>
              <?php btn($csrf, 'mensagens', 'msg_alternar', $m['id'], $m['respondido'] ? 'Reabrir' : 'Marcar como respondida'); ?>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

    <?php endif; ?>

  </div>
</section>

</body>
</html>
