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

$idcoord = $_POST["idtbcoord"];

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
        <h1 class="h1 mt-2"> Saldo supervisor por período </h1>


<div id="collapseExample4" style="
    width: 33%;
">
<form action="./detalhes-saldo-supxcoord.php" method="post">

<h5><b>Coordenador:</b></h5>
    <div class="input-group">
    <select class="form-select" id="inputGroupSelect04" name="idtbcoord">
        <option value=''>Selecione o coordenador...</option>
        <?php

        $sql = "SELECT distinct(a.idtbcoordenador), b.nome FROM bdfrota.tbcoord  a, bdaniel.tbfuncionario b where a.matricula = b.matricula";
        $resultado = mysql_query($sql) or die (mysql_error());

while ($row = mysql_fetch_array($resultado)) {

        $idtbcoord = $row[0];
        $nomecoord = $row[1];

        print"<option value='$idtbcoord'>$nomecoord</option>";

}   


        ?>
      
      </select>
      <input type="submit" class="btn btn-success"  value="Filtrar">
</div>

</form>
</div>

        <div class="card mt-4 mb-4">
            <div class="card-header">
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i>
                    Voltar para Dashboard Inicial 
                </a href="dashboard.php">
            </div>
             <div class="card-body">
                <!--tabela para entrada de dados-->
                <table id='datatablesSimple' class='table table-bordered'>
                    <thead>
                        <tr>
                            <th>Matricula</th>
                            <th>Nome</th>
                            <th>Centro de Custo</th>
                            <th>Saldo</th>
                            <th>Total Período</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php     
if ($idcoord <> '') {
    $sql = "SELECT matricula, valor, orcrecebido FROM bdfrota.tbcoord where idtbcoordenador = '$idcoord'";
      $resultado = mysql_query($sql) or die (mysql_error());
      $row = mysql_fetch_array($resultado);

        $matriculaf = $row[0];
        $valor = $row[1];
        $total = $row[2];

        $sql2 = "SELECT nome, ccusto FROM bdaniel.tbfuncionario where matricula = '$matriculaf'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nome = $row2[0];
        $ccusto = utf8_encode($row2[1]);

        print "<tr>
                <td>$matriculaf</td>
                <td>$nome</td>
                <td>$ccusto</td>
                <td>R$ $valor</td>
                <td>R$ $total</td>
               </tr>";

        $sql = "SELECT matricula, ROUND(valor, 2), ROUND(totorccomp, 2) FROM bdfrota.tbsupervisor where idtbcoordenador = '$idcoord' order by totorccomp desc";
      $resultado = mysql_query($sql) or die (mysql_error());
      while ($row = mysql_fetch_array($resultado)){
        $matriculaf = $row[0];
        $valor = $row[1];
        $total = $row[2];

        $sql2 = "SELECT nome, ccusto FROM bdaniel.tbfuncionario where matricula = '$matriculaf'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nome = $row2[0];
        $ccusto = utf8_encode($row2[1]);

        print "<tr>
                <td>$matriculaf</td>
                <td>$nome</td>
                <td>$ccusto</td>
                <td>R$ $valor</td>
                <td>R$ $total</td>
               </tr>";
      }
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