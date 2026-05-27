<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/auth.php';

exigirAdminOuEditor();

$db   = getDB();
$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

// ── Criar galeria ──────────────────────────────────────────────────────────
if ($acao === 'criar_galeria') {
    $titulo    = trim($_POST['titulo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $tipo      = $_POST['tipo'] ?? 'outro';
    $publicado = isset($_POST['publicado']) ? 1 : 0;

    if (!$titulo) {
        header('Location: galeria.php?msg=Título+obrigatório&tipo=erro');
        exit;
    }

    $tipos_validos = ['acampamento', 'reuniao', 'evento', 'servico', 'outro'];
    if (!in_array($tipo, $tipos_validos)) $tipo = 'outro';

    $stmt = $db->prepare("INSERT INTO galerias (titulo, descricao, tipo, publicado) VALUES (?,?,?,?)");
    $stmt->execute([$titulo, $descricao, $tipo, $publicado]);
    $id = $db->lastInsertId();

    header("Location: galeria-fotos.php?id=$id&msg=Galeria+criada+com+sucesso&tipo=ok");
    exit;
}

// ── Excluir galeria ────────────────────────────────────────────────────────
if ($acao === 'excluir_galeria') {
    $id = (int)($_POST['id'] ?? 0);

    $fotos = $db->prepare("SELECT url FROM fotos WHERE galeria_id = ?");
    $fotos->execute([$id]);
    foreach ($fotos->fetchAll() as $f) {
        $path = __DIR__ . '/../' . $f['url'];
        if (is_file($path)) @unlink($path);
    }

    $db->prepare("DELETE FROM galerias WHERE id = ?")->execute([$id]);
    header('Location: galeria.php?msg=Galeria+excluída&tipo=ok');
    exit;
}

// ── Toggle publicado ───────────────────────────────────────────────────────
if ($acao === 'toggle_publicado') {
    $id = (int)($_GET['id'] ?? 0);
    $db->prepare("UPDATE galerias SET publicado = NOT publicado WHERE id = ?")->execute([$id]);
    header('Location: galeria.php?msg=Status+atualizado&tipo=ok');
    exit;
}

// ── Upload de foto ─────────────────────────────────────────────────────────
if ($acao === 'upload_foto') {
    $galeria_id = (int)($_POST['galeria_id'] ?? 0);
    $legenda    = trim($_POST['legenda'] ?? '');

    if (!$galeria_id) {
        header('Location: galeria.php?msg=Galeria+inválida&tipo=erro');
        exit;
    }

    $redir = "galeria-fotos.php?id=$galeria_id";

    if (empty($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        header("Location: $redir&msg=Erro+no+upload+do+arquivo&tipo=erro");
        exit;
    }

    $mime_permitidos = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $_FILES['foto']['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $mime_permitidos)) {
        header("Location: $redir&msg=Tipo+de+arquivo+inválido+(use+JPG,+PNG+ou+WebP)&tipo=erro");
        exit;
    }

    if ($_FILES['foto']['size'] > 8 * 1024 * 1024) {
        header("Location: $redir&msg=Arquivo+muito+grande+(máx+8+MB)&tipo=erro");
        exit;
    }

    $ext     = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $nome    = uniqid('foto_', true) . '.' . $ext;
    $dir     = __DIR__ . '/../assets/uploads/galeria/';

    if (!is_dir($dir)) mkdir($dir, 0755, true);

    if (!move_uploaded_file($_FILES['foto']['tmp_name'], $dir . $nome)) {
        header("Location: $redir&msg=Falha+ao+salvar+o+arquivo&tipo=erro");
        exit;
    }

    $url  = 'assets/uploads/galeria/' . $nome;
    $stmt = $db->prepare("INSERT INTO fotos (galeria_id, url, legenda) VALUES (?,?,?)");
    $stmt->execute([$galeria_id, $url, $legenda]);

    header("Location: $redir&msg=Foto+adicionada+com+sucesso&tipo=ok");
    exit;
}

// ── Excluir foto ───────────────────────────────────────────────────────────
if ($acao === 'excluir_foto') {
    $foto_id    = (int)($_POST['foto_id'] ?? 0);
    $galeria_id = (int)($_POST['galeria_id'] ?? 0);

    $stmt = $db->prepare("SELECT url FROM fotos WHERE id = ?");
    $stmt->execute([$foto_id]);
    $foto = $stmt->fetch();

    if ($foto) {
        $path = __DIR__ . '/../' . $foto['url'];
        if (is_file($path)) @unlink($path);
        $db->prepare("DELETE FROM fotos WHERE id = ?")->execute([$foto_id]);
    }

    header("Location: galeria-fotos.php?id=$galeria_id&msg=Foto+excluída&tipo=ok");
    exit;
}

header('Location: galeria.php');
exit;
