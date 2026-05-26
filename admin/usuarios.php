<?php
// admin/usuarios.php — Lista de usuários | somente admin
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/auth.php';

exigirAdmin(); // só admin acessa

$db = getDB();
$usuarios = $db->query("SELECT id, nome, email, perfil, ativo, ultimo_login FROM usuarios ORDER BY nome")->fetchAll();

$msg     = $_GET['msg']  ?? '';
$msgTipo = $_GET['tipo'] ?? 'ok';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Usuários | Admin — 71º GE Minuano</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/main.css">
 <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

  <div class="topo">
    <div class="topo-texto">
      <h1>71º Grupo de Escoteiros Minuano</h1>
      <p>Área Restrita — Gerenciar Usuários</p>
    </div>
    <div class="topo-direita">
      <a href="../dashboard.php" class="btn-voltar">← Painel</a>
      <a href="../logout.php" class="btn-sair">Sair</a>
    </div>
  </div>

  <div class="admin-wrap">

    <div class="admin-topbar">
      <h2>👤 Usuários <span class="badge-count"><?= count($usuarios) ?></span></h2>
      <a href="usuario-insere.php" class="btn-primary">+ Novo Usuário</a>
    </div>

    <?php if ($msg): ?>
      <div class="feedback <?= htmlspecialchars($msgTipo) ?>"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <div class="card">
      <table class="doc-table">
        <thead>
          <tr>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Perfil</th>
            <th>Status</th>
            <th>Último login</th>
            <th style="width:160px">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($usuarios as $u): ?>
          <tr>
            <td><strong><?= htmlspecialchars($u['nome']) ?></strong></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><span class="badge-perfil <?= $u['perfil'] ?>"><?= ucfirst($u['perfil']) ?></span></td>
            <td>
              <?php if ($u['ativo']): ?>
                <span style="color:#155724;font-weight:700;font-size:.82rem">Ativo</span>
              <?php else: ?>
                <span class="badge-inativo">Inativo</span>
              <?php endif; ?>
            </td>
            <td style="font-size:.82rem;color:#888">
              <?= $u['ultimo_login'] ? date('d/m/Y H:i', strtotime($u['ultimo_login'])) : '—' ?>
            </td>
            <td>
              <div class="actions">
                <a href="usuario-atualiza.php?id=<?= $u['id'] ?>" class="btn-sm btn-warning">Editar</a>
                <?php if ($u['id'] != $_SESSION['usuario_id']): // não pode excluir a si mesmo ?>
                  <a href="usuario-exclui.php?id=<?= $u['id'] ?>"
                     class="btn-sm btn-danger"
                     onclick="return confirm('Excluir o usuário <?= htmlspecialchars($u['nome']) ?>?')">Excluir</a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  </div>

</body>
</html>