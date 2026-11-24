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
$matricula1 = trim($_SESSION['matricula']);
$perfil = $_SESSION['perfil'];

if ($perfil == null) {
  echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
} /*elseif ($perfil<>'4'){
 echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}*/

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = trim($matricula1);
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;

$data = date('Y-m-d');

include "conecta.php";
//include_once"./func/log.php";
header("Content-type: text/html; charset=utf-8");

//$sqla="SELECT ds_placa FROM bdfrota.tbcarrosxcolaborador WHERE ds_placa ='".$placacorreta."'; ";
$sqla = "SELECT placa,basegestao FROM bdfrota.tbveiculo WHERE matcond='$matricula1' ";
$resultadoa = mysqli_query($con, $sqla) or die(mysqli_error($con));
$rowa = mysqli_fetch_array($resultadoa, MYSQLI_BOTH);
$placacorreta = $rowa['placa'];
$basegestaoatual = $rowa['basegestao'];
/*$sqla= "SELECT placa FROM bdfrota.tbveiculo WHERE placa ='".$placacorreta."'; ";
$resultadoa= mysqli_query($con, $sqla);
$linha = mysqli_num_rows($resultadoa);
if($linha == 0){
  echo"<script language='javascript' type='text/javascript'>alert('Placa não encontrada no sistema.');window.location=\"./condutorinicial.php\"</script>";
}*/

/*$sqla1= "SELECT max(idtbmanprev), status FROM bdfrota.tbmanprev WHERE placa ='$placacorreta'";
$resultadoa1= mysqli_query($con, $sqla1) or die(mysqli_error($con));

$rowa1=mysqli_fetch_array($resultadoa1, MYSQLI_BOTH);
  $statusman = utf8_encode($rowa1[1]);//|| $statusman <> null

if($statusman <> 'CONCLUIDO'){
  $r = 'diferente';
  echo"<script language='javascript' type='text/javascript'>alert('Veículo em manutenção. Vistoria não autorizada.');window.location=\"./condutorinicial.php\"</script>";
}*/

