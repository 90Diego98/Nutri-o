document.addEventListener('DOMContentLoaded', function () {
  renderHeader();
  renderFooter();
  initMobileMenu();
  initAuthPage();
  initScheduleForm();
  initTMBCalculator();
  initPixGenerator();
  initVideoCall();
  initContactForm();
  initWhatsappButton();
  initCardForm();
  initValorPreset('pix');
  initValorPreset('cartao');
  initEvolucaoForm();
  initFotoPerfil();
});

/* ---------------------------------------------------------
   window.SITE_SESSION é definido em cada página .php, logo
   após a tag <body>, com os dados da sessão PHP atual:
   window.SITE_SESSION = { logged: true/false, nome: "..." };
--------------------------------------------------------- */
function getSession() {
  return window.SITE_SESSION || { logged: false, nome: '' };
}

/* ---------------------------------------------------------
   CABEÇALHO E RODAPÉ (injetados em todas as páginas)
--------------------------------------------------------- */
function renderHeader() {
  const headerEl = document.getElementById('site-header');
  if (!headerEl) return;

  const page = document.body.dataset.page || '';
  const session = getSession();

  const links = [
    { href: 'index.php', label: 'Início', key: 'index' },
    { href: 'Sobre_Nos.php', label: 'Sobre Nós', key: 'sobre' },
    { href: 'planosDeProjetiho.php', label: 'Planos', key: 'planos' },
    { href: 'dieta.php', label: 'Dieta', key: 'dieta' },
    { href: 'consultaCMpersonal.php', label: 'Consulta Online', key: 'consulta' },
    { href: 'formaDepagamento.php', label: 'Pagamento', key: 'pagamento' },
    ...(session.logged ? [{ href: 'perfil.php', label: 'Meu Perfil', key: 'perfil' }] : []),
    { href: 'contatos.php', label: 'Contato', key: 'contato' }
  ];

  const linksHtml = links
    .map(l => `<li><a href="${l.href}" class="${page === l.key ? 'active' : ''}">${l.label}</a></li>`)
    .join('');

  const authHtml = session.logged
    ? `<a href="perfil.php" class="nav-user" style="text-decoration:none; display:flex; align-items:center; gap:8px;"><span class="nav-avatar" id="nav-avatar"></span>Olá, ${escapeHtml(session.nome)}</a><a href="logout.php" class="nav-logout">Sair</a>`
    : `<a href="entrar.php" class="nav-login" style="text-decoration:none; display:inline-block;">Entrar</a>`;

  headerEl.innerHTML = `
    <header>
      <nav>
        <a href="index.php" class="brand">Raiz<span>&</span>Nutriente</a>
        <button class="menu-btn" id="menu-btn" aria-label="Abrir menu">&#9776;</button>
        <ul class="nav-links" id="nav-links">${linksHtml}</ul>
        <div class="nav-actions">
          ${authHtml}
          <a href="index.php#agendar" class="nav-cta">Agendar consulta</a>
        </div>
      </nav>
    </header>
  `;

  if (session.logged) loadNavAvatar();
}

function loadNavAvatar() {
  const avatarEl = document.getElementById('nav-avatar');
  if (!avatarEl) return;

  fetch('avatar_info.php')
    .then(r => r.json())
    .then(data => {
      if (data.foto) {
        avatarEl.innerHTML = `<img src="${data.foto}" alt="">`;
      }
    })
    .catch(() => {});
}

function renderFooter() {
  const footerEl = document.getElementById('site-footer');
  if (!footerEl) return;

  footerEl.innerHTML = `
    <footer>
      <div class="wrap">
        <div class="footer-grid">
          <div>
            <div class="footer-brand">Raiz&Nutriente</div>
            <p>Clínica de nutrição dedicada a planos alimentares realistas e acompanhamento contínuo.</p>
          </div>
          <div class="footer-col">
            <h4>Contato</h4>
            <a href="tel:+5500000000000">(00) 0000-0000</a>
            <a href="mailto:contato@raizenutriente.com.br">contato@raizenutriente.com.br</a>
            <p>Rua Exemplo, 123 — Centro</p>
          </div>
          <div class="footer-col">
            <h4>Horário</h4>
            <p>Seg a sex — 8h às 18h</p>
            <p>Sáb — 8h às 12h</p>
          </div>
        </div>
        <div class="footer-bottom">© 2026 Raiz&Nutriente. Todos os direitos reservados.</div>
      </div>
    </footer>
  `;
}

