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
} elseif ($perfil <> '3' and $perfil <> '4' and $perfil <> '5'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

$idsup = $_POST["idtbsup"];

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
        <h1 class="h1 mt-2"> KMs Semanais </h1>



<div id="collapseExample4" style="
    width: 33%;
">
<form action="./detalhes-kmsem.php" method="post">

<h5><b>Supervisor:</b></h5>
    <div class="input-group">
    <select class="form-select" id="inputGroupSelect04" name="idtbsup">
        <option value=''>Selecione o supervisor...</option>
        <?php

        $sql = "SELECT distinct(idtbsupervisor) FROM bdfrota.tbusuario where matricula in (SELECT distinct(matricula) FROM bdfrota.tbsaldo) and idtbsupervisor <> 0";
        $resultado = mysql_query($sql) or die (mysql_error());

while ($row = mysql_fetch_array($resultado)) {

        $idtbsup = $row[0];


        $sql2 = "SELECT b.nome FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b where a.matricula = b.matricula and a.idtbsupervisor = $idtbsup";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nomesup = $row2[0];

        print"<option value='$idtbsup'>$nomesup</option>";

}   


        ?>
      
      </select>
      <input type="submit" class="btn btn-success"  value="Filtrar">
</div>

</form>
</div>
        <div class="card mb-4 mt-4">
            <div class="card-header">
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i>
                    Voltar para Dashboard Inicial 
                </a>
            </div>
             <div class="card-body">
                <!--tabela para entrada de dados-->
                <table id='datatablesSimple' class='table table-bordered' style="font-size: 12px !important;">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Supervisor</th>
                            <th colspan="3" >Média</th>
                            <?php
    $sql = "SELECT distinct(data) FROM bdfrota.tbsaldo where matricula in (SELECT matricula FROM bdfrota.tbusuario where idtbsupervisor = '$idsup') order by data desc LIMIT 4";
    $resultado = mysql_query($sql) or die (mysql_error());
$nsem = 4;
while ($row = mysql_fetch_array($resultado)) {
$dataa = $row[0];
$datas = explode(" ", $dataa);
$datasb = explode("-", $datas[0]);
print"<th colspan='4' >Semana $datasb[2]/$datasb[1]</th>";

$nsem = $nsem - 1;
}
print"</tr>
<tr>
                            <th></th>
                            <th></th>
                            <th>KM Proj.</th>
                            <th>KM Rod.</th>
                            <th>KM Cota Ex.</th>
";

$nsema = 4;

while ($nsema > $nsem) {
    
      print"                            <th>KM Proj.</th>
                            <th>KM OS</th>
                            <th>KM Cota Ex.</th>
                            <th>KM GPS</th>";  
    $nsema = $nsema - 1;
}


                            ?>
                            
                        
                    </thead>
                    <tbody>
<?php

if ($idsup <> '') {
    
    $sql = "SELECT matricula, ROUND(AVG(kmorcsem),2), ROUND(AVG(kmosatual),2), ROUND(AVG(kmproj - kmorcsem),2) FROM bdfrota.tbsaldo where matricula in (SELECT matricula FROM bdfrota.tbusuario where idtbsupervisor = $idsup) group by matricula";
    $resultado = mysql_query($sql) or die (mysql_error());


while ($row = mysql_fetch_array($resultado)) {
        
       $mattec = $row[0];
        $medkmproj = $row[1];
        $medkmos = $row[2];
        $medkmce = $row[3];

        $sql2 = "SELECT nome FROM bdfrota.tbusuario where matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nometec = $row2[0];

        $sql2 = "SELECT b.nome FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b where a.matricula = b.matricula and a.idtbsupervisor = '$idsup'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nomesup = $row2[0];
        
        $sql2 = "SELECT ds_placa FROM bdfrota.tbcarrosxcolaborador where id_colaborador = '$mattec'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $placa = $row2[0];

            print "<tr><td>$nometec</td>
              <td>$nomesup</td>
              <td style='white-space:  nowrap;'>$medkmproj</td>
              <td style='white-space:  nowrap;'>$medkmos</td>
              <td style='white-space:  nowrap;'>$medkmce</td>";

            $sql2 = "SELECT kmorcsem, kmosatual, data, ROUND(kmproj - kmorcsem,2) FROM bdfrota.tbsaldo where matricula = '$mattec' order by data desc LIMIT 4";
    $resultado2 = mysql_query($sql2) or die (mysql_error());


while ($row2 = mysql_fetch_array($resultado2)) {

        $datainigps = $row2[2];
        $datafimgps = date('Y-m-d', strtotime($datainigps ."+6 days"));

        $servidor5="10.17.0.5";
        $usuario5="blvp";
        $senha5="@F3r7n6c2";
        $db5 = "bdfrota";
        $conexao5=mysqli_connect("$servidor5","$usuario5","$senha5","$db5") or die (mysqli_connect_error());

        $sql5 = "SELECT ROUND((max(kmtotal) - min(kmtotal)),2) as kmsem FROM bdfrota.tbgps where placa = '$placa' and datagps >= '$datainigps' and datagps <= '$datafimgps' group by placa";
        $resultado5 = mysqli_query($conexao5, $sql5) or die(mysqli_error($conexao5));
        $row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
        $kmgps = $row5[0];

        print "<td style='white-space:  nowrap;'>$row2[0]</td>
              <td style='white-space:  nowrap;'>$row2[1]</td>
              <td style='white-space:  nowrap;'>$row2[3]</td>
              <td style='white-space:  nowrap;'>$kmgps</td>";


}
 print"</tr>";       
                
    }

} 


?>

                    </tbody>
                </table>
                              <?php /*print' <form id="excel" action="./control/gerarexceldetkms.php"  method="post">
 </form>';              <div class="form-group col-md-2">
                <label for="exampleInputEmail1"><b style="color: white">.</b></label>
                <input type="button" href="javascript:{}" onclick="document.getElementById('excel').submit(); return false;" class="form-control form-control-md btn btn-success" value="GERAR EXCEL">
              </div>*/ ?>

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