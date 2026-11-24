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

if ($perfil == null) {
  echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
} /*elseif ($perfil<>'4'){
echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}*/

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula1;
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;

$placaveic = $_POST['placa'];
//$placaveic = 'PZL5I97';

if (!empty($placaveic)) {
  if (strpos($placaveic, "-") !== false) {
    $placacorreta = str_replace("-", "", $placaveic);
  } else {
    $placacorreta = str_replace(" ", "", $placaveic);
  }
}

$cpf1 = $_POST['cpf1'];
$matricula_busca = $_POST['matricula_busca'];

$data = date('Y-m-d');

// Inicializar variáveis de condutor
$condutor2 = '';
$matrcond2 = '';
$codempresa2 = '';
$codfilial2 = '';
$ccusto2 = '';
$cnh2 = '';
$categoria2 = '';
$validade2a = array('');
$readonly_nome = '';
$readonly_cnh = '';
$readonly_categoria = '';
$readonly_validade = '';
$readonly_ccusto = '';
$readonly_cpf = '';
$readonly_matricula = '';

include "conecta.php";
//include_once"./func/log.php";
header("Content-type: text/html; charset=utf-8");

/*if(file_exists("../../assinaturas/docs/colass/$matricula1.png") == false){
  print "
  <script language='javascript' type='text/javascript'> 
    window.alert('Você não possui assinatura cadastrada. Por favor, procure o DP.');
    window.location=\"./checklistinicio.php\";    
  </script>";
}*/

//$sqla="SELECT ds_placa FROM bdfrota.tbcarrosxcolaborador WHERE ds_placa ='".$placacorreta."'; ";