function escapeHtml(str) {
  const div = document.createElement('div');
  div.textContent = str || '';
  return div.innerHTML;
}

/* ---------------------------------------------------------
   MENU MOBILE
--------------------------------------------------------- */
function initMobileMenu() {
  const btn = document.getElementById('menu-btn');
  const links = document.getElementById('nav-links');
  if (!btn || !links) return;
  btn.addEventListener('click', () => links.classList.toggle('open'));
}

/* ---------------------------------------------------------
   LOGIN / CADASTRO (entrar.php) — via fetch para login.php e register.php
--------------------------------------------------------- */
function initAuthPage() {
  const loginBox = document.getElementById('login-box');
  if (!loginBox) return;

  const registerBox = document.getElementById('register-box');
  document.getElementById('show-register').addEventListener('click', () => {
    loginBox.style.display = 'none';
    registerBox.style.display = 'block';
  });
  document.getElementById('show-login').addEventListener('click', () => {
    registerBox.style.display = 'none';
    loginBox.style.display = 'block';
  });

  const params = new URLSearchParams(window.location.search);
  const destino = params.get('voltar') || 'index.php';

  document.getElementById('login-form').addEventListener('submit', function (e) {
    e.preventDefault();
    const erroEl = document.getElementById('login-erro');
    erroEl.textContent = '';

    fetch('login.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        email: document.getElementById('login-email').value,
        senha: document.getElementById('login-senha').value
      })
    })
      .then(r => r.json())
      .then(data => {
        if (data.ok) {
          window.location.href = destino;
        } else {
          erroEl.textContent = data.msg || 'Não foi possível entrar.';
        }
      })
      .catch(() => { erroEl.textContent = 'Erro de conexão. Tente novamente.'; });
  });

  document.getElementById('register-form').addEventListener('submit', function (e) {
    e.preventDefault();
    const erroEl = document.getElementById('register-erro');
    erroEl.textContent = '';

    fetch('register.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        nome: document.getElementById('register-nome').value,
        email: document.getElementById('register-email').value,
        senha: document.getElementById('register-senha').value
      })
    })
      .then(r => r.json())
      .then(data => {
        if (data.ok) {
          window.location.href = destino;
        } else {
          erroEl.textContent = data.msg || 'Não foi possível criar a conta.';
        }
      })
      .catch(() => { erroEl.textContent = 'Erro de conexão. Tente novamente.'; });
  });
}

/* ---------------------------------------------------------
   AGENDAMENTO — via fetch para agendar_process.php
--------------------------------------------------------- */
function initScheduleForm() {
  const form = document.getElementById('schedule-form');
  if (!form) return;

  const dataInput = document.getElementById('data');
  if (dataInput) dataInput.min = new Date().toISOString().split('T')[0];

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    const successBox = document.getElementById('form-success');
    const submitBtn = form.querySelector('.submit-btn');
    submitBtn.disabled = true;

    fetch('agendar_process.php', {
      method: 'POST',
      body: new FormData(form)
    })
      .then(r => r.json())
      .then(data => {
        successBox.textContent = data.msg;
        successBox.style.display = 'block';
        successBox.style.borderColor = data.ok ? 'var(--gold)' : 'var(--berry)';
        if (data.ok) form.reset();
      })
      .catch(() => {
        successBox.textContent = 'Erro de conexão. Tente novamente.';
        successBox.style.display = 'block';
      })
      .finally(() => { submitBtn.disabled = false; });
  });
}

