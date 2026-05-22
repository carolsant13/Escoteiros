<?php
// admin/usuario-exclui.php — Excluir usuário | somente admin
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/auth.php';

exigirAdmin();

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: usuarios.php?msg=ID+inválido&tipo=erro');
    exit;
}

// Não pode excluir a si mesmo
if ($id === (int)$_SESSION['usuario_id']) {
    header('Location: usuarios.php?msg=' . urlencode('Você não pode excluir seu próprio usuário.') . '&tipo=erro');
    exit;
}

$db = getDB();

$stmt = $db->prepare("SELECT nome FROM usuarios WHERE id = :id");
$stmt->execute([':id' => $id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    header('Location: usuarios.php?msg=Usuário+não+encontrado&tipo=erro');
    exit;
}

$db->prepare("DELETE FROM usuarios WHERE id = :id")->execute([':id' => $id]);

registrarLog('excluiu usuário', 'usuarios', $id, ['nome' => $usuario['nome']]);

header('Location: usuarios.php?msg=' . urlencode('Usuário "' . $usuario['nome'] . '" excluído.') . '&tipo=ok');
exit;