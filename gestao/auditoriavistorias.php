<?php
declare(strict_types=1);
ini_set('log_errors', '1');
ini_set('error_log', '/tmp/auditoria-vistorias-php.log');
error_reporting(E_ALL);

/**
 * Tela de consulta da auditoria de vistorias.
 * Configure as variáveis de ambiente MYSQL_HOST, MYSQL_PORT, MYSQL_DATABASE,
 * MYSQL_USER e MYSQL_PASSWORD no servidor PHP.
 */

function envValue(string $key, string $default = ''): string
{
    $value = getenv($key);
    return $value === false ? $default : $value;
}

function h($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function dateValue(string $value): bool
{
    if ($value === '') {
        return true;
    }
    $date = DateTime::createFromFormat('!Y-m-d', $value);
    return $date !== false && $date->format('Y-m-d') === $value;
}

$statuses = [
    'PENDENTE' => 'Pendente',
    'PROCESSANDO' => 'Processando',
    'SEM_INCONSISTENCIA' => 'Sem inconsistência',
    'INCONSISTENCIA' => 'Inconsistência',
    'INCONCLUSIVA' => 'Inconclusiva',
    'ERRO' => 'Erro',
];

$placa = strtoupper(trim((string)($_GET['placa'] ?? '')));
$status = strtoupper(trim((string)($_GET['status'] ?? '')));
$dataInicio = trim((string)($_GET['data_inicio'] ?? '2026-04-01'));
$dataFim = trim((string)($_GET['data_fim'] ?? ''));
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]) ?: 1;
$perPage = 50;
$offset = ($page - 1) * $perPage;

$errors = [];
if ($status !== '' && $status !== 'NAO_ANALISADA' && !array_key_exists($status, $statuses)) {
    $errors[] = 'O status informado é inválido.';
    $status = '';
}
if (!dateValue($dataInicio) || !dateValue($dataFim)) {
    $errors[] = 'Informe as datas no formato válido AAAA-MM-DD.';
    $dataInicio = '2026-04-01';
    $dataFim = '';
}
if ($dataInicio !== '' && $dataFim !== '' && $dataInicio > $dataFim) {
    $errors[] = 'A data inicial não pode ser posterior à data final.';
}

$rows = [];
$total = 0;
$summary = ['total' => 0, 'inconsistencias' => 0, 'sem_inconsistencia' => 0, 'pendentes' => 0];
$dbError = null;

