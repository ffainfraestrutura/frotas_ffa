<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome1 = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula1 = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
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

$idtbmulta = $_POST['idtbmulta'];

include"../conecta2.php";
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

  <title> Edição de Multa </title>

  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <!--link Material Symbols - simbolos do input-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <!-- vendor jquery js -->
  <!--<script type='text/javascript' src='../src/vendor/jquery/jquery-3.2.1.min.js'></script>
  <script type='text/javascript' src='../src/vendor/jquery/jquery.min.js'></script>-->

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
        <h1 class="h1 pt-2 pb-2 m-auto text-center"> Edição de Multa </h1>

        <!-- div do formulario -->
        <div>
          <form method="post" action="./control/editarmulta.php" style="width: 80%;" class="m-auto">
            <!-- div infos iniciais-->
            <div class="d-flex col-sm-12 m-auto justify-content-start align-items-end">
              <!-- div placa-->
              <?php
                //print $idtbmulta;
                $sql = "SELECT * FROM bdfrota.tbmulta WHERE idtbmulta = '$idtbmulta'";

                $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

                while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                  $placa = $row['placa'];
                  $filial = utf8_encode($row['filial']);
                  $ccusto = utf8_encode($row['ccusto']);
                  $fornecedor = utf8_encode($row['fornecedor']);
                  $anotacao = utf8_encode($row['anotacao']);
                  $tipoinfracao = utf8_encode($row['tipoinfracao']);
                  $autoinfracao= $row['autoinfracao'];
                  $datainfracao = $row['datainfracao'];
                  $datalimitecond = $row['datalimitecond'];
                  $datalimiteloc = $row['datalimiteloc'];
                  $expedicao = $row['expedicao'];
                  $recebimento = $row['recebimento'];
                  $vencimento = $row['vencimento'];
                  $codigom = $row['codigom'];
                  $pontos = $row['pontos'];
                  $gravidade = utf8_encode($row['gravidade']);
                  $valor= $row['valor'];
                  $valdesconto = $row['valdesconto'];
                  $taxaadm = $row['taxaadm'];
                  $juros = $row['juros'];
                  $valtotal = $row['valtotal'];
                  $orgao = utf8_encode($row['orgao']);
                  $endereco = utf8_encode($row['endereco']);
                  $municipio = utf8_encode($row['municipio']);
                  $matriculac = $row['matriculac'];
                  $nomec = utf8_encode($row['nomec']);
                  $numparcelas = $row['numparcelas'];
                  $valparcelas = $row['valparcelas'];
                  $descontoapartir = $row['descontoapartir'];
                  $descontadocondutor = $row['descontadocondutor'];
                  $tipodesconto = utf8_encode($row['tipodesconto']);
                  $datadesconto = $row['datadesconto'];
                  $realinfrator = $row['realinfrator'];
                  $pessoaindicada = $row['pessoaindicada'];
                  $condassinatura = $row['condassinatura'];
                  $reciboassinado = $row['reciboassinado'];
                  $recusaassinar = $row['recusaassinar']; // , , , , recprotocolo
                  $status=$row['status'];
                  $etapa = $row['etapa'];
                  $descricaoinfra = utf8_encode($row['descricaoinfra']);
                  $valcomdesc = $row['valcomdesc'];
                  $datarecorg = $row['datarecorg'];
                  $protrecorg = $row['protrecorg'];
                  $comprpag = $row['comprpag'];
                  $pgempresa = $row['pgempresa'];
                  $datapagempr = $row['datapagempr'];
                  $motivonpg = utf8_decode($row['motivonpg']);
                  $numprotocolo = $row['numprotocolo'];
                  $expprotocolo = $row['expprotocolo'];
                  $recprotocolo = $row['recprotocolo'];

                  //tratando datas
                  $datainfracao1 = explode(" ", $datainfracao);
                  $horainfracao = $datainfracao1[1];
                  
                  $datalimitecond1 = explode(" ", $datalimitecond);

                  $datalimiteloc1 = explode(" ", $datalimiteloc);

                  $expedicao1 = explode(" ", $expedicao);
                  
                  $recebimento1 = explode(" ", $recebimento);
                  
                  $vencimento1 = explode(" ", $vencimento);

                  $datadesconto1 = explode(" ", $datadesconto);

                  $reciboassinado1 = explode(" ", $reciboassinado);

                  $recusaassinar1 = explode(" ", $recusaassinar);

                  $datarecorg1 = explode(" ", $datarecorg);
                  $datapagempr1 = explode(" ", $datapagempr);
                  $expprotocolo1 = explode(" ", $expprotocolo);
                  $recprotocolo1 = explode(" ", $recprotocolo);

                  //valor
                  $valor= str_replace(".", ",", $valor);
                  $valor = "R$ ".$valor;

                  $valdesconto = str_replace(".", ",", $valdesconto);
                  $valdesconto = "R$ ".$valdesconto;

                  $valtotal = str_replace(".", ",", $valtotal);
                  $valtotal = "R$ ".$valtotal;

                  $valparcelas = str_replace(".", ",", $valparcelas);
                  $valparcelas = "R$ ".$valparcelas;

                  $taxaadm = str_replace(".", ",", $taxaadm);
                  $taxaadm = "R$ ".$taxaadm;

                  $valcomdesc = str_replace(".", ",", $valcomdesc);
                  $valcomdesc = "R$ ".$valcomdesc;
                }


                print " 
              <input type='hidden' name='idtbmulta' id='idtbmulta' required value='$idtbmulta'>
              <input type='hidden' name='matr_autor' id='matr_autor' required value='$matricula1'>

              <div class='col-sm-3 d-flex flex-column'>
                <label class='form-label' for='placa'>Placa:<span style='color: red;'>*</span> </label>
                <input class='form-control' style='min-width: 100px;' type='text' name='placa' id='placa' pattern='[Aa-Zz]{3}[0-9]{1}[Aa-Zz]{1}[0-9]{2}|[Aa-Zz]{3}[0-9]{4}' maxlength='7' placeholder='Nº placa do veículo multado' required value='$placa'>
              </div>

              <!-- div filial caso seja input
              <div class='mt-2 d-flex input-group'>
                <label class='form-label mt-2' for='filial'>Filial:<span style='color: red;'>*</span> </label>
                <input class='form-control' style='width: 80%;' type='text' name='filial' id='filial' required> 
              </div>-->
                ";
              ?>
              <!-- div filial select-->
              <div class="ms-1 col-sm-3 d-flex flex-column justify-content-start">
                <p class="mb-0">Filial: <span style="color: red;">*</span></p>
                  <select class="form-select" name="filial" required>
                    <option value=" "> Selecione a filial </option>
                    <?php  
                      //$sql="SELECT * FROM bdfrota.tbfilial WHERE ativo=1;";
                    $sql="SELECT idtbfilial, nome FROM bdaniel.tbfilial";
                      $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
                    
                      while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                        /*$idtbfilial = $row['idtbfilial'];
                        $codigo = $row['codigo'];
                        $descricao= utf8_encode($row['descricao']);
                        $ativo = $row['ativo'];*/

                        $idtbfilial = $row['idtbfilial'];
                        $nomefilial = $row['nome'];

                        $selected = ($filial == $idtbfilial) ? "selected=\"selected\"" : null;

                        print "<option value='$idtbfilial' $selected > $nomefilial </option>";
                      }
                    ?> 
                  </select>
              </div>

              <!-- div central de custo -->
              <div class='ms-1 col-sm-3 d-flex flex-column'>
                <label class='form-label mt-2' for='ccusto'>Central de custo:<span style='color: red;'>*</span> </label>
                <!--<input class='form-control' type='text' name='ccusto' id='ccusto' placeholder='Central de custo' required value='$ccusto'>-->
                <select class="form-select" name="ccusto" required>
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
            
              <!-- div fornecedor select-->
              <div class="ms-1 col-sm-3 d-flex flex-column justify-content-start">
                <p class="mb-0">Fornecedor: <span style="color: red;">*</span></p>
                  <select class="form-select" name="fornecedor" required>
                    <option value=" "> Selecione o fornecedor  </option>
                    <?php  
                      $sql="SELECT idtbfornecedor, fantasia FROM bdfrota.tbfornecedor;";
                      $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
                    
                      while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                        $idtbfornecedor = $row['idtbfornecedor'];
                        $fantasia = utf8_encode($row['fantasia']);
                        
                        $selected = ($fornecedor == $idtbfornecedor) ? "selected=\"selected\"" : null;

                        print "<option value='$idtbfornecedor' $selected> $fantasia </option>";
                      }
                    ?> 
                  </select>
              </div>
            </div>

          <?php
          print"
            <!-- div anotação, tipo de infração, nº auto infração, expedida em -->
            <div class='mt-2 d-flex col-sm-12 m-auto justify-content-start align-items-start'>
              <!-- div anotação -->
              <div class='col-sm-6 d-flex flex-column justify-content-start'>
                <p class='mb-0'>Anotação: </p>
                <textarea class='form-control' name='anotacao' placeholder='Deixe aqui sua anotação'> $anotacao</textarea>
              </div>";
            ?>
            
              <!-- div tipo de infração -->
              <div class="ms-1 col-sm-3 d-flex flex-column justify-content-start">
                <p class="mb-0">Tipo de infração: <span style="color: red;">*</span></p>
                <select name='tipoinfracao' class="form-select" required>
                  <option value='NOTIFICAÇÃO' <?=($tipoinfracao == 'NOTIFICAÇÃO')?'selected':''?> >Notificação</option>
                  <option value="MULTA" <?=($tipoinfracao == 'MULTA')?'selected':''?> >Multa</option>
                  <option value="MULTA FORA DO PRAZO" <?=($tipoinfracao == 'MULTA FORA DO PRAZO')?'selected':''?> >Multa fora do prazo</option>
                  <option value="AGRAVO" <?=($tipoinfracao == 'AGRAVO')?'selected':''?> >Agravo</option>
                </select>
              </div>

            <?php
            print "
              <!-- div nº auto infração -->
              <div class='ms-1 col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='autoinfracao'>Número do auto de infração: <span style='color: red;'>*</span> </label> 
                <input type='text' class='form-control' name='autoinfracao' id='autoinfracao'  placeholder='Nº do auto de infração' required value='$autoinfracao'/>
              </div>
            </div>

            <!-- div data, hora, datas limites -->
            <div class='mt-2 d-flex col-sm-12 m-auto justify-content-start align-items-end'>
              <!-- div data -->
              <div class='col-sm-2 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='datainfracao'>Data da infração: <span style='color: red;'>*</span> </label> 
                <input type='date' class='form-control' name='datainfracao' id='datainfracao' required value='$datainfracao1[0]'/>
              </div>

              <!-- div hora -->
              <div class='ms-1 col-sm-1 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='horainfracao'>Hora infr.: <span style='color: red;'>*</span> </label> 
                <input type='time' class='form-control' name='horainfracao' id='horainfracao' required value='$horainfracao'/>
              </div>

              <!-- div limite condutor -->
              <div class='ms-1 col-sm-4 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='datalimitecond'>Data limite - apresentar o condutor: <span style='color: red;'>*</span> </label> 
                <input type='date' class='form-control' name='datalimitecond' id='datalimitecond' required value='$datalimitecond1[0]'/>
              </div>

              <!-- div limite locador -->
              <div class='ms-1 col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='datalimiteloc'>Data limite - apresentar a locadora: <span style='color: red;'>*</span> </label> 
                <input type='date' class='form-control' name='datalimiteloc' id='datalimiteloc' required value='$datalimiteloc1[0]'/>
              </div>

              <!-- div expedida em -->
              <div class='ms-1 col-sm-2 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='expedicao'>Multa expedida em: <span style='color: red;'>*</span> </label> 
                <input type='date' class='form-control' name='expedicao' id='expedicao' required value= '$expedicao1[0]'/>
              </div>
            </div>

            <!-- div recebida em, data venc multa, código da multa, pontos -->
            <div class='mt-2 d-flex col-sm-12 m-auto justify-content-start align-items-end'>
              <!-- div recebida em -->
              <div class='col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='recebimento'>Multa recebida em: <span style='color: red;'>*</span> </label> 
                <input type='date' class='form-control' name='recebimento' id='recebimento' required value='$recebimento1[0]'/>
              </div>

              <!-- div data de vencimento -->
              <div class='ms-1 col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='vencimento'>Data de vencimento da multa: <span style='color: red;'>*</span> </label> 
                <input type='date' class='form-control' name='vencimento' id='vencimento' required value='$vencimento1[0]'/>
              </div>

              <!-- div código da multa -->
              <div class='ms-1 col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='codigom'>Código da multa: <span style='color: red;'>*</span> </label> 
                <input type='text' class='form-control' name='codigom' id='codigom' onkeypress='return event.charCode >= 48 && event.charCode <= 57' placeholder='Código da multa' required value='$codigom'/>
              </div>

               <!-- div pontos -->
              <div class='ms-1 col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='pontos'>Pontos: <span style='color: red;'>*</span> </label> 
                <input type='text' class='form-control' name='pontos' id='pontos' onkeypress='return event.charCode >= 48 && event.charCode <= 57' placeholder='Nº de pontos da multa' value='$pontos' required/>
              </div>
            </div>";
            ?>
            
            <!--div gravidade, valor, valor com desconto, taxa adm -->
            <div class="mt-2 d-flex col-sm-12 m-auto justify-content-start align-items-end">
              <!-- div gravidade -->
              <div class=" col-sm-3 d-flex flex-column justify-content-start">
                <!--<label class="mb-0" for='gravidade'>Gravidade da multa: </label> 
                <input type="text" class="form-control" name="gravidade" id='gravidade' />-->
                <p class="mb-0">Gravidade da multa: <span style="color: red;">*</span> </p>
                <select name='gravidade' class="form-select" required>
                  <option value='LEVE' <?=($gravidade == 'LEVE')?'selected':''?> >Leve</option>
                  <option value="MÉDIA" <?=($gravidade == 'MÉDIA')?'selected':''?> >Média</option>
                  <option value="GRAVE" <?=($gravidade == 'GRAVE')?'selected':''?> >Grave</option>
                  <option value="GRAVÍSSIMA" <?=($gravidade == 'GRAVÍSSIMA')?'selected':''?> >Gravíssima</option>
                </select>
              </div>

          <?php
          print"
              <!-- div valor -->
              <div class='ms-1 col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='valor'>Valor da multa: <span style='color: red;'>*</span> </label> 
                <input type='text' class='form-control valor' name='valor' id='valor' placeholder='0,00'  value='$valor' required/>
              </div>

              <!-- div valor do desconto -->
              <div class=' ms-1 col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='valdesconto'>Valor do desconto: <span style='color: red;'>*</span> </label> 
                <input type='text' class='form-control valor' name='valdesconto' id='valdesconto' placeholder='R$ 0,00'  value='$valdesconto' required/>
              </div>

              <!-- div taxa adm -->
              <div class='ms-1 col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='taxaadm'>Taxa Administrativa: <span style='color: red;'>*</span> </label> 
                <input type='text' class='form-control valor' name='taxaadm' id='taxaadm' placeholder='R$ 0,00'  value='$taxaadm' required/>
              </div>
            </div>

            <!-- div juros, valor total, valor da multa com desconto -->
            <div class='mt-2 d-flex col-sm-12 m-auto justify-content-start align-items-end'>
              <!-- div juros -->
              <div class='col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='juros'>Juros: <span style='color: red;'>*</span> </label> 
                <input type='text' class='form-control' name='juros' id='juros' placeholder='Taxa de juros'   value='$juros' required/>
              </div>

              <!-- div valortotal -->
              <div class='ms-1 col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='valtotal'>Valor total da multa: <span style='color: red;'>*</span> </label> 
                <input type='text' class='form-control valor' name='valtotal' id='valtotal' placeholder='R$ 0,00'   value='$valtotal' required/>
              </div>

              <!-- div valor com desconto -->
              <div class=' ms-1 col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='valcomdesc'>Valor da multa com desconto: <span style='color: red;'>*</span> </label> 
                <input type='text' class='form-control valor' name='valcomdesc' id='valcomdesc' placeholder='R$ 0,00'  value='$valcomdesc' required/>
              </div>
            </div>

            <!-- orgão atuador, endereço, municipio -->
            <div class='mt-2 d-flex col-sm-12 m-auto justify-content-start align-items-end'>
              <!-- div orgão autuador -->
              <div class='col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='orgao'>Órgão autuador: <span style='color: red;'>*</span> </label> 
                <input type='text' class='form-control' name='orgao' id='orgao' placeholder='Nome do órgão' required value='$orgao'/>
              </div>

              <!-- div endereço -->
              <div class='ms-1 col-sm-6 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='endereco'>Endereço: <span style='color: red;'>*</span> </label> 
                <input type='text' class='form-control' name='endereco' id='endereco' placeholder='Endereço' required value='$endereco'/>
              </div>

              <!-- div municipio -->
              <div class='ms-1 col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='municipio'>Municipio: <span style='color: red;'>*</span> </label> 
                <input type='text' class='form-control' name='municipio' id='municipio' placeholder='Município' required value='$municipio'/>
              </div>
            </div>";?>

            <!-- div status, etapa, descricaoinfra -->
            <div class="mt-2 d-flex col-sm-12 m-auto justify-content-start align-items-start">
              <?php print"
              <!-- div status -->
              <div class='col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='status'>Status da multa: </label> 
                <input type='text' class='form-control' name='status' id='status' value='$status'>
              </div>

              <!-- div etapa -->
              <div class='ms-1 col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='etapa'>Etapa da multa: </label> 
                <input type='text' class='form-control' name='etapa' id='etapa' value='$etapa'>
              </div>

              <!-- div descrição infra -->
              <div class='ms-1 col-sm-6 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='descricao'>Descrição da multa: </label> 
                <textarea class='form-control' name='descricaoinfra' placeholder='Descrição da infração'> $descricaoinfra</textarea>
              </div>
              "; ?>
            </div>


          <?php print"
            <!-- matricula, condutor, nº parcelas, valor da parcela -->
            <div class='mt-2 d-flex col-sm-12 m-auto justify-content-start align-items-end'>
              <!-- div matricula -->
              <div class='col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='matriculac'>Matrícula do Condutor: <span style='color: red;'>*</span> </label> 
                <input type='text' class='form-control' name='matriculac' id='matriculac' placeholder='Matrícula do condutor' onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxlength='6' required value='$matriculac'/>
              </div>

              <!-- div condutor -->
              <div class='ms-1 col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='nomec'>Nome do Condutor: <span style='color: red;'>*</span> </label> 
                <input type='text' class='form-control' name='nomec' id='nomec' placeholder='Nome do Condutor'  required value='$nomec'/>
              </div>

              <!-- div nºparcelas -->
              <div class='ms-1 col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='numparcelas'>Número de parcelas: <span style= 'color: red;'>*</span> </label> 
                <input type='text' class='form-control' name='numparcelas' id='numparcelas' onkeypress='return event.charCode >= 48 && event.charCode <= 57' placeholder='Nº de parcelas' required value='$numparcelas'/>
              </div>

              <!-- div valor das parcelas -->
              <div class='ms-1 col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='valparcelas'>Valor das parcelas: <span style='color: red;'>*</span> </label> 
                <input type='text' class='form-control valor' name='valparcelas' id='valparcelas' placeholder='R 0,00' required value='$valparcelas'/>
              </div>
            </div>

            <!-- descontar a partir, descontado do condutor, tipo desconto, data desconto -->
            <div class='mt-2 d-flex col-sm-12 m-auto justify-content-start align-items-end'>
              <!-- div descontar a partir -->
              <div class='col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='descontoapartir'>Descontar a partir de: <span style='color: red;'>*</span> </label> 
                <!--<input type='text' class='form-control' name='descontoapartir' id='descontoapartir' placeholder='Descontar a partir de...' required value='$descontoapartir'/>-->
                <input class='form-control' type='month' name='descontoapartir' id='descontoapartir' value='$descontoapartir' required>
              </div>";
          ?>
              <!-- div descontado condutor -->
              <div class="ms-1 col-sm-3 d-flex flex-column justify-content-start">
                <p class="mb-0">Descontado do condutor:<span style="color: red;">*</span>  </p> 
                <select name="descontadocondutor" class="form-select" required>
                  <option value="0" <?=($descontadocondutor == '0')?'selected':''?> > Não </option>
                  <option value="1" <?=($descontadocondutor == '1')?'selected':''?>> Sim </option>
                </select>
              </div>

          <?php 
          print "
              <!-- div tipo de desconto -->
              <div class='ms-1 col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='tipodesconto'>Tipo do desconto: </label> 
                <input type='text' class='form-control' name='tipodesconto' id='tipodesconto' placeholder='Tipo de desconto' value='$tipodesconto'>
              </div>

              <!-- div data do desconto -->
              <div class='ms-1 col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='datadesconto'>Data do desconto: <span style='color: red;'>*</span> </label> 
                <input type='date' class='form-control' name='datadesconto' id='datadesconto' required value='$datadesconto1[0]'>
              </div>
            </div>"; 
          ?>
            <!-- real infrator, pessoa indicada, assinatura condutor, data assinatura, data recusa -->
            <div class="mt-2 d-flex col-sm-12 m-auto justify-content-start align-items-end">
              <!-- div real infrator -->
              <div class="col-sm-3 d-flex flex-column justify-content-start">
                <p class="mb-0">Condutor é o real infrator: </p> 
                <select name="realinfrator" class="form-select" required>
                  <option value="0" <?=($realinfrator == '0')?'selected':''?> > Não </option>
                  <option value="1" <?=($realinfrator == '1')?'selected':''?> > Sim </option>
                </select>
              </div>

          <?php print "
              <!-- div pessoa indicada -->
              <div class='ms-1 col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='pessoaindicada'>Pessoa indicada: </label> 
                <input type='text' class='form-control' name='pessoaindicada' id='pessoaindicada' placeholder='Pessoa indicada' value='$pessoaindicada'>
              </div>";  
          ?>

              <!-- div condutor assinou -->
              <div class="ms-1 col-sm-2 d-flex flex-column justify-content-start">
                <p class="mb-0">Condutor assinou: <span style="color: red;">*</span> </p> 
                <select name="condassinatura" class="form-select" required>
                  <option value=" "> </option>
                  <option value="0" <?=($condassinatura == '0')?'selected':''?> > Não </option>
                  <option value="1" <?=($condassinatura == '1')?'selected':''?> > Sim </option>
                </select>
              </div>

          <?php print"
              <!-- div data assinatura -->
              <div class='ms-1 col-sm-2 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='reciboassinado'>Data do recibo assinado: </label> 
                <input type='date' class='form-control' name='reciboassinado' id='reciboassinado' value='$reciboassinado1[0]'>
              </div>

              <!-- div data da recusa -->
              <div class='ms-1 col-sm-2 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='recusaassinar'>Data da recusa em assinar: </label> 
                <input type='date'class='form-control' name='recusaassinar' id='recusaassinar' value='$recusaassinar1[0]'>
              </div>
            </div>";  
          ?>
            <!-- div datarecorg, protrecorg -->
            <div class="mt-2 d-flex col-sm-12 m-auto justify-content-start align-items-start">
              <?php print"
              <!-- div datarecorg -->
              <div class='col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='datarecorg'>Data de recurso ao órgão: </label> 
                <input type='date' class='form-control' name='datarecorg' id='datarecorg' value='$datarecorg1[0]'>
              </div>

              <!-- div protrecorg -->
              <div class='ms-1 col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='protrecorg'>Protocolo de recurso ao órgão: </label> 
                <input type='text' class='form-control' name='protrecorg' id='protrecorg' value='$protrecorg'>
              </div>"; ?>
            </div>

            <!-- div comprpag, pgempresa, datapagempr, motivonpg -->
            <div class="mt-2 d-flex col-sm-12 m-auto justify-content-start align-items-start">
              
              <!-- div comprpag -->
              <div class="col-sm-3 d-flex flex-column justify-content-start">
                <p class="mb-0">Existe comprovante de pagamento? </p> 
                <select name="comprpag" class="form-select">
                  <option value="0" <?=($comprpag == '0')?'selected':''?> > Não </option>
                  <option value="1" <?=($comprpag == '1')?'selected':''?> > Sim </option>
                </select>
              </div>

              <!-- div pgempresa -->
              <div class="ms-1 col-sm-2 d-flex flex-column justify-content-start">
                <p class="mb-0">Pago pela empresa? </p> 
                <select name="pgempresa" class="form-select">
                  <option value="0" <?=($pgempresa == '0')?'selected':''?> > Não </option>
                  <option value="1" <?=($pgempresa == '1')?'selected':''?> > Sim </option>
                </select>
              </div>

            <?php print"
              <!-- div datapagempr -->
              <div class='ms-1 col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='datapagempr'>Data de pagamento: </label> 
                <input type='date' class='form-control' name='datapagempr' id='datapagempr' value='$datapagempr1[0]'>
              </div>

              <!-- div motivonpg -->
              <div class='ms-1 col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='motivonpg'>Motivo não pagamento: </label> 
                <input type='text' class='form-control' name='motivonpg' id='motivonpg' value='$motivonpg'>
              </div>"; ?>
            </div>

            <!-- div numprotocolo, expprotocolo, recprotocolo -->
            <div class="mt-2 d-flex col-sm-12 m-auto justify-content-start align-items-start">
              <?php print"
              <!-- div numprotocolo -->
              <div class='col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='numprotocolo'>Protocolo da multa: </label> 
                <input type='text' class='form-control' name='numprotocolo' id='numprotocolo' value='$numprotocolo'>
              </div>

              <!-- div expprotocolo -->
              <div class='ms-1 col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='expprotocolo'>Dia de expedição: </label> 
                <input type='date' class='form-control' name='expprotocolo' id='expprotocolo' value='$expprotocolo1[0]'>
              </div>

              <!-- div recprotocolo -->
              <div class='ms-1 col-sm-3 d-flex flex-column justify-content-start'>
                <label class='mb-0' for='recprotocolo'>Dia de recebimento: </label> 
                <input type='date' class='form-control' name='recprotocolo' id='recprotocolo' value='$recprotocolo1[0]'>
              </div>"; ?>
            </div>

            <!-- div texto campos obrigatório -->
            <div style="color: red;" class="mt-3 pt-2 col-sm-12 m-auto border-top border-dark">
              <p>* Campos obrigatórios.<p>
            </div>

            <!-- div botão -->
            <div class="mt-3 mb-3 pb-2 d-flex col-sm-12 justify-content-center">
              <div>
                <button class="btn btn-success" type='submit'> Confirmar edição </button>
              </div>

              <div class="ms-5">
                <a class="btn btn-danger" href='./listagemmultas.php'> Cancelar edição </a>
              </div>
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
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>-->