/* ---------------------------------------------------------
   CALCULADORA DE TMB (Mifflin-St Jeor) — só front-end
--------------------------------------------------------- */
function initTMBCalculator() {
  const form = document.getElementById('tmb-form');
  if (!form) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    const sexo = document.getElementById('tmb-sexo').value;
    const idade = parseFloat(document.getElementById('tmb-idade').value);
    const peso = parseFloat(document.getElementById('tmb-peso').value);
    const altura = parseFloat(document.getElementById('tmb-altura').value);
    const atividade = parseFloat(document.getElementById('tmb-atividade').value);

    if (!idade || !peso || !altura || !atividade) return;

    const tmb = sexo === 'masculino'
      ? (10 * peso + 6.25 * altura - 5 * idade + 5)
      : (10 * peso + 6.25 * altura - 5 * idade - 161);

    const get = tmb * atividade;
    const proteina = (get * 0.25) / 4;
    const carbo = (get * 0.45) / 4;
    const gordura = (get * 0.30) / 9;

    document.getElementById('tmb-valor').textContent = Math.round(tmb) + ' kcal/dia';
    document.getElementById('get-valor').textContent = Math.round(get) + ' kcal/dia';
    document.getElementById('macro-proteina').textContent = Math.round(proteina) + ' g';
    document.getElementById('macro-carbo').textContent = Math.round(carbo) + ' g';
    document.getElementById('macro-gordura').textContent = Math.round(gordura) + ' g';

    document.getElementById('tmb-resultado').style.display = 'grid';
    document.getElementById('tmb-resultado').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  });
}

/* ---------------------------------------------------------
   PAGAMENTO VIA PIX — o payload agora é gerado no servidor
   (pix_process.php), que também registra o pagamento no banco.
   Aqui só desenhamos o QR Code a partir do payload recebido.
--------------------------------------------------------- */
function initPixGenerator() {
  const form = document.getElementById('pix-form');
  if (!form) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    const erroEl = document.getElementById('pix-erro');
    erroEl.textContent = '';

    fetch('pix_process.php', {
      method: 'POST',
      body: new FormData(form)
    })
      .then(r => r.json())
      .then(data => {
        if (!data.ok) {
          erroEl.textContent = data.msg || 'Não foi possível gerar o Pix.';
          return;
        }

        document.getElementById('pix-copia-cola').value = data.payload;
        document.getElementById('pix-resultado').style.display = 'block';

        const qrContainer = document.getElementById('qrcode');
        qrContainer.innerHTML = '';
        // eslint-disable-next-line no-undef
        new QRCode(qrContainer, {
          text: data.payload,
          width: 200,
          height: 200,
          colorDark: '#1F3A2E',
          colorLight: '#FFFFFF'
        });
      })
      .catch(() => { erroEl.textContent = 'Erro de conexão. Tente novamente.'; });
  });

  const copyBtn = document.getElementById('pix-copy-btn');
  if (copyBtn) {
    copyBtn.addEventListener('click', function () {
      const field = document.getElementById('pix-copia-cola');
      field.select();
      navigator.clipboard.writeText(field.value).then(() => {
        copyBtn.textContent = 'Copiado!';
        setTimeout(() => { copyBtn.textContent = 'Copiar código'; }, 1800);
      });
    });
  }
}

/* ---------------------------------------------------------
   VIDEOCHAMADA — usa o Jitsi Meet (gratuito, sem backend)
--------------------------------------------------------- */
function initVideoCall() {
  const form = document.getElementById('video-form');
  if (!form) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    const nomeRaw = document.getElementById('video-nome').value.trim();
    const nome = nomeRaw.replace(/[^a-zA-Z0-9]/g, '');
    const sala = 'RaizNutriente' + nome + Math.floor(Math.random() * 10000);

    const iframe = document.getElementById('video-iframe');
    iframe.src = 'https://meet.jit.si/' + encodeURIComponent(sala) +
      '#userInfo.displayName="' + encodeURIComponent(nomeRaw) + '"';

    document.getElementById('video-start-box').style.display = 'none';
    document.getElementById('video-call-box').style.display = 'block';
  });

  const endBtn = document.getElementById('video-end-btn');
  if (endBtn) {
    endBtn.addEventListener('click', function () {
      document.getElementById('video-iframe').src = '';
      document.getElementById('video-call-box').style.display = 'none';
      document.getElementById('video-start-box').style.display = 'block';
    });
  }
}

/* ---------------------------------------------------------
   CONTATO — via fetch para contato_process.php
--------------------------------------------------------- */
function initContactForm() {
  const form = document.getElementById('contact-form');
  if (!form) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    const successBox = document.getElementById('contact-success');
    const submitBtn = form.querySelector('.submit-btn');
    submitBtn.disabled = true;

    fetch('contato_process.php', {
      method: 'POST',
      body: new FormData(form)
    })
      .then(r => r.json())
      .then(data => {
        successBox.textContent = data.msg;
        successBox.style.display = 'block';
        successBox.style.borderColor = data.ok ? 'var(--gold)' : 'var(--berry)';
        if (data.ok) form.reset();
      })
      .catch(() => {
        successBox.textContent = 'Erro de conexão. Tente novamente.';
        successBox.style.display = 'block';
      })
      .finally(() => { submitBtn.disabled = false; });
  });
}

