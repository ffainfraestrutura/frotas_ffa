<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
/*$nome1 = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula1 = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$tipo= $_SESSION['tipo'];*/

$nome1 = $_GET['nome'];
$usuariof = $_GET['usuario'];
$matricula1 = $_GET['matricula'];
$perfil = $_GET['perfil'];
$tipo= $_GET['tipo'];

/*if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
} /*elseif ($perfil<>'1'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";}*/

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula1;
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo']=$tipo;


$data = date('Y-m-d');

/*$placaveic = $_POST['placa'];
if(strpos($placaveic, "-") !== false){
  $placacorreta = str_replace("-", "", $placaveic);
} else {
  $placacorreta = str_replace(" ", "", $placaveic);
}*/

$idtbvistoria = $_POST['idtbvistoria'];

include"../conecta.php";
include"../conecta03.php";

//include_once"./func/log.php";
header("Content-type: text/html; charset=utf-8");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, , shrink-to-fit=no" />
  <meta name="description" content="FFA" />
  <meta name="author" content="FFA" />
  <link rel="icon" type="image/png" href="../src/images/favicon.png"/>

  <title> Visualizar Relatório </title>

  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <!-- Vendor CSS
  <link href="src/vendor/select2/select2.min.css" rel="stylesheet" media="all">
  <link href="src/vendor/datepicker/daterangepicker.css" rel="stylesheet" media="all">-->

  <!-- Bootstrap core CSS 
  <link href="src/index_files/bootstrap.css" rel="stylesheet">-->

  <!-- Custom styles for this template -->
  <!--<script src="src/index_files/ie-emulation-modes-warning.js.download"></script>
  <script type='text/javascript' src='src/vendor/jquery/jquery-3.2.1.min.js'></script>
  <script type='text/javascript' src='src/vendor/jquery/jquery.min.js'></script>-->

  <!--link Material Symbols -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

  <!-- jquery e masks -->
  <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

  <!--data tables - css e js-->
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />
  <!--<script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>-->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.20/sorting/date-eu.js"></script>
  <script src="https://cdn.datatables.net/plug-ins/1.13.4/sorting/date-eu.js"></script>

</head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

  <!--nav-->
  <?php //include "./menu.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">

      <div class="container-fluid px-4" style="width: 80%;">
        <h1 class="h3 pt-3 pb-2 col-12 text-center"> Anexos </h1>
          
        <div class="">
          <?php           

            $sql1 = "SELECT * FROM bdfrota.tbvistoria WHERE idtbvistoria='$idtbvistoria';";

            $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));

            $row1 = mysqli_fetch_array($resultado1, MYSQLI_ASSOC);
              $idtbvistoria = $row1['idtbvistoria'];
              $placa = $row1['placa'];
              $modelo = $row1['modelo'];
              $statusveic = $row1['statusveic'];
              $ano = $row1['ano'];
              $ccusto = $row1['ccusto'];
              $tipo = $row1['tipo'];
              $vistoriador = $row1['vistoriador'];
              $matrvistoriador = $row1['matrvistoriador'];
              $datavistoria = $row1['datavistoria'];
              $estado = $row1['estado'];
              $avaria = $row1['avaria'];
              $observacao = $row1['observacao'];             

              $datavistoria1 = explode(" ",$datavistoria);
              $datavistoria2 = explode("-", $datavistoria1[0]);
              $datavistoriareal = $datavistoria2[2] . "/" .$datavistoria2[1] . "/" .$datavistoria2[0];


              $sql5 = "SELECT * FROM bdfrota.tbvistoriafotos WHERE idtbvistoria = '$idtbvistoria';";              
              $resultado5 = mysqli_query($con, $sql5) or die(mysqli_error($con));
              $row5 = mysqli_fetch_array($resultado5, MYSQLI_ASSOC);
                $extra1 = $row5['extra1'];
                $extra2 = $row5['extra2'];
                $extra3 = $row5['extra3'];
                $extra4 = $row5['extra4'];
                $extra5 = $row5['extra5'];

                $href1 = 'http://ffasip.ddns.net:4242/frotas/checklist'.$extra1;
                $href2 = 'http://ffasip.ddns.net:4242/frotas/checklist'.$extra2;
                $href3 = 'http://ffasip.ddns.net:4242/frotas/checklist'.$extra3;
                $href4 = 'http://ffasip.ddns.net:4242/frotas/checklist'.$extra4;
                $href5 = 'http://ffasip.ddns.net:4242/frotas/checklist'.$extra5;
            ?>

            <div>
              <div>
                
              </div>
            </div>



              if(strpos($extra1, ".pdf") !== false){
                print"
                <div>
                  <a class='btn btn-secondary' href='$href1' target='_blank'> Ver pdf 1</a>
                </div>";
              }else{
                print"
                <div>
                  <img src='$href1' alt='$extra1'>
                </div>";
              }

              if(strpos($extra2, ".pdf") !== false){
                print"
                <div>
                  <a class='btn btn-secondary' href='$href2' target='_blank'> Ver pdf 2</a>
                </div>";
              }else{
                print"
                <div>
                  <img src='$href2' alt='$extra2'>
                </div>";
              }

              if(strpos($extra3, ".pdf") !== false){
                print"
                <div>
                  <a class='btn btn-secondary' href='$href3' target='_blank'> Ver pdf 3</a>
                </div>";
              }else{
                print"
                <div>
                  <img src='$href3' alt='$extra3'>
                </div>";
              }

              if(strpos($extra4, ".pdf") !== false){
                print"
                <div>
                  <a class='btn btn-secondary' href='$href4' target='_blank'> Ver pdf 4</a>
                </div>";
              }else{
                print"
                <div>
                  <img src='$href4' alt='$extra4'>
                </div>";
              }

              if(strpos($extra5, ".pdf") !== false){
                print"
                <div>
                  <a class='btn btn-secondary' href='$href5' target='_blank'> Ver pdf 5</a>
                </div>";
              }else{
                print"
                <div>
                  <img src='$href5' alt='$extra5'>
                </div>";
              } 
          ?> 
        </div>          
              


      </div>
    </main>

    <!--<footer class="py-4 bg-light mt-auto">
      <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between small">
            <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
            <--<div>
                <a href="#">Privacy Policy</a>
                &middot;
                <a href="#">Terms &amp; Conditions</a>
            </div>->
        </div>
      </div>
    </footer>-->
  </div>
  </div><!--div para fechar a div que fica aberta no menu.php-->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../src/js/scripts.js"></script>

