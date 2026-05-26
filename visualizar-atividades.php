<?php
// visualizar-atividades.php — Página pública de atividades
require_once __DIR__ . '/config/db.php';

$db = getDB();

// Busca todas as atividades publicadas
$atividades = $db->query("
    SELECT id, titulo, tipo, local, data_inicio, data_fim, descricao
    FROM atividades
    WHERE publicado = 1
    ORDER BY data_inicio ASC
")->fetchAll();

$tipoLabel = [
    'acampamento' => 'Acampamento',
    'reuniao'     => 'Reunião',
    'evento'      => 'Evento',
    'curso'       => 'Curso',
    'outro'       => 'Outro',
];

$meses = ['', 'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Nossas Atividades | 71º GE Minuano</title>
  <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700;900&family=Source+Sans+3:wght@300;400;600;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="assets/css/main.css"/>
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
        <a href="visualizar-atividades.php" class="active">Nossas Atividades</a>
        <div class="dropdown">
          <a href="visualizar-atividades.php#calendario">Calendário</a>
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
          <img src="assets/img/logo2.png" alt="Logo 71º Grupo Escoteiro Minuano" class="logo-img"/>
        </div>
        <div class="logo-text">
          <h1>71º Grupo de Escoteiros Minuano</h1>
          <p>Av. Waldemar Tietz, 1154 — Conj. Hab. Padre José de Anchieta<br/>São Paulo – SP &nbsp;|&nbsp; Fundado em 31/05/1981</p>
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
        <a href="index.php">Início</a>
        <span class="breadcrumb-sep">›</span>
        <span>Nossas Atividades</span>
      </nav>
      <span class="page-badge">Aventura e Aprendizado</span>
      <h1>Nossas Atividades</h1>
      <p class="page-subtitle">Confira o calendário e eventos do 71º GE Minuano. Uma agenda repleta de aventura, aprendizado e amizades.</p>
    </div>
  </div>

  <!-- CALENDÁRIO -->
  <section id="calendario">
    <div class="container">
      <h2 class="section-title" style="text-align:left">Calendário de Atividades</h2>
      <div class="section-rule" style="margin-left:0"></div>

      <div class="events-list">
        <?php if (empty($atividades)): ?>
          <p style="color:var(--gray);padding:24px 0">Nenhuma atividade programada no momento. Fique ligado!</p>
        <?php else: ?>
          <?php foreach ($atividades as $i => $ativ):
            $dia   = date('d', strtotime($ativ['data_inicio']));
            $mes   = $meses[(int)date('m', strtotime($ativ['data_inicio']))];
            $tipo  = $tipoLabel[$ativ['tipo']] ?? 'Evento';
            $delay = $i > 0 && $i <= 3 ? ' reveal-delay-' . $i : '';
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
              <?php if ($ativ['data_fim']): ?>
                <p style="font-size:.82rem;color:#aaa">até <?= date('d/m/Y', strtotime($ativ['data_fim'])) ?></p>
              <?php endif; ?>
              <span class="event-tag <?= $ativ['tipo'] ?>"><?= $tipo ?></span>
            </div>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

    </div>
  </section>

  <footer>
    <div class="footer-inner">
      <div>
        <h4>71º Grupo de Escoteiros Minuano</h4>
        <address>Av. Waldemar Tietz, 1154<br/>São Paulo – SP</address>
      </div>
      <div>
        <h4>Links Rápidos</h4>
        <ul>
          <li><a href="quem-somos.html">Quem Somos</a></li>
          <li><a href="visualizar-atividades.php">Nossas Atividades</a></li>
          <li><a href="galeria.html">Galeria</a></li>
          <li><a href="inscricao.html">Inscreva-se</a></li>
        </ul>
      </div>
      <div>
        <h4>Contato</h4>
        <ul>
          <li><a href="contato.html">📩 Fale Conosco</a></li>
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