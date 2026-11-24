<?php
  setlocale(LC_ALL, 'pt_BR.utf8');
  date_default_timezone_set('America/Sao_Paulo');
  session_start();
  
  $nome = $_SESSION['nome'];
  $usuariof = $_SESSION['usuario'];
  $matricula = $_SESSION['matricula'];
  $perfil = $_SESSION['perfil'];

  $_SESSION['perfil'] = $perfil;
  $_SESSION['matricula'] = $matricula;
  $_SESSION['nome'] = $nome;
  $_SESSION['usuario'] = $usuariof;

  $data = date('Y-m-d');
  $diasemana = strftime('%A',strtotime("$data"));
  // Se hoje for segunda-feira, use a data de hoje
  if ($diasemana == 'segunda') {
    $useg = $data;
  } else {
    $useg = date('Y-m-d', strtotime($data . 'last monday'));
  }

  $dataInicialfi = isset($_POST["datai"]) ? $_POST["datai"] : '';
  $dataFinalfi = isset($_POST["dataf"]) ? $_POST["dataf"] : '';
  $ccustofi = isset($_POST["ccusto"]) ? $_POST["ccusto"] : '';
  $filialfi = isset($_POST["filial"]) ? $_POST["filial"] : '';
  $empresafi = isset($_POST["empresa"]) ? $_POST["empresa"] : '';
  $coordenadorfi = isset($_POST["coordenador"]) ? $_POST["coordenador"] : '';
  $matsupfi = isset($_POST["matsupf"]) ? $_POST["matsupf"] : '';
  $mattecfi = isset($_POST["mattecf"]) ? $_POST["mattecf"] : '';

  if (strftime('%A') == "domingo" ){
    $segunda = date('Y-m-d', strtotime('last monday'));
    $domingo = date('Y-m-d');
  } elseif (strftime('%A') == "segunda") {
    $segunda = date('Y-m-d');
    $domingo = date('Y-m-d', strtotime('next sunday'));
  } else {
    $segunda = date('Y-m-d', strtotime('last monday'));
    $domingo = date('Y-m-d', strtotime('next sunday'));
  }

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
          <h1 class="h1 mt-2"> Relatório Orçamento Semanal dos Supervisor </h1>
        
          <div class="card mt-4 mb-4">
            <div class="card-header">
              <a href="./dashboard.php" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i>
                Voltar para Dashboard
              </a href="./dashboard.php">
            </div>
            
            <div class="card-body">
              <!--tabela para entrada de dados-->
              <table id='datatablesSimple' class='table table-bordered'>
                <thead>
                  <tr>
                    <th>Matricula</th>
                    <th>Nome</th>
                    <th>Centro de Custo</th>
                    <th>Coordenador</th>
                    <th>Orçamento Atual</th>
                    <th>Orçamento Semanal</th>
                    <th>Sld. Remanejamento</th>
                    
                    
                  </tr>
                </thead>
                
                <tbody>
                <?php 
                  $sql = "SELECT s.matricula, s.idtbcoordenador,  ROUND(s.valor, 2) AS valor, ROUND(s.totorccomp, 2) AS totorccomp, f.nome,  f.ccusto FROM  bdfrota.tbsupervisor s JOIN bdaniel.tbfuncionario f ON s.matricula = f.matricula WHERE  ROUND(s.totorccomp, 2) > 5;";
      
                  $resultado = mysql_query($sql) or die (mysql_error());
                  while($row = mysql_fetch_array($resultado)){
                    $matriculasup = $row[0];
                    $idtbcoordenador = $row[1];
                    $valora = $row[2];
                    $totorc = $row[3];
                    $nomesup = $row[4];
                    $ccusto = $row[5];

                    $sql2 = "SELECT  c.matricula,  f.nome FROM  bdfrota.tbcoord c JOIN  bdaniel.tbfuncionario f ON c.matricula = f.matricula WHERE  c.idtbcoordenador = $idtbcoordenador;";
                    $resultado2 = mysql_query($sql2) or die (mysql_error());
                    $row2 = mysql_fetch_array($resultado2);
                    $nomecoord = $row2[1];

                    //remanejamento positivo
                    $sql4 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matr_autor='$matriculasup' AND data_e_hora BETWEEN  '$useg 00:00:00' AND '$data 23:59:59' AND tipo=1;";                          
                    $resultado4 = mysql_query($sql4) or die (mysql_error());
                    $row4 = mysql_fetch_array($resultado4);
                    $remanejpositivo = $row4[0];
                    //echo "Data ini :";
                    //echo $useg;
                    //echo "----Data :";
                    //echo $data;

                    //remanejamento negativo
                    $sql5 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matr_autor='$matriculasup' AND data_e_hora BETWEEN  '$useg 00:00:00' AND '$data 23:59:59' AND tipo=0;";                          
                    $resultado5 = mysql_query($sql5) or die (mysql_error());
                    $row5 = mysql_fetch_array($resultado5);
                    $remanejnegativo = $row5[0];
                    
                    $saldoremanej = $remanejpositivo-$remanejnegativo;

                    //$sql6 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$matriculasup' AND data_e_hora BETWEEN  '$useg 00:00:00' AND '$data 23:59:59' AND tipo=1 AND perfil_autor=4; ";                          
                    //$resultado6 = mysql_query($sql6) or die (mysql_error());
                    //$row6 = mysql_fetch_array($resultado6);
                    //$ffrota = $row6[0];
                    //$ffrota = str_replace(".", ",", $ffrota);
                    
                    print "
                    <tr>
                      <td>$matriculasup</td>
                      <td>$nomesup</td>
                      <td>$ccusto</td>
                      <td>$nomecoord</td>
                      <td>R$ $valora</td>
                      <td>R$ $totorc</td>
                      <td>R$ $saldoremanej</td>
                                            
                    </tr>";
                  }
                ?>
                </tbody>                
              </table>

            </div>
        </div>
    </div>

</main>

<footer class="py-4 bg-light mt-auto" style=" bottom: 0; start: 0; width: 100%;">
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
            if (!idsupervisor.startsWith(idcoordenador)) {
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
        $('#datatablesSimple').DataTable({
          order:[[1,'asc']]
        });
    });
</script>
</html>