<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$filial = $_SESSION['filial'];
if ($perfil == null) {
    echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil <> '3') {
    echo "<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['filial'] = $filial;

$data = date('Y-m-d');
$useg = date('Y-m-d', strtotime('last monday'));

include "../conecta.php";
include_once "./func/log.php";
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
    <link rel="icon" type="image/png" href="src/images/favicon.png" />
    <title>Portal FFA</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="../src/css/styles.css" rel="stylesheet" />

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

<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

    <!--nav-->
    <?php include "../menu.php"; ?>

    <div id="layoutSidenav_content">

        <main style="width: 100%;">

            <div class="container-fluid px-4" style="width: 100%;">

                <h1 class="h1 pt-2 pb-2"> Remanejamento </h1>



                <!--div tabela-->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table"></i>
                        Saldo Coordenador: <?php $sql = "SELECT valor FROM bdfrota.tbgerente where matricula = '$matricula'";
                        $resultado = mysql_query($sql) or die(mysql_error());
                        $row = mysql_fetch_array($resultado);
                        $saldocoord = $row[0];
                        print "R$ $saldocoord"; ?>
                    </div>

                    <div class="card-body">
                        <!--tabela para entrada de dados-->
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Matrícula</th>
                                    <th>Nome</th>
                                    <th>Saldo</th>
                                    <th>Fd. Frota</th>
                                    <th>Total Orçamento</th>
                                    <th>Retirar</th>
                                    <th>Zerar</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $sql = "SELECT idtbgerente FROM bdfrota.tbgerente where matricula = '$matricula'";
                                $resultado = mysql_query($sql) or die(mysql_error());
                                $row = mysql_fetch_array($resultado);
                                $idtbcoord = $row[0];

                                $sql2 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$matricula' AND data_e_hora BETWEEN  '$useg 00:00:00' AND '$data 23:59:59' AND tipo=1 AND perfil_autor=4; ";
                                $resultado2 = mysql_query($sql2) or die(mysql_error());
                                $row2 = mysql_fetch_array($resultado2);
                                $ffrota = $row2[0];
                                $ffrota = str_replace(".", ",", $ffrota);

                                $sql = "SELECT 
                                            c.matricula, b.nome, c.valor, c.totorccomp
                                        FROM
                                            bdfrota.tbgerente g
                                                JOIN
                                            bdfrota.tbcoord c ON g.idtbgerente = c.idtbgerente
                                                JOIN
                                            bdaniel.tbfuncionario b ON c.matricula = b.matricula
                                        WHERE
                                            g.idtbgerente = '$idtbcoord'
                                            AND b.status != 'demitido'";

                                $resultado = mysql_query($sql) or die(mysql_error());
                                while ($row = mysql_fetch_array($resultado)) {
                                    $matriculasup = $row[0];
                                    $nomesup = $row[1];
                                    $saldosup = round($row[2], 2);
                                    $totalsup = round($row[3], 2);


                                    print "                        
                                            <tr>
                                                <td style='white-space: nowrap;'>$matriculasup</td>
                                                <td style='white-space: nowrap;'>$nomesup</td>            
                                                <td style='white-space: nowrap;'>R$ $saldosup</td>
                                                <td style='white-space: nowrap;'>R$ $ffrota</td>
                                                <td style='white-space: nowrap;'>R$ $totalsup</td>
                                                <td>
                                                    <form action='./control/remanejarorcamento.php' method='post'>
                                                        <div class='input-group mb-3'>             
                                                            <input type='text' class='form-control' placeholder='' aria-label='' aria-describedby='button-addon2' name='valor' onkeyup='formatarMoeda(this);'>
                                                            <div class='input-group-append'>
                                                                <button class='btn  btn-outline-success' type='submit' id='button-addon2'><span class='material-symbols-outlined'>done</span></button>
                                                            </div>
                                                        </div>  

                                                        <input type='hidden' name='matriculasup' value='$matriculasup'>
                                                        <input class='tipocota' type='hidden' name='tipocota' value ='emergencial'>
                                                    </form>
                                                </td>

                                                <td style='white-space: nowrap;' class='d-flex'> 
                                                    <form action='./control/zerarcoord.php' method='post'>
                                                        <button class='btn btn-outline-danger'><span class='material-symbols-outlined' type='submit'>close</span></button>
                                                        <input type='hidden' name='matriculasup' value='$matriculasup'>
                                                        <input class='tipocota' type='hidden' name='tipocota' value ='emergencial'>
                                                    </form>
                                                </td>
                                            </tr>";
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
    <script src="../src/js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="../assets/demo/chart-area-demo.js"></script>
    <script src="../assets/demo/chart-bar-demo.js"></script>
    <script src="../src/js/simple-datatable.js" crossorigin="anonymous"></script>
    <script src="../src/js/datatables-simple-demo.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>

    <script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>

</body>

</html>