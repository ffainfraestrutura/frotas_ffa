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
$datap = $_POST["data"];
if ($datap == '') {
    $datap = date('Y-m-d', strtotime('- 1 days'));
}

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
        <title>KM Zero | Frotas </title>     
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
        <h1 class="h1 mt-2"> Relatório KM Zerado</h1>


<div id="collapseExample4" style="
    width: 33%;
">
<div class="alert alert-info alert-dismissible fade show" role="alert">
  <strong>Filtro Ativo:</strong> Exibindo dados de <strong><?php echo date('d/m/Y', strtotime($datap)); ?></strong>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<form action="./detalhes-kmos-zero.php" method="post">
 <h5><b>Selecione a Data:</b></h5>

     <div class="row">

     <div class="col-md-6">
   <label for="exampleInputDate"><h5>Data:</h5></label>
<input type="date" class="form-control" id="exampleInputDate" name="data" required>
<input type="hidden" name="mattec" value="<?php echo $mattec; ?>">

    </div>


        <div class="col-md-3">
      <label for="exampleInputEmail1" style=" width: 100%; color: white;"><h5>.</h5></label>
      <input type="submit" class="btn btn-success"  value="Filtrar">
    </div>
</div>



</form>
</div>




    <?php   

   
        print"<div class='card mt-4 mb-4'>
            <div class='card-header'>
                 KM OS TOA
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
                            <th>Detalhes</th>
                        </tr>
                    </thead>
                    <tbody>";
                          


        $sql = "(SELECT endereco, bairro, cidade, data, eta, numero_de_ordem, ROUND(kmrota, 1) AS kmrota, motivo, foratoa, matricula
 FROM bdfrota.tbosrota
 WHERE data = '$datap' and numero_de_ordem not in ('CASA', 'To Aqui') and (kmrota = '0' or kmrota is null) Group by matricula)

UNION

(SELECT endereco, bairro, cidade, data, eta, numero_de_ordem, ROUND(kmrota, 1) AS kmrota, motivo, foratoa, matricula
 FROM bdfrota.tbosrotalatlong
 WHERE data = '$datap' and numero_de_ordem not in ('CASA', 'To Aqui') and (kmrota = '0' or kmrota is null) Group by matricula)

ORDER BY matricula , eta;
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


$sql2 = "SELECT * FROM bdfrota.tbveiculo where matcond = '$mattec'";
      $resultado2 = mysql_query($sql2) or die (mysql_error());
      if (mysql_num_rows($resultado2) >= 1) {
           
           // Buscar coordenador e supervisor do técnico
           $sqlCoordenador = "SELECT c.idtbcoordenador, b.matricula FROM bdfrota.tbsupervisor b, bdfrota.tbcoord c, bdfrota.tbusuario a WHERE a.matricula = '$mattec' AND a.idtbsupervisor = b.idtbsupervisor AND b.idtbcoordenador = c.idtbcoordenador";
           $resultCoordenador = mysql_query($sqlCoordenador) or die(mysql_error());
           $rowCoordenador = mysql_fetch_array($resultCoordenador);
           $idCoordenador = $rowCoordenador[0];
           $matriculaSupervisor = $rowCoordenador[1];
           
           $url = "./detalhes-kmoscotaind-latlong.php?mattec=$mattec&coordenador=$idCoordenador&supervisor=$matriculaSupervisor";
           
           print "<tr>
                <td>$mattec</td>
                <td>$nome</td>
                <td>$endcomp</td>
                 <td class='text-nowrap'>$datat</td>
                
                <td><a href='$url' target='_blank' class='btn btn-success btn-sm'>Detalhes</a></td>
               </tr>";

      }
       
        


      }
      
            

        ?>

</tbody></table>
    </div></div>


<?php   

   
        print"<div class='card mt-4 mb-4'>
            <div class='card-header'>
                 KM OS TO AQUI!
            </div>
             <div class='card-body'>
                <!--tabela para entrada de dados-->
                <table id='datatablesSimple2' class='table table-bordered'>
                    <thead>
                        <tr>
                            <th>Matricula</th>
                            <th>Nome</th>
                            <th>Endereço</th>
                            <th>Dia</th>
                            <th>Detalhes</th>
                        </tr>
                    </thead>
                    <tbody>";
                          


        $sql = "(SELECT endereco, bairro, cidade, data, eta, numero_de_ordem, ROUND(kmrota, 1) AS kmrota, motivo, foratoa, matricula
 FROM bdfrota.tbosrota
 WHERE data = '$datap' and numero_de_ordem = 'To Aqui' and (kmrota = '0' or kmrota is null) Group by matricula)

UNION

(SELECT endereco, bairro, cidade, data, eta, numero_de_ordem, ROUND(kmrota, 1) AS kmrota, motivo, foratoa, matricula
 FROM bdfrota.tbosrotalatlong
 WHERE data = '$datap' and numero_de_ordem = 'To Aqui' and (kmrota = '0' or kmrota is null) Group by matricula)

ORDER BY matricula , eta;
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


$sql2 = "SELECT * FROM bdfrota.tbveiculo where matcond = '$mattec'";
      $resultado2 = mysql_query($sql2) or die (mysql_error());
      if (mysql_num_rows($resultado2) >= 1) {
           
           // Buscar coordenador e supervisor do técnico
           $sqlCoordenador = "SELECT c.idtbcoordenador, b.matricula FROM bdfrota.tbsupervisor b, bdfrota.tbcoord c, bdfrota.tbusuario a WHERE a.matricula = '$mattec' AND a.idtbsupervisor = b.idtbsupervisor AND b.idtbcoordenador = c.idtbcoordenador";
           $resultCoordenador = mysql_query($sqlCoordenador) or die(mysql_error());
           $rowCoordenador = mysql_fetch_array($resultCoordenador);
           $idCoordenador = $rowCoordenador[0];
           $matriculaSupervisor = $rowCoordenador[1];
           
           $url = "./detalhes-kmoscotaind-latlong.php?mattec=$mattec&coordenador=$idCoordenador&supervisor=$matriculaSupervisor";
           
           print "<tr>
                <td>$mattec</td>
                <td>$nome</td>
                <td>$endcomp</td>
                 <td class='text-nowrap'>$datat</td>
                
                <td><a href='$url' target='_blank' class='btn btn-success'>Detalhes</a></td>
               </tr>";

      }
       
        


      }
      
            

        ?>
</tbody></table>
</div></div></div>
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