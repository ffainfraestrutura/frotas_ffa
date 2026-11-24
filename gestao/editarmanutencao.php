<?php
date_default_timezone_set('America/Sao_Paulo');
// Defina o tempo de vida da sessão para 1 hora
$lifetime = 3600; // 1 hora em segundos

// Configurar os parâmetros do cookie da sessão
session_set_cookie_params($lifetime);

// Iniciar a sessão
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula1 = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$tipo= $_SESSION['tipo'];

if(empty($perfil)){
  $perfil = $_POST['perfil'];
  $matricula1 = $_POST['mat_autor'];
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
$_SESSION['tipo'] = $tipo;

$idtbmanprev = $_POST['idtbmanprev'];

include"../conecta03.php";
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

  <title> Editar Manutenção Preventiva </title>

  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />

  <!--link Material Symbols - simbolos do input-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <!-- vendor jquery js -->
  <!--<script type='text/javascript' src='src/vendor/jquery/jquery-3.2.1.min.js'></script>
  <script type='text/javascript' src='src/vendor/jquery/jquery.min.js'></script>-->

  <!--data tables - css e js
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>-->

  <!-- jquery e masks -->
  <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

  <script type="text/javascript" src="https://cdn.jsdelivr.net/gh/plentz/jquery-maskmoney@master/dist/jquery.maskMoney.min.js"></script>
</head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

  <!--nav-->
  <?php include"./menu-teste.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;">

      <div class="container-fluid px-4" style="width: 100%;"> 
        <h1 class="h1 pt-2 pb-2 m-auto text-center"> Editar Manutenção </h1>

        <!-- div do formulario -->
        <div class="m-auto" style="width: 80%;">
          <form method="post" action="./control/editarmanutencao.php" enctype="multipart/form-data">

            <!--row-->
            <div class='mt-2 d-flex col-sm-12 justify-content-start'>
            <!-- div placa-->
              <?php 
            $sql = "SELECT * FROM bdfrota.tbmanprev WHERE idtbmanprev= '$idtbmanprev';";

            $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
           
            while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
              $idtbmanprev = $row['idtbmanprev'];
              $placa = $row['placa'];
              $tipo = $row['tipo'];
              $hodometro = $row['hodometro'];
              $data= $row['data']; 
              
              $statusveic = utf8_encode($row['statusveic']);
              $atualizadoem = $row['atualizadoem'];
              $solicitante = utf8_encode($row['solicitante']);
              $status = utf8_encode($row['status']);
              $etapa = utf8_encode($row['etapa']);
              $dataocorrencia = $row['dataocorrencia'];
              $modelo = utf8_encode($row['modelo']);
              $Km = $row['Km'];
              $fornman = utf8_encode($row['fornman']);
              $descricao = utf8_encode($row['descricao']);
              $ccusto = $row['ccusto'];
              
              //$condutor = utf8_encode($row['condutor']);
              
              $oficina = utf8_encode($row['oficina']);
              $dataagendamento = $row['dataagendamento'];
              $prevsaida = $row['prevsaida'];
              $dataentrada = $row['dataentrada'];
              $dataretirada = $row['dataretirada'];
              $tipopagamento = utf8_encode($row['tipopagamento']);
              $reembolsoaprov = $row['reembolsoaprov'];

              $valorreembolso = $row['valorreembolso'];
              $valoroficina = $row['valoroficina'];
              $valordesconto = $row['valordesconto'];
              $valormaoobra = $row['valormaoobra'];
              $valormaterial = $row['valormaterial'];
              $valortransp = $row['valortransp'];
              $outrosvalor = $row['outrosvalor'];
              
              $descontarcond = $row['descontarcond'];
              $datavencimento = $row['datavencimento'];
              $datapagamento = $row['datapagamento'];
              $formapagam = utf8_encode($row['formapagam']);
              $condicaopag = utf8_encode($row['condicaopag']);
              $numparc = $row['numparc'];
              $valorparcela = $row['valorparcela'];
              $dataprimparc = $row['dataprimparc'];
              $protocolo = $row['protocolo'];
              $dataconclusao = $row['dataconclusao'];
              $placaanterior = $row['placaanterior'];
              $observacao = utf8_encode($row['observacao']);
              $doc = utf8_encode($row['doc']);

              $data1 = explode(" ", $data);
              $data1 = $data1[0];

              $atualizadoem1 = explode(" ", $atualizadoem);
              $atualizadoem1 = $atualizadoem1[0];

              $dataocorrencia1 = explode(" ", $dataocorrencia);
              $dataocorrencia1 = $dataocorrencia1[0];

              $dataagendamento1 = explode(" ", $dataagendamento);
              $dataagendamento2 = $dataagendamento1[0];

              $horaagendamento = $dataagendamento1[1];

              $prevsaida1 = explode(" ", $prevsaida);
              $prevsaida1 = $prevsaida1[0];

              $dataentrada1 = explode(" ", $dataentrada);
              $dataentrada1 = $dataentrada1[0];

              $dataretirada1 = explode(" ", $dataretirada);
              $dataretirada1 = $dataretirada1[0];

              $datavencimento1 = explode(" ", $datavencimento);
              $datavencimento1 = $datavencimento1[0];

              $datapagamento1 = explode(" ", $datapagamento);
              $datapagamento1 = $datapagamento1[0];

              $dataprimparc1 = explode(" ", $dataprimparc);
              $dataprimparc1 = $dataprimparc1[0];

              $dataconclusao1 = explode(" ", $dataconclusao);
              $dataconclusao1 = $dataconclusao1[0];
              
              if(strpos($valorreembolso, ".")!== false){
                $valorreembolso = str_replace(".", ",", $valorreembolso);
                $valorreembolso = "R$ $valorreembolso";

              }else{
                $valorreembolso = "R$ $valorreembolso,00";
              }

              if(strpos($valoroficina, ".")!==false){
                $valoroficina = str_replace(".", ",", $valoroficina);
                $valoroficina = "R$ $valoroficina";

              }else{
                $valoroficina = "R$ $valoroficina,00";
              }

              if(strpos($valordesconto, ".")!==false){
                $valordesconto = str_replace(".", ",", $valordesconto);
                $valordesconto = "R$ $valordesconto";
                
              }else{
                $valordesconto = "R$ $valordesconto,00";
              }

              if(strpos($valormaoobra, ".")!==false){
                $valormaoobra = str_replace(".", ",", $valormaoobra);
                $valormaoobra = "R$ $valormaoobra";

              }else{
                $valormaoobra = "R$ $valormaoobra,00";

              }

              if(strpos($valormaterial, ".")!==false){
                $valormaterial = str_replace(".", ",", $valormaterial);
                $valormaterial = "R$ $valormaterial";

              }else{
                $valormaterial = "R$ $valormaterial,00";

              }

              if(strpos($valortransp, ".")!==false){
                $valortransp = str_replace(".", ",", $valortransp);
                $valortransp = "R$ $valortransp";

              }else{
                $valortransp = "R$ $valortransp,00";

              }

              if(strpos($outrosvalor, ".")!==false){
                $outrosvalor = str_replace(".", ",", $outrosvalor);
                $outrosvalor = "R$ $outrosvalor";

              }else{
                $outrosvalor = "R$ $outrosvalor,00";
              }

              if(strpos($valorparcela, ".")!==false){
                $valorparcela = str_replace(".", ",", $valorparcela);
                $valorparcela = "R$ $valorparcela";

              }else{
                $valorparcela = "R$ $valorparcela,00";
              }

              if(strpos($doc,"..")!== false){
                $doc= substr($doc,2);
              }

              $docfind = substr($doc,20);

              $dirin = 'docs/docmanutencao/';

              $diretorio = dir($dirin);

              while($arquivo = $diretorio -> read()){
                if ($arquivo != '.' && $arquivo != '..'){
                  //print "arquivo $arquivo/$docfind,";
                  
                  if(strpos($arquivo, $docfind) !== false){
                    
                    $href = ".$doc";
                    break;
                    
                  }else{
                    
                    $href = "http://ffasip.ddns.net:4242/frotas/gestao$doc";
                    //break;
                    
                  }
                  
                }
              }                 

              $diretorio -> close();

              
            }

            print"<input type='hidden' name='matr_autor' id='matr_autor' value='$matricula1'>
              <input type='hidden' name='idtbmanprev' id='idtbmanprev' value='$idtbmanprev'>
              
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
                  <option value= 'MP' <?=($tipo == 'MP')?'selected':''?> >Preventiva</option> 
                  <option value='MC' <?=($tipo == 'MC')?'selected':''?>  >Corretiva</option>
                  <option value='OS' <?=($tipo == 'OS')?'selected':''?>  >Ordem de Serviço</option> 
                  <option value='SS' <?=($tipo == 'SS')?'selected':''?>  >Sinistro</option> 
                  <option value='FR' <?=($tipo == 'FR')?'selected':''?>  >Furto/Roubo</option> 
                </select>
              </div>
            

            <?php
            print"
            <!-- div hodometro-->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='hodometro'>Hodômetro:<span style='color: red;'>*</span> </label>              
                <input class='form-control' type='text' name='hodometro' id='hodometro' placeholder='Nº do Hodômetro (em Km)' value='$hodometro' required>
              </div>

              <!-- div data
              <div class='ms-1 flex-colum col-sm-3'>
                <label class='form-label' for='data'>Data Manutenção:<span style='color: red;'>*</span></label>
                <input class='form-control' type='date' name='data' id='data' value='$data1'>
              </div>-->
            </div>";
            ?>

            <!-- div statusveic, atualizadoem, solicitante, -->
            <!--mt-1 row-->
            <div class='mt-2 d-flex col-sm-12 justify-content-start'>
            <?php
              print"
              <!-- divatualizadoem-->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='atualizadoem'>Atualizado em:</label>
                <input class='form-control' type='date' name='atualizadoem' id='atualizadoem' value='$atualizadoem1'>
              </div>

              <!-- div solicitante-->
              <div class='ms-1 flex-colum col-sm-5'>
                <label class='form-label' for='solicitante'>Solicitante: </label>              
                <input class='form-control' type='text' name='solicitante' id='solicitante' placeholder='' value='$solicitante'>
              </div>";
            ?> 
              <!-- div ccusto-->
              <div class='ms-1 flex-colum col-sm-3'>
                <label class='form-label' for='ccusto'>Centro de custo: </label>              
                <select class="form-select" name="ccusto">
                  <option value=" "> Selecione o centro de custo  </option>
                  <?php  
                    $sql="SELECT DISTINCT(ccusto) FROM bdaniel.tbfuncionario WHERE ccusto<>'' AND status = 'Ativo' ORDER BY ccusto;";
                    $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                  
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
            </div>

            <!-- div status, etapa, dataocorrencia, modelo -->
            <!-- mt-1 row  -->
            <div class='mt-2 d-flex col-sm-12 justify-content-start'>
            
            <!-- div status da manutenção-->
              <div class='ms-1 flex-colum col-sm-3'>
                <label class='form-label' for='status'>Status da manutenção: </label>              
                <!--<input class='form-control' type='text' name='status' id='status' placeholder='' value='$status'>-->
                <select class='form-select' type='text' name='status' id='status' required>
                  <option value=''>Selecione...</option>
                  <option value='ABERTO'<?=($status == 'ABERTO')?'selected':''?>>ABERTO</option>
                  <option value='CONCLUIDO' <?=($status == 'CONCLUIDO')?'selected':''?> id='opcaoconc'>CONCLUÍDO</option>
                  <option value="CANCELADO" <?=($status == 'CANCELADO')?'selected':''?>>CANCELADO</option>

                </select>
              </div>
          <?php
              print"
              <!-- div etapa-->
              <div class='ms-1 flex-colum col-sm-3'>
                <label class='form-label' for='etapa'>Etapa da manutenção:</label>
                <input class='form-control' type='text' name='etapa' id='etapa' value='$etapa'>
              </div>

              <!-- div dataocorrencia-->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='dataocorrencia'>Data de ocorrência: </label>              
                <input class='form-control' type='date' name='dataocorrencia' id='dataocorrencia' value='$dataocorrencia1'>
              </div>

              <!-- div -->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='modelo'>Modelo:</label>              
                <input class='form-control' type='text' name='modelo' id='modelo' placeholder='Modelo' value='$modelo'>
              </div>";
            ?>  
            </div>

            <!-- div ccusto,condutor, fornman, oficina -->
            <!--mt-1 row-->
            <div class='mt-2 d-flex col-sm-12 justify-content-start'>
            <?php
              print"
              <!-- div condutor
              <div class='ms-1 flex-colum col-sm-6'>
                <label class='form-label' for='condutor'>Matrícula do condutor: </label>
                <input class='form-control' type='text' name='condutor' id='condutor' value='$'>
              </div>-->"?>
            </div>

            <!-- div  fornman, oficina -->
            <!--mt-1 row-->
            <div class='mt-2 d-flex col-sm-12 justify-content-start'>
            <?php print"
              <!-- div fornman-->
              <div class='ms-1 flex-colum col-sm-4'>
                <label class='form-label' for='fornman'>Fornecedor da manutenção: </label>              
                <input class='form-control' type='text' name='fornman' id='fornman' value='$fornman'>
              </div>

              <!-- div oficina-->
              <div class='ms-1 flex-colum col-sm-6'>
                <label class='form-label' for='oficina'>Oficina: </label>              
                <input class='form-control' type='text' name='oficina' id='oficina' placeholder='Oficina' value='$oficina'>
              </div>";
            ?>  
            </div>

            <!-- div  descricao, dataagendamento, prevsaida -->
            <!--mt-2 d-flex col-sm-12 justify-content-start-->
            <div class='mt-1 row'>
            <?php print"
              <!-- div descricao-->
              <div class='ms-1 flex-colum col-sm-10'>
                <label class='form-label' for='descricao'>Descrição: </label>              
                <textarea class='form-control' type='text' name='descricao' id='descricao'>$descricao</textarea>
              </div>";
            ?>  
            </div>

            <div class='mt-2 d-flex col-sm-12 justify-content-start'>
            <?php print"
              <!-- div dataagendamento-->
              <div class='ms-1 flex-colum col-sm-3'>
                <label class='form-label' for='dataagendamento'>Data de agendamento: </label>              
                <input class='form-control' type='date' name='dataagendamento' id='dataagendamento' value='$dataagendamento2'>
              </div>

              <!-- div horaagendamento-->
              <div class='ms-1 flex-colum col-sm-3'>
                <label class='form-label' for='horaagendamento'>Hora do agendamento: </label>              
                <input class='form-control' type='time' name='horaagendamento' id='horaagendamento' value='$horaagendamento'>
              </div>";
            ?>
              
            </div>

            <div class='mt-2 d-flex col-sm-12 justify-content-start align-items-end'>
              <!-- div anexararquivo-->
              <div class='ms-1 flex-colum col-sm-4'>
                <label class='mb-0 form-label' for='arquivo'>Anexar arquivo: </label><p class="mb-0" style="font-size: 10px;">Por favor, envie arquivos de até 2M.</p>             
                <input class='form-control mt-0' type='file' name='arquivo' id='arquivo'>

              </div>

              <?php
              if(!empty($doc)){
                print"
              <div class='ms-1 flex-colum col-sm-4'>
                <a href='$href' target='_blank' class='btn  btn-secondary'>Último Documento Anexado</a>
                
                <!--<p>O documento anexado anterioremente não será mais listado.</p>-->
              </div>";
              }

              ?>
            </div>

            <!-- div  dataentrada, dataretirada, tipopagamento, reembolsoaprov, -->
            <!--mt-2 d-flex col-sm-12 justify-content-start row-->
            <div class='mt-2 d-flex col-sm-12 justify-content-start'>
            <?php print"
            <!-- div prevsaida-->
              <div class='ms-1 flex-colum col-sm-3'>
                <label class='form-label' for='prevsaida'>Previsão de saída: </label>              
                <input class='form-control' type='date' name='prevsaida' id='prevsaida' value='$prevsaida1'>
              </div>

              <!-- div dataentrada-->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='dataentrada'>Data de entrada: </label>              
                <input class='form-control' type='date' name='dataentrada' id='dataentrada' value='$dataentrada1'>
              </div>

              <!-- div dataretirada-->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='dataretirada'>Data de retirada: </label>              
                <input class='form-control' type='date' name='dataretirada' id='dataretirada' value='$dataretirada1'>
              </div>

              <!-- div tipopagamento-->
              <div class='ms-1 flex-colum col-sm-3'>
                <label class='form-label' for='tipopagamento'>Tipo de pagamento: </label>              
                <input class='form-control' type='text' name='tipopagamento' id='tipopagamento' value='$tipopagamento'>
              </div>";
            ?>               
            </div>

            <!-- div  valorreembolso, valoroficina, valordesconto, valormaoobra, -->
            <!--mt-2 d-flex col-sm-12 justify-content-start- row-->
            <div class='mt-2 d-flex col-sm-12 justify-content-start'>
              <!-- div reembolsoaprov -->
              <div class="ms-1 col-sm-3 d-flex flex-column justify-content-start">
                <label class='form-label' for='reembolsoaprov'>Reembolso aprovado:  </label> 
                <select name="reembolsoaprov" class="form-select" >
                  <option value="0" <?=($reembolsoaprov == '0')?'selected':''?> > Não </option>
                  <option value="1" <?=($reembolsoaprov == '1')?'selected':''?>> Sim </option>
                </select>
              </div>
            <?php print"
              <!-- div valorreembolso-->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='valorreembolso'>Valor reembolso: </label>              
                <input class='form-control valor' type='text' name='valorreembolso' id='valorreembolso' placeholder='R$ 0,00' value='$valorreembolso'>
              </div>

              <!-- div valoroficina-->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='valoroficina'>Valor oficina: </label>              
                <input class='form-control valor' type='text' name='valoroficina' id='valoroficina' placeholder='R$ 0,00' value='$valoroficina'>
              </div>

              <!-- div valordesconto-->
              <div class='ms-1 flex-colum col-sm-3'>
                <label class='form-label' for='valordesconto'>Valor do desconto: </label>              
                <input class='form-control valor' type='text' name='valordesconto' id='valordesconto' placeholder='R$ 0,00' value='$valordesconto'>
              </div>";
            ?>
            </div>

            <!-- div  valormaterial, valortransp, outrosvalor, descontarcond -->
            <!--mt-2 d-flex col-sm-12 justify-content-start row-->
            <div class='mt-2 d-flex col-sm-12 justify-content-start'>
            <?php print"
              <!-- div valormaoobra-->
              <div class='ms-1 flex-colum col-sm-3'>
                <label class='form-label' for='valormaoobra'>Valor mão de obra: </label>              
                <input class='form-control valor' type='text' name='valormaoobra' id='valormaoobra' placeholder='R$ 0,00' value='$valormaoobra'>
              </div>

              <!-- div valormaterial-->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='valormaterial'>Valor material: </label>              
                <input class='form-control valor' type='text' name='valormaterial' id='valormaterial' placeholder='R$ 0,00' value='$valormaterial'>
              </div>

              <!-- div valortransp -->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='valortransp'>Valor transporte: </label>              
                <input class='form-control valor' type='text' name='valortransp' id='valortransp' placeholder='R$ 0,00' value='$valortransp'>
              </div>

              <!-- div outrosvalor-->
              <div class='ms-1 flex-colum col-sm-3'>
                <label class='form-label' for='outrosvalor'>Outros valores: </label>              
                <input class='form-control valor' type='text' name='outrosvalor' id='outrosvalor' placeholder='R$ 0,00' value='$outrosvalor'>
              </div>";
            ?>                             
            </div>

            <!-- div  datavencimento, datapagamento, formapagam, condicaopag -->
            <!--mt-2 d-flex col-sm-12 justify-content-start row-->
            <div class='mt-2 d-flex col-sm-12 justify-content-start'>
              <!-- div descontarcond -->
              <div class="ms-1 col-sm-3 d-flex flex-column justify-content-start">
                <label class="form-label" for='descontarcond'>Descontado do condutor:  </label> 
                <select name="descontarcond" class="form-select" >
                  <option value="0" <?=($descontarcond == '0')?'selected':''?> > Não </option>
                  <option value="1" <?=($descontarcond == '1')?'selected':''?>> Sim </option>
                </select>
              </div> 
            
            <?php print"
              <!-- div datavencimento-->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='datavencimento'>Data de vencimento: </label>              
                <input class='form-control' type='date' name='datavencimento' id='datavencimento' value='$datavencimento1'>
              </div>

              <!-- div datapagamento-->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='datapagamento'>Data de pagamento: </label>              
                <input class='form-control' type='date' name='datapagamento' id='datapagamento' value='$datapagamento1'>
              </div>

              <!-- div formapagam-->
              <div class='ms-1 flex-colum col-sm-3'>
                <label class='form-label' for='formapagam'>Forma de pagamento: </label>              
                <input class='form-control' type='text' name='formapagam' id='formapagam' value='$formapagam'>
              </div>";
            ?>  
            </div>

            <!-- div  numparc, valorparcela, dataprimparc, protocolo -->
            <!--row-->
            <div class='mt-2 d-flex col-sm-12 justify-content-start'>
            <?php print"
              <!-- div condicaopag -->
              <div class='ms-1 flex-colum col-sm-3'>
                <label class='form-label' for='condicaopag'>Condição de pagamento: </label>              
                <input class='form-control' type='text' name='condicaopag' id='condicaopag' value='$condicaopag'>
              </div>

              <!-- div numparc-->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='numparc'>Número de parcelas: </label>              
                <input class='form-control' type='text' name='numparc' id='numparc' value='$numparc'>
              </div>

              <!-- div valorparcela-->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='valorparcela'>Valor de parcela: </label>              
                <input class='form-control valor' type='text' name='valorparcela' id='valorparcela' placeholder='R$ 0,00' value='$valorparcela'>
              </div>

              <!-- div dataprimparc-->
              <div class='ms-1 flex-colum col-sm-3'>
                <label class='form-label' for='dataprimparc'>Data da primeira parcela: </label>              
                <input class='form-control' type='date' name='dataprimparc' id='dataprimparc' value='$dataprimparc1'>
              </div>";
            ?>  
            </div>

            <!-- div dataconclusao, placaanterior -->
            <!--row-->
            <div class='mt-2 d-flex col-sm-12 justify-content-start'>
            <?php print"
              <!-- div protocolo -->
              <div class='ms-1 flex-colum col-sm-3'>
                <label class='form-label' for='protocolo'>Protocolo: </label>              
                <input class='form-control' type='text' name='protocolo' id='protocolo' value='$protocolo'>
              </div>

              <!-- div dataconclusao-->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='dataconclusao'>Data de conclusão: </label>              
                <input class='form-control' type='date' name='dataconclusao' id='dataconclusao' value='$dataconclusao1'>
              </div>

              <!-- div placaanterior -->
              <div class='ms-1 flex-colum col-sm-2'>
                <label class='form-label' for='placaanterior'>Placa anterior: </label>              
                <input class='form-control' type='text' name='placaanterior' id='placaanterior' value='$placaanterior'>
              </div>";
            ?>  
            </div>

            <!-- observação-->
            <div class='mt-2 d-flex col-sm-12 justify-content-start'>
            <?php print"
              <!-- div descricao-->
              <div class='ms-1 flex-colum col-sm-10'>
                <label class='form-label' for='observacao'>Observação: </label>              
                <textarea class='form-control' type='text' name='observacao' id='observacao' placeholder ='Observação' rows='4'>$observacao</textarea>
              </div>";?>              
            </div>

            <!-- div texto campos obrigatório -->
            <div style="color: red;" class="pt-2 col-sm-8 ">
              <p>* Campos obrigatórios.<p>
              <p style="font-size: 12px; font-style: italic;">Para marcar a manutenção como concluída, não se esqueça de informar a data de entrada, a data de retirada e a previsão de saída. </p>
            </div>

             <!-- div botão -->
            <div class="mt-3 pb-2 d-flex col-sm-12 justify-content-center">
              <div>
                <button class="btn btn-success" type='submit'> Confirmar edição </button>
              </div>

              <div class="ms-5">
                <!--href='./listagemmanutencaoaberta.php'-->
                <a class="btn btn-danger" onclick="window.close();"> Descartar edição </a>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="mt-2 text-center mb-1">
        <!--../pdf/fpdf/ordemdeservico.php-->
        <form method="post" action='http://ffasip.ddns.net:4242/frotas/pdf/fpdf/ordemdeservico.php' target='_blank'>
          <?php print"
          <input type='hidden' name='idtbmanprev' value='$idtbmanprev'>";
           ?>
          <button class="btn btn-secondary">Ordem de Serviço</button>
        </form>
      </div>
    </main>

    <footer class="py-4 bg-light mt-auto" >
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

  <script type="text/javascript">
    //mascaras de input
    $(document).ready(function(){
      $('#hodometro').mask('000000000');
      $('.valor').maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:',', affixesStay: true});
      
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

      if($('#prevsaida').val()=='' || $('#dataentrada').val()=='' || $('#dataretirada').val()==''){
        console.log($('#prevsaida').val());
        console.log($('#dataentrada').val());
        console.log($('#dataretirada').val());
        $("#opcaoconc").attr('hidden', true);

      }


      if($('#prevsaida').val()==''){
        $('#prevsaida').change(function(){
          if($('#prevsaida').val()!='' && $('#dataentrada').val()!='' && $('#dataretirada').val()!=''){
            $("#opcaoconc").attr('hidden', false);
            
          }
        });
      }

      if($('#dataentrada').val()==''){
        $('#dataentrada').change(function(){
          if($('#prevsaida').val()!='' && $('#dataentrada').val()!='' && $('#dataretirada').val()!=''){
            $("#opcaoconc").attr('hidden', false);
            
          }
        });
      }

      if($('#dataretirada').val()==''){
        $('#dataretirada').change(function(){
          if($('#prevsaida').val()!='' && $('#dataentrada').val()!='' && $('#dataretirada').val()!=''){
            $("#opcaoconc").attr('hidden', false);
            
          }
        });
      }
    
    });
  </script>

</body>
</html>