$sqla = "SELECT placa FROM bdfrota.tbveiculo WHERE placa ='" . $placacorreta . "'; ";
$resultadoa = mysqli_query($con, $sqla);
$linha = mysqli_num_rows($resultadoa);
if ($linha == 0) {
  echo "<script language='javascript' type='text/javascript'>alert('Placa não encontrada no sistema.');window.location=\"./checklistinicio-manualDEV.php\"</script>";
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, , shrink-to-fit=no" />
  <meta name="description" content="FFA" />
  <meta name="author" content="FFA" />
  <link rel="icon" type="image/png" href="../src/images/favicon.png" />

  <title> Checklist Manual </title>

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

  <script>
    function disableButton(btn) {
      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processando...';
    }

    // Função a ser chamada quando o formulário é enviado
    function handleSubmit(event) {
      // Desabilita o botão de submit para evitar envios duplicados
      var submitBtn = event.target.querySelector('button[type="submit"]');
      disableButton(submitBtn);
    }
  </script>


  <style>
    input[readonly], input[readonly]:focus, textarea[readonly], textarea[readonly]:focus {
      background-color: #e9ecef !important;
      color: #495057;
      cursor: not-allowed;
    }
  </style>

</head>

<body class="sb-nav-fixed">

  <!--nav-->
  <?php include "./menu.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">

      <div class="container-fluid px-4 col-sm-12">
        <h1 class="h1 pt-3 pb-2 col-12 text-center"> Vistoria do Veículo </h1>

        <!-- div form-->
        <div>
          <?php
          //pegando informações da última vistoria
          $sql1 = "SELECT max(idtbvistoria) FROM bdfrota.tbvistoria where placa='$placacorreta'";
          $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));
          $row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
          $idtbvistoria = $row1[0];

          $sql1a = "SELECT idnextfleet FROM bdfrota.tbvistoria where idtbvistoria='$idtbvistoria'";
          $resultado1a = mysqli_query($con, $sql1a) or die(mysqli_error($con));
          $row1a = mysqli_fetch_array($resultado1a, MYSQLI_BOTH);
          $idnextfleet = $row1a[0];

          if (empty($idnextfleet)) {
            $sql2 = "SELECT * FROM bdfrota.tbvistoriafotos where idtbvistoria='$idtbvistoria'";
            $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));

            $linhas = mysqli_num_rows($resultado2);

            if ($linhas <= 0) {// parei aqui
              print "
                <form method='post' action='./fpdf/relatorio.php' target='_blank'>
                <input type='hidden' name='idtbvistoria' value='$idtbvistoria'>
                <button type='submit' class='btn btn-success'> Última Vistoria </button>

                </form>
                ";

              //$link = 'http://ffasip.ddns.net:4242/frotas/checklist/fpdf/relatorio.php';                
            } else {

              $row2 = mysqli_fetch_array($resultado2, MYSQLI_ASSOC);
              $extra1 = $row2['extra1'];
              $extra2 = $row2['extra2'];
              $extra3 = $row2['extra3'];
              $extra4 = $row2['extra4'];


              if (strpos($extra1, "pdf") !== false) {
                print "
                  <a href='./$extra1' target='_blank' class='btn btn-success'> Última Vistoria </a>";

                if (!empty($extra2)) {
                  print "
                  <a href='./$extra2' target='_blank' class='btn btn-success'> Imagem 1 </a>";
                }

                if (!empty($extra3)) {
                  print "
                  <a href='./$extra3' target='_blank' class='btn btn-success'> Imagem 2 </a>";
                }

                if (!empty($extra4)) {
                  print "
                  <a href='./$extra4' target='_blank' class='btn btn-success'> Imagem 3 </a>";
                }
              } else {
                print "
                  <form method='post' action='./fpdf/relatoriocomfotos.php' target='_blank'>
                  <input type='hidden' name='idtbvistoria' value='$idtbvistoria'>
                  <button type='submit' class='btn btn-success'> Última Vistoria </button>
                  </form>";
              }
            }



            //$link = 'http://ffasip.ddns.net:4242/frotas/checklist/fpdf/relatoriocomfotos.php';
          } else {
            print "
                <a class='nav-link' href='./checklist/gerarrelatorio.php'>Relatório de Vistoria por Placa</a>";
          }

          ?>
        </div>


        <?php
        //selecionando se tem condutor        
        $sql = "SELECT matcond, anofabr, modelo, basegestao FROM bdfrota.tbveiculo WHERE placa='$placacorreta'; ";
        $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
        $row = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
        $matrcond = $row['matcond'];
        $anofabr = $row['anofabr'];
        $idmodelo = $row['modelo'];
        $basegestaoatual = $row['basegestao'];

        $sql1 = "SELECT nome, cpf, ccusto, codfilial, codempresa FROM bdaniel.tbfuncionario WHERE matricula='$matrcond'; ";
        $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));
        $row1 = mysqli_fetch_array($resultado1, MYSQLI_ASSOC);
        $condutor = $row1['nome'];
        $cpf = $row1['cpf'];
        $ccusto = $row1['ccusto'];
        $codfilial = $row1['codfilial'];
        $codempresa = $row1['codempresa'];



        $sql2 = "SELECT numcnh, validade, categoria FROM bdfrota.tbcnh WHERE matricula ='$matrcond'; ";
        $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
        $row2 = mysqli_fetch_array($resultado2, MYSQLI_ASSOC);
        $cnh = $row2['numcnh'];
        $validade = $row2['validade'];
        $categoria = $row2['categoria'];

        $sql3 = "SELECT modelo FROM bdfrota.tbmodeloveic WHERE idtbmodeloveic='$idmodelo'; ";
        $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));
        $row3 = mysqli_fetch_array($resultado3, MYSQLI_ASSOC);
        $modelo = $row3['modelo'];

        $validade1 = explode(" ", $validade);


        //print $nome, $matricula, $id_colaborador, $cpf, $numcnh, $categoria, $validade;
        if ($cpf != '') {
          $cpf2 = $cpf;
        }

        /*if(!empty($condutor)){
          $d = 'd-none';
        }*/

        if (!empty($matrcond)) {//quando existe cond vinculado         
        
          print "
          <!--div informações do condutor-->
          <form method='post' action='./control/checklistp1-manual.php.php' enctype='multipart/form-data' onsubmit='handleSubmit(event)'>

          <div class='mt-4 border-top border-dark pt-2'>
            <h6> Informações do condutor </h6>

            <!--div cpf-->
            <div class='d-flex'>
              <!--<input type='hidden' name='placa' value='$placacorreta'>-->
                  
              <label class='col-3 col-form-label' for='cpf'> CPF:<span style='color: red;'>*</span> </label>
              <div class='col-7'>
                <input type='text' name='cpf' id='cpf' class='form-control' placeholder='CPF do Condutor' required value='$cpf2'>
              </div>
            </div>

            <!--div nome--> 
            <div class='row mt-2 col-12'>
              <label class='col-2 col-form-label' for='nome'> Nome:<span style='color: red;'>*</span> </label>
              <div class='col-10'>
                <input type='text' name='nome' id='nome' class='form-control' placeholder='Nome do Condutor' required value='$condutor'>
              </div>
            </div>

            <!--div matricula-->
            <div class='row mt-2 col-12'>
              <label class='col-3 col-form-label' for='matricula'> Matrícula:<span style='color: red;'>*</span> </label>
              <div class='col-9'>
                <input type='text' name='matricula' id='matricula' class='form-control' placeholder='Matricula do Condutor' required value='$matrcond'>
              </div>
            </div>                

            <!--div numero cnh-->
            <div class='row mt-2 col-12'>
              <label class='col-3 col-form-label' for='cnh'> CNH:<span style='color: red;'>*</span> </label>
              <div class='col-9'>
                <input type='text' name='cnh' id='cnh2' class='form-control' placeholder='CNH do Condutor' required value='$cnh'>
              </div>
            </div>

            <!--div categoria cnh-->
            <div class='row mt-2 col-12'>
              <label class='col-7 col-form-label' for='categoriacnh'> Categoria CNH:<span style='color: red;'>*</span> </label>
              <div class='col-5'>
                <input type='text' name='categoriacnh' id='categoriacnh2' class='form-control' placeholder='Categoria' required value='$categoria'>
              </div>
            </div>

            <!--div validade cnh-->
            <div class='row mt-2'>
              <label class='col-5 col-form-label' for='validadecnh'> Vencimento CNH:<span style='color: red;'>*</span> </label>
              <div class='col-6'>
                <input type='date' name='validadecnh' id='validadecnh2' value='$validade1[0]' class='data form-control' required>
              </div>
            </div>
                

            <div class='row mt-2'>
              <label class='col-5 col-form-label' for='centrocusto'> Centro de Custo: <span style='color: red;'>*</span> </label>
              <div class='col-6'>
                <input type='text' name='centrocusto' id='centrocusto' class='form-control' placeholder='Centro Custo' required value='$ccusto'>
              </div>
            </div>                       
          </div>";
          ?>

          <div class='row mt-2 d-none'>
            <label class='col-5 col-form-label' for='unidade'> Base gestão:<span style='color: red;'>*</span> </label>
            <div class='col-6'>
              <!--<input type='text' name='unidade' id='unidade' class='form-control' placeholder='Unidade' required>-->
              <select name='unidade' id='unidade' class='form-select' required>
                <option value=''> Selecione a opção </option>
                <?php
                //$sql3="SELECT * FROM bdaniel.tbfilial";
                //$sql="SELECT DISTINCT (unidade) FROM bdfrota.tbveiculo ORDER BY unidade;";
                $sql = "SELECT DISTINCT (basegestao) FROM bdfrota.tbveiculo WHERE basegestao NOT IN ('RJ', 'SP', 'PR') ORDER BY basegestao;";
                $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

                while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                  //$basegestao = $row['unidade'];
                  $basegestao = $row['basegestao'];
                  if ($basegestao != '') {
                    $selected = $basegestao == $basegestaoatual ? "selected" : "";
                    print "<option value='$basegestao' $selected> $basegestao </option>";
                  }
                }

                ?>
              </select>
            </div>
          </div>

          <?php print "

          <!--div informações do veiculo-->
          <div class='mt-4 border-top border-dark pt-2'>
            <h6> Informações do veículo </h6>

            <!--div placa --> 
            <div class='row mt-2'>
              <label class='col-5 col-form-label' for='placa'> Placa:<span style='color: red;'>*</span> </label>
              <div class='col-6'>
                <input type='text' name='placa' id='placa' class='form-control' placeholder='Placa do Veículo' required value='$placacorreta'>
              </div>
            </div>

            <!--div modelo -->
            <div class='row mt-2'>
              <label class='col-5 col-form-label' for='modelo'> Modelo:<span style='color: red;'>*</span> </label>
              <div class='col-6'>
                <input type='text' name='modelo' id='modelo' class='form-control' placeholder='Modelo do Veículo' required value='$modelo'>
              </div>
            </div>

            <!--div anofabricacao -->
            <div class='row mt-2'>
              <label class='col-5 col-form-label' for='anofabricacao'> Ano - fabricação:<span style='color: red;'>*</span> </label>
              <div class='col-6'>
                <input type='text' name='anofabricacao' id='anofabricacao' class='form-control' placeholder='Ano de fabricação' required value='$anofabr'>
              </div>
            </div>
          </div>

          

            <div class='mt-4 border-top border-dark pt-2'>
              <h6> Arquivos da vistoria </h6>

              <!--div pdf-->
              <div class='row mt-2'>
                <label class='col-5 col-form-label' for='pdfvist'> Vistoria Manual: <span style='color: red;'>*</span> </label>
                <div class='col-6'>
                  <input type='file' name='pdfvist' id='pdfvist' class='form-control' accept='.pdf' required>
                </div>
              </div>

              <!--div imagem1-->
              <div class='row mt-2'>
                <label class='col-5 col-form-label' for='imagem1'> Imagem 1: </label>
                <div class='col-6'>
                  <input type='file' name='imagem1' id='imagem1' class='form-control' accept='.jpg, .jpeg, .png, .pdf'>
                </div>
              </div>

              <!--div imagem2-->
              <div class='row mt-2'>
                <label class='col-5 col-form-label' for='imagem2'> Imagem 2: </label>
                <div class='col-6'>
                  <input type='file' name='imagem2' id='imagem2' class='form-control' accept='.jpg, .jpeg, .png, .pdf'>
                </div>
              </div>

              <!--div imagem3-->
              <div class='row mt-2'>
                <label class='col-5 col-form-label' for='imagem3'> Imagem 3: </label>
                <div class='col-6'>
                  <input type='file' name='imagem3' id='imagem3' class='form-control' accept='.jpg, .jpeg, .png, .pdf'>
                </div>
              </div>              
            </div>";
        } else {//quando não existe cond vinculado
        
          if (!empty($cpf1) || !empty($matricula_busca)) {
            // Remove formatação do CPF (ponto e traço)
            $cpf1_limpo = preg_replace('/[^0-9]/', '', $cpf1);
            
            // Monta a condição de busca
            if (!empty($matricula_busca)) {
              $condicao_busca = "matricula='$matricula_busca'";
            } else {
              $condicao_busca = "cpf='$cpf1_limpo'";
            }
            
            $sql5 = "SELECT nome, matricula, codempresa, codfilial, ccusto FROM bdaniel.tbfuncionario WHERE $condicao_busca /*AND status='Ativo'*/ AND status<>'Demitido' AND status<>'Afastado'; ";
            $resultado5 = mysqli_query($con, $sql5) or die(mysqli_error($con));
            $row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
            $condutor2 = $row5['nome'];
            $matrcond2 = $row5['matricula'];
            $codempresa2 = $row5['codempresa'];
            $codfilial2 = $row5['codfilial'];
            $ccusto2 = $row5['ccusto'];
            
            // Marcar campos como readonly apenas se têm dados preenchidos
            if (!empty($condutor2)) $readonly_nome = 'readonly';
            if (!empty($matrcond2)) $readonly_matricula = 'readonly';
            if (!empty($cpf1_limpo)) $readonly_cpf = 'readonly';
            if (!empty($ccusto2)) $readonly_ccusto = 'readonly';
            
            // Se encontrou pela matrícula, pegua o CPF para preenchimento
            if (!empty($matricula_busca) && !empty($matrcond2)) {
              $sql5a = "SELECT cpf FROM bdaniel.tbfuncionario WHERE matricula='$matricula_busca'";
              $resultado5a = mysqli_query($con, $sql5a) or die(mysqli_error($con));
              $row5a = mysqli_fetch_array($resultado5a, MYSQLI_BOTH);
              $cpf1 = $row5a['cpf'];
              if (!empty($cpf1)) $readonly_cpf = 'readonly';
            }

            $ln = mysqli_num_rows($resultado5);
            if ($ln <= 0) {
              echo "<script language='javascript' type='text/javascript'>alert('Condutor não cadastrado ou com status inválido (Ferias / Afastado)');window.location=\"./checklistinicio-manualDEV.php\"</script>";
            }

            $sql6 = "SELECT numcnh, categoria, validade FROM bdfrota.tbcnh WHERE matricula='$matrcond2' ";
            $resultado6 = mysqli_query($con, $sql6) or die(mysqli_error($con));
            $row6 = mysqli_fetch_array($resultado6, MYSQLI_BOTH);
            $cnh2 = $row6['numcnh'];
            $categoria2 = $row6['categoria'];
            $validade2 = $row6['validade'];
            
            // Marcar campos CNH como readonly apenas se têm dados preenchidos
            if (!empty($cnh2)) $readonly_cnh = 'readonly';
            if (!empty($categoria2)) $readonly_categoria = 'readonly';
            if (!empty($validade2)) $readonly_validade = 'readonly';

            $validade2a = explode(" ", $validade2);
            $validade2b = $validade2a[0];

            /*$validadecnh1= explode(" ",$validadecnh);
            $validadecnh2 = explode("-", $validadecnh1[0]);
            $validadecnhf = $validadecnh2[2]."/".$validadecnh2[1]."/".$validadecnh2[0];*/

            $sql7 = "SELECT * FROM bdaniel.tbfilial WHERE codfilial='$codfilial2' AND codempresa='$codempresa2' ";
            $resultado7 = mysqli_query($con, $sql7) or die(mysqli_error($con));
            $row7 = mysqli_fetch_array($resultado7, MYSQLI_BOTH);
            $filial2 = $row7['nome'];

          }

          /*$sql2 = "SELECT b.placa AS placa, b.modelo AS modelo, b.anofabr AS anofabr, b.statusvel AS statusvel FROM  bdfrota.tbveiculo b WHERE b.placa= '$placacorreta' ";

          $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));

          $row2 = mysqli_fetch_array($resultado2, MYSQLI_ASSOC);
            $placa = $row2['placa'];
            $idmodelo = $row2['modelo'];
            $anofabr = $row2['anofabr'];              

          $sql3="SELECT modelo FROM bdfrota.tbmodeloveic WHERE idtbmodeloveic='".$idmodelo."' ";
            $res3= mysqli_query($con, $sql3) or die(mysqli_error($con));
            $row3= mysqli_fetch_array($res3, MYSQLI_BOTH);
              $modelo = $row3['modelo'];*/

          print "
        <!--div informações do condutor-->       

        <div class='mt-4 border-top border-dark pt-2'>
          <h6> Informações do condutor </h6>

          <!--div cpf e matricula-->
          <div>
            <form method='post' action='./checklistp1-manual.php.php'>

              <p class=''>Preencha o campo <strong>CPF</strong> ou <strong>Matrícula</strong> e clique no ícone <span class='material-symbols-outlined mt-1'>arrow_forward</span>. As demais informações do condutor serão inseridas automaticamente.</p>
              <input type='hidden' name='placa' value='$placacorreta'>

              <div class='row mt-2'>
                <div class='col-md-6'>
                  <label class='col-form-label' for='cpf1'> CPF:<span style='color: red;'>*</span> </label>
                  <div class='d-flex'>
                    <input type='text' name='cpf1' id='cpf1' class='form-control' placeholder='CPF do Condutor' value='$cpf1' $readonly_cpf>
                  </div>
                </div>
                <div class='col-md-6'>
                  <label class='col-form-label' for='matricula_busca'> Matrícula:<span style='color: red;'>*</span> </label>
                  <div class='d-flex'>
                    <input type='text' name='matricula_busca' id='matricula_busca' class='form-control' placeholder='Matrícula do Condutor' value=\"<?php echo isset($matrcond2) && !empty($matrcond2) ? $matrcond2 : ''; ?>\" $readonly_matricula>
                    <button type='submit' style='background-color: transparent; border: none; cursor: pointer;' class='ms-2'><span class='material-symbols-outlined'>arrow_forward</span></button>
                  </div>
                </div>
              </div>
            </form>
          

          <form method='post' action='./control/checklistp1-manual.php.php' enctype='multipart/form-data' onsubmit='handleSubmit(event)'>
            <input type='hidden' name='placa' value='$placacorreta'>
            <input type='hidden' name='cpf' value='$cpf1'>

            <!--div nome--> 
            <div class='row mt-2 col-12'>
              <label class='col-2 col-form-label' for='nome'> Nome:<span style='color: red;'>*</span> </label>
              <div class='col-10'>
                <input type='text' name='nome' id='nome' class='form-control' placeholder='Nome do Condutor' required value=\"$condutor2\" $readonly_nome>
              </div>
            </div>

            <!--div matricula (oculta para envio, preenchida automaticamente)-->
            <input type='hidden' name='matricula' id='matricula' value=\"$matrcond2\">

            <!--div numero cnh-->
            <div class='row mt-2 col-12'>
              <label class='col-3 col-form-label' for='cnh'> CNH:<span style='color: red;'>*</span> </label>
              <div class='col-9'>
                <input type='text' name='cnh' id='cnh2' class='form-control' placeholder='CNH do Condutor' required value=\"$cnh2\" $readonly_cnh>
              </div>
            </div>

            <!--div categoria cnh-->
            <div class='row mt-2 col-12'>
              <label class='col-7 col-form-label' for='categoriacnh'> Categoria CNH:<span style='color: red;'>*</span> </label>
              <div class='col-5'>
                <input type='text' name='categoriacnh' id='categoriacnh2' class='form-control' placeholder='Categoria' required value=\"$categoria2\" $readonly_categoria>>>
              </div>
            </div>

            <!--div validade cnh-->
            <div class='row mt-2'>
              <label class='col-5 col-form-label' for='validadecnh'> Vencimento CNH:<span style='color: red;'>*</span> </label>
              <div class='col-6'>
                <input type='date' name='validadecnh' id='validadecnh2' value=\"$validade2a[0]\" class='data form-control' required $readonly_validade>
              </div>
            </div>
                

            <div class='row mt-2'>
              <label class='col-5 col-form-label' for='centrocusto'> Centro de Custo: <span style='color: red;'>*</span> </label>
              <div class='col-6'>
                <input type='text' name='centrocusto' id='centrocusto' class='form-control' placeholder='Centro Custo' required value=\"$ccusto2\" $readonly_ccusto>>>
              </div>
            </div>                       
          </div>";
          ?>
          <!--div unidade / base gestão ( a base gestão vai gerar a unidade no control) -->
          <?php //print"  <div class='row mt-2 $d'>"; ?>
          <div class='row mt-2'>
            <label class='col-5 col-form-label' for='unidade'> Base gestão:<span style='color: red;'>*</span> </label>
            <div class='col-6'>
              <!--<input type='text' name='unidade' id='unidade' class='form-control' placeholder='Unidade' required>-->
              <select name='unidade' id='unidade' class='form-select' required>
                <option value=''> Selecione a opção </option>
                <?php
                //$sql3="SELECT * FROM bdaniel.tbfilial";
                //$sql="SELECT DISTINCT (unidade) FROM bdfrota.tbveiculo ORDER BY unidade;";
                $sql = "SELECT DISTINCT (basegestao) FROM bdfrota.tbveiculo WHERE basegestao NOT IN ('RJ', 'SP', 'PR') ORDER BY basegestao;";
                $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

                while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                  //$basegestao = $row['unidade'];
                  $basegestao = $row['basegestao'];
                  if ($basegestao != '') {
                    print "<option value='$basegestao'> $basegestao </option>";
                  }
                }

                ?>
              </select>
            </div>
          </div>

          <?php print "
          <!--div informações do veiculo-->
          <div class='mt-4 border-top border-dark pt-2'>
            <h6> Informações do veículo </h6>

            <!--div placa --> 
            <div class='row mt-2'>
              <label class='col-5 col-form-label' for='placa'> Placa:<span style='color: red;'>*</span> </label>
              <div class='col-6'>
                <input type='text' name='placa' id='placa' class='form-control' placeholder='Placa do Veículo' required value='$placacorreta'>
              </div>
            </div>

            <!--div modelo -->
            <div class='row mt-2'>
              <label class='col-5 col-form-label' for='modelo'> Modelo:<span style='color: red;'>*</span> </label>
              <div class='col-6'>
                <input type='text' name='modelo' id='modelo' class='form-control' placeholder='Modelo do Veículo' required value='$modelo'>
              </div>
            </div>

            <!--div anofabricacao -->
            <div class='row mt-2'>
              <label class='col-5 col-form-label' for='anofabricacao'> Ano - fabricação:<span style='color: red;'>*</span> </label>
              <div class='col-6'>
                <input type='text' name='anofabricacao' id='anofabricacao' class='form-control' placeholder='Ano de fabricação' required value='$anofabr'>
              </div>
            </div>
          </div>          

        <div class='mt-4 border-top border-dark pt-2'>
          <h6> Arquivos da vistoria </h6>

          <!--div pdf-->
          <div class='row mt-2'>
            <label class='col-5 col-form-label' for='pdfvist'> Vistoria Manual: <span style='color: red;'>*</span> </label>
            <div class='col-6'>
              <input type='file' name='pdfvist' id='pdfvist' class='form-control' accept='.pdf' required>
            </div>
          </div>

          <!--div imagem1-->
          <div class='row mt-2'>
            <label class='col-5 col-form-label' for='imagem1'> Imagem 1: </label>
            <div class='col-6'>
              <input type='file' name='imagem1' id='imagem1' class='form-control' accept='.jpg, .jpeg, .png, .pdf'>
            </div>
          </div>

          <!--div imagem2-->
          <div class='row mt-2'>
            <label class='col-5 col-form-label' for='imagem2'> Imagem 2: </label>
            <div class='col-6'>
              <input type='file' name='imagem2' id='imagem2' class='form-control' accept='.jpg, .jpeg, .png, .pdf'>
            </div>
          </div>

          <!--div imagem3-->
          <div class='row mt-2'>
            <label class='col-5 col-form-label' for='imagem3'> Imagem 3: </label>
            <div class='col-6'>
              <input type='file' name='imagem3' id='imagem3' class='form-control' accept='.jpg, .jpeg, .png, .pdf'>
            </div>
          </div>              
        </div>";
        }

        ?>

        <!-- div informações checklist-->
        <div class='mt-4 border-top border-dark pt-2'>
          <h6> Informações da vistoria </h6>

          <div class='row mt-2'>
            <label class='col-5 col-form-label' for='tipo'> Tipo:<span style='color: red;'>*</span> </label>
            <div class='col-6'>
              <select name="tipo" id='tipo' class="form-select" required>
                <option value=''> Selecione a opção </option>
                <?php
                $sql = "SELECT * FROM bdfrota.tbatipovist /*WHERE idtbatipovist<>'1'*/;";
                $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                while ($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)) {
                  $idtbatipovist = $row['idtbatipovist'];
                  $tipo = $row['tipo'];

                  print "
              <option value='$idtbatipovist'> $tipo </option>";
                }
                ?>
              </select>
            </div>
          </div>

          <!--div vistoriador -->
          <?php
          $sql = "SELECT nome FROM bdaniel.tbfuncionario WHERE matricula = '$matricula1';";

          $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

          while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
            $nomevist = $row['nome'];
          }

          print "
        <div class='row mt-2'>
        <label class='col-5 col-form-label' for='vistoriador'> Vistoriador:<span style='color: red;'>*</span> </label>
        <div class='col-6'>
          <input type='hidden' name='matrvistoriador' value='$matricula1'>
          <input type='text' name='vistoriador' id='vistoriador' class='form-control' placeholder='Vistoriador' value='$nomevist' required>
        </div>
      </div>
        ";
          ?>

          <!--div data da vistoria -->
          <div class='row mt-2'>
            <label class='col-5 col-form-label' for='datavistoria'> Data da Vistoria:<span style='color: red;'>*</span>
            </label>
            <div class='col-6'>
              <input type='date' name='datavistoria' id='datavistoria' class='form-control' <?php print "max='$data'"; ?> required>
              <!--<input type='text' name='datavistoria' id='datavistoria' class='form-control' required>-->
            </div>
          </div>

          <!--div hora da vistoria -->
          <div class='row mt-2'>
            <label class='col-5 col-form-label' for='datavistoria'> Hora da Vistoria:<span style='color: red;'>*</span>
            </label>
            <div class='col-6'>
              <input type='time' name='horavistoria' id='horavistoria' class='form-control' required>
            </div>
          </div>

          <!--div tipo-->
          <div class='row mt-2'>
            <label class='col-5 col-form-label' for='tipo'>Status:<span style='color: red;'>*</span> </label>
            <div class='col-6'>
              <select name="statusveic" id='statusveic' class="form-select" required>
                <option value=''> Selecione a opção </option>
                <?php
                //$sql3="SELECT * FROM bdfrota.tbastatusvel WHERE idtbastatusvel in ('1','2','5','11', '18','36', '32')"; //idtbastatusvel, status
                $sql3 = "SELECT * FROM bdfrota.tbastatusvel WHERE idtbastatusvel NOT IN ('18','7','13','19','39') ORDER BY status";
                $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));

                while ($row3 = mysqli_fetch_array($resultado3, MYSQLI_ASSOC)) {
                  $idtbastatusvel = $row3['idtbastatusvel'];
                  $statusveic = $row3['status'];

                  $selected = ($statusvel == $idtbastatusvel) ? "selected=\"selected\"" : null;

                  print "<option value='$idtbastatusvel' $selected> $statusveic </option>";
                }

                ?>


              </select>
            </div>
          </div>

          <!--div estado geral -->
          <div class='column mt-2 align-items-center'>
            <label class='col-form-label' for='estado'> Estado Geral do Veículo:<span style='color: red;'>*</span>
            </label>

            <div class='col-12' style='flex-wrap: wrap;'>
              <input type="radio" id="pessimo" name="estado" value="pessimo" required>
              <label for="pessimo">Péssimo</label>
              <input class="ms-1" type="radio" id="ruim" name="estado" value="ruim">
              <label for="ruim">Ruim</label>
              <input class="ms-1" type="radio" id="regular" name="estado" value="regular">
              <label for="regular">Regular</label>
              <input class="ms-1" type="radio" id="bom" name="estado" value="bom">
              <label for="bom">Bom</label>
              <input class="ms-1" type="radio" id="otimo" name="estado" value="otimo">
              <label for="otimo">Ótimo</label>
            </div>
          </div>

          <!--div avaria -->
          <div class='row mt-2 align-items-center'>
            <label class='col-form-label col-6' for='avaria'> Possui avaria? <span style='color: red;'>*</span> </label>
            <div class='col-6'>
              <input type="radio" id="sim" name="avaria" value="1" required>
              <label for="sim">Sim</label>
              <input class="ms-1" type="radio" id="nao" name="avaria" value="0">
              <label for="nao">Não</label>
            </div>
          </div>

          <!--div observaçao -->
          <div class='mt-2 align-items-center'>
            <label class='col-form-label' for='observacao'>Observação:</label>
            <textarea class="form-control" id="observacao" name="observacao" placeholder="Observação"
              rows="5"></textarea>
          </div>


          <!--div hodometro
        <div class='row mt-2'>
          <label class='col-5 col-form-label' for='hodometro'> Hodômetro:<span style='color: red;'>*</span> </label>
          <div class='col-6'>
            <input type='text' name='hodometro' id='hodometro' class='form-control' placeholder='Hodômetro' required>
          </div>
        </div>

        <--div nivel do tanque 
        <div class='row mt-2'>
          <label class='col-5 col-form-label' for='niveltanque'> Nível do Tanque:<span style='color: red;'>*</span> </label>
          <div class='col-6'>
            <input type='text' name='niveltanque' id='niveltanque' class='form-control' placeholder='Nível do tanque' required>
          </div>
        </div>-->
        </div>

        <!-- div campos obrigatórios-->
        <div class='mt-4 col-12'>
          <p style='color: red;'> * Campos obrigatórios. </p>
        </div>

        <!-- div botão-->
        <div class='mt-5 mb-5 col-12 text-center'>
          <button id="btnSubmit" class='col-4 btn btn-success' type='submit'> Enviar </button>
        </div>
        </form>
      </div>




  </div>
  </main>

  <!--<footer class="py-4 bg-light mt-auto">
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
  </div><!--div para fechar a div que fica aberta no menu.php-->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    crossorigin="anonymous"></script>
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

      $('#cnh').mask('00000000000', { reverse: true });
      //$('#cpf').mask('000.000.000-00', {reverse: true});
      
      // Máscaras para busca
      $('#cpf1').mask('000.000.000-00', { reverse: true });
      $('#matricula_busca').mask('0000000', { reverse: true });

      $('#hodometro').mask('000000000');
      $('#niveltanque').mask('0/0');

      //$('#validadecnh').mask('0000-00-00');

      // Validação para CPF ou Matrícula - ao menos um deve ser preenchido
      $('form').on('submit', function(e) {
        var cpf = $('#cpf1').val().trim();
        var matricula = $('#matricula_busca').val().trim();
        
        // Se nenhum dos dois estiver preenchido, impede o envio
        if (cpf === '' && matricula === '') {
          e.preventDefault();
          alert('Por favor, preencha pelo menos um campo: CPF ou Matrícula');
          return false;
        }
      });

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

          //$("#statusveic option[value!='1']").hide();


        } else if ($("#tipo").val() == '3') {

          $("#statusveic").val('2');
          $("#statusveic option[value!='1']").show();
          $("#statusveic option[value='1']").hide();

        } else if ($("#tipo").val() == '1') {

          $("#statusveic").val('2');
          $("#statusveic option[value!='1']").show();
          $("#statusveic option[value='1']").hide();
        }
      });


    });
  </script>

</body>

</html>