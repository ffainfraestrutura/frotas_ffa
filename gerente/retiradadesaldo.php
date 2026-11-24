<?php
date_default_timezone_set('America/Sao_Paulo');
include_once './controleacesso.php';
include_once '../conecta05.php';
include_once '../func/consultacartao.php';

header('Content-type: text/html; charset=utf-8');

$coordenadorSelecionado = filter_input(INPUT_GET, 'coordenador', FILTER_VALIDATE_INT);

$stmt = mysqli_prepare($conexao05, 'SELECT valor FROM bdfrota.tbgerente WHERE matricula = ?');
mysqli_stmt_bind_param($stmt, 's', $matricula);
mysqli_stmt_execute($stmt);
$gerente = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
$saldoGerente = $gerente ? (float) $gerente['valor'] : 0;

$sqlCoordenadores = "SELECT c.idtbcoordenador, c.matricula, fc.nome
                    FROM bdfrota.tbgerente g
                    INNER JOIN bdfrota.tbcoord c ON c.idtbgerente = g.idtbgerente
                    INNER JOIN bdaniel.tbfuncionario fc ON fc.matricula = c.matricula
                    WHERE g.matricula = ?
                    ORDER BY fc.nome";
$stmtCoord = mysqli_prepare($conexao05, $sqlCoordenadores);
mysqli_stmt_bind_param($stmtCoord, 's', $matricula);
mysqli_stmt_execute($stmtCoord);
$coordenadores = mysqli_stmt_get_result($stmtCoord);

