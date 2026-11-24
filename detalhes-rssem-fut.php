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

$idsup = $_POST["idtbsup"];
$idcoord = $_POST["coordenador"];

$hoje = new DateTime();

// Se hoje for segunda-feira, use a data de hoje
if ($hoje->format('N') == 1) {
    $dataUltimaSegunda = $hoje->format('Y-m-d');
} else {
    // Caso contrário, subtrair o número de dias desde a última segunda-feira
    $diasParaSubtrair = $hoje->format('N') - 1;
    $dataUltimaSegunda = $hoje->sub(new DateInterval('P' . $diasParaSubtrair . 'D'))->format('Y-m-d');
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
        <h1 class="h1 mt-2"> Gastos Semanais </h1>


<form action="./detalhes-rssem-fut.php" method="post">
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

            $sql2 ="SELECT DISTINCT (a.matricula), c.idtbcoordenador, b.nome, a.idtbsupervisor, b.ccusto FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b, bdfrota.tbcoord c where a.matricula = b.matricula and a.idtbcoordenador = c.idtbcoordenador;";
             
            $resultado2 = mysql_query($sql2) or die(mysql_error());
            while($row2 = mysql_fetch_array($resultado2)){
                $matsup = $row2[0];
                $idcoord = $row2[1];

                $nomesup = $row2[2];
                $idsupf = $row2[3];

                $ccusto= $row2[4];

                $addmatriz = array($matsup => $i );
                foreach($addmatriz as $key => $value)
                {
                    $idselect[$key] = $value;
                }
                $i++;
                print"<option id='$idselect[$idcoord]-$idselect[$matsup]' value='$idsupf'>$nomesup </option>";
            }

            ?>
<option value="td"> TODOS </option>
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
                            <th>Centro de Custo</th>
                            <th colspan="2" >Semana Atual</th>
                            <?php
                            if ($idsup == 'td' or $idcoord == 'td') {
                                
    $sql = "SELECT distinct(data) FROM bdfrota.`tbrssemanal-novo` order by data desc LIMIT 8";
    $resultado = mysql_query($sql) or die (mysql_error());
$nsem = 4;
while ($row = mysql_fetch_array($resultado)) {
$dataa = $row[0];
$datas = explode(" ", $dataa);
$datasb = explode("-", $datas[0]);
print"<th colspan='1' >Semana $datasb[2]/$datasb[1]</th>";

$nsem = $nsem - 1;
}
print"</tr>
<tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>Orçamento Semanal</th>
                            <th>KM Projetado</th>
";

$nsema = 4;

while ($nsema > $nsem) {
    
      print"                
                            <th>R$ OS</th>
                            ";  
    $nsema = $nsema - 1;
}


                            } else {

    $sql = "SELECT distinct(data) FROM bdfrota.tbrssemanal where matricula in (SELECT matricula FROM bdfrota.tbusuario where idtbsupervisor = '$idsup') order by data desc LIMIT 8";
    $resultado = mysql_query($sql) or die (mysql_error());
$nsem = 4;
while ($row = mysql_fetch_array($resultado)) {
$dataa = $row[0];
$datas = explode(" ", $dataa);
$datasb = explode("-", $datas[0]);
print"<th colspan='1' >Semana $datasb[2]/$datasb[1]</th>";

$nsem = $nsem - 1;
}
print"</tr>
<tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>Orçamento Semanal</th>
                            <th>KM Projetado</th>
";

$nsema = 4;

while ($nsema > $nsem) {
    
      print"                
                            <th>R$ OS</th>";  
    $nsema = $nsema - 1;
}


                            }
                            ?>
                            
                        
                    </thead>
                    <tbody>
<?php


    
if ($idsup == 'td' or $idcoord == 'td') {

   $sql = "SELECT matricula, ROUND(orcsemanal,2), ROUND(kmorcsem,2), ROUND(slddscnt,2) FROM bdfrota.tbsaldofut";
  
    $resultado = mysql_query($sql) or die (mysql_error());


while ($row = mysql_fetch_array($resultado)) {
        
        $mattec = $row[0];
        $orcsem = $row[1];
        $kmproj = $row[2];
        $valdesc = $row[3];

        $sql2 = "SELECT nome, ccusto FROM bdaniel.tbfuncionario where matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nometec = $row2[0];
        $ccusto = utf8_encode($row2[1]);

        $sql2 = "SELECT idtbsupervisor FROM bdfrota.tbusuario where matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $idtbsupa = $row2[0];

        $sql2 = "SELECT b.nome FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b where a.matricula = b.matricula and a.idtbsupervisor = '$idtbsupa'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nomesup = $row2[0];
        

            print "<tr><td>$nometec</td>
              <td>$nomesup</td>
              <td>$ccusto</td>
              <td style='white-space:  nowrap;'>$orcsem</td>
              <td style='white-space:  nowrap;'>$kmproj</td>
              ";

            $sql2 = "SELECT data FROM bdfrota.`tbrssemanal-novo` group by data order by data desc";
    $resultado2 = mysql_query($sql2) or die (mysql_error());


while ($row2 = mysql_fetch_array($resultado2)) {
$datasem = $row2['data'];

    $sqla = "SELECT rsproj, rsrod, data, rscota, ROUND(rsproj + rscota, 2) as tot, ROUND(rsproj + rscota - rsrod ,2) as diff FROM bdfrota.`tbrssemanal-novo` where matricula = '$mattec' and data = '".$datasem."'  LIMIT 8";
    $resultadoa = mysql_query($sqla) or die (mysql_error());
    $rowa = mysql_fetch_array($resultadoa);


    if ($rowa[5] < 0) {
           $resultdiff = 0;
        } else {
            $resultdiff = $rowa[5];
        }

            print "<td style='white-space:  nowrap;'><a href='./rotassemana.php?matriculatec=$mattec&diai=$datasem' target='_blank'>$rowa[1]</a></td>";

}
 print"</tr>";            
                
    }

} else {

  $sql = "SELECT matricula, ROUND(orcsemanal,2), ROUND(kmorcsem,2) FROM bdfrota.tbsaldofut where matricula in (SELECT matricula FROM bdfrota.tbusuario where idtbsupervisor = '$idsup') order by data desc limit 1";
    $resultado = mysql_query($sql) or die (mysql_error());


while ($row = mysql_fetch_array($resultado)) {
        
        $mattec = $row[0];
        $orcsem = $row[1];
        $kmproj = $row[2];

        if ($medrsdiff < 0) {
           $medrsdiff = 0;
        }

        $sql2 = "SELECT nome, ccusto FROM bdaniel.tbfuncionario where matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nometec = $row2[0];
        $ccusto = utf8_encode($row2[1]);

        $sql2 = "SELECT b.nome FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b where a.matricula = b.matricula and a.idtbsupervisor = '$idsup'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nomesup = $row2[0];
        

            print "<tr><td>$nometec</td>
              <td>$nomesup</td>
              <td>$ccusto</td>
              <td style='white-space:  nowrap;'>$orcsem</td>
              <td style='white-space:  nowrap;'>$kmproj</td>";
              
            $sql2 = "SELECT data FROM bdfrota.`tbrssemanal-novo` group by data order by data desc";
    $resultado2 = mysql_query($sql2) or die (mysql_error());


while ($row2 = mysql_fetch_array($resultado2)) {
    $datasem = $row2['data'];

    $sqla = "SELECT rsproj, rsrod, data, rscota, ROUND(rsproj + rscota, 2) as tot, ROUND(rsproj + rscota - rsrod ,2) as diff FROM bdfrota.`tbrssemanal-novo` where matricula = '$mattec' and data = '".$datasem."'";
    $resultadoa = mysql_query($sqla) or die (mysql_error());
    $rowa = mysql_fetch_array($resultadoa);

if ($rowa[5] < 0) {
           $resultdiff = 0;
        } else {
            $resultdiff = $rowa[5];
        }

            print "<td style='white-space:  nowrap;'><a href='./detalhes-kmossemanais.php?matriculatec=$mattec&semana=$datasem' target='_blank'>$rowa[1]</a></td>";


}
 print"</tr>";            
                
    }

}




?>

                    </tbody>
                </table>
                           
<form action="./control/gerarexceldetrssemanal-fut.php" method="post">
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