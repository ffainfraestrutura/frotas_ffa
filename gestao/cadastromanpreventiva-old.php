<?php
date_default_timezone_set('America/Sao_Paulo');
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
}/*elseif ($perfil <> '1'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}*/

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula1;
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo']=$tipo;

$link = $_SERVER["REQUEST_URI"];
if(strpos($link, "=") !== false){
  $placa = $_GET['placa'];
} else {
  $placa = $_POST['placa'];
}

include"../conecta2.php";
//include"conecta.php";
//include_once"./func/log.php";
header("Content-type: text/html; charset=utf-8");

$sqla1 = "SELECT * FROM bdfrota.tbmanprev WHERE placa ='$placa'";
$resultadoa1= mysqli_query($conexao, $sqla1) or die(mysqli_error($con));
$linhasa1 = mysqli_num_rows($resultadoa1);

//echo "<script language='javascript' type='text/javascript'>alert('$sqla1');</script>";

if($linhasa1>0){
  //SELECT idtbmanprev, status, placa FROM bdfrota.tbmanprev where idtbmanprev=(select max(idtbmanprev) FROM bdfrota.tbmanprev WHERE placa ='$placacorreta')
  $sqla2= "SELECT max(idtbmanprev) FROM bdfrota.tbmanprev WHERE placa ='$placa'";
  $resultadoa2= mysqli_query($conexao, $sqla2) or die(mysqli_error($con));
  $rowa2=mysqli_fetch_array($resultadoa2, MYSQLI_BOTH);
    $maxidtbmanprev = utf8_encode($rowa2[0]);//|| $statusman <> null
print $sql2;
  $sqla3= "SELECT status FROM bdfrota.tbmanprev WHERE idtbmanprev ='$maxidtbmanprev'";
  $resultadoa3= mysqli_query($conexao, $sqla3) or die(mysqli_error($con));
  $rowa3=mysqli_fetch_array($resultadoa3, MYSQLI_BOTH);
    $statusman = utf8_encode($rowa3[0]);

  if($statusman <> 'CONCLUIDO'){
    $r = 'diferente';
    echo"<script language='javascript' type='text/javascript'>alert('Veículo com manutenção em $statusman. Impossível abrir outra manutenção.');window.location=\"./iniciocadastromanpreventiva.php\"</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, , shrink-to-fit=no" />
  <meta name="description" content="FFA" />
  <meta name="author" content="FFA" />
  <link rel="icon" type="image/png" href="../src/images/favicon.png"/>

  <title> Cadastro de Manutenção Preventiva </title>

  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <!--link Material Symbols - simbolos do input-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <!-- vendor jquery js -->
  <!--<script type='text/javascript' src='../src/vendor/jquery/jquery-3.2.1.min.js'></script>
  <script type='text/javascript' src='../src/vendor/jquery/jquery.min.js'></script>-->

  <!--data tables - css e js-->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>

  <!-- jquery e masks -->
  <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

  <script type="text/javascript" src="https://cdn.jsdelivr.net/gh/plentz/jquery-maskmoney@master/dist/jquery.maskMoney.min.js"></script>

  <style type="text/css">
    @media screen and (min-width: 992px) {
      .row {
        --bs-gutter-x: 0rem;
        flex-wrap: nowrap;
      }
    }
  </style>
</head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

  <!--nav-->
  <?php include"menu-teste.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;">

      <div class="container-fluid px-4" style="width: 100%;"> 
        <h1 class="h1 pt-2 pb-2 m-auto text-center"> Cadastro de Manutenção </h1>

        <!-- div do formulario -->
        <form method="post" action="./control/cadastrarmanpreventiva.php" style="width: 80%;" class="m-auto">
        <!--mt-2 d-flex col-sm-12 justify-content-start-->
          <div class='row'>
            <?php

            $sql = "SELECT placa, hodometro, matcond, modelo, oficina, statusvel FROM bdfrota.tbveiculo WHERE placa= '$placa';";

            $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
           
            while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
              $placa = $row['placa'];
              $hodometro = $row['hodometro'];
              $matcond = $row['matcond'];
              $modelo = $row['modelo'];
              $oficina = utf8_encode($row['oficina']);
              $statusvel = utf8_encode($row['statusvel']);
            }

            $sql1 = "SELECT nome, ccusto FROM bdaniel.tbfuncionario WHERE matricula= '$matcond';";

            $resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));

            while($row1 = mysqli_fetch_array($resultado1, MYSQLI_ASSOC)){
              $nomecond = $row1['nome'];
              $ccusto = $row1['ccusto'];
            }

            $sql2 = "SELECT modelo FROM bdfrota.tbmodeloveic WHERE idtbmodeloveic= '$modelo';";

            $resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));

            while($row2 = mysqli_fetch_array($resultado2, MYSQLI_ASSOC)){
              $modelof = $row2['modelo'];
            }

            print"
            <input type='hidden' name='matr_autor' id='matr_autor' value='$matricula1'>
              
              <!-- div placa-->
              <div class='flex-colum col-sm-2'>
                <label class='form-label' for='placa'>Placa:<span style='color: red;'>*</span> </label>
                <input class='form-control' type='text' name='placa' id='placa' placeholder='Nº da Placa' value='$placa' required>
              </div>";
            ?>

              <!-- div plano manutenção-->
              <div class='ms-1 flex-colum col-sm-3'>
                <p class='form-label'>Plano manutenção: <span style='color: red;'>*</span></p>
                <select class='form-select' name='manutencao' required>
                  <option value= 'MP'>Preventiva</option> 
                  <option value='MC'>Corretiva</option>
                  <option value='OS'>Ordem de Serviço</option> 
                  <option value='SS'>Sinistro</option> 
                </select>
              </div>
            

            <?php
            print"
            <!-- div hodometro-->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='hodometro'>Hodômetro:<span style='color: red;'>*</span> </label>              
                <input class='form-control' type='text' name='hodometro' id='hodometro' placeholder='Nº do Hodômetro (em Km)' value='$hodometro' required>
              </div>";
            ?>

              <!-- div data-->
              <div class='ms-1 flex-colum col-sm-3'>
                <label class='form-label' for='data'>Data Manutenção:<span style='color: red;'>*</span></label>
                <input class='form-control' type='date' name='data' id='data' required>
              </div>
            </div>

            <!-- div statusveic, atualizadoem, solicitante, -->
            <!--mt-2 d-flex col-sm-12 justify-content-start-->
            <div class='row'>
            <!-- div statusveic-->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='statusveic'>Status Veículo: </label>              
                <select name="statusveic" class="mb-3 form-select" id='statusveic' required>
                  <option value="">Selecione...</option>
                    <?php
                     $sql = "SELECT * FROM bdfrota.tbastatusvel ORDER BY status";
                      $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
                      
                      while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                        $statusvel1 = utf8_encode($row['status']);
                        $idtbastatusvel = $row['idtbastatusvel'];
                        
                        $selected = ($idtbastatusvel == $statusvel) ? "selected=\"selected\"" : null;

                        print "<option value='$idtbastatusvel' $selected> $statusvel1 </option>
                        ";
                      }
                     ?>
                  </select>
              </div>

              <!-- divatualizadoem-->
              <div class='ms-1 flex-colum col-sm-3'>
                <label class='form-label' for='atualizadoem'>Atualizado em:</label>
                <input class='form-control' type='date' name='atualizadoem' id='atualizadoem'>
              </div>

              <!-- div solicitante-->
              <div class='ms-1 flex-colum col-sm-5'>
                <label class='form-label' for='solicitante'>Solicitante:</label>              
                <input class='form-control' type='text' name='solicitante' id='solicitante' placeholder='Solicitante'>
              </div>
            </div>

            <!-- div status, etapa, dataocorrencia, modelo -->
            <!--mt-2 d-flex col-sm-12 justify-content-start-->
            <div class='row'>
            <!-- div status da manutenção-->
              <div class='ms-1 flex-colum col-sm-3'>
                <label class='form-label' for='status'>Status da manutenção:<span style="color: red;">*</span> </label>              
                <!--<input class='form-control' type='text' name='status' id='status' placeholder='Status da manutenção'>-->
                <select class='form-select' type='text' name='status' id='status' required>
                  <option value="">Selecione...</option>
                  <option value="ABERTO">ABERTO</option>
                  <option value="CONCLUIDO">CONCLUIDO</option>
                </select>
              </div>

              <!-- div etapa-->
              <div class='ms-1 flex-colum col-sm-3'>
                <label class='form-label' for='etapa'>Etapa da manutenção:</label>
                <input class='form-control' type='text' name='etapa' id='etapa' placeholder ='Etapa da manutenção'>
              </div>

              <!-- div dataocorrencia-->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='dataocorrencia'>Data de ocorrência: </label>              
                <input class='form-control' type='date' name='dataocorrencia' id='dataocorrencia'>
              </div>
              <?php print"
              <!-- div -->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='modelo'>Modelo: </label>              
                <input class='form-control' type='text' name='modelo' id='modelo' placeholder='Modelo' value='$modelof'>
              </div>";?> 
            </div>

            <!-- div ccusto,condutor, fornman, oficina -->
            <!--mt-2 d-flex col-sm-12 justify-content-start-->
            <div class='row'>
            <!-- div ccusto-->
              <div class='ms-1 flex-colum col-sm-4'>
                <label class='form-label' for='ccusto'>Centro de custo: </label>              
                <select class="form-select" name="ccusto">
                  <option value=" "> Selecione o centro de custo  </option>
                  <?php  
                    $sql="SELECT DISTINCT(ccusto) FROM bdaniel.tbfuncionario WHERE ccusto<>'' AND status = 'Ativo' ORDER BY ccusto;";
                    $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
                  
                    while($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)){
                      //$idtbfornecedor = $row['idtbfornecedor'];
                      //$fantasia = utf8_encode($row['fantasia']);
                      $ccusto1 = utf8_encode($row[0]);

                      $selected = ($ccusto == $ccusto1) ? "selected=\"selected\"" : null;

                      print "<option value='$ccusto1' $selected> $ccusto1 </option>";
                    }
                  ?> 
                </select>
              </div>

              <?php print"
              <!-- div condutor-->
              <div class='ms-1 flex-colum col-sm-6'>
                <label class='form-label' for='condutor'>Matrícula do condutor: </label>
                <input class='form-control' type='text' name='condutor' id='condutor' placeholder='Matrícula do condutor' value='$matcond'>
              </div>
            </div>";
            ?>

            <!-- div  fornman, oficina -->
            <!--mt-2 d-flex col-sm-12 justify-content-start-->
            <div class='row'>
              <!-- div fornman-->
              <div class='ms-1 flex-colum col-sm-4'>
                <label class='form-label' for='fornman'>Fornecedor da manutenção: </label>              
                <input class='form-control' type='text' name='fornman' id='fornman' placeholder ='Fornecedor da manutenção'>
              </div>

              <!-- div oficina-->
              <?php print"
              <div class='ms-1 flex-colum col-sm-6'>
                <label class='form-label' for='oficina'>Oficina: </label>              
                <input class='form-control' type='text' name='oficina' id='oficina' placeholder='Oficina' value='$oficina'>
              </div>
            </div>";?>

            <!-- div  descricao, dataagendamento, prevsaida -->
            <!--mt-2 d-flex col-sm-12 justify-content-start-->
            <div class='row'>
              <!-- div descricao-->
              <div class='ms-1 flex-colum col-sm-5'>
                <label class='form-label' for='descricao'>Descrição: </label>              
                <input class='form-control' type='text' name='descricao' id='descricao' placeholder ='Descrição'>
              </div>

              <!-- div dataagendamento-->
              <div class='ms-1 flex-colum col-sm-3'>
                <label class='form-label' for='dataagendamento'>Data de agendamento: </label>              
                <input class='form-control' type='date' name='dataagendamento' id='dataagendamento'>
              </div>

              <!-- div prevsaida-->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='prevsaida'>Previsão de saída: </label>              
                <input class='form-control' type='date' name='prevsaida' id='prevsaida'>
              </div>
            </div>

            <!-- div  dataentrada, dataretirada, tipopagamento, reembolsoaprov, -->
            <!--mt-2 d-flex col-sm-12 justify-content-start-->
            <div class='row'>
              <!-- div dataentrada-->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='dataentrada'>Data de entrada: </label>              
                <input class='form-control' type='date' name='dataentrada' id='dataentrada'>
              </div>

              <!-- div dataretirada-->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='dataretirada'>Data de retirada: </label>              
                <input class='form-control' type='date' name='dataretirada' id='dataretirada'>
              </div>

              <!-- div tipopagamento-->
              <div class='ms-1 flex-colum col-sm-3'>
                <label class='form-label' for='tipopagamento'>Tipo de pagamento: </label>              
                <input class='form-control' type='text' name='tipopagamento' id='tipopagamento' placeholder='Tipo de pagamento'>
              </div>

              <!-- div reembolsoaprov -->
              <div class="ms-1 col-sm-3 d-flex flex-column justify-content-start">
                <label class='form-label' for='reembolsoaprov'>Reembolso aprovado:  </label> 
                <select name="reembolsoaprov" class="form-select" >
                  <option value="0"> Não </option>
                  <option value="1"> Sim </option>
                </select>
              </div> 
            </div>

            <!-- div  valorreembolso, valoroficina, valordesconto, valormaoobra, -->
            <!--mt-2 d-flex col-sm-12 justify-content-start-->
            <div class='row'>
              <!-- div valorreembolso-->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='valorreembolso'>Valor reembolso: </label>              
                <input class='form-control valor' type='text' name='valorreembolso' id='valorreembolso' placeholder='R$ 0,00'>
              </div>

              <!-- div valoroficina-->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='valoroficina'>Valor oficina: </label>              
                <input class='form-control valor' type='text' name='valoroficina' id='valoroficina' placeholder='R$ 0,00'>
              </div>

              <!-- div valordesconto-->
              <div class='ms-1 flex-colum col-sm-3'>
                <label class='form-label' for='valordesconto'>Valor do desconto: </label>              
                <input class='form-control valor' type='text' name='valordesconto' id='valordesconto' placeholder='R$ 0,00'>
              </div>

              <!-- div valormaoobra-->
              <div class='ms-1 flex-colum col-sm-3'>
                <label class='form-label' for='valormaoobra'>Valor mão de obra: </label>              
                <input class='form-control valor' type='text' name='valormaoobra' id='valormaoobra' placeholder='R$ 0,00'>
              </div>
            </div>

            <!-- div  valormaterial, valortransp, outrosvalor, descontarcond -->
            <!--mt-2 d-flex col-sm-12 justify-content-start-->
            <div class='row'>
              <!-- div valormaterial-->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='valormaterial'>Valor material: </label>              
                <input class='form-control valor' type='text' name='valormaterial' id='valormaterial' placeholder='R$ 0,00'>
              </div>

              <!-- div valortransp -->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='valortransp'>Valor transporte: </label>              
                <input class='form-control valor' type='text' name='valortransp' id='valortransp' placeholder='R$ 0,00'>
              </div>

              <!-- div outrosvalor-->
              <div class='ms-1 flex-colum col-sm-3'>
                <label class='form-label' for='outrosvalor'>Outros valores: </label>              
                <input class='form-control valor' type='text' name='outrosvalor' id='outrosvalor' placeholder='R$ 0,00'>
              </div>

              <!-- div descontarcond -->
              <div class="ms-1 col-sm-3 d-flex flex-column justify-content-start">
                <label class="form-label" for='descontarcond'>Descontado do condutor:  </label> 
                <select name="descontarcond" class="form-select" >
                  <option value="0"> Não </option>
                  <option value="1"> Sim </option>
                </select>
              </div>                
            </div>

            <!-- div  datavencimento, datapagamento, formapagam, condicaopag -->
            <!--mt-2 d-flex col-sm-12 justify-content-start-->
            <div class='row'>
              <!-- div datavencimento-->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='datavencimento'>Data de vencimento: </label>              
                <input class='form-control' type='date' name='datavencimento' id='datavencimento'>
              </div>

              <!-- div datapagamento-->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='datapagamento'>Data de pagamento: </label>              
                <input class='form-control' type='date' name='datapagamento' id='datapagamento'>
              </div>

              <!-- div formapagam-->
              <div class='ms-1 flex-colum col-sm-3'>
                <label class='form-label' for='formapagam'>Forma de pagamento: </label>              
                <input class='form-control' type='text' name='formapagam' id='formapagam' placeholder ='Forma de pagamento'>
              </div>

              <!-- div condicaopag -->
              <div class='ms-1 flex-colum col-sm-3'>
                <label class='form-label' for='condicaopag'>Condição de pagamento: </label>              
                <input class='form-control' type='text' name='condicaopag' id='condicaopag' placeholder='Condição de pagamento'>
              </div>
            </div>

            <!-- div  numparc, valorparcela, dataprimparc, protocolo -->
            <!--mt-2 d-flex col-sm-12 justify-content-start-->
            <div class='row'>
              <!-- div numparc-->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='numparc'>Número de parcelas: </label>              
                <input class='form-control' type='text' name='numparc' id='numparc' placeholder='Nº de parcelas'>
              </div>

              <!-- div valorparcela-->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='valorparcela'>Valor de parcela: </label>              
                <input class='form-control valor' type='text' name='valorparcela' id='valorparcela' placeholder='R$ 0,00'>
              </div>

              <!-- div dataprimparc-->
              <div class='ms-1 flex-colum col-sm-3'>
                <label class='form-label' for='dataprimparc'>Data da primeira parcela: </label>              
                <input class='form-control' type='date' name='dataprimparc' id='dataprimparc'>
              </div>

              <!-- div protocolo -->
              <div class='ms-1 flex-colum col-sm-3'>
                <label class='form-label' for='protocolo'>Protocolo: </label>              
                <input class='form-control' type='text' name='protocolo' id='protocolo' placeholder ='Protocolo'>
              </div>  
            </div>

            <!-- div dataconclusao, placaanterior -->
            <!--mt-2 d-flex col-sm-12 justify-content-start-->
            <div class='row'>
              <!-- div dataconclusao-->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='dataconclusao'>Data de conclusão: </label>              
                <input class='form-control' type='date' name='dataconclusao' id='dataconclusao' >
              </div>

              <!-- div placaanterior -->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='placaanterior'>Placa anterior: </label>              
                <input class='form-control' type='text' name='placaanterior' id='placaanterior' placeholder='Placa Anterior'>
              </div>
            </div>

            <!-- div texto campos obrigatório -->
            <div style="color: red;" class="pt-2 col-sm-12">
              <p>* Campos obrigatórios.<p>
            </div>

            <!-- div botão -->
            <div class="mt-3 pb-2 d-flex col-sm-12 justify-content-center">
              <div>
                <button class="btn btn-success" type='submit'> Confirmar cadastro </button>
              </div>

              <div class="ms-5">
                <a class="btn btn-danger" href='./listagemmanutencao.php'> Cancelar cadastro </a>
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
              </div>!-->
          </div>
      </div>
    </footer>
  </div>