<script type="text/javascript">

  //mascaras de input
    $(document).ready(function(){
      $('#juros').mask('00.00');
      $('#codigom').mask('000-00');
      $('.valor').maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:',', affixesStay: true});

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
    });

  //função para formatar moeda do id - guardada de reserva
  function formatarMoeda() {
    var elemento = document.getElementById('id');
    var valor = elemento.value;

    valor = valor + '';
    valor = parseInt(valor.replace(/[\D]+/g, ''));
    valor = valor + '';
    valor = valor.replace(/([0-9]{2})$/g, ",$1");

    if (valor.length > 6) {
    valor = valor.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");
    }

    elemento.value = valor;
    if(valor == 'NaN') elemento.value = '';
  }

  //função para forçar a entrada de apenas numeros no input text
  function onlynumber(evt) {
   var theEvent = evt || window.event;
   var key = theEvent.keyCode || theEvent.which;
   key = String.fromCharCode( key );
   //var regex = /^[0-9.,]+$/;
   var regex = /^[0-9.]+$/;
   if( !regex.test(key) ) {
      theEvent.returnValue = false;
      if(theEvent.preventDefault) theEvent.preventDefault();
   }
  }

  //função para habilitar ou desabilitar input tipodesconto
  function verifica(value){
    var input = document.getElementById("tipodesconto");

    if(value == '1'){
      input.readonly = false;
    }else {
      input.readonly = true;
    }
  }; 

  //função para habilitar ou desabilitar input pessoaindicadao
  function verifica2(value){
    var input = document.getElementById("pessoaindicada");

    if(value == '0'){
      input.disabled = false;
    }else {
      input.disabled = true;
    }
  }; 

  //função para habilitar ou desabilitar input recusaassinar
  function verifica3(value){
    var input1 = document.getElementById("recusaassinar");
    var input2 = document.getElementById("reciboassinado");

    if(value == '0'){
      input1.disabled = false;
      input2.disabled = true;
      
    }else if(value == '1') {
      input1.disabled = true;
      input2.disabled = false;
    }
  };

</script>

</body>
</html>