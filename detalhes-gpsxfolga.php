<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil == '0'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

$diai = $_POST["diai"];
$diaf = $_POST["diaf"];
$matriculaf = $_POST["matriculatec"];
$nometec = $_POST["nometec"];
$placatec = $_POST["placatec"];
$matsup = $_POST["matsupf"];
$ordData = isset($_POST["ord_data"]) ? strtoupper($_POST["ord_data"]) : 'ASC';
if ($ordData != 'DESC') {
    $ordData = 'ASC';
}
$proxOrdData = ($ordData == 'ASC') ? 'DESC' : 'ASC';

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
        <title>Detalhes Desloc Fora da Escala</title>
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
        <h1 class="h1 mt-2"> Relatório Deslocamento Fora da Escala </h1>


<div id="collapseExample4" style="
    width: 33%;
">

</div>

        <div class="card mt-4 mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <!-- <button type="button" class="btn btn-secondary" onclick="window.history.back(); return false;">
                    <i class="fa-solid fa-arrow-left"></i> Voltar
                </button> -->
                <form action="./detalhes-gpsxfolga.php" method="post" class="d-inline">
                    <?php
                    print "<input type='hidden' name='diai' value='$diai'>";
                    print "<input type='hidden' name='diaf' value='$diaf'>";
                    print "<input type='hidden' name='matriculatec' value='$matriculaf'>";
                    print "<input type='hidden' name='nometec' value='$nometec'>";
                    print "<input type='hidden' name='placatec' value='$placatec'>";
                    print "<input type='hidden' name='matsupf' value='$matsup'>";
                    print "<input type='hidden' name='ord_data' value='$proxOrdData'>";
                    ?>
                    <button type="submit" class="btn btn-outline-primary">
                        Ordem Data/Hora: <?php echo $ordData; ?>
                    </button>
                </form>
                <!-- <button type="button" class="btn btn-danger" onclick="window.close();">
                    <i class="fa-solid fa-xmark"></i> Fechar
                </button> -->
            </div>
             <div class="card-body">
                <!--tabela para entrada de dados-->
                <table id='datatablesSimple' class='table table-bordered'>
                    <thead>
                        <tr>
                            <th>Matricula</th>
                            <th>Nome</th>
                            <th>Placa</th>
                            <th>Endereço</th>
                            <th>KMs</th>
                            <th>Data e Hora (<?php echo $ordData; ?>)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php     
if ($diaf <> '') {
    



} else {

        print "<tr>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
               </tr>";
}
        $sql = "SELECT endereco, kmtotal, data FROM bdfrota.tbrelatoriogpsfalta where matricula = '$matriculaf' and data > '$diai 00:00:00' and data < '$diaf 23:39:59' order by data $ordData";
      $resultado = mysql_query($sql) or die (mysql_error());
      
      while ($row = mysql_fetch_array($resultado)){

        $endereco = $row[0];
        $kmtotal = $row[1];
        $datagps = $row[2];


        print "<tr>
                <td>$matriculaf</td>
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
            <form action="./control/gerarexcelgpsxfolga-det.php" method="post">
              <?php 
              print "<input type='hidden' name='diai' value='$diai'>";
              print "<input type='hidden' name='diaf' value='$diaf'>";
              print "<input type='hidden' name='matriculaf' value='$matriculaf'>";
              print "<input type='hidden' name='placatec' value='$placatec'>";
              print "<input type='hidden' name='nometec' value='$nometec'>";
              print "<input type='hidden' name='matsupf' value='$matsupf'>"; ?>
              <div class="form-group col-md-2">
                <label for="exampleInputEmail1"><b style="color: white">.</b></label>
                <input type="submit" class="form-control form-control-md btn btn-success" value="GERAR EXCEL">
              </div></form>
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