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

if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
} /*elseif ($perfil<>'4'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}*/

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula1;
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;

$data = date('Y-m-d');

include"conecta.php";
//include_once"./func/log.php";
header("Content-type: text/html; charset=utf-8");

//$sqla="SELECT ds_placa FROM bdfrota.tbcarrosxcolaborador WHERE ds_placa ='".$placacorreta."'; ";
$sqla="SELECT placa FROM bdfrota.tbveiculo WHERE matcond='$matricula1' ";
$resultadoa = mysqli_query($con, $sqla) or die(mysqli_error($con));
$rowa = mysqli_fetch_array($resultadoa, MYSQLI_BOTH);
  $placacorreta = $rowa['placa'];

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

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
</head>
<body class="sb-nav-fixed">

  <!--nav-->
  <?php include "./menu.php";  ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">

      <div class="container-fluid px-4 col-sm-12">
        <h1 class="h1 pt-3 pb-2 col-12 text-center"> Vistoria do Veículo </h1>

        <!-- div form-->
        <div>
          <form method="post" action="./control/checklistp1.php">

          <?php
            //pegando informações da última vistoria
          if($placacorreta != ''){
            $sql1="SELECT max(idtbvistoria) FROM bdfrota.tbvistoria where placa='$placacorreta'";
            $resultado1=mysqli_query($con, $sql1) or die(mysqli_error($con));
            $row1=mysqli_fetch_array($resultado1, MYSQLI_BOTH);
              $idtbvistoria=$row1[0];

            $sql2="SELECT * FROM bdfrota.tbvistoria where idtbvistoria='$idtbvistoria'"; 
            $resultado2=mysqli_query($con, $sql2) or die(mysqli_error($con));
            $row2=mysqli_fetch_array($resultado2, MYSQLI_BOTH);
              $datavistoria=$row2['datavistoria'];
              $estado=$row2['estado'];
              $avaria=$row2['avaria'];
              $hodometro=$row2['hodometro'];
              $documentacao=$row2['documentacao'];
              $drenagem=$row2['drenagem'];
              $consplaca=$row2['consplaca'];
              $alarme=$row2['alarme'];
              $conscabine=$row2['conscabine'];
              $luzespainel=$row2['luzespainel'];
              $combustivel=$row2['combustivel'];
              $arrefecimento=$row2['arrefecimento'];
              $mtcxdf=$row2['mtcxdf'];
              $eletrica=$row2['eletrica'];
              $macaco=$row2['macaco'];
              $alternador=$row2['alternador'];
              $carroceria=$row2['carroceria'];
              $pneus=$row2['pneus'];
              $freios=$row2['freios'];
              $niveloleo=$row2['niveloleo'];
              $pressaopneu=$row2['pressaopneu'];
              $statusveic=$row2['statusveic'];
              $direcao=$row['direcao'];

              if($avaria=='1'){
                $avaria= 'SIM';
              } elseif($avaria=='0'){
                $avaria= 'NÃO';
              }
          }
            //pegando do sistema atual
            $sql2 = "SELECT b.placa AS placa, b.modelo AS modelo, b.anofabr AS anofabr, a.ccusto AS ccusto, a.codfilial AS codfilial, a.codempresa AS codempresa, b.statusvel AS statusvel FROM bdaniel.tbfuncionario a, bdfrota.tbveiculo b WHERE b.placa= '$placacorreta' AND a.matricula = b.matcond";

            //pegando da tbveiculo
            //$sql="SELECT placa, modelo, anofabr, aplicacao AS ccusto FROM bdfrota.tbveiculo WHERE placa='".$placacorreta."';";

            $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));

            while($row2 = mysqli_fetch_array($resultado2, MYSQLI_ASSOC)){
              $placa = $row2['placa'];
              $idmodelo = $row2['modelo'];
              $anofabricacao = $row2['anofabr'];
              $centrocusto = utf8_encode($row2['ccusto']);
              $statusvel = $row2['statusvel'];
              $codfilial= $row2['codfilial'];
              $codempresa=$row2['codempresa'];

              $sql3="SELECT modelo FROM bdfrota.tbmodeloveic WHERE idtbmodeloveic='".$idmodelo."' ";
              $res3= mysqli_query($con, $sql3) or die(mysqli_error($con));
              $row3= mysqli_fetch_array($res3, MYSQLI_BOTH);
                $modelo = utf8_encode($row3['modelo']);

              $sql4="SELECT * FROM bdaniel.tbfilial WHERE codfilial='$codfilial' AND codempresa='$codempresa' ";
              $res4= mysqli_query($con, $sql4) or die(mysqli_error($con));
              $row4= mysqli_fetch_array($res4, MYSQLI_BOTH);
                $filial = utf8_encode($row4['nome']);
            }

            print"<!--div informações do condutor-->
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
        $sql = "SELECT a.nome AS condutor, a.matricula AS matrcond, a.cpf AS cpf, c.numcnh AS cnh, c.validade AS vencimento, c.categoria AS categoria FROM bdaniel.tbfuncionario a, bdfrota.tbveiculo b, bdfrota.tbcnh c, bdfrota.tbcondutor d  WHERE b.placa= '$placacorreta' AND a.matricula = b.matcond AND a.matricula = c.matricula";

        //$sql="SELECT a.nome AS condutor, a.matricula AS matrcond, b.numcnh AS cnh, b.validade AS vencimento, b.categoria AS categoria, c.cpf AS cpf FROM bdfrota.tbcondutor a, bdfrota.tbcnh b, bdaniel.tbfuncionario c WHERE placaassoc='".$placacorreta."' AND ativo=1 AND a.matricula = b.matricula AND a.matricula = c.matricula; ";
        
        $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

        while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
          $condutor = utf8_encode($row['condutor']);
          $matrcond = $row['matrcond'];
          $cpf = $row['cpf'];
          $cnh =$row['cnh'];
          $validade= $row['vencimento'];
          $categoria = $row['categoria'];

          $validade1=explode(" ", $validade);
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
                      $sql3="SELECT * FROM bdaniel.tbfilial";
                      $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));

                      while($row3 = mysqli_fetch_array($resultado3, MYSQLI_ASSOC)){
                        $idtbfilial = $row3['idtbfilial'];
                        //$codigo = $row3['codfilial'];
                        $descricao = utf8_encode($row3['nome']);
                        $selected = ($filial == $descricao) ? "selected=\"selected\"" : null;
                        
                        print"<option value='$descricao' $selected> $descricao </option>";
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

            <!-- div informações checklist-->
            <div class='mt-4 border-top border-dark pt-2'>
            <h6> Informações da vistoria </h6>
              <!--div tipo-->
              <div class='row mt-2'>
                <label class='col-5 col-form-label' for='tipo'> Tipo:<span style='color: red;'>*</span> </label>
                <div class='col-6'>
                  <select name="tipo" id='tipo' class="form-select">
                      <option value=''> Selecione a opção </option>
                  <?php
                    $sql="SELECT * FROM bdfrota.tbatipovist WHERE idtbatipovist='1';";
                    $resultado=mysqli_query($con, $sql) or die(mysqli_error($con));
                    while($row=mysqli_fetch_array($resultado, MYSQLI_BOTH)){
                      $idtbatipovist=$row['idtbatipovist'];
                      $tipo=utf8_encode($row['tipo']);

                      if($idtbatipovist=='1'){
                        $selected='selected';
                      } else {
                        $selected = '';
                      }
                      print"
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

                while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
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
                <label class='col-5 col-form-label' for='datavistoria'> Data da Vistoria:<span style='color: red;'>*</span> </label>
                <div class='col-6'>
                  <input type='date' name='datavistoria' id='datavistoria' class='form-control' required>
                </div>
              </div>

              <!--div tipo-->
              <div class='row mt-2'>
                <label class='col-5 col-form-label' for='tipo'> Status:<span style='color: red;'>*</span> </label>
                <div class='col-6'>
                  <select name="statusveic" id='statusveic' class="form-select">
                      <option value=''> Selecione a opção </option>
                    <?php
                      $sql3="SELECT * FROM bdfrota.tbastatusvel"; //idtbastatusvel, status
                      $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));

                      while($row3 = mysqli_fetch_array($resultado3, MYSQLI_ASSOC)){
                        $idtbastatusvel = $row3['idtbastatusvel'];
                        $statusveic = utf8_encode($row3['status']);

                        $selected = ($statusvel == $idtbastatusvel) ? "selected=\"selected\"" : null;
                        
                        print"<option value='$idtbastatusvel' $selected> $statusveic </option>";
                      }

                    ?>                   
                  </select>
                </div>
              </div>

              <!--div estado geral -->
              <div class='column mt-2 align-items-center'>
                <label class='col-form-label' for='estado'> Estado Geral:<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $estado</p>";?>
                <div class='col-12' style= 'flex-wrap: wrap;'>
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
              <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $avaria</p>";?>

              <!--div hodometro -->
              <div class='row mt-2'>
                <label class='col-5 col-form-label' for='hodometro'> Hodômetro:<span style='color: red;'>*</span><?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $hodometro km</p>";?> </label>
                <div class='col-6'>
                  <input type='text' name='hodometro' id='hodometro' class='form-control' placeholder='Hodômetro' required>
                </div>
              </div>

              <!--div nivel do tanque -->
              <div class='row mt-2'>
                <label class='col-5 col-form-label' for='niveltanque'> Nível do Tanque:<span style='color: red;'>*</span> </label>
                <div class='col-6'>
                  <input type='text' name='niveltanque' id='niveltanque' class='form-control' placeholder='Nível do tanque' required>
                </div>
              </div>              
            </div>

            <!-- div Itens personalizados da vistoria -->
            <div class='mt-4 border-top border-dark pt-2'>
              <h6> Itens personalizados da vistoria </h6>
              <!--div documentacao-->
              <div class='column mt-2 align-items-center'>
                <label class='col-form-label' for='documentacao'> Documentação do veículo:<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $documentacao</p>";?>
                <div class=''>
                  <input type="radio" id="pessimo1" name="documentacao" value="pessimo" required>
                  <label for="pessimo1">Péssimo</label>
                  <input class="ms-1" type="radio" id="ruim1" name="documentacao" value="ruim">
                  <label for="ruim1">Ruim</label>
                  <input class="ms-1" type="radio" id="regular1" name="documentacao" value="regular">
                  <label for="regular1">Regular</label>
                  <input class="ms-1" type="radio" id="bom1" name="documentacao" value="bom">
                  <label for="bom1">Bom</label>
                  <input class="ms-1" type="radio" id="otimo1" name="documentacao" value="otimo">
                  <label for="otimo1">Ótimo</label>
                </div>
              </div>

              <!--div drenagem-->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='drenagem'> Drenagem do filtro separador de água:<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $drenagem</p>";?>
                <div class=''>
                  <input type="radio" id="pessimo2" name="drenagem" value="pessimo" required>
                  <label for="pessimo2">Péssimo</label>
                  <input class="ms-1" type="radio" id="ruim2" name="drenagem" value="ruim">
                  <label for="ruim2">Ruim</label>
                  <input class="ms-1" type="radio" id="regular2" name="drenagem" value="regular">
                  <label for="regular2">Regular</label>
                  <input class="ms-1" type="radio" id="bom2" name="drenagem" value="bom">
                  <label for="bom2">Bom</label>
                  <input class="ms-1" type="radio" id="otimo2" name="drenagem" value="otimo">
                  <label for="otimo2">Ótimo</label>
                </div>
              </div>

              <!--div estado de conservação placa-->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='consplaca'> Estado de conservação da placa:<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $consplaca</p>";?>
                <div class=''>
                  <input type="radio" id="pessimo3" name="consplaca" value="pessimo" required>
                  <label for="pessimo3">Péssimo</label>
                  <input class="ms-1" type="radio" id="ruim3" name="consplaca" value="ruim">
                  <label for="ruim3">Ruim</label>
                  <input class="ms-1" type="radio" id="regular3" name="consplaca" value="regular">
                  <label for="regular3">Regular</label>
                  <input class="ms-1" type="radio" id="bom3" name="consplaca" value="bom">
                  <label for="bom3">Bom</label>
                  <input class="ms-1" type="radio" id="otimo3" name="consplaca" value="otimo">
                  <label for="otimo3">Ótimo</label>
                </div>
              </div>

              <!--div alarme sonoro-->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='alarme'> Alarme sonoro:<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $alarme</p>";?>
                  <div class=''>
                  <input type="radio" id="pessimo4" name="alarme" value="pessimo" required>
                  <label for="pessimo4">Péssimo</label>
                  <input class="ms-1" type="radio" id="ruim4" name="alarme" value="ruim">
                  <label for="ruim4">Ruim</label>
                  <input class="ms-1" type="radio" id="regular4" name="alarme" value="regular">
                  <label for="regular4">Regular</label>
                  <input class="ms-1" type="radio" id="bom4" name="alarme" value="bom">
                  <label for="bom4">Bom</label>
                  <input class="ms-1" type="radio" id="otimo4" name="alarme" value="otimo">
                  <label for="otimo4">Ótimo</label>
                </div>
              </div>

              <!--div estado de conservação cabine-->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='conscabine'> Estado de conservação da cabine:<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $conscabine</p>";?>
                <div class=''>
                  <input type="radio" id="pessimo5" name="conscabine" value="pessimo" required>
                  <label for="pessimo5">Péssimo</label>
                  <input class="ms-1" type="radio" id="ruim5" name="conscabine" value="ruim">
                  <label for="ruim5">Ruim</label>
                  <input class="ms-1" type="radio" id="regular5" name="conscabine" value="regular">
                  <label for="regular5">Regular</label>
                  <input class="ms-1" type="radio" id="bom5" name="conscabine" value="bom">
                  <label for="bom5">Bom</label>
                  <input class="ms-1" type="radio" id="otimo5" name="conscabine" value="otimo">
                  <label for="otimo5">Ótimo</label>
                </div>
              </div>

              <!--div funcionamento das luzes do painel-->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='luzespainel'> Funcionamento das luzes do painel:<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $luzespainel</p>";?>
                <div class=''>
                  <input type="radio" id="pessimo6" name="luzespainel" value="pessimo" required>
                  <label for="pessimo6">Péssimo</label>
                  <input class="ms-1" type="radio" id="ruim6" name="luzespainel" value="ruim">
                  <label for="ruim6">Ruim</label>
                  <input class="ms-1" type="radio" id="regular6" name="luzespainel" value="regular">
                  <label for="regular6">Regular</label>
                  <input class="ms-1" type="radio" id="bom6" name="luzespainel" value="bom">
                  <label for="bom6">Bom</label>
                  <input class="ms-1" type="radio" id="otimo6" name="luzespainel" value="otimo">
                  <label for="otimo6">Ótimo</label>
                </div>
              </div>

              <!--div nivel combustivel e vazamentos-->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='combustivel'> Nível de combustível e existência de vazamentos:<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $combustivel</p>";?>
                <div class=''>
                  <input type="radio" id="pessimo7" name="combustivel" value="pessimo" required>
                  <label for="pessimo7">Péssimo</label>
                  <input class="ms-1" type="radio" id="ruim7" name="combustivel" value="ruim">
                  <label for="ruim7">Ruim</label>
                  <input class="ms-1" type="radio" id="regular7" name="combustivel" value="regular">
                  <label for="regular7">Regular</label>
                  <input class="ms-1" type="radio" id="bom7" name="combustivel" value="bom">
                  <label for="bom7">Bom</label>
                  <input class="ms-1" type="radio" id="otimo7" name="combustivel" value="otimo">
                  <label for="otimo7">Ótimo</label>
                </div>
              </div>

              <!--div nivel sistema de arrefecimento e vazamentos-->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='arrefecimento'> Sistema de arrefecimento e existência de vazamentos:<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $arrefecimento</p>";?>
                <div class=''>
                  <input type="radio" id="pessimo8" name="arrefecimento" value="pessimo" required>
                  <label for="pessimo8">Péssimo</label>
                  <input class="ms-1" type="radio" id="ruim8" name="arrefecimento" value="ruim">
                  <label for="ruim8">Ruim</label>
                  <input class="ms-1" type="radio" id="regular8" name="arrefecimento" value="regular">
                  <label for="regular8">Regular</label>
                  <input class="ms-1" type="radio" id="bom8" name="arrefecimento" value="bom">
                  <label for="bom8">Bom</label>
                  <input class="ms-1" type="radio" id="otimo8" name="arrefecimento" value="otimo">
                  <label for="otimo8">Ótimo</label>
                </div>
              </div>

              <!-- div motor, caixa e diferencial -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='mtcxdf'> Motor, caixa e diferencial:<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $mtcxdf</p>";?>
                <div class=''>
                  <input type="radio" id="pessimo10" name="mtcxdf" value="pessimo" required>
                  <label for="pessimo10">Péssimo</label>
                  <input class="ms-1" type="radio" id="ruim10" name="mtcxdf" value="ruim">
                  <label for="ruim10">Ruim</label>
                  <input class="ms-1" type="radio" id="regular10" name="mtcxdf" value="regular">
                  <label for="regular10">Regular</label>
                  <input class="ms-1" type="radio" id="bom10" name="mtcxdf" value="bom">
                  <label for="bom10">Bom</label>
                  <input class="ms-1" type="radio" id="otimo10" name="mtcxdf" value="otimo">
                  <label for="otimo10">Ótimo</label>
                </div>
              </div>

              <!-- div sistema elétrico -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='eletrica'> Sistema elétrico:<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $eletrica</p>";?>
                <div class=''>
                  <input type="radio" id="pessimo11" name="eletrica" value="pessimo" required>
                  <label for="pessimo11">Péssimo</label>
                  <input class="ms-1" type="radio" id="ruim11" name="eletrica" value="ruim">
                  <label for="ruim11">Ruim</label>
                  <input class="ms-1" type="radio" id="regular11" name="eletrica" value="regular">
                  <label for="regular11">Regular</label>
                  <input class="ms-1" type="radio" id="bom11" name="eletrica" value="bom">
                  <label for="bom11">Bom</label>
                  <input class="ms-1" type="radio" id="otimo11" name="eletrica" value="otimo">
                  <label for="otimo11">Ótimo</label>
                </div>
              </div>

              <!-- div macaco, chave de roda, triângulo e extintor -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='macaco'> Macaco, chave de roda, triângulo e extintor:<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $macaco</p>";?>
                <div class=''>
                  <input type="radio" id="pessimo13" name="macaco" value="pessimo" required>
                  <label for="pessimo13">Péssimo</label>
                  <input class="ms-1" type="radio" id="ruim13" name="macaco" value="ruim">
                  <label for="ruim13">Ruim</label>
                  <input class="ms-1" type="radio" id="regular13" name="macaco" value="regular">
                  <label for="regular13">Regular</label>
                  <input class="ms-1" type="radio" id="bom13" name="macaco" value="bom">
                  <label for="bom13">Bom</label>
                  <input class="ms-1" type="radio" id="otimo13" name="macaco" value="otimo">
                  <label for="otimo13">Ótimo</label>
                </div>
              </div> 

              <!-- div luz indicadora alternador -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='alternador'> Luz indicadora alternador:<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $alternador</p>";?>
                <div class=''>
                  <input type="radio" id="pessimo14" name="alternador" value="pessimo" required>
                  <label for="pessimo14">Péssimo</label>
                  <input class="ms-1" type="radio" id="ruim14" name="alternador" value="ruim">
                  <label for="ruim14">Ruim</label>
                  <input class="ms-1" type="radio" id="regular14" name="alternador" value="regular">
                  <label for="regular14">Regular</label>
                  <input class="ms-1" type="radio" id="bom14" name="alternador" value="bom">
                  <label for="bom14">Bom</label>
                  <input class="ms-1" type="radio" id="otimo14" name="alternador" value="otimo">
                  <label for="otimo14">Ótimo</label>
                </div>
              </div>

              <!-- div conservação carroceria -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='carroceria'> Estado de conservação da carroceria:<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $carroceria</p>";?>
                <div class=''>
                  <input type="radio" id="pessimo15" name="carroceria" value="pessimo" required>
                  <label for="pessimo15">Péssimo</label>
                  <input class="ms-1" type="radio" id="ruim15" name="carroceria" value="ruim">
                  <label for="ruim15">Ruim</label>
                  <input class="ms-1" type="radio" id="regular15" name="carroceria" value="regular">
                  <label for="regular15">Regular</label>
                  <input class="ms-1" type="radio" id="bom15" name="carroceria" value="bom">
                  <label for="bom15">Bom</label>
                  <input class="ms-1" type="radio" id="otimo15" name="carroceria" value="otimo">
                  <label for="otimo15">Ótimo</label>
                </div>
              </div>

              <!-- div estado geral dos pneus -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='pneus'> Estado geral dos pneus:<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $pneus</p>";?>
                <div class=''>
                  <input type="radio" id="pessimo16" name="pneus" value="pessimo" required>
                  <label for="pessimo16">Péssimo</label>
                  <input class="ms-1" type="radio" id="ruim16" name="pneus" value="ruim">
                  <label for="ruim16">Ruim</label>
                  <input class="ms-1" type="radio" id="regular16" name="pneus" value="regular">
                  <label for="regular16">Regular</label>
                  <input class="ms-1" type="radio" id="bom16" name="pneus" value="bom">
                  <label for="bom16">Bom</label>
                  <input class="ms-1" type="radio" id="otimo16" name="pneus" value="otimo">
                  <label for="otimo16">Ótimo</label>
                </div>
              </div>

              <!-- div freios -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='freios'> Freios:<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $freios</p>";?>
                <div class=''>
                  <input type="radio" id="pessimo17" name="freios" value="pessimo" required>
                  <label for="pessimo17">Péssimo</label>
                  <input class="ms-1" type="radio" id="ruim17" name="freios" value="ruim">
                  <label for="ruim17">Ruim</label>
                  <input class="ms-1" type="radio" id="regular17" name="freios" value="regular">
                  <label for="regular17">Regular</label>
                  <input class="ms-1" type="radio" id="bom17" name="freios" value="bom">
                  <label for="bom17">Bom</label>
                  <input class="ms-1" type="radio" id="otimo17" name="freios" value="otimo">
                  <label for="otimo17">Ótimo</label>
                </div>
              </div>

              <!-- div nível de óleo do motor -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='niveloleo'> Nível de óleo do motor:<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $niveloleo</p>";?>
                <div class=''>
                  <input type="radio" id="pessimo18" name="niveloleo" value="pessimo" required>
                  <label for="pessimo18">Péssimo</label>
                  <input class="ms-1" type="radio" id="ruim18" name="niveloleo" value="ruim">
                  <label for="ruim18">Ruim</label>
                  <input class="ms-1" type="radio" id="regular18" name="niveloleo" value="regular">
                  <label for="regular18">Regular</label>
                  <input class="ms-1" type="radio" id="bom18" name="niveloleo" value="bom">
                  <label for="bom18">Bom</label>
                  <input class="ms-1" type="radio" id="otimo18" name="niveloleo" value="otimo">
                  <label for="otimo18">Ótimo</label>
                </div>
              </div>

              <!-- div pressão dos pneus -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='pressaopneu'> Pressão dos pneus:<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $pressaopneu</p>";?>
                <div class=''>
                  <input type="radio" id="pessimo20" name="pressaopneu" value="pessimo" required>
                  <label for="pessimo20">Péssimo</label>
                  <input class="ms-1" type="radio" id="ruim20" name="pressaopneu" value="ruim">
                  <label for="ruim20">Ruim</label>
                  <input class="ms-1" type="radio" id="regular20" name="pressaopneu" value="regular">
                  <label for="regular20">Regular</label>
                  <input class="ms-1" type="radio" id="bom20" name="pressaopneu" value="bom">
                  <label for="bom20">Bom</label>
                  <input class="ms-1" type="radio" id="otimo20" name="pressaopneu" value="otimo">
                  <label for="otimo20">Ótimo</label>
                </div>
              </div>

              <!-- div sistema de direção -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='direcao'> Sistema de direção:<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 mt-1 blockquote-footer'> Última vistoria: $direcao</p>";?>
                <div class=''>
                  <input type="radio" id="pessimo21" name="direcao" value="pessimo" required>
                  <label for="pessimo21">Péssimo</label>
                  <input class="ms-1" type="radio" id="ruim21" name="direcao" value="ruim">
                  <label for="ruim21">Ruim</label>
                  <input class="ms-1" type="radio" id="regular21" name="direcao" value="regular">
                  <label for="regular21">Regular</label>
                  <input class="ms-1" type="radio" id="bom21" name="direcao" value="bom">
                  <label for="bom21">Bom</label>
                  <input class="ms-1" type="radio" id="otimo21" name="direcao" value="otimo">
                  <label for="otimo21">Ótimo</label>
                </div>
              </div>          

            </div>

            <!--div observação -->
              <div class='mt-2 border-top'>
                <label class='col-5 col-form-label' for='observacao'> Observação: </label>
                <div class='col-12'>
                  <textarea type='text' name='observacao' id='observacao' class='form-control' placeholder='Observação'> </textarea> 
                </div>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../src/js/scripts.js"></script>

<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>-->
  

  <script type='text/javascript' src='/var/www/html/presenca/src/vendor/bootstrap/js/bootstrap.min.js'></script>

  <script>window.jQuery || document.write('<script src="src/vendor/jquery/jquery.min.js"><\/script>')</script>

  <script type="text/javascript">
    $(document).ready(function () {
      //$('#tabela1').DataTable();

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


    });
  </script>

</body>
</html>