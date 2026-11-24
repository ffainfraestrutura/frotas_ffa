<?php
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
$tipo=$_SESSION['tipo'];

if($perfil == ''){
  $perfil =  $_POST['perfil'];
  $matricula1 = $_POST['matr_autor'];
}

/*if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.close();</script>";

} elseif ($perfil == '0' || $perfil == '1' || $perfil == '2'|| $perfil == '3'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.close();</script>";

}elseif ($matricula1 == '160030'||$matricula1 == '410109'||$matricula1 == '500422'||$matricula1 == '501285'||$matricula1 == '410039'||$matricula1 == '501640'||$matricula1 == '500459' || $matricula1=='411425' || $matricula1 == '003931'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.close();</script>";

  /*window.location=\"../index.php\"
}*/

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula1;
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo'] = $tipo;

$idtbcondutor = $_POST['idtbcondutor'];

include"../conecta03.php";
//include"conecta.php";
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

  <title> Cadastro de Condutor </title>

  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <!--link Material Symbols - simbolos do input-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <!-- vendor jquery js 
  <script type='text/javascript' src='../src/vendor/jquery/jquery-3.2.1.min.js'></script>
  <script type='text/javascript' src='../src/vendor/jquery/jquery.min.js'></script>-->

  <!-- jquery e masks -->
  <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

</head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

  <!--nav-->
  <?php include "./menu-teste.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">

      <div class="container-fluid px-4" style="width: 100%;"> 
        <h1 class="h1 pt-2 pb-2 m-auto text-center"> Desvincular Veículo </h1>

        <!-- div do formulario -->
        <div>
          <form method="post" action="./control/desvincularveiculo.php" style="width: 70%;" class="mb-3 m-auto">
                         
              <?php 
              $sql = "SELECT * FROM bdfrota.tbcondutor WHERE idtbcondutor = '".$idtbcondutor."';";

              $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                  $nome = utf8_encode($row['nome']);
                  $matricula = $row['matricula'];
                  $placa = $row['placaassoc'];
                  $cartao = $row['cartaoassoc'];
                  $dataassoc = $row['dataassoc'];

                  $data_assoc = explode(" ", $dataassoc);
                }

                //echo $nome, $matricula;

            print "

            <p class='m-auto text-center'> Para desvincular o veículo  de placa $placa deste condutor, insira a data de desvinculação. </p>
            <!-- div identificação -->
            <div>
            <input  type='hidden' name='idtbcondutor' value='$idtbcondutor'>
            <input type='hidden' name='matr_autor' id='matr_autor' value='$matricula1'>
            <!--div nome e matricula-->
                <div class='col-sm-12 m-auto d-flex justify-content-center'>
                  <!-- div nome -->
                  <div class='d-flex flex-column col-sm-5'>
                    <label class='form-label mt-2 me-5' for='nome'>Nome: </label>
                    <input class='form-control'  type='text' name='nome' id='nome' placeholder='Nome' value='$nome' disable>
                  </div>

                  <!-- div matricula -->
                  <div class='ms-3 d-flex flex-column col-sm-4'>
                    <label class='form-label mt-2' for='matricula'>Matricula: </label>
                    <input class='form-control'  type='text' name='matricula' id='matricula'  value='$matricula'  placeholder='Matrícula' disable>
                  </div>
                </div>
              
                <!-- div placa cartão -->
                <div class='col-sm-12 d-flex justify-content-center'>
                  <!-- cartão -->
                  <div class='d-flex flex-column col-sm-5'>
                    <label class='form-label mt-2' for='cartao'>Cartão vinculado ao veículo: </label>
                    <input class='form-control' type='text' name='cartao' id='cartao'  value='$cartao' placeholder='Cartao' disable>
                  </div>

                  <!-- placa -->
                  <div class='ms-3 d-flex flex-column col-sm-4'>
                    <label class='form-label mt-2' for='placa'>Placa do veículo: </label>
                    <input class='form-control' type='text' name='placa' id='placa' value='$placa'  placeholder='Placa' disable>
                  </div>
                </div>

                <!-- div datas -->
                <div class='col-sm-12 d-flex justify-content-center'>
                  <!-- data associação -->
                  <div class='d-flex flex-column col-sm-4'>
                    <label class='form-label mt-2' for='data'>Data de associação: </label>
                    <input class='form-control' type='date' name='data' id='data' value='$data_assoc[0]' disable>
                  </div>

                  <!-- data dissociação -->
                  <div class='d-flex flex-column col-sm-4' style='margin-left: 95px;'>
                    <label class='form-label mt-2' for='datad'>Data de dissociação:<span style='color: red;'>*</span> </label>
                    <input class='form-control' type='date' name='datad' id='datad' >
                  </div>
                </div>
              </div>
            </div>";
            ?> 

            <!-- div texto campos obrigatório -->
            <div class="col-7 mb-2 mt-3 pt-2 border-top border-dark" style="color: red; margin-left: 280px;">
              * Campos obrigatórios.
            </div>

            <!-- div botão -->
            <div class="mt-3 pb-2 d-flex col-sm-12 justify-content-center">
              <div>
                <button class="btn btn-success" type='submit'> Confirmar </button>
              </div>

              <div class="ms-5"> <!--href='./listagemcondutores.php'-->
                <a class="btn btn-danger" onclick="window.close();"> Cancelar </a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </main>

    <footer class="mt-4 py-4 bg-light mt-auto" style="position: fixed; bottom: 0; start: 0; width: 100%;">
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
  </div><!--div para fechar a div que fica aberta no menu.php-->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../src/js/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>

  <!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="../src/js/scripts.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>-->
  <!--<script src="assets/demo/chart-area-demo.js"></script>
  <script src="assets/demo/chart-bar-demo.js"></script>
  <script src="../src/js/simple-datatable.js" crossorigin="anonymous"></script>
  <script src="../src/js/datatables-simple-demo.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>   
        
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>-->

  <script>
    //mascaras de input
    $(document).ready(function(){
      $('#cnh').mask('00000000000', {reverse: true});
      $('#pontos').mask('000', {reverse: true});
      $('#cartao').mask('0000 0000 0000 0000');

      //formatação de placa
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

      //formatação do texto no input
      $("#nome").on("input", function(){
        $(this).val($(this).val().toUpperCase());
      });
    });
    
  </script>

</body>
</html>