<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
/*if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil == '0'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}*/
include"conecta.php";
include_once"./func/log.php";

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

$idtbpedidostec = $_POST["idtbpedido"];
//$idtbpedidostec = '18267';
//$idtbpedidostec = '19006';

$sql = "SELECT matricula, data FROM bdfrota.tbpedidostec where idtbpedidostec = '$idtbpedidostec'";

        $resultado = mysql_query($sql) or die (mysql_error());
        $row = mysql_fetch_array($resultado);
        $mattec = $row[0];
        $dataac = $row[1];

// Data de referência (pode ser qualquer data)
$data_referencia = new DateTime($dataac); // Substitua essa data pela sua data de referência

// Verifique se hoje é uma segunda-feira (1 é segunda-feira no PHP)
if ($data_referencia->format('N') == 1) {
    // Hoje é uma segunda-feira
    $usegunda = $data_referencia->format('Y-m-d');
} else {
    // Hoje não é uma segunda-feira
    $data_referencia->modify('last monday');
    $usegunda = $data_referencia->format('Y-m-d');
}

$diai = $usegunda;
$diaf = $dataac;
$matriculaf = $mattec;


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
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    </head>

    <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
        <!--nav-->
        <?php include"menu.php"; ?>

<div id="layoutSidenav_content">

    <main>
    <div class="container-fluid px-4">
        <h1 class="h1 mt-2"> Relatório Deslocamento Fora Horario</h1>


<div id="collapseExample4" style="
    width: 33%;
">

</div>

<div class="card mt-4 mb-4">
            <div class="card-header">
                 
            </div>
             <div class="card-body">
                <!--tabela para entrada de dados-->
                <table id='datatablesSimple2' class='table table-bordered'>
                    <thead>
                        <tr>
                            <th>Matricula</th>
                            <th>Nome</th>
                            <th>Placa</th>
                            <th>Endereço</th>
                            <th>KMs</th>
                            <th>Data e Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php   
        

        $sql = "SELECT nome, ccusto FROM bdaniel.tbfuncionario where matricula = '$mattec'";
      $resultado = mysql_query($sql) or die (mysql_error());
      $row = mysql_fetch_array($resultado);
        $nometec = utf8_encode($row[0]);
        $ccusto = utf8_encode($row[1]);

         $sql = "SELECT endereco, kmtotal, data, placa FROM bdfrota.tbrelatoriogpsmovind where data > '$diai 00:00:00' and data < '$diaf 23:39:59' and matricula = '$mattec' order by data";
         
      $resultado = mysql_query($sql) or die (mysql_error());     
      while ($row = mysql_fetch_array($resultado)){

        $endereco = $row[0];
        $kmtotal = $row[1];
        $datagps = $row[2];
        $placatec = $row[3];


        print "<tr>
                <td>$mattec</td>
                <td>$nometec</td>
                <td>$placatec</td>
                <td>$endereco</td>
                <td>$kmtotal</td>
                <td>$datagps</td>
               </tr>";

        }


      
      ?>
                        
                    </tbody>
                </table>
            
            </div>
        </div>


    </div>

</main>

<footer class="py-4 bg-light mt-auto" style="bottom: 0; start: 0; width: 100%;">
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
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="src/js/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="assets/demo/chart-area-demo.js"></script>
<script src="assets/demo/chart-bar-demo.js"></script>
<script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
<script src="src/js/datatables-simple-demo.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>

<!--script para criar data table-->
<script>
    //função iniciar data table 
    $(document).ready( function () {
        $('#datatablesSimple').DataTable();
    });
</script>
</html>