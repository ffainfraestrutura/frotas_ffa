<?php

date_default_timezone_set('America/Sao_Paulo');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params(3600);
    session_start();
}

$nome1 = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula1 = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$tipo = $_SESSION['tipo'];

if ($perfil == null) {
    echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
    exit;
} elseif ($perfil != '4') {
    echo "<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
    exit;
}

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula1;
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo'] = $tipo;

$matricula = isset($_POST['matricula']) ? $_POST['matricula'] : '';

include_once __DIR__ . '/../conecta2.php';
mysqli_set_charset($conexao, 'utf8mb4');
header('Content-type: text/html; charset=utf-8');

function formularioEscapar($valor)
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function formularioCsrfToken()
{
    if (empty($_SESSION['formulario_csrf'])) {
        $_SESSION['formulario_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['formulario_csrf'];
}

function formularioValidarCsrf($token)
{
    return !empty($_SESSION['formulario_csrf'])
        && is_string($token)
        && hash_equals($_SESSION['formulario_csrf'], $token);
}

function formularioBuscar($conexao, $id)
{
    $sql = 'SELECT id, data, cc, motorista_nome, motorista_matricula, placa, defeito,
                   servico_executado, valor_total, autorizado_por, autorizado_matricula,
                   endereco, criado_em, atualizado_em
            FROM bdfrota.tbconsertopneu WHERE id = ?';
    $stmt = mysqli_prepare($conexao, $sql);
    if (!$stmt) {
        return null;
    }
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $registro = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);

    return $registro ?: null;
}
