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

$matsup = $_POST["matsupf"];


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
        <h1 class="h1 mt-2"> Relatório Deslocamento Fora da Escala </h1>



<form action="./gpsxfolga-dia.php" method="post">
<div class='d-flex'>
    <div id="collapseExample4">

        <h5><b>Coordenador:</b></h5>
            <div class="input-group">
            <select class="form-select mb-3" name="coordenador" id="selectcoordenador">
                <option value=" "> Selecione o coordenador </option>

            <?php
            $i=0;

            $sql = "SELECT DISTINCT (a.idtbcoordenador), b.nome FROM bdfrota.tbcoord a, bdaniel.tbfuncionario b where a.matricula = b.matricula;";
            $resultado = mysql_query($sql) or die(mysql_error());
            while($row = mysql_fetch_array($resultado)){
                $idcoord = $row[0];
                $nomecoord = $row[1];

                $addmatriz = array($idcoord => $i );
                foreach($addmatriz as $key => $value)
                {
                    $idselect[$key] = $value;
                }
                $i++;
                print"<option id='$idselect[$idcoord]' value='$idcoord'>$nomecoord</option>";
            }

            ?>

            </select>
            </div>
        
    </div>

    <!-- tipo de relatório-->
    <div class="ms-5">
        <h5><b>Supervisor:</b></h5>
            <div class="input-group">
            <select class="form-select mb-3" name="matsupf" id="selectsupervisor">
                <option value="ss"> Selecione o supervisor </option>

            <?php
            $i=0;
            $idselect;

            $sql2 ="SELECT DISTINCT (a.matricula), c.idtbcoordenador, b.nome FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b, bdfrota.tbcoord c where a.matricula = b.matricula and a.idtbcoordenador = c.idtbcoordenador;";
             
            $resultado2 = mysql_query($sql2) or die(mysql_error());
            while($row2 = mysql_fetch_array($resultado2)){
                $matsupf = $row2[0];
                $idcoord = $row2[1];
                $nomesup = $row2[2];

                $addmatriz = array($matsup => $i );
                foreach($addmatriz as $key => $value)
                {
                    $idselect[$key] = $value;
                }
                $i++;
                print"<option id='$idselect[$idcoord]-$idselect[$matsupf]' value='$matsupf'>$nomesup</option>";
            }

            ?>

            </select>
            </div>
    </div>
    
</div>
<div class="row" style="width: 100%;">     
<h5><b>Selecione o periodo:</b></h5>
     <div class="col-md-3">
      <label for="exampleInputEmail1"><h5>Início:</h5></label>
      <input min="2023-03-17" type="date" class="form-control form-control-md" name="diai">
    </div>

    <div class="col-md-3">
      <label for="exampleInputEmail1"><h5>Fim:</h5></label>
      <input min="2023-03-17" type="date" class="form-control form-control-md" name="diaf">
    </div></div>   

<div class='mt-2' style="width: 50%;">
   

        <div class="mt-4">
            <input type="submit" class="btn btn-success"  value="Filtrar">
        </div>


        <!--</form>
    </div>-->
</div>
      </form>  



<div class="mt-4"><h5 class="text-danger"><b><?php if ($diaf <> '') {
    $dataf = explode("-", $diaf);
    $datat = $dataf[2]."/".$dataf[1]."/".$dataf[0];
    $datai = explode("-", $diai);
    $datati = $datai[2]."/".$datai[1]."/".$datai[0];
    print"Dados dos dias $datati até $datat";

} ?></b></h5></div>
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
                            <th>Filial</th>
                            <th>Nome</th>
                            <th>Centro de Custo</th>
                            <th>Placa</th>
                            <th>Dia</th>
                            <th>KM Período</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php     
if ($diaf <> '') {
 
$timestampInicio = strtotime($diai);
$timestampFim = strtotime($diaf);
$diferencaSegundos = $timestampFim - $timestampInicio;
$diferencaDias = $diferencaSegundos / 86400; 

if ($matsup == 'ss') {

    $sql = "
  SELECT matricula, placa, ROUND(MAX(kmtotal) - MIN(kmtotal), 2) as kmtotal, DATE(data) as dia
  FROM bdfrota.tbrelatoriogpsfalta
  WHERE data > '$diai 00:00:00' AND data < '$diaf 23:59:59' and kmtotal <> 0
  GROUP BY matricula, dia
  HAVING kmtotal > 5
  order by dia

";
    
} else {
     
    $sqls = "SELECT idtbsupervisor FROM bdfrota.tbsupervisor where matricula = '$matsup'";
    $resultados = mysql_query($sqls) or die (mysql_error());
    $rows = mysql_fetch_array($resultados);
    $idtbsupervisor = $rows[0];

    $sql = "
  SELECT a.matricula, a.placa, ROUND(MAX(a.kmtotal) - MIN(a.kmtotal), 2) as kmtotal, DATE(a.data) as dia
  FROM bdfrota.tbrelatoriogpsfalta a, bdfrota.tbusuario b
  WHERE a.matricula = b.matricula
    AND b.idtbsupervisor = '$idtbsupervisor'
    AND a.data > '$diai 00:00:00'
    AND a.data < '$diaf 23:39:59' and kmtotal <> 0
  GROUP BY matricula, dia
  HAVING kmtotal > 5
  order by dia";

    


}

      $resultado = mysql_query($sql) or die (mysql_error());
      while ($row = mysql_fetch_array($resultado)){

        $matriculaf = $row[0];
        $placa = $row[1];
        $kmtotal = $row[2];
        $dia = $row[3];


        if ($kmtotal < 3500) {
        
        

        $sql2 = "SELECT nome, codempresa, ccusto FROM bdaniel.tbfuncionario where matricula = '$matriculaf'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nome = $row2[0];
        if ($row2[1] == '2') {
            $filial = 'SP';
        } else {
            $filial = 'RJ';
        }
        $ccusto = utf8_encode($row2[2]);

        print "<tr>
                <td>$matriculaf</td>
                <td>$filial</td>
                <td>$nome</td>
                <td>$ccusto</td>
                <td>$placa</td>
                <td>$dia</td>
                <td>$kmtotal</td>";

        }


      }


} else {

        print "<tr>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
               </tr>";
}
       
      ?>
                        
                    </tbody>
                </table>
                <form action="./control/gerarexcelgpsxfolga-dia.php" method="post">
              <?php 
              print "<input type='hidden' name='diai' value='$diai'>";
              print "<input type='hidden' name='diaf' value='$diaf'>";
              print "<input type='hidden' name='matsupf' value='$matsup'>"; ?>
              <div class="form-group col-md-2">
                <label for="exampleInputEmail1"><b style="color: white">.</b></label>
                <input type="submit" class="form-control form-control-md btn btn-success" value="GERAR EXCEL">
              </div></form>
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
        $('#datatablesSimple').DataTable();
    });
</script>
</html>