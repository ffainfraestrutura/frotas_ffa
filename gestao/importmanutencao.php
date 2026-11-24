<?php
date_default_timezone_set('America/Sao_Paulo');

// Defina o tempo de vida da sessão para 1 hora
$lifetime = 3600; // 1 hora em segundos

// Configurar os parâmetros do cookie da sessão
session_set_cookie_params($lifetime);

// Iniciar a sessão
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$tipo= $_SESSION['tipo'];

if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
} elseif ($perfil == '0' || $perfil == '1' || $perfil == '2'|| $perfil == '3'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}elseif ($matricula == '160030'||$matricula == '410109'||$matricula == '500422'||$matricula == '501285'||$matricula == '410039'||$matricula== '501640'||$matricula == '500459' || $matricula1=='411425' || $matricula1 == '003931'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo']= $tipo;

include"../conecta2.php";
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

  <title> Import de Multas </title>

  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />

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
        <h1 class="h1 pt-2 pb-2 ms-3"> Importação de Manutencao </h1>

        <!-- div do formulario --> <!-- action="./control/importarmulta.php" -->
        <div style="width: 80%;">
          <form method="post" action="./control/importarmanutencao.php" enctype="multipart/form-data" style="width: 90%;" class="pb-5 mb-5 m-auto">
            <?php print "
              <input type='hidden' name='mat_autor' value='$matricula'>";
            ?>

            <div class="col-md-12 d-flex justify-content-start">
              <!--div importação -->
              <div class="ms-2 d-flex col-sm-8 flex-column">
                <label for="arquivo" class="form-label">Selecione o arquivo:</label>
                  <input class="form-control" type="file" id="arquivo" name="arquivo" accept=".xls, .xlsx"  required>
              </div>
            </div>

            <!-- div texto campos obrigatório -->
            <div style="color: red;" class="pt-2">
              * Campos obrigatórios.
            </div>

            <!-- div botão -->
            <div class="mt-3 pb-2 d-flex col-sm-12 justify-content-start">
              <div>
                <input class="btn btn-success" type='submit' value='Confirmar cadastro'>
              </div>

              <div class="ms-5">
                <a class="btn btn-danger" href='./listagemveiculos.php'> Cancelar cadastro </a>
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="../src/js/scripts.js"></script>
  <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>-->
  <!--<script src="assets/demo/chart-area-demo.js"></script>
  <script src="assets/demo/chart-bar-demo.js"></script>
  <script src="../src/js/simple-datatable.js" crossorigin="anonymous"></script>
  <script src="../src/js/datatables-simple-demo.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>   
        
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>-->

  <script>
    //mascaras de input
    $(document).ready(function(){
      $('#anofabric').mask('0000');
      $('#anomodelo').mask('0000');
      $('#renavam').mask('00000000000');
      $('#nummotor').mask('000000'); 

      $("#chassi").on("input", function(){
        $(this).val($(this).val().toUpperCase());
      });

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
    });

    //função para forçar a entrada de somente números
    function onlynumber(evt) {
     var theEvent = evt || window.event;
     var key = theEvent.keyCode || theEvent.which;
     key = String.fromCharCode( key );
     //var regex = /^[0-9.,]+$/;
     var regex = /^[0-9.]+$/;
      if( !regex.test(key) ) {
        theEvent.returnValue = false;
        if(theEvent.preventDefault) theEvent.preventDefault();
      }
    };

    //função para habilitar ou desabilitar input
    function verifica(value){
      var locadora = document.getElementById("locador");

      if(value == 'locado'){
        locadora.disabled = false;
      }else {
        locadora.disabled = true;
      }
    };

    /*function verificaPlaca() {
      var regex = '[A-Z]{3}[0-9][0-9A-Z][0-9]{2}';
      var placa = window.getElementById('placa');
      
      if (!placa.match(regex)) {
        return false;
      }
    }*/
    

    /*function teste() {
      return onlynumber(); 

      if(this.value.length==4) return false;
    }*/
  </script>

</body>
</html>