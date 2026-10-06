<?php
require_once 'config.php';

$logado = isset($_SESSION['user_id']);
$usuario = null;
$pagamentos = [];
$agendamentos = [];
$evolucao = [];

if ($logado) {
    $stmt = $pdo->prepare('SELECT nome, email, foto, foto_aprovada, criado_em FROM usuarios WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $usuario = $stmt->fetch();

    $stmt = $pdo->prepare('SELECT valor, forma, status, cartao_final, criado_em FROM pagamentos WHERE usuario_id = ? ORDER BY criado_em DESC');
    $stmt->execute([$_SESSION['user_id']]);
    $pagamentos = $stmt->fetchAll();

    $stmt = $pdo->prepare('SELECT tipo, formato, data_consulta, horario, observacoes FROM agendamentos WHERE usuario_id = ? ORDER BY data_consulta DESC');
    $stmt->execute([$_SESSION['user_id']]);
    $agendamentos = $stmt->fetchAll();

    $stmt = $pdo->prepare('SELECT peso, observacao, registrado_em FROM evolucao WHERE usuario_id = ? ORDER BY registrado_em ASC');
    $stmt->execute([$_SESSION['user_id']]);
    $evolucao = $stmt->fetchAll();
}

function nomePlano($valor) {
    $valor = (float) $valor;
    if ($valor >= 1290) return 'Acompanhamento trimestral';
    if ($valor >= 480) return 'Acompanhamento mensal';
    if ($valor >= 220) return 'Consulta avulsa';
    return 'Plano personalizado';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Meu Perfil | Raiz&Nutriente</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,500&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="estiloa.css">
</head>
<body data-page="perfil">

<div id="site-header"></div>

<?php if (!$logado): ?>

  <section class="page-hero">
    <div class="wrap">
      <div class="hero-eyebrow">Meu perfil</div>
      <h1>Você precisa entrar</h1>
      <p>Faça login pra ver seus dados, seu plano e sua evolução.</p>
      <div class="hero-actions">
        <a href="entrar.php?voltar=perfil.php" class="btn-primary">Entrar na minha conta</a>
      </div>
    </div>
  </section>

<?php else: ?>

  <section class="page-hero">
    <div class="wrap">
      <div class="perfil-topo">
        <div class="perfil-avatar-wrap">
          <?php if ($usuario['foto'] && $usuario['foto_aprovada']): ?>
            <img src="<?php echo htmlspecialchars($usuario['foto']); ?>" alt="Foto de perfil" class="perfil-avatar" id="avatar-preview">
          <?php else: ?>
            <div class="perfil-avatar perfil-avatar-vazio" id="avatar-preview"><?php echo strtoupper(substr($usuario['nome'], 0, 1)); ?></div>
          <?php endif; ?>
          <label for="foto-input" class="perfil-avatar-editar">Trocar foto</label>
          <input type="file" id="foto-input" accept="image/png, image/jpeg, image/webp" hidden>
          <?php if ($usuario['foto'] && !$usuario['foto_aprovada']): ?>
            <p class="form-note" style="margin-top:8px; max-width:150px;">Foto em análise</p>
          <?php endif; ?>
        </div>
        <div>
          <div class="hero-eyebrow">Meu perfil</div>
          <h1 style="margin-bottom:6px;"> <?php echo htmlspecialchars(explode(' ', $usuario['nome'])[0]); ?></h1>
          <p style="margin-bottom:0;"><?php echo htmlspecialchars($usuario['nome']); ?></p>
          <p style="margin-bottom:0;"><?php echo htmlspecialchars($usuario['email']); ?></p>
          <p class="form-note" style="margin-top:6px;">Membro desde <?php echo date('d/m/Y', strtotime($usuario['criado_em'])); ?></p>
          <p class="form-error" id="foto-erro"></p>
          <p class="form-note" id="foto-sucesso" style="display:none;"></p>
        </div>
      </div>
    </div>
  </section>

  <section class="section alt-bg">
    <div class="wrap">

      <div class="payment-method-box" style="max-width:560px; margin-bottom:28px;">
        <h2 class="payment-method-title">Seu plano atual</h2>
        <?php if (count($pagamentos) > 0): ?>
          <?php $ultimo = $pagamentos[0]; ?>
          <div class="plano-atual-info">
            <div class="plano-nome"><?php echo nomePlano($ultimo['valor']); ?></div>
            <div class="plano-detalhe">
              R$ <?php echo number_format($ultimo['valor'], 2, ',', '.'); ?>
              — pago via <?php echo $ultimo['forma'] === 'cartao' ? 'cartão de crédito' : 'Pix'; ?>
              em <?php echo date('d/m/Y', strtotime($ultimo['criado_em'])); ?>
            </div>
            <div class="plano-status status-<?php echo htmlspecialchars($ultimo['status']); ?>">
              <?php echo ucfirst($ultimo['status']); ?>
            </div>
          </div>
        <?php else: ?>
          <p class="form-note" style="margin-top:0;">Você ainda não tem nenhum pagamento registrado.</p>
          <a href="formaDepagamento.php" class="btn-primary" style="margin-top:12px; display:inline-block;">Ver planos e pagar</a>
        <?php endif; ?>
      </div>

      <?php if (count($pagamentos) > 1): ?>
        <div class="payment-method-box" style="margin-bottom:28px;">
          <h2 class="payment-method-title">Histórico de pagamentos</h2>
          <table class="evolucao-tabela">
            <thead><tr><th>Data</th><th>Valor</th><th>Forma</th><th>Status</th></tr></thead>
            <tbody>
              <?php foreach ($pagamentos as $p): ?>
                <tr>
                  <td><?php echo date('d/m/Y', strtotime($p['criado_em'])); ?></td>
                  <td>R$ <?php echo number_format($p['valor'], 2, ',', '.'); ?></td>
                  <td><?php echo $p['forma'] === 'cartao' ? 'Cartão final ' . htmlspecialchars($p['cartao_final']) : 'Pix'; ?></td>
                  <td><?php echo ucfirst($p['status']); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <div class="payment-method-box" style="margin-bottom:28px;">
        <h2 class="payment-method-title">Minhas consultas</h2>
        <?php if (count($agendamentos) > 0): ?>
          <table class="evolucao-tabela">
            <thead><tr><th>Data</th><th>Horário</th><th>Tipo</th><th>Formato</th></tr></thead>
            <tbody>
              <?php foreach ($agendamentos as $a): ?>
                <tr>
                  <td><?php echo date('d/m/Y', strtotime($a['data_consulta'])); ?></td>
                  <td><?php echo substr($a['horario'], 0, 5); ?></td>
                  <td><?php echo htmlspecialchars($a['tipo']); ?></td>
                  <td><?php echo htmlspecialchars($a['formato']); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p class="form-note" style="margin-top:0;">Nenhuma consulta agendada ainda.</p>
          <a href="index.php#agendar" class="btn-primary" style="margin-top:12px; display:inline-block;">Agendar consulta</a>
        <?php endif; ?>
      </div>

      <div class="payment-method-box">
        <h2 class="payment-method-title">Minha evolução</h2>
        <p class="form-note" style="margin-top:0; margin-bottom:22px;">Registre seu peso ao longo do acompanhamento pra visualizar sua evolução.</p>

        <form id="evolucao-form">
          <div class="form-row" style="margin-bottom:18px;">
            <div class="field light">
              <label for="evolucao-peso">Peso atual (kg)</label>
              <input type="number" id="evolucao-peso" name="peso" min="1" max="500" step="0.1" placeholder="Ex: 72.5" required>
            </div>
            <div class="field light">
              <label for="evolucao-obs">Observação (opcional)</label>
              <input type="text" id="evolucao-obs" name="observacao" placeholder="Ex: Após 1 mês de dieta">
            </div>
          </div>
          <button type="submit" class="submit-btn" style="max-width:260px;">Registrar peso</button>
          <p class="form-error" id="evolucao-erro"></p>
          <div id="evolucao-sucesso" class="success-box"></div>
        </form>

        <?php if (count($evolucao) > 0): ?>
          <?php
            $pesos = array_column($evolucao, 'peso');
            $max = max($pesos);
            $min = min($pesos);
            $faixa = max($max - $min, 1);
          ?>
          <div class="evolucao-chart">
            <?php foreach ($evolucao as $reg):
              $altura = 20 + (($reg['peso'] - $min) / $faixa) * 80;
            ?>
              <div class="evolucao-barra-wrap">
                <div class="evolucao-barra" style="height:<?php echo $altura; ?>%;">
                  <span class="evolucao-valor"><?php echo number_format($reg['peso'], 1, ',', '.'); ?></span>
                </div>
                <div class="evolucao-data"><?php echo date('d/m', strtotime($reg['registrado_em'])); ?></div>
              </div>
            <?php endforeach; ?>
          </div>

          <table class="evolucao-tabela">
            <thead>
              <tr><th>Data</th><th>Peso</th><th>Observação</th></tr>
            </thead>
            <tbody>
              <?php foreach (array_reverse($evolucao) as $reg): ?>
                <tr>
                  <td><?php echo date('d/m/Y', strtotime($reg['registrado_em'])); ?></td>
                  <td><?php echo number_format($reg['peso'], 1, ',', '.'); ?> kg</td>
                  <td><?php echo htmlspecialchars($reg['observacao'] ?? '—'); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p class="form-note">Nenhum registro ainda — adicione o primeiro acima.</p>
        <?php endif; ?>
      </div>
    </div>
  </section>

<?php endif; ?>

<div id="site-footer"></div>

<script>
  window.SITE_SESSION = {
    logged: <?php echo $logado ? 'true' : 'false'; ?>,
    nome: "<?php echo htmlspecialchars($_SESSION['user_nome'] ?? '', ENT_QUOTES); ?>"
  };
</script>
<script src="script.js"></script>
</body>
</html>
