<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/auth.php';

exigirAdmin();

$db = getDB();
$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: galeria.php'); exit; }

$stmt = $db->prepare("SELECT * FROM galerias WHERE id = ?");
$stmt->execute([$id]);
$galeria = $stmt->fetch();
if (!$galeria) { header('Location: galeria.php'); exit; }

$stmt = $db->prepare("SELECT * FROM fotos WHERE galeria_id = ? ORDER BY ordem ASC, id ASC");
$stmt->execute([$id]);
$fotos = $stmt->fetchAll();

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
  <title>Fotos: <?= htmlspecialchars($galeria['titulo']) ?> | Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/main.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
  <style>
    .foto-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 16px;
      margin-top: 4px;
    }
    .foto-card {
      border: 1px solid #dde1e8;
      border-radius: 8px;
      overflow: hidden;
      background: #fff;
    }
    .foto-card img {
      width: 100%;
      height: 150px;
      object-fit: cover;
      display: block;
    }
    .foto-card-body {
      padding: 10px 12px;
    }
    .foto-card-body p {
      font-size: .82rem;
      color: #666;
      margin-bottom: 8px;
      min-height: 18px;
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
    }
    .galeria-info {
      display: flex;
      gap: 16px;
      flex-wrap: wrap;
      align-items: center;
      margin-bottom: 24px;
      padding: 14px 18px;
      background: #fff;
      border: 1px solid #dde1e8;
      border-radius: 8px;
      font-size: .9rem;
    }
    .galeria-info strong { color: var(--verde-escuro); }
    .galeria-info .sep { color: #ccc; }
  </style>
</head>
<body>

  <div class="topo">
    <div class="topo-texto">
      <h1>71º Grupo de Escoteiros Minuano</h1>
      <p>Área Restrita — Fotos da Galeria</p>
    </div>
    <div class="topo-direita">
      <a href="galeria.php" class="btn-voltar">← Galerias</a>
      <a href="../dashboard.php" class="btn-voltar">Painel</a>
      <a href="../logout.php" class="btn-sair">Sair</a>
    </div>
  </div>

  <div class="admin-wrap">

    <div class="admin-topbar">
      <h2>📷 <?= htmlspecialchars($galeria['titulo']) ?>
        <span class="badge-count"><?= count($fotos) ?> foto<?= count($fotos) !== 1 ? 's' : '' ?></span>
      </h2>
    </div>

    <?php if ($msg): ?>
      <div class="feedback <?= htmlspecialchars($msgTipo) ?>"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <!-- Info da galeria -->
    <div class="galeria-info">
      <span><strong>Categoria:</strong> <?= htmlspecialchars($tipos[$galeria['tipo']] ?? $galeria['tipo']) ?></span>
      <span class="sep">|</span>
      <span><strong>Status:</strong>
        <?= $galeria['publicado']
            ? '<span style="color:#155724;font-weight:700">Publicada</span>'
            : '<span style="color:#856404;font-weight:700">Rascunho</span>' ?>
      </span>
      <?php if ($galeria['descricao']): ?>
        <span class="sep">|</span>
        <span><?= htmlspecialchars($galeria['descricao']) ?></span>
      <?php endif; ?>
    </div>

    <!-- Upload -->
    <div class="card">
      <h2>Adicionar Foto</h2>
      <form method="POST" action="galeria_action.php" enctype="multipart/form-data">
        <input type="hidden" name="acao" value="upload_foto">
        <input type="hidden" name="galeria_id" value="<?= $galeria['id'] ?>">
        <div class="form-row">
          <div class="form-group" style="flex:2">
            <label>Imagem * &nbsp;<small style="font-weight:400;text-transform:none">(JPG, PNG, WebP — máx. 8 MB)</small></label>
            <input type="file" name="foto" accept="image/jpeg,image/png,image/webp,image/gif" required>
          </div>
          <div class="form-group" style="flex:2">
            <label>Legenda</label>
            <input type="text" name="legenda" placeholder="Ex: Montagem do acampamento">
          </div>
          <div class="form-group" style="flex:0 0 auto;justify-content:flex-end">
            <label style="opacity:0">Enviar</label>
            <button type="submit" class="btn-primary">⬆ Enviar</button>
          </div>
        </div>
      </form>
    </div>

    <!-- Fotos existentes -->
    <?php if ($fotos): ?>
    <div class="card">
      <h2>Fotos (<?= count($fotos) ?>)</h2>
      <div class="foto-grid">
        <?php foreach ($fotos as $f): ?>
        <div class="foto-card">
          <img src="../<?= htmlspecialchars($f['url']) ?>"
               alt="<?= htmlspecialchars($f['legenda'] ?? '') ?>">
          <div class="foto-card-body">
            <p title="<?= htmlspecialchars($f['legenda'] ?? '') ?>">
              <?= htmlspecialchars($f['legenda'] ?: '—') ?>
            </p>
            <form method="POST" action="galeria_action.php"
                  onsubmit="return confirm('Excluir esta foto permanentemente?')">
              <input type="hidden" name="acao" value="excluir_foto">
              <input type="hidden" name="foto_id" value="<?= $f['id'] ?>">
              <input type="hidden" name="galeria_id" value="<?= $galeria['id'] ?>">
              <button type="submit" class="btn-sm btn-danger">Excluir</button>
            </form>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php else: ?>
      <p style="color:#888;text-align:center;padding:28px 0">
        Nenhuma foto ainda. Faça o upload da primeira foto acima.
      </p>
    <?php endif; ?>

  </div>

</body>
</html>
