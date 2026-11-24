<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();

$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
} elseif ($perfil <> '4'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";  
}

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

$idsup = $_POST["idtbsup"];
$idcoord = $_POST["coordenador"];

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
        <h1 class="h1 mt-2"> Relatório Pedidos Hodômetro</h1>


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
                            <th>Matricula</th>
                            <th>Nome</th>
                            <th>Centro de Custo</th>
                            <th>Placa</th>
                            <th>Data</th>
                            <th>Valor</th>
                            <th>Km</th>
                            <th>Foto</th>
                    </thead>
                    <tbody>
<?php


   $sql = "SELECT t1.matricula, t1.data, t1.dir, t1.kmhodometro, t1.valor, t1.placa
FROM bdfrota.tbpedidostec t1
JOIN (
    SELECT matricula, MAX(idtbpedidostec) as max_id
    FROM bdfrota.tbpedidostec where matricula <> ''
    GROUP BY matricula
) t2 ON t1.matricula = t2.matricula AND t1.idtbpedidostec = t2.max_id
ORDER BY t1.data desc;";
   
  
    $resultado = mysql_query($sql) or die (mysql_error());


while ($row = mysql_fetch_array($resultado)) {
        
       $mattec = $row[0];
       $dataaceite = $row[1];
       $doc = $row[2];
       $kmh = $row[3];
       $valor = $row[4];
       $placassoc = $row[5];

       $sql2 = "SELECT nome, ccusto FROM bdaniel.tbfuncionario where matricula = '$mattec'";
       $resultado2 = mysql_query($sql2) or die (mysql_error());
       $row2 = mysql_fetch_array($resultado2);
        $nometec = utf8_encode($row2[0]);
        $ccusto = utf8_encode($row2[1]);

      if(empty($placassoc)){
        $sql3 = "SELECT placaassoc FROM bdfrota.tbcondutor where dataassoc <= '$dataaceite' AND datadissoc >= '$dataaceite' AND matricula = '$mattec'; ";
        $resultado3 = mysql_query($sql3) or die (mysql_error());
        $row3 = mysql_fetch_array($resultado3);
        $placassoc = $row3[0];       
        
      }


            print "<tr><td>$mattec</td>
              <td>$nometec</td>
              <td>$ccusto</td>
              <td>$placassoc</td>
              <td>$dataaceite</td>
              <td>R$ $valor</td>
              <td>$kmh</td>
              <td><a href='./$doc' target='_blank'>
              <button class='btn btn-link' type='submit'><i class='fa-solid fa-eye'></i></button></a></td>";
               print"</tr>";  


}
          
                
  




?>

                    </tbody>
                </table>
                           
<form action="./control/gerarexcelkmshodometro.php" method="post">
              <?php 
              print "<input type='hidden' name='idtbsup' value='$idsup'>";?>
              <div class="form-group col-md-2">
                <label for="exampleInputEmail1"><b style="color: white">.</b></label>
                <input type="submit" class="form-control form-control-md btn btn-success" value="GERAR EXCEL">
              </div></form>
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

<script>
        
       $("#selectcoordenador").change(function() {

          var selectcoordenador = document.getElementById('selectcoordenador');
          var idcoordenador = selectcoordenador[selectcoordenador.selectedIndex].id;
          var selectsupervisor = document.getElementById('selectsupervisor');
          var selecttecnico = document.getElementById('selecttecnico');
          
          for (var i = 0; i < selectsupervisor.length; i++) {
            var idsupervisor = selectsupervisor.options[i].id;
            if (!idsupervisor.startsWith(idcoordenador + "-")) {
              $(selectsupervisor.options[i]).attr('disabled', 'disabled').hide();
            } else {
              $(selectsupervisor.options[i]).removeAttr('disabled').show();
            }
          }
          
          for (var i = 0; i < selecttecnico.length; i++) {
            var idtecnico = selecttecnico.options[i].id;
            if (!idtecnico.startsWith(idcoordenador)) {
              $(selecttecnico.options[i]).attr('disabled', 'disabled').hide();
            } else {
              $(selecttecnico.options[i]).removeAttr('disabled').show();
            }
          }
        });

         $("#selectsupervisor").change(function() {
          var selectcoordenador = document.getElementById('selectcoordenador');
          var idcoordenador = selectcoordenador[selectcoordenador.selectedIndex].id;
          var selectsupervisor = document.getElementById('selectsupervisor');
          var idsupervisor = selectsupervisor[selectsupervisor.selectedIndex].id;
          var selecttecnico = document.getElementById('selecttecnico');
          for (var i = 0; i < selecttecnico.length; i++) {
            var idtecnico = selecttecnico.options[i].id;
            if (!idtecnico.startsWith(idsupervisor)) {
              $(selecttecnico.options[i]).attr('disabled', 'disabled').hide();
            } else {
              $(selecttecnico.options[i]).removeAttr('disabled').show();
            }
          }
          const id = idsupervisor.split('-');
          const [coordenador, supervisor] = id;
          for (var i = 0; i < selectcoordenador.length; i++) {
            if (selectcoordenador[i].id == coordenador) {
              selectcoordenador[i].selected = true;
            }else{
              selectcoordenador[i].selected = false;
            }
          }
        });

        $("#selecttecnico").change(function() {
          var selectcoordenador = document.getElementById('selectcoordenador');
          var idcoordenador = selectcoordenador[selectcoordenador.selectedIndex].id;
          var selectsupervisor = document.getElementById('selectsupervisor');
          var idsupervisor = selectsupervisor[selectsupervisor.selectedIndex].id;
          var selecttecnico = document.getElementById('selecttecnico');
          var idtecnico = selecttecnico[selecttecnico.selectedIndex].id;
          const id = idtecnico.split('-');
          const [coordenador, supervisor, tecnico] = id;
          for (var i = 0; i < selectcoordenador.length; i++) {
            if (selectcoordenador[i].id == coordenador) {
              selectcoordenador[i].selected = true;
            }else{
              selectcoordenador[i].selected = false;
            }
          }
          for (var i = 0; i < selectsupervisor.length; i++) {
            if (selectsupervisor[i].id == coordenador+'-'+supervisor) {
              selectsupervisor[i].selected = true;
            }else{
              selectsupervisor[i].selected = false;
            }
          }
        });
        </script>

<!--script para criar data table-->
<script>
    //função iniciar data table 
    $(document).ready( function () {
        $('#datatablesSimple').DataTable();
    });
</script>
</html>