<?php
require_once __DIR__ . '/src/auth.php';

exigirLogin();

$usuario = usuarioLogado();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — Área Restrita | 71º GE Minuano</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
 <link rel="stylesheet" href="assets/css/dashboard.css" >
</head>
<body>

  <div class="topo">
    <div class="topo-esquerda">
      <div class="topo-texto">
        <h1>71º Grupo de Escoteiros Minuano</h1>
        <p>Área Restrita</p>
      </div>
    </div>
    <div class="topo-direita">
      <span class="usuario-info">
        ⚜️ <?= htmlspecialchars($usuario['nome']) ?>
        <span class="badge-perfil"><?= htmlspecialchars($usuario['perfil']) ?></span>
      </span>
      <a href="logout.php" class="btn-sair">Sair</a>
    </div>
  </div>

  <div class="pagina">

    <div class="boas-vindas">
      <h2>Olá, <?= htmlspecialchars($usuario['nome']) ?>! 👋</h2>
      <p>O que você deseja gerenciar hoje?</p>
    </div>

    <div class="cards-grid">

      <a href="-" class="card-acao">
        <div class="card-icone">🏕️</div>
        <div class="card-info">
          <h3>Inserir Atividade</h3>
          <p>Cadastre acampamentos, reuniões, eventos e cursos</p>
        </div>
        <div class="card-seta">→</div>
      </a>

      <a href="documentos.php" class="card-acao">
        <div class="card-icone">📄</div>
        <div class="card-info">
          <h3>Inserir Documento</h3>
          <p>Adicione formulários, regulamentos e arquivos para download</p>
        </div>
        <div class="card-seta">→</div>
      </a>

      <?php if ($usuario['perfil'] === 'admin'): ?>
      <a href="admin/usuario-insere.php" class="card-acao">
        <div class="card-icone">👤</div>
        <div class="card-info">
          <h3>Inserir Usuário</h3>
          <p>Cadastre novos membros com acesso à área restrita</p>
        </div>
        <div class="card-seta">→</div>
      </a>
      <?php endif; ?>

    </div>

    <a href="index.html" class="voltar">← Voltar para o site</a>

  </div>

  <div class="rodape">
    &copy; <?= date('Y') ?> 71º Grupo de Escoteiros Minuano — Sempre Alerta
  </div>

</body>
</html>