<?php
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('America/Sao_Paulo');
// Defina o tempo de vida da sessão para 1 hora
$lifetime = 3600; // 1 hora em segundos

// Configurar os parâmetros do cookie da sessão
session_set_cookie_params($lifetime);

// Iniciar a sessão
session_start();

$nome1 = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula1 = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
} /*elseif ($perfil<>'4'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}*/

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula1;
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;

/*$placaveic = $_POST['placa'];
$placacorreta = str_replace(" ", "", $placaveic);*/

$data = date('Y-m-d');

include"conecta.php";

//include_once"./func/log.php";

//$link = "$_SERVER[REQUEST_URI]";
//$idaux = explode("=", $link);
//$placa = $idaux[1];

$placa = $_GET['placa'];
$datavistoria = $_GET['datavistoria'];
$matriculac = $_GET['matricula'];

$sql2 = "SELECT max(idtbvistoria) FROM bdfrota.tbvistoria";
$resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
$row2= mysqli_fetch_array($resultado2, MYSQLI_BOTH);
$idtbvistoria2=$row2['idtbvistoria'];

$sql = "SELECT max(idtbvistoria) FROM bdfrota.tbvistoria where placa='".$placa."'";
$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
$row= mysqli_fetch_array($resultado, MYSQLI_BOTH);
$idtbvistoria=$row['idtbvistoria'];

