<?php
// admin/atividade-exclui.php — Excluir atividade | admin e editor
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/auth.php';

exigirLogin();
$usuario = usuarioLogado();
if (!in_array($usuario['perfil'], ['admin', 'editor'])) {
    header('Location: ../dashboard.php?erro=acesso_negado');
    exit;
}

$db = getDB();
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: atividades.php?msg=ID+inválido&tipo=erro');
    exit;
}

$stmt = $db->prepare("SELECT titulo FROM atividades WHERE id = :id");
$stmt->execute([':id' => $id]);
$atividade = $stmt->fetch();

if (!$atividade) {
    header('Location: atividades.php?msg=Atividade+não+encontrada&tipo=erro');
    exit;
}

// Remove ramos vinculados e depois a atividade (FK CASCADE já faz, mas por segurança)
$db->prepare("DELETE FROM atividade_ramo WHERE atividade_id = :id")->execute([':id' => $id]);
$db->prepare("DELETE FROM atividades WHERE id = :id")->execute([':id' => $id]);

registrarLog('excluiu atividade', 'atividades', $id, ['titulo' => $atividade['titulo']]);

header('Location: atividades.php?msg=' . urlencode('Atividade "' . $atividade['titulo'] . '" excluída.') . '&tipo=ok');
exit;