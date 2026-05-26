<?php
// index.php — Página inicial | busca atividades e documentos do banco
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/src/auth.php';

$db = getDB();

// Busca próximas 3 atividades publicadas a partir de hoje
$stmtAtiv = $db->prepare("
    SELECT titulo, tipo, local, data_inicio, descricao
    FROM atividades
    WHERE publicado = 1
    ORDER BY data_inicio ASC
    LIMIT 3
");
$stmtAtiv->execute();
$proximasAtividades = $stmtAtiv->fetchAll();

// Busca documentos públicos (restrito = 0) ativos
$stmtDocs = $db->prepare("
    SELECT titulo, descricao, icone, tipo_fonte, arquivo_path, categoria
    FROM documentos
    WHERE ativo = 1 AND restrito = 0
    ORDER BY categoria, ordem, id
    LIMIT 6
");
$stmtDocs->execute();
$documentos = $stmtDocs->fetchAll();

// Labels dos tipos de atividade
$tipoLabel = [
  'acampamento' => 'Acampamento',
  'reuniao'     => 'Reunião',
  'evento'      => 'Evento',
  'curso'       => 'Curso',
  'outro'       => 'Outro',
];

// Meses em português
$meses = ['', 'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
?>
<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>71º Grupo de Escoteiros Minuano — Início</title>
  <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700;900&family=Source+Sans+3:wght@300;400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/main.css" />
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
        <a href="atividades.html">Nossas Atividades</a>
        <div class="dropdown">
          <a href="atividades.html#calendario">Calendário</a>
          <a href="atividades.html#acampamentos">Acampamentos</a>
          <a href="atividades.html#eventos">Eventos</a>
          <a href="galeria.html">Galeria</a>
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
          <p>Av. Waldemar Tietz, 1154 — Conj. Hab. Padre José de Anchieta<br />São Paulo – SP &nbsp;|&nbsp; Fundado em 31/05/1981</p>
        </div>
      </a>
      <div class="header-ctas">
        <a href="inscricao.html" class="header-cta">
          <svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <circle cx="40" cy="22" r="12" fill="none" stroke="#1e6b35" stroke-width="4" />
            <path d="M20 65 C20 50 60 50 60 65" fill="none" stroke="#1e6b35" stroke-width="4" stroke-linecap="round" />
            <path d="M35 34 L28 45 L40 42 L52 45 L45 34" fill="#e8b800" stroke="#1e6b35" stroke-width="2" />
          </svg>
          Seja Escoteiro
        </a>
        <a href="atividades.html" class="header-cta">
          <svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M15 55 L15 35 L40 20 L65 35 L65 55" fill="none" stroke="#1e6b35" stroke-width="4" stroke-linejoin="round" />
            <path d="M30 55 L30 42 L50 42 L50 55" fill="none" stroke="#1e6b35" stroke-width="3" />
            <path d="M10 55 L70 55" stroke="#1e6b35" stroke-width="4" stroke-linecap="round" />
            <circle cx="55" cy="28" r="10" fill="none" stroke="#1e6b35" stroke-width="3" />
            <path d="M55 22 L55 34 M49 28 L61 28" stroke="#e8b800" stroke-width="2.5" stroke-linecap="round" />
          </svg>
          Nossas Atividades
        </a>
      </div>
    </div>
  </header>

  <!-- HERO CARROSSEL -->
  <div class="hero">
    <div class="slides" id="slides">
      <div class="slide slide-1">
        <div class="slide-content">
          <span class="badge">Desde 1981</span>
          <h2>Formando líderes para um mundo melhor</h2>
          <p>Mais de 40 anos desenvolvendo jovens através do escotismo, com valores, aventura e cidadania.</p>
          <a href="quem-somos.html" class="btn-primary">Conheça nossa história</a>
        </div>
      </div>
      <div class="slide slide-2">
        <div class="slide-content">
          <span class="badge">Sempre Alerta</span>
          <h2>Aventura, amizade e aprendizado na natureza</h2>
          <p>Acampamentos, trilhas, primeiros socorros e muito mais. Descubra o escotismo com a gente!</p>
          <a href="atividades.html" class="btn-primary">Nossas atividades</a>
        </div>
      </div>
      <div class="slide slide-3">
        <div class="slide-content">
          <span class="badge">Faça a sua inscrição!</span>
          <h2>Ajude a manter vivo o espírito escoteiro</h2>
          <p>Preencha o formulário e faça parte dessa história.</p>
          <a href="inscricao.html" class="btn-primary">Inscrição</a>
        </div>
      </div>
    </div>
    <button class="hero-btn prev" aria-label="Slide anterior">&#8249;</button>
    <button class="hero-btn next" aria-label="Próximo slide">&#8250;</button>
    <div class="hero-dots" role="tablist" aria-label="Navegação do carrossel">
      <div class="hero-dot active" role="tab" aria-label="Slide 1"></div>
      <div class="hero-dot" role="tab" aria-label="Slide 2"></div>
      <div class="hero-dot" role="tab" aria-label="Slide 3"></div>
    </div>
  </div>

  <!-- RAMOS -->
  <section class="ramos">
    <div class="container">
      <h2 class="section-title reveal">Venha fazer parte do movimento escoteiro</h2>
      <div class="section-rule"></div>
      <p class="ramos-intro reveal">Nosso grupo possui quatro ramos, cada um pensado para a faixa etária e o desenvolvimento do jovem escoteiro.</p>
      <div class="ramos-grid">
        <a href="ramos/lobinhos.html" class="ramo-card ramo-lobinhos reveal">
          <div class="ramo-icon"><img src="assets/img/Logo_ramo_lobinho_principal.png" alt="Lobinhos" /></div>
          <p>Primeiros passos na aventura escoteira, com jogos, histórias da floresta e amizades para a vida.</p>
          <span class="ramo-age">6½ a 10 anos</span>
        </a>
        <a href="ramos/escoteiros.html" class="ramo-card ramo-escoteiros reveal reveal-delay-1">
          <div class="ramo-icon"><img src="assets/img/Logo_ramo_escoteiro_principal.png" alt="Escoteiros" /></div>
          <p>Acampamentos, trilhas, primeiros socorros e desenvolvimento de competências e liderança.</p>
          <span class="ramo-age">11 a 14 anos</span>
        </a>
        <a href="ramos/senior.html" class="ramo-card ramo-senior reveal reveal-delay-2">
          <div class="ramo-icon"><img src="assets/img/Logo_ramo_senior_principal.png" alt="Sênior" /></div>
          <p>Desafios maiores, expedições e desenvolvimento de liderança, cidadania e autonomia.</p>
          <span class="ramo-age">15 a 17 anos</span>
        </a>
        <a href="ramos/pioneiros.html" class="ramo-card ramo-pioneiros reveal reveal-delay-3">
          <div class="ramo-icon"><img src="assets/img/Logo_ramo_pioneiro_principal.png" alt="Pioneiros" /></div>
          <p>Protagonismo, serviço comunitário e projetos de impacto social com foco no bem comum.</p>
          <span class="ramo-age">18 a 21 anos</span>
        </a>
      </div>
    </div>
  </section>

  <!-- ESTATÍSTICAS -->
  <section class="stats-section">
    <div class="container">
      <div class="stats-grid">
        <div class="stat-item reveal"><span class="stat-number counter" data-target="44" data-suffix="+">44+</span><span class="stat-label">Anos de história</span></div>
        <div class="stat-item reveal reveal-delay-1"><span class="stat-number counter" data-target="85" data-suffix="+">85+</span><span class="stat-label">Membros ativos</span></div>
        <div class="stat-item reveal reveal-delay-2"><span class="stat-number counter" data-target="200" data-suffix="+">200+</span><span class="stat-label">Acampamentos realizados</span></div>
        <div class="stat-item reveal reveal-delay-3"><span class="stat-number counter" data-target="40" data-suffix="+">40+</span><span class="stat-label">Atividades por ano</span></div>
      </div>
    </div>
  </section>

  <!-- DESTAQUES -->
  <section class="destaques">
    <div class="container">
      <h2 class="section-title reveal">Destaques</h2>
      <div class="section-rule"></div>
      <div class="cards-grid">
        <div class="card reveal">
          <div class="card-img">🏕️</div>
          <div class="card-body">
            <h3>Acampamento de Verão 2025</h3>
            <p>Uma semana de aventura na Serra da Cantareira com trilhas, rapel e muita natureza. Mais de 40 jovens participaram desta experiência única.</p>
            <a href="atividades.html#acampamentos" class="card-link">Ver mais →</a>
          </div>
        </div>
        <div class="card reveal reveal-delay-1">
          <div class="card-img">🤝</div>
          <div class="card-body">
            <h3>Projeto Solidariedade</h3>
            <p>Nossos pioneiros realizaram coleta de alimentos e distribuíram cestas básicas para famílias em situação de vulnerabilidade no bairro.</p>
            <a href="atividades.html#eventos" class="card-link">Ver mais →</a>
          </div>
        </div>
        <div class="card reveal reveal-delay-2">
          <div class="card-img">🏆</div>
          <div class="card-body">
            <h3>Medalha de Ouro no Jamboree</h3>
            <p>A equipe de Pioneiros conquistou o primeiro lugar no Jamboree Estadual de São Paulo, representando nossa região com excelência.</p>
            <a href="atividades.html#eventos" class="card-link">Ver mais →</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================================
         PRÓXIMAS ATIVIDADES — DINÂMICO DO BANCO
         ============================================================ -->
  <section style="background: white">
    <div class="container">
      <h2 class="section-title reveal">Próximas Atividades</h2>
      <div class="section-rule"></div>

      <div class="events-list">
        <?php if (empty($proximasAtividades)): ?>
          <p style="color:var(--gray);padding:24px 0">Nenhuma atividade programada no momento. Fique ligado!</p>
        <?php else: ?>
          <?php foreach ($proximasAtividades as $i => $ativ):
            $dia  = date('d', strtotime($ativ['data_inicio']));
            $mes  = $meses[(int)date('m', strtotime($ativ['data_inicio']))];
            $tipo = $tipoLabel[$ativ['tipo']] ?? 'Evento';
            $delay = $i > 0 ? ' reveal-delay-' . $i : '';
          ?>
            <div class="event-item reveal<?= $delay ?>">
              <div class="event-date-box">
                <span class="event-day"><?= $dia ?></span>
                <span class="event-month"><?= $mes ?></span>
              </div>
              <div class="event-info">
                <h4><?= htmlspecialchars($ativ['titulo']) ?></h4>
                <?php if ($ativ['descricao']): ?>
                  <p><?= htmlspecialchars($ativ['descricao']) ?></p>
                <?php endif; ?>
                <?php if ($ativ['local']): ?>
                  <p style="font-size:.85rem;color:#888">📍 <?= htmlspecialchars($ativ['local']) ?></p>
                <?php endif; ?>
                <span class="event-tag <?= $ativ['tipo'] ?>"><?= $tipo ?></span>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <div class="more-link reveal"><a href="visualizar-atividades.php">➤ Ver calendário completo</a></div>
    </div>
  </section>

  <!-- ============================================================
         DOCUMENTOS — DINÂMICO DO BANCO
         ============================================================ -->
  <?php if (!empty($documentos)): ?>
    <section>
      <div class="container">
        <h2 class="section-title reveal">Documentos</h2>
        <div class="section-rule"></div>
        <div class="document-list reveal">
          <?php foreach ($documentos as $doc):
            if ($doc['tipo_fonte'] === 'google_drive' && $doc['arquivo_path']) {
              $href   = htmlspecialchars($doc['arquivo_path']);
              $target = 'target="_blank" rel="noopener noreferrer"';
            } elseif ($doc['arquivo_path']) {
              $href   = htmlspecialchars($doc['arquivo_path']);
              $target = '';
            } else {
              $href   = '#';
              $target = '';
            }
          ?>
            <a href="<?= $href ?>" class="document-item" <?= $target ?>>
              <div class="document-icon"><?= $doc['icone'] ?? '📄' ?></div>
              <div class="document-info">
                <h4><?= htmlspecialchars($doc['titulo']) ?></h4>
                <?php if ($doc['descricao']): ?>
                  <span><?= htmlspecialchars($doc['descricao']) ?></span>
                <?php endif; ?>
              </div>
              <span class="document-download">⬇ Download</span>
            </a>
          <?php endforeach; ?>
        </div>
        <div class="more-link reveal"><a href="documentos.php">➤ Ver todos os documentos</a></div>
      </div>
    </section>
  <?php endif; ?>

  <!-- NOTÍCIAS -->
  <section class="noticias">
    <div class="container">
      <h2 class="section-title reveal">Notícias</h2>
      <div class="section-rule"></div>
      <div class="cards-grid">
        <div class="noticia-card reveal">
          <div class="noticia-img">📰</div>
          <div class="noticia-body">
            <div class="noticia-date">15 de Março, 2025</div>
            <h3>Inscrições abertas para novos membros — Ramos Lobinhos e Escoteiros</h3>
            <p>O 71º GE Minuano abre vagas para novos integrantes. Venha conhecer nossa sede e participar de uma reunião experimental gratuita.</p>
            <a href="inscricao.html" class="card-link">Leia mais →</a>
          </div>
        </div>
        <div class="noticia-card reveal reveal-delay-1">
          <div class="noticia-img">🌲</div>
          <div class="noticia-body">
            <div class="noticia-date">02 de Março, 2025</div>
            <h3>Dia do Escoteiro: plantio de árvores na região</h3>
            <p>Em homenagem ao Dia do Escoteiro, nossos membros realizaram ação ambiental plantando mudas nativas no Parque Estadual da Cantareira.</p>
            <a href="atividades.html" class="card-link">Leia mais →</a>
          </div>
        </div>
        <div class="noticia-card reveal reveal-delay-2">
          <div class="noticia-img">📅</div>
          <div class="noticia-body">
            <div class="noticia-date">18 de Fevereiro, 2025</div>
            <h3>Calendário 2025: confira todas as atividades do ano</h3>
            <p>Publicamos o calendário completo de atividades para 2025: acampamentos, caminhadas, cursos de formação e eventos especiais.</p>
            <a href="atividades.html#calendario" class="card-link">Leia mais →</a>
          </div>
        </div>
      </div>
      <div class="more-link reveal"><a href="atividades.html">➤ Mais notícias</a></div>
    </div>
  </section>

  <!-- DEPOIMENTOS -->
  <section class="depoimentos">
    <div class="container">
      <h2 class="section-title reveal">Depoimentos</h2>
      <div class="section-rule"></div>
      <div class="depoimentos-grid">
        <div class="depoimento-card reveal">
          <div class="depoimento-header">
            <div class="depoimento-avatar">👨</div>
            <div>
              <div class="depoimento-name">Carlos Eduardo Silva</div>
              <div class="depoimento-role">Ex-escoteiro, membro desde 1998</div>
            </div>
          </div>
          <p class="depoimento-text">O escotismo me ensinou valores que carrego para sempre. A disciplina, o respeito à natureza e a capacidade de trabalhar em equipe foram fundamentais na minha vida profissional e pessoal.</p>
        </div>
        <div class="depoimento-card reveal reveal-delay-1">
          <div class="depoimento-header">
            <div class="depoimento-avatar">👩</div>
            <div>
              <div class="depoimento-name">Ana Paula Ferreira</div>
              <div class="depoimento-role">Mãe de escoteiro</div>
            </div>
          </div>
          <p class="depoimento-text">Meu filho entrou como Lobinho tímido e hoje é um escoteiro confiante e responsável. A dedicação dos chefes e a qualidade das atividades fazem toda a diferença.</p>
        </div>
      </div>
    </div>
  </section>

  <footer>
    <div class="footer-inner">
      <div>
        <h4>71º Grupo de Escoteiros Minuano</h4>
        <address>Av. Waldemar Tietz, 1154<br />Conj. Hab. Padre José de Anchieta<br />São Paulo – SP</address>
        <div class="social-icons">
          <a href="#" title="Instagram" aria-label="Instagram"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
            </svg></a>
          <a href="#" title="Facebook" aria-label="Facebook"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
              <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
            </svg></a>
          <a href="#" title="YouTube" aria-label="YouTube"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
              <path d="M23.495 6.205a3.007 3.007 0 0 0-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 0 0 .527 6.205a31.247 31.247 0 0 0-.522 5.805 31.247 31.247 0 0 0 .522 5.783 3.007 3.007 0 0 0 2.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 0 0 2.088-2.088 31.247 31.247 0 0 0 .5-5.783 31.247 31.247 0 0 0-.5-5.805zM9.609 15.601V8.408l6.264 3.602z" />
            </svg></a>
        </div>
      </div>
      <div>
        <h4>Links Rápidos</h4>
        <ul>
          <li><a href="quem-somos.html">Quem Somos</a></li>
          <li><a href="escotismo.html">Escotismo</a></li>
          <li><a href="atividades.html">Nossas Atividades</a></li>
          <li><a href="galeria.html">Galeria de Fotos</a></li>
          <li><a href="documentos.php">Documentos</a></li>
          <li><a href="inscricao.html">Como me Inscrever</a></li>
        </ul>
      </div>
      <div>
        <h4>Contato</h4>
        <ul>
          <li><a href="contato.html">📩 Fale Conosco</a></li>
          <li><a href="contato.html#faq">❓ Dúvidas Frequentes</a></li>
          <li><a href="login.php">📋 Área Restrita</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">© <?= date('Y') ?> 71º Grupo de Escoteiros Minuano — Todos os direitos reservados.</div>
  </footer>

  <button class="back-to-top" id="back-to-top" aria-label="Voltar ao topo">↑</button>
  <script src="assets/js/main.js"></script>
</body>

</html>