</div><!--div para fechar a div que fica aberta no menu.php-->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../src/js/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>

<script type="text/javascript">
  $(document).ready(function () {
    //mascara de hodometro
    $('#hodometro').mask('000000000');
    $('.valor').maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:',', affixesStay: true});

    //formatando texto em maiusculas
    $("#solicitante").on("input", function(){
      $(this).val($(this).val().toUpperCase());
    });

    $("#etapa").on("input", function(){
      $(this).val($(this).val().toUpperCase());
    });

    $("#modelo").on("input", function(){
      $(this).val($(this).val().toUpperCase());
    });

    $("#fornman").on("input", function(){
      $(this).val($(this).val().toUpperCase());
    });

    $("#oficina").on("input", function(){
      $(this).val($(this).val().toUpperCase());
    });

    $("#descricao").on("input", function(){
      $(this).val($(this).val().toUpperCase());
    });

    $("#tipopagamento").on("input", function(){
      $(this).val($(this).val().toUpperCase());
    });

    $("#formapagam").on("input", function(){
      $(this).val($(this).val().toUpperCase());
    });

    $("#condicaopag").on("input", function(){
      $(this).val($(this).val().toUpperCase());
    });

    $("#protocolo").on("input", function(){
      $(this).val($(this).val().toUpperCase());
    });

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

    $('#placaanterior').mask('AAA0U00', {
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
</script>

</body>
</html>