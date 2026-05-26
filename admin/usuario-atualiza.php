<?php
// admin/usuario-atualiza.php — Editar usuário | somente admin
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/auth.php';

exigirAdmin();

$db = getDB();
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: usuarios.php?msg=ID+inválido&tipo=erro');
    exit;
}

// Busca o usuário
$stmt = $db->prepare("SELECT id, nome, email, perfil, ativo FROM usuarios WHERE id = :id");
$stmt->execute([':id' => $id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    header('Location: usuarios.php?msg=Usuário+não+encontrado&tipo=erro');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome   = trim($_POST['nome']   ?? '');
    $email  = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $perfil = $_POST['perfil'] ?? '';
    $senha  = $_POST['senha']  ?? '';

    if (empty($nome) || empty($email) || empty($perfil)) {
        $erro = 'Nome, e-mail e perfil são obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'E-mail inválido.';
    } elseif (!in_array($perfil, ['admin', 'editor'])) {
        $erro = 'Perfil inválido.';
    } else {
        // Verifica email duplicado (exceto o próprio)
        $check = $db->prepare("SELECT id FROM usuarios WHERE email = :email AND id != :id");
        $check->execute([':email' => $email, ':id' => $id]);
        if ($check->fetch()) {
            $erro = 'Este e-mail já está em uso por outro usuário.';
        } else {
            // Se senha foi preenchida, gera novo hash; senão mantém a atual
            if (!empty($senha)) {
                if (strlen($senha) < 6) {
                    $erro = 'A nova senha deve ter pelo menos 6 caracteres.';
                    goto fim;
                }
                $novaHash = password_hash($senha, PASSWORD_BCRYPT, ['cost' => 12]);
                $db->prepare("UPDATE usuarios SET nome=:nome, email=:email, senha=:senha, perfil=:perfil WHERE id=:id")
                   ->execute([':nome' => $nome, ':email' => $email, ':senha' => $novaHash, ':perfil' => $perfil, ':id' => $id]);
            } else {
                $db->prepare("UPDATE usuarios SET nome=:nome, email=:email, perfil=:perfil WHERE id=:id")
                   ->execute([':nome' => $nome, ':email' => $email, ':perfil' => $perfil, ':id' => $id]);
            }

            registrarLog('atualizou usuário', 'usuarios', $id, ['nome' => $nome]);

            header('Location: usuarios.php?msg=' . urlencode('Usuário "' . $nome . '" atualizado com sucesso!') . '&tipo=ok');
            exit;
        }
    }
    fim:
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Usuário | Admin — 71º GE Minuano</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/main.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

  <div class="topo">
    <div class="topo-texto">
      <h1>71º Grupo de Escoteiros Minuano</h1>
      <p>Área Restrita — Editar Usuário</p>
    </div>
    <div class="topo-direita">
      <a href="usuarios.php" class="btn-voltar">← Voltar</a>
      <a href="../logout.php" class="btn-sair">Sair</a>
    </div>
  </div>

  <div class="admin-wrap">
    <div class="card">
      <h2>Editar usuário</h2>

      <?php if ($erro): ?>
        <div class="feedback erro"><?= htmlspecialchars($erro) ?></div>
      <?php endif; ?>

      <form method="POST" action="" autocomplete="off">

        <div class="form-group">
          <label for="nome">Nome *</label>
          <input type="text" id="nome" name="nome" required maxlength="100"
                 value="<?= htmlspecialchars($_POST['nome'] ?? $usuario['nome']) ?>">
        </div>

        <div class="form-group">
          <label for="email">E-mail *</label>
          <input type="email" id="email" name="email" required maxlength="150"
                 value="<?= htmlspecialchars($_POST['email'] ?? $usuario['email']) ?>">
        </div>

        <div class="form-group">
          <label for="senha">Nova senha <small style="font-weight:400;text-transform:none">(deixe em branco para manter a atual)</small></label>
          <input type="password" id="senha" name="senha" minlength="6" placeholder="••••••••">
        </div>

        <div class="form-group">
          <label for="perfil">Perfil *</label>
          <select id="perfil" name="perfil" required>
            <option value="">— Selecione —</option>
            <?php
            $perfilAtual = $_POST['perfil'] ?? $usuario['perfil'];
            foreach (['admin' => 'Admin — acesso total', 'editor' => 'Editor — atividades e documentos'] as $val => $label):
            ?>
              <option value="<?= $val ?>" <?= $perfilAtual === $val ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <button type="submit" class="btn-primary" style="margin-top:8px">Salvar alterações</button>

      </form>
    </div>
  </div>

</body>
</html>