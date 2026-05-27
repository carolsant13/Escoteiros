
<?php
// admin/documentos.php — painel de gerenciamento de documentos
// Acesso: somente perfil 'admin'
 
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/auth.php';
 
exigirAdminOuEditor(); // redireciona se não for admin ou editor
 
$db = getDB();
 
// Mensagem de feedback após ação
$msg     = $_GET['msg']  ?? '';
$msgTipo = $_GET['tipo'] ?? 'ok'; // ok | erro
 
// Carrega categorias distintas para o <select>
$cats = $db->query("SELECT DISTINCT categoria FROM documentos ORDER BY categoria")->fetchAll(PDO::FETCH_COLUMN);
 
// Carrega todos os documentos agrupados
$stmt = $db->query("
    SELECT id, categoria, titulo, descricao, icone, tipo_fonte, arquivo_path, ativo, ordem
    FROM documentos
    ORDER BY categoria, ordem, id
");
$todos = $stmt->fetchAll();
$porCategoria = [];
foreach ($todos as $doc) {
    $porCategoria[$doc['categoria']][] = $doc;
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Gerenciar Documentos | Admin — 71º GE Minuano</title>
  <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700;900&family=Source+Sans+3:wght@300;400;600;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="../assets/css/main.css"/>
  <style>
    /* ── Layout admin ── */
    body { background: #f4f5f7; }
    .admin-wrap { max-width: 980px; margin: 0 auto; padding: 32px 20px 60px; }
 
    .admin-topbar {
      display: flex; align-items: center; justify-content: space-between;
      margin-bottom: 28px; flex-wrap: wrap; gap: 12px;
    }
    .admin-topbar h1 {
      font-family: 'Merriweather', serif; font-size: 1.5rem;
      color: var(--navy-dark); margin: 0;
    }
    .admin-topbar a {
      font-size: 0.85rem; color: var(--navy-light); text-decoration: none;
    }
    .admin-topbar a:hover { text-decoration: underline; }
 
    /* Feedback */
    .feedback {
      padding: 12px 18px; border-radius: 6px; margin-bottom: 24px;
      font-size: 0.9rem; font-weight: 600;
    }
    .feedback.ok    { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .feedback.erro  { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
 
    /* Card principal */
    .card {
      background: #fff; border: 1px solid #dde1e8;
      border-radius: 10px; padding: 28px 28px 24px; margin-bottom: 28px;
    }
    .card h2 {
      font-family: 'Merriweather', serif; font-size: 1.1rem;
      color: var(--navy); margin: 0 0 20px;
      padding-bottom: 12px; border-bottom: 2px solid var(--gold);
      display: inline-block;
    }
 
    /* Formulário de adição */
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .form-grid .span2 { grid-column: span 2; }
    .form-group { display: flex; flex-direction: column; gap: 5px; }
    .form-group label { font-size: 0.82rem; font-weight: 600; color: #444; text-transform: uppercase; letter-spacing: .04em; }
    .form-group input,
    .form-group select,
    .form-group textarea {
      border: 1px solid #cdd1d9; border-radius: 5px;
      padding: 9px 12px; font-size: 0.92rem; font-family: inherit;
      transition: border-color .2s;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus { outline: none; border-color: var(--navy-light); }
 
    /* Fonte toggle */
    .fonte-toggle { display: flex; gap: 10px; }
    .fonte-toggle label {
      flex: 1; text-align: center; cursor: pointer;
      border: 1px solid #cdd1d9; border-radius: 5px; padding: 8px;
      font-size: 0.88rem; font-weight: 600; color: #555;
      transition: all .2s; text-transform: none; letter-spacing: 0;
    }
    .fonte-toggle input[type="radio"] { display: none; }
    .fonte-toggle input[type="radio"]:checked + label {
      background: var(--navy); color: #fff; border-color: var(--navy);
    }
 
    .field-upload, .field-drive { display: none; }
    .field-upload.visible, .field-drive.visible { display: flex; flex-direction: column; gap: 5px; }
 
    /* Botão submit */
    .btn-primary {
      background: var(--navy); color: #fff;
      border: none; border-radius: 6px; padding: 11px 26px;
      font-family: inherit; font-size: 0.92rem; font-weight: 700;
      cursor: pointer; transition: background .2s;
    }
    .btn-primary:hover { background: var(--navy-light); }
 
    /* Tabela de documentos */
    .doc-table { width: 100%; border-collapse: collapse; font-size: 0.88rem; }
    .doc-table th {
      background: #f0f2f5; text-align: left;
      padding: 9px 12px; font-weight: 700;
      color: #444; border-bottom: 2px solid #dde1e8;
    }
    .doc-table td {
      padding: 9px 12px; border-bottom: 1px solid #edf0f4;
      vertical-align: middle;
    }
    .doc-table tr:last-child td { border-bottom: none; }
    .doc-table tr:hover td { background: #fafbfc; }
 
    .badge-upload { background: #d4edda; color: #155724; padding: 2px 8px; border-radius: 12px; font-size: .78rem; font-weight: 700; }
    .badge-drive  { background: #cce5ff; color: #004085; padding: 2px 8px; border-radius: 12px; font-size: .78rem; font-weight: 700; }
    .badge-inativo{ background: #f8d7da; color: #721c24; padding: 2px 8px; border-radius: 12px; font-size: .78rem; font-weight: 700; }
 
    .actions { display: flex; gap: 6px; flex-wrap: wrap; }
    .btn-sm {
      padding: 4px 12px; border-radius: 4px; font-size: .8rem;
      font-weight: 600; cursor: pointer; border: 1px solid transparent;
      font-family: inherit; text-decoration: none; display: inline-block;
    }
    .btn-danger  { background: #f8d7da; color: #721c24; border-color: #f5c6cb; }
    .btn-danger:hover  { background: #f5c6cb; }
    .btn-warning { background: #fff3cd; color: #856404; border-color: #ffc107; }
    .btn-warning:hover { background: #ffeeba; }
    .btn-success { background: #d4edda; color: #155724; border-color: #c3e6cb; }
    .btn-success:hover { background: #c3e6cb; }
 
    .cat-header {
      font-family: 'Merriweather', serif; font-size: .95rem;
      font-weight: 700; color: var(--navy-dark);
      padding: 10px 12px 6px; margin-top: 16px;
      border-left: 3px solid var(--gold); background: #fafbfc;
    }
 
    @media (max-width: 640px) {
      .form-grid { grid-template-columns: 1fr; }
      .form-grid .span2 { grid-column: span 1; }
    }
  </style>
</head>
<body>
<div class="admin-wrap">
 
  <div class="admin-topbar">
    <h1>📁 Gerenciar Documentos</h1>
    <div style="display:flex;gap:16px;align-items:center">
      <a href="../documentos.php">← Ver página pública</a>
      <a href="../dashboard.php">Painel Admin</a>
    </div>
  </div>
 
  <?php if ($msg): ?>
    <div class="feedback <?= htmlspecialchars($msgTipo) ?>">
      <?= htmlspecialchars($msg) ?>
    </div>
  <?php endif; ?>
 
  <!-- ── Formulário: Adicionar Documento ── -->
  <div class="card">
    <h2>Adicionar Documento</h2>
 
    <form method="POST" action="documentos_action.php" enctype="multipart/form-data">
      <input type="hidden" name="acao" value="adicionar"/>
 
      <div class="form-grid">
 
        <!-- Título -->
        <div class="form-group span2">
          <label for="titulo">Título *</label>
          <input type="text" id="titulo" name="titulo" required maxlength="200"
                 placeholder="Ex: Ficha de Inscrição — Lobinhos"/>
        </div>
 
        <!-- Categoria -->
        <div class="form-group">
          <label for="categoria_sel">Categoria *</label>
          <select id="categoria_sel" name="categoria_sel">
            <option value="">— Escolher existente —</option>
            <?php foreach ($cats as $c): ?>
              <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="categoria_nova">Ou criar nova categoria</label>
          <input type="text" id="categoria_nova" name="categoria_nova"
                 maxlength="80" placeholder="Ex: Atas de Reunião"/>
        </div>
 
        <!-- Descrição -->
        <div class="form-group span2">
          <label for="descricao">Descrição curta</label>
          <input type="text" id="descricao" name="descricao" maxlength="300"
                 placeholder="Ex: PDF · 2 páginas · Atualizado em Jan/2025"/>
        </div>
 
        <!-- Ícone e Ordem -->
        <div class="form-group">
          <label for="icone">Ícone (emoji)</label>
          <input type="text" id="icone" name="icone" value="📄" maxlength="10"/>
        </div>
        <div class="form-group">
          <label for="ordem">Ordem de exibição</label>
          <input type="number" id="ordem" name="ordem" value="0" min="0" max="999"/>
        </div>
 
        <!-- Fonte do arquivo -->
        <div class="form-group span2">
          <label>Fonte do arquivo *</label>
          <div class="fonte-toggle">
            <input type="radio" name="tipo_fonte" id="fonte_upload" value="upload" checked/>
            <label for="fonte_upload">📤 Upload do dispositivo</label>
            <input type="radio" name="tipo_fonte" id="fonte_drive" value="google_drive"/>
            <label for="fonte_drive">🔗 Link do Google Drive</label>
          </div>
        </div>
 
        <!-- Campo: Upload -->
        <div class="form-group span2 field-upload visible" id="wrap-upload">
          <label for="arquivo">Arquivo (PDF, DOC, DOCX, XLS, XLSX — máx. 20 MB)</label>
          <input type="file" id="arquivo" name="arquivo"
                 accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"/>
        </div>
 
        <!-- Campo: Google Drive -->
        <div class="form-group span2 field-drive" id="wrap-drive">
          <label for="drive_url">URL do Google Drive</label>
          <input type="url" id="drive_url" name="drive_url"
                 placeholder="https://drive.google.com/file/d/…/view?usp=sharing"/>
          <small style="color:#666;font-size:.8rem;margin-top:3px">
            No Google Drive: clique com botão direito no arquivo → <strong>Compartilhar</strong>
            → <strong>Copiar link</strong> (acesso "Qualquer pessoa com o link").
          </small>
        </div>
 
        <div class="form-group span2" style="margin-top:6px">
          <button type="submit" class="btn-primary">➕ Adicionar Documento</button>
        </div>
 
      </div><!-- /.form-grid -->
    </form>
  </div><!-- /.card -->
 
  <!-- ── Listagem de Documentos ── -->
  <div class="card">
    <h2>Documentos Cadastrados</h2>
 
    <?php if (empty($todos)): ?>
      <p style="color:#888;font-size:.9rem">Nenhum documento cadastrado ainda.</p>
    <?php else: ?>
      <table class="doc-table">
        <thead>
          <tr>
            <th style="width:32px">#</th>
            <th>Título</th>
            <th>Fonte</th>
            <th>Status</th>
            <th style="width:160px">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($porCategoria as $cat => $docs): ?>
            <tr>
              <td colspan="5">
                <div class="cat-header"><?= htmlspecialchars($cat) ?></div>
              </td>
            </tr>
            <?php foreach ($docs as $doc): ?>
            <tr>
              <td style="color:#aaa"><?= $doc['id'] ?></td>
              <td>
                <strong><?= htmlspecialchars($doc['titulo']) ?></strong>
                <?php if ($doc['descricao']): ?>
                  <br><span style="color:#888;font-size:.8rem"><?= htmlspecialchars($doc['descricao']) ?></span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($doc['tipo_fonte'] === 'google_drive'): ?>
                  <span class="badge-drive">Drive</span>
                <?php else: ?>
                  <span class="badge-upload">Upload</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if (!$doc['ativo']): ?>
                  <span class="badge-inativo">Inativo</span>
                <?php else: ?>
                  <span style="color:#155724;font-size:.8rem;font-weight:700">Ativo</span>
                <?php endif; ?>
              </td>
              <td>
                <div class="actions">
                  <?php if ($doc['ativo']): ?>
                    <form method="POST" action="documentos_action.php" style="display:inline">
                      <input type="hidden" name="acao" value="desativar"/>
                      <input type="hidden" name="id"   value="<?= $doc['id'] ?>"/>
                      <button type="submit" class="btn-sm btn-warning"
                              onclick="return confirm('Desativar este documento?')">Ocultar</button>
                    </form>
                  <?php else: ?>
                    <form method="POST" action="documentos_action.php" style="display:inline">
                      <input type="hidden" name="acao" value="ativar"/>
                      <input type="hidden" name="id"   value="<?= $doc['id'] ?>"/>
                      <button type="submit" class="btn-sm btn-success">Ativar</button>
                    </form>
                  <?php endif; ?>
                  <form method="POST" action="documentos_action.php" style="display:inline">
                    <input type="hidden" name="acao" value="excluir"/>
                    <input type="hidden" name="id"   value="<?= $doc['id'] ?>"/>
                    <button type="submit" class="btn-sm btn-danger"
                            onclick="return confirm('Excluir permanentemente?')">Excluir</button>
                  </form>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
 
</div><!-- /.admin-wrap -->
 
<script>
// Toggle campos Upload / Drive
const radios = document.querySelectorAll('input[name="tipo_fonte"]');
const wrapUpload = document.getElementById('wrap-upload');
const wrapDrive  = document.getElementById('wrap-drive');
 
radios.forEach(r => r.addEventListener('change', () => {
  const val = document.querySelector('input[name="tipo_fonte"]:checked').value;
  wrapUpload.classList.toggle('visible', val === 'upload');
  wrapDrive.classList.toggle('visible',  val === 'google_drive');
}));
</script>
</body>
</html>
 