$tecnicos = null;
$temDadosTabela = false;
if ($coordenadorSelecionado) {
    $sql = "SELECT u.matricula, f.nome, f.codempresa, c.matricula AS matriculacoord,
                   fc.nome AS nomecoord, s.idtbsaldo, s.orcsemanal, v.placa
            FROM bdfrota.tbgerente g
            INNER JOIN bdfrota.tbcoord c ON c.idtbgerente = g.idtbgerente
            INNER JOIN bdaniel.tbfuncionario fc ON fc.matricula = c.matricula
            INNER JOIN bdfrota.tbsupervisor sup ON sup.idtbcoordenador = c.idtbcoordenador
            INNER JOIN bdfrota.tbusuario u ON u.idtbsupervisor = sup.idtbsupervisor AND u.perfil = 0
            INNER JOIN bdaniel.tbfuncionario f ON f.matricula = u.matricula
            INNER JOIN (
                SELECT saldo.* FROM bdfrota.tbsaldo saldo
                INNER JOIN (
                    SELECT matricula, MAX(idtbsaldo) AS idtbsaldo
                    FROM bdfrota.tbsaldo GROUP BY matricula
                ) atual ON atual.idtbsaldo = saldo.idtbsaldo
            ) s ON s.matricula = u.matricula
            INNER JOIN bdfrota.tbveiculo v ON v.matcond = u.matricula
            WHERE g.matricula = ?
              AND c.idtbcoordenador = ?
              AND f.status <> 'Demitido'
            ORDER BY fc.nome, f.nome";
    $stmt = mysqli_prepare($conexao05, $sql);
    mysqli_stmt_bind_param($stmt, 'si', $matricula, $coordenadorSelecionado);
    mysqli_stmt_execute($stmt);
    $tecnicos = mysqli_stmt_get_result($stmt);
    $temDadosTabela = $tecnicos && mysqli_num_rows($tecnicos) > 0;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Portal FFA</title>
    <link rel="icon" type="image/png" href="../src/images/favicon.png">
    <link href="../src/css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-maskmoney/3.0.2/jquery.maskMoney.min.js"></script>
</head>
<body class="sb-nav-fixed vsc-initialized">
<?php include '../menu.php'; ?>
<div id="layoutSidenav_content">
    <main class="container-fluid px-4">
        <h1 class="h1 pt-2 pb-2">Retirada de saldo</h1>
        <h2>Saldo do gerente: R$ <?= number_format($saldoGerente, 2, ',', '.') ?></h2>
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-filter"></i> Filtro de coordenador</div>
            <div class="card-body">
                <form method="get" class="row g-2 align-items-end">
                    <div class="col-12 col-md-6 col-lg-4">
                        <label for="coordenador" class="form-label">Selecione o coordenador</label>
                        <select name="coordenador" id="coordenador" class="form-select" required>
                            <option value="">Selecione...</option>
                            <?php while ($coord = mysqli_fetch_assoc($coordenadores)): ?>
                                <option value="<?= (int) $coord['idtbcoordenador'] ?>" <?= ((int) $coordenadorSelecionado === (int) $coord['idtbcoordenador']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($coord['nome']) ?> - <?= htmlspecialchars($coord['matricula']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-12 col-md-auto">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-table"></i> Retirada de saldo por coordenador</div>
            <div class="card-body">
                <?php if (!$coordenadorSelecionado): ?>
                    <div class="alert alert-info">Selecione um coordenador para visualizar as opções de retirada.</div>
                <?php elseif (!$temDadosTabela): ?>
                    <div class="alert alert-warning">Nenhum técnico encontrado para o coordenador selecionado.</div>
                <?php endif; ?>
                <table id="tabela1" class="table table-bordered table-striped">
                    <thead><tr><th>Coordenador</th><th>Matrícula</th><th>Nome</th><th>Placa</th><th>Orç. semanal</th><th>Saldo atual</th><th>Retirar (R$)</th></tr></thead>
                    <tbody>
                    <?php if ($temDadosTabela): ?>
                    <?php while ($tecnico = mysqli_fetch_assoc($tecnicos)): ?>
                        <?php
                        $saldoCartaoExibicao = consultacartao($tecnico['codempresa'], $tecnico['placa']);
                        $semCartao = $saldoCartaoExibicao === 'Sem cartão vinculado';
                        $saldoCartao = $semCartao ? 0 : str_replace(',', '.', str_replace(['R$ ', '.'], '', $saldoCartaoExibicao));
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($tecnico['nomecoord']) ?><br><small><?= htmlspecialchars($tecnico['matriculacoord']) ?></small></td>
                            <td><?= htmlspecialchars($tecnico['matricula']) ?></td>
                            <td><?= htmlspecialchars($tecnico['nome']) ?></td>
                            <td><?= htmlspecialchars($tecnico['placa']) ?></td>
                            <td>R$ <?= number_format((float) $tecnico['orcsemanal'], 2, ',', '.') ?></td>
                            <td><?= htmlspecialchars($saldoCartaoExibicao) ?></td>
                            <td>
                                <form action="./control/retiradadesaldo.php" method="post" class="d-flex">
                                    <input type="hidden" name="matriculatec" value="<?= htmlspecialchars($tecnico['matricula']) ?>">
                                    <input type="hidden" name="coordenador" value="<?= (int) $coordenadorSelecionado ?>">
                                    <input type="hidden" name="saldoatual" value="<?= htmlspecialchars($saldoCartao) ?>">
                                    <input type="hidden" name="idtbsaldo" value="<?= (int) $tecnico['idtbsaldo'] ?>">
                                    <input type="hidden" name="codemptec" value="<?= htmlspecialchars($tecnico['codempresa']) ?>">
                                    <input type="hidden" name="placa" value="<?= htmlspecialchars($tecnico['placa']) ?>">
                                    <input type="text" name="valor" class="form-control numerico" inputmode="numeric" required <?= $semCartao ? 'disabled' : '' ?>>
                                    <button class="btn btn-outline-success ms-1" type="submit" title="Retirar saldo" <?= $semCartao ? 'disabled' : '' ?>><span class="material-symbols-outlined">done</span></button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <footer class="py-4 bg-light mt-auto"><div class="container-fluid px-4"><div class="text-muted small">Copyright &copy; FFA Infraestrutura</div></div></footer>
</div>
<script>
$(function () {
    $('.numerico').maskMoney({thousands: '.', decimal: ',', precision: 0, affixesStay: false});
    if ($('#tabela1 tbody tr').length > 0) {
        $('#tabela1').DataTable({
            order: [[0, 'asc'], [2, 'asc']],
            pageLength: 25,
            lengthChange: false,
            deferRender: true
        });
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../src/js/scripts.js"></script>
</body>
</html>