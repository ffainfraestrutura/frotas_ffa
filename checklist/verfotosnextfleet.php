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

/*if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
} /*elseif ($perfil<>'4'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}*/

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula1;
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;

$idtbvistoria = isset($_REQUEST['idtbvistoria']) ? trim((string)$_REQUEST['idtbvistoria']) : '';
$placa = isset($_REQUEST['placa']) ? trim((string)$_REQUEST['placa']) : '';
$datavistoria = isset($_REQUEST['datavistoria']) ? trim((string)$_REQUEST['datavistoria']) : '';
$idnextfleet = isset($_REQUEST['idnextfleet']) ? trim((string)$_REQUEST['idnextfleet']) : '';

if(!empty($placaveic)){
  if(strpos($placaveic, "-") !== false){
    $placacorreta = str_replace("-", "", $placaveic);
  } else {
    $placacorreta = str_replace(" ", "", $placaveic);
  }
}

$data = date('Y-m-d');

include"conecta.php";
include"../conecta03.php";
//include_once"./func/log.php";
header("Content-type: text/html; charset=utf-8");

function arquivoRelacionadoVistoria($nomeArquivo, $idnextfleet, $placa, $idtbvistoria) {
  $nomeArquivo = (string)$nomeArquivo;

  if ($idnextfleet !== '' && stripos($nomeArquivo, $idnextfleet) !== false) {
    return true;
  }

  if ($idtbvistoria !== '' && stripos($nomeArquivo, $idtbvistoria) !== false) {
    return true;
  }

  $placaNormalizada = strtolower(preg_replace('/[^a-z0-9]/i', '', (string)$placa));
  if ($placaNormalizada !== '') {
    $nomeNormalizado = strtolower(preg_replace('/[^a-z0-9]/i', '', $nomeArquivo));
    if (strpos($nomeNormalizado, $placaNormalizada) !== false) {
      return true;
    }
  }

  return false;
}

if ($idnextfleet === '' && $idtbvistoria !== '') {
  $idtbvistoriaEsc = mysqli_real_escape_string($con, $idtbvistoria);
  $sqlIdNextfleet = "SELECT idnextfleet FROM bdfrota.tbvistoria WHERE idtbvistoria = '$idtbvistoriaEsc' LIMIT 1";
  $resultadoIdNextfleet = mysqli_query($con, $sqlIdNextfleet);
  if ($resultadoIdNextfleet) {
    $rowIdNextfleet = mysqli_fetch_assoc($resultadoIdNextfleet);
    if (!empty($rowIdNextfleet['idnextfleet'])) {
      $idnextfleet = trim((string)$rowIdNextfleet['idnextfleet']);
    }
  }
}

$deveFiltrarArquivos = ($idnextfleet !== '' || $placa !== '' || $idtbvistoria !== '');

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, , shrink-to-fit=no" />
  <meta name="description" content="FFA" />
  <meta name="author" content="FFA" />
  <link rel="icon" type="image/png" href="../src/images/favicon.png"/>

  <title> Imagens Vistoria </title>

  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  
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

  <!--data tables - css e js-->
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />
  <script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>

  <!-- jquery e masks -->
  <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>

  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

  <style type="text/css">
    body{
      font-size: 12px;
    }

    .form-control, .btn, .form-select{
      font-size: 12px;
    }
  </style>

  

