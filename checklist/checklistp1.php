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

if(empty($perfil)){
  $perfil = $_POST['perfil'];
}

if(empty($matricula1)){
  $matricula1 = $_POST['matrvist'];
}

if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
} /*elseif ($perfil<>'4'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}*/

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula1;
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;

$placaveic = $_POST['placa'];

if(strpos($placaveic, "-") !== false){
  $placacorreta = str_replace("-", "", $placaveic);
} else {
  $placacorreta = str_replace(" ", "", $placaveic);
}

$cpf1 = $_POST['cpf1'];
$cpf2 = $_POST['cpf'];

$data = date('Y-m-d');

include"../conecta03.php";
//include_once"./func/log.php";
header("Content-type: text/html; charset=utf-8");

/*if(file_exists("http://ffasip.ddns.net:4242/assinaturas/docs/colass/$matricula1.png") == false){
  print "
  <script language='javascript' type='text/javascript'> 
    window.alert('Você não possui assinatura cadastrada. Por favor, procure o DP.$matricula1');
    window.location=\"http://ffasip.ddns.net:4242/frotas-teste/checklist/checklistinicio.php\";    
  </script>";
}*/

$sqla= "SELECT placa FROM `bdfrota-teste`.tbveiculo WHERE placa ='".$placacorreta."'; ";
$resultadoa= mysqli_query($con, $sqla);
$linha = mysqli_num_rows($resultadoa);
if($linha == 0){
  echo"<script language='javascript' type='text/javascript'>alert('Placa não encontrada no sistema.');window.location=\"./checklistinicio.php\"</script>";
}

$sqla1 = "SELECT * FROM `bdfrota-teste`.tbmanprev WHERE placa ='$placacorreta'";
$resultadoa1= mysqli_query($con, $sqla1) or die(mysqli_error($con));
$linhasa1 = mysqli_num_rows($resultadoa1);

