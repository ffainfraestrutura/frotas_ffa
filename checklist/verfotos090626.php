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

$idtbvistoria = $_POST['idtbvistoria'];
$placa = $_POST['placa'];
$datavistoria = $_POST['datavistoria'];

if (!empty($placaveic)) {
  if (strpos($placaveic, "-") !== false) {
    $placacorreta = str_replace("-", "", $placaveic);
  } else {
    $placacorreta = str_replace(" ", "", $placaveic);
  }
}



$data = date('Y-m-d');

include "conecta.php";
include "../conecta03.php";
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
  <link rel="icon" type="image/png" href="../src/images/favicon.png" />

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
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

  <!--data tables - css e js-->
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />
  <script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8"
    src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>

  <!-- jquery e masks -->
  <script src='https://code.jquery.com/jquery-3.6.0.min.js'
    integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>

  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

  <script type="text/javascript"
    src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

  <style type="text/css">
    body {
      font-size: 12px;
    }

    .form-control,
    .btn,
    .form-select {
      font-size: 12px;
    }
    
    /* Loading spinner */
    .loading-spinner {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 9999;
    }
    
    .spinner-border {
      width: 3rem;
      height: 3rem;
    }
    
    /* Lazy load placeholder */
    img[loading="lazy"] {
      background: #f0f0f0;
      min-height: 290px;
      min-width: 290px;
    }
  </style>



</head>