</head>
<body class="sb-nav-fixed">

  <!--nav-->
  <?php //include "./menu.php";  ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">

      <div class="container-fluid px-4 col-sm-12">
        <h3 class="h3 pt-3 pb-2 col-12 text-center"> Imagens Vistoria </h3>

        <!-- div form-->
        <div>
          <?php
            $imagens = array();
            $href = array();
            $hrefpdf = '';
  
  
            
            //Nome do diretório inicial            
            $dirin = 'nextfleetfotos/';
            $diretorio = dir($dirin);
            while($arquivo = $diretorio -> read()){
              if ($arquivo != '.' && $arquivo != '..'){

                if($deveFiltrarArquivos && arquivoRelacionadoVistoria($arquivo, $idnextfleet, $placa, $idtbvistoria)){
                  $imagens[] = $arquivo;
                  $href[] = './nextfleetfotos/'.$arquivo;
                }

                
              }
              
            }
            $diretorio -> close();

            

            // Nome do diretório inicial
            $dirin = 'nextfleet/';
            $diretorio1 = dir($dirin);
            while($arquivo = $diretorio1 -> read()){
              if ($arquivo != '.' && $arquivo != '..'){

                if($deveFiltrarArquivos && arquivoRelacionadoVistoria($arquivo, $idnextfleet, $placa, $idtbvistoria)){
                  $hrefpdf = $arquivo;
                }
                
              }
            }
            $diretorio1 -> close();

            $pdf = '';
            if($hrefpdf !== ''){
              $pdf = "<td><a href='nextfleet/$hrefpdf' target='_blank'><button class='btn btn-success'> Visualizar PDF</button></a> </td>";
            }
              
          ?>
        </div>

        <?php               

        print "
        <div class='mt-4 border-top border-dark pt-2'>
          <!--div informações do condutor-->
          <div class='mt-4 pt-2'>
            <h6> Informações do veículo </h6>

            <!--div placa --> 
            <div class='d-flex mt-2'>
              <p> Placa: $placa</p>
              <!--<p class='ms-3 '> Data da Vistoria: $datavistoriareal</p>-->
              
            </div>
          </div>
        </div>

        <div class='mt-2'>         
            
          <div class='d-flex mt-2 flex-wrap'>";

        for ($i=0; $i < count($imagens) ; $i++) { 
        print "
            <!--<div class='row mt-2'>  -->              
              <img class='ms-1 mt-1' src='$href[$i]' width='290px' height='290px'>             
            <!--</div>-->";        
        }
        print" 
          </div>
        </div>";

        print"
          <div class='d-flex col-3 justify-content-start align-items-center'>
            <!--div relatorio pdf-->
            <div class='ms-1 mt-3 d-flex justify-content-start align-items-center'>
              $pdf
            </div>
                          
          </div>
        </div>";
   
        ?>

      </div>

      </div>

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

  <script>window.jQuery || document.write('<script src="src/vendor/jquery/jquery.min.js"><\/script>')</script>

  <script type="text/javascript">
    $(document).ready(function () {
      //$('#tabela1').DataTable();

      /*$('#datavistoria').mask("00/00/0000");
      $('#validadecnh').mask("00/00/0000");
      $('#validadecnh2').mask("00/00/0000");*/

      //$(".data").mask("99/99/9999",{placeholder:" "});      

      $('#cnh').mask('00000000000', {reverse: true});
      //$('#cpf').mask('000.000.000-00', {reverse: true});

      $('#hodometro').mask('000000000');
      $('#niveltanque').mask('0/0');

      //$('#validadecnh').mask('0000-00-00');
      

      //formatação do texto no input
      $("#categoriacnh").on("input", function(){
        $(this).val($(this).val().toUpperCase());
      });

      /*$("#unidade").on("input", function(){
        $(this).val($(this).val().toUpperCase());
      });*/


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


      // quando for alterado
      /*$("#cpf").change(function() {      
        $("#nome").val()="";
        $("#cnh").val()="";
        $("#matricula").val()="";
        $("#categoriacnh").val()="";
        $("#validadecnh").val()="";
        $("#centrocusto").val()="";        
      });*/

      $('#tipo').change(function() {        
        if($("#tipo").val() == '2'){

          $("#statusveic").val('1');
          $("#statusveic option[value='1']").show();
          
          $("#statusveic option[value!='1']").hide();

            
        } else if($("#tipo").val() == '3'){
          
          $("#statusveic").val('2');
          $("#statusveic option[value!='1']").show();
          $("#statusveic option[value='1']").hide();
        }       
      });


  });
  </script>

</body>
</html>