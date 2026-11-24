<?php
// Tela avulsa da fila utilizada pelo worker de respostas de chamados.
date_default_timezone_set('America/Sao_Paulo');
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['usuario']) || empty($_SESSION['matricula'])) {
    header('Location: index.php');
    exit;
}
$perfil = (int)($_SESSION['perfil'] ?? 0);
if (!in_array($perfil, [4, 5, 6], true)) {
    http_response_code(403);
    exit('Acesso restrito aos perfis autorizados para gestão de chamados da Frota.');
}
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$nome = $_SESSION['nome'] ?? '';
$tipo = $_SESSION['tipo'] ?? null;
$filial = $_SESSION['filial'] ?? null;
header('Cache-Control: no-store');
require __DIR__ . '/components/mensagem_whatsapp.php';
$escape = static fn($value): string => htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$statusFiltro = (string)($_GET['status'] ?? 'todos');
$dataInicio = (string)($_GET['data_inicio'] ?? '');
$dataFim = (string)($_GET['data_fim'] ?? '');
$opcoesStatus = ['todos', 'pendentes', 'processados'];
if (!in_array($statusFiltro, $opcoesStatus, true)) {
    $statusFiltro = 'todos';
}
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataInicio)) {
    $dataInicio = '';
}
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataFim)) {
    $dataFim = '';
}
$messages = [];
$error = null;
if (empty($_SESSION['fila_frotas_csrf'])) $_SESSION['fila_frotas_csrf'] = bin2hex(random_bytes(32));
$notice = $_SESSION['fila_frotas_notice'] ?? null;
unset($_SESSION['fila_frotas_notice']);
$filtrou = true;
if (!isset($_GET['status']) && !isset($_GET['data_inicio']) && !isset($_GET['data_fim'])) {
    $statusFiltro = 'pendentes';
}
$urlFiltro = 'mensagens-pendentes.php' . ($filtrou ? '?' . http_build_query([
    'status' => $statusFiltro, 'data_inicio' => $dataInicio, 'data_fim' => $dataFim
]) : '');
try {
    // Usa a mesma conexão do script Python (bdfrota), priorizando conecta.php do projeto.
    $conexao = null;
    $arquivoConecta = __DIR__ . '/conecta.php';
    if (is_file($arquivoConecta)) {
        require $arquivoConecta;
        if (isset($conn) && $conn instanceof mysqli) {
            $conexao = $conn;
        }
    }
    if (!$conexao instanceof mysqli) {
        $conexao = @mysqli_connect('10.17.0.5', 'blvp', '@F3r7n6c2', 'bdfrota');
    }
    if (!$conexao instanceof mysqli) {
        throw new RuntimeException('Conexao MySQLi nao encontrada: ' . mysqli_connect_error());
    }
    mysqli_set_charset($conexao, 'utf8mb4');

    // Mesmas tabelas do worker Python (sem schema explícito).
    $tabelaFila = 'queue_ocorrencia_resposta';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!hash_equals($_SESSION['fila_frotas_csrf'], (string)($_POST['csrf'] ?? ''))) {
            throw new RuntimeException('Token de formulario invalido.');
        }
        $messageId = filter_var($_POST['mensagem_id'] ?? null, FILTER_VALIDATE_INT);
        if (!$messageId || $messageId < 1) throw new RuntimeException('Mensagem invalida.');
        $stmt = mysqli_prepare($conexao, "UPDATE {$tabelaFila}
            SET processed_at = NOW() WHERE id = ? AND processed_at IS NULL");
        if (!$stmt) {
            throw new RuntimeException('Falha ao preparar UPDATE: ' . mysqli_error($conexao));
        }
        mysqli_stmt_bind_param($stmt, 'i', $messageId);
        if (!mysqli_stmt_execute($stmt)) {
            throw new RuntimeException('Falha ao executar UPDATE: ' . mysqli_stmt_error($stmt));
        }
        $_SESSION['fila_frotas_notice'] = mysqli_stmt_affected_rows($stmt) > 0
            ? 'Mensagem marcada como processada.' : 'Mensagem ja processada ou indisponivel.';
        mysqli_stmt_close($stmt);
        header('Location: ' . $urlFiltro, true, 303);
        exit;
    }

    if ($filtrou) {
        $condicoes = [];
        $tipos = '';
        $binds = [];

        if ($statusFiltro === 'pendentes') {
            $condicoes[] = 'q.processed_at IS NULL';
        } elseif ($statusFiltro === 'processados') {
            $condicoes[] = 'q.processed_at IS NOT NULL';
        }

        $condicoes[] = "TRIM(COALESCE(q.resposta, '')) <> ''";
        $condicoes[] = "q.telefone IS NOT NULL AND TRIM(COALESCE(q.telefone, '')) <> ''";

        if ($dataInicio !== '') {
            $condicoes[] = 'DATE(q.created_at) >= ?';
            $tipos .= 's';
            $binds[] = $dataInicio;
        }
        if ($dataFim !== '') {
            $condicoes[] = 'DATE(q.created_at) <= ?';
            $tipos .= 's';
            $binds[] = $dataFim;
        }

        $sql = "SELECT q.*, o.tipodechamado, o.observacao, o.data_fech FROM {$tabelaFila} q LEFT JOIN tbchamados o ON o.idtbchamados = q.idtbchamados";
        if ($condicoes !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $condicoes);
        }
        $sql .= ' ORDER BY q.id DESC';

        $stmt = mysqli_prepare($conexao, $sql);
        if (!$stmt) {
            throw new RuntimeException('Falha ao preparar SELECT: ' . mysqli_error($conexao));
        }
        if ($binds !== []) {
            $refs = [];
            foreach ($binds as $key => $valor) {
                $refs[$key] = &$binds[$key];
            }
            mysqli_stmt_bind_param($stmt, $tipos, ...$refs);
        }
        if (!mysqli_stmt_execute($stmt)) {
            throw new RuntimeException('Falha ao executar SELECT: ' . mysqli_stmt_error($stmt));
        }
        $result = mysqli_stmt_get_result($stmt);
        if (!$result) {
            throw new RuntimeException('Falha ao obter resultado SELECT: ' . mysqli_stmt_error($stmt));
        }
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);
        foreach ($rows as $row) {
            $row['whatsapp_url'] = frotas_fila_whatsapp_url($row);
            $messages[] = $row;
        }
    }
} catch (Throwable $e) {
    error_log('Falha ao carregar mensagens pendentes: ' . $e->getMessage());
    $error = 'Não foi possível carregar a fila. Verifique conexão/tabela do bdfrota (queue_ocorrencia_resposta). Detalhe: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MSGS FROTA | Frota FFA</title>
    <link href="src/css/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@9.0.3/dist/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php require __DIR__ . '/menu.php'; ?>
<div id="layoutSidenav_content">
<main class="container-fluid px-4 py-4">
<div class="d-flex justify-content-between align-items-center mb-3">
    <div><h1 class="h3">Respostas de chamados da Frota</h1><p class="text-muted mb-0">Atualizações e situação de processamento.</p></div>
</div>
<div class="alert alert-info">Instruções: Aplique o filtro e visualize as mensagens programadas para envio no whatsapp. Caso deseje enviar, clique no ícone, visualize a prévia e em seguida envie. Caso contrário, marque como processada sem enviar</div>

<form method="get" class="row g-2 mb-3 align-items-end">
    <div class="col-md-3">
        <label class="form-label mb-1" for="statusFiltro">Status</label>
        <select class="form-select" id="statusFiltro" name="status">
            <option value="pendentes" <?= $statusFiltro === 'pendentes' ? 'selected' : '' ?>>Não processados</option>
            <option value="processados" <?= $statusFiltro === 'processados' ? 'selected' : '' ?>>Processados</option>
            <option value="todos" <?= $statusFiltro === 'todos' ? 'selected' : '' ?>>Todos</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label mb-1" for="dataInicio">De</label>
        <input type="date" class="form-control" id="dataInicio" name="data_inicio" value="<?= $escape($dataInicio) ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label mb-1" for="dataFim">Até</label>
        <input type="date" class="form-control" id="dataFim" name="data_fim" value="<?= $escape($dataFim) ?>">
    </div>
    <div class="col-md-1">
        <button type="submit" class="btn btn-primary w-100">Filtrar</button>
    </div>
    <div class="col-md-2">
        <a href="mensagens-pendentes.php" class="btn btn-outline-secondary w-100">Limpar</a>
    </div>
</form>

<?php if ($notice): ?><div class="alert alert-success" role="status"><?= $escape($notice) ?></div><?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-warning" role="alert"><?= $escape($error) ?></div>
<?php endif; ?>
    <div class="card border-0 shadow-sm"><div class="table-responsive">
        <table id="tabelaMensagens" class="table table-hover align-middle mb-0">
            <thead><tr><th>Chamado</th><th>Data</th><th>Tipo de chamado</th><th>Observação</th><th>Telefone</th><th>Resposta</th><th>Processamento / Ações</th></tr></thead>
            <tbody>
            <?php if ($filtrou): ?>
            <?php foreach ($messages as $message): ?>
                <?php
                $telefone = trim((string)($message['telefone'] ?? ''));
                $resposta = trim((string)($message['resposta'] ?? ''));
                if ($telefone === '' || $resposta === '') {
                    continue;
                }
                ?>
                <tr>
                    <td><?= $escape($message['idtbchamados']) ?></td>
                    <td><?= $escape($message['created_at']) ?></td>
                    <td><?= $escape($message['tipodechamado'] ?? '') ?></td>
                    <td style="max-width:250px"><div style="white-space:pre-wrap;overflow-wrap:anywhere"><?= $escape($message['observacao'] ?? '') ?></div></td>
                    <td><?= $escape($telefone) ?> <button type="button" class="btn btn-sm btn-outline-secondary copiar-texto" data-texto="<?= $escape($telefone) ?>" aria-label="Copiar telefone"><i class="fa-solid fa-copy" aria-hidden="true"></i></button></td>
                    <td style="min-width:260px;max-width:500px"><details><summary class="text-truncate" style="max-width:400px"><?= $escape(frotas_fila_resumo($message)) ?></summary><div class="mt-2" style="white-space:pre-wrap;overflow-wrap:anywhere"><?= $escape(frotas_fila_texto($message)) ?></div></details><button type="button" class="btn btn-sm btn-outline-secondary copiar-texto mt-2" data-texto="<?= $escape(frotas_fila_texto($message)) ?>"><i class="fa-solid fa-copy" aria-hidden="true"></i> Copiar mensagem</button></td>
                    <td>
                        <?php if ($message['processed_at'] === null): ?>
                        <form method="post" action="<?= $escape($urlFiltro) ?>" class="mb-2" onsubmit="return confirm('Marcar esta mensagem como processada?');">
                            <input type="hidden" name="csrf" value="<?= $escape($_SESSION['fila_frotas_csrf']) ?>">
                            <input type="hidden" name="mensagem_id" value="<?= $escape($message['id']) ?>">
                            <button type="submit" class="btn btn-sm btn-primary">Marcar como processada</button>
                        </form>
                        <?php if ($message['whatsapp_url']): ?>
                            <a class="btn btn-success" href="<?= $escape($message['whatsapp_url']) ?>" target="_blank" rel="noopener noreferrer" aria-label="Abrir WhatsApp para <?= $escape($message['telefone']) ?>"><i class="fab fa-whatsapp" aria-hidden="true"></i> Enviar</a>
                        <?php else: ?>
                            <span class="text-muted small">Confira telefone e mensagem</span>
                        <?php endif; ?>
                        <?php else: ?>
                            <span class="badge bg-success">Processada</span>
                            <small class="d-block text-muted"><?= $escape($message['processed_at']) ?></small>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div></div>

</main></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@9.0.3/dist/umd/simple-datatables.js"></script>
<script>
if (window.simpleDatatables) {
    new simpleDatatables.DataTable('#tabelaMensagens', {
        perPage: 25,
        perPageSelect: [10, 25, 50, 100],
        columns: [{ select: 6, sortable: false }],
        labels: {
            placeholder: 'Buscar mensagens...',
            perPage: 'registros por página',
            noRows: 'Nenhuma mensagem na fila.',
            noResults: 'Nenhuma mensagem encontrada.',
            info: 'Exibindo {start} a {end} de {rows} mensagens'
        }
    });
}

document.addEventListener('click', async function (event) {
    const botao = event.target.closest('.copiar-texto');
    if (!botao || botao.disabled) return;
    botao.disabled = true;
    try {
        const texto = botao.dataset.texto;
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(texto);
        } else {
            const campo = document.createElement('textarea');
            campo.value = texto;
            campo.style.position = 'fixed';
            campo.style.opacity = '0';
            document.body.appendChild(campo);
            try {
                campo.select();
                if (!document.execCommand('copy')) throw new Error('Cópia indisponível');
            } finally {
                campo.remove();
            }
        }
        alert('Copiado para a área de transferência.');
    } catch (error) {
        alert('Não foi possível copiar. Selecione o texto e copie manualmente.');
    } finally {
        botao.disabled = false;
    }
});
document.getElementById('sidebarToggle')?.addEventListener('click', function () {
    document.body.classList.toggle('sb-sidenav-toggled');
});
</script>
</body></html>