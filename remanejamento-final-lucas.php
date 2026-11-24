<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

$ultseg = date('Y-m-d',strtotime('-1 Monday'));

$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil <> '1'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

include"conecta.php";
include_once"./func/log.php";
include_once"./func/consultacartao.php";

header("Content-type: text/html; charset=utf-8");

/*$sql="SELECT codempresa FROM bdaniel.tbfuncionario WHERE matricula='$matricula'; ";
$resultado = mysql_query($sql) or die (mysql_error());
$row = mysql_fetch_array($resultado);
$codempresa = $row[0];

if($codempresa == 2){
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
        <link rel="icon" type="image/png" href="src/images/favicon.png"/>
        <title>Portal FFA</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="src/css/styles.css" rel="stylesheet" />

        <!--link Material Symbols - simbolos do input-->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

        <!-- vendor jquery js -->
        <!--<script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>-->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script src="http://plentz.github.io/jquery-maskmoney/javascripts/jquery.maskMoney.min.js"></script>


        <!--data tables - css e js-->
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />

        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
        
        <script src="https://cdn.datatables.net/plug-ins/1.13.4/sorting/date-eu.js"></script>

        <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
        
        <style type="text/css">
            table{
                font-size: 12px;
            }

        </style>
    </head>

    <body class="sb-nav-fixed vsc-initialized">
    	<!--nav-->
    	<?php include"menu.php"; ?>

        <div id="layoutSidenav_content">

            <main style="width: 100%;">

                <div class="container-fluid px-4" style="width: 100%;">
    	
                    <h1 class="h1 pt-2 pb-2"> Remanejamento </h1>
                    
                    <h2>Saldo Supervisor:    
                            
                        <?php 
                            $sql = "SELECT valor FROM bdfrota.tbsupervisor where matricula = '$matricula'";
                            $resultado = mysql_query($sql) or die (mysql_error());
                            $row = mysql_fetch_array($resultado);
                            $saldosup = $row[0]; 
                            print"R$ $saldosup";
                        ?>
                    </h2>

                    <div>
                        <p style='color: red;'>Informe que se você deseja adicionar ou retirar saldo. </p>
                    </div>

                    <div class="d-flex justify-content-between">
                    <!--div tabela retirar-->
                    <div class="card mb-4  col-12">
                        <div class="card-header">
                            <i class="fas fa-table"></i>
                            Remanejamento de Saldo
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
                                        <th>Remanejar (R$)</th>
                                    </tr>
                                </thead>

                                <tbody>
                                <?php
                                $sql = "SELECT idtbsupervisor FROM bdfrota.tbsupervisor where matricula = '$matricula'";
                                
                                $resultado = mysql_query($sql) or die (mysql_error());
                                $row = mysql_fetch_array($resultado);
                                $idtbsupervisor = $row[0];

                                $sql = "SELECT a.matricula, b.nome, b.codempresa, b.status FROM bdfrota.tbusuario a, bdaniel.tbfuncionario b where a.matricula = b.matricula and a.idtbsupervisor = '$idtbsupervisor' and a.perfil = 0 AND b.status<>'Demitido'";                                
                                $resultado = mysql_query($sql) or die (mysql_error());
                                while($row = mysql_fetch_array($resultado)){
                                    $matriculatec = $row[0];
                                    $nometec = $row[1];
                                    $codemptec = $row[2];
                                    $statustec = $row[3];

                                    $sql2 = "SELECT totalextra, idtbsaldo, saldo, orcsemanal FROM bdfrota.tbsaldo where matricula = '$matriculatec' order by data desc";
                                    $resultado2 = mysql_query($sql2) or die (mysql_error());
                                    $row2 = mysql_fetch_array($resultado2);
                                    $totalextra = $row2[0];
                                    $idtbsaldo = $row2[1];
                                    $orcsemanal = $row2[3];
                                    $orcsemanal = round($orcsemanal, 2);
                                    $orcsemanal = str_replace(".", ",", $orcsemanal);

                                    $sql2 = "SELECT placa FROM bdfrota.tbveiculo where matcond = '$matriculatec'";
                                    $resultado2 = mysql_query($sql2) or die (mysql_error());
                                    $row2 = mysql_fetch_array($resultado2);
                                    $placa = $row2[0];                        

                                    if ($placa <> '') {

                                        $saldocartaov = consultacartao($codemptec, $placa);

                                        if($saldocartaov == 'Sem cartão vinculado'){
                                            $d = 'disabled';
                                            $saldocartao = 0;

                                        }else{
                                            $d = '';
                                            $saldocartao = str_replace("R$ ", "", $saldocartaov);
                                            $saldocartao = str_replace(",", ".", $saldocartao);
                                        }

                                        print"                        
                                        <tr>
                                            <td>$matriculatec</td>
                                            <td>$nometec</td>
                                            <td>$placa</td> 
                                            <td>R$ $orcsemanal</td> 
                                            <td>$saldocartaov</td>
                                            <td>
                                                <div class='h-25'>
                                                <form action='./control/remanejamentofinal.php' method='post'>
                                                <div class=''>
                                                    <input type='hidden' name='matriculatec' value='$matriculatec'> 
                                                    <input type='hidden' name='saldoatual' value='$saldocartao'>
                                                    <input type='hidden' name='saldosup' value='$saldosup'>
                                                    <input type='hidden' name='idtbsaldo' value='$idtbsaldo'>
                                                    <input type='hidden' name='codemptec' value='$codemptec'>
                                                    <input type='hidden' name='placa' value='$placa'>
                                                    <input type='hidden' name='idtbsupervisor' value='$idtbsupervisor'>
                                                    <input type='hidden' name='numeroCartao' value='$numeroCartao'>

                                                    <div>Tipo de Ação:<br>
                                                        <input type='radio' name='tipoacao' value='1' required>
                                                        <label for='adic'>Adicionar Saldo</label>
                                                        
                                                        <input type='radio'  name='tipoacao' value='0' class='ms-2'>
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
                //v = v.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
                i.value = v;
            }

            $(function() {
                $(".numerico").maskMoney({
                    //allowNegative: true,
                    thousands: '.',
                    decimal: ',',
                    affixesStay: false
                });
            });

            /*new DataTable('#tabela1', {
                order: [[1, 'asc']],
                //responsive: true,
                responsive: {
                    breakpoints: [
                    {name: 'bigdesktop', width: Infinity},
                    {name: 'meddesktop', width: 1480},
                    {name: 'smalldesktop', width: 1280},
                    {name: 'medium', width: 1188},
                    {name: 'tabletl', width: 1024},
                    {name: 'btwtabllandp', width: 848},
                    {name: 'tabletp', width: 768},
                    {name: 'mobilel', width: 480},
                    {name: 'mobilep', width: 320}
                    ]
                }
            });*/

            $(document).ready(function(){
                /*$(".numerico").maskMoney({
                    allowNegative: true,
                    thousands: '.',
                    decimal: ',',
                    affixesStay: false
                });*/

                $('#tabela1').DataTable({
                    order: [[1, 'asc']],
                    //responsive: true,
                });
            });
        </script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="src/js/scripts.js"></script>
        

    </body>

</html>