try {
    $host = envValue('MYSQL_HOST', '127.0.0.1');
    $port = envValue('MYSQL_PORT', '3306');
    $database = envValue('MYSQL_DATABASE', 'bdfrota');
    $username = envValue('MYSQL_USER', 'blvp');
    $password = envValue('MYSQL_PASSWORD', '@F3r7n6c2');

    if ($username === '') {
        throw new RuntimeException('Configure MYSQL_USER e as demais variáveis de ambiente do banco de dados.');
    }

    $pdo = new PDO(
        "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    $where = ['tv.datavistoria >= :data_inicio'];
    $params = [':data_inicio' => $dataInicio !== '' ? $dataInicio . ' 00:00:00' : '2026-04-01 00:00:00'];

    if ($dataFim !== '') {
        $where[] = 'tv.datavistoria < :data_fim';
        $params[':data_fim'] = (new DateTime($dataFim))->modify('+1 day')->format('Y-m-d 00:00:00');
    }
    if ($placa !== '') {
        $where[] = 'tv.placa LIKE :placa';
        $params[':placa'] = '%' . $placa . '%';
    }
    if ($status === 'NAO_ANALISADA') {
        $where[] = 'aud.id_auditoria IS NULL';
    } elseif ($status !== '') {
        $where[] = 'aud.status = :status';
        $params[':status'] = $status;
    }

    $whereSql = implode(' AND ', $where);
    $fromSql = "
        FROM bdfrota.tbvistoria tv
        JOIN bdfrota.tbvistoriaauditoriaia aud ON aud.idtbvistoria = tv.idtbvistoria
        LEFT JOIN bdfrota.tbveiculo tve ON tve.placa = tv.placa
        LEFT JOIN bdfrota.tbmodeloveic tm ON tm.idtbmodeloveic = tve.modelo
        LEFT JOIN bdfrota.tbmarcaveic tmv ON tmv.idtbmarcaveic = tve.marca
        WHERE {$whereSql}
    ";

    $countStmt = $pdo->prepare("SELECT COUNT(*) {$fromSql}");
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();

    $summarySql = "
        SELECT
            COUNT(*) AS total,
            SUM(aud.status = 'INCONSISTENCIA') AS inconsistencias,
            SUM(aud.status = 'SEM_INCONSISTENCIA') AS sem_inconsistencia,
            SUM(aud.id_auditoria IS NULL OR aud.status IN ('PENDENTE','PROCESSANDO','INCONCLUSIVA','ERRO')) AS pendentes
        {$fromSql}
    ";
    $summaryStmt = $pdo->prepare($summarySql);
    $summaryStmt->execute($params);
    $summaryRow = $summaryStmt->fetch() ?: [];
    $summary = [
        'total' => (int)($summaryRow['total'] ?? 0),
        'inconsistencias' => (int)($summaryRow['inconsistencias'] ?? 0),
        'sem_inconsistencia' => (int)($summaryRow['sem_inconsistencia'] ?? 0),
        'pendentes' => (int)($summaryRow['pendentes'] ?? 0),
    ];

    $sql = "
        SELECT
            tv.idtbvistoria,
            tv.placa,
            tv.datavistoria,
            tv.tipo,
            tve.unidade,
            tm.modelo,
            tmv.marca,
            aud.id_auditoria,
            aud.status AS auditoria_status,
            aud.verificado,
            aud.inconsistente,
            aud.motivo,
            aud.detalhes,
            aud.erro,
            aud.modelo_ia,
            aud.tentativas,
            aud.criado_em,
            aud.atualizado_em,
            aud.analisado_em
        {$fromSql}
        ORDER BY tv.datavistoria DESC, tv.idtbvistoria DESC
        LIMIT {$perPage} OFFSET {$offset}
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
} catch (Throwable $e) {
    // O detalhe técnico fica no log do servidor, não é exibido ao usuário.
    error_log('[auditoria-vistorias-php] ' . $e->getMessage());
    $dbError = $e instanceof RuntimeException && strpos($e->getMessage(), 'Configure MYSQL_USER') === 0
        ? $e->getMessage()
        : 'Não foi possível consultar a auditoria. Verifique a conexão e as permissões do banco.';
}

$totalPages = max(1, (int)ceil($total / $perPage));
$queryBase = [
    'placa' => $placa,
    'status' => $status,
    'data_inicio' => $dataInicio,
    'data_fim' => $dataFim,
];

function statusClass(?string $status): string
{
    switch ($status) {
        case 'SEM_INCONSISTENCIA': return 'success';
        case 'INCONSISTENCIA': return 'danger';
        case 'INCONCLUSIVA': return 'warning';
        case 'ERRO': return 'danger';
        case 'PROCESSANDO': return 'info';
        default: return 'secondary';
    }
}

function statusLabel(?string $status, array $labels): string
{
    return $status === null || $status === '' ? 'Não analisada' : ($labels[$status] ?? $status);
}

function detailsSummary($json): array
{
    if (!is_string($json) || trim($json) === '') {
        return [];
    }
    $decoded = json_decode($json, true);
    return is_array($decoded) ? $decoded : [];
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Auditoria de vistorias</title>
    <style>
        :root { color-scheme: light; --bg:#f4f6f9; --text:#243041; --muted:#64748b; --border:#dce2ea; --primary:#0d6efd; }
        * { box-sizing: border-box; }
        body { margin:0; background:var(--bg); color:var(--text); font:14px/1.45 Arial, Helvetica, sans-serif; }
        .container { max-width:1600px; margin:0 auto; padding:24px; }
        h1 { margin:0 0 5px; font-size:26px; }
        .subtitle { margin:0 0 22px; color:var(--muted); }
        .cards { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:14px; margin-bottom:20px; }
        .card, .panel { background:#fff; border:1px solid var(--border); border-radius:10px; box-shadow:0 2px 8px rgba(20,35,55,.04); }
        .card { padding:16px; }
        .card .label { color:var(--muted); font-size:12px; text-transform:uppercase; letter-spacing:.04em; }
        .card .number { font-size:27px; font-weight:700; margin-top:5px; }
        .panel { padding:18px; margin-bottom:18px; }
        .filters { display:grid; grid-template-columns:1.2fr 1fr 1fr 1fr auto auto; align-items:end; gap:12px; }
        label { display:block; font-size:12px; color:#475569; margin-bottom:5px; font-weight:700; }
        input, select { width:100%; min-height:38px; border:1px solid #cbd5e1; border-radius:6px; padding:8px 10px; background:#fff; color:var(--text); }
        .btn { display:inline-flex; align-items:center; justify-content:center; border:0; border-radius:6px; padding:9px 12px; font-weight:700; font-size:12px; cursor:pointer; text-decoration:none; white-space:nowrap; }
        .btn-primary { background:#0d6efd; color:#fff; }
        .btn-success { background:#198754; color:#fff; }
        .btn-light { background:#e9eef5; color:#243041; }
        .btn:focus, input:focus, select:focus { outline:2px solid #93c5fd; outline-offset:1px; }
        .table-wrap { overflow-x:auto; }
        table { width:100%; border-collapse:collapse; min-width:1250px; }
        th, td { border-bottom:1px solid #e5eaf0; text-align:left; padding:11px 10px; vertical-align:top; }
        th { background:#f8fafc; color:#475569; font-size:11px; text-transform:uppercase; letter-spacing:.03em; position:sticky; top:0; }
        tbody tr:hover { background:#fafcff; }
        .badge { display:inline-block; border-radius:20px; padding:4px 8px; font-size:11px; font-weight:700; }
        .badge-success { background:#d1fae5; color:#065f46; }
        .badge-danger { background:#fee2e2; color:#991b1b; }
        .badge-warning { background:#fef3c7; color:#92400e; }
        .badge-info { background:#dbeafe; color:#1e40af; }
        .badge-secondary { background:#e2e8f0; color:#475569; }
        .motivo { min-width:230px; max-width:360px; white-space:normal; overflow-wrap:anywhere; }
        .actions { display:flex; flex-direction:column; gap:7px; min-width:130px; }
        .muted { color:var(--muted); font-size:12px; }
        .error { border:1px solid #fecaca; background:#fef2f2; color:#991b1b; padding:12px 14px; border-radius:8px; margin-bottom:16px; }
        .empty { text-align:center; padding:34px 12px; color:var(--muted); }
        .pagination { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-top:16px; }
        .pagination .links { display:flex; gap:8px; }
        form.inline { margin:0; }
        @media (max-width:900px) { .container{padding:14px}.cards{grid-template-columns:repeat(2,minmax(0,1fr))}.filters{grid-template-columns:repeat(2,minmax(0,1fr))}.filters .btn{width:100%} }
        @media (max-width:480px) { .cards{grid-template-columns:1fr 1fr}.card .number{font-size:22px}.filters{grid-template-columns:1fr} }
    </style>
</head>
<body>
<div class="container">
    <h1>Auditoria de vistorias</h1>
    <p class="subtitle">Análise das fotos e dos dados cadastrados do veículo.</p>

    <?php if ($dbError !== null): ?>
        <div class="error"><?= h($dbError) ?></div>
    <?php endif; ?>
    <?php foreach ($errors as $error): ?>
        <div class="error"><?= h($error) ?></div>
    <?php endforeach; ?>

    <div class="cards">
        <div class="card"><div class="label">Vistorias no filtro</div><div class="number"><?= number_format($summary['total'], 0, ',', '.') ?></div></div>
        <div class="card"><div class="label">Inconsistências</div><div class="number"><?= number_format($summary['inconsistencias'], 0, ',', '.') ?></div></div>
        <div class="card"><div class="label">Sem inconsistência</div><div class="number"><?= number_format($summary['sem_inconsistencia'], 0, ',', '.') ?></div></div>
        <div class="card"><div class="label">Pendentes / revisar</div><div class="number"><?= number_format($summary['pendentes'], 0, ',', '.') ?></div></div>
    </div>

    <div class="panel">
        <form method="get" class="filters">
            <div>
                <label for="placa">Placa</label>
                <input id="placa" name="placa" value="<?= h($placa) ?>" placeholder="Ex.: TES6C34" maxlength="20">
            </div>
            <div>
                <label for="status">Status da auditoria</label>
                <select id="status" name="status">
                    <option value="">Todos os status</option>
                    <?php foreach ($statuses as $key => $label): ?>
                        <option value="<?= h($key) ?>" <?= $status === $key ? 'selected' : '' ?>><?= h($label) ?></option>
                    <?php endforeach; ?>
                    <option value="NAO_ANALISADA" <?= $status === 'NAO_ANALISADA' ? 'selected' : '' ?>>Não analisada</option>
                </select>
            </div>
            <div>
                <label for="data_inicio">Data inicial</label>
                <input type="date" id="data_inicio" name="data_inicio" value="<?= h($dataInicio) ?>">
            </div>
            <div>
                <label for="data_fim">Data final</label>
                <input type="date" id="data_fim" name="data_fim" value="<?= h($dataFim) ?>">
            </div>
            <button class="btn btn-primary" type="submit">Filtrar</button>
            <a class="btn btn-light" href="<?= h(strtok($_SERVER['REQUEST_URI'] ?? '/', '?')) ?>">Limpar</a>
        </form>
    </div>

    <div class="panel">
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Vistoria</th>
                    <th>Data</th>
                    <th>Veículo</th>
                    <th>Unidade</th>
                    <th>Status IA</th>
                    <th>Motivo / erro</th>
                    <th>Analisado em</th>
                    <th>Links</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!$rows && $dbError === null): ?>
                    <tr><td colspan="8" class="empty">Nenhuma vistoria encontrada para os filtros selecionados.</td></tr>
                <?php endif; ?>
                <?php foreach ($rows as $row): ?>
                    <?php
                    $auditStatus = $row['auditoria_status'] ?? null;
                    $label = statusLabel($auditStatus, $statuses);
                    $class = statusClass($auditStatus);
                    $reason = $row['motivo'] ?: ($row['erro'] ?: '—');
                    $details = detailsSummary($row['detalhes'] ?? null);
                    $detailItems = $details['detalhes'] ?? [];
                    ?>
                    <tr>
                        <td>
                            <strong>#<?= h($row['idtbvistoria']) ?></strong><br>
                            <span class="muted">Tipo: <?= h($row['tipo'] ?? '—') ?></span>
                        </td>
                        <td><?= h($row['datavistoria']) ?></td>
                        <td>
                            <strong><?= h($row['placa']) ?></strong><br>
                            <?= h(trim(($row['marca'] ?? '') . ' ' . ($row['modelo'] ?? ''))) ?: 'Modelo não cadastrado' ?>
                        </td>
                        <td><?= h($row['unidade'] ?? '—') ?></td>
                        <td>
                            <span class="badge badge-<?= h($class) ?>"><?= h($label) ?></span><br>
                            <?php if ($row['verificado'] !== null): ?>
                                <span class="muted">Verificado: <?= (int)$row['verificado'] === 1 ? 'Sim' : 'Não' ?></span>
                            <?php endif; ?>
                            <?php if ($row['tentativas'] !== null): ?>
                                <br><span class="muted">Tentativas: <?= h($row['tentativas']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="motivo">
                            <?= nl2br(h($reason)) ?>
                            <?php if ($detailItems): ?>
                                <details style="margin-top:7px">
                                    <summary>Detalhes da análise</summary>
                                    <ul>
                                    <?php foreach ($detailItems as $item): ?>
                                        <li>
                                            <strong><?= h($item['arquivo'] ?? $item['vista'] ?? 'Foto') ?>:</strong>
                                            <?= h($item['motivo'] ?? $item['observacao'] ?? $item['categoria'] ?? 'Sem descrição') ?>
                                        </li>
                                    <?php endforeach; ?>
                                    </ul>
                                </details>
                            <?php endif; ?>
                        </td>
                        <td><?= h($row['analisado_em'] ?? '—') ?></td>
                        <td>
                            <div class="actions">
                                <form class="inline" method="post" action="https://ffa.painel-telecom.com:4242/frotas/checklist/fpdf/relatoriocomfotos.php" target="_blank" rel="noopener">
                                    <input type="hidden" name="idtbvistoria" value="<?= h($row['idtbvistoria']) ?>">
                                    <input type="hidden" name="datavistoria" value="<?= h($row['datavistoria']) ?>">
                                    <input type="hidden" name="placa" value="<?= h($row['placa']) ?>">
                                    <button class="btn btn-success" type="submit">Visualizar PDF</button>
                                </form>
                                <form class="inline" method="post" action="https://ffa.painel-telecom.com:4242/frotas/checklist/verfotos.php" target="_blank" rel="noopener">
                                    <input type="hidden" name="idtbvistoria" value="<?= h($row['idtbvistoria']) ?>">
                                    <input type="hidden" name="datavistoria" value="<?= h($row['datavistoria']) ?>">
                                    <input type="hidden" name="placa" value="<?= h($row['placa']) ?>">
                                    <button class="btn btn-primary" type="submit">Ver fotos</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <span class="muted">Página <?= h($page) ?> de <?= h($totalPages) ?> — <?= number_format($total, 0, ',', '.') ?> registros — até <?= $perPage ?> por página</span>
            <div class="links">
                <?php if ($page > 1): ?>
                    <a class="btn btn-light" href="?<?= h(http_build_query(array_merge($queryBase, ['page' => $page - 1]))) ?>">Anterior</a>
                <?php endif; ?>
                <?php if ($page < $totalPages): ?>
                    <a class="btn btn-light" href="?<?= h(http_build_query(array_merge($queryBase, ['page' => $page + 1]))) ?>">Próxima</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <p class="muted">Modelo de IA: <?= h(implode(', ', array_unique(array_filter(array_column($rows, 'modelo_ia')))) ?: '—') ?></p>
</div>
</body>
</html>
