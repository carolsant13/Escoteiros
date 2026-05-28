<?php
// admin/documentos_action.php — processa todas as ações do painel de documentos
// Aceita somente POST, exige admin.
 
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/auth.php';
 
exigirAdminOuEditor();
 
// Só aceita POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: documentos.php');
    exit;
}
 
$db   = getDB();
$acao = trim($_POST['acao'] ?? '');
 
// Helper de redirecionamento com mensagem
function redir(string $msg, string $tipo = 'ok'): void {
    header('Location: documento.php?msg=' . urlencode($msg) . '&tipo=' . $tipo);
    exit;
}
 
// ================================================================
// AÇÃO: ADICIONAR
// ================================================================
if ($acao === 'adicionar') {
 
    // --- Categoria: prioriza campo "nova", cai para seleção existente
    $categoriaNova = trim($_POST['categoria_nova'] ?? '');
    $categoriaSel  = trim($_POST['categoria_sel']  ?? '');
    $categoria     = $categoriaNova !== '' ? $categoriaNova : $categoriaSel;
 
    if ($categoria === '') {
        redir('Selecione ou crie uma categoria.', 'erro');
    }
 
    $titulo     = trim($_POST['titulo']     ?? '');
    $descricao  = trim($_POST['descricao']  ?? '');
    $icone      = trim($_POST['icone']      ?? '📄');
    $ordem      = (int) ($_POST['ordem']    ?? 0);
    $tipoFonte  = $_POST['tipo_fonte']      ?? 'upload';
    $driveUrl   = trim($_POST['drive_url']  ?? '');
 
    if ($titulo === '') {
        redir('O título é obrigatório.', 'erro');
    }
 
    // Validações básicas por tipo de fonte
    $arquivePath = null;
 
    if ($tipoFonte === 'upload') {
        // ── Upload de arquivo ──────────────────────────────────────
        if (empty($_FILES['arquivo']['name'])) {
            redir('Selecione um arquivo para upload.', 'erro');
        }
 
        $file      = $_FILES['arquivo'];
        $maxBytes  = 20 * 1024 * 1024; // 20 MB
 
        if ($file['error'] !== UPLOAD_ERR_OK) {
            redir('Erro no upload (código ' . $file['error'] . '). Tente novamente.', 'erro');
        }
 
        if ($file['size'] > $maxBytes) {
            redir('O arquivo ultrapassa o limite de 20 MB.', 'erro');
        }
 
        // Extensões permitidas
        $extPermitidas = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $extPermitidas, true)) {
            redir('Tipo de arquivo não permitido. Use: ' . implode(', ', $extPermitidas), 'erro');
        }
 
        // Diretório de destino (relativo à raiz do site)
        $uploadDir  = __DIR__ . '/../uploads/documentos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
 
        // Nome único para evitar colisão
        $nomeSeguro  = preg_replace('/[^a-z0-9_\-]/i', '_', pathinfo($file['name'], PATHINFO_FILENAME));
        $nomeArquivo = date('Ymd_His') . '_' . $nomeSeguro . '.' . $ext;
        $destino     = $uploadDir . $nomeArquivo;
 
        if (!move_uploaded_file($file['tmp_name'], $destino)) {
            redir('Falha ao salvar o arquivo no servidor.', 'erro');
        }
 
        // Caminho relativo salvo no banco (a partir da raiz do site)
        $arquivePath = 'uploads/documentos/' . $nomeArquivo;
        $driveUrl    = null;
 
    } elseif ($tipoFonte === 'google_drive') {
        // ── Google Drive ──────────────────────────────────────────
        if ($driveUrl === '') {
            redir('Informe a URL do Google Drive.', 'erro');
        }
 
        // Aceita links do Drive e do Docs/Sheets/Slides
        if (!str_contains($driveUrl, 'drive.google.com') &&
            !str_contains($driveUrl, 'docs.google.com')) {
            redir('A URL informada não parece ser um link do Google Drive.', 'erro');
        }
 
        $arquivePath = null;
 
    } else {
        redir('Tipo de fonte inválido.', 'erro');
    }
 
    // Unifica: upload salva o caminho relativo, Drive salva a URL — ambos em arquivo_path
    $valorPath = ($tipoFonte === 'google_drive') ? ($driveUrl ?: null) : $arquivePath;
 
    // Insere no banco
    $stmt = $db->prepare("
        INSERT INTO documentos
            (categoria, titulo, descricao, icone, tipo_fonte, arquivo_path, ordem, ativo, criado_por)
        VALUES
            (:cat, :titulo, :desc, :icone, :fonte, :path, :ordem, 1, :uid)
    ");
    $stmt->execute([
        ':cat'   => $categoria,
        ':titulo'=> $titulo,
        ':desc'  => $descricao ?: null,
        ':icone' => $icone,
        ':fonte' => $tipoFonte,
        ':path'  => $valorPath,
        ':ordem' => $ordem,
        ':uid'   => $_SESSION['usuario_id'],
    ]);
 
    $novoId = (int) $db->lastInsertId();
 
    registrarLog('adicionou documento', 'documentos', $novoId, ['titulo' => $titulo]);
 
    redir('Documento "' . $titulo . '" adicionado com sucesso!');
}
 
// ================================================================
// AÇÃO: DESATIVAR / ATIVAR
// ================================================================
if ($acao === 'desativar' || $acao === 'ativar') {
 
    $id    = (int) ($_POST['id'] ?? 0);
    $ativo = ($acao === 'ativar') ? 1 : 0;
 
    if ($id <= 0) redir('ID inválido.', 'erro');
 
    $db->prepare("UPDATE documentos SET ativo = :a WHERE id = :id")
       ->execute([':a' => $ativo, ':id' => $id]);
 
    registrarLog($acao . ' documento', 'documentos', $id);
 
    redir('Documento ' . ($ativo ? 'ativado' : 'ocultado') . ' com sucesso.');
}
 
// ================================================================
// AÇÃO: EXCLUIR
// ================================================================
if ($acao === 'excluir') {
 
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) redir('ID inválido.', 'erro');
 
    // Busca para remover arquivo físico (se for upload)
    $doc = $db->prepare("SELECT tipo_fonte, arquivo_path, titulo FROM documentos WHERE id = :id");
    $doc->execute([':id' => $id]);
    $row = $doc->fetch();
 
    if (!$row) redir('Documento não encontrado.', 'erro');
 
    // Remove arquivo físico do servidor (se existir)
    if ($row['tipo_fonte'] === 'upload' && $row['arquivo_path']) {
        $caminho = __DIR__ . '/../' . $row['arquivo_path'];
        if (file_exists($caminho)) {
            unlink($caminho);
        }
    }
 
    $db->prepare("DELETE FROM documentos WHERE id = :id")->execute([':id' => $id]);
 
    registrarLog('excluiu documento', 'documentos', $id, ['titulo' => $row['titulo']]);
 
    redir('Documento excluído permanentemente.');
}
 
// Ação desconhecida
redir('Ação inválida.', 'erro');
 