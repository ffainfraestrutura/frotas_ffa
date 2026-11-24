<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$filial = $_SESSION['filial'];
if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil <> '2'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['filial'] = $filial;
$origem = $_POST['origem'];
$destino = $_POST['destino'];
$volta = $_POST['volta'];
$tipoveiculof = $_POST['tipoveiculo'];
$mattec = $_POST['tecnico'];

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
        <!--link style bootstrap 5 -->
        <link href="src/css/styles.css" rel="stylesheet" />

        <!--link Material Symbols - simbolos do input-->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

        <!-- vendor jquery js -->
        <script type='text/javascript' src='src/vendor/jquery/jquery-3.2.1.min.js'></script>
        <script type='text/javascript' src='src/vendor/jquery/jquery.min.js'></script>

        <!--data tables - css e js-->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>

        
    </head>

    <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

        <!--nav-->
        <?php include"menu.php"; ?>

<div id="layoutSidenav_content">

<main class="m-auto mt-5" style="width: 100%;">

<div class="container-fluid px-4" style="width: 100%;">

    <h1 class="h1 pb-2"> Cálculo Rota Coordenador  </h1>
    <p class="text-danger"><b>Padrão de escrita: Rua Tanagra, 42 - Rio de Janeiro, RJ</b></p>

    <form action='./calculorotacoord.php' method='post'> <!--editar arq action-->

        <!-- div select técnico -->
        <div class="col-6 text-center">
            <select class="form-select mb-3" name="tecnico" id="selecttecnico">
                <option value=" "> Selecione o técnico </option>

            <?php
           //seleciona tecnicos dos supervisores subordinados ao coordenador da sessão
            $sql ="SELECT DISTINCT (a.matricula), b.nome FROM bdfrota.tbtecnico a, bdaniel.tbfuncionario b,bdfrota.tbsupervisor c, bdfrota.tbcoord d  where a.matricula = b.matricula and a.matriculasup=c.matricula and c.idtbcoordenador = d.idtbcoordenador and d.matricula='$matricula'";
            
            $resultado = mysql_query($sql) or die(mysql_error());
            while($row = mysql_fetch_array($resultado)){
                $matriculatec=$row[0];
                $nometec = $row[1];

                print"<option value='$matriculatec'>$nometec</option>";
            }

            ?>

            </select>
        </div>

      <!-- div inputs -->
        
        <!-- Depois procurar com que a pessoa escreva o endereço pelo cep -->
            <!--input origem-->
            <div class="ms-0 mt-2 d-flex input-group-sm" style='height: 100%;'>
                <label class="fw-bolder form-label mt-2" for="origem">Origem: </label>
                <input class="ms-1 form-control" style= "width: 80%;" type="text" name="origem" id="origem" required> 
            </div>
            
            <!--input destino-->
            <div class="ms-0 mt-2 d-flex input-group-sm" style='height: 100%;'>
                <label class="fw-bolder form-label mt-2" for="destino">Destino: </label>
                <input class="ms-1 form-control" style= "width: 80%;" type="text" name="destino" id="destino" required> 
            </div>

            <!--input volta-->
            <div class="ms-0 mt-2 d-flex input-group-sm" style='height: 100%;'>
                <label class="fw-bolder form-label mt-2" for="volta">Destino: </label>
                <input class="ms-1 form-control" style= "width: 80%;" type="text" name="volta" id="volta"> 
            </div>

            <!--input tipo de veículo-->
            <div class='ms-0 mt-2 d-flex input-group' style='height: 100%; width: 25%;'>
                <label class="fw-bolder form-label mt-2" for="tipoveiculo">Tipo Veiculo: </label>
                <select id="tipoveiculo" class='ms-1 form-select' name="tipoveiculo">
<?php

      $sql = "SELECT DISTINCT(tipoveiculo) FROM bdfrota.tbconsumocombustivel";

      /*filtra por claro $sql = "SELECT DISTINCT (nome)  FROM bdaniel.tbfuncionario WHERE ccusto LIKE '%CLARO%' AND cargo LIKE '%coordenador%' ORDER BY nome";*/

      $resultado = mysql_query($sql) or die (mysql_error());
