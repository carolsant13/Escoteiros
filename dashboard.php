<?php

require_once __DIR__ . '/src/auth.php';

exigirLogin(); // Se não estiver logado, redireciona para login.php

$usuario = usuarioLogado();
?>
<!DOCTYPE html>
<html lang="pt-BR">

    

     <!-- //DASHBOARD 

     //$usuario['nome']    nome do usuário logado
     //$usuario['email']  e-mail do usuário logado
     //$usuario['perfil']  'admin' ou 'user'

     Para mostrar algo só para admin: -->
     <?php if ($usuario['perfil'] === 'admin'): ?>
       <a href="/usuarios.php">Gerenciar usuários</a>
     <?php endif; ?>


<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
</head>
<body>
  <h1>Olá, <?= htmlspecialchars($usuario['nome']) ?>!</h1>
  <p>Perfil: <?= htmlspecialchars($usuario['perfil']) ?></p>

  <?php if ($usuario['perfil'] === 'admin'): ?>
    <a href="/usuarios.php">Gerenciar usuários</a>
  <?php endif; ?>

  <a href="logout.php">Sair</a>

</body>
</html>
