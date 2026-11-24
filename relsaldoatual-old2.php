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
        <h1 class="h1 mt-2"> Relatório Saldo Semanal </h1>


<form action="./relsaldoatual.php" method="post">
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
                <option value=" "> Selecione o supervisor </option>

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

<div class='d-flex'>
    <div class="mt-2" id="collapseExample4" style="width: 28%;">
        

        <h5><b>Tecnico:</b></h5>
            <div class="input-group">
            <select class="form-select mb-3" name="mattecf" id="selecttecnico">
                <option value=" "> Selecione o técnico </option>

            <?php
            $i=0;

            $sql3 ="SELECT DISTINCT (a.matricula), b.nome, c.matricula, d.idtbcoordenador FROM bdfrota.tbusuario a, bdaniel.tbfuncionario b, bdfrota.tbsupervisor c, bdfrota.tbcoord d where a.matricula = b.matricula AND a.idtbsupervisor = c.idtbsupervisor AND c.idtbcoordenador = d.idtbcoordenador;";
             
            $resultado3 = mysql_query($sql3) or die(mysql_error());
            while($row3 = mysql_fetch_array($resultado3)){
                $mattecf = $row3[0];
                $nometec = $row3[1];
                $matsupf = $row3[2];
                $idcoord = $row3[3];

                print"<option id='$idselect[$idcoord]-$idselect[$matsupf]-$i' value='$mattecf'>$nometec </option>";
            }
            $i++;

            ?>

            </select>
            </div>
        
    </div>

    <!-- tipo de relatório-->
    <div class="ms-5 mt-2">
        <h5 class="mb-3"><b>Tipo de Relatório:</b></h5>
        <!-- <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="coord" name="tiporelat" value="coord">
            <label class="form-check-label" for="coord">Coordenador</label>
        </div>-->
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="super" name="tiporelat" value="sup">
            <label class="form-check-label" for="super">Supervisor</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="tecn" name="tiporelat" value="tec">
            <label class="form-check-label" for="tecn">Técnico</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="tecn" name="tiporelat" value="todos">
            <label class="form-check-label" for="todos">Todos</label>
        </div>
    </div>
    
</div>

<div class='mt-2' style="width: 50%;">
   

        <div class="mt-4">
            <input type="submit" class="btn btn-success"  value="Filtrar">
        </div>


        <!--</form>
    </div>-->
</div>
      </form>      

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
                            <th>Placa</th>
                            <th>Orc. Semanal</th>
                            <th>KM Proj.</th>
                            <th>KM OS</th>
                            <th>Tot. Extra</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php     
