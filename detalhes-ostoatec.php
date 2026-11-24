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
        <h1 class="h1 mt-2"> Detalhes OS-TOA </h1>

        <div class="card mb-4">
            <div class="card-header">
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i>
                    Voltar para Dashboard Inicial 
                </a>
            </div>
             <div class="card-body">
                <!--tabela para entrada de dados-->
                <table id='datatablesSimple' class='table table-bordered'>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Endereço</th>
                            <th>Inicio</th>
                            <th>Fim</th>
                            <th>Data</th>
                            <th>OS</th>
                        </tr>
                    </thead>
                    <tbody>
<?php

if (strftime('t') >= 21) {

    $mesini = date('m');
    $anoini = date('y');
    if (date('m') == 12) {
        $mesfim = 1;
        $anofim = date('y', strtotime('next year'));
    } else {
        $mesfim = date('m', strtotime('next month'));
        $anofim = date('y');
    }

} else {

    $mesfim = date('m');
    $anofim = date('y');
    if (date('m') == 1) {
        $mesini = 12;
        $anoini = date('y', strtotime('last year'));
    } else {
        $mesini = date('m', strtotime('last month'));
        $anoini = date('y');
    }

}



    $sql = "SELECT provedor, endereco, cidade, eta, fim, data, numero_de_ordem FROM bdfrota.tbosrota where provedor like '%pedro henri%' and data = '2023-02-02' order by data, fim";
    $resultado = mysql_query($sql) or die (mysql_error());

while ($row = mysql_fetch_array($resultado)) {
        
$dataos = explode(' ', $row[5]);

        print "<tr><td style='white-space: nowrap;'>$row[0]</td>
              <td>$row[1] - $row[2]</td>
              <td>$row[3]</td>
              <td>$row[4]</td>
              <td style='white-space: nowrap;'>$dataos[0]</td>
              <td>$row[6]</td>
              </tr> ";
    }


?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

</main>

<footer class="py-4 bg-light mt-auto" >
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