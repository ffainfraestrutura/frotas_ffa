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
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$tipo= $_SESSION['tipo'];

if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
} elseif ($perfil == '0' || $perfil == '1' || $perfil == '2'|| $perfil == '3'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}elseif ($matricula == '160030'||$matricula == '410109'||$matricula == '500422'||$matricula == '501285'||$matricula == '410039'||$matricula == '501640'||$matricula == '500459' || $matricula1=='411425' || $matricula1 == '003931'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo']= $tipo;

$link = $_SERVER["REQUEST_URI"];
if(strpos($link, "=") !== false){
  $idtbveiculo = $_GET['idtbveiculo'];
} else {
  $idtbveiculo = $_POST['idtbveiculo'];
}
//$idtbveiculo = $_POST['idtbveiculo'];

include"../conecta2.php";
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

  <title> Editar Cadastro de Veículo </title>

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
  <?php include "./menu-teste.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">

      <div class="container-fluid px-4" style="width: 100%;"> 
        <h1 class="h1 pt-2 pb-2 text-center"> Editar Cadastro de Veículo </h1>

        <!-- div do formulario -->
        <div>
          <form method="post" action="./control/editarcadveiculo.php" enctype="multipart/form-data" style="width: 90%;" class="pb-5 mb-5 m-auto">

            <?php
            $sql = "SELECT * FROM bdfrota.tbveiculo WHERE idtbveiculo= '$idtbveiculo';";

            $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
           
            while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
              $idtbveiculo = $row['idtbveiculo'];
              $placa = $row['placa'];
              $marca = $row['marca'];
              $modelo = $row['modelo'];
              $versao = $row['versao'];
              $categoria = $row['categoria'];
              $anofabr = $row['anofabr'];
              $anomodelo = $row['anomodelo'];
              $tipo = $row['tipo'];
              $aplicacao = $row['aplicacao'];
              $zerokm = $row['zerokm'];
              $renavam = $row['renavam'];
              $chassi = $row['chassi'];
              $nummotor = $row['nummotor'];
              $uf = $row['uf'];
              $combustivel = $row['combustivel'];
              $tanque = $row['tanque'];
              $motorizacao = $row['motorizacao'];
              $cor = $row['cor'];
              $nportas = $row['nportas'];
              $npassageiros = $row['npassageiros'];
              $qtdeixos = $row['qtdeixos'];
              $qtdpneus = $row['qtdpneus'];
              $qtdestepe = $row['qtdestepe'];
              $aro = $row['aro'];
              $calibragem = $row['calibragem'];
              $velocmax = $row['velocmax'];
              $gnv = $row['gnv'];
              $gps = $row['gps'];
              $tagpedagio = $row['tagpedagio'];
              $status = $row['status'];
              $hodometro = $row['hodometro'];
              $tipoposse = $row['tipoposse'];
              $idlocador = $row['idlocador'];
              $hodometroinicial = $row['hodometroinicial'];
              $obsveiculo = $row['obsveiculo'];
              $basegestao = $row['basegestao'];
              $ccusto = $row['ccusto'];

              $statusvel = $row['statusvel'];
              $datamov = $row['datamovimentacao'];
                $datamovimentacao = substr($datamov, 0, 10);
                $horamovimentacao = substr($datamov, 11, 8);
              
              $matcond = $row['matcond'];
              $oficina = $row['oficina'];
              $dtentregaa = $row['dtentrega'];
                  $dtentrega = substr($dtentregaa, 0, 10);
              $dtdevolucaoa = $row['dtdevolucao'];
              $dtdevolucao = substr($dtdevolucaoa, 0, 10);
              $tipovel = $row['tipovel'];
              $situacao = $row['situacao'];
              $doccrlv = $row['doccrlv'];
              $airbag = $row['airbag'];
              $gpsemp = $row['gpsemp'];
              $rack = $row['rack'];
              $ncontloc = $row['ncontloc'];
              $dtdisponivelloca = $row['dtdisponivelloc'];
              $dtdisponivelloc = substr($dtdisponivelloca, 0, 10);
              $dtdevolucaoloca = $row['dtdevolucaoloc'];
              $dtdevolucaoloc = substr($dtdevolucaoloca, 0, 10);
              $valaquisicao = $row['valaquisicao'];
              $totkmmensal = $row['totkmmensal'];
              $blindagem = $row['blindagem'];
              $bo = $row['bo'];
              $basegestaof = $row['basegestao'];
              $ccusto = $row['ccusto'];

              $dttermo = $row['dttermodisp'];
              $dtdisp = $row['dtdesmobilizacao'];

              //valor
              $valaquisicao= str_replace(".", ",", $valaquisicao);
              $valaquisicao= str_replace(" ", ".", $valaquisicao);
              $valaquisicao = "R$ ".$valaquisicao;

              $disable=($tipoposse !== "LOCADO")?'disabled':'';

            }

          print "
          <input type='hidden' name='idtbveiculo' id='idtbveiculo'  value='$idtbveiculo'>
          <input type='hidden' name='matr_autor' id='matr_autor' value='$matricula'>


          <div class='row'>
            <!--div status-->
            <div class='d-flex flex-column col-sm-2'>
              <label class='form-label mb-0' for='status'>Status: <span style='color: red;'>*</span></label>";
          ?>

              <select class='form-select' name='status' id='status' required>
                <option value=''>Selecione...</option>
                <option value='1' <?=($status == '1')?'selected':''?>>Ativo</option>
                <option value='0' <?=($status == '0')?'selected':''?>>Inativo</option>
              </select>

              
            </div>
          </div>

          <?php print"

          <!-- div placa, uf, aplicação frota, 0km, categoria, tipo-->

          <!--mt-2 col-sm-12 d-flex justify-content-start-->
            <div class='mt-3 row'>

              <!-- div placa-->
              <div class='d-flex flex-column col-sm-2'>
                <label class='form-label mb-0' for='placa'>Placa: <span style='color: red;'>*</span></label>
                <input class='form-control' type='text' name='placa' id='placa'placeholder='Nº da Placa' required value='$placa'>
              </div>";
            ?>

              <!-- div uf-->
              <div class="ms-2 d-flex flex-column col-sm-1">
                <p class="mb-0">UF: <span style="color: red;">*</span></p>
                  <select class="form-select" name="uf" value="$uf" required>
                    <option value="AC" <?=($uf == 'AC')?'selected':''?> >AC</option> 
                    <option value="AL" <?=($uf == 'AL')?'selected':''?> >AL</option>
                    <option value="AP" <?=($uf == 'AP')?'selected':''?> >AP</option> 
                    <option value="AM" <?=($uf == 'AM')?'selected':''?> >AM</option>  
                    <option value="BA" <?=($uf == 'BA')?'selected':''?> >BA</option> 
                    <option value="CE" <?=($uf == 'CE')?'selected':''?> >CE</option> 
                    <option value="DF" <?=($uf == 'DF')?'selected':''?> >DF</option> 
                    <option value="ES" <?=($uf == 'ES')?'selected':''?> >ES</option> 
                    <option value="GO" <?=($uf == 'GO')?'selected':''?> >GO</option> 
                    <option value="MA" <?=($uf == 'MA')?'selected':''?> >MA</option> 
                    <option value="MT" <?=($uf == 'MT')?'selected':''?> >MT</option> 
                    <option value="MS" <?=($uf == 'MS')?'selected':''?> >MS</option> 
                    <option value="MG" <?=($uf == 'MG')?'selected':''?> >MG</option> 
                    <option value="PA" <?=($uf == 'PA')?'selected':''?> >PA</option> 
                    <option value="PB" <?=($uf == 'PB')?'selected':''?> >PB</option> 
                    <option value="PR" <?=($uf == 'PR')?'selected':''?> >PR</option> 
                    <option value="PE" <?=($uf == 'PE')?'selected':''?> >PE</option> 
                    <option value="PI" <?=($uf == 'PI')?'selected':''?> >PI</option> 
                    <option value="RJ" <?=($uf == 'RJ')?'selected':''?> >RJ</option> 
                    <option value="RN" <?=($uf == 'RN')?'selected':''?> >RN</option>
                    <option value="RS" <?=($uf == 'RS')?'selected':''?> >RS</option> 
                    <option value="RO" <?=($uf == 'RO')?'selected':''?> >RO</option> 
                    <option value="RR" <?=($uf == 'RR')?'selected':''?> >RR</option> 
                    <option value="SC" <?=($uf == 'SC')?'selected':''?> >SC</option> 
                    <option value="SP" <?=($uf == 'SP')?'selected':''?> >SP</option>
                    <option value="SE" <?=($uf == 'SE')?'selected':''?> >SE</option>  
                    <option value="TO" <?=($uf == 'TO')?'selected':''?> >TO</option> 
                  </select>
              </div>

              <!-- div aplicação frota-->
              <div class="ms-2 d-flex flex-column col-sm-3">
                <p class="mb-0"> Aplicação na frota: <span style="color: red;">*</span> </p>
                <select name="aplicacaofrota" class="mb-3 form-select" aria-label="Default select example" required>
                  <option value=''>Selecione... </option>
                  <?php 
                    $sql = "SELECT * FROM bdfrota.tbaplicacaoveic ORDER BY aplicacao";
                    $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));                   
                    
                    while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                      $idtbaplicacaoveic = $row['idtbaplicacaoveic'];
                      $aplicacaof = $row['aplicacao'];
                      $selected = ($aplicacao == $idtbaplicacaoveic) ? "selected=\"selected\"" : null;

                      print "<option value='$idtbaplicacaoveic' $selected> $aplicacaof </option>
                      ";
                    }
                   ?>
                </select>
              </div>

              <!-- div 0km -->
              <div class="ms-2 d-flex flex-column col-sm-2">
                <p class="mb-0">O veículo é 0km? <span style="color: red;">*</span> </p>
                <select name="zerokm" class="mb-0 form-select" aria-label="Default select example" required>
                  <option value=''> Selecione... </option>
                  <option value='0' <?=($zerokm == '0')?'selected':''?> > Não </option>
                  <option value='1' <?=($zerokm == '1')?'selected':''?> > Sim </option>                  
                </select>  
              </div>

              <!-- div select categoria -->
              <div class="ms-2 d-flex flex-column col-sm-2">
                <p class="mb-0"> Categoria: <span style="color: red;">*</span> </p>
                <select name="categoria" class="mb-3 form-select" aria-label="Default select example" required>
                  <option value="">Selecione...</option>
                  <option value="motocicleta" <?=($categoria == 'motocicleta')?'selected':''?> >Motocicleta</option>
                  <option value="automovel" <?=($categoria == 'automovel')?'selected':''?> >Automóvel</option>
                  <option value="microonibus" <?=($categoria == 'microonibus')?'selected':''?> >Microônibus</option>
                  <option value="onibus" <?=($categoria == 'onibus')?'selected':''?> >Ônibus</option>
                  <option value="reboque" <?=($categoria == 'reboque')?'selected':''?> >Reboque</option>
                  <option value="caminhonete" <?=($categoria == 'caminhonete')?'selected':''?> >Caminhonete</option>
                  <option value="caminhao" <?=($categoria == 'caminhao')?'selected':''?> >Caminhão</option>
                  <option value="camioneta" <?=($categoria == 'camioneta')?'selected':''?> >Camioneta</option>
                  <option value="utilitario" <?=($categoria == 'utilitario')?'selected':''?> >Utilitário</option>
                </select>
              </div>

              <!-- div select tipo -->
              <div class="ms-2 d-flex flex-column col-sm-2">
                <p class="mb-0"> Classificação:</p>
                <select name="tipoveic" class="mb-3 form-select" aria-label="Default select example">
                  <option value=''>Selecione... </option>
                  <option value="De passageiros" <?=($tipo == 'De passageiros')?'selected':''?>>De passageiros</option>
                  <option value="De carga" <?=($tipo == 'De carga')?'selected':''?>>De carga</option>
                  <option value="De carga caminhonete" <?=($tipo == 'De carga caminhonete')?'selected':''?>>De carga</option>
                  <option value="Misto" <?=($tipo == 'Misto')?'selected':''?>>Misto</option>
                  <option value="De competição" <?=($tipo == 'De competição')?'selected':''?>>De competição</option>
                  <option value="De tração" <?=($tipo == 'De tração')?'selected':''?>>De tração</option>
                  <option value="Especial" <?=($tipo == 'Especial')?'selected':''?>>Especial</option>
                  <option value="De coleção" <?=($tipo == 'De coleção')?'selected':''?>>De coleção</option>                               
                </select>  
              </div>
            </div>

            <!-- div marca,modelo, versão, cor, ano fabricação, ano modelo-->
            <!--col-sm-12 d-flex justify-content-start align-items-end-->
            <div class="row">
              <!-- div marca-->
              <div class="col-sm-2 d-flex justify-content-start align-items-end">
                <!--div select-->
                <div class="d-flex flex-column" style="width: 80%;">
                  <p class="mb-0"> Marca: <span style="color: red;">*</span> </p>
                  <select name="marca" class="mb-3 form-select" id='marca' required>
                    <option value="">TODAS</option>
                    <?php
                      $i = 0;

                      //$sql = "SELECT * FROM bdfrota.tbmarcaveic WHERE idtbmarcaveic<>'16'  ORDER BY marca";
                      $sql="SELECT * FROM bdfrota.tbmarcaveic where idtbmarcaveic not in (16,35,18,34,39,1059,30,32,1057,29,22,27,25,28,20,24,17,33,31) AND idtbmarcaveic not between 40 and 1055 AND marca<>'' ORDER BY marca";
                      $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

                      $sql1 = "SELECT marca FROM bdfrota.tbveiculo where idtbveiculo = '$idtbveiculo'";
                      $resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));

                       while($row1 = mysqli_fetch_array($resultado1, MYSQLI_ASSOC)){
                        $marca1 = $row1['marca'];//retorna o tipo

                        //print "<option> $marca </option>";
                       }
                      
                      while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                        $idtbmarcaveic = $row['idtbmarcaveic'];
                        $marca = $row['marca'];
                        $selected = ($marca1 == $idtbmarcaveic) ? "selected=\"selected\"" : null;

                        $addmatriz = [$idtbmarcaveic => $i];
                        foreach($addmatriz as $key => $value)
                        {
                          $idselect[$key] = $value;
                        };
                        $i++;

                        print "<option id='$idselect[$idtbmarcaveic]' value='$idtbmarcaveic' $selected> $marca </option>
                        ";
                      }
                     ?>
                  </select>
                </div>
              
                <!--div botão-->
                <div class="mb-3 ms-1">
                  <a title="Adicionar nova marca" href="./cadastromarcaveic.php" target='_blank'> <span class="material-symbols-outlined">add</span> </a>
                </div>
              </div>

              <!-- div modelo-->
              <div class="ms-2 col-sm-2 d-flex justify-content-start align-items-end">
                <!--div select-->
                <div class="d-flex flex-column" style="width: 80%;">
                  <p class="mb-0"> Modelo: <span style="color: red;">*</span> </p>
                  <select name="modelo" class="mb-3 form-select" id='modelo' required>
                    <option value="">TODOS</option> 
                    <?php
                      $i = 0;
                      $idselect;

                      $sql = "SELECT * FROM bdfrota.tbmodeloveic WHERE idtbmodeloveic not in (135,1476,1442,1441,1440,1475,158,1449) and idtbmodeloveic not between 159 AND 206 AND idtbmodeloveic not between 209 AND 1438 ORDER BY modelo";
                      $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

                      $sql1 = "SELECT modelo FROM bdfrota.tbveiculo where idtbveiculo = '$idtbveiculo'";
                      $resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));

                       while($row1 = mysqli_fetch_array($resultado1, MYSQLI_ASSOC)){
                        $modelo1 = $row1['modelo'];//retorna o tipo

                        //print "<option> $marca </option>";
                       }
                      
                      while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                        $idtbmodeloveic = $row['idtbmodeloveic'];
                        $modelo = $row['modelo'];
                        $marca = $row['marca'];
                        $selected = ($modelo1 == $idtbmodeloveic) ? "selected=\"selected\"" : null;

                        $addmatriz = array($modelo => $i );
                        foreach($addmatriz as $key => $value)
                        {
                          $idselect[$key] = $value;
                        }
                        $i++;

                        print "<option id='$idselect[$marca]-$idselect[$modelo]' value='$idtbmodeloveic' $selected> $modelo </option>
                        ";
                      }
                     ?>
                  </select>
                </div>

                <!--div botão-->
                <div class="mb-3 ms-1">
                  <a title="Adicionar nova modelo" href="./cadastromodeloveic.php" target='_blank'> <span class="material-symbols-outlined">add</span> </a>
                </div>
              </div>

              <?php
              print "
              <!-- div versão-->
              <div class='ms-2 mb-3 d-flex flex-column col-sm-2'>
                <label class='form-label mb-0' for='versao'>Versão:</label>
                <input class='form-control' type='text' name='versao' id='versao' placeholder='Versão do Veículo' value='$versao'>
              </div>

              <!-- div cor-->
              <div class='ms-2 mb-3 col-sm-2 d-flex flex-column'>
                <label class='form-label mb-0' for='cor'>Cor: <span style='color: red;'>*</span> </label>
                <input class='form-control' type='text' name='cor' id='cor' placeholder='Cor do Veículo' value='$cor' required>
              </div>
              <!-- div ano fabricação-->
              <div class='ms-2 mb-3 col-sm-2 d-flex flex-column'>
                <label class='form-label mb-0' for='anofabric'>Ano de fabricação: <span style='color: red;'>*</span> </label>
                <input class='form-control' type='text' name='anofabric' id='anofabric' placeholder='Ex.: 1990' value='$anofabr' required>
              </div>

              <!-- div ano do modelo-->
              <div class='ms-2 mb-3 col-sm-2 d-flex flex-column'>
                <label class='form-label mb-0' for='anomodelo'>Ano do modelo: </label>
                <input class='form-control' type='text' name='anomodelo' id='anomodelo' placeholder='Ex.: 1990' value='$anomodelo'>
              </div>
            </div>"; ?>

            <!-- div Status Veículo,Tipo Veículo, Situação, Hodômetro Inicial (em Total de Km): *-->
            <!--col-md-12 d-flex justify-content-start-->
            <div class="row">
              <!-- div Status Veículo-->
              <div class="d-flex flex-column col-sm-2">
                <!--div select-->
                <div class="d-flex flex-column">
                  <p class="mb-0"> Status do Veículo: <span style="color: red;">*</span> </p>
                  <select name="statusvel" class="mb-3 form-select" id='statusvel' required>
                    <option value=" ">Selecione...</option>
                    <?php
                      $sql = "SELECT * FROM bdfrota.tbastatusvel WHERE idtbastatusvel NOT IN ('18','7','13','19','39') ORDER BY status";
                      $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));                  
                      
                      while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                        $statusvel1 = $row['status'];
                        $idtbastatusvel = $row['idtbastatusvel'];
                        $statusgeral = $row['statusgeral'];

                        if($statusgeral == '0'){
                          $statusgeral = 'inativo';
                        }elseif($statusgeral == '1'){
                          $statusgeral = 'ativo';
                        }else{
                          $statusgeral = 'semreg';
                        }

                        $selected = ($idtbastatusvel == $statusvel) ? "selected=\"selected\"" : null;
                        print "<option class='$statusgeral' value='$idtbastatusvel' $selected> $statusvel1 </option>
                        ";
                      }
                     ?>
                  </select>
                </div>
              
              </div>

