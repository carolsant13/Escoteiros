<?php
// admin/atividade-insere.php — Inserir nova atividade | admin e editor
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/auth.php';

exigirLogin();

$usuario = usuarioLogado();
// Editor e admin podem inserir
if (!in_array($usuario['perfil'], ['admin', 'editor'])) {
    header('Location: ../dashboard.php?erro=acesso_negado');
    exit;
}

$db   = getDB();
$erro = '';

// Busca ramos para o checkbox
$ramos = $db->query("SELECT id, nome, icone FROM ramos WHERE ativo = 1 ORDER BY ordem")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titulo      = trim($_POST['titulo']      ?? '');
    $descricao   = trim($_POST['descricao']   ?? '');
    $tipo        = $_POST['tipo']             ?? '';
    $local       = trim($_POST['local']       ?? '');
    $data_inicio = $_POST['data_inicio']      ?? '';
    $data_fim    = $_POST['data_fim']         ?? '';
    $publicado   = isset($_POST['publicado']) ? 1 : 0;
    $ramosSel    = $_POST['ramos']            ?? [];

    // Validações
    if (empty($titulo)) {
        $erro = 'O título é obrigatório.';
    } elseif (empty($tipo)) {
        $erro = 'Selecione o tipo da atividade.';
    } elseif (empty($data_inicio)) {
        $erro = 'A data de início é obrigatória.';
    } else {
        // Gera slug único
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $titulo));
        $slug = trim($slug, '-');
        // Garante unicidade do slug
        $slugBase = $slug;
        $i = 1;
        while (true) {
            $check = $db->prepare("SELECT id FROM atividades WHERE slug = :slug");
            $check->execute([':slug' => $slug]);
            if (!$check->fetch()) break;
            $slug = $slugBase . '-' . $i++;
        }

        $stmt = $db->prepare("
            INSERT INTO atividades (titulo, slug, descricao, tipo, local, data_inicio, data_fim, publicado)
            VALUES (:titulo, :slug, :descricao, :tipo, :local, :data_inicio, :data_fim, :publicado)
        ");
        $stmt->execute([
            ':titulo'      => $titulo,
            ':slug'        => $slug,
            ':descricao'   => $descricao ?: null,
            ':tipo'        => $tipo,
            ':local'       => $local ?: null,
            ':data_inicio' => $data_inicio,
            ':data_fim'    => $data_fim ?: null,
            ':publicado'   => $publicado,
        ]);

        $novoId = (int)$db->lastInsertId();

        // Vincula ramos
        if (!empty($ramosSel)) {
            $stmtRamo = $db->prepare("INSERT INTO atividade_ramo (atividade_id, ramo_id) VALUES (:aid, :rid)");
            foreach ($ramosSel as $rId) {
                $stmtRamo->execute([':aid' => $novoId, ':rid' => (int)$rId]);
            }
        }

        registrarLog('inseriu atividade', 'atividades', $novoId, ['titulo' => $titulo]);

        header('Location: atividades.php?msg=' . urlencode('Atividade "' . $titulo . '" cadastrada com sucesso!') . '&tipo=ok');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nova Atividade | Admin — 71º GE Minuano</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/main.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

  <div class="topo">
    <div class="topo-texto">
      <h1>71º Grupo de Escoteiros Minuano</h1>
      <p>Área Restrita — Nova Atividade</p>
    </div>
    <div class="topo-direita">
      <a href="atividades.php" class="btn-voltar">← Voltar</a>
      <a href="../logout.php" class="btn-sair">Sair</a>
    </div>
  </div>

  <div class="admin-wrap">
    <div class="card">
      <h2>Inserir nova atividade</h2>

      <?php if ($erro): ?>
        <div class="feedback erro"><?= htmlspecialchars($erro) ?></div>
      <?php endif; ?>

      <form method="POST" action="" autocomplete="off">

        <div class="form-group">
          <label for="titulo">Título *</label>
          <input type="text" id="titulo" name="titulo" required maxlength="200"
                 placeholder="Ex: Acampamento de Inverno 2025"
                 value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>">
        </div>

        <div class="form-grid">
          <div class="form-group">
            <label for="tipo">Tipo *</label>
            <select id="tipo" name="tipo" required>
              <option value="">— Selecione —</option>
              <?php foreach (['acampamento' => '🏕️ Acampamento', 'reuniao' => '📋 Reunião', 'evento' => '🎉 Evento', 'curso' => '📚 Curso', 'outro' => '📌 Outro'] as $val => $label): ?>
                <option value="<?= $val ?>" <?= ($_POST['tipo'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="local">Local</label>
            <input type="text" id="local" name="local" maxlength="200"
                   placeholder="Ex: Serra da Cantareira"
                   value="<?= htmlspecialchars($_POST['local'] ?? '') ?>">
          </div>

          <div class="form-group">
            <label for="data_inicio">Data de início *</label>
            <input type="datetime-local" id="data_inicio" name="data_inicio" required
                   value="<?= htmlspecialchars($_POST['data_inicio'] ?? '') ?>">
          </div>

          <div class="form-group">
            <label for="data_fim">Data de fim</label>
            <input type="datetime-local" id="data_fim" name="data_fim"
                   value="<?= htmlspecialchars($_POST['data_fim'] ?? '') ?>">
          </div>
        </div>

        <div class="form-group">
          <label for="descricao">Descrição</label>
          <textarea id="descricao" name="descricao" rows="4"
                    placeholder="Descreva a atividade..."><?= htmlspecialchars($_POST['descricao'] ?? '') ?></textarea>
        </div>

        <!-- Ramos -->
        <div class="form-group">
          <label>Ramos participantes</label>
          <div class="checkboxes">
            <?php foreach ($ramos as $ramo): ?>
              <label class="checkbox-item">
                <input type="checkbox" name="ramos[]" value="<?= $ramo['id'] ?>"
                  <?= in_array($ramo['id'], $_POST['ramos'] ?? []) ? 'checked' : '' ?>>
                <?= $ramo['icone'] ?> <?= htmlspecialchars($ramo['nome']) ?>
              </label>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Publicar -->
        <div class="form-group" style="flex-direction:row;align-items:center;gap:10px;margin-top:4px">
          <input type="checkbox" id="publicado" name="publicado" value="1"
                 <?= isset($_POST['publicado']) ? 'checked' : '' ?>>
          <label for="publicado" style="text-transform:none;letter-spacing:0;font-size:.95rem;cursor:pointer">
            Publicar imediatamente no site
          </label>
        </div>

        <button type="submit" class="btn-primary" style="margin-top:12px">Salvar Atividade</button>

      </form>
    </div>
  </div>

  <style>
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .checkboxes { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 4px; }
    .checkbox-item {
      display: flex; align-items: center; gap: 6px;
      font-size: .92rem; cursor: pointer;
      background: #f4f5f7; padding: 6px 14px;
      border-radius: 20px; border: 1px solid #dde1e8;
      transition: background .2s;
      text-transform: none; letter-spacing: 0; font-weight: 400;
    }
    .checkbox-item:hover { background: #e8f5ec; }
    .checkbox-item input { cursor: pointer; }
    @media (max-width: 600px) { .form-grid { grid-template-columns: 1fr; } }
  </style>

</body>
</html>