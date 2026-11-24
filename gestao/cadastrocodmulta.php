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

if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
} elseif ($perfil == '0' || $perfil == '1' || $perfil == '2'|| $perfil == '3'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
} elseif ($matricula1 == '160030'||$matricula1 == '410109'||$matricula1 == '500422'||$matricula1 == '501285'||$matricula1 == '410039'||$matricula1 == '501640'||$matricula1 == '500459' || $matricula1=='411425' || $matricula1 == '003931'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula1;
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo']= $tipo;

$idtbacodmulta = $_POST['idtbacodmulta'];

//include"../conecta.php";
include"../conecta2.php";
//include_once"./func/log.php";
header("Content-type: text/html; charset=utf-8");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, , shrink-to-fit=no" />
  <meta name="description" content="FFA" />
  <meta name="author" content="FFA" />
  <link rel="icon" type="image/png" href="../src/images/favicon.png"/>

  <title> Cadastrar Código Multa </title>

  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />

  <!--link Material Symbols - simbolos do input-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <!-- vendor jquery js -->
  <!--<script type='text/javascript' src='../src/vendor/jquery/jquery-3.2.1.min.js'></script>
  <script type='text/javascript' src='../src/vendor/jquery/jquery.min.js'></script>-->

  <!--data tables - css e js
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>-->

  <!-- jquery e masks -->
  <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

  <script type="text/javascript" src="https://cdn.jsdelivr.net/gh/plentz/jquery-maskmoney@master/dist/jquery.maskMoney.min.js"></script>
</head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

  <!--nav-->
  <?php include"menu-teste.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;">

      <div class="container-fluid px-4" style="width: 100%;"> 
        <h1 class="h1 pt-2 pb-2 m-auto text-center"> Cadastrar Código de Multa </h1>

        <!-- div do formulario -->
        <div>
          <form method="post" action="./control/cadastrarcodmulta.php" style="width: 80%;" class="m-auto">            
            <!-- div cadastro-->
            <div class='d-flex input-group-sm col-sm-10 m-auto justify-content-start'>
              <?php print"
              <input type='hidden' name='matr_autor' id='matr_autor' value='$matricula1'>";?>
              <div>
                <label class='form-label mb-0' for='codigo'>Código da multa:<span style='color: red;'>*</span> </label>
                <input class='form-control' type='text' name='codigo' id='codigo' onkeypress='return event.charCode >= 48 && event.charCode <= 57' required>
              </div>

              <div class='ms-2'>
                <label class='form-label mb-0' for='pontos'>Pontos:<span style='color: red;'>*</span></label>
                <input class='form-control' type='text' name='pontos' id='pontos' onkeypress='return event.charCode >= 48 && event.charCode <= 57' required>
              </div>

              <div class='ms-2'>
                <label class='form-label mb-0' for='valor'>Valor:<span style='color: red;'>*</span></label>
                <input class='form-control valor' type='text' name='valor' id='pontos' onkeypress='return event.charCode >= 48 && event.charCode <= 57' required>
              </div>
            </div>

            <!-- div descrição-->
            <div class='mt-2 col-sm-10 m-auto'>
              <label class='form-label mb-0' for='descricao'>Descrição:<span style='color: red;'>*</span> </label>
              <input class='form-control' style='width: 84%;' type='text' name='descricao' id='descricao' required>
            </div>

            <div class='mt-2 col-sm-10 m-auto d-flex justify-content-start'>
              <!-- div infrator-->
              <div class='col-sm-4'>
                <label class='form-label mb-0' for='infrator'>Infrator:<span style='color: red;'>*</span> </label>
                <input class='form-control' type='text' name='infrator' id='infrator' required>
              </div>
          
              <!-- div gravidade
              <div class='ms-2 col-sm-4'>
                <label class='form-label mb-0' for='gravidade'>Gravidade:<span style='color: red;'>*</span> </label>
                <input class='form-control' type='text' name='gravidade' id='gravidade' required>
              </div>-->

              <!-- div gravidade-->
                <div class='ms-2 col-sm-4'>
                  <label class='form-label mb-0' for='gravidade'>Gravidade:<span style='color: red;'>*</span> </label>
                  <select class='form-select' name='gravidade' id='gravidade' required>
                    <option value=""> SELECIONE... </option>
                    <option value="LEVE"> LEVE </option>
                    <option value="MÉDIA"> MÉDIA </option>
                    <option value="GRAVE"> GRAVE </option>
                    <option value="GRAVÍSSIMA"> GRAVÍSSIMA </option>
                  </select>
                </div>
            </div>

            <!-- div valor com desc, valor sem desc, valor juros-->
            <div class='mt-2 d-flex input-group-sm col-sm-10 m-auto justify-content-start'>
              <!-- div valor com desc-->
              <div>
                <label class='form-label mb-0' for='valorcdesc'>Valor com Desconto:</label>
                <input class='form-control valor' type='text' name='valorcdesc' id='valorcdesc' onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
              </div>

              <!-- div valor sem desc-->
              <div class='ms-2'>
                <label class='form-label mb-0' for='valorsdesc'>Valor sem desconto:</label>
                <input class='form-control valor' type='text' name='valorsdesc' id='valorsdesc' onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
              </div>

              <!-- div valor juros -->
              <div class='ms-2'>
                <label class='form-label mb-0' for='valorjuros'>Valor juros:</label>
                <input class='form-control valor' type='text' name='valorjuros' id='valorjuros' onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
              </div>
            </div>

            <!-- div texto campos obrigatório -->
            <div style="color: red;" class="mt-4 border-top border-dark pt-2 col-sm-10 m-auto">
              <p>* Campos obrigatórios.<p>
            </div>

             <!-- div botão -->
            <div class="mt-3 pb-2 d-flex col-sm-12 justify-content-center">
              <div>
                <button class="btn btn-success" type='submit'> Confirmar cadastro </button>
              </div>

              <div class="ms-5">
                <a class="btn btn-danger" href='./listagemcodmulta.php'> Cancelar cadastro </a>
              </div>
            </div>
          </form>
        </div>
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <!--<script src="../src/js/scripts.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
  <script src="assets/demo/chart-area-demo.js"></script>
  <script src="assets/demo/chart-bar-demo.js"></script>
  <script src="../src/js/simple-datatable.js" crossorigin="anonymous"></script>
  <script src="../src/js/datatables-simple-demo.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
        
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>--> 
  
  <script type="text/javascript">
    $(document).ready(function(){
      //formatação do texto no input
      /*$("#descricao").on("input", function(){
        $(this).val($(this).val().toUpperCase());
      });*/

      $('.valor').maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:',', affixesStay: true});
    });

  </script>

</body>
</html>