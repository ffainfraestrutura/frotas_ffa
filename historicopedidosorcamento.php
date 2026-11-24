<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

$nome = isset($_SESSION['nome']) ? $_SESSION['nome'] : '';
$matricula = isset($_SESSION['matricula']) ? $_SESSION['matricula'] : '';
$perfil = isset($_SESSION['perfil']) ? (string) $_SESSION['perfil'] : '';

if ($perfil === '') {
    echo "<script>alert('Você deve logar para ter acesso');window.location='index.php'</script>";
    exit;
}
if (!in_array($perfil, array('3', '4', '10'), true)) {
    echo "<script>alert('Você não tem permissão');window.location='telainicio.php'</script>";
    exit;
}

include 'conecta.php';
include_once './func/log.php';
header('Content-type: text/html; charset=utf-8');

function historicoEsc($valor)
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function historicoAdicionarPedidos(&$pedidos, $sql, $origem, $fluxo)
{
    $resultado = mysql_query($sql) or die(mysql_error());
    while ($row = mysql_fetch_assoc($resultado)) {
        $row['origem'] = $origem;
        $row['fluxo'] = $fluxo;
        $pedidos[] = $row;
    }
}

function historicoStatus($flag)
{
    $flag = (int) $flag;
    if ($flag === 2) {
        return array('Aprovado', 'success', 'check-circle');
    }
    if ($flag === 1) {
        return array('Negado', 'danger', 'times-circle');
    }
    return array('Pendente', 'warning', 'clock');
}

$matriculaSql = mysql_real_escape_string($matricula);
$pedidos = array();

$camposGerente = "SELECT p.idtbpedidosgerente AS id_pedido, p.matricula, p.valor, p.justificativa, p.data, p.flag,
                          COALESCE(f.nome, p.matricula) AS solicitante
                     FROM bdfrota.tbpedidosgerente p
                LEFT JOIN bdaniel.tbfuncionario f ON f.matricula = p.matricula";
$camposDiretor = "SELECT p.idtbpedidosdiretor AS id_pedido, p.matricula, p.valor, p.justificativa, p.data, p.flag,
                         COALESCE(f.nome, p.matricula) AS solicitante
                    FROM bdfrota.tbpedidosdiretor p
               LEFT JOIN bdaniel.tbfuncionario f ON f.matricula = p.matricula";

if ($perfil === '3') {
    historicoAdicionarPedidos($pedidos, $camposGerente . " WHERE p.matricula = '$matriculaSql'", 'Pedido à Frota', 'Enviado');
    historicoAdicionarPedidos($pedidos, $camposDiretor . " WHERE p.matricula = '$matriculaSql'", 'Pedido ao Diretor', 'Enviado');
} elseif ($perfil === '4') {
    historicoAdicionarPedidos($pedidos, $camposGerente, 'Pedido de Gerente/Diretor', 'Recebido');
} else {
    $sqlDiretor = $camposDiretor . "
                INNER JOIN bdfrota.tbgerente g ON g.matricula = p.matricula
                INNER JOIN bdfrota.tbdiretor d ON d.id = g.idtbdiretor
                     WHERE d.matricula = '$matriculaSql'";
    historicoAdicionarPedidos($pedidos, $sqlDiretor, 'Pedido de Gerente', 'Recebido');
    historicoAdicionarPedidos($pedidos, $camposGerente . " WHERE p.matricula = '$matriculaSql'", 'Pedido à Frota', 'Enviado');
}

usort($pedidos, function ($a, $b) {
    return strcmp((string) $b['data'], (string) $a['data']);
});

