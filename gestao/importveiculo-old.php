<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome1 = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula1 = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$tipo= $_SESSION['tipo'];

if($matricula1 == ''){
  $matricula1 = $_POST['matr_autor'];
}

if($perfil == ''){
  $perfil = $_POST['perfil_autor'];
}


/*if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
} elseif ($perfil == '0' || $perfil == '1' || $perfil == '2'|| $perfil == '3'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}elseif($matricula1 == '160030'||$matricula1 == '410109'||$matricula1 == '500422'||$matricula1 == '501285'||$matricula1 == '410039'||$matricula1 == '501640'||$matricula1 == '500459'){
  
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.close();</script>";
} /*elseif ($perfil <> '5'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}*/

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula1;
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo']= $tipo;

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

  <title> Import de Veículo </title>

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
        <h1 class="h1 pt-2 pb-2 ms-3"> Importação de Veículos </h1>

        <!-- div do formulario https://ffa.painel-telecom.com:4545/frotas/gestao/control/importarveiculo-teste.php target='_blank'-->
        <div style="width: 80%;">
          
          <form method="post" action="./control/importarveiculo.php" enctype="multipart/form-data" style="width: 90%;" class="mb-1 m-auto">          

            <div class="col-md-12 d-flex justify-content-start">
              <!--div importação -->
              <?php print"
              <input type='hidden' name='matr_autor' value='$matricula1'>";?>
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
                <a class="btn btn-danger" onclick="window.close();"> Voltar </a>
              </div>
              <!--href='./listagemveiculos.php'-->

              <div class="ms-5">
                <a class="btn btn-secondary" href='./control/docs/modelos/ImportModeloVeiculos.xlsx'> Modelo Excel </a>
              </div>
            </div>
          </form>

          <p class="ms-5 font-italic" style="font-size: 13px; font-style: italic;">Substituia as anotações no modelo pelos dados corretos. Para data e dados númericos, lembre-se utilizar a apóstrofo ('). Porém, evite o uso do apóstrofo em nomes e outros dados textuais.</p>

          <p class="ms-5 font-italic" style="font-size: 13x; font-style: italic;">Caso não possua informação para preencher o campo, deixe a célula em branco (lembre-se de apagar o conteúdo pré-preenchido do modelo).</p>

          <p class="ms-5 font-italic" style="font-size: 13px; font-style: italic;">Envie arquivos no formato .xlsx (Excel) com apenas uma planilha e sem filtros.</p>
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