if($idtbvistoria == null){
  $id=$idtbvistoria;
} else {
  $id=$idtbvistoria2;
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta http-equiv="ScreenOrientation" content="autoRotate:disabled"/>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, , shrink-to-fit=no" />
  
  <meta name="description" content="FFA" />
  <meta name="author" content="FFA" />
  <link rel="icon" type="image/png" href="../src/images/favicon.png"/>

  <title> Checklist - Passo 2 </title>

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

  <!--link Material Symbols - simbolos do input-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
  
  <!--data tables - css e js
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="src/css/styles.css" rel="stylesheet" />
  <script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>-->

  <!-- jquery e masks -->
  <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

  <style type="text/css">
    @media only screen and (min-width: 320px) and (max-width: 767px) and (orientation:landscape) {
      body {
        height: 100vw;
        transform: rotate(90deg);
      }
    }

    
  </style>
</head>
<body class="sb-nav-fixed">

  <!--nav-->
  <?php include "./menu.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100vw;" class="mb-2">

      <div class="container-fluid px-4 col-sm-12">
        <h1 class="h1 pt-3 pb-2 col-12 text-center"> Checklist do Veículo </h1>
        <?php print $id; ?>

        <!-- div form-->
        <div>
          <form method="post" action="./control/checklistp2.php" enctype='multipart/form-data'>
            <div class="position-relative" style="height: 70vh;">
              <div style="position: relative; height: 100%;">

                <?php
                  print "<input type='hidden' id='placa' name='placa' value='$placa' />
                  <input type='hidden' id='vistoriador' name='vistoriador' value='$matricula1' />
                  <input type='hidden' id='matriculac' name='matriculac' value='$matriculac' />
                  <input type='hidden' name='datavistoriaf' id='datavistoriaf' value='$datavistoria' />";
                  //print $datavistoria;
                  //print $placa;
                ?>
               

                <img src="./src/images/istockphoto-carro1.jpg" style="position:  absolute; top:  15%; left: 15.5%; width: 200px; height: 372px;">

                <!-- div foto frontal -->
                <div style="position: absolute; left: 30%; top: 10%;">
                  <label for="inputGroupFile01" class="btn btn-primary" id='frontal'>
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-camera" style="margin-top: -3px;" viewBox="0 0 16 16">
                     <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
                    <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                   </svg>  Frontal <span style="color: red;">*</span>
                  </label>
                  <input type='file' id='inputGroupFile01' aria-describedby='inputGroupFileAddon04' aria-label='Upload' capture='camera' name='frontal' style='display: none;' oninput="mudar()" required>
                </div>

                <!-- div foto Traseira --> 
                <div style="position: absolute; left: 29%; bottom: 2.5%;">
                  <label for="inputGroupFile02" class="btn btn-primary" id='traseira'>
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-camera" style="margin-top: -3px;" viewBox="0 0 16 16">
                     <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
                    <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                   </svg>  Traseira <span style="color: red;">*</span>
                  </label>
                  <input type='file' id='inputGroupFile02' aria-describedby='inputGroupFileAddon04' aria-label='Upload' capture='camera' name='traseira' style='display: none;' required>
                </div>

                <!-- div foto Painel -->
                <div style="position: absolute; left: -5%; top: 35%;">
                  <label for="inputGroupFile03" class="btn btn-primary" id='painel'>
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-camera" style="margin-top: -3px;" viewBox="0 0 16 16">
                     <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
                    <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                   </svg>  Painel<span style="color: red;">*</span>
                  </label>
                  <input type='file' id='inputGroupFile03' aria-describedby='inputGroupFileAddon04' aria-label='Upload' capture='camera' name='painel' style='display: none;' required>
                </div>

                <!-- div foto Lateral Esquerda -->
                <div style="position: absolute; left: -5%; top: 50%; width: 58px;">
                  <label for="inputGroupFile04" class="btn btn-primary" id='esquerda'>
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-camera" style="margin-top: -3px;" viewBox="0 0 16 16">
                     <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
                    <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                   </svg>  Lateral Esquerda<span style="color: red;">*</span>
                  </label>
                  <input type='file' id='inputGroupFile04' aria-describedby='inputGroupFileAddon04' aria-label='Upload' capture='camera' name='esquerda' style='display: none;' required>
                </div>

                <!-- div foto Lateral Direita -->
                <div style="position: absolute; right: -5%; top: 39%; width: 100px;">
                  <label for="inputGroupFile05" class="btn btn-primary" id='direita'>
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-camera" style="margin-top: -3px;" viewBox="0 0 16 16">
                     <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
                    <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                   </svg>  Lateral Direita<span style="color: red;">*</span>
                  </label>
                  <input type='file' id='inputGroupFile05' aria-describedby='inputGroupFileAddon04' aria-label='Upload' capture='camera' name='direita' style='display: none;' required>
                </div>                

                <!-- div foto Selfie -->
                <div style="position: absolute; right: -1%; top: 20%;">
                  <label for="inputGroupFile07" class="btn btn-primary" id='selfie'>
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-camera" style="margin-top: -3px;" viewBox="0 0 16 16">
                     <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
                    <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                   </svg>  Selfie<span style="color: red;">*</span>
                  </label>
                  <input type='file' id='inputGroupFile07' aria-describedby='inputGroupFileAddon04' aria-label='Upload' capture='camera' accept='user' name='selfie' style='display: none;' required>
                </div> 

                <!-- div foto CNH -->
                <div style="position: absolute; right: -1%; top: 30%;">
                  <label for="inputGroupFile08" class="btn btn-primary" id='cnh'>
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-camera" style="margin-top: -3px;" viewBox="0 0 16 16">
                     <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
                    <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                   </svg>CNH<span style="color: red;">*</span>
                  </label>
                  <input type='file' id='inputGroupFile08' aria-describedby='inputGroupFileAddon04' aria-label='Upload' capture='camera' name='cnh' style='display: none;' required>
                </div> 

                <!-- div foto Extra -->
                <div style="position: absolute; right: -1%; top: 53%;">
                  <label for="inputGroupFile09" class="btn btn-primary" id='extra1'>
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-camera" style="margin-top: -3px;" viewBox="0 0 16 16">
                     <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
                    <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                   </svg>  Extra 1
                  </label>
                  <input type='file' id='inputGroupFile09' aria-describedby='inputGroupFileAddon04' aria-label='Upload' capture='camera' name='extra1' style='display: none;'>
                </div> 

                <!-- div foto Extra -->
                <div style="position: absolute; right: -1%; top: 62%;">
                  <label for="inputGroupFile10" class="btn btn-primary" id='extra2'>
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-camera" style="margin-top: -3px;" viewBox="0 0 16 16">
                     <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
                    <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                   </svg>  Extra 2
                  </label>
                  <input type='file' id='inputGroupFile10' aria-describedby='inputGroupFileAddon04' aria-label='Upload' capture='camera' name='extra2' style='display: none;'>
                </div>

                <!-- div foto Extra -->
                <div style="position: absolute; right: -1%; top: 71%;">
                  <label for="inputGroupFile11" class="btn btn-primary" id='extra3'>
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-camera" style="margin-top: -3px;" viewBox="0 0 16 16">
                     <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
                    <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                   </svg>  Extra 3
                  </label>
                  <input type='file' id='inputGroupFile11' aria-describedby='inputGroupFileAddon04' aria-label='Upload' capture='camera' name='extra3' style='display: none;'>
                </div>

              <!-- div campos obrigatórios-->
              <div class="mt-4 col-12" style="top: 10%;">
                <p style='color: red;'> * Campos obrigatórios. </p>
              </div>

              <!-- div botão text-center end-0-->

              <div class="mt-4 col-12  position-absolute" style="top: 95%; right: -30%;" id='inputGroupFileAddon04'>
                <button class="col-4 btn btn-success text-center" type='submit' id='proximo'> Próximo </button>
              </div>
            </div>

          </form>
        </div>   
      </div>
    </main>

    <footer class="py-4 bg-light mt-auto fixed-bottom">
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

  /*function mudar(){
    var btnfrontal = document.getElementById('frontal');
    var frontal = document.getElementById("inputGroupFile01");

    if(frontal.value!=""){
      btnfrontal.classList.remove('btn-primary');
      btnfrontal.classList.add('btn-success');
      console.log(frontal.value);
    }
  }*/

  $(document).ready(function () {
    setInterval(function(){
        var frontal = document.getElementById("inputGroupFile01");
        var traseira = document.getElementById("inputGroupFile02");
        var painel = document.getElementById("inputGroupFile03");
        var esquerda = document.getElementById("inputGroupFile04");
        var direita = document.getElementById("inputGroupFile05");       
        var selfie = document.getElementById("inputGroupFile07");
        var cnh = document.getElementById("inputGroupFile08");
        var extra1 = document.getElementById("inputGroupFile09");
        var extra2 = document.getElementById("inputGroupFile10");
        var extra3 = document.getElementById("inputGroupFile11");
        
        var btnfrontal = document.getElementById('frontal');
        var btntraseira = document.getElementById("traseira");
        var btnpainel = document.getElementById("painel");
        var btnesquerda = document.getElementById("esquerda");
        var btndireita = document.getElementById("direita");        
        var btnselfie = document.getElementById("selfie");
        var btncnh = document.getElementById("cnh");
        var btnextra1 = document.getElementById("extra1");
        var btnextra2 = document.getElementById("extra2");
        var btnextra3 = document.getElementById("extra3");
        

        if(frontal.value!=""){
          btnfrontal.classList.remove('btn-primary');
          btnfrontal.classList.add('btn-secondary');
        }
        
        if(traseira.value!=""){
          btntraseira.classList.remove('btn-primary');
          btntraseira.classList.add('btn-secondary');
        }

        if(painel.value!=""){
          btnpainel.classList.remove('btn-primary');
          btnpainel.classList.add('btn-secondary');
        }

        if(esquerda.value!=""){
          btnesquerda.classList.remove('btn-primary');
          btnesquerda.classList.add('btn-secondary');
        }

        if(direita.value!=""){
          btndireita.classList.remove('btn-primary');
          btndireita.classList.add('btn-secondary');
        }

        

        if(selfie.value!=""){
          btnselfie.classList.remove('btn-primary');
          btnselfie.classList.add('btn-secondary');
        }

        if(cnh.value!=""){
          btncnh.classList.remove('btn-primary');
          btncnh.classList.add('btn-secondary');
        }

        if(extra1.value!=""){
          btnextra1.classList.remove('btn-primary');
          btnextra1.classList.add('btn-secondary');
        }

        if(extra2.value!=""){
          btnextra2.classList.remove('btn-primary');
          btnextra2.classList.add('btn-secondary');
        }

        if(extra3.value!=""){
          btnextra3.classList.remove('btn-primary');
          btnextra3.classList.add('btn-secondary');
        }        

        if(frontal.value=="" || traseira.value=="" || painel.value=="" || esquerda.value=="" || direita.value=="" || selfie.value=="" || cnh.value=="") {
          $("#inputGroupFileAddon04").addClass('d-none');
        }else{
          $("#inputGroupFileAddon04").removeClass('d-none');
        }

        /*if(frontal.value=="" || traseira.value=="" ) {
          $("#inputGroupFileAddon04").addClass('d-none');
        }else{
          $("#inputGroupFileAddon04").removeClass('d-none');
        }*/
    });


  });

  $( "form" ).on( "submit", function( event ) {
    //var btn = document.getElementById("proximo");
    $("#proximo").addClass('d-none');
  });

  

  </script>

</body>
</html>