/* ---------------------------------------------------------
   Botão flutuante do WhatsApp — aparece em todas as páginas
--------------------------------------------------------- */
function initWhatsappButton() {
  const numero = '5517988153348'; // formato: código do país + DDD + número, sem espaços/símbolos
  const mensagem = encodeURIComponent('Olá! Vim pelo site da Raiz&Nutriente e gostaria de saber mais.');

  const btn = document.createElement('a');
  btn.href = `https://wa.me/${numero}?text=${mensagem}`;
  btn.target = '_blank';
  btn.rel = 'noopener';
  btn.className = 'whatsapp-float';
  btn.setAttribute('aria-label', 'Falar no WhatsApp');
  btn.innerHTML = `
    <svg viewBox="0 0 32 32" width="28" height="28" fill="#fff">
      <path d="M16.004 3C9.376 3 4 8.373 4 15c0 2.42.71 4.673 1.936 6.567L4 29l7.62-1.902A11.93 11.93 0 0 0 16.004 27C22.63 27 28 21.627 28 15S22.63 3 16.004 3zm0 21.6c-1.99 0-3.85-.57-5.42-1.55l-.39-.23-4.52 1.13 1.2-4.4-.25-.4a9.58 9.58 0 0 1-1.5-5.15c0-5.3 4.32-9.6 9.63-9.6 5.3 0 9.62 4.3 9.62 9.6 0 5.3-4.32 9.6-9.63 9.6zm5.28-7.19c-.29-.15-1.7-.84-1.96-.93-.26-.1-.46-.15-.65.14-.19.29-.75.93-.92 1.12-.17.19-.34.22-.63.07-.29-.14-1.22-.45-2.32-1.43-.86-.76-1.44-1.71-1.6-2-.17-.29-.02-.44.13-.59.13-.13.29-.34.44-.51.15-.17.19-.29.29-.48.1-.19.05-.36-.02-.51-.07-.14-.65-1.57-.89-2.15-.23-.56-.47-.48-.65-.49-.17-.01-.36-.01-.55-.01-.19 0-.51.07-.78.36-.26.29-1.03 1.01-1.03 2.46 0 1.45 1.06 2.85 1.2 3.04.15.19 2.09 3.19 5.07 4.47.71.31 1.26.49 1.69.62.71.23 1.35.2 1.86.12.57-.08 1.7-.7 1.94-1.37.24-.67.24-1.24.17-1.37-.07-.12-.26-.19-.55-.34z"/>
    </svg>`;
  document.body.appendChild(btn);
}

/* ---------------------------------------------------------
   ABAS DE PAGAMENTO (Pix / Cartão)
--------------------------------------------------------- */
function initPaymentTabs() {
  const tabs = document.querySelectorAll('.payment-tab');
  if (!tabs.length) return;

  tabs.forEach(tab => {
    tab.addEventListener('click', function () {
      tabs.forEach(t => t.classList.remove('active'));
      this.classList.add('active');

      document.querySelectorAll('.payment-panel').forEach(p => p.style.display = 'none');
      const alvo = document.getElementById('panel-' + this.dataset.tab);
      if (alvo) alvo.style.display = 'block';
    });
  });
}