if($linhasa1>0){
  //SELECT idtbmanprev, status, placa FROM `bdfrota-teste`.tbmanprev where idtbmanprev=(select max(idtbmanprev) FROM `bdfrota-teste`.tbmanprev WHERE placa ='$placacorreta')
  $sqla2= "SELECT max(idtbmanprev) FROM `bdfrota-teste`.tbmanprev WHERE placa ='$placacorreta'";
  $resultadoa2= mysqli_query($con, $sqla2) or die(mysqli_error($con));
  $rowa2=mysqli_fetch_array($resultadoa2, MYSQLI_BOTH);
    $maxidtbmanprev = utf8_encode($rowa2[0]);//|| $statusman <> null

  $sqla3= "SELECT status FROM `bdfrota-teste`.tbmanprev WHERE idtbmanprev ='$maxidtbmanprev'";
  $resultadoa3= mysqli_query($con, $sqla3) or die(mysqli_error($con));
  $rowa3=mysqli_fetch_array($resultadoa3, MYSQLI_BOTH);
    $statusman = utf8_encode($rowa3[0]);

  if($statusman == 'ABERTO'){
    
    echo"<script language='javascript' type='text/javascript'>alert('Veículo em manutenção. Vistoria não autorizada. Status da Manutenção: $statusman');window.location=\"./checklistinicio.php\"</script>";
  }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, , shrink-to-fit=no" />
  <meta name="description" content="FFA" />
  <meta name="author" content="FFA" />
  <link rel="icon" type="image/png" href="../src/images/favicon.png"/>

  <title> Checklist - Passo 1 </title>

  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

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
    @media only screen and (max-width: 567px) {
      input[type="date"]::-webkit-inner-spin-button,
      input[type="date"]::-webkit-calendar-picker-indicator {
          display: none;
          -webkit-appearance: none;
      }
    }
  </style>
</head>

<body class="sb-nav-fixed">

  <!--nav-->
  <?php include "./menu.php";  ?>

  <div id="layoutSidenav_content" >

    <main style="width: 100%;" class="mb-2">

      <div class="container-fluid px-4 col-sm-12">
        <h1 class="h1 pt-3 pb-2 col-12 text-center"> Vistoria do Veículo </h1>
        <!-- div form-->
        <div id='div1'>

        <?php
        
          $sql1="SELECT max(idtbvistoria) FROM `bdfrota-teste`.tbvistoria where placa='$placacorreta'";
          $resultado1=mysqli_query($con, $sql1) or die(mysqli_error($con));
          $row1=mysqli_fetch_array($resultado1, MYSQLI_BOTH);
            $idtbvistoria=$row1[0];

          $sql2="SELECT * FROM `bdfrota-teste`.tbvistoria where idtbvistoria='$idtbvistoria'"; 
          $resultado2=mysqli_query($con, $sql2) or die(mysqli_error($con));
          $row2=mysqli_fetch_array($resultado2, MYSQLI_BOTH);
            $datavistoria=$row2['datavistoria'];
            $estado=$row2['estado'];
            $avaria=$row2['avaria'];
            $hodometro=$row2['hodometro'];
            $documentacao=$row2['documentacao'];
            $niveltanque = $row2['niveltanque'];

            $teto=$row2['teto'];
            $tetoesp=$row2['tetoesp'];
            $tetoesq=$row2['tetoesq'];
            $tetodir=$row2['tetodir'];
            $frente=$row2['frente'];
            $capo=$row2['capo'];
            $parabrisa=$row2['parabrisa'];
            $farolesq=$row2['farolesq'];
            $faroldir=$row2['faroldir'];
            $parachoque=$row2['parachoque'];
            $grade=$row2['grade'];
            $latesq=$row2['latesq'];
            $paralamaesq=$row2['paralamaesq'];
            $retrovesq=$row2['retrovesq'];
            $cxaresq=$row2['cxaresq'];
            $ptdiantesq=$row2['ptdiantesq'];
            $pttrasesq=$row2['pttrasesq'];
            $latdir=$row2['latdir'];
            $paralamadir= $row2['paralamadir'];
            $retrovdir = $row2['retrovdir'];
            $cxardir = $row2['cxardir'];
            $ptdiantdir = $row2['ptdiantdir'];
            $pttrasdir = $row2['pttrasdir'];
            $traseira =  $row2['traseira'];
            $lantesq = $row2['lantesq'];
            $lantdir = $row2['lantdir'];
            $tmpmala = $row2['tmpmala'];
            $parachoquet = $row2['parachoquet'];
            $itinterno = $row2['itinterno'];
            $painel = $row2['painel'];
            $som = $row2['som'];
            $bancos = $row2['bancos'];
            $ilumint = $row2['ilumint'];
            $tmpbag = $row2['tmpbag'];
            $retrovint = $row2['retrovint'];
            $tapetes = $row2['tapetes'];
            $pneus = $row2['pneus'];
            $step = $row2['step'];
            $marcapneus = $row2['marcapneus'];
            $kitstep = $row2['kitstep'];
            $calotas = $row2['calotas'];
            $bateria = $row2['bateria'];
            $safecar = $row2['safecar'];
            $limpint = $row2['limpint'];
            $limpext = $row2['limpext'];
            $idnextfleet = $row2['idnextfleet'];
            //$idnextfleet = '1505';

          $pdf = 'https://ffa.painel-telecom.com:4545/frotas/checklist/nextfleet/checklist_ID_'.$idnextfleet.'_'.$placacorreta.'.pdf';

          if($avaria=='1'){
            $avaria= 'SIM';
          } elseif($avaria=='0'){
            $avaria= 'NÃO';
          }

          $sql3 = "SELECT matcond, placa, modelo, anofabr, statusvel, unidade FROM `bdfrota-teste`.tbveiculo WHERE placa='$placacorreta';";
          $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));
          $row3 = mysqli_fetch_array($resultado3, MYSQLI_ASSOC);
            $matrcond = $row3['matcond'];
            $placa = $row3['placa'];
            $idmodelo = $row3['modelo'];
            $anofabricacao = $row3['anofabr'];
            $unidade = $row3['unidade'];
            //$centrocusto = utf8_encode($row3['ccusto']);
            //$codfilial= $row3['codfilial'];
            //$codempresa=$row3['codempresa'];
          
          $sql4="SELECT modelo FROM `bdfrota-teste`.tbmodeloveic WHERE idtbmodeloveic='".$idmodelo."' ";
          $res4= mysqli_query($con, $sql4) or die(mysqli_error($con));
          $row4= mysqli_fetch_array($res4, MYSQLI_BOTH);
            $modelo = utf8_encode($row4['modelo']);

          $sql5="SELECT * FROM bdaniel.tbfilial WHERE codfilial='$codfilial' AND codempresa='$codempresa' ";
          $res5= mysqli_query($con, $sql5) or die(mysqli_error($con));
          $row5= mysqli_fetch_array($res5, MYSQLI_BOTH);
            $filial = utf8_encode($row5['nome']);
          
          if($cpf1=='' && $cpf==''){
            $d = 'd-none';
          }elseif($cpf2<>''){
            $d = 'd-none';
          }else{
            $dd = 'd-none';
            
          }
          
          if(!empty($matrcond)){
            include "checlistcommatricula.php";
            
            
          }else{
            include "checlistsemmatricula.php";
            
          }
          
         
        ?>


        </div>

      </div>

      <?php include"checklistp2new.php"; ?>
    
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

  <script>window.jQuery || document.write('<script src="src/vendor/jquery/jquery.min.js"><\/script>')</script>

  <script type="text/javascript">
    $(document).ready(function () {
      //$('#tabela1').DataTable();

      $('#datavistoria').mask("00/00/0000");
      $('#validadecnh').mask("00/00/0000");
      $('#validadecnh2').mask("00/00/0000");

      $(".data").mask("00/00/0000",{placeholder:"00/00/0000"});      

      $('#cnh').mask('00000000000', {reverse: true});
      //$('#cpf').mask('000.000.000-00', {reverse: true});

      $('#hodometro').mask('000000000');
      $('#niveltanque').mask('0/0');

      $('#validadecnh').mask('0000-00-00');
      $('#anofabricacao').mask('0000');

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

      /*$("#cpfachar").submit(function() {
        $("#divnome").removeClass('d-none');
        console.log('certo');
      });*/

      // quando for alterado
      $("#cpf").change(function() {      
      $("#nome").val()="";
      $("#cnh").val()="";
      $("#matricula").val()="";
      $("#categoriacnh").val()="";
      $("#validadecnh").val()="";
      $("#centrocusto").val()="";        
      });*/

      $('input:radio[name="teto"]').change(function() {
        if ($(this).val() == 'ok') {        
          $("#tetonaook").attr("hidden", true);

          $("#oktetoesp").attr("required", false);
          $("#oktetoesq").attr("required", false);
          $("#oktetodir").attr("required", false);

          $("#oktetoesp").attr("checked", true);
          $("#oktetoesq").attr("checked", true);
          $("#oktetodir").attr("checked", true);

          $(this).attr("checked", true);
          
        } else if ($(this).val() == 'naook') {
          $("#tetonaook").attr("hidden", false);
          /*$('#tetonaook').css('display', 'flex');*/

          $("#oktetoesp").attr("required", true);       
          $("#oktetoesq").attr("required", true);
          $("#oktetodir").attr("required", true);

          $("#oktetoesp").attr("checked", false);
          $("#oktetoesq").attr("checked", false);
          $("#oktetodir").attr("checked", false);

          $(this).attr("checked", true);
        }
      });

      $('input:radio[name="frente"]').change(function() {
        if ($(this).val() == 'ok') {        
          $("#frentenaook").attr("hidden", true);

          $("#okcapo").attr("required", false);
          $("#okparabrisa").attr("required", false);
          $("#okfesq").attr("required", false);
          $("#okfdir").attr("required", false);
          $("#okpc").attr("required", false);
          $("#okgrade").attr("required", false);

          $(this).attr("checked", true);

          $("#okcapo").attr("checked", true);
          $("#okparabrisa").attr("checked", true);
          $("#okfesq").attr("checked", true);
          $("#okfdir").attr("checked", true);
          $("#okpc").attr("checked", true);
          $("#okgrade").attr("checked", true);
          
        } else if ($(this).val() == 'naook') {
          $("#frentenaook").attr("hidden", false);
          /*$('#tetonaook').css('display', 'flex');*/

          $("#okcapo").attr("required", true);       
          $("#okparabrisa").attr("required", true);
          $("#okfesq").attr("required", true);
          $("#okfdir").attr("required", true);
          $("#okpc").attr("required", true);
          $("#okgrade").attr("required", true);

          $("#okcapo").attr("checked", false);
          $("#okparabrisa").attr("checked", false);
          $("#okfesq").attr("checked", false);
          $("#okfdir").attr("checked", false);
          $("#okpc").attr("checked", false);
          $("#okgrade").attr("checked", false);

          $(this).attr("checked", true);
        }
      });

      $('input:radio[name="latesq"]').change(function() {
        if ($(this).val() == 'ok') {        
          $("#latesqnaook").attr("hidden", true);
          
          $("#okplesq").attr("required", false);
          $("#okrtesq").attr("required", false);
          $("#okcxesq").attr("required", false);
          $("#okpdesq").attr("required", false);
          $("#okptesq").attr("required", false);

          $("#okplesq").attr("checked", true);
          $("#okrtesq").attr("checked", true);
          $("#okcxesq").attr("checked", true);
          $("#okpdesq").attr("checked", true);
          $("#okptesq").attr("checked", true);

          $(this).attr("checked", true);
          
        } else if ($(this).val() == 'naook') {
          $("#latesqnaook").attr("hidden", false);
          /*$('#tetonaook').css('display', 'flex');*/
          
          $("#okplesq").attr("required", true);       
          $("#okrtesq").attr("required", true);
          $("#okcxesq").attr("required", true);
          $("#okpdesq").attr("required", true);
          $("#okptesq").attr("required", true);

          $("#okplesq").attr("checked", false);
          $("#okrtesq").attr("checked", false);
          $("#okcxesq").attr("checked", false);
          $("#okpdesq").attr("checked", false);
          $("#okptesq").attr("checked", false);

          $(this).attr("checked", true);
        }
      });

      $('input:radio[name="latdir"]').change(function() {
        if ($(this).val() == 'ok') {        
          $("#latdirnaook").attr("hidden", true);

          $("#okpldir").attr("required", false);
          $("#okrtdir").attr("required", false);
          $("#okcxdir").attr("required", false);
          $("#okpddir").attr("required", false);
          $("#okptdir").attr("required", false);
          
          $(this).attr("checked", true);

          $("#okpldir").attr("checked", true);
          $("#okrtdir").attr("checked", true);
          $("#okcxdir").attr("checked", true);
          $("#okpddir").attr("checked", true);
          $("#okptdir").attr("checked", true);
          
        } else if ($(this).val() == 'naook') {
          $("#latdirnaook").attr("hidden", false);
          /*$('#tetonaook').css('display', 'flex');*/
          
          $("#okpldir").attr("required", true);
          $("#okrtdir").attr("required", true);
          $("#okcxdir").attr("required", true);
          $("#okpddir").attr("required", true);
          $("#okptdir").attr("required", true);

          $("#okpldir").attr("checked", false);
          $("#okrtdir").attr("checked", false);
          $("#okcxdir").attr("checked", false);
          $("#okpddir").attr("checked", false);
          $("#okptdir").attr("checked", false);

          $(this).attr("checked", true);
        }
      });

      $('input:radio[name="traseira"]').change(function() {
        if ($(this).val() == 'ok') {        
          $("#traseiranaook").attr("hidden", true);
          
          $("#oklteq").attr("required", false);
          $("#okltdr").attr("required", false);
          $("#oktm").attr("required", false);
          $("#okpct").attr("required", false);

          $("#oklteq").attr("checked", true);
          $("#okltdr").attr("checked", true);
          $("#oktm").attr("checked", true);
          $("#okpct").attr("checked", true);

          $(this).attr("checked", true);
          
        } else if ($(this).val() == 'naook') {
          $("#traseiranaook").attr("hidden", false);
          
          $("#oklteq").attr("required", true);
          $("#okltdr").attr("required", true);
          $("#oktm").attr("required", true);
          $("#okpct").attr("required", true);

          $("#oklteq").attr("checked", false);
          $("#okltdr").attr("checked", false);
          $("#oktm").attr("checked", false);
          $("#okpct").attr("checked", false);        
          
          $(this).attr("checked", false);
        }
      });

      $('input:radio[name="itinterno"]').change(function() {
        if ($(this).val() == 'ok') {        
          $("#itinternonaook").attr("hidden", true);
          
          $("#okpainel").attr("required", false);
          $("#oksom").attr("required", false);
          $("#okbancos").attr("required", false);
          $("#okilumint").attr("required", false);
          $("#oktmpbag").attr("required", false);
          $("#okretrovint").attr("required", false);
          $("#oktapetes").attr("required", false);

          $("#okpainel").attr("checked", true);
          $("#oksom").attr("checked", true);
          $("#okbancos").attr("checked", true);
          $("#okilumint").attr("checked", true);
          $("#oktmpbag").attr("checked", true);
          $("#okretrovint").attr("checked", true);
          $("#oktapetes").attr("checked", true);

          $(this).attr("checked", true);
          
        } else if ($(this).val() == 'naook') {
          $("#itinternonaook").attr("hidden", false);
          
          $("#okpainel").attr("required", true);
          $("#oksom").attr("required", true);
          $("#okbancos").attr("required", true);
          $("#okilumint").attr("required", true);
          $("#oktmpbag").attr("required", true);
          $("#okretrovint").attr("required", true);
          $("#oktapetes").attr("required", true);

          $("#okpainel").attr("checked", false);
          $("#oksom").attr("checked", false);
          $("#okbancos").attr("checked", false);
          $("#okilumint").attr("checked", false);
          $("#oktmpbag").attr("checked", false);
          $("#okretrovint").attr("checked", false);
          $("#oktapetes").attr("checked", false);
          
          $(this).attr("checked", false);
        }
      });

      $('#tipo').change(function() {        
        if($("#tipo").val() == '2'){

          $("#statusveic").val('1');
          $("#statusveic option[value='1']").show();        
          $("#statusveic option[value!='1']").hide();

            
        } else if($("#tipo").val() == '3'){
          
          $("#statusveic").val('2');
          $("#statusveic option[value!='1']").show();
          $("#statusveic option[value='1']").hide();
          /*$("#statusveic option[value='2']").show();        
          $("#statusveic option[value!='2']").hide();*/
        }       
      });

      /*$("#primeiroform").on( "submit", function( event ) {
        $('#div1').attr('hidden', true);
        $('#div2').attr('hidden', false);
      }); */
      
      $("#primeirobotao").click (function() {
        
        var inputs = [];
        var radios = [];
        var valradios = [];
        var estadoform='incompleto';

        let marcados = $("input[type=radio]:checked").length;
        //console.log(marcados);
        
        /*for (let i = 0; i < vazios; i++) {
          var inputs = $('input[value=""]').attr('name');
          console.log(inputs);
          
        }*/

        var count = $(".inputtexto")
          .filter(function (input) {
            return $(this).val() == "";
        }).length;
        //

        /*if(marcados<45){
          estadoform='completo';
        }*/

        if((marcados<45) || (count>0)){

          alert('Há campos sem preenchimento.');
          
          console.log(count);
          console.log(marcados);

          //$('#div1').attr('hidden', false);
          //$('#div2').attr('hidden', true);

        }else{
          $('#div1').attr('hidden', true);
          $('#div2').attr('hidden', false);
        }
        
      });

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
        var btncnh = document.getElementById("cnh3");
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

  </script>

</body>
</html>