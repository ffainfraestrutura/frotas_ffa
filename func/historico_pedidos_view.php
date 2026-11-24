<?php
if (!isset($historicoTitulo, $historicoPedidos, $historicoBaseUrl, $historicoBaseDir)) {
    exit('Dados do histórico indisponíveis.');
}

function historicoOrcamentoEsc($valor)
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function historicoOrcamentoStatus($flag)
{
    return (int) $flag === 2
        ? array('Aprovado', 'success', 'check-circle')
        : array('Negado', 'danger', 'times-circle');
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= historicoOrcamentoEsc($historicoTitulo) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet">
    <link href="<?= $historicoBaseUrl ?>src/css/styles.css" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed sb-sidenav-toggled">
<?php include $historicoBaseDir . 'menu.php'; ?>
<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4 mb-1"><?= historicoOrcamentoEsc($historicoTitulo) ?></h1>
            <p class="text-muted">Pedidos de orçamento que já foram aprovados ou negados.</p>

            <div class="card mb-4 shadow-sm">
                <div class="card-header"><i class="fas fa-history me-1"></i> Pedidos finalizados</div>
                <div class="card-body table-responsive">
                    <table id="datatablesSimple" class="table table-striped table-hover align-middle" style="font-size: 12px">
                        <thead>
                            <tr>
                                <th>Pedido</th>
                                <th>Solicitante</th>
                                <th>Data</th>
                                <th>Justificativa</th>
                                <th>Valor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($historicoPedidos as $pedido): ?>
                            <?php $status = historicoOrcamentoStatus($pedido['flag']); ?>
                            <tr>
                                <td><strong>#<?= historicoOrcamentoEsc($pedido['id_pedido']) ?></strong></td>
                                <td><?= historicoOrcamentoEsc($pedido['solicitante']) ?><br><span class="text-muted"><?= historicoOrcamentoEsc($pedido['matricula']) ?></span></td>
                                <td><?= historicoOrcamentoEsc(date('d/m/Y H:i', strtotime($pedido['data']))) ?></td>
                                <td style="min-width: 220px; white-space: normal"><?= historicoOrcamentoEsc($pedido['justificativa']) ?></td>
                                <td class="text-nowrap fw-bold">R$ <?= number_format((float) $pedido['valor'], 2, ',', '.') ?></td>
                                <td><span class="badge bg-<?= $status[1] ?>"><i class="fas fa-<?= $status[2] ?> me-1"></i><?= $status[0] ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="<?= $historicoBaseUrl ?>src/js/scripts.js"></script>
<script src="<?= $historicoBaseUrl ?>src/js/simple-datatable.js"></script>
<script src="<?= $historicoBaseUrl ?>src/js/datatables-simple-demo.js"></script>
</body>
</html>