/* ---------------------------------------------------------
   CARTÃO DE CRÉDITO — via fetch para cartao_process.php
   (simulação: não há integração real com operadora de cartão)
--------------------------------------------------------- */
function initCardForm() {
  const form = document.getElementById('cartao-form');
  if (!form) return;

  const numeroInput = document.getElementById('cartao-numero');
  numeroInput.addEventListener('input', function () {
    this.value = this.value.replace(/\D/g, '').slice(0, 19).replace(/(\d{4})(?=\d)/g, '$1 ');
  });

  const validadeInput = document.getElementById('cartao-validade');
  validadeInput.addEventListener('input', function () {
    let v = this.value.replace(/\D/g, '').slice(0, 4);
    if (v.length >= 3) v = v.slice(0, 2) + '/' + v.slice(2);
    this.value = v;
  });

  const cvvInput = document.getElementById('cartao-cvv');
  cvvInput.addEventListener('input', function () {
    this.value = this.value.replace(/\D/g, '').slice(0, 4);
  });

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    const erroEl = document.getElementById('cartao-erro');
    const resultBox = document.getElementById('cartao-resultado');
    const submitBtn = form.querySelector('.submit-btn');
    erroEl.textContent = '';
    resultBox.style.display = 'none';
    submitBtn.disabled = true;
    submitBtn.textContent = 'Processando...';

    fetch('cartao_process.php', {
      method: 'POST',
      body: new FormData(form)
    })
      .then(r => r.json())
      .then(data => {
        if (!data.ok) {
          erroEl.textContent = data.msg || 'Não foi possível processar o pagamento.';
          return;
        }
        resultBox.textContent = data.msg;
        resultBox.style.display = 'block';
        resultBox.style.borderColor = 'var(--accent)';
        form.reset();
      })
      .catch(() => { erroEl.textContent = 'Erro de conexão. Tente novamente.'; })
      .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Pagar com cartão';
      });
  });
}

/* ---------------------------------------------------------
   VALORES PRÉ-SELECIONADOS (Pix e Cartão) — planos fixos
   com opção de digitar um valor personalizado
--------------------------------------------------------- */
function initValorPreset(prefixo) {
  const select = document.getElementById(prefixo + '-valor-select');
  const wrap = document.getElementById(prefixo + '-valor-custom-wrap');
  const input = document.getElementById(prefixo + '-valor');
  if (!select || !wrap || !input) return;

  function atualizar() {
    if (select.value === 'outro') {
      wrap.style.display = 'block';
      input.value = '';
      input.focus();
    } else {
      wrap.style.display = 'none';
      input.value = select.value;
    }
  }

  select.addEventListener('change', atualizar);
  atualizar(); // já preenche com o primeiro valor da lista
}

/* ---------------------------------------------------------
   EVOLUÇÃO DE PESO (perfil.php) — via fetch para evolucao_process.php
--------------------------------------------------------- */
function initEvolucaoForm() {
  const form = document.getElementById('evolucao-form');
  if (!form) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    const erroEl = document.getElementById('evolucao-erro');
    const sucessoEl = document.getElementById('evolucao-sucesso');
    const submitBtn = form.querySelector('.submit-btn');
    erroEl.textContent = '';
    sucessoEl.style.display = 'none';
    submitBtn.disabled = true;

    fetch('evolucao_process.php', {
      method: 'POST',
      body: new FormData(form)
    })
      .then(r => r.json())
      .then(data => {
        if (!data.ok) {
          erroEl.textContent = data.msg || 'Não foi possível salvar.';
          return;
        }
        sucessoEl.textContent = data.msg;
        sucessoEl.style.display = 'block';
        setTimeout(() => { window.location.reload(); }, 900);
      })
      .catch(() => { erroEl.textContent = 'Erro de conexão. Tente novamente.'; })
      .finally(() => { submitBtn.disabled = false; });
  });
}

/* ---------------------------------------------------------
   FOTO DE PERFIL (perfil.php) — via fetch para foto_process.php
--------------------------------------------------------- */
function initFotoPerfil() {
  const input = document.getElementById('foto-input');
  if (!input) return;

  const erroEl = document.getElementById('foto-erro');
  const sucessoEl = document.getElementById('foto-sucesso');

  input.addEventListener('change', function () {
    if (!this.files || !this.files[0]) return;
    erroEl.textContent = '';
    sucessoEl.style.display = 'none';

    const formData = new FormData();
    formData.append('foto', this.files[0]);

    fetch('foto_process.php', {
      method: 'POST',
      body: formData
    })
      .then(r => r.json())
      .then(data => {
        if (!data.ok) {
          erroEl.textContent = data.msg || 'Não foi possível enviar a foto.';
          return;
        }
        sucessoEl.textContent = data.msg;
        sucessoEl.style.display = 'block';
        setTimeout(() => { window.location.reload(); }, 1400);
      })
      .catch(() => { erroEl.textContent = 'Erro de conexão. Tente novamente.'; });
  });
}
