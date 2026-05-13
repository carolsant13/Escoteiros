<?php

// login.php 

require_once __DIR__ . '/src/auth.php';

// Se já está logado, vai direto pro dashboard
if (!empty($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $erro = 'Preencha e-mail e senha.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'E-mail inválido.';

    } else {
        $usuario = tentarLogin($email, $senha);

        if ($usuario) {
            iniciarSessao($usuario);
            header('Location: dashboard.php');
            exit;
        } else {
            $erro = 'E-mail ou senha incorretos.';
            sleep(1);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Área Restrita — 71º Grupo Escoteiros Minuano</title>
  <link rel="stylesheet" href="assets/css/login.css" >
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

  <div class="topo">
    <div class="topo-texto">
      <h1>71º Grupo de Escoteiros Minuano</h1>
      <p>São Paulo – SP &nbsp;|&nbsp; Fundado em 31/05/1981</p>
    </div>
  </div>

  <div class="pagina">
    <div class="card">

      <div class="card-cabecalho">
        <div class="icone">⚜️</div>
        <h2>Área Restrita</h2>
        <p>Acesso exclusivo para membros autorizados</p>
      </div>

      <div class="card-corpo">

        <?php if (!empty($erro)): ?>
          <div class="alerta erro">⚠ <?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['campos_obrigatorios'])): ?>
          <div class="alerta">⚠ Preencha todos os campos antes de continuar.</div>
        <?php endif; ?>

        <form method="POST" action="" autocomplete="off">

          <div class="campo">
            <label for="email">E-mail</label>
            <input
              type="email"
              id="email"
              name="email"
              placeholder="seu@email.com"
              value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
              required>
          </div>

          <div class="campo">
            <label for="senha">Senha</label>
            <input
              type="password"
              id="senha"
              name="senha"
              placeholder="••••••••"
              required>
          </div>

          <button type="submit" class="btn-entrar">Entrar</button>

        </form>

        <a href="index.html" class="voltar">← Voltar para o site</a>

      </div>
    </div>
  </div>

  <div class="rodape">
    &copy; <?= date('Y') ?> 71º Grupo de Escoteiros Minuano — Sempre Alerta
  </div>

</body>
</html>