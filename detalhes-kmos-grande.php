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
    $datai = isset($_POST["datai"]) ? $_POST["datai"] : '';
    $dataf = isset($_POST["dataf"]) ? $_POST["dataf"] : '';
$datap = $datai;
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
        <h1 class="h1 mt-2"> Relatório Deslocamento Grandes OS</h1>


<div id="collapseExample4" style="
    width: 33%;
">
<form action="./detalhes-kmos-grande.php" method="post">
 <h5><b>Selecione o Periodo:</b></h5>

     <div class="row">


     <div class="col-md-4">
   <label for="exampleInputDate1"><h5>Data Inicial:</h5></label>
<input type="date" class="form-control" id="exampleInputDate1" name="datai" required>
    </div>     
    <div class="col-md-4">
   <label for="exampleInputDate2"><h5>Data Final:</h5></label>
<input type="date" class="form-control" id="exampleInputDate2" name="dataf" required>
    </div>
<input type="hidden" name="mattec" value="<?php echo $mattec; ?>">
        <div class="col-md-3">
      <label for="exampleInputEmail1" style=" width: 100%; color: white;"><h5>.</h5></label>
      <input type="submit" class="btn btn-success"  value="Filtrar">
    </div>
</div>



</form>
</div>




    <?php   

  if ($datap == '' or $dataf == '') {
    $datap = date('Y-m-d', strtotime('- 1 days'));
    $dataf = date('Y-m-d', strtotime('- 1 days'));
}
$datam = date('Y-m-d', strtotime($dataf.'- 15 days')); 
        print"<div class='card mt-4 mb-4'>
            <div class='card-header'>
                 KM OS
            </div>
             <div class='card-body'>
                <!--tabela para entrada de dados-->
                <table id='datatablesSimple' class='table table-bordered'>
                    <thead>
                        <tr>
                            <th>Matricula</th>
                            <th>Nome</th>
                            <th>Endereço</th>
                            <th>Dia</th>
                            <th>Hora</th>
                            <th>OS</th>
                            <th>Origem</th>
                            <th>KM</th>
                            <th>Editar</th>
                        </tr>
                    </thead>
                    <tbody>";
    $sql = "SELECT ROUND(AVG(kmrota),2) FROM bdfrota.tbosrota where data >= '$datam' and kmrota is not null and kmrota > 0 and matricula in (SELECT matricula FROM bdaniel.tbfuncionario where status <> 'demitido' and (ccusto like '%inst%' or ccusto like '%rep%' or ccusto like '%iat%'))";                
    $resultado = mysql_query($sql) or die (mysql_error());
    $row = mysql_fetch_array($resultado);
    $medkm = $row[0];

        $sql = "(SELECT endereco, bairro, cidade, data, eta, numero_de_ordem, ROUND(kmrota, 1) AS kmrota, motivo, foratoa, matricula
 FROM bdfrota.tbosrota
 WHERE data >= '$datap' and data <= '$dataf' AND kmrota > $medkm * 2 AND matricula IN (SELECT matricula FROM bdaniel.tbfuncionario WHERE ccusto LIKE '%inst%' OR ccusto LIKE '%rep%'))

UNION

(SELECT endereco, bairro, cidade, data, eta, numero_de_ordem, ROUND(kmrota, 1) AS kmrota, motivo, foratoa, matricula
 FROM bdfrota.tbosrotalatlong
 WHERE data >= '$datap 00:00:01' and data <= '$dataf 23:59:59' AND kmrota > $medkm * 2 AND matricula IN (SELECT matricula FROM bdaniel.tbfuncionario WHERE ccusto LIKE '%inst%' OR ccusto LIKE '%rep%'))

ORDER BY matricula, kmrota, eta;
";
      $resultado = mysql_query($sql) or die (mysql_error());
      
              
      while ($row = mysql_fetch_array($resultado)){

        $data = $row[3];
        $datac = explode(" ", $data);
        $datat = $datac[0];

        $endereco = $row[0];
        $bairro = $row[1];
        $cidade = $row[2];
        $eta = $row[4];
        $os = $row[5];
        if ($os == '') {
            $os = $row[7];
        }
        $kmrota = $row[6];
        if ($kmrota == '') {
            $kmrota = 0;
        }

        $foratoa = $row[8];
        if ($foratoa == 1) {
            $origem = 'MANUAL';
        } elseif ($os == 'CASA') {
            $origem = 'ANIEL';
        } else {
            $origem = 'TOA';
        }
        $mattec = $row[9];

        $endcomp = "$endereco $bairro $cidade";

        $sql2 = "SELECT nome, ccusto FROM bdaniel.tbfuncionario where matricula = '$mattec'";
      $resultado2 = mysql_query($sql2) or die (mysql_error());
      $row2 = mysql_fetch_array($resultado2);
      $nome = $row2[0];
      $ccusto = $row2[1];

      // Verifica se o valor de $ccusto contém "TIM"
        if (strpos($ccusto, 'TIM') !== false) {
            $tabela = "bdfrota.tbosrota";
        }
        // Verifica se o valor de $ccusto contém "CLARO"
        elseif (strpos($ccusto, 'CLARO') !== false) {
            $tabela = "bdfrota.tbosrotalatlong";
        }elseif(strpos($ccusto, 'LIGGA') !== false) {
            $tabela = "bdfrota.tbosrota";
        }


if (mb_detect_encoding($endcomp, 'UTF-8', true)) {
    $endcomp = "$endereco $bairro $cidade";
} else {
    $endcomp = utf8_encode("$endereco $bairro $cidade");
}
        


        $kmsrodados = $row2[0];

        print "<tr>
                <td>$mattec</td>
                <td>$nome</td>
                <td>$endcomp</td>
                 <td class='text-nowrap'>$datat</td>
                <td>$eta</td>
                <td>$os</td>
                <td>$origem</td>
                <td>$kmrota</td>
                <td>    <form action='./detalhes-kmoscotaind-edit.php' method='post'>
        <!-- Campo de entrada oculto -->
        <input type='hidden' name='mattec' value='$mattec'>

        <!-- Botão de editar com classe 'btn-success' do Bootstrap 5 -->
        <button type='submit' class='btn btn-success'>Editar</button>
    </form></td>
               </tr>";

        


      }
      
            

        ?>


    </div>

</main>



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