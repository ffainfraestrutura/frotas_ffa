<?php
date_default_timezone_set('America/Sao_Paulo');
include_once './controleacesso.php';

$ultseg = date('Y-m-d', strtotime('-1 Monday'));

if (file_exists('../conecta05.php')) {
    include '../conecta05.php';
} elseif (file_exists('../../conecta05.php')) {
    include '../../conecta05.php';
} else {
    die('ERRO: Arquivo conecta05.php não encontrado');
}

if (!isset($conexao05) || !$conexao05) {
    die('ERRO CRÍTICO: Conexão com banco de dados não foi estabelecida.');
}

if (!mysqli_ping($conexao05)) {
    die('ERRO CRÍTICO: Conexão com banco de dados perdida. Erro: ' . mysqli_connect_error());
}

include_once '../func/log.php';
include_once '../func/consultacartao.php';

header('Content-type: text/html; charset=utf-8');
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
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="http://plentz.github.io/jquery-maskmoney/javascripts/jquery.maskMoney.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.13.4/sorting/date-eu.js"></script>
    <script type="text/javascript"
        src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>

    <style type="../text/css">
        table {
            font-size: 12px;
        }
    </style>
</head>

<body class="sb-nav-fixed vsc-initialized">
    <?php include '../menu.php'; ?>

    <div id="layoutSidenav_content">
        <main style="width: 100%;">
            <div class="container-fluid px-4" style="width: 100%;">
                <h1 class="h1 pt-2 pb-2"> Retirada de saldo </h1>
                <h2>Saldo Retirado:
                    <?php
                    $sql = "SELECT saldorem FROM bdfrota.tbsupervisor WHERE matricula = '$matricula'";
                    $resultado = mysqli_query($conexao05, $sql) or die(mysqli_error($conexao05));
                    $row = mysqli_fetch_array($resultado);
                    $saldosup = $row[0];
                    print "R$ $saldosup";
                    ?>
                </h2>
                <div class="d-flex justify-content-between">
                    <div class="card mb-4  col-12">
                        <div class="card-header">
                            <i class="fas fa-table"></i>
                            Retirada de Saldo
                        </div>

                        <div class="card-body col-12">
                            <table table id="tabela1" class="table">
                                <thead>
                                    <tr>
                                        <th>Matrícula</th>
                                        <th>Nome</th>
                                        <th>Placa</th>
                                        <th>Orç. Semanal</th>
                                        <th>Saldo Atual</th>
                                        <th>Retirar (R$)</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    $sql = "SELECT idtbsupervisor FROM bdfrota.tbsupervisor WHERE matricula = '$matricula'";
                                    $resultado = mysqli_query($conexao05, $sql) or die(mysqli_error($conexao05));
                                    $row = mysqli_fetch_array($resultado);
                                    $idtbsupervisor = $row[0];

                                    $sql = "SELECT a.matricula, b.nome, b.codempresa, b.status
                                            FROM bdfrota.tbusuario a
                                            JOIN bdaniel.tbfuncionario b ON a.matricula = b.matricula
                                            WHERE a.idtbsupervisor = '$idtbsupervisor' AND a.perfil = 0 AND b.status <> 'Demitido'";
                                    $resultado = mysqli_query($conexao05, $sql) or die(mysqli_error($conexao05));

                                    while ($row = mysqli_fetch_array($resultado)) {
                                        $matriculatec = $row[0];
                                        $nometec = $row[1];
                                        $codemptec = $row[2];

                                        $sql2 = "SELECT totalextra, idtbsaldo, saldo, orcsemanal
                                                 FROM bdfrota.tbsaldo
                                                 WHERE matricula = '$matriculatec'
                                                 ORDER BY data DESC";
                                        $resultado2 = mysqli_query($conexao05, $sql2) or die(mysqli_error($conexao05));
                                        $row2 = mysqli_fetch_array($resultado2);
                                        $idtbsaldo = $row2[1];
                                        $orcsemanal = round($row2[3], 2);
                                        $orcsemanal = str_replace('.', ',', $orcsemanal);

                                        $sql2 = "SELECT placa FROM bdfrota.tbveiculo WHERE matcond = '$matriculatec'";
                                        $resultado2 = mysqli_query($conexao05, $sql2) or die(mysqli_error($conexao05));
                                        $row2 = mysqli_fetch_array($resultado2);
                                        $placa = $row2[0];

                                        if ($placa <> '') {
                                            $saldocartaov = consultacartao($codemptec, $placa);

                                            if ($saldocartaov == 'Sem cartão vinculado') {
                                                $d = 'disabled';
                                                $saldocartao = 0;
                                            } else {
                                                $d = '';
                                                $saldocartao = str_replace('R$ ', '', $saldocartaov);
                                                $saldocartao = str_replace(',', '.', $saldocartao);
                                            }

                                            print "
                                        <tr>
                                            <td>$matriculatec</td>
                                            <td>$nometec</td>
                                            <td>$placa</td>
                                            <td>R$ $orcsemanal</td>
                                            <td>$saldocartaov</td>
                                            <td>
                                                <div class='h-25'>
                                                <form action='./control/retiradadesaldosup.php' method='post'>
                                                <div class=''>
                                                    <input type='hidden' name='matriculatec' value='$matriculatec'>
                                                    <input type='hidden' name='saldoatual' value='$saldocartao'>
                                                    <input type='hidden' name='saldosup' value='$saldosup'>
                                                    <input type='hidden' name='idtbsaldo' value='$idtbsaldo'>
                                                    <input type='hidden' name='codemptec' value='$codemptec'>
                                                    <input type='hidden' name='placa' value='$placa'>
                                                    <input type='hidden' name='idtbsupervisor' value='$idtbsupervisor'>
                                                    <input type='hidden' name='numeroCartao' value=''>

                                                    <div>Tipo de Ação:<br>
                                                        <input type='radio' checked name='tipoacao' value='0' class='ms-2'>
                                                        <label for='remov'>Remover Saldo</label><br>
                                                    </div>

                                                    <div class='d-flex mt-1'>
                                                        <input type='text' class='form-control numerico' name='valor' $d required>
                                                        <button class='btn btn-outline-success' type='submit' id='button-addon2' $d><span class='material-symbols-outlined'>done</span></button>
                                                    </div>
                                                </div>
                                                </form>
                                                </div>
                                            </td>
                                        </tr>";
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
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

    <script language="javascript">
        function formatarMoeda(i) {
            var v = i.value.replace(/\D/g, '');
            v = (v / 100).toFixed(2) + '';
            v = v.replace('.', ',');
            i.value = v;
        }

        $(function () {
            $('.numerico').maskMoney({
                thousands: '.',
                decimal: ',',
                affixesStay: false
            });
        });

        $(document).ready(function () {
            $('#tabela1').DataTable({
                order: [[1, 'asc']],
            });
        });
        // testado e ok

    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../src/js/scripts.js"></script>
</body>

</html>