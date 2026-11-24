<?php
session_start();
include __DIR__ . '/../conecta.php';

$id = (int) ($_POST['idinserido'] ?? 0);
$tokenRecebido = (string) ($_POST['csrf_token'] ?? '');
$tokenSessao = (string) ($_SESSION['checklist_csrf_token'] ?? '');

if ($_SERVER['REQUEST_METHOD'] !== 'POST'
    || $id < 1
    || $tokenSessao === ''
    || !hash_equals($tokenSessao, $tokenRecebido)) {
    http_response_code(400);
    exit('Não foi possível excluir o checklist.');
}

mysqli_begin_transaction($con);

try {
    $stmtFotos = mysqli_prepare($con, 'DELETE FROM bdfrota.tbvistoriafotos WHERE idtbvistoria = ?');
    mysqli_stmt_bind_param($stmtFotos, 'i', $id);
    mysqli_stmt_execute($stmtFotos);

    $stmtVistoria = mysqli_prepare($con, 'DELETE FROM bdfrota.tbvistoria WHERE idtbvistoria = ? AND statusreg = 0');
    mysqli_stmt_bind_param($stmtVistoria, 'i', $id);
    mysqli_stmt_execute($stmtVistoria);

    if (mysqli_stmt_affected_rows($stmtVistoria) !== 1) {
        throw new RuntimeException('Vistoria pendente não encontrada.');
    }

    mysqli_commit($con);
    unset($_SESSION['checklist_csrf_token']);
    header('Location: ../checklistinicio.php?excluido=1');
    exit;
} catch (Throwable $erro) {
    mysqli_rollback($con);
    error_log('[excluir-checklist] ID ' . $id . ': ' . $erro->getMessage());
    http_response_code(500);
    echo 'Não foi possível excluir o checklist pendente.';
}
