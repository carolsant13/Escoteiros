<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/auth.php';

exigirAdminOuEditor();

$db = getDB();
$galerias = $db->query("
    SELECT g.*, COUNT(f.id) AS total_fotos
    FROM galerias g
    LEFT JOIN fotos f ON f.galeria_id = g.id
    GROUP BY g.id
    ORDER BY g.created_at DESC
")->fetchAll();

$msg     = $_GET['msg']  ?? '';
$msgTipo = $_GET['tipo'] ?? 'ok';

$tipos = [
    'acampamento' => 'Acampamento',
    'reuniao'     => 'Reunião',
    'evento'      => 'Evento',
    'servico'     => 'Serviço',
    'outro'       => 'Outro',
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Galeria | Admin — 71º GE Minuano</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/main.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

  <div class="topo">
    <div class="topo-texto">
      <h1>71º Grupo de Escoteiros Minuano</h1>
      <p>Área Restrita — Gerenciar Galeria</p>
    </div>
    <div class="topo-direita">
      <a href="../dashboard.php" class="btn-voltar">← Painel</a>
      <a href="../logout.php" class="btn-sair">Sair</a>
    </div>
  </div>

  <div class="admin-wrap">

    <div class="admin-topbar">
      <h2>📷 Galeria <span class="badge-count"><?= count($galerias) ?></span></h2>
    </div>

    <?php if ($msg): ?>
      <div class="feedback <?= htmlspecialchars($msgTipo) ?>"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <!-- Criar nova galeria -->
    <div class="card">
      <h2>Nova Galeria</h2>
      <form method="POST" action="galeria_action.php">
        <input type="hidden" name="acao" value="criar_galeria">
        <div class="form-row">
          <div class="form-group" style="flex:2">
            <label>Título *</label>
            <input type="text" name="titulo" required placeholder="Ex: Acampamento Serra da Cantareira 2026">
          </div>
          <div class="form-group" style="flex:1">
            <label>Categoria</label>
            <select name="tipo">
              <?php foreach ($tipos as $v => $l): ?>
                <option value="<?= $v ?>"><?= $l ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label>Descrição</label>
          <textarea name="descricao" rows="2" placeholder="Descrição opcional da galeria"></textarea>
        </div>
        <div class="form-group" style="flex-direction:row;align-items:center;gap:8px">
          <input type="checkbox" name="publicado" id="publicado" value="1">
          <label for="publicado" style="text-transform:none;letter-spacing:0;font-size:.9rem;font-weight:400">Publicar imediatamente</label>
        </div>
        <button type="submit" class="btn-primary">+ Criar Galeria</button>
      </form>
    </div>

    <!-- Lista de galerias -->
    <?php if ($galerias): ?>
    <div class="card">
      <h2>Galerias cadastradas</h2>
      <table class="doc-table">
        <thead>
          <tr>
            <th>Título</th>
            <th>Categoria</th>
            <th>Fotos</th>
            <th>Status</th>
            <th style="width:230px">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($galerias as $g): ?>
          <tr>
            <td>
              <strong><?= htmlspecialchars($g['titulo']) ?></strong>
              <?php if ($g['descricao']): ?>
                <br><small style="color:#888"><?= htmlspecialchars(mb_strimwidth($g['descricao'], 0, 60, '…')) ?></small>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($tipos[$g['tipo']] ?? $g['tipo']) ?></td>
            <td><?= $g['total_fotos'] ?></td>
            <td>
              <?php if ($g['publicado']): ?>
                <span style="color:#155724;font-weight:700;font-size:.82rem">Publicada</span>
              <?php else: ?>
                <span class="badge-inativo">Rascunho</span>
              <?php endif; ?>
            </td>
            <td>
              <div class="actions">
                <a href="galeria-fotos.php?id=<?= $g['id'] ?>" class="btn-sm btn-warning">📷 Fotos</a>
                <a href="galeria_action.php?acao=toggle_publicado&id=<?= $g['id'] ?>"
                   class="btn-sm" style="background:#e8f4fd;color:#0056b3;border-color:#b8daff">
                  <?= $g['publicado'] ? 'Ocultar' : 'Publicar' ?>
                </a>
                <form method="POST" action="galeria_action.php" style="display:inline"
                      onsubmit="return confirm('Excluir a galeria e todas as fotos?')">
                  <input type="hidden" name="acao" value="excluir_galeria">
                  <input type="hidden" name="id" value="<?= $g['id'] ?>">
                  <button type="submit" class="btn-sm btn-danger">Excluir</button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
      <p style="color:#888;text-align:center;padding:32px 0">Nenhuma galeria cadastrada ainda. Crie a primeira acima.</p>
    <?php endif; ?>

  </div>

</body>
</html>
