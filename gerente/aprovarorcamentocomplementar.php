<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$filial = $_SESSION['filial'];

if ($perfil == null) {
    echo "<script>alert('Você deve logar para ter acesso');window.location='../index.php'</script>";
    exit;
} elseif ($perfil != '3') {
    echo "<script>alert('Você não tem permissão');window.location='../index.php'</script>";
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
$sql2 = "SELECT valor, orcrecebido FROM bdfrota.tbgerente where matricula = '$matricula'";
$resultado2 = mysql_query($sql2) or die(mysql_error());
$row2 = mysql_fetch_array($resultado2);
$saldo = $row2[0];
$orccomp = $row2[1];
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <link rel="icon" type="image/png" href="../src/images/favicon.png" />
    <title>Portal FFA</title>
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
                <h1 class="mt-4">Aprovar Orçamento Complementar</h1>
                <h6>Saldo Atual: <?= $saldo ?></h6>
            </div>

            <div class="container-fluid px-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i>
                        Pedidos
                    </div>
                    <div class="card-body">
                        <table id="datatablesSimple" style="font-size: 12px;">
                            <thead>
                                <tr>
                                    <th>Matrícula</th>
                                    <th>Coordenador</th>
                                    <th>Saldo</th>
                                    <th>Orc. Complementar</th>
                                    <th>Filial</th>
                                    <th>Data e Hora</th>
                                    <th>Justificativa</th>
                                    <th>Valor Pedido</th>
                                    <th>Decisão</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT 
                                            c.matricula,
                                            c.valor AS saldo,
                                            c.orcrecebido,
                                            f.nome,
                                            f.codempresa,
                                            p.justificativa,
                                            ROUND(p.valor, 2) AS valor_pedido,
                                            p.idtbpedidoscoord,
                                            p.data
                                        FROM 
                                            bdfrota.tbcoord c
                                        INNER JOIN 
                                            bdaniel.tbfuncionario f ON c.matricula = f.matricula
                                        INNER JOIN 
                                            bdfrota.tbpedidoscoord p ON c.matricula = p.matricula
										INNER JOIN
											bdfrota.tbgerente g on c.idtbgerente = g.idtbgerente
                                        WHERE 
                                            p.flag = 0
										AND
											g.matricula = $matricula
                                        ORDER BY 
                                            f.nome ASC";

                                $resultado = mysql_query($sql) or die(mysql_error());

                                while ($row = mysql_fetch_assoc($resultado)):
                                    $matriculasup = $row['matricula'];
                                    $saldosup = number_format($row['saldo'], 2, ',', '.');
                                    $orccomp = number_format($row['orcrecebido'] ?? 0, 2, ',', '.');
                                    $nome = $row['nome'];
                                    $codempresa = $row['codempresa'];
                                    $filial = ($codempresa == 2) ? 'SP' : 'RJ';
                                    $justificativa = $row['justificativa'];
                                    $novoorccomp = number_format($row['valor_pedido'], 2, ',', '.');
                                    $id = $row['idtbpedidoscoord'];
                                    $dataehora = $row['data'];
                                    ?>


                                    <tr>
                                        <td><?= $matriculasup ?></td>
                                        <td><?= $nome ?></td>
                                        <td>R$ <?= $saldosup ?></td>
                                        <td>R$ <?= $orccomp ?></td>
                                        <td><?= $filial ?></td>
                                        <td><?= $dataehora ?></td>
                                        <td style='font-size: 9px;'><?= $justificativa ?></td>
                                        <td>R$ <?= $novoorccomp ?></td>
                                        <td style='white-space: nowrap;'>
                                            <div class='btn-group'>
                                                <form method='post' action='./control/aprovarorcamentocomplementar.php'>
                                                    <input type='hidden' name='matriculasup' value='<?= $matriculasup ?>'>
                                                    <input type='hidden' name='decisao' value='2'>
                                                    <input type='hidden' name='id' value='<?= $id ?>'>
                                                    <input type='hidden' name='valor' value='<?= $novoorccomp ?>'>
                                                    <input type='hidden' name='valor_ant' value='<?= $saldosup ?>'>
                                                    <button type='submit' class='btn btn-outline-success'>
                                                        <span class='material-symbols-outlined'>done</span>
                                                    </button>
                                                </form>
                                                <form method='post' action='./control/aprovarorcamentocomplementar.php'>
                                                    <input type='hidden' name='matriculasup' value='<?= $matriculasup ?>'>
                                                    <input type='hidden' name='decisao' value='1'>
                                                    <input type='hidden' name='id' value='<?= $id ?>'>
                                                    <button class='btn btn-outline-danger'>
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