$row = mysql_fetch_array($resultado);
              $tipoveiculo= $row[0];

        print"<option value='$tipoveiculo' selected>$tipoveiculo</option>";
      while($row = mysql_fetch_array($resultado)){
        $tipoveiculo= $row[0];

        print"<option value='$tipoveiculo'>$tipoveiculo</option>";
      }
?>
                </select> 
            </div>

          <!--div botão confirmar-->
            <div class='ms-0 mt-2'>
                <button class='btn btn-success mb-1' type='subimt'> Confirmar </button>
            </div>
        
    </form>


<?php
if ($origem <> '') {

$sql = "SELECT nome FROM bdaniel.tbfuncionario where matricula = '$mattec'";
$resultado = mysql_query($sql) or die (mysql_error());
$row = mysql_fetch_array($resultado);
$nometec = $row[0];

echo "Nome Técnico = $nometec <br> Origem = $origem <br> Destino 1 = $destino <br> Destino 2 = $volta <br><br> ";

$origem = str_replace(" ", "", $origem);
$destino = str_replace(" ", "", $destino);
$volta = str_replace(" ", "", $volta);

$data = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?destinations=$destino&origins=$origem&mode=CAR&sensor=false&language=PT&key=AIzaSyAjFFf5-Iws6-IPKtalnuQnopWgOHu-9z4");
$data = json_decode($data);

$time = 0;
$distance = 0;

foreach($data->rows[0]->elements as $road) {
    $time1 += $road->duration->value;
    $distance1 += $road->distance->value;
}
$km1=round($distance1/1000, 2);
$tempo1=$time1/60;

$data = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?destinations=$volta&origins=$destino&mode=CAR&sensor=false&language=PT&key=AIzaSyAjFFf5-Iws6-IPKtalnuQnopWgOHu-9z4");
$data = json_decode($data);

$time = 0;
$distance = 0;

foreach($data->rows[0]->elements as $road) {
    $time2 += $road->duration->value;
    $distance2 += $road->distance->value;
}
$km2=round($distance2/1000, 2);
$tempo2=$time2/60;

$km = $km1 + $km2;

$sql = "SELECT tipocombustivel, consumo_litro FROM bdfrota.tbconsumocombustivel where tipoveiculo = '$tipoveiculof'";
$resultado = mysql_query($sql) or die (mysql_error());
$row = mysql_fetch_array($resultado);
$tipocomb = $row[0];
$consumo_litro = $row[1];

$sql = "SELECT valorcomb FROM bdfrota.tbvalorcombustivel where tipo = '$tipocomb' and cidade = 'RIO DE JANEIRO'";
$resultado = mysql_query($sql) or die (mysql_error());
$row = mysql_fetch_array($resultado);
$valorcomb = $row[0];

$litros = round($km / $consumo_litro, 2);

$valor = round($litros * $valorcomb, 2);


    print"<!--div tabela - entrar dados do sistema-->
    <div id='containertab' style='
    margin-bottom: 100px;'>
        <table class='table table-striped table-bordered'>
            <thead>
                <tr>
                    <th style='white-space: nowrap;'>Km</th>
                    <th style='white-space: nowrap;'>Litro</th>
                    <th style='white-space: nowrap;'>Valor R$</th>
                    <th style='white-space: nowrap;'>Enviar</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td style='white-space: nowrap;'>$km</td>
                    <td style='white-space: nowrap;'>$litros</td>
                    <td style='white-space: nowrap;'>R$ $valor</td>
                    <td style='white-space: nowrap;'>
                            <form action='./control/cotarotacoord.php' method='post'>
                                        <button class='btn btn-outline-success btn-sm'>
                                        <span class='material-symbols-outlined'>done</span>
                                    </button> 
                            <input type='hidden' name='matriculatec' value='$mattec'>
                            <input type='hidden' name='valor' value='$valor'>
                            <input class='tipocota' type='hidden' name='tipocota' value ='rota'>
                            </form>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>";
}
    
?>



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
            
<script>
    //função iniciar data table 
    $(document).ready( function () {
        $('#datatablesSimple').DataTable();
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="src/js/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="assets/demo/chart-area-demo.js"></script>
<script src="assets/demo/chart-bar-demo.js"></script>
<script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
<script src="src/js/datatables-simple-demo.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>   
        
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
</body>

</html>