<?php


             if ($bo <> '') {
                print"<div class='ms-2 d-flex flex-column col-sm-1'>
                <!--div select-->
                <p class='mb-0'> Documento: </p>
                <a href='./$bo' target='blank_' class='btn btn-secundary'><span class='material-symbols-outlined'>
description
</span></a>
              
              </div>";
              } else {
                print"<div class='ms-2 d-flex flex-column col-sm-3'>
                <!--div select-->
                <div class='d-flex flex-column'>
                  
                  <label for='formFileSm1' class='mb-0'>Documento:</label>
      <input class='mb-3 form-control' id='formFileSm1' type='file' name='doc01'>
                </div>
              
              </div>";
              }

              ?>

              <!-- div modelo-->
              <div class="ms-2 d-flex flex-column col-sm-2">
                <!--div select-->
                  <div class="d-flex flex-column">
                  <p class="mb-0"> Tipo: <span style="color: red;">*</span> </p>
                  <select name="tipovel" class="mb-3 form-select" id='tipovel' required>
                    <option value=" ">Selecione...</option>
                    <?php
                      $sql = "SELECT * FROM bdfrota.tbatipovel ORDER BY tipo";
                      $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
                      
                      while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                        $tipoveic = $row['tipo'];
                        $idtbatipovel = $row['idtbatipovel'];

                        $selected = ($tipovel == $idtbatipovel) ? "selected=\"selected\"" : null;

                        print "<option value='$idtbatipovel' $selected> $tipoveic </option>
                        ";
                      }
                     ?>
                  </select>
                </div>

              </div>


              <div class="ms-2 d-flex flex-column col-sm-2">
              <!--div select-->
                <div class="d-flex flex-column">
                <p class="mb-0"> Situação: <span style="color: red;">*</span> </p>
                <select name="situacao" class="mb-3 form-select" id='situacao' required>
                  <option value=" ">Selecione...</option>
                  <?php
                    $sql = "SELECT DISTINCT(situacao) FROM bdfrota.tbveiculo ORDER BY situacao";
                    $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

                    $sql1 = "SELECT situacao FROM bdfrota.tbveiculo where idtbveiculo = '$idtbveiculo'";
                    $resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));

                     $row1 = mysqli_fetch_array($resultado1, MYSQLI_ASSOC);
                      $situacao1 = $row1['situacao'];
                    
                    while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                      $situacao = $row['situacao'];
                      $selected = ($situacao1 == $situacao) ? "selected=\"selected\"" : null;

                      print "<option value='$situacao' $selected> $situacao </option>
                      ";
                    }
                   ?>
                </select>
              </div>

            </div>

            <?php
            print "
            <!-- div hodometro -->
            <div class='ms-2 d-flex col-sm-3 flex-column'>
              <label class='form-label mb-0' for='hodometroinicial'>Hodômetro Inicial(em Total de Km):</label>
              <input class='form-control' type='text' name='hodometroinicial' id='hodometroinicial' onkeypress='return onlynumber();' placeholder='Hodômetro em Total de Km' value='$hodometroinicial'>
            </div>
          </div>"; ?>

