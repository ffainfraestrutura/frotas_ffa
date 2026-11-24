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

$diaf = $_POST["diaf"];
$diai = $_POST["diai"];



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
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    </head>

    <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
        <!--nav-->
        <?php include"menu.php"; ?>

<div id="layoutSidenav_content">

    <main>
    <div class="container-fluid px-4">
        <h1 class="h1 mt-2"> Relatório Cotas Extra Indevidas </h1>


<div id="collapseExample4" style="
    width: 100%;
">
<form action="./detalhes-cota-extra-indevidas-sup.php" method="post">
 <h5><b>Selecione a Data e Supervisor:</b></h5>

     <div class="row">

     <div class="col-md-3">
      <label for="exampleInputEmail1"><h5>Início:</h5></label>
      <input min="2023-03-17" type="date" class="form-control form-control-md" name="diai">
    </div>

    <div class="col-md-3">
      <label for="exampleInputEmail1"><h5>Fim:</h5></label>
      <input min="2023-03-17" type="date" class="form-control form-control-md" name="diaf">
    </div>
        <div class="col-md-3">
      <label for="exampleInputEmail1" style=" width: 100%; color: white;"><h5>.</h5></label>
      <input type="submit" class="btn btn-success"  value="Filtrar">
    </div>
</div>



</form>

</div>

        <div class="card mt-4 mb-4">
            <div class="card-header">
                <a href="./dashboard.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i>
                    Voltar para Dashboard
                </a href="./dashboard.php">
            </div>
             <div class="card-body">
                <!--tabela para entrada de dados-->
                <table id='datatablesSimple' class='table table-bordered'>
                    <thead>
                        <tr>
                            <th>Matricula</th>
                            <th>Nome</th>
                            <th>Placa</th>
                            <th>Supervisor</th>
                            <th>Orc. Semanal</th>
                            <th>KM Proj.</th>
                            <th>KM OS</th>
                            <th>KM GPS</th>
                            <th>Sld. Cartão</th>
                            <th>Tot. Extra</th>
                            <th>Data e Hora</th>
                            <th>Justificativa</th>
                            <th>Valor Pedido</th>
                            <th>Valor Inserido</th>
                            <th>Just. Supervisor</th>
                            <th>Data Aceito</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php     
if ($diaf == '' or $diai == '') {
    
print "<tr>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
               </tr>";


} else {

        

        $sql = "SELECT matricula, orcsemanal, kmproj, kmos, gps, sldcartao, totalextra, data, justificativa, valor, valorinserido, dataaceite, matsup, justsup FROM bdfrota.tbcotaextraac where matsup = '$matricula' and dataaceite > '$diai 00:00:00' and dataaceite < '$diaf 23:39:59' and justsup is not null order by data";
        
      $resultado = mysql_query($sql) or die (mysql_error());
      
      while ($row = mysql_fetch_array($resultado)){

        $mattec = $row[0];
        $orcsemanal = $row[1];
        $kmproj = $row[2];
        $kmos = $row[3];
        $gps = $row[4];
        $sldcartao = $row[5];
        $totalextra = $row[6];
        $data = $row[7];
        $justificativa = $row[8];
        $valor = $row[9];
        $valorinserido = $row[10];
        $dataaceite = $row[11];
        $matricula = $row[12];
        $justsup = $row[13];

        $sql2 = "SELECT ds_placa FROM bdfrota.tbcarrosxcolaborador where id_colaborador = '$mattec'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $placa = $row2[0];

        $sql2 = "SELECT nome FROM bdfrota.tbusuario where matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nometec = $row2[0];

                $sql2 = "SELECT nome FROM bdfrota.tbusuario where matricula = '$matricula'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nomesup = $row2[0];


        print "<tr>
                <td>$mattec</td>
                <td>$nometec</td>
                <td>$placa</td>
                <td>$nomesup</td>
                <td>$orcsemanal</td>
                <td>$kmproj</td>
                <td>$kmos</td>
                <td>$gps</td>
                <td>$sldcartao</td>
                <td>$totalextra</td>
                <td>$data</td>
                <td>$justificativa</td>
                <td>$valor</td>
                <td>$valorinserido</td>
                <td>$justsup</td>
                <td>$dataaceite</td>
               </tr>";

        


      }
}
      ?>
                        
                    </tbody>
                </table>
                <form action="./control/gerarexcelcota-extra-indevida.php" method="post">
              <?php 
              print "<input type='hidden' name='diai' value='$diai'>";
              print "<input type='hidden' name='diaf' value='$diaf'>";?>
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