<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$filial = $_SESSION['filial'];

// Lista de matrículas autorizadas (Diretores)
$matriculas_autorizadas = ['601010', '270907', '601004', '620006', '601008', '251748', '003842'];

if ($perfil == null) {
    echo "<script>alert('Você deve logar para ter acesso');window.location='../index.php'</script>";
    exit;
} elseif (!in_array($matricula, $matriculas_autorizadas)) {
    echo "<script>alert('Você não tem permissão para acessar esta área');window.location='../index.php'</script>";
    exit;
}

include_once "../func/log.php";
header("Content-type: text/html; charset=utf-8");

if (strftime('%A') == "domingo") {
    $segunda = date('Y-m-d', strtotime('last monday'));
    $domingo = date('Y-m-d');
} elseif (strftime('%A') == "segunda") {
    $segunda = date('Y-m-d');
    $domingo = date('Y-m-d', strtotime('next sunday'));
} else {
    $segunda = date('Y-m-d', strtotime('last monday'));
    $domingo = date('Y-m-d', strtotime('next sunday'));
}

include "../conecta.php";

// Busca o saldo do diretor
$sql2 = "SELECT valor, orcrecebido FROM bdfrota.tbdiretor WHERE matricula = '$matricula'";
$resultado2 = mysql_query($sql2) or die(mysql_error());
$row2 = mysql_fetch_array($resultado2);
$saldo_diretor = floatval($row2[0] ?? 0);
$saldo = number_format($saldo_diretor, 2, ',', '.');
$orccomp = number_format($row2[1] ?? 0, 2, ',', '.');
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <link rel="icon" type="image/png" href="../src/images/favicon.png" />
    <title>Aprovar Orçamento de Gerentes - Diretor</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="../src/css/styles.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed sb-sidenav-toggled">

    <?php include "../menu.php"; ?>

    <div id="layoutSidenav_content">
        <main>

            <div class="container-fluid px-4">
                <h1 class="mt-4">Aprovar Orçamento de Gerentes</h1>
                <h6>Saldo Atual do Diretor: R$ <?= $saldo ?></h6>
            </div>

            <div class="container-fluid px-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i>
                        Pedidos de Gerentes
                    </div>
                    <div class="card-body">
                        <table id="datatablesSimple" style="font-size: 12px;">
                            <thead>
                                <tr>
                                    <th>Matrícula</th>
                                    <th>Gerente</th>
                                    <th>Saldo Atual</th>
                                    <th>Orc. Recebido</th>
                                    <th>Filial</th>
                                    <th>Data e Hora</th>
                                    <th>Justificativa</th>
                                    <th>Valor Solicitado</th>
                                    <th>Decisão</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Busca pedidos pendentes de gerentes vinculados ao diretor logado
                                $sql = "SELECT 
                                            g.matricula,
                                            g.valor AS saldo,
                                            g.orcrecebido,
                                            f.nome,
                                            f.codempresa,
                                            p.justificativa,
                                            ROUND(p.valor, 2) AS valor_pedido,
                                            p.idtbpedidosdiretor,
                                            DATE_FORMAT(p.data, '%d/%m/%Y %H:%i:%s') AS data_formatada
                                        FROM 
                                            bdfrota.tbgerente g
                                        INNER JOIN 
                                            bdaniel.tbfuncionario f ON g.matricula = f.matricula
                                        INNER JOIN 
                                            bdfrota.tbpedidosdiretor p ON g.matricula = p.matricula
                                        INNER JOIN
                                            bdfrota.tbdiretor d ON g.idtbdiretor = d.id
                                        WHERE 
                                            p.flag = 0
                                        AND
                                            d.matricula = '$matricula'
                                        ORDER BY 
                                            p.data DESC, f.nome ASC";

                                $resultado = mysql_query($sql) or die(mysql_error());

                                while ($row = mysql_fetch_assoc($resultado)):
                                    $matriculager = $row['matricula'];
                                    $saldoger = number_format($row['saldo'], 2, ',', '.');
                                    $orcrecebido = number_format($row['orcrecebido'] ?? 0, 2, ',', '.');
                                    $nome = $row['nome'];
                                    $codempresa = $row['codempresa'];
                                    $filial = ($codempresa == 2) ? 'SP' : 'RJ';
                                    $justificativa = $row['justificativa'];
                                    $valor_pedido_num = floatval($row['valor_pedido']);
                                    $valorpedido = number_format($valor_pedido_num, 2, ',', '.');
                                    $id = $row['idtbpedidosdiretor'];
                                    $dataehora = $row['data_formatada'];
                                    
                                    // Verifica se há saldo suficiente
                                    $tem_saldo = ($valor_pedido_num <= $saldo_diretor);
                                    $cor_saldo = $tem_saldo ? 'text-success' : 'text-danger';
                                    $icone_saldo = $tem_saldo ? '✓' : '✗';
                                    ?>


                                    <tr>
                                        <td><?= $matriculager ?></td>
                                        <td><?= $nome ?></td>
                                        <td>R$ <?= $saldoger ?></td>
                                        <td>R$ <?= $orcrecebido ?></td>
                                        <td><?= $filial ?></td>
                                        <td><?= $dataehora ?></td>
                                        <td style='font-size: 9px;'><?= $justificativa ?></td>
                                        <td class='<?= $cor_saldo ?>' style='font-weight: bold;'>
                                            <?= $icone_saldo ?> R$ <?= $valorpedido ?>
                                            <?php if (!$tem_saldo): ?>
                                                <br><small>(Saldo insuficiente)</small>
                                            <?php endif; ?>
                                        </td>
                                        <td style='white-space: nowrap;'>
                                            <div class='btn-group'>
                                                <form method='post' action='./control/aprovarorcamentocomplementar.php'>
                                                    <input type='hidden' name='matriculager' value='<?= $matriculager ?>'>
                                                    <input type='hidden' name='decisao' value='2'>
                                                    <input type='hidden' name='id' value='<?= $id ?>'>
                                                    <input type='hidden' name='valor' value='<?= $valorpedido ?>'>
                                                    <input type='hidden' name='valor_ant' value='<?= $saldoger ?>'>
                                                    <button type='submit' class='btn btn-outline-success' 
                                                            title='<?= $tem_saldo ? "Aprovar pedido" : "Saldo insuficiente - não é possível aprovar" ?>'
                                                            <?= $tem_saldo ? '' : 'disabled' ?>>
                                                        <span class='material-symbols-outlined'>done</span>
                                                    </button>
                                                </form>
                                                <form method='post' action='./control/aprovarorcamentocomplementar.php'>
                                                    <input type='hidden' name='matriculager' value='<?= $matriculager ?>'>
                                                    <input type='hidden' name='decisao' value='1'>
                                                    <input type='hidden' name='id' value='<?= $id ?>'>
                                                    <input type='hidden' name='valor' value='<?= $valorpedido ?>'>
                                                    <input type='hidden' name='valor_ant' value='<?= $saldoger ?>'>
                                                    <button type='submit' class='btn btn-outline-danger' title='Negar pedido'>
                                                        <span class='material-symbols-outlined'>close</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="../src/js/scripts.js"></script>
    <script src="../src/js/simple-datatable.js" crossorigin="anonymous"></script>
    <script src="../src/js/datatables-simple-demo.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
</body>

</html>