<!-- div marca,modelo, versão, cor, ano fabricação, ano modelo-->
<!--col-md-12 d-flex justify-content-start-->
            <div class="row">            

              <!-- div data -->
              <div class="col-sm-3 d-flex flex-column justify-content-start">
                <label class="mb-0" for='datainfracao'>Data Ult. Movimentação: </label> 
                <input type="date" class="form-control" name="datamovimentacao" id='datamovimentacao' value="<?php echo $datamovimentacao; ?>" />
              </div>

              <!-- div hora -->
              <div class="ms-1 col-sm-2 d-flex flex-column justify-content-start"> 
                <label class="mb-0" for='horainfracao'>Hora Ult. Mov:  </label> 
                <input type="time" class="form-control" name="horamovimentacao" id='horamovimentacao' value="<?php echo $horamovimentacao; ?>" />
              </div>
              <?php
              print "
              <!-- div matricula condutor (matcond) -->
              <div class='ms-2 d-flex col-sm-3 flex-column'>
                <label class='form-label mb-0' for='matcond'>Matrícula do condutor: </label>
                <input class='form-control' type='text' name='matcond' id='matcond' onkeypress='return onlynumber();' placeholder='Matrícula do condutor' value='$matcond' >
              </div>"; ?>

              <!-- div data -->
              <div class="ms-2 col-sm-2 d-flex flex-column justify-content-start">
                <label class="mb-0" for='datainfracao'>Data Entrega: </label> 
                <input type="date" class="form-control" name="dtentrega" id='dtentrega' value="<?php echo $dtentrega; ?>" />
              </div>

              <!-- div data -->
              <div class="ms-2 col-sm-2 d-flex flex-column justify-content-start">
                <label class="mb-0" for='datainfracao'>Data Devolução:  </label> 
                <input type="date" class="form-control" name="dtdevolucao" id='dtdevolucao' value="<?php echo $dtdevolucao; ?>"  />
              </div>

              
            </div>
