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

$dataInicialfi = isset($_POST["datai"]) ? $_POST["datai"] : date('Y-m-d', strtotime('today'));
$dataFinalfi = isset($_POST["dataf"]) ? $_POST["dataf"] : date('Y-m-d', strtotime('today'));
$mattecfi = isset($_POST["mattec"]) ? $_POST["mattec"] : '';

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
        <h1 class="h1 mt-2"> Relatório LOG</h1>
<form action="./rel-log.php" method="post">
 <h5><b>Selecione o Periodo:</b></h5>

     <div class="row">


     <div class="col-md-3">
   <label for="exampleInputDate1"><h5>Data Inicial:</h5></label>
<input type="date" class="form-control" id="exampleInputDate1" name="datai" required>
    </div>     
    <div class="col-md-3">
   <label for="exampleInputDate2"><h5>Data Final:</h5></label>
<input type="date" class="form-control" id="exampleInputDate2" name="dataf" required>
    </div>

     <div class="col-md-3">
            <label for="matricula"><h5>Matrícula:</h5></label>
            <input type="text" class="form-control mb-3" name="mattec" placeholder="Digite a matrícula">
        </div>   
    


</div>




<div class='mt-2'>
   

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
                            <th>Matricula Atuante</th>
                            <th>Nome Atuante</th>
                            <th>Ação</th>
                            <th>valor</th>
                            <th>Matricula Funcionário</th>
                            <th>Nome Funcionário</th>
                            <th>Data e hora</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php     
if ($dataInicialfi == '' or $dataFinalfi == '') {
    
print "<tr>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
               </tr>";


} else {


    $sql = "SELECT acao, data_e_hora, matricula, mat_autor, valor_novo FROM bdfrota.tblog where data_e_hora >= '$dataInicialfi 00:00:00' and data_e_hora <= '$dataFinalfi 23:59:59' and (matricula = '$mattecfi' or mat_autor = '$mattecfi')";
      
                    $resultado = mysql_query($sql) or die (mysql_error());
                    while($row = mysql_fetch_array($resultado)){
                        $acao = utf8_encode($row[0]);
                        $data_e_hora = $row[1];
                        $matricula1 = $row[2];
                        $mat_autor = $row[3];
                        $valor = $row[4];
                        
                        $sql2 = "SELECT nome FROM bdaniel.tbfuncionario where matricula = '$matricula1'";
                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        $row2 = mysql_fetch_array($resultado2);
                        $nome1 = $row2[0];

                        $sql2 = "SELECT nome FROM bdaniel.tbfuncionario where matricula = '$mat_autor'";
                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        $row2 = mysql_fetch_array($resultado2);
                        $nome2 = $row2[0];


        print "<tr>
                            <td>$mat_autor</td>
                            <td>$nome2</td>
                            <td>$acao</td>
                            <td>$valor</td>
                            <td>$matricula1</td>
                            <td>$nome1</td>
                            <td>$data_e_hora</td>
               </tr>";



      }

} 
      ?>
                        
                    </tbody>
                </table>
                <form action="./control/gerarexceltoaqui.php" method="post">
              
                <input type="hidden" name="dataf" value="<?php echo htmlspecialchars($dataFinalfi); ?>">
                <input type="hidden" name="ccusto" value="<?php echo htmlspecialchars($ccustofi); ?>">
                <input type="hidden" name="filial" value="<?php echo htmlspecialchars($filialfi); ?>">
                <input type="hidden" name="empresa" value="<?php echo htmlspecialchars($empresafi); ?>">
                <input type="hidden" name="coordenador" value="<?php echo htmlspecialchars($coordenadorfi); ?>">
                <input type="hidden" name="matsupf" value="<?php echo htmlspecialchars($matsupfi); ?>">
                <input type="hidden" name="mattecf" value="<?php echo htmlspecialchars($mattecfi); ?>">
                
              
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