if (file_exists("../../assinaturas/docs/colass/$matricula1.png") == false) {
  print "
  <script language='javascript' type='text/javascript'> 
    window.alert('Você não possui assinatura cadastrada. Por favor, procure o DP.');
    window.location=\"./condutorinicial.php\";    
  </script>";
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

  <title> Checklist - Passo 1 </title>

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

  <script type="text/javascript"
    src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
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
          <form method="post" action="./control/checklistp1.php">

            <?php
            //pegando informações da última vistoria
            if ($placacorreta != '') {
              $sql1 = "SELECT max(idtbvistoria) FROM bdfrota.tbvistoria where placa='$placacorreta'";
              $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));
              $row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
              $idtbvistoria = $row1[0];

              $sql2 = "SELECT * FROM bdfrota.tbvistoria where idtbvistoria='$idtbvistoria'";
              $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
              $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
              $datavistoria = $row2['datavistoria'];
              $estado = $row2['estado'];
              $avaria = $row2['avaria'];
              $hodometro = $row2['hodometro'];
              $documentacao = $row2['documentacao'];
              $niveltanque = $row2['niveltanque'];

              $teto = $row2['teto'];
              $tetoesp = $row2['tetoesp'];
              $tetoesq = $row2['tetoesq'];
              $tetodir = $row2['tetodir'];
              $frente = $row2['frente'];
              $capo = $row2['capo'];
              $parabrisa = $row2['parabrisa'];
              $farolesq = $row2['farolesq'];
              $faroldir = $row2['faroldir'];
              $parachoque = $row2['parachoque'];
              $grade = $row2['grade'];
              $latesq = $row2['latesq'];
              $paralamaesq = $row2['paralamaesq'];
              $retrovesq = $row2['retrovesq'];
              $cxaresq = $row2['cxaresq'];
              $ptdiantesq = $row2['ptdiantesq'];
              $pttrasesq = $row2['pttrasesq'];
              $latdir = $row2['latdir'];
              $paralamadir = $row2['paralamadir'];
              $retrovdir = $row2['retrovdir'];
              $cxardir = $row2['cxardir'];
              $ptdiantdir = $row2['ptdiantdir'];
              $pttrasdir = $row2['pttrasdir'];
              $traseira = $row2['traseira'];
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
            
              $pdf = '../pdfnextfleet/checklist_ID_' . $idnextfleet . '_' . $placa . '.pdf';

              if ($avaria == '1') {
                $avaria = 'SIM';
              } elseif ($avaria == '0') {
                $avaria = 'NÃO';
              }
            }
            //pegando do sistema atual
            $sql2 = "SELECT b.placa AS placa, b.modelo AS modelo, b.anofabr AS anofabr, a.ccusto AS ccusto, a.codfilial AS codfilial, a.codempresa AS codempresa, b.statusvel AS statusvel FROM bdaniel.tbfuncionario a, bdfrota.tbveiculo b WHERE b.placa= '$placacorreta' AND a.matricula = b.matcond";

            //pegando da tbveiculo
            //$sql="SELECT placa, modelo, anofabr, aplicacao AS ccusto FROM bdfrota.tbveiculo WHERE placa='".$placacorreta."';";
            
            $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));

            while ($row2 = mysqli_fetch_array($resultado2, MYSQLI_ASSOC)) {
              $placa = $row2['placa'];
              $idmodelo = $row2['modelo'];
              $anofabricacao = $row2['anofabr'];
              $centrocusto = utf8_encode($row2['ccusto']);
              $statusvel = $row2['statusvel'];
              $codfilial = $row2['codfilial'];
              $codempresa = $row2['codempresa'];

              $sql3 = "SELECT modelo FROM bdfrota.tbmodeloveic WHERE idtbmodeloveic='" . $idmodelo . "' ";
              $res3 = mysqli_query($con, $sql3) or die(mysqli_error($con));
              $row3 = mysqli_fetch_array($res3, MYSQLI_BOTH);
              $modelo = utf8_encode($row3['modelo']);

              $sql4 = "SELECT * FROM bdaniel.tbfilial WHERE codfilial='$codfilial' AND codempresa='$codempresa' ";
              $res4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
              $row4 = mysqli_fetch_array($res4, MYSQLI_BOTH);
              $filial = utf8_encode($row4['nome']);
            }

            print "<!--div informações do condutor-->
              <div class='mt-4 border-top border-dark pt-2'>
                <h6> Informações do veículo </h6>

                <!--div placa --> 
                <div class='row mt-2'>
                  <label class='col-5 col-form-label' for='placa'> Placa:<span style='color: red;'>*</span> </label>
                  <div class='col-6'>
                    <input type='text' name='placa' id='placa' class='form-control' placeholder='Placa do Veículo' required value='$placa'>
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
                    <input type='text' name='anofabricacao' id='anofabricacao' class='form-control' placeholder='Ano de fabricação' required value='$anofabricacao'>
                  </div>
                </div>";
            ?>

            <?php
            //echo $matricula1;
            //echo $placacorreta;
            
            //pegando dados dos sistemas já existentes pegando dados da tabela tbcondutor, tbcnh
            //$sql = "SELECT a.nome AS condutor, a.matricula AS matrcond, a.cpf AS cpf, c.numcnh AS cnh, c.validade AS vencimento, c.categoria AS categoria FROM bdaniel.tbfuncionario a, bdfrota.tbveiculo b, bdfrota.tbcnh c, bdfrota.tbcondutor d  WHERE a.matricula='$matricula1' AND  a.matricula = b.matcond AND a.matricula = c.matricula;";
            
            //b.placa= '$placacorreta' AND
            //$sql="SELECT a.nome AS condutor, a.matricula AS matrcond, b.numcnh AS cnh, b.validade AS vencimento, b.categoria AS categoria, c.cpf AS cpf FROM bdfrota.tbcondutor a, bdfrota.tbcnh b, bdaniel.tbfuncionario c WHERE placaassoc='".$placacorreta."' AND ativo=1 AND a.matricula = b.matricula AND a.matricula = c.matricula; ";
            
            $sql = "SELECT a.nome AS condutor, a.matricula AS matrcond, a.cpf AS cpf, c.numcnh AS cnh, c.validade AS vencimento, c.categoria AS categoria FROM bdaniel.tbfuncionario a, bdfrota.tbveiculo b, bdfrota.tbcnh c, bdfrota.tbcondutor d  WHERE a.matricula='$matricula1' AND  a.matricula = b.matcond AND a.matricula = c.matricula;";

            $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

            while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
              $condutor = utf8_encode($row['condutor']);
              $matrcond = $row['matrcond'];
              $cpf = $row['cpf'];
              $cnh = $row['cnh'];
              $validade = $row['vencimento'];
              $categoria = $row['categoria'];

              $validade1 = explode(" ", $validade);
            }

            //print $nome, $matricula, $id_colaborador, $cpf, $numcnh, $categoria, $validade;
            
            print "
            <input type='hidden' value='$placacorreta'>
              <!--div informações do condutor-->
              <div class='mt-4 border-top border-dark pt-2'>
                <h6> Informações do condutor </h6>

                <!--div cpf-->
                <div class='row col-12'>
                  <label class='col-3 col-form-label' for='cpf'> CPF:<span style='color: red;'>*</span> </label>
                  <div class='col-9'>
                    <input type='text' name='cpf' id='cpf' class='form-control' placeholder='CPF do Condutor' required value='$cpf'>
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
                    <input type='text' name='cnh' id='cnh' class='form-control' placeholder='CNH do Condutor' required value='$cnh'>
                  </div>
                </div>

                <!--div categoria cnh-->
                <div class='row mt-2 col-12'>
                  <label class='col-7 col-form-label' for='categoriacnh'> Categoria CNH:<span style='color: red;'>*</span> </label>
                  <div class='col-5'>
                    <input type='text' name='categoriacnh' id='categoriacnh' class='form-control' placeholder='Categoria' required value='$categoria'>
                  </div>
                </div>

                <!--div validade cnh-->
                <div class='row mt-2'>
                  <label class='col-5 col-form-label' for='validadecnh'> Venc. CNH (aaaa-mm-dd):<span style='color: red;'>*</span> </label>
                  <div class='col-6'>
                    <!--<input type='text' name='validadecnh' id='validadecnh' class='form-control' placeholder='Validade da CNH' required >-->
                    <input type='date' name='validadecnh' id='validadecnh' value='$validade1[0]' class='form-control' required>
                  </div>
                </div>
              </div>";
            ?>

            <!--div unidade -->
            <div class='row mt-2'>
              <label class='col-5 col-form-label' for='unidade'> Unidade:<span style='color: red;'>*</span> </label>
              <div class='col-6'>
                <!--<input type='text' name='unidade' id='unidade' class='form-control' placeholder='Unidade' required>-->
                <select name='unidade' id='unidade' class='form-select' required>
                  <option value=''> Selecione a opção </option>
                  <?php
                  $sql3 = "SELECT * FROM bdaniel.tbfilial";
                  $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));

                  while ($row3 = mysqli_fetch_array($resultado3, MYSQLI_ASSOC)) {
                    $idtbfilial = $row3['idtbfilial'];
                    //$codigo = $row3['codfilial'];
                    $descricao = utf8_encode($row3['nome']);
                    $selected = ($filial == $descricao) ? "selected=\"selected\"" : null;

                    print "<option value='$descricao' $selected> $descricao </option>";
                  }

                  ?>
                </select>
              </div>
            </div>

            <?php
            print "<!--div centro custo -->
                <div class='row mt-2'>
                  <label class='col-5 col-form-label' for='centrocusto'> Centro Custo:<span style='color: red;'>*</span> </label>
                  <div class='col-6'>
                    <input type='text' name='centrocusto' id='centrocusto' class='form-control' placeholder='Centro Custo' required value='$centrocusto'>
                  </div>
                </div>
              </div>
            ";
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

            <!-- div informações checklist-->
            <div class='mt-4 border-top border-dark pt-2'>
              <h6> Informações da vistoria </h6>
              <!--div tipo-->
              <?php
              if ($idnextfleet <> '') {
                print "
              <div class='row mt-2 align-items-center'>
                <p class='col-5'> Informações da vistoria anterior: </p>
                <div class='col-6'>
                  <a class='btn btn-secondary' target='_blank' href='$pdf'> Visualizar </a>
                </div>
              </div>";
              }
              ?>
              <div class='row mt-2'>
                <label class='col-5 col-form-label' for='tipo'> Tipo:<span style='color: red;'>*</span> </label>
                <div class='col-6'>
                  <select name="tipo" id='tipo' class="form-select">
                    <option value=''> Selecione a opção </option>
                    <?php
                    $sql = "SELECT * FROM bdfrota.tbatipovist WHERE idtbatipovist='1';";
                    $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                    while ($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)) {
                      $idtbatipovist = $row['idtbatipovist'];
                      $tipo = utf8_encode($row['tipo']);

                      if ($idtbatipovist == '1') {
                        $selected = 'selected';
                      } else {
                        $selected = '';
                      }
                      print "
                      <option value='$idtbatipovist' $selected> $tipo </option>";
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
                $nomevist = utf8_encode($row['nome']);
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
                <label class='col-5 col-form-label' for='datavistoria'> Data da Vistoria:<span
                    style='color: red;'>*</span> </label>
                <div class='col-6'>
                  <input type='date' name='datavistoria' id='datavistoria' class='form-control' required>
                </div>
              </div>

              <!--div tipo-->
              <div class='row mt-2'>
                <label class='col-5 col-form-label' for='tipo'> Status:<span style='color: red;'>*</span> </label>
                <div class='col-6'>
                  <select name="statusveic" id='statusveic' class="form-select" required>
                    <option value=''> Selecione a opção </option>
                    <?php
                    $sql3 = "SELECT * FROM bdfrota.tbastatusvel"; //idtbastatusvel, status
                    $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));

                    while ($row3 = mysqli_fetch_array($resultado3, MYSQLI_ASSOC)) {
                      $idtbastatusvel = $row3['idtbastatusvel'];
                      $statusveic = utf8_encode($row3['status']);

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
                <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $estado</p>"; ?>
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
                <label class='col-form-label col-6' for='avaria'> Possui avaria? <span style='color: red;'>*</span>
                </label>
                <div class='col-6'>
                  <input type="radio" id="sim" name="avaria" value="1" required>
                  <label for="sim">Sim</label>
                  <input class="ms-1" type="radio" id="nao" name="avaria" value="0">
                  <label for="nao">Não</label>
                </div>
              </div>
              <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $avaria</p>"; ?>

              <!--div hodometro -->
              <div class='row mt-2'>
                <label class='col-5 col-form-label' for='hodometro'> Hodômetro:<span
                    style='color: red;'>*</span><?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $hodometro km</p>"; ?>
                </label>
                <div class='col-6'>
                  <input type='text' name='hodometro' id='hodometro' class='form-control' placeholder='Hodômetro'
                    required>
                </div>
              </div>

              <!--div nivel do tanque -->
              <div class='row mt-2'>
                <label class='col-5 col-form-label' for='niveltanque'> Nível do Tanque:<span
                    style='color: red;'>*</span> </label>
                <div class='col-6'>
                  <input type='text' name='niveltanque' id='niveltanque' class='form-control'
                    placeholder='Nível do tanque' required>
                </div>
              </div>
            </div>

            <!-- div Itens personalizados da vistoria -->
            <div class='mt-4 border-top border-dark pt-2'>
              <h6> Itens personalizados da vistoria </h6>
              <!--div teto -->
              <div class='column mt-2 align-items-center'>
                <label class='col-form-label' for='teto'> Teto do veículo - Estado Geral:<span
                    style='color: red;'>*</span> </label>
                <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $teto</p>"; ?>
                <div class=''>
                  <input type="radio" id="okteto" name="teto" value="ok" required>
                  <label for="okteto">Ok</label>
                  <input class="ms-2" type="radio" id="nokteto" name="teto" value="naook">
                  <label for="nokteto">Não Ok</label>
                </div>
              </div>

              <!--div teto não ok -->
              <div id='tetonaook' hidden>
                <!--div teto não ok - teto geral-->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='tetoesp'> Teto geral:<span style='color: red;'>*</span> </label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $tetoesp</p>"; ?>
                  <div class=''>
                    <input type="radio" id="oktetoesp" name="tetoesp" value="ok">
                    <label for="oktetoesp">Ok</label>
                    <input class="ms-2" type="radio" id="amtetoesp" name="tetoesp" value="amassado">
                    <label for="amtetoesp">Amassado</label>
                    <input class="ms-2" type="radio" id="arrtetoesp" name="tetoesp" value="arranhado">
                    <label for="arrtetoesp">Arranhado</label>
                  </div>
                </div>

                <!--div teto não ok - Coluna do teto Lado Esquerdo -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='tetoesq'> Coluna do teto - lado esquerdo: <span
                      style='color: red;'>*</span> </label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $tetoesq</p>"; ?>
                  <div class=''>
                    <input type="radio" id="oktetoesq" name="tetoesq" value="ok">
                    <label for="oktetoesq">Ok</label>
                    <input class="ms-2" type="radio" id="amtetoesq" name="tetoesq" value="amassado">
                    <label for="amtetoesq">Amassado</label>
                    <input class="ms-2" type="radio" id="arrtetoesq" name="tetoesq" value="arranhado">
                    <label for="arrtetoesq">Arranhado</label>
                  </div>
                </div>

                <!--div teto não ok - Coluna do teto Lado Direito -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='tetoesq'> Coluna do teto - lado direito: <span
                      style='color: red;'>*</span> </label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $tetodir</p>"; ?>
                  <div class=''>
                    <input type="radio" id="oktetodir" name="tetodir" value="ok">
                    <label for="oktetodir">Ok</label>
                    <input class="ms-2" type="radio" id="amtetodir" name="tetodir" value="amassado">
                    <label for="amtetodir">Amassado</label>
                    <input class="ms-2" type="radio" id="arrtetodir" name="tetodir" value="arranhado">
                    <label for="arrtetodir">Arranhado</label>
                  </div>
                </div>
              </div>

              <!--div frente -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='frente'> Frente do veículo - Estado Geral:<span
                    style='color: red;'>*</span> </label>
                <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $frente</p>"; ?>
                <div class=''>
                  <input type="radio" id="okfrente" name="frente" value="ok" required>
                  <label for="okfrente">Ok</label>
                  <input class="ms-2" type="radio" id="nokfrente" name="frente" value="naook">
                  <label for="nokfrente">Não Ok</label>
                </div>
              </div>

              <!--div frente não ok -->
              <div id='frentenaook' hidden>

                <!--div frente não ok - capo  -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='capo'> Capô: <span style='color: red;'>*</span> </label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $capo</p>"; ?>
                  <div class=''>
                    <input type="radio" id="okcapo" name="capo" value="ok">
                    <label for="okcapo">Ok</label>
                    <input class="ms-2" type="radio" id="amcapo" name="capo" value="amassado">
                    <label for="amcapo">Amassado</label>
                    <input class="ms-2" type="radio" id="arrcapo" name="capo" value="arranhado">
                    <label for="arrcapo">Arranhado</label>
                  </div>
                </div>

                <!--div frente não ok - parabrisa  -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='parabrisa'> Para-brisa: <span style='color: red;'>*</span> </label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $parabrisa</p>"; ?>
                  <div class=''>
                    <input type="radio" id="okparabrisa" name="parabrisa" value="ok">
                    <label for="okparabrisa">Ok</label>
                    <input class="ms-2" type="radio" id="qbparabrisa" name="parabrisa" value="quebrado">
                    <label for="qbparabrisa">Quebrado</label>
                    <input class="ms-2" type="radio" id="trparabrisa" name="parabrisa" value="trincado">
                    <label for="trparabrisa">Trincado</label>
                  </div>
                </div>

                <!--div frente não ok - Farol esquerdo  -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='farolesq'> Farol esquerdo: <span style='color: red;'>*</span>
                  </label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $farolesq</p>"; ?>
                  <div class=''>
                    <input type="radio" id="okfesq" name="farolesq" value="ok">
                    <label for="okfesq">Ok</label>
                    <input class="ms-2" type="radio" id="qbfesq" name="farolesq" value="quebrado">
                    <label for="qbfesq">Quebrado</label>
                    <input class="ms-2" type="radio" id="trfesq" name="farolesq" value="trincado">
                    <label for="trfesq">Trincado</label>
                  </div>
                </div>

                <!--div frente não ok - Farol direito  -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='faroldir'> Farol direito: <span style='color: red;'>*</span>
                  </label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $faroldir</p>"; ?>
                  <div class=''>
                    <input type="radio" id="okfdir" name="faroldir" value="ok">
                    <label for="okfdir">Ok</label>
                    <input class="ms-2" type="radio" id="qbfdir" name="faroldir" value="quebrado">
                    <label for="qbfdir">Quebrado</label>
                    <input class="ms-2" type="radio" id="trfdir" name="faroldir" value="trincado">
                    <label for="trfdir">Trincado</label>
                  </div>
                </div>

                <!--div frente não ok - para-choque dianteiro  -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='parachoque'> Para-choque dianteiro: <span
                      style='color: red;'>*</span></label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $parachoque</p>"; ?>
                  <div class=''>
                    <input type="radio" id="okpc" name="parachoque" value="ok">
                    <label for="okpc">Ok</label>

                    <input class="ms-2" type="radio" id="ampc" name="parachoque" value="amassado">
                    <label for="ampc">Amassado</label>
                    <input class="ms-2" type="radio" id="qbpc" name="parachoque" value="quebrado">
                    <label for="qbpc">Quebrado</label>
                    <input class="ms-2" type="radio" id="arrpc" name="parachoque" value="arranhado">
                    <label for="arrpc">Arranhado</label>
                  </div>
                </div>

                <!--div frente não ok - Grade Frontal   -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='grade'> Grade Frontal: <span style='color: red;'>*</span> </label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $grade</p>"; ?>
                  <div class=''>
                    <input type="radio" id="okgrade" name="grade" value="ok">
                    <label for="okgrade">Ok</label>
                    <input class="ms-2" type="radio" id="amgrade" name="grade" value="amassado">
                    <label for="amgrade">Amassado</label>
                    <input class="ms-2" type="radio" id="qbgrade" name="grade" value="quebrado">
                    <label for="qbgrade">Quebrado</label>
                  </div>
                </div>
              </div>

              <!--div Lateral Esquerda -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='latesq'> Lateral esquerda - Estado Geral:<span
                    style='color: red;'>*</span> </label>
                <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $latesq</p>"; ?>
                <div class=''>
                  <input type="radio" id="oklesq" name="latesq" value="ok">
                  <label for="oklesq">Ok</label>
                  <input class="ms-2" type="radio" id="noklesq" name="latesq" value="naook">
                  <label for="noklesq">Não Ok</label>
                </div>
              </div>

              <!--div latesq não ok -->
              <div id='latesqnaook' hidden>
                <!--div latesq não ok - para-lama -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='paralamaesq'> Para-lama esquerdo:<span style='color: red;'>*</span>
                  </label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $paralamaesq</p>"; ?>
                  <div class=''>
                    <input type="radio" id="okplesq" name="paralamaesq" value="ok">
                    <label for="okplesq">Ok</label>
                    <input class="ms-2" type="radio" id="amplesq" name="paralamaesq" value="amassado">
                    <label for="amplesq">Amassado</label>
                    <input class="ms-2" type="radio" id="arrlesq" name="paralamaesq" value="arranhado">
                    <label for="arrlesq">Arranhado</label>
                  </div>
                </div>

                <!--div latesq não ok - Retrovisor Lado Esquerdo  -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='retrovesq'>Retrovisor Lado Esquerdo: <span
                      style='color: red;'>*</span> </label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $retrovesq</p>"; ?>
                  <div class=''>
                    <input type="radio" id="okrtesq" name="retrovesq" value="ok">
                    <label for="okrtesq">Ok</label>

                    <input class="ms-2" type="radio" id="amrtesq" name="retrovesq" value="amassado">
                    <label for="amrtesq">Amassado</label>
                    <input class="ms-2" type="radio" id="qbrtesq" name="retrovesq" value="quebrado">
                    <label for="qbrtesq">Quebrado</label>
                    <input class="ms-2" type="radio" id="arrrtesq" name="retrovesq" value="arranhado">
                    <label for="arrrtesq">Arranhado</label>
                  </div>
                </div>

                <!--div latesq não ok -  Caixa de Ar Lado Esquerdo   -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='cxaresq'> Caixa de Ar Lado Esquerdo:<span
                      style='color: red;'>*</span> </label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $cxaresq</p>"; ?>
                  <div class=''>
                    <input type="radio" id="okcxesq" name="cxaresq" value="ok">
                    <label for="okcxesq">Ok</label>

                    <input class="ms-2" type="radio" id="amcxesq" name="cxaresq" value="amassado">
                    <label for="amcxesq">Amassado</label>
                    <input class="ms-2" type="radio" id="arrcxesq" name="cxaresq" value="arranhado">
                    <label for="arrcxesq">Arranhado</label>
                  </div>
                </div>

                <!--div latesq não ok - Porta Dianteira Esquerda   -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='ptdiantesq'> Porta Dianteira Esquerda: <span
                      style='color: red;'>*</span> </label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $ptdiantesq</p>"; ?>
                  <div class=''>
                    <input type="radio" id="okpdesq" name="ptdiantesq" value="ok">
                    <label for="okpdesq">Ok</label>

                    <input class="ms-2" type="radio" id="ampdesq" name="ptdiantesq" value="amassado">
                    <label for="ampdesq">Amassada</label>

                    <input class="ms-2" type="radio" id="qbpdesq" name="ptdiantesq" value="quebrado">
                    <label for="qbpdesq">Quebrada</label>

                    <input class="ms-2" type="radio" id="arrpdesq" name="ptdiantesq" value="arranhado">
                    <label for="arrpdesq">Arranhada</label>
                  </div>
                </div>

                <!--div latesq não ok - Porta Traseira Esquerda  -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='pttrasesq'>Porta Traseira Esquerda:<span
                      style='color: red;'>*</span> </label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $pttrasesq</p>"; ?>
                  <div class=''>
                    <input type="radio" id="okptesq" name="pttrasesq" value="ok">
                    <label for="okptesq">Ok</label>

                    <input class="ms-2" type="radio" id="amptesq" name="pttrasesq" value="amassado">
                    <label for="amptesq">Amassado</label>

                    <input class="ms-2" type="radio" id="qbptesq" name="pttrasesq" value="quebrado">
                    <label for="qbptesq">Quebrado</label>

                    <input class="ms-2" type="radio" id="arrptesq" name="pttrasesq" value="arranhado">
                    <label for="arrptesq">Arranhado</label>
                  </div>
                </div>
              </div>

              <!--div Lateral Direita -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='latdir'> Lateral direita - Estado Geral:<span
                    style='color: red;'>*</span> </label>
                <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $latdir</p>"; ?>
                <div class=''>
                  <input type="radio" id="okltdir" name="latdir" value="ok">
                  <label for="okltdir">Ok</label>
                  <input class="ms-1" type="radio" id="nokltdir" name="latdir" value="naook">
                  <label for="nokltdir">Não Ok</label>
                </div>
              </div>

              <!--div latdir não ok -->
              <div id='latdirnaook' hidden>

                <!--div latdir não ok - para-lama -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='paralamadir'> Para-lama direito:<span style='color: red;'>*</span>
                  </label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $paralamadir</p>"; ?>
                  <div class=''>
                    <input type="radio" id="okpldir" name="paralamadir" value="ok">
                    <label for="okpldir">Ok</label>

                    <input class="ms-2" type="radio" id="ampldir" name="paralamadir" value="amassado">
                    <label for="ampldir">Amassado</label>

                    <input class="ms-2" type="radio" id="arrpldir" name="paralamadir" value="arranhado">
                    <label for="arrpldir">Arranhado</label>
                  </div>
                </div>

                <!--div latdir não ok - Retrovisor Lado Direito  -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='retrovdir'>Retrovisor Lado Direito: <span
                      style='color: red;'>*</span> </label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $retrovdir</p>"; ?>
                  <div class=''>
                    <input type="radio" id="okrtdir" name="retrovdir" value="ok">
                    <label for="okrtdir">Ok</label>

                    <input type="radio" id="amrtdir" name="retrovdir" value="amassado">
                    <label for="amrtdir">Amassado</label>

                    <input type="radio" id="qbrtdir" name="retrovdir" value="quebrado">
                    <label for="qbrtdir">Quebrado</label>

                    <input class="ms-1" type="radio" id="arrrtdir" name="retrovdir" value="arranhado">
                    <label for="arrrtdir">Arranhado</label>
                  </div>
                </div>

                <!--div latesq não ok -  Caixa de Ar Lado Direito   -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='cxardir'> Caixa de Ar Lado Direito:<span
                      style='color: red;'>*</span> </label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $cxaresq</p>"; ?>
                  <div class=''>
                    <input type="radio" id="okcxdir" name="cxardir" value="ok">
                    <label for="okcxdir">Ok</label>

                    <input class="ms-2" type="radio" id="amcxdir" name="cxardir" value="amassado">
                    <label for="amcxdir">Amassada</label>

                    <input class="ms-2" type="radio" id="arrcxdir" name="cxardir" value="arranhado">
                    <label for="arrcxdir">Arranhada</label>
                  </div>
                </div>

                <!--div latesq não ok - Porta Dianteira direita   -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='ptdiantdir'> Porta Dianteira Direita: <span
                      style='color: red;'>*</span> </label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $ptdiantdir</p>"; ?>
                  <div class=''>
                    <input type="radio" id="okpddir" name="ptdiantdir" value="ok">
                    <label for="okpddir">Ok</label>

                    <input class="ms-2" type="radio" id="ampddir" name="ptdiantdir" value="amassado">
                    <label for="ampddir">Amassada</label>

                    <input class="ms-2" type="radio" id="qbpddir" name="ptdiantdir" value="quebrado">
                    <label for="qbpddir">Quebrada</label>

                    <input class="ms-2" type="radio" id="arrpddir" name="ptdiantdir" value="arranhado">
                    <label for="arrpddir">Arranhada</label>
                  </div>
                </div>

                <!--div latesq não ok - Porta Traseira direita  -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='pttrasdir'>Porta Traseira Direita:<span
                      style='color: red;'>*</span> </label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $pttrasdir</p>"; ?>
                  <div class=''>
                    <input type="radio" id="okptdir" name="pttrasdir" value="ok">
                    <label for="okptdir">Ok</label>

                    <input class="ms-2" type="radio" id="amptdir" name="pttrasdir" value="amassado">
                    <label for="amptdir">Amassada</label>

                    <input class="ms-2" type="radio" id="qbptdir" name="pttrasdir" value="quebrado">
                    <label for="qbptdir">Quebrada</label>

                    <input class="ms-2" type="radio" id="arrptdir" name="pttrasdir" value="arranhado">
                    <label for="arrptdir">Arranhada</label>
                  </div>
                </div>
              </div>

              <!--div Traseira -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='traseira'> Traseira do veículo - Estado Geral:<span
                    style='color: red;'>*</span> </label>
                <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $traseira</p>"; ?>
                <div class=''>
                  <input type="radio" id="oktras" name="traseira" value="ok">
                  <label for="oktras">Ok</label>

                  <input class="ms-1" type="radio" id="noktras" name="traseira" value="naook">
                  <label for="noktras">Não Ok</label>
                </div>
              </div>

              <!--div traseira não ok -->
              <div id='traseiranaook' hidden>

                <!--div traseira não ok - lanterna esq -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='lantesq'> Lanterna Esquerda:<span style='color: red;'>*</span>
                  </label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $lantesq</p>"; ?>
                  <div class=''>
                    <input type="radio" id="oklteq" name="lantesq" value="ok">
                    <label for="oklteq">Ok</label>

                    <input class="ms-2" type="radio" id="qblteq" name="lantesq" value="quebrado">
                    <label for="qblteq">Quebrado</label>
                  </div>
                </div>

                <!--div traseira não ok - lanterna dir -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='lantdir'> Lanterna Direita:<span style='color: red;'>*</span>
                  </label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $lantdir</p>"; ?>
                  <div class=''>
                    <input type="radio" id="okltdr" name="lantdir" value="ok">
                    <label for="okltdr">Ok</label>

                    <input class="ms-2" type="radio" id="qbltdr" name="lantdir" value="quebrado">
                    <label for="qbltdr">Quebrado</label>
                  </div>
                </div>

                <!--div traseira não ok -  Tampa da Mala -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='tmpmala'>Tampa da Mala:<span style='color: red;'>*</span> </label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $tmpmala</p>"; ?>
                  <div class=''>
                    <input type="radio" id="oktm" name="tmpmala" value="ok">
                    <label for="oktm">Ok</label>

                    <input class="ms-2" type="radio" id="amtm" name="tmpmala" value="amassado">
                    <label for="amtm">Amassado</label>

                    <input class="ms-2" type="radio" id="qbtm" name="tmpmala" value="quebrado">
                    <label for="qbtm">Quebrado</label>

                    <input class="ms-2" type="radio" id="arrtm" name="tmpmala" value="arranhado">
                    <label for="arrtm">Arranhado</label>
                  </div>
                </div>

                <!--div traseira não ok - Para-choque Traseiro -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='parachoquet'>Para-choque Traseiro: <span
                      style='color: red;'>*</span> </label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $parachoquet</p>"; ?>
                  <div class=''>
                    <input type="radio" id="okpct" name="parachoquet" value="ok">
                    <label for="okpct">Ok</label>

                    <input class="ms-2" type="radio" id="ampct" name="parachoquet" value="amassado">
                    <label for="ampct">Amassado</label>

                    <input class="ms-2" type="radio" id="qbpct" name="parachoquet" value="quebrado">
                    <label for="qbpct">Quebrado</label>

                    <input class="ms-2" type="radio" id="arrpct" name="parachoquet" value="arranhado">
                    <label for="arrpct">Arranhado</label>
                  </div>
                </div>
              </div>

              <!--div itens internos -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='itinterno'> Itens Internos - Estado Geral:<span
                    style='color: red;'>*</span> </label>
                <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $itinterno</p>"; ?>
                <div class=''>
                  <input type="radio" id="okitin" name="itinterno" value="ok">
                  <label for="okitin">Ok</label>
                  <input class="ms-1" type="radio" id="nokitin" name="itinterno" value="naook">
                  <label for="nokitin">Não Ok</label>
                </div>
              </div>

              <!--div itens internos não ok -->
              <div id='itinternonaook' hidden>

                <!--div itens internos não ok - Painel -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='painel'>Painel:<span style='color: red;'>*</span> </label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $painel</p>"; ?>
                  <div class=''>
                    <input type="radio" id="okpainel" name="painel" value="ok">
                    <label for="okpainel">Ok</label>

                    <input class="ms-2" type="radio" id="nokpainel" name="painel" value="naook">
                    <label for="nokpainel">Não Ok</label>
                  </div>
                </div>

                <!--div itens internos não ok - som -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='som'>Som :<span style='color: red;'>*</span> </label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $som</p>"; ?>
                  <div class=''>
                    <input type="radio" id="oksom" name="som" value="ok">
                    <label for="oksom">Ok</label>
                    <input type="radio" id="noksom" name="som" value="naook">
                    <label for="noksom">Não Ok</label>
                    <input type="radio" id="ausentesom" name="som" value="ausente">
                    <label for="ausentesom">Ausente</label>
                  </div>
                </div>

                <!--div itens internos não ok - Bancos-->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='bancos'>Bancos:<span style='color: red;'>*</span> </label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $bancos</p>"; ?>
                  <div class=''>
                    <input type="radio" id="okbancos" name="bancos" value="ok">
                    <label for="okbancos">Ok</label>

                    <input class="ms-2" type="radio" id="nokbancos" name="bancos" value="naook">
                    <label for="nokbancos">Não Ok</label>
                  </div>
                </div>

                <!--div itens internos não ok - Iluminação Interna -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='ilumint'>Iluminação Interna: <span style='color: red;'>*</span>
                  </label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $ilumint</p>"; ?>
                  <div class=''>
                    <input type="radio" id="okilumint" name="ilumint" value="ok">
                    <label for="okilumint">Ok</label>

                    <input class="ms-2" type="radio" id="nokilumint" name="ilumint" value="naook">
                    <label for="nokilumint">Não Ok</label>
                  </div>
                </div>

                <!--div itens internos não ok - Tampa do Bagagito -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='tmpbag'>Tampa do Bagagito: <span style='color: red;'>*</span>
                  </label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $tmpbag</p>"; ?>
                  <div class=''>
                    <input type="radio" id="oktmpbag" name="tmpbag" value="ok">
                    <label for="oktmpbag">Ok</label>

                    <input class="ms-2" type="radio" id="quebtmpbag" name="tmpbag" value="quebrado">
                    <label for="quebtmpbag">Quebrado</label>

                    <input class="ms-2" type="radio" id="austmpbag" name="tmpbag" value="ausente">
                    <label for="austmpbag">Ausente</label>
                  </div>
                </div>

                <!--div itens internos não ok - Retrovisor interno  -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='retrovint'>Retrovisor interno: <span style='color: red;'>*</span>
                  </label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $retrovint</p>"; ?>
                  <div class=''>
                    <input type="radio" id="okretrovint" name="retrovint" value="ok">
                    <label for="okretrovint">Ok</label>

                    <input class="ms-2" type="radio" id="ausretrovint" name="retrovint" value="ausente">
                    <label for="ausretrovint">Ausente</label>
                  </div>
                </div>

                <!--div itens internos não ok - Tapetes-->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='tapetes'>Tapetes: <span style='color: red;'>*</span> </label>
                  <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $tapetes</p>"; ?>
                  <div class=''>
                    <input type="radio" id="oktapetes" name="tapetes" value="ok">
                    <label for="oktapetes">Ok</label>

                    <input class="ms-2" type="radio" id="austapetes" name="tapetes" value="ausente">
                    <label for="austapetes">Ausente</label>
                  </div>
                </div>
              </div>

              <!--div Pneus/Kit Step  -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='pneus'> Pneus:<span style='color: red;'>*</span> </label>
                <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $pneus</p>"; ?>
                <div class=''>
                  <input type="radio" id="okpneus" name="pneus" value="ok" required>
                  <label for="okpneus">Ok</label>

                  <input class="ms-2" type="radio" id="naook5" name="pneus" value="naook">
                  <label for="naook5">Não Ok</label>
                </div>
              </div>

              <!--div - Step -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='step'>Step:<span style='color: red;'>*</span> </label>
                <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $step</p>"; ?>
                <div class=''>
                  <input type="radio" id="okstep" name="step" value="ok">
                  <label for="okstep">Ok</label>

                  <input class="ms-2" type="radio" id="nokstep" name="step" value="naook">
                  <label for="nokstep">Não Ok</label>
                </div>
              </div>

              <!--div - • Marca dos Pneus  -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='marcapneus'>Marca dos Pneus:<span style='color: red;'>*</span>
                </label>
                <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $marcapneus</p>"; ?>
                <input type="text" class='form-control' id="marcapneus" name="marcapneus" required>
              </div>

              <!--div - Kit Step -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='kitstep'>Kit Step :<span style='color: red;'>*</span> </label>
                <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $kitstep</p>"; ?>
                <div class=''>
                  <input type="radio" id="okkitstep" name="kitstep" value="ok" required>
                  <label for="okkitstep">Ok</label>

                  <input class="ms-2" type="radio" id="nokkitstep" name="kitstep" value="naook">
                  <label for="nokkitstep">Não Ok</label>
                </div>
              </div>

              <!--div • Calotas -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='calotas'>Calotas: <span style='color: red;'>*</span> </label>
                <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $calotas</p>"; ?>
                <div class=''>
                  <input type="radio" id="okcalotas" name="calotas" value="ok">
                  <label for="okcalotas">Ok</label>

                  <input class="ms-2" type="radio" id="nokcalotas" name="calotas" value="naook">
                  <label for="nokcalotas">Não Ok</label>
                </div>
              </div>
            </div>

            <!--div Bateria-->
            <div class='column mt-2 align-items-center border-top'>
              <label class='col-form-label' for='bateria'>Marca da bateria: <span style='color: red;'>*</span> </label>
              <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $bateria</p>"; ?>
              <input type="text" class='form-control' id="bateria" name="bateria" required>
            </div>

            <!--div SafeCar -->
            <div class='column mt-2 align-items-center border-top'>
              <label class='col-form-label' for='safecar'>SafeCar: <span style='color: red;'>*</span> </label>
              <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $safecar</p>"; ?>
              <div class=''>
                <input type="radio" id="oksafecar" name="safecar" value="ok" required>
                <label for="oksafecar">Ok</label>

                <input class="ms-2" type="radio" id="aussafecar" name="safecar" value="ausente">
                <label for="aussafecar">Ausente</label>
              </div>
            </div>

            <!--div Limpeza interna -->
            <div class='column mt-2 align-items-center border-top'>
              <label class='col-form-label' for='limpint'>Limpeza interna: <span style='color: red;'>*</span> </label>
              <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $limpint</p>"; ?>
              <div class=''>
                <input type="radio" id="limpoint" name="limpint" value="limpo" required>
                <label for="limpoint">Limpo</label>

                <input class="ms-2" type="radio" id="sujoint" name="limpint" value="sujo">
                <label for="sujoint">Sujo</label>
              </div>
            </div>

            <!--div Limpeza externa -->
            <div class='column mt-2 align-items-center border-top'>
              <label class='col-form-label' for='limpint'>Limpeza externa: <span style='color: red;'>*</span> </label>
              <?php print "<p class='mt-1 blockquote-footer'> Última vistoria: $limpext</p>"; ?>
              <div class=''>
                <input type="radio" id="limpoext" name="limpext" value="limpo" required>
                <label for="limpoext">Limpo</label>

                <input class="ms-2" type="radio" id="sujoext" name="limpext" value="sujo">
                <label for="sujoext">Sujo</label>
              </div>
            </div>

        </div>

        <!--div observação -->
        <div class='mt-2 border-top'>
          <label class='col-5 col-form-label' for='observacao'> Observação: </label>
          <div class='col-12'>
            <textarea type='text' name='observacao' id='observacao' class='form-control'
              placeholder='Observação'> </textarea>
          </div>


          <!-- div campos obrigatórios-->
          <div class="mt-4 col-12">
            <p style='color: red;'> * Campos obrigatórios. </p>
          </div>

          <!-- div botão-->
          <div class="mt-4 col-12 text-center">
            <button class="col-4 btn btn-success" type='submit'> Próximo </button>
          </div>
          </form>
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
    $(document).ready(function () {
      //$('#tabela1').DataTable();

      $('#cnh').mask('00000000000', { reverse: true });
      //$('#cpf').mask('000.000.000-00', {reverse: true});

      $('#hodometro').mask('000000000');
      $('#niveltanque').mask('0/0');

      $('#validadecnh').mask('0000-00-00');
      $('#anofabricacao').mask('0000');

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
      /*$("form").on("blur", "#cpf", function() {
          $.ajax({url: "./busca_banco.php?cpf="+$("#cpf").val(), success: function(resultado) {
              // faz a lógica
              var json = $.parseJSON(resultado);
              $("#nome").val(json.condutor);
              $("#cnh").val(json.cnh);
              $("#matricula").val(json.matrcond);
              $("#categoriacnh").val(json.categoria);
              $("#validadecnh").val(json.vencimento);
              $("#unidade").val(json.unidade);
              $("#ccusto").val(json.ccusto);
          }
        })
      })*/

      $('input:radio[name="teto"]').change(function () {
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

      $('input:radio[name="frente"]').change(function () {
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

      $('input:radio[name="latesq"]').change(function () {
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

      $('input:radio[name="latdir"]').change(function () {
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

      $('input:radio[name="traseira"]').change(function () {
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

      $('input:radio[name="itinterno"]').change(function () {
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


    });
  </script>

</body>

</html>