<?php
            print"<!-- div velocidade máxima, tipo de combustivel, tanque, motorização, calibragem, aro -->
            <!--col-md-12 d-flex justify-content-start mt-4-->
            <div class='mt-3 row'>
              <!-- div velocidade máxima-->
              <div class='col-sm-3 d-flex flex-column'>
                <label class='form-label mb-0' for='velmax'>Velocidade máxima (em km/h): </label>
                <input class='form-control' type='text' name='velmax' id='velmax' onkeypress='return onlynumber();' placeholder='Velocidade máxima' value='$velocmax'>
              </div>";
              ?>

              <!-- div select tipo de combustível -->
              <div class="ms-2 d-flex flex-column col-sm-2">
                  <p class="mb-0"> Tipo de combustível: <span style="color: red;">*</span> </p>
                  <select name="combustivel" class="form-select mb-3" aria-label="Default select example" required> 
                    <option value=''> Selecione... </option>
                    <option value='ALCOOL' <?=($combustivel == 'ALCOOL')?'selected':''?> > Álcool </option>
                    <option value='GASOLINA' <?=($combustivel == 'GASOLINA')?'selected':''?> > Gasolina </option>
                    <option value='FLEX' <?=($combustivel == 'FLEX')?'selected':''?> > Flex </option>
                    <option value='DIESEL' <?=($combustivel == 'DIESEL')?'selected':''?> > Diesel </option>
                    <option value='NAO SE APLICA' <?=($combustivel == 'NAO SE APLICA')?'selected':''?>> Não se aplica </option>

                  </select>
              </div>

              <?php

              print"
              <!-- lts tanque -->
              <div class='ms-2 d-flex col-sm-2 flex-column'>
                <label class='form-label mb-0' for='tanque'>Capac tanque(em L):</label>
                <input class='form-control' type='text' name='tanque' id='tanque' placeholder='Capacidade em Litros' onkeypress='return onlynumber();' value='$tanque'>
              </div>
              ";  
              ?>

              <!-- motorização -->
              <div class="ms-2 d-flex flex-column col-sm-2">
                <p class="mb-0">Motorização:</p>
                <select name="motorizacao" class="form-select" aria-label="Default select example">
                  <option value=''> Selecione... </option>
                  <option value='1.0' <?=($motorizacao == '1.0')?'selected':''?> > 1.0 </option>
                  <option value='1.4' <?=($motorizacao == '1.4')?'selected':''?> > 1.4 </option>
                  <option value='1.6' <?=($motorizacao == '1.6')?'selected':''?> > 1.6 </option>
                  <option value='1.8' <?=($motorizacao == '1.8')?'selected':''?> > 1.8 </option>
                  <option value='2.0' <?=($motorizacao == '2.0')?'selected':''?> > 2.0 </option>
                </select>
              </div>

              <?php
                print "
              <!-- div calibragem -->
              <div class='ms-2 d-flex col-sm-2 flex-column'>
                <label class='form-label mb-0' for='calibragem'>Calibragem (em PSI):</label>
                <input class='form-control' type='text' name='calibragem' id='calibragem' onkeypress='return onlynumber();' placeholder='Calibragem (em PSI)' value='$calibragem'>
              </div>

               <!-- div aro-->
              <div class='ms-2 d-flex col-sm-1 flex-column'>
                <label class='form-label mb-0' for='aro'>Aro:</label>
                <input class='form-control' type='text' name='aro' id='aro' onkeypress='return onlynumber();' placeholder='Aro' value='$aro'>
              </div>
            </div>
            <!-- div renavam, numero do chassi, num motor, nº de portas, nº de passageiros, nº de pneus -->
            <!--mb-3 col-sm-12 d-flex justify-content-start-->
            <div class='row'>
              <!--div renavam-->
              <div class='d-flex col-sm-2 flex-column'>
                <label class='form-label mb-0'  for='renavam'>RENAVAM:</label>
                <input class='form-control' type='renavam' name='renavam' id='renavam' placeholder='RENAVAM' value='$renavam'>
              </div>

              <!--div num chassi -->
              <div class='ms-2 d-flex col-sm-2 flex-column'>
                <label class='form-label mb-0'  for='chassi'>Número do Chassi:</label>
                <input class='form-control' type='text' name='chassi' id='chassi' onKeyPress='if(this.value.length==17) return false;' value='$chassi' placeholder='Nº do Chassi'>
              </div>

              <!--div num motor -->
              <div class='ms-2 d-flex col-sm-2 flex-column'>
                <label class='form-label mb-0'  for='nummotor'>Número do Motor:</label>
                <input class='form-control' type='text' name='nummotor' id='nummotor' placeholder='Nº do motor' value='$nummotor'>
              </div>

              <!-- div num portas-->
              <div class='ms-2 d-flex col-sm-2 flex-column'>
                <label class='form-label mb-0' for='nportas'>Número de Portas: </label>
                <input class='form-control' type='text' name='nportas' id='nportas' onkeypress='return onlynumber();' placeholder='Nº de portas' value='$nportas'>
              </div>

              <!-- div num passsageiros-->
              <div class='ms-2 d-flex col-sm-2 flex-column'>
                <label class='form-label mb-0' for='npassageiros'>Nº de Passageiros: </label>
                <input class='form-control' type='text' name='npassageiros' id='npassageiros' onkeypress='return onlynumber();'placeholder='Nº de passageiros' value='$npassageiros'>
              </div>

              <!-- div num qtd pneus-->
              <div class='ms-2 d-flex col-sm-2 flex-column'>
                <label class='form-label mb-0' for='qtdpneus'>Quantidade de Pneus: </label>
                <input class='form-control' type='text' name='qtdpneus' id='qtdpneus' onkeypress='return onlynumber();' placeholder='Quantidade de Pneus' value='$qtdpneus'>
              </div>
            </div>

            <!-- div qtd estepes, qtd eixos, gnv, gps, tag pedágio -->
            <!--col-md-12 d-flex justify-content-start-->
            <div class='mt-3 row'>
              <!-- div qtd estepes -->
              <div class='d-flex col-sm-2 flex-column'>
                <label class='form-label mb-0' for='qtdestepes'>Quant. de estepes:</label>
                <input class='form-control' type='text' name='qtdestepes' id='qtdestepes' onkeypress='return onlynumber();' placeholder='Quantidade de estepes' value= '$qtdestepe'>
              </div>

              <!-- div qtd eixos -->
              <div class='ms-2 d-flex col-sm-2 flex-column'>
                <label class='form-label mb-0' for='qtdeixos'>Quantidade de eixos:</label>
                <input class='form-control' type='text' name='qtdeixos' id='qtdeixos' onkeypress='return onlynumber();' placeholder='Quantidade de eixos' value='$qtdeixos'>
              </div>";
              ?>

              <!-- div gnv -->
              <div class="ms-2 d-flex flex-column col-sm-2">
                <p class="mb-0">O veículo é GNV? <span style="color: red;">*</span></p>
                <select name="gnv" class="mb-3 form-select" aria-label="Default select example" required>
                  <option value=''> Selecione... </option>
                  <option value='0' <?=($gnv == '0')?'selected':''?> > Não </option>
                  <option value='1' <?=($gnv == '1')?'selected':''?> > Sim </option>                  
                </select>  
              </div>

              <!-- div gps -->
              <div class="ms-2 d-flex flex-column col-sm-2">
                <p class="mb-0">O veículo possui GPS? <span style="color: red;">*</span></p>
                <select name="gps" class="mb-3 form-select" aria-label="Default select example" required>
                  <option value=''> Selecione... </option>
                  <option value='0' <?=($gps == '0')?'selected':''?> > Não </option>
                  <option value='1' <?=($gps == '1')?'selected':''?> > Sim </option>                  
                </select>  
              </div>

              <!-- div tag pedágio -->
              <div class="ms-2 d-flex flex-column col-sm-3">
                <p class="mb-0">O veículo possui Tag Pedágio?</p>
                <select name="tagpedagio" class="mb-3 form-select" aria-label="Default select example">
                  <option value=''> Selecione... </option>
                  <option value='0' <?=($tagpedagio == '0')?'selected':''?> > Não </option>
                  <option value='1' <?=($tagpedagio == '1')?'selected':''?> > Sim </option>                  
                </select>  
              </div> 
            </div>

            <!--col-md-12 d-flex justify-content-start-->
            <div class="row">
              <!-- div marca-->  
              <div class="d-flex flex-column col-sm-2">
                <!--div select-->
                <div class="d-flex flex-column">
                  <p class="mb-0"> GPS Empresa: <span style="color: red;">*</span> </p>
                  <select name="gpsemp" class="mb-3 form-select" id='gpsemp' required>
                    <option value=''> Selecione... </option>
                    <?php
                      $sql = "SELECT fantasia FROM bdfrota.tbfornecedor where tipo = 5";
                      $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));    
                      while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                        $gpsemp1 = $row['fantasia'];
                        $selected = ($gpsemp1 == $gpsemp) ? "selected=\"selected\"" : null;
                        print "<option value='$gpsemp1' $selected> $gpsemp1 </option>
                        ";
                      }
                     ?>
                     <option value='nao possui' <?=($gpsemp == 'nao possui')?'selected':''?>>Não possui</option>
                  </select>
                </div>
              
              </div>
            
              <?php
              print"<!-- div qtd doccrlv -->
              <div class='ms-2 d-flex col-sm-2 flex-column'>
                <label class='form-label mb-0' for='doccrlv'>Doc. CRLV:</label>
                <input class='form-control' type='text' name='doccrlv' id='doccrlv' placeholder='Doc. CRLV' value='$doccrlv'>
              </div>";
              ?>

              <!-- div gnv -->
              <div class="ms-2 d-flex flex-column col-sm-3">
                <p class="mb-0">O veículo possui Airbag?</p>
                <select name="airbag" class="mb-3 form-select" aria-label="Default select example">
                  <option value=''> Selecione... </option>
                  <option value='0' <?=($airbag == '0')?'selected':''?> > Não </option>
                  <option value='1' <?=($airbag == '1')?'selected':''?> > Sim </option>                  
                </select>  
              </div>

              <!-- div rack -->
              <div class="ms-2 d-flex flex-column col-sm-3">
                <p class="mb-0">O veículo possui rack?</p>
                <select name="rack" class="mb-3 form-select" aria-label="Default select example">
                  <option value=''> Selecione... </option>
                  <option value='0' <?=($rack == '0')?'selected':''?> > Não </option>
                  <option value='1' <?=($rack == '1')?'selected':''?> > Sim </option>                  
                </select>  
              </div>

              <!-- div tag pedágio -->
              <div class="ms-2 d-flex flex-column col-sm-2">
                <p class="mb-0">Tipo de Blindagem:</p>
                <select name="blindagem" class="mb-3 form-select" aria-label="Default select example">
                  <option value=''> Selecione... </option>
                    <?php
                      $sql = "SELECT DISTINCT(blindagem) FROM bdfrota.tbveiculo";
                      $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
                      while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                        $blindagem1 = $row['blindagem'];
                        $selected = ($blindagem1 == $blindagem) ? "selected=\"selected\"" : null;
                        print "<option value='$blindagem1' $selected> $blindagem1 </option>";
                      }
                     ?>                 
                </select>  
              </div> 
            </div>
          
          <!--col-md-12 d-flex justify-content-start-->
            <div class="row">
              <!-- div marca-->
              <?php
              print"<!-- div qtd oficina -->
              <div class='d-flex col-sm-4 flex-column'>
                <label class='form-label mb-0' for='oficina'>Oficina:</label>
                <input class='form-control' type='text' name='oficina' id='oficina' placeholder='Oficina' value='$oficina'>
              </div>";
              ?>
            
