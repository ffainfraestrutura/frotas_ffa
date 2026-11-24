<?php
date_default_timezone_set('America/Sao_Paulo');
header("Content-Type: text/html; charset=utf-8");

include_once "./controleacesso.php";
include_once "../conecta.php";
include_once "../func/log.php";

$matricula = mysqli_real_escape_string($conn, $matricula);

// Função de saldo
function buscarSaldoCartao($unidade, $placa)
{
    $isRJ = ($unidade === "RJ");
    $url = $isRJ
        ? 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/relatorioExtratoSimplificado/search'
        : 'https://kissapi.valecard.com.br/core/services/frotaCliente/buscarBasedeCartoes';

    $headers = [
        'Content-Type: application/json',
        'Authorization: ' . ($isRJ
            ? 'Basic W09wZXJhZG9yV2ViXWFwcDE4MzA2MjQyNDc6RFhYSm5WejA='
            : 'Basic NTI0MDczQHdlYlNlcnZpY2U6MDY5NDc=')
    ];

    $body = $isRJ ? json_encode([
        "codigoCliente" => 183062,
        "codigoProduto" => 4,
        "placaVeiculo" => $placa,
        "situacaoCartao" => "T",
        "ordem" => "C"
    ]) : json_encode([
            "contratoCliente" => "524073",
            "placa" => $placa
        ]);

    $curl = curl_init($url);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => $body
    ]);

    $result = json_decode(curl_exec($curl));
    curl_close($curl);

    if (!$result)
        return 0;

    // Simplifica iteração
    $itens = $isRJ ? ($result->itens ?? []) : $result;
    foreach ($itens as $item) {
        if (($item->statusCartao ?? $item->situacao) === 'A') {
            return number_format((float) $item->saldo, 2, ',', '.');
        }
    }

    return 0;
}

// Buscar saldo gerente
list($saldoGerente) = mysqli_fetch_row(mysqli_query($conn, "
    SELECT valor FROM bdfrota.tbgerente WHERE matricula = '$matricula'
"));

// Consulta principal
$sql = "
WITH coordenador AS (
    SELECT tc.idtbcoordenador AS id, tc.matricula, tc.totcotaextra AS cotaextra, 'coordenador' AS hierarquia
    FROM bdfrota.tbcoord tc
    JOIN bdfrota.tbgerente tg ON tg.idtbgerente = tc.idtbgerente
    WHERE tg.matricula = '$matricula'
),
supervisor AS (
    SELECT ts.idtbsupervisor AS id, ts.matricula, ts.totcotaextra AS cotaextra, 'supervisor' AS hierarquia
    FROM bdfrota.tbsupervisor ts
    JOIN coordenador c ON ts.idtbcoordenador = c.id
),
ultimo_saldo AS (
    SELECT t1.* FROM bdfrota.tbsaldo t1
    JOIN (
        SELECT matricula, MAX(idtbsaldo) AS max_id
        FROM bdfrota.tbsaldo GROUP BY matricula
    ) t2 ON t1.matricula = t2.matricula AND t1.idtbsaldo = t2.max_id
),
tecnico AS (
    SELECT tt.id_usuario AS id, tt.matricula, us.totalextra AS cotaextra, 'tecnico' AS hierarquia
    FROM bdfrota.tbusuario tt
    JOIN supervisor s ON s.id = tt.idtbsupervisor
    JOIN ultimo_saldo us ON us.matricula = tt.matricula
)
SELECT tbf.id, tbf.matricula, tbf.hierarquia, tbf.cotaextra, tf.nome, tv.placa, tv.unidade
FROM (
    SELECT * FROM coordenador
    UNION ALL
    SELECT * FROM supervisor
    UNION ALL
    SELECT * FROM tecnico
) tbf
JOIN bdaniel.tbfuncionario tf ON tf.matricula = tbf.matricula
JOIN bdfrota.tbveiculo tv ON tf.matricula = tv.matcond
WHERE tf.status = 'ATIVO';
";

$result = mysqli_query($conn, $sql);

$dados = ['coordenador' => [], 'supervisor' => [], 'tecnico' => []];
while ($row = mysqli_fetch_assoc($result)) {
    $dados[$row['hierarquia']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="FFA" />
    <meta name="author" content="FFA" />
    <link rel="icon" type="image/png" href="../src/images/favicon.png" />
    <title>Portal FFA</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="../src/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
    <?php include "../menu.php"; ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="pt-2 pb-2">Visão Força</h1>
                <h6>Saldo Gerente: R$ <?= number_format($saldoGerente, 2, ',', '.') ?></h6>

                <?php foreach (['coordenador', 'supervisor', 'tecnico'] as $tipo): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i> Visão Força - <?= ucfirst($tipo) ?>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered datatable">
                                <thead>
                                    <tr>
                                        <th>Matrícula</th>
                                        <th>Nome</th>
                                        <th>Placa</th>
                                        <th>Saldo</th>
                                        <th>Total Orçamento</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dados[$tipo] as $row): ?>
                                        <?php $saldo = buscarSaldoCartao($row['unidade'], $row['placa']); ?>
                                        <tr>
                                            <td><?= $row['matricula'] ?></td>
                                            <td><?= $row['nome'] ?></td>
                                            <td><?= $row['placa'] ?></td>
                                            <td>R$ <?= $saldo ?></td>
                                            <td><?= $row['cotaextra'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
                    <!--<div>
                    <a href="#">Privacy Policy</a>
                    &middot;
                    <a href="#">Terms &amp; Conditions</a>
                </div>!-->
                </div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="../src/js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="../assets/demo/chart-area-demo.js"></script>
    <script src="../assets/demo/chart-bar-demo.js"></script>
    <script src="../src/js/simple-datatable.js" crossorigin="anonymous"></script>
    <script src="../src/js/datatables-simple-demo.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
</body>

</html>