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
//$idtbpedidostec = '19005';

$sql = "SELECT matricula, dataaceite FROM bdfrota.tbcotaextraac where id = '$idtbpedidostec'";

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

// Data de referência (pode ser qualquer data)
$data_referenciad = new DateTime($usegunda); // Substitua essa data pela sua data de referência

// Use o método modify para avançar para o próximo domingo
$data_referenciad->modify('next sunday');

// Obtenha a data do próximo domingo formatada
$proximo_domingo = $data_referenciad->format('Y-m-d');

$segundas = [];
$domingoss = [];
$i=0;
while (count($segundas) < 3) {
        $data_referenciad->modify('last monday');
        $segundas[] = $data_referenciad->format('Y-m-d');
        $segundasf[] = $data_referenciad->format('d/m/Y');
        $data_referenciad2 = new DateTime($segundas[$i]);
        $data_referenciad2->modify('next sunday');
        $domingos[] = $data_referenciad2->format('Y-m-d');
        $domingosf[] = $data_referenciad2->format('d/m/Y');
        $data_referenciad->modify('-1 day'); // Volte 1 dia para evitar a repetição da mesma segunda-feira
        $i=$i+1;
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
        <h1 class="h1 mt-2">Extrato KM Projetado</h1>


<div id="collapseExample4" style="
    width: 33%;
">
<?php
$sql = "SELECT nome, ccusto FROM bdaniel.tbfuncionario where matricula = '$mattec'";
      $resultado = mysql_query($sql) or die (mysql_error());
      $row = mysql_fetch_array($resultado);
      $nome = $row[0];
      $ccusto = $row[1];
Print"$mattec $nome";
?>
</div>

<div class="card mt-4 mb-4">
            <div class="card-header">
                 Semana <?php print"$segundasf[0] - $domingosf[0]"; ?>
            </div>
             <div class="card-body">
                <!--tabela para entrada de dados-->
                <table class='table table-bordered'>
                   
                    <thead>
                        <tr>
                            <th>Dia</th>
                            <th>KMs</th>
                        </tr>
                    </thead>
                    <tbody>
                         <?php     
        

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
        $sql = "SELECT data FROM $tabela where matricula = '$mattec' and data >= '$segundas[0]' and data <= '$domingos[0] 23:59:59' group by data order by data";
        
      $resultado = mysql_query($sql) or die (mysql_error());
      $skmrodados1 = 0;
      while ($row = mysql_fetch_array($resultado)){

        $data = $row[0];
        $datac = explode(" ", $data);
        $datat = $datac[0];

        $endcomp = utf8_encode("$endereco $bairro $cidade");

        $sql2 = "SELECT ROUND(kmsrodados,0) FROM bdfrota.tbkmrodadosdia where matricula = '$mattec' and dia = '$data'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);

        $kmsrodados = $row2[0];
        $skmrodados1 = $skmrodados1 + $kmsrodados;
        print "<tr>
                <td>$datat</td>
                <td>$kmsrodados</td>
               </tr>";

        


      }
      ?>
                        
                    </tbody>
                    <thead>
                        <tr>
                            <th>Semana <?php print"$segundasf[0] - $domingosf[0]"; ?> total</th>
                            <th><?php print"$skmrodados1"; ?></th>
                        </tr>
                    </thead>
                </table>
                
            </div>
        </div>

<div class="card mt-4 mb-4">
            <div class="card-header">
                 Semana <?php print"$segundasf[1] - $domingosf[1]"; ?>
            </div>
             <div class="card-body">
                <!--tabela para entrada de dados-->
                <table class='table table-bordered'>
               
                    <thead>
                        <tr>
                            <th>Dia</th>
                            <th>KMs</th>
                        </tr>
                    </thead>
                    <tbody>
                         <?php     
        

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
        $sql = "SELECT data FROM $tabela where matricula = '$mattec' and data >= '$segundas[1]' and data <= '$domingos[1] 23:59:59' group by data order by data";
        
      $resultado = mysql_query($sql) or die (mysql_error());
      $skmrodados2 = 0;
      while ($row = mysql_fetch_array($resultado)){

        $data = $row[0];
        $datac = explode(" ", $data);
        $datat = $datac[0];

        $endcomp = utf8_encode("$endereco $bairro $cidade");

        $sql2 = "SELECT ROUND(kmsrodados,0) FROM bdfrota.tbkmrodadosdia where matricula = '$mattec' and dia = '$data'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);

        $kmsrodados = $row2[0];
        $skmrodados2 = $skmrodados2 + $kmsrodados;
        print "<tr>
                <td>$datat</td>
                <td>$kmsrodados</td>
               </tr>";

        


      }
      ?>
                        
                    </tbody>
                    <thead>
                        <tr>
                            <th>Semana <?php print"$segundasf[1] - $domingosf[1]"; ?> total</th>
                            <th><?php print"$skmrodados2"; ?></th>
                        </tr>
                    </thead>
                </table>
            
            </div>
        </div>

<div class="card mt-4 mb-4">
            <div class="card-header">
                 Semana <?php print"$segundasf[2] - $domingosf[2]"; ?>
            </div>
             <div class="card-body">
                <!--tabela para entrada de dados-->
                <table class='table table-bordered'>
                
                    <thead>
                        <tr>
                            <th>Dia</th>
                            <th>KMs</th>
                        </tr>
                    </thead>
                    <tbody>
                         <?php     
        

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
        $sql = "SELECT data FROM $tabela where matricula = '$mattec' and data >= '$segundas[2]' and data <= '$domingos[2] 23:59:59' group by data order by data";
        
      $resultado = mysql_query($sql) or die (mysql_error());
      $skmrodados3 = 0;
      while ($row = mysql_fetch_array($resultado)){

        $data = $row[0];
        $datac = explode(" ", $data);
        $datat = $datac[0];

        $endcomp = utf8_encode("$endereco $bairro $cidade");

        $sql2 = "SELECT ROUND(kmsrodados,0) FROM bdfrota.tbkmrodadosdia where matricula = '$mattec' and dia = '$data'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);

        $kmsrodados = $row2[0];
        $skmrodados3 = $skmrodados3 + $kmsrodados;
        print "<tr>
                <td>$datat</td>
                <td>$kmsrodados</td>
               </tr>";

        


      }
      ?>
                        
                    </tbody>
                    <thead>
                        <tr>
                            <th>Semana <?php print"$segundasf[2] - $domingosf[2]"; ?> total</th>
                            <th><?php print"$skmrodados3"; ?></th>
                        </tr>
                    </thead>
                </table>
            
            </div>
        </div>


        <div class="card mt-4 mb-4">
            <div class="card-header">
                 Média Projetada
            </div>
             <div class="card-body">
                <!--tabela para entrada de dados-->
                <table class='table table-bordered'>
                    <thead>
                        <tr>
                            <th>KM</th>
                            <th>R$</th>
                        </tr>
                    </thead>
                    <tbody>
                         <?php     
        $mkmrodados = round(($skmrodados1 + $skmrodados2 + $skmrodados3) / 3, 0);
        $rsrodados = round(($mkmrodados / 10) * 5.74, 2);
        print "<tr>
                <td>$mkmrodados</td>
                <td>R$: $rsrodados</td>
               </tr>";

        


      
      ?>
                        
                    </tbody>
                    <thead>
                        <tr>
                            <th>Semana <?php print"$segundasf[2] - $domingosf[2]"; ?> total</th>
                            <th><?php print"$skmrodados"; ?></th>
                        </tr>
                    </thead>
                </table>
            <p class="text-danger"><b>OBS: Utiliamos o valor do combustível atual para o cálculo de média</b></p>
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