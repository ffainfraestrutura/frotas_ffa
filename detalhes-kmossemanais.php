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

$mattec = $_GET["matriculatec"];
$datasem = $_GET["semana"];

// Data de referência (pode ser qualquer data)
$data_referencia = new DateTime($datasem); // Substitua essa data pela sua data de referência

// Use o método modify para avançar para o próximo domingo
$data_referencia->modify('next sunday');

// Obtenha a data do próximo domingo formatada
$proximo_domingo = $data_referencia->format('Y-m-d');

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
        <h1 class="h1 mt-2"> Relatório Deslocamento</h1>


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
                            <th>Dia</th>
                            <th>KMs</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php     
        $sql = "SELECT nome, ccusto FROM bdaniel.tbfuncionario where matricula = '$mattec'";
      $resultado = mysql_query($sql) or die (mysql_error());
      $row = mysql_fetch_array($resultado);
      $nome = $row[0];
      $ccusto = $row[1];

     // Verifica se o valor de $ccusto contém "TIM"
        if (strpos($ccusto, 'TIM') !== false) {
            $tabela = "bdfrota.tbosrota";
        }
        // Verifica se o valor de $ccusto contém "CLARO"
        elseif (strpos($ccusto, 'CLARO') !== false) {
            $tabela = "bdfrota.tbosrotalatlong";
        }elseif(strpos($ccusto, 'LIGGA') !== false) {
            $tabela = "bdfrota.tbosrota";
        } else {
            $tabela = "bdfrota.tbosrota";
        }
        $sql = "SELECT data FROM $tabela where matricula = '$mattec' and data >= '$datasem' and data <= '$proximo_domingo 23:59:59' group by data order by data";
        
      $resultado = mysql_query($sql) or die (mysql_error());
      
      while ($row = mysql_fetch_array($resultado)){

        $data = $row[0];
        $datac = explode(" ", $data);
        $datat = $datac[0];

        $endcomp = utf8_encode("$endereco $bairro $cidade");

        $sql2 = "SELECT ROUND(kmsrodados,2) FROM bdfrota.tbkmrodadosdia where matricula = '$mattec' and dia = '$data'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);

        $kmsrodados = $row2[0];

        print "<tr>
                <td>$mattec</td>
                <td>$nome</td>
                <td>$datat</td>
                <td>$kmsrodados</td>
               </tr>";

        


      }
      ?>
                        
                    </tbody>
                </table>
            
            </div>
        </div>

        <div class="card mt-4 mb-4">
            <div class="card-header">
                 
            </div>
             <div class="card-body">
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php     
        $sql = "SELECT nome, ccusto FROM bdaniel.tbfuncionario where matricula = '$mattec'";
      $resultado = mysql_query($sql) or die (mysql_error());
      $row = mysql_fetch_array($resultado);
      $nome = $row[0];
      $ccusto = $row[1];

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


        $sql = "SELECT endereco, bairro, cidade, data, eta, numero_de_ordem FROM $tabela where matricula = '$mattec' and data >= '$datasem' and data <= '$proximo_domingo 23:59:59' order by data, eta";
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

        $endcomp = utf8_encode("$endereco $bairro $cidade");


        $kmsrodados = $row2[0];

        print "<tr>
                <td>$mattec</td>
                <td>$nome</td>
                <td>$endcomp</td>
                <td>$datat</td>
                <td>$eta</td>
                <td>$os</td>
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