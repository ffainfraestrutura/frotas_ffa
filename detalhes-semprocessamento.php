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
include"conecta05.php";
include_once"./func/log.php";
$datap = $_POST["data"];
if ($datap == '') {
    $datap = date('Y-m-d');
}
$datahora = date("Y-m-d H:i:s");
$hoje = date("Y-m-d");
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
        <h1 class="h1 mt-2"> Relatório Sem Processamento</h1>


<div id="collapseExample4" style="
    width: 33%;
">
<form action="./detalhes-semprocessamento.php" method="post">
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
                 KM OS
            </div>
             <div class='card-body'>
                <!--tabela para entrada de dados-->
                <table id='datatablesSimple' class='table table-bordered'>
                    <thead>
                        <tr>
                            <th>Matricula</th>
                            <th>Nome</th>
                            <th>Centro de Custo</th>
                            <th>Última Att</th>
                        </tr>
                    </thead>
                    <tbody>";
                          


        $sql = "SELECT matricula FROM bdaniel.tbpresenca where data = '$hoje' and situacau = 'presente' and matricula in (SELECT matricula FROM bdfrota.tbusuario where idtbsupervisor <> '' and idtbsupervisor <> 0 and perfil = 0)
";
      $resultado = mysql_query($sql) or die (mysql_error());
      
              
      while ($row = mysql_fetch_array($resultado)){

        $mattec = $row[0];

      $sql2 = "SELECT * FROM bdfrota.tbveiculo where matcond = '$mattec'";
      $resultado2 = mysql_query($sql2) or die (mysql_error());
      if (mysql_num_rows($resultado2) >= 1) {

      $sql2 = "SELECT nome, ccusto FROM bdaniel.tbfuncionario where matricula = '$mattec'";
      $resultado2 = mysql_query($sql2) or die (mysql_error());
      $row2 = mysql_fetch_array($resultado2);
      $nome = utf8_encode($row2[0]);
      $ccusto = utf8_encode($row2[1]);

$sql2 = "SELECT * FROM bdfrota.tbtbultimaos WHERE matricula = '$mattec'";
$resultado2 = mysqli_query($conexao05, $sql2) or die(mysqli_error($conexao05));
      if (mysqli_num_rows($resultado2) <= 0) {
        
        $sql2 = "INSERT INTO bdfrota.tbtbultimaos (matricula, ultimaatt) VALUES ('$mattec','$hoje 00:00:00')";
        $resultado2 = mysqli_query($conexao05, $sql2) or die(mysqli_error($conexao05));
           

      }
$sql2 = "SELECT * FROM bdfrota.tbtbultimaos WHERE matricula = '$mattec' AND ultimaatt < NOW() - INTERVAL 5 HOUR";
$resultado2 = mysqli_query($conexao05, $sql2) or die(mysqli_error($conexao05));
if (mysqli_num_rows($resultado2) >= 1) {
$row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);       
$ultatt = $row2['ultimaatt'];
print "<tr>
                <td>$mattec</td>
                <td>$nome</td>
                <td>$ccusto</td>
                <td>$ultatt</td>
               </tr>";        

}}
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