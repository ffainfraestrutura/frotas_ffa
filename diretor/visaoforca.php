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
    <title>Portal FFA</title>
    <link rel="stylesheet" href="../src/css/styles.css" />
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>
</head>

<body class="sb-nav-fixed">
    <?php include "../menu.php"; ?>
    <div id="layoutSidenav_content">
        <main style="width: 100%;">
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
        </main>
    </div>
</body>

</html>