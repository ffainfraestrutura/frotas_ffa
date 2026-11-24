<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$filial = $_SESSION['filial'];
/*if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil <> '1'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}*/
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['filial'] = $filial;
$matriculasuper = $_POST['coordenador'];

include"conecta.php";
include_once"./func/log.php";
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
        <link rel="icon" type="image/png" href="src/images/favicon.png"/>
        <title>Portal FFA</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="src/css/styles.css" rel="stylesheet" />
        <!--link Material Symbols - simbolos do input-->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

        <!-- vendor jquery js -->
        <!--<script type='text/javascript' src='src/vendor/jquery/jquery-3.2.1.min.js'></script>
        <script type='text/javascript' src='src/vendor/jquery/jquery.min.js'></script>-->

        <!--data tables - css e js-->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>  
        
    </head>


    <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

    	<!--nav-->
    	<?php include"menu.php"; ?>

<div id="layoutSidenav_content">

<main style="width: 100%;">

<div class="container-fluid px-4" style="width: 100%;">

    	
    	<h1 class="h1 pt-2 pb-2"> Orçamento Supervisores </h1>
  	<?php

$sql1 = "SELECT orcrecebido, valor FROM bdfrota.tbcoord where matricula = '$matricula'";
                    $resultado1 = mysql_query($sql1) or die (mysql_error());
                    $row1 = mysql_fetch_array($resultado1);
                    $orccomp = $row1[0];
                    $valorc = $row1[1];



    ?>
                    <div>
                        <p class="fs-6">Valor Orçamento R$: <?php echo"$orccomp"; ?></p>
                        <p class="fs-6">Saldo Atual R$: <?php echo"$valorc"; ?></p>
                    </div>
            <!--div tabela - entrar dados do sistema-->
    		<div id="containertab">
                <table id="datatablesSimple" class="display" style="font-size: 12px;">
                    <thead>
                        <tr>
                            <th style="white-space: nowrap;">Matrícula</th>
                            <th style="white-space: nowrap;">Nome</th>  
                            <th>Saldo</th>   
                            <th>Total Orçamento</th>       
                            <th style="white-space: nowrap;">Enviar</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php
                      
$sql1 = "SELECT idtbcoordenador FROM bdfrota.tbcoord where matricula = '$matricula'";
                    $resultado1 = mysql_query($sql1) or die (mysql_error());
                    $row1 = mysql_fetch_array($resultado1);
                    $idtbcoord = $row1[0];

                  $sql = "SELECT a.matricula, b.nome, ROUND(a.valor, 2), ROUND(a.totorccomp,2) from bdfrota.tbsupervisor a, bdaniel.tbfuncionario b where a.matricula=b.matricula and a.idtbcoordenador = '$idtbcoord' and b.status != 'demitido'";
                    
                    $resultado = mysql_query($sql) or die (mysql_error());
                    while($row = mysql_fetch_array($resultado)){
                        $matriculasup = $row[0];
                        $nomesup = $row[1];
                        $saldo = $row[2];
                        $valrec = $row[3];


                            print"                        
                            <tr>
                            <td style='white-space: nowrap;'>$matriculasup</td>
                            <td style='white-space: nowrap;'>$nomesup</td>  
                            <td style='white-space: nowrap;'>R$ $saldo</td>  
                            <td style='white-space: nowrap;'>R$ $valrec</td>  
                            <td>
                            <form action='./control/coordenadordist.php' method='post'>
                            <div class='input-group mb-3'>             
  <input type='text' class='ivalor form-control' placeholder='' aria-label='' aria-describedby='button-addon2' name='valor' onkeyup='formatarMoeda(this);'>
  <div class='input-group-append'>
    <button class='btn  btn-outline-success' type='submit' id='button-addon2'><span class='material-symbols-outlined'>done</span></button>
  </div>
</div>

                            <input type='hidden' name='matriculasup' value='$matriculasup'>
                            <input class='tipocota' type='hidden' name='tipocota' value ='orcdist'>
</form>
                            </td>
                        </tr>";


                        }
                
                    ?>

                    </tbody>
                </table>
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
            
        </div>

        <script>
            //função data table para criar barra de rolagem
            $(document).ready(function () {
                $('#datatablesSimple').DataTable({
                scrollX: true,
                });
            });

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