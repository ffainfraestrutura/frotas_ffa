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

$diaf = $_POST["diaf"];
$diai = $_POST["diai"];
$matsup = $_POST["matsupf"];
$tiporelat = $_POST["tiporelat"];
$mattec = $_POST["mattecf"];

$data = date('Y-m-d');
//$data = date('2024-04-05');

if (strftime('%A') == "segunda") {
  $ultseg = $data;
} else {
  $ultseg = date('Y-m-d', strtotime($data . 'last monday'));
}
//$penultseg = date('Y-m-d', strtotime($ultseg . 'last monday'));

/*if (strftime('%A') == "domingo" ){
  $segunda = date('Y-m-d', strtotime('last monday'));
  $domingo = date('Y-m-d');
} elseif (strftime('%A') == "segunda") {
  $segunda = date('Y-m-d');
  $domingo = date('Y-m-d', strtotime('next sunday'));
} else {
    $segunda = date('Y-m-d', strtotime('last monday'));
    $domingo = date('Y-m-d', strtotime('next sunday'));
}*/

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
    
    <link href="src/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!--datatable-->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.1/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.print.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.1/css/buttons.dataTables.css">
  </head>

  <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
    <!--nav-->
    <?php include"menu.php"; ?>

    <div id="layoutSidenav_content">

      <main>
        <div class="container-fluid px-4">
          <h1 class="h1 mt-2"> Relatório Saldo Semanal </h1>
          
          <div id="collapseExample4">
            <div class="card mt-4 mb-4">
              <div class="card-header">
                  <a href="./dashboard.php" class="btn btn-secondary">
                      <i class="fa-solid fa-arrow-left"></i>
                      Voltar para Dashboard
                  </a href="./dashboard.php">
              </div>
            
              <div class="card-body">
                <!--tabela para entrada de dados-->
                <table id='example' class='table table-bordered'>
                  <thead>
                    <tr>
                      <th>Matricula</th>
                      <th>Nome</th>
                      <th>Placa</th>                      
                      <th>KM Proj.</th>
                      <th>KM OS</th>
                      <th>KM GPS</th>
                      <th>Orc. Semanal</th>
                      <th>Sld. Remanej.</th>
                      <th>Fd. Frota</th>
                      <th>Tot. Extra </th>
                    </tr>
                  </thead>
                  
                  <tbody>
                    <?php 
                      $sql = "SELECT idtbsupervisor FROM bdfrota.tbsupervisor where matricula = '$matricula'";
                      $resultado = mysql_query($sql) or die (mysql_error());
                      $row = mysql_fetch_array($resultado);
                      $idtbsupervisor = $row[0];

                      $sql = "SELECT a.matricula, b.nome, b.codempresa, b.status FROM bdfrota.tbusuario a, bdaniel.tbfuncionario b where a.matricula = b.matricula and a.idtbsupervisor = $idtbsupervisor AND b.status<>'Demitido';" ;
                  
                      $resultado = mysql_query($sql) or die (mysql_error());
                      while($row = mysql_fetch_array($resultado)){
                        $matriculatec = $row[0];
                        $nometec = $row[1];
                        $codempresa = $row[2];
                        $status = $row[3];

                        $sql2 = "SELECT ROUND(saldo,2) as saldo, idtbsaldo, orcsemanal, kmproj, kmosatual, (valoraplicado - orcsemanal) as totalextra, slddscnt FROM bdfrota.tbsaldo where matricula = '$matriculatec' AND data= '$ultseg'; ";

                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        $row2 = mysql_fetch_array($resultado2);
                        $saldotec = $row2[0];
                        $saldotec = str_replace(".", ",", $saldotec);
                        $idtbsaldo = $row2[1];
                        
                        $orcsemanal = round($row2[2],2);
                        $orcsemanal = str_replace(".", ",", $orcsemanal);
                        
                        $kmproj = $row2[3];
                        $kmproj = round($kmproj, 2);
                        $kmosatual = $row2[4];
                        $totalextra = $row2[5];
                        $valdesc = $row2[6];

                        $sql3 = "SELECT placa FROM bdfrota.tbveiculo where matcond = '$matriculatec'";
                        $resultado3 = mysql_query($sql3) or die (mysql_error());
                        $row3 = mysql_fetch_array($resultado3);
                        $placa = $row3[0];

                        if(!empty($placa)){
                          //remanejamento positivo
                          $sql4 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$matriculatec' AND data_e_hora BETWEEN  '$ultseg 00:00:00' AND '$data 23:59:59' AND tipo=1; ";                          
                          $resultado4 = mysql_query($sql4) or die (mysql_error());
                          $row4 = mysql_fetch_array($resultado4);
                          $remanejpositivo = $row4[0];
                          
                          //remanejamento negativo
                          $sql5 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$matriculatec' AND data_e_hora BETWEEN  '$ultseg 00:00:00' AND '$data 23:59:59' AND tipo=0; ";                          
                          $resultado5 = mysql_query($sql5) or die (mysql_error());
                          $row5 = mysql_fetch_array($resultado5);
                          $remanejnegativo = $row5[0];
                          
                          $saldoremanej = $remanejpositivo-$remanejnegativo;

                          //saldo fundo frota
                          $sql6 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$matriculatec' AND data_e_hora BETWEEN  '$ultseg 00:00:00' AND '$data 23:59:59' AND tipo=1 AND perfil_autor=4; ";                          
                          $resultado6 = mysql_query($sql6) or die (mysql_error());
                          $row6 = mysql_fetch_array($resultado6);
                          $ffrota = $row6[0];
                          $ffrota = str_replace(".", ",", $ffrota);

                          
                          
                          print "
                            <tr>
                              <td>$matriculatec</td>
                              <td style=\"white-space: nowrap;\">$nometec</td>
                              <td>$placa</td>                              
                              <td>$kmproj km</td>
                              <td>$kmosatual km</td>
                              <td>$kmsem km</td>
                              <td>R$ $orcsemanal</td>
                              <td>R$ $saldoremanej</td>
                              <td>R$ $ffrota</td>
                              <td>R$ 0</td>
                            </tr>";
                        }
                        
                      }
                      
                    ?>
                      
                  </tbody>
                </table>
              
                <!--<form action="./control/gerarexcelrelsaldoatual.php" method="post">
                  <?php 
                    print "<input type='hidden' name='matsupf' value='$matricula'>";
                    print "<input type='hidden' name='tiporelat' value='sup'>";
                    print "<input type='hidden' name='mattecf' value='$mattec'>";
                    print "<input type='hidden' name='idtbsupervisor' value='$idtbsupervisor'>";
                  ?>
                  
                  <div class="form-group col-md-2">
                    <label for="exampleInputEmail1"><b style="color: white">.</b></label>
                    <input type="submit" class="form-control form-control-md btn btn-success" value="GERAR EXCEL">
                  </div>
                </form>-->
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
    

    new DataTable('#example', {
      order: [[1, 'asc']],
      layout: {
        topStart: {
          //buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
          buttons: [{
                    extend: 'excelHtml5',
                    title: 'Relatório Saldo Semanal'
                },]
        }
      }
    });
  </script>
</html>