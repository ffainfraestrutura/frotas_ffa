<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['filial'] = $filial;
include"../conecta.php";
include_once"../func/log.php";
header("Content-type: text/html; charset=utf-8");

/*$sql="SELECT ccusto FROM bdaniel.tbfuncionario WHERE matricula='$matricula'";
$resultado = mysql_query($sql) or die (mysql_error());
$row = mysql_fetch_array($resultado);
    $ccusto=$row[0];*/


?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="FFA" />
        <meta name="author" content="FFA" />
        <link rel="icon" type="image/png" href="src/images/favicon.png"/>
        <title>Portal FFA</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="src/css/styles.css" rel="stylesheet" />

        <!--link Material Symbols - simbolos do input-->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

        <!-- vendor jquery js -->
        <script type='text/javascript' src='src/vendor/jquery/jquery-3.2.1.min.js'></script>
        <script type='text/javascript' src='src/vendor/jquery/jquery.min.js'></script>

        <!--data tables - css e js-->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>        
    </head>

    <body class="sb-nav-fixed vsc-initialized">

    	<!--nav-->
    	<?php include"menu.php"; ?>

<div id="layoutSidenav_content">

<main style="width: 100%;">

<div class="container-fluid px-4" style="width: 100%;">
    	
    <h1 class="h1 pt-2 pb-2"> Listagem Usuários </h1>

  

    <!--div tabela-->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table"></i>
        </div>
        
        <div class="card-body">
        <!--tabela para entrada de dados-->
            <table id='datatablesSimple' class='table table-bordered'>
                <thead>
                    <tr>
                        <th>Matrícula</th>
                        <th>Nome</th>            
                        <th>Centro de Custo</th>
                        <th>Placa</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $sql = "SELECT a.matricula, b.nome, a.idtbsupervisor, b.ccusto FROM bdfrota.tbusuario a, bdaniel.tbfuncionario b where a.matricula = b.matricula and a.perfil in ('0', '1', '2') and b.status <> 'demitido' order by a.nome";

                    
                    $resultado = mysql_query($sql) or die (mysql_error());
                    while($row = mysql_fetch_array($resultado)){
                        $matriculatec = $row[0];
                        $nometec = utf8_encode($row[1]);
                        $idtbsup = $row[2];  
                        $ccusto = utf8_encode($row[3]);
                        if ($ccusto == '') {
                            $ccusto = 'Sem centro de custo';
                        }
                        $sql2 = "SELECT placa FROM bdfrota.tbveiculo where matcond = '$matriculatec'";
                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        $row2 = mysql_fetch_array($resultado2);
                        $placa = $row2[0];
                        if ($placa == '') {
                            $placa = 'Sem placa';
                        }
                    

                            print"                        
                            <tr>
                            <td>$matriculatec</td>
                            <td style='white-space: nowrap;'>$nometec</td>            
                            <td>$ccusto</td>
                            <td>$placa</td>
                            

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
                <!--<div>
                    <a href="#">Privacy Policy</a>
                                &middot;
                    <a href="#">Terms &amp; Conditions</a>
                </div>!-->
            </div>
        </div>
    </footer>

</div>

    <script language="javascript">   
    //função para formatar valor do input - moeda
            function formatarMoeda(i) {
                var v = i.value.replace(/\D/g,'');
                v = (v/100).toFixed(2) + '';
                v = v.replace(".", ",");
                v = v.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
                i.value = v;
            }
    </script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="src/js/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="assets/demo/chart-area-demo.js"></script>
<script src="assets/demo/chart-bar-demo.js"></script>
<script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
<script src="src/js/datatables-simple-demo.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>   
    	
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>

</body>

</html>