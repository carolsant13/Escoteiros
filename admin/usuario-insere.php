<?php
// admin/usuario-insere.php — Inserir novo usuário | somente admin
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/auth.php';

exigirAdmin();

$db   = getDB();
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome   = trim($_POST['nome']   ?? '');
    $email  = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $perfil = $_POST['perfil'] ?? '';
    $senha  = $_POST['senha']  ?? '';

    // Validações
    if (empty($nome) || empty($email) || empty($senha) || empty($perfil)) {
        $erro = 'Preencha todos os campos obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'E-mail inválido.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter pelo menos 6 caracteres.';
    } elseif (!in_array($perfil, ['admin', 'editor'])) {
        $erro = 'Perfil inválido.';
    } else {
        // Verifica se email já existe
        $check = $db->prepare("SELECT id FROM usuarios WHERE email = :email");
        $check->execute([':email' => $email]);
        if ($check->fetch()) {
            $erro = 'Este e-mail já está cadastrado.';
        } else {
            $hash = password_hash($senha, PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt = $db->prepare("
                INSERT INTO usuarios (nome, email, senha, perfil, ativo)
                VALUES (:nome, :email, :senha, :perfil, 1)
            ");
            $stmt->execute([
                ':nome'   => $nome,
                ':email'  => $email,
                ':senha'  => $hash,
                ':perfil' => $perfil,
            ]);

            registrarLog('inseriu usuário', 'usuarios', (int)$db->lastInsertId(), ['nome' => $nome]);

            header('Location: usuarios.php?msg=' . urlencode('Usuário "' . $nome . '" cadastrado com sucesso!') . '&tipo=ok');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inserir Usuário | Admin — 71º GE Minuano</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/main.css">
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

  <div class="topo">
    <div class="topo-texto">
      <h1>71º Grupo de Escoteiros Minuano</h1>
      <p>Área Restrita — Inserir Usuário</p>
    </div>
    <div class="topo-direita">
      <a href="usuarios.php" class="btn-voltar">← Voltar</a>
      <a href="../logout.php" class="btn-sair">Sair</a>
    </div>
  </div>

  <div class="admin-wrap">
    <div class="card">
      <h2>Inserir novo usuário</h2>

      <?php if ($erro): ?>
        <div class="feedback erro"><?= htmlspecialchars($erro) ?></div>
      <?php endif; ?>

      <form method="POST" action="" autocomplete="off">

        <div class="form-group">
          <label for="nome">Nome *</label>
          <input type="text" id="nome" name="nome" required maxlength="100"
                 value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label for="email">E-mail *</label>
          <input type="email" id="email" name="email" required maxlength="150"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label for="senha">Senha * <small style="font-weight:400;text-transform:none">(mínimo 6 caracteres)</small></label>
          <input type="password" id="senha" name="senha" required minlength="6">
        </div>

        <div class="form-group">
          <label for="perfil">Perfil *</label>
          <select id="perfil" name="perfil" required>
            <option value="">— Selecione —</option>
            <option value="admin"        <?= ($_POST['perfil'] ?? '') === 'admin'         ? 'selected' : '' ?>>Admin — acesso total</option>
            <option value="editor"       <?= ($_POST['perfil'] ?? '') === 'editor'        ? 'selected' : '' ?>>Editor — atividades e documentos</option>
            </select>
        </div>

        <button type="submit" class="btn-primary" style="margin-top:8px">Inserir Usuário</button>

      </form>
    </div>
  </div>

</body>
</html>