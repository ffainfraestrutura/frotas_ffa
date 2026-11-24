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



include"conecta.php";
include"conecta05.php";
include_once"./func/log.php";
header("Content-type: text/html; charset=utf-8");

$diaf2 = $_POST["diaf2"]; //  1;
$diai2 = $_POST["diai2"];  // 2; 
$mattec = $_POST['mattec'];
$matsup = $_POST['matsup'];

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
        <?php print" <h1 class='h1 mt-2'> Relatório de Deslocamento </h1>"; ?>

        <!--<div id="collapseExample4" style="
    width: 33%;
">
<form action="./detalhes-gpsxfolga.php" method="post">

<h5><b>Selecione o dia:</b></h5>
    <div class="input-group">
        <input class="form-control" type="date" name="diaf">
    
      <input type="submit" class="btn btn-success"  value="Filtrar">
</div>

</form>
</div>-->

<div class="mt-4"><h5 class="text-danger"><b><?php if ($diaf2 <> '') {
    $dataf = explode("-", $diaf2);
    $datat = $dataf[2]."/".$dataf[1]."/".$dataf[0];
    //print"Dados do dia $datat";

} ?></b></h5></div>


        <div class="card mt-4 mb-4">          
            <div class="card-header">
                <?php
                print"<form action='./detalhes-cota-extra-aprovadas.php' method='post'>
                    <input type='hidden' name='diai' value='$diai2'/>
                    <input type='hidden' name='diaf' value='$diaf2'/>
                    <input type='hidden' name='matsupf' value='$matsup' />
                <input type='submit' class='btn btn-secondary'  value='Voltar para Cotas Aprovadas'> ";
                ?>
            </div>
             <div class="card-body">
                <!--tabela para entrada de dados-->
                <table id='datatablesSimple' class='table table-bordered'>
                    <thead>
                        <tr>
                            <th>Matricula</th>
                            <th>Placa</th>
                            <th>Filial</th>
                            <th>Data</th>
                            <th>Endereço</th>
                            <th>KM dia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php     
if ($diaf2 <> '') {
    $sql3 = "SELECT nome, codempresa FROM bdaniel.tbfuncionario where matricula = '$mattec'";
        $resultado3 = mysql_query($sql3) or die (mysql_error());
        $row3 = mysql_fetch_array($resultado3);
        $nometec = $row3[0];
        if ($row3[1] == '2') {
            $filial = 'SP';
        } else {
            $filial = 'RJ';
        }

    $sql4="SELECT ds_placa FROM bdfrota.tbcarrosxcolaborador where id_colaborador='".$mattec."';";
    $resultado4 = mysql_query($sql4) or die(mysql_error());
    $row4=mysql_fetch_array($resultado4);
    $placa = $row4[0];

    $sql2 = "SELECT endereco FROM bdfrota.tbgps where placa = '$placa' ORDER BY idtbgps DESC LIMIT 1";
    
    $resultado2 = mysqli_query($conexao05, $sql2) or die(mysqli_error($conexao05));
    $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
    
    $ukm = 0;

    if (isset($row2['endereco'])) {

    $sql5="SELECT datagps, placa, kmtotal, endereco FROM bdfrota.tbgps where placa = '$placa' and datagps > '$diai2 00:00:00' and datagps < '$diaf2 23:59:59' group by datagps, placa order by datagps";
    $resultado5= mysqli_query($conexao05, $sql5) or die(mysqli_error($conexao05));
    while($row5=mysqli_fetch_array($resultado5, MYSQLI_BOTH)){
        $datagps=$row5['datagps'];
        $kmtotal = $row5['kmtotal'];
        $endereco= $row5['endereco'];

        //tratando data
        $datagps=explode(' ', $datagps);
        $data = explode('-',$datagps[0]);
        $datafim = $data[2]."/".$data[1]."/".$data[0];


                //tratando endereço

        if($kmtotal <> $kmtotalfim){
        print "<tr> <td> $mattec </td>
                    <td> $placa </td>
                    <td> $filial </td>
                    <td> $datafim $datagps[1] </td>
                    <td> $endereco </td>
                    <td> $kmtotal  </td>
                </tr>";
        }

        $kmtotalfim = $kmtotal;
    }

    } else {

    $sql5="SELECT datagps, placa, kmtotal, latitude, longitude FROM bdfrota.tbgps where placa = '$placa' and datagps > '$diai2 00:00:00' and datagps < '$diaf2 23:59:59' group by datagps, placa order by datagps";
    $resultado5= mysqli_query($conexao05, $sql5) or die(mysqli_error($conexao05));
    while($row5=mysqli_fetch_array($resultado5, MYSQLI_BOTH)){
        $datagps=$row5['datagps'];
        $kmtotal = $row5['kmtotal'];
        $lat = $row5['latitude'];
        $lng = $row5['longitude'];

        //tratando data
        $datagps=explode(' ', $datagps);
        $data = explode('-',$datagps[0]);
        $datafim = $data[2]."/".$data[1]."/".$data[0];



        $google_api_key = "AIzaSyAjFFf5-Iws6-IPKtalnuQnopWgOHu-9z4";

        // Monta a URL da API com a latitude, longitude e a chave da API
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$lat.",".$lng."&key=".$google_api_key;

        // Faz uma requisição GET para a API do Google Maps Geocoding
        $response = file_get_contents($url);

        // Converte a resposta JSON em um objeto PHP
        $data = json_decode($response);


          // Obtém o endereço a partir dos componentes retornados pela API
          $endereco = $data->results[0]->formatted_address;
          //echo "Endereço: ".$endereco;

                  //tratando endereço

        if($kmtotal <> $kmtotalfim){
        print "<tr> <td> $mattec </td>
                    <td> $placa </td>
                    <td> $filial </td>
                    <td> $datafim $datagps[1] </td>
                    <td> $endereco </td>
                    <td> $kmtotal  </td>
                </tr>";
        }

        $kmtotalfim = $kmtotal;
    }

    }





} else {

        print "<tr>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
               </tr>";
}
       
      ?>
                        
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>

</main>

<footer class="py-4 bg-light mt-auto" style="position: fixed; bottom: 0; start: 0; width: 100%;">
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