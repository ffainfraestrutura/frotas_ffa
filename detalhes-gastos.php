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
        <h1 class="h1 mt-2"> Ofensores Km Projetado/Km OS </h1>

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
                            <td>Matricula</td>
                            <td>Nome</td>                                
                            <td>Valor Proj.</td>
                            <td>Val. Cota Extra.</td>
                            <td>Val. Gasto OS</td>
                            <td>Total Gasto</td>
                            <td>Saldo</td>
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

$diah = strftime('t');
$ontem = $diah - 1;

$dataini = "$anoini-$mesini-21";
$datafim = "$anofim-$mesfim-$ontem";



if (strftime('%A') == "segunda") {

    $sql = "SELECT matricula, SUM(orcsemanal), SUM(totalextra), SUM(valorosatual), SUM(valoraplicado), round(SUM(valoraplicado - valorosatual),2) as diff FROM bdfrota.tbsaldo where data >= '20".$dataini."' and data <= '20".$datafim."' group by matricula order by diff desc";
    $resultado = mysql_query($sql) or die (mysql_error());

while ($row = mysql_fetch_array($resultado)) {
        
        $mattec = $row[0];
        $valproj = round($row[1],2);
        $totex = round($row[2],2);
        $valos = round($row[3],2);
        $valap = round($row[4],2);
        $diff = round($row[5],2);
        
        $sql2 = "SELECT nome FROM bdaniel.tbfuncionario where matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nometec = $row2[0];

        print "<tr><td>$mattec</td>
              <td>$nometec</td>
              <td style='white-space:  nowrap;'>R$ $valproj</td>
              <td style='white-space:  nowrap;'>R$ $totex</td>
              <td style='white-space:  nowrap;'>R$ $valos</td>
              <td style='white-space:  nowrap;'>R$ $valap</td>
              <td style='white-space:  nowrap;'>R$ $diff</td></tr> ";
    }

} else {

    $domingo = date('Y-m-d', strtotime('last sunday'));
    $hoje = date('Y-m-d');

    $datetime1 = new DateTime($domingo);
    $datetime2 = new DateTime($hoje);
    $interval = $datetime1->diff($datetime2);
    $differenceInDays = $interval->format('%a');


    $sql = "SELECT matricula, round(SUM(valoraplicado - valorosatual),2) as diff FROM bdfrota.tbsaldo where data >= '20".$dataini."' and data <= '$hoje' group by matricula order by diff desc";
    $resultado = mysql_query($sql) or die (mysql_error());

    while ($row = mysql_fetch_array($resultado)) {
        
        $mattec = $row[0];

        $mattec = $row[0];

        $sql2 = "SELECT SUM(orcsemanal), SUM(totalextra), SUM(valorosatual), SUM(valoraplicado), round(SUM(valoraplicado - valorosatual),2) as diff FROM bdfrota.tbsaldo where data >= '20".$dataini."' and data <= '$domingo' and matricula = '$mattec'";
        //echo "$sql2 <br>";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $valproj = round($row2[0],2);
        $totex = round($row2[1],2);
        $valos = round($row2[2],2);
        $valap = round($row2[3],2);
        $diff = round($row2[4],2);


        $sql2 = "SELECT SUM(orcsemanal), SUM(totalextra), SUM(valorosatual), SUM(valoraplicado), round(SUM(valoraplicado - valorosatual),2) as diff FROM bdfrota.tbsaldo where data > '$domingo' and data <= '$hoje' and matricula = $mattec";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        //echo "$sql2 <br>";
        $row2 = mysql_fetch_array($resultado2);
        
        $valproj = round($valproj + (($row2[0] / 7) * $differenceInDays),2);
        $totex = round($totex + (($row2[1] / 7) * $differenceInDays),2);
        $valos = round($valos + (($row2[2] / 7) * $differenceInDays),2);
        $valap = round($valap + (($row2[3] / 7) * $differenceInDays),2);
        $diff = round($diff + (($row2[4] / 7) * $differenceInDays),2);

        $sql2 = "SELECT nome FROM bdaniel.tbfuncionario where matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nometec = $row2[0];

        print "<tr><td>$mattec</td>
              <td>$nometec</td>
              <td style='white-space:  nowrap;'>R$ $valproj</td>
              <td style='white-space:  nowrap;'>R$ $totex</td>
              <td style='white-space:  nowrap;'>R$ $valos</td>
              <td style='white-space:  nowrap;'>R$ $valap</td>
              <td style='white-space:  nowrap;'>R$ $diff</td></tr> ";
    }

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