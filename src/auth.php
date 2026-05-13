<?php
// src/auth.php autenticação da área restrita
// Banco: minuano - tabela: usuarios

require_once __DIR__ . '/../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// -------------------------------------------------------
// Exige que o usuário esteja logado.
// Qualquer perfil (admin, editor, visualizador) passa.
// -------------------------------------------------------
function exigirLogin(): void {
    if (empty($_SESSION['usuario_id'])) {
    header('Location: login.php');
        exit;
    }
}

// -------------------------------------------------------
// Exige perfil admin.
// Editor e visualizador são barrados por enquanto.

function exigirAdmin(): void {
    exigirLogin();
    if ($_SESSION['perfil'] !== 'admin') {
        header('Location: /admin/dashboard.php?erro=acesso_negado');
        exit;
    }
}


// Tenta logar: busca pelo e-mail, verifica senha e ativo.
// Atualiza o campo ultimo_login na tabela usuarios.
// Retorna array com dados ou false.

function tentarLogin(string $email, string $senha): array|false {
    $db = getDB();

    $stmt = $db->prepare("
        SELECT id, nome, email, senha, perfil
        FROM usuarios
        WHERE email = :email
          AND ativo = 1
        LIMIT 1
    ");
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch();

 
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        // Atualiza o último login
        $db->prepare("
            UPDATE usuarios SET ultimo_login = NOW() WHERE id = :id
        ")->execute([':id' => $usuario['id']]);

        unset($usuario['senha']); // nunca carrega o hash na sessão
        return $usuario;
    }

    return false;
}

// -------------------------------------------------------
// Salva os dados na sessão após login bem-sucedido.
// -------------------------------------------------------
function iniciarSessao(array $usuario): void {
    session_regenerate_id(true); // previne session fixation

    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['nome']        = $usuario['nome'];
    $_SESSION['email']       = $usuario['email'];
    $_SESSION['perfil']      = $usuario['perfil'];
}

// -------------------------------------------------------
// Destroi a sessão completamente (logout seguro).
// -------------------------------------------------------
function fazerLogout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
   header('Location: login.php');
    exit;
}

// -------------------------------------------------------
// Retorna os dados do usuário logado (da sessão).
// -------------------------------------------------------
function usuarioLogado(): array {
    return [
        'id'     => $_SESSION['usuario_id'] ?? null,
        'nome'   => $_SESSION['nome']        ?? '',
        'email'  => $_SESSION['email']       ?? '',
        'perfil' => $_SESSION['perfil']      ?? '',
    ];
}

// -------------------------------------------------------
// Registra uma ação no log de auditoria (tabela logs_admin).
// Uso: registrarLog('criou atividade', 'atividades', $id);
// -------------------------------------------------------
function registrarLog(string $acao, string $tabela = '', int $registroId = 0, array $detalhes = []): void {
    $usuario = usuarioLogado();
    if (!$usuario['id']) return;

    $db = getDB();
    $db->prepare("
        INSERT INTO logs_admin (usuario_id, acao, tabela, registro_id, detalhes, ip)
        VALUES (:uid, :acao, :tabela, :rid, :det, :ip)
    ")->execute([
        ':uid'    => $usuario['id'],
        ':acao'   => $acao,
        ':tabela' => $tabela ?: null,
        ':rid'    => $registroId ?: null,
        ':det'    => $detalhes ? json_encode($detalhes, JSON_UNESCAPED_UNICODE) : null,
        ':ip'     => $_SERVER['REMOTE_ADDR'] ?? null,
    ]);
}
