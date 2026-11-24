<?php
  date_default_timezone_set('America/Sao_Paulo');
  // Defina o tempo de vida da sessão para 1 hora
  $lifetime = 3600; // 1 hora em segundos

  // Configurar os parâmetros do cookie da sessão
  session_set_cookie_params($lifetime);

  $encoding = 'UTF-8';

  // Iniciar a sessão
  session_start();
  $nome = $_SESSION['nome'];
  $usuariof = $_SESSION['usuario'];
  $matricula1 = $_SESSION['matricula'];
  $perfil = $_SESSION['perfil'];
  $tipo = $_SESSION['tipo'];

  if(empty($matricula1)){
    $matricula1=$_POST['matr_autor'];
    $perfil = $_POST['perfil_autor'];
    $n='ok';

  }

  /*if ($perfil == null){
    echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.close();</script>";

  } elseif ($perfil == '0' || $perfil == '1' || $perfil == '2'|| $perfil == '3'){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.close();</script>";

  }elseif ($matricula1 == '160030'||$matricula1 == '410109'||$matricula1 == '501285'||$matricula1 == '410039' || $matricula1=='411425' || $matricula1 == '003931'){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.close();</script>";

    //*window.location=\"../index.php\"

  }*/

  /*||$matricula1 == '501640'*/

  $_SESSION['perfil'] = $perfil;
  $_SESSION['matricula'] = $matricula1;
  $_SESSION['nome'] = $nome;
  $_SESSION['usuario'] = $usuariof;
  $_SESSION['tipo']=$tipo;

  $link = $_SERVER["REQUEST_URI"];

  if(strpos($link, "=") !== false){
    $idtbveiculo = $_GET['idtbveiculo'];
  } else {
    $idtbveiculo = $_POST['idtbveiculo'];
  }

  if($idtbveiculo==''){
    $placaveic = $_POST['placa'];

    if(strpos($placaveic, "-") !== false){
      $placa = str_replace("-", "", $placaveic);
    } else {
      $placa = str_replace(" ", "", $placaveic);
    }
  }

  include"../conecta03.php";
  //include"conecta.php";
  //include_once"./func/log.php";
  header("Content-type: text/html; charset=utf-8");
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

    <!--link Material Symbols - simbolos do input-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

    <!--data tables - css e js-->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">

    <!-- estilos próprios -->
    <link href="../src/css/styles.css" rel="stylesheet" />
    
  </head>
  <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

    <!--nav-->
    <?php include"menu-teste.php"; ?>

    <div id="layoutSidenav_content">

      <main style="width: 100%;">

        <div class="container-fluid px-4" style="width: 100%;"> 
          <h1 class="h1 pt-2 pb-2 m-auto text-center"> Cadastro de Manutenção </h1>

          <!-- div do formulario -->
          <form method="post" action="./control/cadastrarmanpreventiva.php" style="width: 80%;" class="m-auto" enctype="multipart/form-data">
            <div class='mt-2 d-flex col-sm-12 justify-content-start'>
              <?php
              //$sql = "SELECT placa, hodometro, modelo, oficina, statusvel, idlocador, status FROM bdfrota.tbveiculo WHERE placa= '$placa';";

              if($idtbveiculo==''){

                $sql = "SELECT placa, hodometro, modelo, oficina, statusvel, idlocador, status, ccusto, matcond FROM bdfrota.tbveiculo WHERE placa= '$placa';";
              }else{
                $sql = "SELECT placa, hodometro, modelo, oficina, statusvel, idlocador, status, ccusto, matcond FROM bdfrota.tbveiculo WHERE idtbveiculo= '$idtbveiculo';";
              }

              $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
            
              while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                $placa = $row['placa'];
                $hodometro = $row['hodometro'];
                $matcond = $row['matcond'];
                $modelo = $row['modelo'];
                $oficina = utf8_encode($row['oficina']);
                $statusvel = utf8_encode($row['statusvel']);
                $idlocador = $row['idlocador'];
                $statusesp = utf8_encode($row['status']);
                //$ccusto = utf8_encode($row['ccusto']);
                //$ccusto =  strtoupper($ccusto);
                //$ccusto = mb_convert_case($ccusto, MB_CASE_UPPER, $encoding);
              }
              

              $sql1 = "SELECT nome, ccusto FROM bdaniel.tbfuncionario WHERE matricula= '$matcond';";

              $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));

              while($row1 = mysqli_fetch_array($resultado1, MYSQLI_ASSOC)){
                //$nomecond = $row1['nome'];
                $ccusto = utf8_encode($row1['ccusto']);
                $ccusto = mb_convert_case($ccusto, MB_CASE_UPPER, $encoding);
              }

              $sql2 = "SELECT modelo FROM bdfrota.tbmodeloveic WHERE idtbmodeloveic= '$modelo';";
              $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));

              while($row2 = mysqli_fetch_array($resultado2, MYSQLI_ASSOC)){
                $modelof = $row2['modelo'];
              }

              $sql3 = "SELECT fantasia FROM bdfrota.tbfornecedor WHERE idtbfornecedor='$idlocador' ";
              $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));

              $row3 = mysqli_fetch_array($resultado3, MYSQLI_ASSOC);
                $fornecedor = $row2['fantasia'];
              

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

                <!-- div data
                <div class='ms-1 flex-colum col-sm-3'>
                  <label class='form-label' for='data'>Data Manutenção:<span style='color: red;'>*</span></label>
                  <input class='form-control' type='date' name='data' id='data'>
                </div>-->
              </div>

              <!-- div statusveic, atualizadoem, solicitante, -->
              <div class='mt-2 d-flex col-sm-12 justify-content-start'>
                <!-- divatualizadoem-->
                <div class='ms-1 flex-colum col-sm-2'>
                  <label class='form-label' for='atualizadoem'>Atualizado em:</label>
                  <input class='form-control' type='date' name='atualizadoem' id='atualizadoem'>
                </div>

                <!-- div solicitante-->
                <div class='ms-1 flex-colum col-sm-5'>
                  <label class='form-label' for='solicitante'>Solicitante:</label>              
                  <input class='form-control' type='text' name='solicitante' id='solicitante' placeholder='Solicitante'>
                </div>

                <!-- div ccusto-->
                <div class='ms-1 flex-colum col-sm-3'>
                  <label class='form-label' for='ccusto'>Centro de custo: <span style="color: red;">*</span> </label>              
                  <select class="form-select" name="ccusto" required>
                    <option value=""> Selecione o centro de custo  </option>
                    <?php  
                      $sql="SELECT descricao AS ccusto FROM BdPonto.tbccusto ORDER BY descricao;";
                      $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                    
                      while($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)){
                        //$idtbfornecedor = $row['idtbfornecedor'];
                        //$fantasia = utf8_encode($row['fantasia']);
                        $ccusto1 = utf8_encode($row[0]);


                        print "<option value='$ccusto1'> $ccusto1 </option>";
                      }
                    ?> 
                  </select>
                </div>
              </div>

              <!-- div status, etapa, dataocorrencia, modelo -->
              <div class='mt-2 d-flex col-sm-12 justify-content-start'>
              <!-- div status da manutenção-->
                <div class='ms-1 flex-colum col-sm-3'>
                  <label class='form-label' for='status'>Status da manutenção:<span style="color: red;">*</span> </label>              
                  <!--<input class='form-control' type='text' name='status' id='status' placeholder='Status da manutenção'>-->
                  <select class='form-select' type='text' name='status' id='status' required>
                    <option value="">Selecione...</option>
                    <option value="ABERTO">ABERTO</option>
                    <option value="CONCLUIDO">CONCLUIDO</option>
                    <option value="CANCELADO">CANCELADO</option>

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
              <div class='mt-2 d-flex col-sm-12 justify-content-start'>
                

                <?php print"
                <!-- div condutor
                <div class='ms-1 flex-colum col-sm-6'>
                  <label class='form-label' for='condutor'>Matrícula do condutor: </label>
                  <input class='form-control' type='text' name='condutor' id='condutor' placeholder='Matrícula do condutor' value='$'>
                </div>-->";
              ?>
              </div>

              <!-- div  fornman, oficina -->
              <div class='mt-2 d-flex col-sm-12 justify-content-start'>
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
              <div class='mt-2 d-flex col-sm-12 justify-content-start'>
                <!-- div descricao-->
                <div class='ms-1 flex-colum col-sm-10'>
                  <label class='form-label' for='descricao'>Descrição: </label>              
                  <textarea class='form-control' type='text' name='descricao' id='descricao' placeholder ='Descrição' rows='4'></textarea>
                </div>              
              </div>

              <!-- div dataagendamento, hora de agendamento, prevsaida-->
              <div class='mt-2 d-flex col-sm-12 justify-content-start'>
                <!-- div dataagendamento-->
                <div class='ms-1 flex-colum col-sm-3'>
                  <label class='form-label' for='dataagendamento'>Data de agendamento:<span style="color: red;">*</span> </label>              
                  <input class='form-control' type='date' name='dataagendamento' id='dataagendamento' required>
                </div>

                <!-- div horaagendamento-->
                <div class='ms-1 flex-colum col-sm-3'>
                  <label class='form-label' for='horaagendamento'>Hora do agendamento: </label>              
                  <input class='form-control' type='time' name='horaagendamento' id='horaagendamento'>
                </div>

                <!-- div anexararquivo-->
                <div class='ms-1 flex-colum col-sm-4'>
                  <label class='form-label' for='arquivo'>Anexar arquivo: </label>              
                  <input class='form-control' type='file' name='arquivo' id='arquivo'>
                </div>
              </div>

              <!-- div  dataentrada, dataretirada, tipopagamento, reembolsoaprov, -->
              <div class='mt-2 d-flex col-sm-12 justify-content-start'>
                <!-- div prevsaida-->
                <div class='ms-1 flex-colum col-sm-3'>
                  <label class='form-label' for='prevsaida'>Previsão de saída: <span style="color: red;">*</span></label>              
                  <input class='form-control' type='date' name='prevsaida' id='prevsaida' required>
                </div>

                <!-- div dataentrada-->
                <div class='ms-1 flex-colum col-sm-2'>
                  <label class='form-label' for='dataentrada'>Data de entrada:<span style="color: red;">*</span> </label>              
                  <input class='form-control' type='date' name='dataentrada' id='dataentrada' required>
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
              </div>

              <!-- div  valorreembolso, valoroficina, valordesconto, valormaoobra, -->
              <div class='mt-2 d-flex col-sm-12 justify-content-start'>
                <!-- div reembolsoaprov -->
                <div class="ms-1 col-sm-3 d-flex flex-column justify-content-start">
                  <label class='form-label' for='reembolsoaprov'>Reembolso aprovado:  </label> 
                  <select name="reembolsoaprov" class="form-select" >
                    <option value="0"> Não </option>
                    <option value="1"> Sim </option>
                  </select>
                </div>

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
              </div>

              <!-- div  valormaterial, valortransp, outrosvalor, descontarcond -->
              <div class='mt-2 d-flex col-sm-12 justify-content-start'>
                <!-- div valormaoobra-->
                <div class='ms-1 flex-colum col-sm-3'>
                  <label class='form-label' for='valormaoobra'>Valor mão de obra: </label>              
                  <input class='form-control valor' type='text' name='valormaoobra' id='valormaoobra' placeholder='R$ 0,00'>
                </div>

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
              </div>

              <!-- div  datavencimento, datapagamento, formapagam, condicaopag -->
              <div class='mt-2 d-flex col-sm-12 justify-content-start'>
                <!-- div descontarcond -->
                <div class="ms-1 col-sm-3 d-flex flex-column justify-content-start">
                  <label class="form-label" for='descontarcond'>Descontado do condutor:  </label> 
                  <select name="descontarcond" class="form-select" >
                    <option value="0"> Não </option>
                    <option value="1"> Sim </option>
                  </select>
                </div>

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
              </div>

              <!-- div  numparc, valorparcela, dataprimparc, protocolo -->
              <div class='mt-2 d-flex col-sm-12 justify-content-start'>
                <!-- div condicaopag -->
                <div class='ms-1 flex-colum col-sm-3'>
                  <label class='form-label' for='condicaopag'>Condição de pagamento: </label>              
                  <input class='form-control' type='text' name='condicaopag' id='condicaopag' placeholder='Condição de pagamento'>
                </div>

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

                  
              </div>

              <!-- div dataconclusao, placaanterior -->
              <div class='mt-2 d-flex col-sm-12 justify-content-start'>
                <!-- div protocolo -->
                <div class='ms-1 flex-colum col-sm-3'>
                  <label class='form-label' for='protocolo'>Protocolo: </label>              
                  <input class='form-control' type='text' name='protocolo' id='protocolo' placeholder ='Protocolo'>
                </div>

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

              <!-- observação-->
              <div class='mt-2 d-flex col-sm-12 justify-content-start'>
                <!-- div descricao-->
                <div class='ms-1 flex-colum col-sm-10'>
                  <label class='form-label' for='observacao'>Observação: </label>              
                  <textarea class='form-control' type='text' name='observacao' id='observacao' placeholder ='Observação' rows='4'></textarea>
                </div>              
              </div>


              <!-- div texto campos obrigatório -->
              <div style="color: red;" class="pt-2 col-sm-12">
                <p>* Campos obrigatórios.<p>
              </div>

              <!-- div botão -->
              <div class="mt-3 pb-2 d-flex col-sm-12 justify-content-center">
                <div>
                  <input class="btn btn-success" type='submit' value='Confirmar cadastro' />
                </div>
                  <!--href='./listagemmanutencaoaberta.php'-->
                <div class="ms-5">
                  <a class="btn btn-danger" onclick='window.close();'> Cancelar cadastro </a>
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

    <!-- jquery e masks -->
    <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/gh/plentz/jquery-maskmoney@master/dist/jquery.maskMoney.min.js"></script>

    <!-- datatables -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>

    <!-- fontawesome -->
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!-- bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <!-- scripts próprios -->
    <script src="../src/js/scripts.js"></script>
    
    <!-- script local -->
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