<body class="sb-nav-fixed">

  <!-- Loading Spinner -->
  <div id="loadingSpinner" class="loading-spinner">
    <div class="spinner-border text-primary" role="status">
      <span class="visually-hidden">Carregando...</span>
    </div>
  </div>

  <!--nav-->
  <?php //include "./menu.php";  ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">

      <div class="container-fluid px-4 col-sm-12">
        <h3 class="h3 pt-3 pb-2 col-12 text-center"> Imagens Vistoria </h3>

        <!-- div form-->
        <div>
          <?php
          $sql = "SELECT tipo FROM bdfrota.tbvistoria WHERE idtbvistoria='$idtbvistoria';";
          $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
          $row = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
          $tipo = $row['tipo'];

          $sql2 = "SELECT  tipo FROM bdfrota.tbatipovist WHERE idtbatipovist = '$tipo' ";
          $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
          $row2 = mysqli_fetch_array($resultado2, MYSQLI_ASSOC);
          $tipofinal = $row2['tipo'];

          $sql1 = "SELECT * FROM bdfrota.tbvistoriafotos WHERE idtbvistoria='$idtbvistoria' AND idtbvistfotos NOT IN (40,41,42,43,44,45,47, 2639,2640,2641,2642,2643,2644,2645,2646,2647, 3198,3199,3201,4175) LIMIT 1;";

          $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));

          $row1 = mysqli_fetch_array($resultado1, MYSQLI_ASSOC);
          $idtbvistoria = $row1['idtbvistoria'];
          $placa = $row1['placa'];
          $datavistoria = $row1['datavistoriaf'];
          
          // Função para processar caminho de imagem (similar ao PDF mas com URLs HTTP)
          function getFilePathHTTP($file, $url_atual, $url_antiga, $pasta_atual, $pasta_antiga) {
            if (empty($file)) {
              return null;
            }
            
            // Separa o caminho por '/'
            $file_parts = explode("/", $file);
            
            // Pega apenas o nome do arquivo (última parte)
            $filename = end($file_parts);
            
            // Monta o caminho local para verificar existência
            $file_path_antiga = $pasta_antiga . $filename;
            $file_path_atual = $pasta_atual . $filename;
            
            // Verifica se o arquivo existe na pasta antiga ou na atual
            if (file_exists($file_path_antiga)) {
              return $url_antiga . $filename;
            } elseif (file_exists($file_path_atual)) {
              return $url_atual . $filename;
            } else {
              // Se não encontrar em nenhuma, tenta pela URL atual como fallback
              return $url_atual . $filename;
            }
          }

          // URLs base para os arquivos no servidor remoto
          $url_atual = 'http://ffasip.ddns.net:4242/frotas/checklist/docs/';
          $url_antiga = 'http://ffasip.ddns.net:4242/frotas/checklist/docs_old/';
          
          // Caminhos locais para verificar existência dos arquivos
          $pasta_atual = '/var/www/html/frotas/checklist/docs/';
          $pasta_antiga = '/var/www/html/frotas/checklist/docs_old/';

          // Processa todas as imagens
          $frontal = getFilePathHTTP($row1['frontal'], $url_atual, $url_antiga, $pasta_atual, $pasta_antiga);
          $traseira = getFilePathHTTP($row1['traseira'], $url_atual, $url_antiga, $pasta_atual, $pasta_antiga);
          $direita = getFilePathHTTP($row1['direita'], $url_atual, $url_antiga, $pasta_atual, $pasta_antiga);
          $esquerda = getFilePathHTTP($row1['esquerda'], $url_atual, $url_antiga, $pasta_atual, $pasta_antiga);
          $selfie = getFilePathHTTP($row1['selfie'], $url_atual, $url_antiga, $pasta_atual, $pasta_antiga);
          $cnh = getFilePathHTTP($row1['cnh'], $url_atual, $url_antiga, $pasta_atual, $pasta_antiga);
          $extra1 = getFilePathHTTP($row1['extra1'], $url_atual, $url_antiga, $pasta_atual, $pasta_antiga);
          $extra2 = getFilePathHTTP($row1['extra2'], $url_atual, $url_antiga, $pasta_atual, $pasta_antiga);
          $extra3 = getFilePathHTTP($row1['extra3'], $url_atual, $url_antiga, $pasta_atual, $pasta_antiga);
          $extra4 = getFilePathHTTP($row1['extra4'], $url_atual, $url_antiga, $pasta_atual, $pasta_antiga);
          $extra5 = getFilePathHTTP($row1['extra5'], $url_atual, $url_antiga, $pasta_atual, $pasta_antiga);
          $painel = getFilePathHTTP($row1['painel'], $url_atual, $url_antiga, $pasta_atual, $pasta_antiga);

          $datavistoria1 = explode(" ", $datavistoria);
          $datavistoria2 = explode("-", $datavistoria1[0]);
          $datavistoriareal = $datavistoria2[2] . "/" . $datavistoria2[1] . "/" . $datavistoria2[0];

          $hreff =  $frontal;
          $hreft =  $traseira;
          $hrefd =  $direita;
          $hrefe =  $esquerda;
          $hrefs =  $selfie;
          $hrefc =  $cnh;


          $href1 =  $extra1;
          $href2 =  $extra2;
          $href3 =  $extra3;
          $href4 =  $extra4;
          $href5 =  $extra5;

          $hrefp =  $painel;

          $link = 'http://ffasip.ddns.net:4242/frotas/checklist/fpdf/relatoriocomfotos.php';

          $relatoriopdf = "
              <form method='post' action='$link' target='_blank'>
                <input type='hidden' name='idtbvistoria' value='$idtbvistoria'/>                      
                <input type='hidden' name='datavistoria' value='$datavistoria'/>
                <input type='hidden' name='placa' value='$placacorreta'/>
                <button class='btn btn-success' type='submit'> Relatório PDF</button>
              </form>";

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
            <p class='ms-3 '> Data da Vistoria: $datavistoriareal</p>
            <p class='ms-3 '> Tipo da Vistoria: $tipofinal</p>
            
          </div>
        </div>

          
          <div class='col-12 d-flex justify-content-start align-items-center'>
            <!--div frontal-->
            <div class='mt-3 justify-content-start align-items-center'>
              <p> Foto Frontal:</p>            
              <img width='290px' height='290px' src='$hreff' loading='lazy' onerror=\"this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22290%22 height=%22290%22%3E%3Crect fill=%22%23ddd%22 width=%22290%22 height=%22290%22/%3E%3Ctext fill=%22%23999%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3ESem imagem%3C/text%3E%3C/svg%3E';\">           
            </div>

            <!--div traseira-->
            <div class='ms-1 mt-3 justify-content-start align-items-center'>
              <p> Foto Traseira:</p>            
              <img width='290px' height='290px' src='$hreft' loading='lazy' onerror=\"this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22290%22 height=%22290%22%3E%3Crect fill=%22%23ddd%22 width=%22290%22 height=%22290%22/%3E%3Ctext fill=%22%23999%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3ESem imagem%3C/text%3E%3C/svg%3E';\">           
            </div>

            <!--div lateral direita-->
            <div class='ms-1 mt-3 justify-content-start align-items-center'>
              <p> Foto Lateral Direita:</p>            
              <img width='290px' height='290px' src='$hrefd' loading='lazy' onerror=\"this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22290%22 height=%22290%22%3E%3Crect fill=%22%23ddd%22 width=%22290%22 height=%22290%22/%3E%3Ctext fill=%22%23999%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3ESem imagem%3C/text%3E%3C/svg%3E';\">           
            </div>

            <!--div lateral esquerda-->
            <div class='ms-1 mt-3 justify-content-start align-items-center'>
              <p> Foto Lateral Esquerda:</p>            
              <img width='290px' height='290px' src='$hrefe' loading='lazy' onerror=\"this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22290%22 height=%22290%22%3E%3Crect fill=%22%23ddd%22 width=%22290%22 height=%22290%22/%3E%3Ctext fill=%22%23999%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3ESem imagem%3C/text%3E%3C/svg%3E';\">           
            </div>
          </div>

          <div class='col-12 d-flex justify-content-start align-items-center'>
            <!--div selfie-->
            <div class='mt-3 justify-content-start align-items-center'>
              <p> Foto Selfie:</p>            
              <img width='290px' height='290px' src='$hrefs' loading='lazy' onerror=\"this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22290%22 height=%22290%22%3E%3Crect fill=%22%23ddd%22 width=%22290%22 height=%22290%22/%3E%3Ctext fill=%22%23999%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3ESem imagem%3C/text%3E%3C/svg%3E';\">           
            </div>

            <!--div cnh-->
            <div class='ms-1 mt-3 justify-content-start align-items-center'>
              <p> Foto CNH:</p>            
              <img width='290px' height='290px' src='$hrefc' loading='lazy' onerror=\"this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22290%22 height=%22290%22%3E%3Crect fill=%22%23ddd%22 width=%22290%22 height=%22290%22/%3E%3Ctext fill=%22%23999%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3ESem imagem%3C/text%3E%3C/svg%3E';\">           
            </div>";

        if (!empty($extra1)) {
          print "
            <!--div extra1 -->
            <div class='ms-1 mt-3 justify-content-start align-items-center'>
              <p> Foto Extra1:</p>            
              <img width='290px' height='290px' src='$href1' loading='lazy' onerror=\"this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22290%22 height=%22290%22%3E%3Crect fill=%22%23ddd%22 width=%22290%22 height=%22290%22/%3E%3Ctext fill=%22%23999%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3ESem imagem%3C/text%3E%3C/svg%3E';\">           
            </div>";
        }

        if (!empty($extra2)) {
          print "
            <!--div extra2-->
            <div class='ms-1 mt-3 justify-content-start align-items-center'>
              <p> Foto Extra 2:</p>            
              <img width='290px' height='290px' src='$href2' loading='lazy' onerror=\"this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22290%22 height=%22290%22%3E%3Crect fill=%22%23ddd%22 width=%22290%22 height=%22290%22/%3E%3Ctext fill=%22%23999%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3ESem imagem%3C/text%3E%3C/svg%3E';\">           
            </div>";
        }
        print "
          </div>
          <div class='col-12 d-flex justify-content-start align-items-center'>";

        if (!empty($extra3)) {
          print "
          
            <!--div extra3-->
            <div class='ms-1 mt-3 justify-content-start align-items-center'>
              <p> Foto Extra 3:</p>            
              <img width='290px' height='290px' src='$href3' loading='lazy' onerror=\"this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22290%22 height=%22290%22%3E%3Crect fill=%22%23ddd%22 width=%22290%22 height=%22290%22/%3E%3Ctext fill=%22%23999%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3ESem imagem%3C/text%3E%3C/svg%3E';\">           
            </div>";
        }

        if (!empty($extra4)) {
          print "

            <!--div extra4-->
            <div class='ms-1 mt-3 justify-content-start align-items-center'>
              <p> Foto Extra 4:</p>            
              <img width='290px' height='290px' src='$href4' loading='lazy' onerror=\"this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22290%22 height=%22290%22%3E%3Crect fill=%22%23ddd%22 width=%22290%22 height=%22290%22/%3E%3Ctext fill=%22%23999%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3ESem imagem%3C/text%3E%3C/svg%3E';\">           
            </div>";
        }

        if (!empty($extra5)) {
          print "

            <!--div extra5-->
            <div class='ms-1 mt-3 justify-content-start align-items-center'>
              <p> Foto Extra 5:</p>            
              <img width='290px' height='290px' src='$href5' loading='lazy' onerror=\"this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22290%22 height=%22290%22%3E%3Crect fill=%22%23ddd%22 width=%22290%22 height=%22290%22/%3E%3Ctext fill=%22%23999%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3ESem imagem%3C/text%3E%3C/svg%3E';\">           
            </div>";
        }

        if (!empty($painel)) {
          print "

            <!--div extra5-->
            <div class='ms-1 mt-3 justify-content-start align-items-center'>
              <p> Painel</p>            
              <img width='290px' height='290px' src='$hrefp' loading='lazy' onerror=\"this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22290%22 height=%22290%22%3E%3Crect fill=%22%23ddd%22 width=%22290%22 height=%22290%22/%3E%3Ctext fill=%22%23999%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3ESem imagem%3C/text%3E%3C/svg%3E';\">           
            </div>";
        }

        //if(!empty($extra3)){
        print "
          </div>";

        //}
        
        print "
          <div class='d-flex col-3 justify-content-start align-items-center'>
            <!--div relatorio pdf-->
            <div class='ms-1 mt-3 d-flex justify-content-start align-items-center'>
              $relatoriopdf
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    crossorigin="anonymous"></script>
  <script src="../src/js/scripts.js"></script>

  <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>-->


  <script type='text/javascript' src='/var/www/html/presenca/src/vendor/bootstrap/js/bootstrap.min.js'></script>

  <script>window.jQuery || document.write('<script src="src/vendor/jquery/jquery.min.js"><\/script>')</script>

  <script type="text/javascript">
    // Ocultar loading spinner quando a página carregar
    window.addEventListener('load', function() {
      document.getElementById('loadingSpinner').style.display = 'none';
    });
    
    $(document).ready(function () {
      //$('#tabela1').DataTable();

      /*$('#datavistoria').mask("00/00/0000");
      $('#validadecnh').mask("00/00/0000");
      $('#validadecnh2').mask("00/00/0000");*/

      //$(".data").mask("99/99/9999",{placeholder:" "});      

      $('#cnh').mask('00000000000', { reverse: true });
      //$('#cpf').mask('000.000.000-00', {reverse: true});

      $('#hodometro').mask('000000000');
      $('#niveltanque').mask('0/0');

      //$('#validadecnh').mask('0000-00-00');


      //formatação do texto no input
      $("#categoriacnh").on("input", function () {
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
          if (val.length > 4 && isNumeric) {
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

      $('#tipo').change(function () {
        if ($("#tipo").val() == '2') {

          $("#statusveic").val('1');
          $("#statusveic option[value='1']").show();

          $("#statusveic option[value!='1']").hide();


        } else if ($("#tipo").val() == '3') {

          $("#statusveic").val('2');
          $("#statusveic option[value!='1']").show();
          $("#statusveic option[value='1']").hide();
        }
      });


    });
  </script>

</body>

</html>