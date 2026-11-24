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
include"conecta2.php";
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
$dataac = date("Y-m-d", strtotime($dataac . "-1 days"));
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

$idtbpedidostec = $_POST["idtbpedido"];

$sql = "SELECT matricula, data FROM bdfrota.tbpedidostec where idtbpedidostec = '$idtbpedidostec'";

        $resultado = mysql_query($sql) or die (mysql_error());
        $row = mysql_fetch_array($resultado);
        $mattec = $row[0];
        $dataac = $row[1];
$dataAtual = date('Y-m-d'); // Data atual

if ($dataac <= $dataComparacao) {
    echo"<script language='javascript' type='text/javascript'>alert('Só existem dados acima de 02/10/2023');window.location=\"index.php\"</script>";
}


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
        <h1 class="h1 mt-2"> Relatório Atividades Fora TOA</h1>


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
                            <th>Endereço</th>
                            <th>Operador</th>
                            <th>Motivo</th>
                            <th>Data</th>
                            <th>Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php   
        

        $sql = "SELECT nome, ccusto FROM bdaniel.tbfuncionario where matricula = '$mattec'";
      $resultado = mysql_query($sql) or die (mysql_error());
      $row = mysql_fetch_array($resultado);
        $nometec = utf8_encode($row[0]);
        $ccusto = utf8_encode($row[1]);

         // Verifica se o valor de $ccusto contém "TIM"
        if (strpos($ccusto, 'TIM') !== false) {
            $sql = "SELECT data, provedor, operador, motivo, endereco, eta FROM bdfrota.tbosrota WHERE data >= '$diai 00:00:00' and data <= '$diaf' and matricula = '$mattec' and foratoa = 1 order by data, eta";
        }
        // Verifica se o valor de $ccusto contém "CLARO"
        elseif (strpos($ccusto, 'CLARO') !== false) {
            $sql = "SELECT data, provedor, operador, motivo, endereco, eta FROM bdfrota.tbosrotalatlong WHERE data >= '$diai 00:00:00' and data <= '$diaf' and matricula = '$mattec' and foratoa = 1 order by data, eta";
        }elseif(strpos($ccusto, 'LIGGA') !== false) {
            $sql = "SELECT data, provedor, operador, motivo, endereco, eta FROM bdfrota.tbosrota WHERE data >= '$diai 00:00:00' and data <= '$diaf' and matricula = '$mattec' and foratoa = 1 order by data, eta";
        } else {
            $sql = "SELECT data, provedor, operador, motivo, endereco, eta FROM bdfrota.tbosrota WHERE data >= '$diai 00:00:00' and data <= '$diaf' and matricula = '$mattec' and foratoa = 1 order by data, eta";
        }

         
      $resultado = mysql_query($sql) or die (mysql_error());     
      while ($row = mysql_fetch_array($resultado)){

        $dataf = $row['data'];
        $provedor = $row['provedor'];
        $operador = $row['operador'];
        $motivo = $row['motivo'];
        $enderecof = $row['endereco'];
        $eta = $row['eta'];

        $dataf1 = explode(" ", $dataf);
        $dataf2 = explode("-", $dataf1[0]);
        $datafinal= $dataf2[2]."/".$dataf2[1]."/".$dataf2[0];

        $sql2 = "SELECT nome, ccusto FROM bdaniel.tbfuncionario WHERE matricula = '$operador'";
        $resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
        $row2=mysqli_fetch_array($resultado2, MYSQLI_BOTH);

        $nomeop = $row2['nome'];
        $ccusto = $row2['ccusto'];

        print "<tr>
                <td>$mattec</td>
                <td>$nometec</td>
                <td>$enderecof</td>
                <td>$nomeop</td>
                <td>$motivo</td>
                <td>$datafinal</td>
                <td>$eta</td>
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