$filtro = isset($_GET['status']) ? (string) $_GET['status'] : 'todos';
if (!in_array($filtro, array('todos', '0', '1', '2'), true)) {
    $filtro = 'todos';
}
$contadores = array(0 => 0, 1 => 0, 2 => 0);
foreach ($pedidos as $pedido) {
    $flag = (int) $pedido['flag'];
    if (isset($contadores[$flag])) {
        $contadores[$flag]++;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="icon" type="image/png" href="src/images/favicon.png" />
    <title>Histórico de Pedidos de Orçamento</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="src/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed sb-sidenav-toggled">
    <?php include './menu.php'; ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4 mb-3">
                    <div>
                        <h1 class="mb-1">Histórico de Pedidos de Orçamento</h1>
                        <p class="text-muted mb-0">Acompanhe os pedidos recebidos e enviados, incluindo decisões e pendências.</p>
                    </div>
                    <form method="get" class="d-flex align-items-end gap-2">
                        <div>
                            <label for="status" class="form-label mb-1">Filtrar por status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="todos" <?= $filtro === 'todos' ? 'selected' : '' ?>>Todos</option>
                                <option value="0" <?= $filtro === '0' ? 'selected' : '' ?>>Pendentes</option>
                                <option value="2" <?= $filtro === '2' ? 'selected' : '' ?>>Aprovados</option>
                                <option value="1" <?= $filtro === '1' ? 'selected' : '' ?>>Negados</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filtrar</button>
                    </form>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4"><div class="card border-warning h-100"><div class="card-body"><div class="text-muted">Pendentes</div><div class="fs-3 fw-bold text-warning"><?= $contadores[0] ?></div></div></div></div>
                    <div class="col-md-4"><div class="card border-success h-100"><div class="card-body"><div class="text-muted">Aprovados</div><div class="fs-3 fw-bold text-success"><?= $contadores[2] ?></div></div></div></div>
                    <div class="col-md-4"><div class="card border-danger h-100"><div class="card-body"><div class="text-muted">Negados</div><div class="fs-3 fw-bold text-danger"><?= $contadores[1] ?></div></div></div></div>
                </div>

                <div class="card mb-4 shadow-sm">
                    <div class="card-header"><i class="fas fa-history me-1"></i> Pedidos de <?= historicoEsc($nome) ?></div>
                    <div class="card-body table-responsive">
                        <table id="datatablesSimple" class="table table-striped table-hover align-middle" style="font-size: 12px;">
                            <thead><tr><th>Pedido</th><th>Fluxo</th><th>Solicitante</th><th>Data e hora</th><th>Justificativa</th><th>Valor</th><th>Status</th></tr></thead>
                            <tbody>
                            <?php foreach ($pedidos as $pedido): ?>
                                <?php if ($filtro !== 'todos' && (string) ((int) $pedido['flag']) !== $filtro) continue; ?>
                                <?php $status = historicoStatus($pedido['flag']); ?>
                                <tr>
                                    <td><strong>#<?= historicoEsc($pedido['id_pedido']) ?></strong><br><span class="text-muted"><?= historicoEsc($pedido['origem']) ?></span></td>
                                    <td><?= historicoEsc($pedido['fluxo']) ?></td>
                                    <td><?= historicoEsc($pedido['solicitante']) ?><br><span class="text-muted"><?= historicoEsc($pedido['matricula']) ?></span></td>
                                    <td><?= historicoEsc(date('d/m/Y H:i', strtotime($pedido['data']))) ?></td>
                                    <td style="min-width: 220px; white-space: normal;"><?= historicoEsc($pedido['justificativa']) ?></td>
                                    <td class="text-nowrap fw-bold">R$ <?= number_format((float) $pedido['valor'], 2, ',', '.') ?></td>
                                    <td><span class="badge bg-<?= $status[1] ?> text-<?= $status[1] === 'warning' ? 'dark' : 'white' ?>"><i class="fas fa-<?= $status[2] ?> me-1"></i><?= $status[0] ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
        <footer class="py-4 bg-light mt-auto"><div class="container-fluid px-4"><div class="small text-muted">Copyright &copy; FFA Infraestrutura</div></div></footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="src/js/scripts.js"></script>
    <script src="src/js/simple-datatable.js"></script>
    <script src="src/js/datatables-simple-demo.js"></script>
</body>
</html>
