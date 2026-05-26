<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/src/auth.php';

$db = getDB();

$fotos = $db->query("
    SELECT f.id, f.url, f.legenda, g.titulo AS galeria_titulo, g.tipo
    FROM fotos f
    JOIN galerias g ON g.id = f.galeria_id
    WHERE g.publicado = 1
    ORDER BY g.created_at DESC, f.ordem ASC, f.id ASC
")->fetchAll();

$isAdmin = isset($_SESSION['usuario_id']) && ($_SESSION['perfil'] ?? '') === 'admin';
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Galeria | 71º GE Minuano</title>
  <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700;900&family=Source+Sans+3:wght@300;400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/main.css" />
  <?php if ($isAdmin): ?>
  <style>
    .admin-bar {
      position: fixed; bottom: 24px; right: 24px; z-index: 999;
    }
    .admin-bar a {
      display: inline-flex; align-items: center; gap: 8px;
      background: var(--navy); color: #fff;
      font-family: 'Source Sans 3', sans-serif;
      font-size: 0.9rem; font-weight: 600;
      padding: 12px 22px; border-radius: 40px;
      text-decoration: none;
      box-shadow: 0 4px 18px rgba(0,0,0,0.25);
      transition: background 0.2s, transform 0.15s;
    }
    .admin-bar a:hover { background: var(--navy-light); transform: translateY(-2px); }
  </style>
  <?php endif; ?>
</head>
<body>

  <nav class="topnav">
    <div class="topnav-inner" id="topnav-inner">
      <div class="has-dropdown">
        <a href="quem-somos.html">Quem somos</a>
        <div class="dropdown">
          <a href="quem-somos.html#escoteiros-brasil">Escoteiros do Brasil</a>
          <a href="quem-somos.html#nossa-missao">Nossa Missão</a>
          <a href="quem-somos.html#nosso-patrono">Nosso Patrono</a>
          <a href="quem-somos.html#nossa-sede">Nossa Sede</a>
        </div>
      </div>
      <div class="has-dropdown">
        <a href="escotismo.html">Escotismo</a>
        <div class="dropdown">
          <a href="escotismo.html#principios">Princípios</a>
          <a href="escotismo.html#metodos">Métodos</a>
          <a href="escotismo.html#lema">Lema</a>
          <a href="escotismo.html#ramos">Ramos</a>
          <a href="escotismo.html#modalidades">Modalidades</a>
          <a href="escotismo.html#especialidades">Especialidades</a>
          <a href="inscricao.html">Como me inscrever?</a>
        </div>
      </div>
      <div class="has-dropdown">
        <a href="#">Ramos</a>
        <div class="dropdown">
          <a href="https://www.escoteiros.org.br/ramo-lobinho/">Lobinhos</a>
          <a href="https://www.escoteiros.org.br/ramo-escoteiro/">Escoteiros</a>
          <a href="https://www.escoteiros.org.br/ramo-senior/">Sênior</a>
          <a href="https://www.escoteiros.org.br/ramo-pioneiro/">Pioneiros</a>
        </div>
      </div>
   <div class="has-dropdown">
        <a href="visualizar-atividades.php#">Nossas Atividades</a>
        <div class="dropdown">
          <a href="visualizar-atividades.php">Atividades</a>
          <a href="galeria.php">Galeria</a>
        </div>
      </div>
      <a href="documentos.php">Documentos</a>
      <a href="login.php" class="area-restrita">|Área Restrita|</a>
      <button class="menu-toggle" id="menu-toggle" aria-label="Abrir menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>
  </nav>

  <header>
    <div class="header-inner">
            <a href="index.php" class="logo-block">
        <div class="logo-circle">
          <img src="assets/img/logo2.png" alt="Logo 71º Grupo Escoteiro Minuano" class="logo-img" />
        </div>
        <div class="logo-text">
          <h1>71º Grupo de Escoteiros Minuano</h1>
          <p>Av. Waldemar Tietz, 1154 — Conj. Hab. Padre José de Anchieta<br>
             São Paulo – SP &nbsp;|&nbsp; Fundado em 31/05/1981</p>
        </div>
      </a>
      <div class="header-ctas">
        <a href="inscricao.html" class="header-cta">
          <svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <circle cx="40" cy="22" r="12" fill="none" stroke="#1e6b35" stroke-width="4"/>
            <path d="M20 65 C20 50 60 50 60 65" fill="none" stroke="#1e6b35" stroke-width="4" stroke-linecap="round"/>
          </svg>
          Seja Escoteiro
        </a>
        <a href="visualizar-atividades.php" class="header-cta">
          <svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M15 55 L15 35 L40 20 L65 35 L65 55" fill="none" stroke="#1e6b35" stroke-width="4" stroke-linejoin="round"/>
            <path d="M30 55 L30 42 L50 42 L50 55" fill="none" stroke="#1e6b35" stroke-width="3"/>
            <path d="M10 55 L70 55" stroke="#1e6b35" stroke-width="4" stroke-linecap="round"/>
          </svg>
          Nossas Atividades
        </a>
      </div>
    </div>
  </header>

  <div class="page-header">
    <div class="container">
      <nav class="breadcrumb">
        <a href="index.html">Início</a>
        <span class="breadcrumb-sep">›</span>
        <a href="atividades.html">Nossas Atividades</a>
        <span class="breadcrumb-sep">›</span>
        <span>Galeria</span>
      </nav>
      <span class="page-badge">Momentos Inesquecíveis</span>
      <h1>Galeria de Fotos</h1>
      <p class="page-subtitle">
        Reviva os melhores momentos do 71º GE Minuano: acampamentos, eventos, reuniões e muito mais.
      </p>
    </div>
  </div>

  <section>
    <div class="container">

      <?php if (empty($fotos)): ?>
        <p style="text-align:center;color:#888;padding:60px 0;font-size:1.1rem">
          Em breve novas fotos serão publicadas aqui. 📷
        </p>
      <?php else: ?>

        <div class="gallery-filter">
          <button class="filter-btn active" data-filter="all">Todas</button>
          <button class="filter-btn" data-filter="acampamento">Acampamentos</button>
          <button class="filter-btn" data-filter="evento">Eventos</button>
          <button class="filter-btn" data-filter="reuniao">Reuniões</button>
          <button class="filter-btn" data-filter="servico">Serviço</button>
        </div>

        <div class="gallery-grid" id="gallery-grid">
          <?php foreach ($fotos as $f):
            $caption = htmlspecialchars($f['legenda'] ?: $f['galeria_titulo']);
            $src     = htmlspecialchars($f['url']);
          ?>
          <div class="gallery-item" data-cat="<?= htmlspecialchars($f['tipo']) ?>">
            <div class="gallery-thumb">
              <img src="<?= $src ?>" alt="<?= $caption ?>" loading="lazy"
                   style="width:100%;height:100%;object-fit:cover;display:block">
            </div>
            <div class="gallery-caption-bar"><?= $caption ?></div>
          </div>
          <?php endforeach; ?>
        </div>

      <?php endif; ?>

      <div class="info-box reveal" style="margin-top:40px">
        <h4>Quer participar desta história?</h4>
        <p>Junte-se ao 71º GE Minuano e crie suas próprias memórias inesquecíveis.
           <a href="inscricao.html" style="color:var(--navy-light)">Quero me inscrever →</a>
        </p>
      </div>

    </div>
  </section>

  <!-- LIGHTBOX -->
  <div class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-label="Visualização de foto">
    <div class="lightbox-inner"></div>
    <button class="lightbox-close" aria-label="Fechar">✕</button>
    <div class="lightbox-caption"></div>
  </div>

  <footer>
    <div class="footer-inner">
      <div>
        <h4>71º Grupo de Escoteiros Minuano</h4>
        <address>Av. Waldemar Tietz, 1154<br>São Paulo – SP</address>
      </div>
      <div>
        <h4>Links Rápidos</h4>
        <ul>
          <li><a href="quem-somos.html">Quem Somos</a></li>
          <li><a href="visualizar-atividades.php">Nossas Atividades</a></li>
          <li><a href="galeria.php">Galeria</a></li>
          <li><a href="inscricao.html">Inscreva-se</a></li>
        </ul>
      </div>
      <div>
        <h4>Contato</h4>
        <ul>
          <li><a href="contato.html">📩 Fale Conosco</a></li>
          <li><a href="login.php">📋 Área Restrita</a></li>
          <li><a href="doe-agora.html">💛 Doe Agora</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">© <?= date('Y') ?> 71º Grupo de Escoteiros Minuano — Todos os direitos reservados.</div>
  </footer>

  <?php if ($isAdmin): ?>
  <div class="admin-bar">
    <a href="admin/galeria.php">📷 Gerenciar Galeria</a>
  </div>
  <?php endif; ?>

  <button class="back-to-top" id="back-to-top" aria-label="Voltar ao topo">↑</button>
  <script src="assets/js/main.js"></script>
  <script>
    // Filtro de categorias
    document.querySelectorAll('.filter-btn').forEach(btn => {
      btn.addEventListener('click', function () {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        const filter = this.dataset.filter;
        document.querySelectorAll('.gallery-item').forEach(item => {
          item.style.display = (filter === 'all' || item.dataset.cat === filter) ? '' : 'none';
        });
      });
    });

    // Lightbox com imagens reais
    const lightbox    = document.getElementById('lightbox');
    const lbInner     = lightbox.querySelector('.lightbox-inner');
    const lbCaption   = lightbox.querySelector('.lightbox-caption');
    const lbClose     = lightbox.querySelector('.lightbox-close');

    document.querySelectorAll('.gallery-item').forEach(item => {
      item.addEventListener('click', function () {
        const img     = this.querySelector('img');
        const caption = this.querySelector('.gallery-caption-bar')?.textContent || '';
        lbInner.innerHTML = img
          ? `<img src="${img.src}" alt="${caption}" style="max-width:90vw;max-height:78vh;border-radius:8px;display:block">`
          : '';
        lbCaption.textContent = caption;
        lightbox.classList.add('active');
      });
    });

    lbClose.addEventListener('click', () => lightbox.classList.remove('active'));
    lightbox.addEventListener('click', e => { if (e.target === lightbox) lightbox.classList.remove('active'); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') lightbox.classList.remove('active'); });
  </script>
</body>
</html>
