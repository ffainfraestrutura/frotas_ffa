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
$tipo = $_SESSION['tipo'];

if($perfil==''){
  $perfil = $_GET['perfil'];
}

if($matricula1==''){
  $matricula1 = $_GET['mat'];
}

/*if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
} elseif ($perfil <> '4' && $perfil == '5'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}elseif ($matricula1 == '160030'||$matricula1 == '410109'||$matricula1 == '500422'||$matricula1 == '501285'||$matricula1 == '410039'||$matricula1== '501640'||$matricula1 == '500459' || $matricula1=='411425' || $matricula1 == '003931'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
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

  <title> Importar Multas </title>

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

      <div class="container-fluid px-4 pb-5" style="width: 100%;"> 
        <h1 class="h1 pt-2 pb-2 ms-3"> Importar Novas Multas </h1>

        <!-- div do formulario -->
        <div style="width: 80%;" class='mb-5'>
          <form method="post" action="./control/importmultanova.php" enctype="multipart/form-data" style="width: 90%;" class="mb-3 m-auto">
          <?php print "<input type='hidden' name='matr_autor' id='matr_autor' required value='$matricula1'>"; ?>

            <div class="col-md-12 d-flex justify-content-start">
              <!--div importação -->
              <div class="ms-2 d-flex col-sm-8 flex-column">
                <label for="arquivo" class="form-label">Selecione o arquivo:</label>
                  <input class="form-control" type="file" name="arquivo" accept=".xls, .xlsx"  required>
              </div>
            </div>

            <!-- div texto campos obrigatório -->
            <div style="color: red;" class="pt-2">
              * Campos obrigatórios.
            </div>

            <!-- div botão -->
            <div class="mt-3 pb-2 d-flex col-sm-12 justify-content-start">
              <div>
                <input id="btnEnviar" class="btn btn-success" type='submit' value='Confirmar cadastro'>
              </div>

              <div class="ms-5">
                <a class="btn btn-danger" onclick="window.close();"> Voltar </a>
              </div>

              <div class="ms-5">
                <a class="btn btn-secondary" href='./control/docs/modelos/ImportModeloMultas.xlsx'> Modelo Excel </a>
              </div>
            </div>
          </form>

          <div class='mb-2'>
            <h5 class="ms-5" style='color: red;'>Antes de enviar o arquivo, confira se ele segue as recomendações abaixo. Não seguir as referidas recomendações pode acarretar em erro no envio, e/ou insconsitência nos dados, e/ou não inserção do auto de infração no sistema:</h5>
            
            <p class="ms-5 font-italic" style="font-size: 13px">- Envie arquivos no formato .xlsx (Excel) com apenas uma planilha e sem filtros. Envie arquivos com, no máximo, 1000 registros (linhas) na planilha.</p>

            <p class="ms-5 font-italic" style="font-size: 13px">- Placas não cadastradas no sistema não serão salvas.</p>

            <p class="ms-5 font-italic" style="font-size: 13px;">- Envie os dados das colunas DATA HORA CADASTRO no formato DD/MM/AAAA HH:MM:SS.</p>

            <p class="ms-5 font-italic" style="font-size: 13px;">- Envie os dados da coluna DATA HORA INFRAÇÃO no formato DD/MM/AAAA HH:MM.</p>

            <p class="ms-5 font-italic" style="font-size: 13px;">- Os autos de infração já incluídos no sistema anteriormente, não serão atualizados. Os autos de infração ainda não incluídos no sistema, serão incluídos.</p>

            <p class="ms-5 font-italic" style="font-size: 13px;">- Autos de infração com menos de 8 dígitos não serão inseridos.</p>

            <p class="ms-5 font-italic" style="font-size: 13px;">- Envie o campo UF com dois dígitos.</p>

            <p class="ms-5 font-italic" style="font-size: 13px;">- Envie o Código multa com cinco dígitos. Evite espaços em branco.</p>

            <p class="ms-5 font-italic" style="font-size: 13px;">- Evite espaços em branco na coluna VALOR.</p>

            <p class="ms-5 font-italic" style="font-size: 13px;">- No campo VALOR, não envie dados formatados com formatação contábil e nem com o ponto (.) que marca a casa do milhar.</p>

            <p class="ms-5 font-italic" style="font-size: 13px;">- Não envie dados com o conteúdo preenchido como ' - ' na coluna VALOR e nas colunas de Data.</p>

            <p class="ms-5 font-italic" style="font-size: 13px;">- Envie o campo  AP CONDUTOR DATA VENCIMENTO com o formato  DD/MM/AAAA HH:MM:SS.</p>

            <p class="ms-5 font-italic" style="font-size: 13px;">- Envie o nome do condutor indicado com a mesma grafia registrada no Aniel.</p>

            <p class="ms-5 font-italic" style="font-size: 13px;">- Os valores de pontuação aceitos serão 3 (infração leve), 4 (infração média), 5 (infração grave), 7 (infração gravíssima).</p>

            <p class="ms-5 font-italic" style="font-size: 13px;">- Os valores de gravidade aceitos serão "LEVE" ou "leve", "MEDIA" ou "media", "GRAVE" ou "grave","GRAVISSIMA" ou "gravissima". Não use acentos nesse campo. </p>

            <p class="ms-5 font-italic" style="font-size: 13px;">- Os valores de trâmite aceitos são 'Inserir infrator', 'Imprimir Recibo DP', 'Confirmar Desconto', 'Fazer Pagamento', 'Finalizado Frota', 'DP Finalizado', 'Finalizado Financeiro'. Utilize essa a mesma grafia (sem as aspas).</p>

            <p class="ms-5 font-italic" style="font-size: 13px;">- Os valores de etapa aceitos são "EM ABERTO", "AGUARDANDO ASSINATURA CONDUTOR", "CONCLUIDO", "MULTA INDEVIDA","COLABORADOR DEMITIDO", "MULTA ASSINADA", "ENVIADO PARA DESCONTO". Utilize essa a mesma grafia (sem as aspas).</p>

            <p class="ms-5 font-italic" style="font-size: 13px;">- Envie o centro de custo do condutor indicado com a mesma grafia registrada no Aniel.</p>

            <p class="ms-5 mb-5 font-italic" style="font-size: 13px;">- Dados textuais devem ser enviados sem o apóstrofo('). Se não se tem informação de algum dado, deixe a célula vazia.</p>
          </div>
        </div>

        
      </div>
    </main>

    <!--<footer class="mt-4 py-4 bg-light mt-auto" style="position: fixed; bottom: 0; start: 0; width: 100%;">
      <div class="container-fluid px-4">
          <div class="d-flex align-items-center justify-content-between small">
              <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
              <--<div>
                  <a href="#">Privacy Policy</a>
                                  &middot;
                  <a href="#">Terms &amp; Conditions</a>
              </div>--
          </div>
      </div>
    </footer>-->
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="../src/js/scripts.js"></script>

  <script>
    //mascaras de input
    $(document).ready(function(){
      //*$("#btnEnviar").on( "click", function() {
      $( "form" ).on( "submit", function( event ) {
        $("#btnEnviar").prop("disabled", true);
      });
    });

    //função para forçar a entrada de somente números
        
  </script>

</body>
</html>