<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome1 = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula1 = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
} /*elseif ($perfil <> '0' && $perfil <>'1' && $perfil<>'2' && $perfil<>'3'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}*/

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula1;
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;

$data = date('Y-m-d');

include"conecta.php";
//include_once"./func/log.php";
header("Content-type: text/html; charset=utf-8");

//control de assinatura
if(!file_exists("../../assinaturas/docs/colass/$matricula1.png")){
  print "<script> window.alert('Necessário cadastrar assinatura.'); window.location='../assinaturas/assinatura.php' </script>";
} ;

$autoinfra = $_POST['autoinfra'];
$placa = $_POST['placa'];

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, , shrink-to-fit=no" />
  <meta name="description" content="FFA" />
  <meta name="author" content="FFA" />
  <link rel="icon" type="image/png" href="../src/images/favicon.png"/>

  <title> Multas Pendentes </title>

  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <!--link Material Symbols - simbolos do input-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

  <!-- jquery e masks -->
  <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

  <!--data tables - css e js-->
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles2.css" rel="stylesheet" />
  <!--<script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>-->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.20/sorting/date-eu.js"></script>
  <script src="https://cdn.datatables.net/plug-ins/1.13.4/sorting/date-eu.js"></script>

</head>

<body class="sb-nav-fixed">

  <!--nav-->
  <?php include "./menu-teste.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">

      <div class="container-fluid px-4" style="width: 80%;">
        <h4 class="h4 pt-3 pb-2 col-12 text-center"> Assinar recibo de adiantamento salarial </h4>

        <?php print"
        <p class='ms-2 me-2'> Tire uma selfie para assinar o recibo de adiantamento salarial referente a multa de auto de infração $autoinfra. </p>";/*./control/assinarreciboffa.php*/
        ?>
        
        <form action="https://ffa.painel-telecom.com:4545/frotas/multas/control/assinarreciboffa.php" method="post" name="form" enctype="multipart/form-data">

          <?php print"
            <input type='hidden' name='matricula' value='$matricula1'>
            <input type='hidden' name='placa' value='$placa'>            
            <input type='hidden' name='autoinfracao' value='$autoinfra'>";
          ?>
          
          <div class="col-12 text-center">
            <label for="inputGroupFile01" class="btn btn-primary">
              <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-camera" style="margin-top: -3px;" viewBox="0 0 16 16">
               <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
              <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
             </svg>  Assinar <span style="color: red;">*</span>
            </label>
            <input type='file'  accept="image/*" capture='camera' name='frontal' id='inputGroupFile01' aria-describedby='inputGroupFileAddon04' aria-label='Upload' style='display: none;' required>
          </div>

           <!-- div campos obrigatórios-->
            <div class="mt-4 col-12 text-center">
              <p style='color: red;'>*Campo obrigatório. </p>
            </div>

          <!--div botão confirmar-->
          <div class="mt-4 col-12 text-center" id='inputGroupFileAddon04'>
              <button class='btn btn-success' type='submit'>Confirmar Assinatura</button>
          </div>
        </form>
       
      </div>
    </main>

    <footer class="py-4 bg-light mt-auto">
      <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between small">
            <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
            <!--<div>
                <a href="#">Privacy Policy</a>
                &middot;
                <a href="#">Terms &amp; Conditions</a>
            </div>-->
        </div>
      </div>
    </footer>
  </div>
  </div><!--div para fechar a div que fica aberta no menu.php-->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../src/js/scripts.js"></script>

<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>-->
  

  <script type='text/javascript' src='/var/www/html/presenca/src/vendor/bootstrap/js/bootstrap.min.js'></script>

  <script>window.jQuery || document.write('<script src="../src/vendor/jquery/jquery.min.js"><\/script>')</script>

  <script type="text/javascript">
    $(document).ready(function () {
      setInterval(function(){
        var foto = document.getElementById("inputGroupFile01");
        var doc01 = document.getElementById("inputGroupFile04");

        if(foto.value=="") {
          $("#inputGroupFileAddon04").addClass('d-none');
        }else{
          $("#inputGroupFileAddon04").removeClass('d-none');
        }
      });
    });
  </script>

</body>
</html>