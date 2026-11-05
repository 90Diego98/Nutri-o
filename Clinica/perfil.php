<?php
require_once 'config.php';

$logado = isset($_SESSION['user_id']);
$usuario = null;
$pagamentos = [];
$agendamentos = [];
$evolucao = [];

if ($logado) {
    $stmt = $pdo->prepare('SELECT nome, email, foto, foto_aprovada, criado_em, is_admin FROM usuarios WHERE id = ?');
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

// Dados para o gráfico de evolução de peso
$labelsPeso  = [];
$valoresPeso = [];
$obsPeso     = [];
foreach ($evolucao as $r) {
    $labelsPeso[]  = date('d/m/Y', strtotime($r['registrado_em']));
    $valoresPeso[] = (float) $r['peso'];
    $obsPeso[]     = $r['observacao'] ?? '';
}

$pesoInicial = $valoresPeso ? $valoresPeso[0] : null;
$pesoAtual   = $valoresPeso ? end($valoresPeso) : null;
$variacao    = ($pesoInicial !== null) ? round($pesoAtual - $pesoInicial, 1) : null;

/* ---------------------------------------------------------
   LIBERAÇÃO DE ACESSO (Dieta e Consulta Online)
   Só libera quem tem um pagamento de plano (220, 480 ou 1290)
   com status confirmado. Compras da loja não contam.

   - $statusLiberados: valores da coluna "status" que significam pago.
     Confira no phpMyAdmin (tabela pagamentos) como o seu banco grava.
   - $exigirPagamentoConfirmado: deixe false se quiser liberar já
     com o pagamento "pendente" (útil pra testar/apresentar).
--------------------------------------------------------- */
$valoresPlanos = [220.00, 480.00, 1290.00];
$statusLiberados = ['pago', 'aprovado', 'confirmado'];
$exigirPagamentoConfirmado = true;

$temPlano = false;
foreach ($pagamentos as $pg) {
    $valorOk = in_array(round((float) $pg['valor'], 2), $valoresPlanos, true);
    $statusOk = !$exigirPagamentoConfirmado || in_array(strtolower($pg['status']), $statusLiberados, true);
    if ($valorOk && $statusOk) { $temPlano = true; break; }
}

// Administrador tem acesso a tudo, sem precisar pagar
if ($logado && !empty($usuario['is_admin'])) {
    $temPlano = true;
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

<div style="padding: 24px 28px 0; text-align: left;">
    <a href="index.php" class="btn-primary">Voltar</a>
</div>

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
      <div class="perfil-topo" style="margin-top:0;">
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

      <?php if (!empty($usuario['is_admin'])): ?>
        <a href="admin.php" class="btn-primary" style="margin-bottom:28px; display:inline-block;">Painel admin</a>
      <?php endif; ?>

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

          <div class="evolucao-resumo">
            <div class="evolucao-card">
              <span>Peso inicial</span>
              <strong><?php echo number_format($pesoInicial, 1, ',', '.'); ?> kg</strong>
            </div>
            <div class="evolucao-card">
              <span>Peso atual</span>
              <strong><?php echo number_format($pesoAtual, 1, ',', '.'); ?> kg</strong>
            </div>
            <div class="evolucao-card">
              <span>Variação</span>
              <strong><?php echo ($variacao > 0 ? '+' : '') . number_format($variacao, 1, ',', '.'); ?> kg</strong>
            </div>
          </div>

          <?php if (count($valoresPeso) === 1): ?>
            <p class="evolucao-vazia">Registre mais uma vez para ver a linha de evolução.</p>
          <?php endif; ?>

          <div class="evolucao-grafico">
            <canvas id="graficoPeso"></canvas>
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

<?php if ($temPlano): ?>
      <!-- ===================== DIETA (movida de dieta.php) ===================== -->
      <div class="payment-method-box" id="dieta" style="margin-top:28px;">
        <h2 class="payment-method-title">Dieta e calculadora de TMB</h2>
        <p class="form-note" style="margin-top:0; margin-bottom:22px;">Estime sua Taxa de Metabolismo Basal (TMB) e seu Gasto Energético Total (GET), o ponto de partida de qualquer plano alimentar. A fórmula é a de Mifflin-St Jeor.</p>

        <form id="tmb-form">
          <div class="form-row" style="margin-bottom:16px;">
            <div class="field light">
              <label for="tmb-sexo">Sexo biológico</label>
              <select id="tmb-sexo" required>
                <option value="feminino">Feminino</option>
                <option value="masculino">Masculino</option>
              </select>
            </div>
            <div class="field light">
              <label for="tmb-idade">Idade (anos)</label>
              <input type="number" id="tmb-idade" min="10" max="100" placeholder="Ex: 32" required>
            </div>
          </div>
          <div class="form-row" style="margin-bottom:16px;">
            <div class="field light">
              <label for="tmb-peso">Peso (kg)</label>
              <input type="number" id="tmb-peso" min="30" max="300" step="0.1" placeholder="Ex: 68.5" required>
            </div>
            <div class="field light">
              <label for="tmb-altura">Altura (cm)</label>
              <input type="number" id="tmb-altura" min="100" max="230" placeholder="Ex: 170" required>
            </div>
          </div>
          <div class="field light" style="margin-bottom:20px;">
            <label for="tmb-atividade">Nível de atividade física</label>
            <select id="tmb-atividade" required>
              <option value="1.2">Sedentário (pouco ou nenhum exercício)</option>
              <option value="1.375">Leve (exercício leve 1 a 3 dias/semana)</option>
              <option value="1.55" selected>Moderado (exercício moderado 3 a 5 dias/semana)</option>
              <option value="1.725">Intenso (exercício pesado 6 a 7 dias/semana)</option>
              <option value="1.9">Muito intenso (treino físico + trabalho físico)</option>
            </select>
          </div>
          <button type="submit" class="submit-btn" style="max-width:260px;">Calcular</button>
        </form>

        <div class="tmb-results" id="tmb-resultado">
          <div class="tmb-result-card">
            <div class="label">Taxa de Metabolismo Basal (TMB)</div>
            <div class="value" id="tmb-valor">—</div>
          </div>
          <div class="tmb-result-card">
            <div class="label">Gasto Energético Total (GET)</div>
            <div class="value" id="get-valor">—</div>
          </div>
          <div class="macros">
            <div class="macro-chip">
              <div class="m-label">Proteínas / dia</div>
              <div class="m-value" id="macro-proteina">—</div>
            </div>
            <div class="macro-chip">
              <div class="m-label">Carboidratos / dia</div>
              <div class="m-value" id="macro-carbo">—</div>
            </div>
            <div class="macro-chip">
              <div class="m-label">Gorduras / dia</div>
              <div class="m-value" id="macro-gordura">—</div>
            </div>
          </div>
        </div>
        <p class="form-note">Estimativa educativa, que não substitui uma avaliação nutricional individualizada. <a href="index.php#agendar" style="text-decoration:underline;">Agende uma consulta</a> para um plano feito sob medida.</p>

        <h3 style="margin:28px 0 14px;">Boas práticas para o dia a dia</h3>
        <div class="cards-grid cols-4">
          <div class="card">
            <h3>Hidratação</h3>
            <p>Distribua a ingestão de água ao longo do dia, em vez de concentrar tudo à noite.</p>
          </div>
          <div class="card">
            <h3>Fibras</h3>
            <p>Inclua vegetais, frutas e grãos integrais nas principais refeições.</p>
          </div>
          <div class="card">
            <h3>Regularidade</h3>
            <p>Manter horários parecidos de refeição ajuda a regular a fome e a saciedade.</p>
          </div>
          <div class="card">
            <h3>Sono</h3>
            <p>Uma boa noite de sono influencia diretamente o apetite e o metabolismo.</p>
          </div>
        </div>
      </div>
      <!-- ===================== FIM DA DIETA ===================== -->

      <!-- ===================== CALCULADORAS: IMC E ÁGUA ===================== -->
      <div class="payment-method-box" id="calculadoras" style="margin-top:28px;">
        <h2 class="payment-method-title">IMC e meta de água</h2>
        <p class="form-note" style="margin-top:0; margin-bottom:22px;">Calcule seu Índice de Massa Corporal (IMC) e quanto de água beber por dia. O peso já vem preenchido com o seu último registro de evolução, se houver.</p>

        <form id="imc-form">
          <div class="form-row" style="margin-bottom:16px;">
            <div class="field light">
              <label for="imc-peso">Peso (kg)</label>
              <input type="number" id="imc-peso" min="30" max="300" step="0.1" placeholder="Ex: 68.5" value="<?php echo $pesoAtual ? htmlspecialchars((string) $pesoAtual) : ''; ?>" required>
            </div>
            <div class="field light">
              <label for="imc-altura">Altura (cm)</label>
              <input type="number" id="imc-altura" min="100" max="230" placeholder="Ex: 170" required>
            </div>
          </div>
          <div class="field light" style="margin-bottom:20px;">
            <label for="imc-atividade">Nível de atividade física (para a meta de água)</label>
            <select id="imc-atividade" required>
              <option value="30">Sedentário (30 ml por kg)</option>
              <option value="35" selected>Moderado (35 ml por kg)</option>
              <option value="40">Intenso (40 ml por kg)</option>
            </select>
          </div>
          <button type="submit" class="submit-btn" style="max-width:260px;">Calcular</button>
        </form>

        <div class="tmb-results" id="imc-resultado">
          <div class="tmb-result-card">
            <div class="label">Seu IMC</div>
            <div class="value" id="imc-valor">—</div>
          </div>
          <div class="tmb-result-card">
            <div class="label">Classificação</div>
            <div class="value" id="imc-classe">—</div>
          </div>
          <div class="macros">
            <div class="macro-chip">
              <div class="m-label">Faixa de peso saudável</div>
              <div class="m-value" id="imc-faixa">—</div>
            </div>
            <div class="macro-chip">
              <div class="m-label">Meta de água / dia</div>
              <div class="m-value" id="agua-litros">—</div>
            </div>
            <div class="macro-chip">
              <div class="m-label">Em copos de 250 ml</div>
              <div class="m-value" id="agua-copos">—</div>
            </div>
          </div>
        </div>
        <p class="form-note">Estimativas educativas para adultos. O IMC não diferencia massa muscular de gordura e não se aplica a gestantes ou menores de 18 anos. A meta de água pode mudar com clima, saúde e orientação profissional. <a href="index.php#agendar" style="text-decoration:underline;">Agende uma consulta</a> para uma avaliação individual.</p>
      </div>
      <!-- ===================== FIM DAS CALCULADORAS ===================== -->

      <!-- ===================== CONSULTA ONLINE (movida de consultaCMpersonal.php) ===================== -->
      <div class="payment-method-box" id="consulta" style="margin-top:28px;">
        <h2 class="payment-method-title">Consulta online</h2>

        <div id="video-start-box">
          <p class="form-note" style="margin-top:0; margin-bottom:22px;">Inicie a chamada em vídeo direto pelo navegador, sem instalar nenhum aplicativo. Ao entrar, seu navegador vai pedir permissão para usar câmera e microfone. A chamada é privada: só quem tiver o link da sala consegue entrar.</p>
          <form id="video-form">
            <div class="field light" style="margin-bottom:18px; max-width:360px;">
              <label for="video-nome">Seu nome</label>
              <input type="text" id="video-nome" placeholder="Como a nutricionista vai te chamar" value="<?php echo htmlspecialchars(explode(' ', $usuario['nome'])[0]); ?>" required>
            </div>
            <button type="submit" class="submit-btn" style="max-width:260px;">Iniciar videochamada</button>
          </form>
          <p class="form-note">Se a nutricionista ainda não estiver na sala, você entra em uma sala de espera até ela se conectar.</p>
        </div>

        <div id="video-call-box">
          <iframe
            id="video-iframe"
            class="video-frame"
            allow="camera; microphone; fullscreen; display-capture; autoplay"
            allowfullscreen>
          </iframe>
          <div class="video-end-row">
            <button type="button" id="video-end-btn">Encerrar chamada</button>
          </div>
        </div>

        <h3 style="margin:28px 0 14px;">Como funciona</h3>
        <div class="cards-grid" style="grid-template-columns:repeat(3,1fr);">
          <div class="card">
            <h3>1. Agende</h3>
            <p>Marque o formato "Online" no <a href="index.php#agendar">formulário de agendamento</a>.</p>
          </div>
          <div class="card">
            <h3>2. Entre na sala</h3>
            <p>No horário combinado, volte ao seu perfil e inicie a videochamada com seu nome.</p>
          </div>
          <div class="card">
            <h3>3. Converse com a nutricionista</h3>
            <p>A consulta acontece por vídeo, com a mesma qualidade de um atendimento presencial.</p>
          </div>
        </div>
      </div>
      <!-- ===================== FIM DA CONSULTA ONLINE ===================== -->

<?php else: ?>
      <!-- ===================== ÁREA BLOQUEADA (sem plano pago) ===================== -->
      <div class="payment-method-box" id="dieta" style="margin-top:28px; max-width:560px;">
        <div id="consulta"></div>
        <h2 class="payment-method-title">Dieta e Consulta online</h2>
        <?php
          $temPagamentoPlano = false;
          foreach ($pagamentos as $pg) {
              if (in_array(round((float) $pg['valor'], 2), $valoresPlanos, true)) { $temPagamentoPlano = true; break; }
          }
        ?>
        <?php if ($temPagamentoPlano): ?>
          <p class="form-note" style="margin-top:0;">Seu pagamento ainda está pendente. Assim que for confirmado, a calculadora de dieta e a videochamada são liberadas aqui.</p>
        <?php else: ?>
          <p class="form-note" style="margin-top:0;">A calculadora de dieta e a consulta por videochamada são exclusivas para quem contrata um dos nossos planos.</p>
          <a href="formaDepagamento.php" class="btn-primary" style="margin-top:12px; display:inline-block;">Ver planos e pagar</a>
        <?php endif; ?>
      </div>
<?php endif; ?>

    </div>
  </section>

<?php endif; ?>

<script>
  window.SITE_SESSION = {
    logged: <?php echo $logado ? 'true' : 'false'; ?>,
    nome: "<?php echo htmlspecialchars($_SESSION['user_nome'] ?? '', ENT_QUOTES); ?>"
  };
</script>

<?php if (count($valoresPeso) > 0): ?>
<!-- ===================== GRÁFICO DE EVOLUÇÃO DE PESO ===================== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    const labels  = <?php echo json_encode($labelsPeso); ?>;
    const valores = <?php echo json_encode($valoresPeso); ?>;
    const obs     = <?php echo json_encode($obsPeso, JSON_UNESCAPED_UNICODE); ?>;

    const canvas = document.getElementById('graficoPeso');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    const gradiente = ctx.createLinearGradient(0, 0, 0, 320);
    gradiente.addColorStop(0, 'rgba(250, 204, 21, 0.35)');
    gradiente.addColorStop(1, 'rgba(250, 204, 21, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Peso (kg)',
                data: valores,
                borderColor: '#facc15',
                backgroundColor: gradiente,
                borderWidth: 3,
                pointBackgroundColor: '#facc15',
                pointBorderColor: '#0f0f1a',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1a1a2e',
                    titleColor: '#fff',
                    bodyColor: '#facc15',
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    padding: 12,
                    callbacks: {
                        label: function (item) {
                            return item.parsed.y.toString().replace('.', ',') + ' kg';
                        },
                        afterLabel: function (item) {
                            const o = obs[item.dataIndex];
                            return o ? o : '';
                        }
                    }
                }
            },
            scales: {
                x: {
                    ticks: { color: '#9ca3af' },
                    grid: { color: 'rgba(255,255,255,0.05)' }
                },
                y: {
                    ticks: {
                        color: '#9ca3af',
                        callback: function (v) { return v + ' kg'; }
                    },
                    grid: { color: 'rgba(255,255,255,0.05)' }
                }
            }
        }
    });
})();
</script>
<?php endif; ?>

<script src="script.js"></script>
</body>
</html>