if ($tiporelat == '') {
    
print "<tr>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
               </tr>";


} elseif ($tiporelat == 'sup') {
   
 $sql = "SELECT idtbsupervisor FROM bdfrota.tbsupervisor where matricula = '$matsup'";
    $resultado = mysql_query($sql) or die (mysql_error());
    $row = mysql_fetch_array($resultado);
    $idtbsupervisor = $row[0];

    $sql = "SELECT a.matricula, b.nome, b.codempresa, b.ccusto FROM bdfrota.tbusuario a, bdaniel.tbfuncionario b where a.matricula = b.matricula and a.idtbsupervisor = $idtbsupervisor";
                    
                    $resultado = mysql_query($sql) or die (mysql_error());
                    while($row = mysql_fetch_array($resultado)){
                        $matriculatec = $row[0];
                        $nometec = $row[1];
                        $codempresa = $row[2];
                        $ccusto = utf8_encode($row[3]);

                        $sql2 = "SELECT ROUND(saldo,2) as saldo, idtbsaldo, ROUND(orcsemanal,2), ROUND(kmorcsem,0), ROUND(kmosatual,0), ROUND((valoraplicado - orcsemanal),2) as totalextra, slddscnt FROM bdfrota.tbsaldo where matricula = '$matriculatec' order by data desc";
                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        if (mysql_num_rows($resultado2)>=1){
                        $row2 = mysql_fetch_array($resultado2);
                        $saldotec = $row2[0];
                        $saldotec = str_replace(".", ",", $saldotec);
                        $idtbsaldo = $row2[1];
                        $orcsemanal = $row2[2];
                        $orcsemanal = str_replace(".", ",", $orcsemanal);
                        $kmproj = $row2[3];
                        $kmosatual = $row2[4];
                        $valdesc = $row2[6];

                        $sql2 = "SELECT ROUND(SUM(valorinserido),2) FROM bdfrota.tbcotaextraac where data >= '$segunda 00:00:00'  and matricula = '$matriculatec'";

                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        $row2 = mysql_fetch_array($resultado2);
                        $totalextra = $row2[0];
                        if ($totalextra == '') {
                            $totalextra = 0;
                        }

                        $sql2 = "SELECT ds_placa FROM bdfrota.tbcarrosxcolaborador where id_colaborador = '$matriculatec'";
                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        $row2 = mysql_fetch_array($resultado2);
                        $placa = $row2[0];
if (mysql_num_rows($resultado2)>=1){
/*$servidor5="10.17.0.5";
$usuario5="blvp";
$senha5="@F3r7n6c2";
$db5 = "bdfrota";
$conexao5=mysqli_connect("$servidor5","$usuario5","$senha5","$db5") or die (mysqli_connect_error());

$sql5 = "SELECT ROUND((max(kmtotal) - min(kmtotal)),2) as kmsem FROM bdfrota.tbgps where placa = '$placa' and datagps >= '$segunda' group by placa";

$resultado5 = mysqli_query($conexao5, $sql5) or die(mysqli_error($conexao5));
$row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
$kmsem = $row5[0];
if ($kmsem > 2500) {
    $kmsem = 0;
}*/

        print "<tr>
                            <td>$matriculatec</td>
                            <td style=\"white-space: nowrap;\">$nometec</td>
                            <td>$ccusto</td>
                            <td>$placa</td>
                            <td>R$ $orcsemanal</td>
                            <td>$kmproj</td>
                            <td>$kmosatual</td>
                            <td>R$ $totalextra</td>
               </tr>";

        }
}

      }

} elseif ($tiporelat == 'todos') {
   
 $sql = "SELECT idtbsupervisor FROM bdfrota.tbsupervisor where matricula = '$matsup'";
    $resultado = mysql_query($sql) or die (mysql_error());
    $row = mysql_fetch_array($resultado);
    $idtbsupervisor = $row[0];

    $sql = "SELECT a.matricula, b.nome, b.codempresa, b.ccusto FROM bdfrota.tbusuario a, bdaniel.tbfuncionario b where a.matricula = b.matricula and a.idtbsupervisor <> '0' and a.idtbsupervisor is not null";
                    
                    $resultado = mysql_query($sql) or die (mysql_error());
                    while($row = mysql_fetch_array($resultado)){
                        $matriculatec = $row[0];
                        $nometec = $row[1];
                        $codempresa = $row[2];
                        $ccusto = utf8_encode($row[3]);

                        $sql2 = "SELECT ROUND(saldo,2) as saldo, idtbsaldo, ROUND(orcsemanal,2), ROUND(kmorcsem,0), ROUND(kmosatual,0), ROUND((valoraplicado - orcsemanal),2) as totalextra, slddscnt FROM bdfrota.tbsaldo where matricula = '$matriculatec' order by data desc";
                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        if (mysql_num_rows($resultado2)>=1){
                        $row2 = mysql_fetch_array($resultado2);
                        $saldotec = $row2[0];
                        $saldotec = str_replace(".", ",", $saldotec);
                        $idtbsaldo = $row2[1];
                        $orcsemanal = $row2[2];
                        $orcsemanal = str_replace(".", ",", $orcsemanal);
                        $kmproj = $row2[3];
                        $kmosatual = $row2[4];
                        $valdesc = $row2[6];

                         $sql2 = "SELECT ROUND(SUM(valorinserido),2) FROM bdfrota.tbcotaextraac where data >= '$segunda 00:00:00'  and matricula = '$matriculatec'";
                         
                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        $row2 = mysql_fetch_array($resultado2);
                        $totalextra = $row2[0];
                        if ($totalextra == '') {
                            $totalextra = 0;
                        }


                        $sql2 = "SELECT ds_placa FROM bdfrota.tbcarrosxcolaborador where id_colaborador = '$matriculatec'";
                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        $row2 = mysql_fetch_array($resultado2);
                        $placa = $row2[0];
if (mysql_num_rows($resultado2)>=1){
/*$servidor5="10.17.0.5";
$usuario5="blvp";
$senha5="@F3r7n6c2";
$db5 = "bdfrota";
$conexao5=mysqli_connect("$servidor5","$usuario5","$senha5","$db5") or die (mysqli_connect_error());

$sql5 = "SELECT ROUND((max(kmtotal) - min(kmtotal)),2) as kmsem FROM bdfrota.tbgps where placa = '$placa' and datagps >= '$segunda' group by placa";

$resultado5 = mysqli_query($conexao5, $sql5) or die(mysqli_error($conexao5));
$row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
$kmsem = $row5[0];
if ($kmsem > 2500) {
    $kmsem = 0;
}*/

        print "<tr>
                            <td>$matriculatec</td>
                            <td style=\"white-space: nowrap;\">$nometec</td>
                            <td>$ccusto</td>
                            <td>$placa</td>
                            <td>R$ $orcsemanal</td>
                            <td>$kmproj</td>
                            <td>$kmosatual</td>
                            <td>R$ $totalextra</td>
               </tr>";

        }}


      }

} elseif ($tiporelat == 'tec') {

   


    $sql = "SELECT a.matricula, b.nome, b.codempresa, b.ccusto FROM bdfrota.tbusuario a, bdaniel.tbfuncionario b where a.matricula = b.matricula and a.matricula = '$mattec'";
                    
                    $resultado = mysql_query($sql) or die (mysql_error());
                    while($row = mysql_fetch_array($resultado)){
                        $matriculatec = $row[0];
                        $nometec = $row[1];
                        $codempresa = $row[2];
                        $ccusto = utf8_encode($row[3]);

                        $sql2 = "SELECT ROUND(saldo,2) as saldo, idtbsaldo, orcsemanal, ROUND(kmorcsem,0), ROUND(kmosatual,0), totalextra, slddscnt FROM bdfrota.tbsaldo where matricula = '$mattec' order by data desc";
                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        if (mysql_num_rows($resultado2)>=1){
                        $row2 = mysql_fetch_array($resultado2);
                        $saldotec = $row2[0];
                        $saldotec = str_replace(".", ",", $saldotec);
                        $idtbsaldo = $row2[1];
                        $orcsemanal = $row2[2];
                        $orcsemanal = str_replace(".", ",", $orcsemanal);
                        $kmproj = $row2[3];
                        $kmosatual = $row2[4];
                        $valdesc = $row2[6];

                         $sql2 = "SELECT ROUND(SUM(valorinserido),2) FROM bdfrota.tbcotaextraac where data >= '$segunda 00:00:00'  and matricula = '$matriculatec'";

                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        $row2 = mysql_fetch_array($resultado2);
                        $totalextra = $row2[0];
                        if ($totalextra == '') {
                            $totalextra = 0;
                        }


                        $sql2 = "SELECT ds_placa FROM bdfrota.tbcarrosxcolaborador where id_colaborador = '$mattec'";
                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        $row2 = mysql_fetch_array($resultado2);
                        $placa = $row2[0];



        print "<tr>
                            <td>$matriculatec</td>
                            <td style=\"white-space: nowrap;\">$nometec</td>
                            <td>$ccusto</td>
                            <td>$placa</td>
                            <td>R$ $orcsemanal</td>
                            <td>$kmproj</td>
                            <td>$kmosatual</td>
                            <td>R$ $totalextra</td>
               </tr>";

        
}

      }


}
      ?>
                        
                    </tbody>
                </table>
                <form action="./control/gerarexcelrelsaldoatual.php" method="post">
              <?php 
              print "<input type='hidden' name='matsupf' value='$matsup'>";
              print "<input type='hidden' name='tiporelat' value='$tiporelat'>";
              print "<input type='hidden' name='mattecf' value='$mattec'>";
              ?>
              <div class="form-group col-md-2">
                <label for="exampleInputEmail1"><b style="color: white">.</b></label>
                <input type="submit" class="form-control form-control-md btn btn-success" value="GERAR EXCEL">
              </div></form>
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
        $('#datatablesSimple').DataTable();
    });
</script>
</html>