<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>-->
  

  <script type='text/javascript' src='/var/www/html/presenca/src/vendor/bootstrap/js/bootstrap.min.js'></script>

  <script>window.jQuery || document.write('<script src="src/vendor/jquery/jquery.min.js"><\/script>')</script>

  <script type="text/javascript">
    $(document).ready(function () {
      $('#tabela1').DataTable({
        "columns": [
          null,
          { "type": "date-eu" },
          null,
          null
        ],

        order: [[1, 'desc']],

        "language": {
          "decimal":        "",
          "emptyTable":     "Nada para exibir",
          "info":           "Mostrando de _START_ até _END_ de _TOTAL_ registros",
          "infoEmpty":      "Exibindo página 0 de 0 de 0 registros",
          "infoFiltered":   "(filtrado do total de _MAX_ registros)",
          "infoPostFix":    "",
          "thousands":      ",",
          "lengthMenu":     "Exibir _MENU_ registros",
          "loadingRecords": "Carregando...",
          "processing":     "Processando...",
          "search":         "Buscar:",
          "zeroRecords":    "Nenhum resultado encontrado",
          "paginate": {
              "first":      "Primeira",
              "last":       "Última",
              "next":       "Próxima",
              "previous":   "Anterior"
          }
        },

        lengthMenu: [
            [10, 5, 1, -1],
            [10, 5, 1, 'Todos'],
        ],
      });

      //mascara de placa
      $('#placa').mask('AAA0U00', {
        translation: {
            'A': {
                pattern: /[A-Za-z]/
            },
            'U': {
                pattern: /[A-Za-z0-9]/
            },
        },
        onKeyPress: function (value, e, field, options) {
            // Convert to uppercase
            e.currentTarget.value = value.toUpperCase();

            // Get only valid characters
            let val = value.replace(/[^\w]/g, '');

            // Detect plate format
            let isNumeric = !isNaN(parseFloat(val[4])) && isFinite(val[4]);
            let mask = 'AAA 0U00';
            if(val.length > 4 && isNumeric) {
                mask = 'AAA-0000';
            }
            $(field).mask(mask, options);
        }
      });
    });
  </script>

</body>
</html>