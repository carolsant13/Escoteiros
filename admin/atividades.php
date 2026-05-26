<?php
// admin/atividades.php — Lista de atividades | admin e editor
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/auth.php';

exigirLogin();

$usuario = usuarioLogado();
$db      = getDB();

$msg     = $_GET['msg']  ?? '';
$msgTipo = $_GET['tipo'] ?? 'ok';

// Busca todas as atividades
$atividades = $db->query("
    SELECT id, titulo, tipo, local, data_inicio, data_fim, publicado
    FROM atividades
    ORDER BY data_inicio DESC
")->fetchAll();

// Labels dos tipos
$tipoLabel = [
    'acampamento' => '🏕️ Acampamento',
    'reuniao'     => '📋 Reunião',
    'evento'      => '🎉 Evento',
    'curso'       => '📚 Curso',
    'outro'       => '📌 Outro',
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Atividades | Admin — 71º GE Minuano</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/main.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

  <div class="topo">
    <div class="topo-texto">
      <h1>71º Grupo de Escoteiros Minuano</h1>
      <p>Área Restrita — Gerenciar Atividades</p>
    </div>
    <div class="topo-direita">
      <a href="../dashboard.php" class="btn-voltar">← Painel</a>
      <a href="../logout.php" class="btn-sair">Sair</a>
    </div>
  </div>

  <div class="admin-wrap">

    <div class="admin-topbar">
      <h2>🏕️ Atividades <span class="badge-count"><?= count($atividades) ?></span></h2>
      <a href="atividade-insere.php" class="btn-primary">+ Nova Atividade</a>
    </div>

    <?php if ($msg): ?>
      <div class="feedback <?= htmlspecialchars($msgTipo) ?>"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <div class="card">
      <?php if (empty($atividades)): ?>
        <p style="color:#888;font-size:.9rem">Nenhuma atividade cadastrada ainda.</p>
      <?php else: ?>
        <table class="doc-table">
          <thead>
            <tr>
              <th>Título</th>
              <th>Tipo</th>
              <th>Data</th>
              <th>Local</th>
              <th>Status</th>
              <th style="width:140px">Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($atividades as $a): ?>
            <tr>
              <td><strong><?= htmlspecialchars($a['titulo']) ?></strong></td>
              <td><?= $tipoLabel[$a['tipo']] ?? $a['tipo'] ?></td>
              <td style="font-size:.85rem">
                <?= date('d/m/Y', strtotime($a['data_inicio'])) ?>
                <?php if ($a['data_fim']): ?>
                  <br><span style="color:#888">até <?= date('d/m/Y', strtotime($a['data_fim'])) ?></span>
                <?php endif; ?>
              </td>
              <td style="font-size:.85rem;color:#666"><?= htmlspecialchars($a['local'] ?? '—') ?></td>
              <td>
                <?php if ($a['publicado']): ?>
                  <span class="badge-perfil editor">Publicado</span>
                <?php else: ?>
                  <span class="badge-inativo">Rascunho</span>
                <?php endif; ?>
              </td>
              <td>
                <div class="actions">
                  <a href="atividade-atualiza.php?id=<?= $a['id'] ?>" class="btn-sm btn-warning">Editar</a>
                  <a href="atividade-exclui.php?id=<?= $a['id'] ?>"
                     class="btn-sm btn-danger"
                     onclick="return confirm('Excluir a atividade <?= htmlspecialchars($a['titulo']) ?>?')">Excluir</a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

  </div>
</body>
</html>