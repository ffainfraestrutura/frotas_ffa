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


<form action="./detalhes-kmsem-8.php" method="post">
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
            <select class="form-select mb-3" name="idtbsup" id="selectsupervisor">
                <option value=" "> Selecione o supervisor </option>

            <?php
            $i=0;
            $idselect;

            $sql2 ="SELECT DISTINCT (a.matricula), c.idtbcoordenador, b.nome, a.idtbsupervisor FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b, bdfrota.tbcoord c where a.matricula = b.matricula and a.idtbcoordenador = c.idtbcoordenador;";
             
            $resultado2 = mysql_query($sql2) or die(mysql_error());
            while($row2 = mysql_fetch_array($resultado2)){
                $matsup = $row2[0];
                $idcoord = $row2[1];

                $nomesup = $row2[2];
                $idsupf = $row2[3];

                $addmatriz = array($matsup => $i );
                foreach($addmatriz as $key => $value)
                {
                    $idselect[$key] = $value;
                }
                $i++;
                print"<option id='$idselect[$idcoord]-$idselect[$matsup]' value='$idsupf'>$nomesup </option>";
            }

            ?>

            </select>
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
    $sql = "SELECT distinct(data) FROM bdfrota.aexcluirtbkmsemanalaux where matricula in (SELECT matricula FROM bdfrota.tbusuario where idtbsupervisor = '$idsup') order by data desc LIMIT 8";
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
    
      print"                <th>KM Proj.</th>
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
    
    $sql = "SELECT matricula, ROUND(AVG(kmproj),2), ROUND(AVG(kmrod),2), ROUND(AVG(kmcota),2), ROUND(AVG(kmgps),2) FROM bdfrota.aexcluirtbkmsemanalaux where matricula in ( SELECT matricula FROM bdfrota.tbusuario where idtbsupervisor = $idsup ) group by matricula";
        
    $resultado = mysql_query($sql) or die (mysql_error());


while ($row = mysql_fetch_array($resultado)) {
        
       $mattec = $row[0];
        $medkmproj = $row[1];
        $medkmos = $row[2];
        $medkmce = $row[3];
        $medkmgps = $row[4];

        $sql2 = "SELECT nome FROM bdfrota.tbusuario where matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nometec = $row2[0];

        $sql2 = "SELECT b.nome FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b where a.matricula = b.matricula and a.idtbsupervisor = '$idsup'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nomesup = $row2[0];
        

            print "<tr><td>$nometec</td>
              <td>$nomesup</td>
              <td style='white-space:  nowrap;'>$medkmproj</td>
              <td style='white-space:  nowrap;'>$medkmos</td>
              <td style='white-space:  nowrap;'>$medkmce</td>";

            $sql2 = "SELECT kmproj, kmrod, data, kmcota, kmgps FROM bdfrota.aexcluirtbkmsemanalaux where matricula = '$mattec' order by data desc LIMIT 8";
    $resultado2 = mysql_query($sql2) or die (mysql_error());


while ($row2 = mysql_fetch_array($resultado2)) {


        print "<td style='white-space:  nowrap;'>$row2[0]</td>
              <td style='white-space:  nowrap;'>$row2[1]</td>
              <td style='white-space:  nowrap;'>$row2[3]</td>
              <td style='white-space:  nowrap;'>$row2[4]</td>";


}
 print"</tr>";       
                
    }

} 


?>

                    </tbody>
                </table>
                           
<form action="./control/gerarexcelrelsaldoatual.php" method="post">
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