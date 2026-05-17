<?php

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/src/auth.php';
 
// Carrega todos os documentos ativos agrupados por categoria
$db   = getDB();
$stmt = $db->query("
    SELECT id, categoria, titulo, descricao, icone, tipo_fonte, arquivo_path
    FROM documentos
    WHERE ativo = 1
    ORDER BY categoria, ordem, id
");
$todos = $stmt->fetchAll();
 
// Agrupa por categoria mantendo ordem de inserção
$categorias = [];
foreach ($todos as $doc) {
    $categorias[$doc['categoria']][] = $doc;
}
 
// Verifica se o usuário logado é admin (para mostrar o botão de gerenciar)
$isAdmin = isset($_SESSION['usuario_id']) && ($_SESSION['perfil'] ?? '') === 'admin';
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Documentos | 71º GE Minuano</title>
  <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700;900&family=Source+Sans+3:wght@300;400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/main.css" />
  <?php if ($isAdmin): ?>
  <style>
    /* Barra flutuante de admin */
    .admin-bar {
      position: fixed;
      bottom: 24px;
      right: 24px;
      z-index: 999;
    }
    .admin-bar a {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: var(--navy);
      color: #fff;
      font-family: 'Source Sans 3', sans-serif;
      font-size: 0.9rem;
      font-weight: 600;
      padding: 12px 22px;
      border-radius: 40px;
      text-decoration: none;
      box-shadow: 0 4px 18px rgba(0,0,0,0.25);
      transition: background 0.2s, transform 0.15s;
    }
    .admin-bar a:hover { background: var(--navy-light); transform: translateY(-2px); }
    .admin-bar a svg { width: 18px; height: 18px; }
  </style>
  <?php endif; ?>
</head>
<body>
 
  <nav class="topnav">
    <div class="topnav-inner" id="topnav-inner">
      <div class="has-dropdown">
        <a href="quem-somos.php">Quem somos</a>
        <div class="dropdown">
          <a href="quem-somos.php#escoteiros-brasil">Escoteiros do Brasil</a>
          <a href="quem-somos.php#nossa-missao">Nossa Missão</a>
          <a href="quem-somos.php#nosso-patrono">Nosso Patrono</a>
          <a href="quem-somos.php#nossa-sede">Nossa Sede</a>
        </div>
      </div>
      <div class="has-dropdown">
        <a href="escotismo.php">Escotismo</a>
        <div class="dropdown">
          <a href="escotismo.php#principios">Princípios</a>
          <a href="escotismo.php#metodos">Métodos</a>
          <a href="escotismo.php#lema">Lema</a>
          <a href="escotismo.php#ramos">Ramos</a>
          <a href="escotismo.php#modalidades">Modalidades</a>
          <a href="escotismo.php#especialidades">Especialidades</a>
          <a href="inscricao.php">Como me inscrever?</a>
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
        <a href="atividades.php">Nossas Atividades</a>
        <div class="dropdown">
          <a href="atividades.php#calendario">Calendário</a>
          <a href="atividades.php#acampamentos">Acampamentos</a>
          <a href="atividades.php#eventos">Eventos</a>
          <a href="galeria.php">Galeria</a>
        </div>
      </div>
      <a href="documentos.php" class="active">Documentos</a>
      <a href="area-restrita.php" class="area-restrita">|Área Restrita|</a>
      <button class="menu-toggle" id="menu-toggle" aria-label="Abrir menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>
  </nav>
 
  <header>
    <div class="header-inner">
      <a href="index.php" class="logo-block">
        <div class="logo-circle"><img src="assets/img/logo2.png" alt="Logo" class="logo-img" /></div>
        <div class="logo-text">
          <h1>71º Grupo de Escoteiros Minuano</h1>
          <p>Av. Waldemar Tietz, 1154 — São Paulo – SP &nbsp;|&nbsp; Fundado em 31/05/1981</p>
        </div>
      </a>
      <div class="header-ctas">
        <a href="inscricao.php" class="header-cta">
          <svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <circle cx="40" cy="22" r="12" fill="none" stroke="#1e6b35" stroke-width="4"/>
            <path d="M20 65 C20 50 60 50 60 65" fill="none" stroke="#1e6b35" stroke-width="4" stroke-linecap="round"/>
          </svg>
          Seja Escoteiro
        </a>
        <a href="atividades.php" class="header-cta">
          <svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M15 55 L15 35 L40 20 L65 35 L65 55" fill="none" stroke="#1e6b35" stroke-width="4" stroke-linejoin="round"/>
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
        <span>Documentos</span>
      </nav>
      <span class="page-badge">Downloads</span>
      <h1>Documentos</h1>
      <p class="page-subtitle">
        Acesse formulários, regulamentos, estatuto e demais documentos do 71º GE Minuano para download.
      </p>
    </div>
  </div>
 
  <section>
    <div class="container">
 
      <?php if (empty($categorias)): ?>
        <p style="color: var(--gray); text-align:center; padding: 40px 0;">
          Nenhum documento disponível no momento.
        </p>
      <?php else: ?>
 
        <?php $first = true; foreach ($categorias as $nomeCategoria => $docs): ?>
          <h2 class="section-title <?= $first ? '' : 'reveal' ?>"
              style="text-align:left; margin-bottom:16px; <?= $first ? '' : 'margin-top:52px' ?>">
            <?= htmlspecialchars($nomeCategoria) ?>
          </h2>
          <div class="section-rule" style="margin-left:0; margin-bottom:24px"></div>
 
          <div class="document-list reveal">
            <?php foreach ($docs as $doc):
              // arquivo_path guarda caminho local (upload) ou URL completa (Drive)
              if ($doc['tipo_fonte'] === 'google_drive' && $doc['arquivo_path']) {
                $href   = htmlspecialchars($doc['arquivo_path']);
                $target = 'target="_blank" rel="noopener noreferrer"';
              } elseif ($doc['arquivo_path']) {
                $href   = htmlspecialchars($doc['arquivo_path']);
                $target = '';
              } else {
                // Documento sem arquivo ainda: link inativo
                $href   = '#';
                $target = '';
              }
            ?>
            <a href="<?= $href ?>" class="document-item" <?= $target ?>>
              <div class="document-icon"><?= $doc['icone'] ?></div>
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
 
          <?php $first = false; endforeach; ?>
      <?php endif; ?>
 
      <div class="info-box reveal" style="margin-top:40px">
        <h4>Precisa de outro documento?</h4>
        <p>
          Se precisar de algum documento não listado acima, entre em contato conosco pelo
          <a href="contato.php" style="color:var(--navy-light)">formulário de contato</a>
          ou WhatsApp e teremos prazer em ajudar.
        </p>
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
          <li><a href="quem-somos.php">Quem Somos</a></li>
          <li><a href="atividades.php">Nossas Atividades</a></li>
          <li><a href="galeria.php">Galeria</a></li>
          <li><a href="inscricao.php">Inscreva-se</a></li>
        </ul>
      </div>
      <div>
        <h4>Contato</h4>
        <ul>
          <li><a href="contato.php">📩 Fale Conosco</a></li>
          <li><a href="area-restrita.php">📋 Área Restrita</a></li>
          <li><a href="doe-agora.php">💛 Doe Agora</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">© 2025 71º Grupo de Escoteiros Minuano — Todos os direitos reservados.</div>
  </footer>
 
  <?php if ($isAdmin): ?>
  <div class="admin-bar">
    <a href="admin/documentos.php">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                 m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
      </svg>
      Gerenciar Documentos
    </a>
  </div>
  <?php endif; ?>
 
  <button class="back-to-top" id="back-to-top" aria-label="Voltar ao topo">↑</button>
  <script src="assets/js/main.js"></script>
</body>
</html>