<?php
              print"<!-- div qtd ncontloc -->
              <div class='ms-2 d-flex col-sm-2 flex-column'>
                <label class='form-label mb-0' for='ncontloc'>N° Contrato Locação:</label>
                <input class='form-control' type='text' name='ncontloc' id='ncontloc' placeholder='Número Contrato Locação' value='$ncontloc'>
              </div>";
              ?>

              <div class="ms-2 col-sm-2 d-flex flex-column justify-content-start">
                <label class="mb-0" for='dtdisponivelloc'>Data Disp. Locadora:  </label> 
                <input type="date" class="form-control" name="dtdisponivelloc" id='dtdisponivelloc' value="<?php echo $dtdisponivelloc; ?>"  />
              </div>

              <div class="ms-2 col-sm-2 d-flex flex-column justify-content-start">
                <label class="mb-0" for='dtdevolucaoloc'>Data Dev. Locadora:  </label> 
                <input type="date" class="form-control" name="dtdevolucaoloc" id='dtdevolucaoloc' value="<?php echo $dtdevolucaoloc; ?>" />
              </div>
            <?php print"
              <!-- div valaquisicao -->
              <div class='ms-2 d-flex col-sm-2 flex-column'>
                <label class='form-label mb-0' for='ncontloc'>Valor Aquisição:</label>
                <input class='form-control valor' type='text' name='valaquisicao' id='valaquisicao' placeholder='R$ 0,00' value='$valaquisicao'>
              </div>";?>
            </div>
            
          <!--mt-3 col-md-12 d-flex justify-content-start-->
            <div class="mt-3 row">
            <?php  
              print "
              <!-- div hodometro -->
              <div class='d-flex col-sm-3 flex-column'>
                <label class='form-label mb-0' for='hodometro'>Hodômetro (em Total de Km):</label>
                <input class='form-control' type='text' name='hodometro' id='hodometro' onkeypress='return onlynumber();' placeholder='Hodômetro em Total de Km' value='$hodometro'>
              </div>";
            ?>

              <!-- div tag propriedade -->
              <div class='ms-2 d-flex flex-column col-sm-3'>
                <p class='mb-0'>Tipo de posse do veículo: <span style='color: red;'>*</span></p>
                <select name='tipoposse' id='tipoposse' class='mb-3 form-select' aria-label='Default select example' required onchange='verifica(this.value)'>
                  <option value='0'> Selecione uma opção </option>
                  <option value='AGREGADO' <?=($tipoposse == 'AGREGADO')?'selected':''?>> Agregado </option>
                  <option value='LOCADO' <?=($tipoposse == 'LOCADO')?'selected':''?>> Locado </option>
                  <option value='PRÓPRIO' <?=($tipoposse == 'PRÓPRIO')?'selected':''?>> Próprio </option>
                  <option value='TERCEIRO' <?=($tipoposse == 'TERCEIRO')?'selected':''?>> Terceiro </option>
                  <option value='PARTICULAR' <?=($tipoposse == 'PARTICULAR')?'selected':''?>> Particular </option>                                    
                </select>  
              </div>

              <!-- div locador -->
              <div class='ms-2 d-flex col-sm-3 flex-column'>
                <label class='form-label mb-0' for='locador'>Locador (se o veículo for locado): </label>
                <!--<input class='form-control' type='text' name='locador' id='locador' placeholder='Locador' disabled value='$locador'>-->
                <?php print"
                <select name='locador' id='locador' class='mb-3 form-select' $disable>
                  <option value=' '>Selecione...</option>";

                    $sql = "SELECT idtbfornecedor, fantasia FROM bdfrota.tbfornecedor WHERE tipo='4' AND status='1'";
                    $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
                    
                    while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                      $idtbfornecedor = $row['idtbfornecedor'];
                      $fantasia = $row['fantasia'];
                      $selected = ($idlocador == $idtbfornecedor) ? "selected=\"selected\"" : null;

                      print "<option value='$idtbfornecedor' $selected> $fantasia </option>
                      ";
                    }
                ?>
                </select>
              </div>

              <!-- div bases ffa -->
              <div class="ms-2  col-sm-3 flex-column" id='baseffa2'>
                <label class="form-label mb-0" for="baseffa">CNPJ Base FFA (veículo próprio): </label>
                <select name="baseffa" id='baseffa'  class="mb-3 form-select"disabled>
                  <option value=" ">Selecione... </option>
                  <?php 
                    $sql = "SELECT * FROM bdaniel.tbfilial";
                    $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
                    
                    while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                      $cnpj = $row['cnpj'];
                      $nome = $row['nome'];

                      print "<option value='$cnpj'> $cnpj </option>
                      ";
                    }
                   ?>
                </select>
              </div>            
            </div>

            <!-- div base gestão e centro de custo -->
            <div class="mt-3 col-md-12 d-flex justify-content-start">
              <!--<div class="col-sm-4">                
                <label for="basegestao">Base Gestão:</label>
                <input class='form-control' type='text' name='basegestao' id='basegestao' placeholder='Base Gestão' <?php print "value = '$basegestao' " ?>>
              </div>-->

              <div class='col-sm-4'>
                <label for='basegestao'> Base gestão: </label>
                <select name='basegestao' id='basegestao' class="mb-3 form-select" >
                  <option value=''> Selecione a opção </option>
                <?php
                  //$sql3="SELECT * FROM bdaniel.tbfilial";
                  $sql="SELECT DISTINCT (basegestao) FROM bdfrota.tbveiculo ORDER BY basegestao;";
                  $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($con));
                    
                  while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                    $basegestao = $row['basegestao'];
                    $selected = ($basegestaof == $basegestao) ? "selected=\"selected\"" : null;
                    if($basegestao!=''){
                      print "<option value='$basegestao' $selected> $basegestao </option>";
                    }                    
                  }

                ?>
                </select>                
              </div>

              <div class="ms-2 col-sm-4">                
                <label for="centrocusto">Centro de Custo:</label>
                <select name="centrocusto" id='centrocusto'  class="mb-3 form-select" required>
                  <option value="">Selecione... </option>
                  <?php 
                    $sql1 = "SELECT descricao AS ccusto FROM BdPonto.tbccusto ORDER BY descricao";
                    $resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));
                    
                    while($row1 = mysqli_fetch_array($resultado1, MYSQLI_ASSOC)){
                      $ccusto1 = $row1['ccusto'];
                      $selected = ($ccusto == $ccusto1) ? "selected=\"selected\"" : null;                     
                      
                      if($ccusto1 !=''){
                        print "<option value='$ccusto1' $selected> $ccusto1 </option>";
                      }
                      
                    }
                   ?>
                </select>
              </div>
            </div>

            <!-- div Data termo de disponibilização e de desmobilização -->
            <div class="mt-1 col-md-12 d-flex justify-content-start">
              <div class="col-sm-4">                
                <label for="dttermo">Data termo de disponibilização:</label>
                <input class='form-control' type='date' name='dttermo' id='dttermo' <?php print"value = '$dttermo1'"; ?>>
              </div>

              <div class="ms-2 col-sm-4">                
                <label for="dtdisp">Data de desmobilização:</label>
                <input class='form-control' type='date' name='dtdisp' id='dtdisp' <?php print"value = '$dtdisp1'"; ?>>
                
              </div>
            </div>

            <!--div observação-->
            <div class="col-md-12 d-flex justify-content-start">
              <div class="mt-3 col-sm-12 form-floating mb-3">
                <?php print"
                <textarea class='form-control' name='obsveiculo' id='obsveiculo' type='text' placeholder='Observação' rows='4' cols='50' style='height: 200px !important;'>$obsveiculo</textarea>";?>
                <label for="obsveiculo">Observações:</label>
              </div>
            </div>

            <!-- div texto campos obrigatório -->
            <div style="color: red;" class="pt-2">
              * Campos obrigatórios.
            </div>

            <!-- div botão -->
            <div class="mt-3 pb-2 d-flex col-sm-12 justify-content-center">
              <div>
                <button class="btn btn-success" type='submit'> Confirmar edição </button>
              </div>

              <div class="ms-5">
                <a class="btn btn-danger" href='./listagemveiculos.php'> Cancelar edição </a>
              </div>
          </form>
        </div>
      </div>
    </main>

    <footer class="mt-4 py-4 bg-light mt-auto" style="bottom: 0; start: 0; width: 100%;">
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
  <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>-->
  <!--<script src="assets/demo/chart-area-demo.js"></script>
  <script src="assets/demo/chart-bar-demo.js"></script>
  <script src="../src/js/simple-datatable.js" crossorigin="anonymous"></script>
  <script src="../src/js/datatables-simple-demo.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>   
        
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>-->

  <script>
    $("#marca").change(function() {
      var marca = document.getElementById('marca');
      var idmarca = marca[marca.selectedIndex].id;
      var idmarca1 = idmarca.substring(0,2);
      var modelo = document.getElementById('modelo');
      
      for (var i = 0; i < modelo.length; i++) {
        var idmodelo = modelo.options[i].id;
        
        var idmodelo1 = idmodelo.split("-");
        var idmodelo1 = idmodelo1[0];

        if (idmodelo1 != idmarca1) {
          $(modelo.options[i]).attr('disabled', 'disabled').hide();
        } else {
          $(modelo.options[i]).removeAttr('disabled').show();
        }
      }
        /*console.log(idmodelo1); !idmodelo1.startsWith(idmarca1)
        console.log(idmarca);*/
      
    });

    //mascaras de input
    $(document).ready(function(){
      $('#anofabric').mask('0000');
      $('#anomodelo').mask('0000');
      $('#renavam').mask('00000000000');
      //$('#nummotor').mask('000000');

      $("#nummotor").on("input", function(){
        $(this).val($(this).val().toUpperCase());
      });

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

      $('#tipoposse').change(function() {
        if ($(this).val() == 'LOCADO') {
          $('#locador').attr('disabled', false);
        } else {
          $('#locador').attr('disabled', true);
        }

        if ($(this).val() == 'PRÓPRIO') {
          $('#baseffa').attr('disabled', false);
        } else {
          $('#baseffa').attr('disabled', true);
        }       
      });

    });

    //função para forçar a entrada de somente números
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
    };

    if($("#status").val() == '0'){
      $(".inativo").show();
      $(".ativo").hide();
      $(".semreg").hide();

    } else if($("#status").val() == '1'){
      $(".ativo").show();
      $(".inativo").hide();
      $(".semreg").hide();
    }

    $('#status').change(function() {        
      if($("#status").val() == '0'){
        $(".inativo").show();
        $(".ativo").hide();
        $(".semreg").hide();

      } else if($("#status").val() == '1'){
        $(".ativo").show();
        $(".inativo").hide();
        $(".semreg").hide();
      }       
    });

    //função para habilitar ou desabilitar input
    /*function verifica(value){
      var input = document.getElementById("locador");

      if(value == 'LOCADO'){
        input.disabled = false;
      }else {
        input.disabled = true;
      }

      //console.log(value);
    };*/

    /*function verificaPlaca() {
      var regex = '[A-Z]{3}[0-9][0-9A-Z][0-9]{2}';
      var placa = window.getElementById('placa');
      
      if (!placa.match(regex)) {
        return false;
      }
    }*/
    

    /*function teste() {
      return onlynumber(); 

      if(this.value.length==4) return false;
    }*/
  </script>

</body>
</html>
<!-- 11/02 -->