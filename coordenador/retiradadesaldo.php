<?php
date_default_timezone_set('America/Sao_Paulo');
include_once "./controleacesso.php";

$ultseg = date('Y-m-d', strtotime('-1 Monday'));

// Tentar carregar de ambos os caminhos possíveis
if (file_exists("../conecta05.php")) {
    include "../conecta05.php";
} elseif (file_exists("../../conecta05.php")) {
    include "../../conecta05.php";
} else {
    die("ERRO: Arquivo conecta05.php não encontrado");
}

// Verificar conexão
if (!isset($conexao05) || !$conexao05) {
    die("ERRO CRÍTICO: Conexão com banco de dados não foi estabelecida.");
}

if (!mysqli_ping($conexao05)) {
    die("ERRO CRÍTICO: Conexão com banco de dados perdida. Erro: " . mysqli_connect_error());
}

include_once "../func/log.php";
include_once "../func/consultacartao.php";

header("Content-type: text/html; charset=utf-8");
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

    <!--link Material Symbols - simbolos do input-->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!-- vendor jquery js -->
    <!--<script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="http://plentz.github.io/jquery-maskmoney/javascripts/jquery.maskMoney.min.js"></script>


    <!--data tables - css e js-->
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
    <!--nav-->
    <?php include "../menu.php"; ?>

    <div id="layoutSidenav_content">

        <main style="width: 100%;">

            <div class="container-fluid px-4" style="width: 100%;">

                <h1 class="h1 pt-2 pb-2"> Retirada de saldo </h1>

                <h2>Saldo Retirado:

                    <?php
                    $sql = "SELECT saldo FROM bdfrota.tbcoord where matricula = '$matricula'";
                    $resultado = mysqli_query($conexao05, $sql) or die(mysqli_error($conexao05));
                    $row = mysqli_fetch_array($resultado);
                    $saldosup = $row[0];
                    print "R$ $saldosup";
                    ?>
                </h2>
                <div class="d-flex justify-content-between">
                    <!--div tabela retirar-->
                    <div class="card mb-4  col-12">
                        <div class="card-header">
                            <i class="fas fa-table"></i>
                            Retirada de Saldo
                        </div>

                        <div class="card-body col-12">
                            <!--tabela para entrada de dados-->
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
                                    $sql = "SELECT 
                                                a.matricula,
                                                b.nome,
                                                tf.codempresa,
                                                b.status,
                                                c.idtbcoordenador,
                                                ts.totalextra,
                                                ts.idtbsaldo,
                                                ts.saldo,
                                                ts.orcsemanal,
                                                v.placa
                                            FROM
                                                bdfrota.tbusuario a
                                                    JOIN
                                                bdaniel.tbfuncionario tf ON tf.matricula = a.matricula
                                                    JOIN
                                                bdaniel.tbfuncionario b ON a.matricula = b.matricula
                                                    JOIN
                                                bdfrota.tbsupervisor s ON s.idtbsupervisor = a.idtbsupervisor
                                                    JOIN
                                                bdfrota.tbcoord c ON s.idtbcoordenador = c.idtbcoordenador
                                                    JOIN
                                                (SELECT 
                                                    t1.*
                                                FROM
                                                    bdfrota.tbsaldo t1
                                                INNER JOIN (SELECT 
                                                    matricula, MAX(idtbsaldo) AS max_id
                                                FROM
                                                    bdfrota.tbsaldo
                                                GROUP BY matricula) t2 ON t1.matricula = t2.matricula
                                                    AND t1.idtbsaldo = t2.max_id) ts ON a.matricula = ts.matricula
                                                    JOIN
                                                bdfrota.tbveiculo v ON v.matcond = a.matricula
                                            WHERE
                                                c.matricula = '$matricula' AND a.perfil = 0
                                                    AND b.status <> 'Demitido';";
                                    $resultado = mysqli_query($conexao05, $sql) or die(mysqli_error($conexao05));
                                    while ($row = mysqli_fetch_array($resultado)) {
                                        $matriculatec = $row[0];
                                        $nometec = $row[1];
                                        $codemptec = $row[2];
                                        $statustec = $row[3];
                                        $idtbcoordenador = $row[4];
                                        $totalextra = $row[5];
                                        $idtbsaldo = $row[6];
                                        $orcsemanal = $row[8];
                                        $placa = $row[9];

                                        if ($placa <> '') {

                                            $saldocartaov = consultacartao($codemptec, $placa);

                                            if ($saldocartaov == 'Sem cartão vinculado') {
                                                $d = 'disabled';
                                                $saldocartao = 0;

                                            } else {
                                                $d = '';
                                                $saldocartao = str_replace("R$ ", "", $saldocartaov);
                                                $saldocartao = str_replace(",", ".", $saldocartao);
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
                                                <form action='./control/retiradadesaldo.php' method='post'>
                                                <div class=''>
                                                    <input type='hidden' name='matriculatec' value='$matriculatec'> 
                                                    <input type='hidden' name='saldoatual' value='$saldocartao'>
                                                    <input type='hidden' name='saldosup' value='$saldosup'>
                                                    <input type='hidden' name='idtbsaldo' value='$idtbsaldo'>
                                                    <input type='hidden' name='codemptec' value='$codemptec'>
                                                    <input type='hidden' name='placa' value='$placa'>
                                                    <input type='hidden' name='idtbcoordenador' value='$idtbcoordenador'>
                                                    <input type='hidden' name='numeroCartao' value='$numeroCartao'>
                                                    <input type='hidden' name='matriculacoord' value='$matricula'>

                                                    <div>Tipo de Ação:<br>
                                                        <input type='radio' checked name='tipoacao' value='0' class='ms-2'>
                                                        <label for='remov'>Remover Saldo</label><br>
                                                    </div>
                                                    
                                                    <div class='d-flex mt-1'>
                                                        <input type='text' class='form-control numerico' placeholder='' aria-label='' aria-describedby='button-addon2' name='valor'  $d required>
                                                        
                                                        <button class='btn  btn-outline-success' type='submit' id='button-addon2' $d><span class='material-symbols-outlined'>done</span></button>
                                                        
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

                            <div></div>
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
        //função para formatar valor do input - moeda
        function formatarMoeda(i) {
            var v = i.value.replace(/\D/g, '');
            v = (v / 100).toFixed(2) + '';
            v = v.replace(".", ",");
            i.value = v;
        }

        $(function () {
            $(".numerico").maskMoney({
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
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="../src/js/scripts.js"></script>


</body>

</html>