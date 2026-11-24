<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$filial = $_SESSION['filial'];

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['filial'] = $filial;

include "conecta.php";
include_once "./func/log.php";
include_once "./func/consultacartao.php";
header("Content-type: text/html; charset=utf-8");

$matsup = $_POST['mattec'];

$sql = "SELECT codempresa FROM bdaniel.tbfuncionario WHERE matricula='$matricula'; ";
$resultado = mysql_query($sql) or die(mysql_error());
$row = mysql_fetch_array($resultado);
$codempresa = $row[0];

/*if($codempresa == 2){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}*/

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="FFA" />
    <meta name="author" content="FFA" />
    <link rel="icon" type="image/png" href="src/images/favicon.png" />
    <title>Portal FFA</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="src/css/styles.css" rel="stylesheet" />

    <!--link Material Symbols - simbolos do input-->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!-- vendor jquery js -->
    <script type='text/javascript' src='src/vendor/jquery/jquery-3.2.1.min.js'></script>
    <script type='text/javascript' src='src/vendor/jquery/jquery.min.js'></script>

    <!--data tables - css e js-->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
</head>

<body class="sb-nav-fixed vsc-initialized">

    <!--nav-->
    <?php include "menu.php"; ?>

    <div id="layoutSidenav_content">

        <main style="width: 100%;">

            <div class="container-fluid px-4" style="width: 100%;">

                <h1 class="h1 pt-2 pb-2"> Visão Força SUP</h1>

                <div id="collapseExample4">
                    <form id="mainForm" action="./visaoforcafrotas.php" method="post">
                        <h5><b>Selecione o Supervisor:</b></h5>
                        <div class="col-md-3">
                            <label for="matricula">
                                <h5>Matrícula:</h5>
                            </label>
                            <input type="text" class="form-control mb-3" name="mattec" placeholder="Digite a matrícula">
                        </div>

                        <input type="submit" class="btn btn-success" value="Filtrar">
                    </form>
                </div>

                <!--div tabela-->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table"></i>
                        Saldo Supervisor:
                        <?php
                        $sql = "SELECT ROUND(valor,2) FROM bdfrota.tbsupervisor where matricula = '$matsup'";
                        $resultado = mysql_query($sql) or die(mysql_error());
                        $row = mysql_fetch_array($resultado);
                        $saldosup = $row[0];
                        print "R$ $saldosup";
                        ?>
                    </div>

                    <div class="card-body">
                        <!--tabela para entrada de dados-->
                        <table class="table table-bordered">
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
                                <?php
                                if ($matsup <> '') {
                                    $sql = "SELECT * FROM bdfrota.tbsupervisor where matricula = '$matsup'";

                                    $resultado = mysql_query($sql) or die(mysql_error());
                                    $row = mysql_fetch_array($resultado);
                                    $idtbsupervisor = $row[0];

                                    $sql = "
                                        SELECT 
                                            a.matricula,
                                            b.nome,
                                            b.codempresa,
                                            s.totalextra,
                                            s.idtbsaldo,
                                            v.placa
                                        FROM bdfrota.tbusuario a
                                        JOIN bdaniel.tbfuncionario b 
                                            ON a.matricula = b.matricula
                                        LEFT JOIN (
                                            SELECT s1.matricula, s1.totalextra, s1.idtbsaldo
                                            FROM bdfrota.tbsaldo s1
                                            INNER JOIN (
                                                SELECT matricula, MAX(data) as max_data
                                                FROM bdfrota.tbsaldo
                                                GROUP BY matricula
                                            ) s2 
                                                ON s1.matricula = s2.matricula 
                                                AND s1.data = s2.max_data
                                        ) s 
                                            ON s.matricula = a.matricula
                                        LEFT JOIN bdfrota.tbveiculo v 
                                            ON v.matcond = a.matricula
                                        WHERE 
                                            a.idtbsupervisor = '$idtbsupervisor'
                                            AND a.perfil = 0
                                            AND b.status <> 'demitido'
                                        ";

                                    $resultado = mysql_query($sql) or die(mysql_error());

                                    while ($row = mysql_fetch_array($resultado)) {

                                        $matriculatec = $row['matricula'];
                                        $nometec = $row['nome'];
                                        $codemptec = $row['codempresa'];
                                        $totalextra = $row['totalextra'] ?? 0;
                                        $idtbsaldo = $row['idtbsaldo'] ?? null;
                                        $placa = $row['placa'];

                                        if ($placa) {
                                            $saldocartao = consultacartao($codemptec, $placa);
                                        } else {
                                            $placa = 'Sem placa';
                                            $saldocartao = 0;
                                        }
                                        print "                        
                                                <tr>
                                                <td style='white-space: nowrap;'>$matriculatec</td>
                                                <td style='white-space: nowrap;'>$nometec</td>
                                                <td style='white-space: nowrap;'>$placa</td> 

                                                <td style='white-space: nowrap;'>R$ $saldocartao</td>
                                                <td style='white-space: nowrap;'>$totalextra</td>    
                                                
                                            </tr>";
                                    }
                                }
                                ?>
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

    <script language="javascript">
        //função para formatar valor do input - moeda
        function formatarMoeda(i) {
            var v = i.value.replace(/\D/g, '');
            v = (v / 100).toFixed(2) + '';
            v = v.replace(".", ",");
            v = v.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
            i.value = v;
        }
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="src/js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
    <script src="src/js/datatables-simple-demo.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>

    <script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>

</body>

</html>