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
$tipo= $_SESSION['tipo'];

if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
} elseif ($perfil == '0' || $perfil == '1' || $perfil == '2'|| $perfil == '3'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}elseif ($matricula1 == '160030'||$matricula1 == '410109'||$matricula1 == '500422'||$matricula1 == '501285'||$matricula1 == '410039'||$matricula1 == '501640'||$matricula1 == '500459' || $matricula1=='411425' || $matricula1 == '003931'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula1;
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo']= $tipo;

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

  <title> Cadastro de Veículo </title>

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
        <h1 class="h1 pt-2 pb-2 text-center"> Cadastro de Veículo </h1>
        
        <p class="text-center mb-3" style="font-size: 14px; color: #666;">
          <strong>Cadastrando como:</strong> <?php echo $nome1; ?> (Matrícula: <?php echo $matricula1; ?>)
        </p>

        <!-- div do formulario -->
        <div>
          <form method="post" action="./control/cadastrarveiculo.php" enctype="multipart/form-data" style="width: 90%;" class="pb-5 mb-5 m-auto">
            <?php print"<input type='hidden' name='matr_autor' value='$matricula1'>"; ?>

            <div class='row'>
            <!--div status-->
            <div class='d-flex flex-column col-sm-2'>
              <label class='form-label mb-0' for='status'>Status: <span style='color: red;'>*</span></label>

              <select class='form-select' name='status' id='status' required>
                <option value=''>Selecione...</option>
                <option value='1'>Ativo</option>
                <option value='0'>Inativo</option>
              </select>

              
            </div>
          </div>
            <!-- div placa, uf, aplicação frota, 0km, categoria, tipo-->
            <!--mt-2 col-sm-12 d-flex justify-content-start-->
            <div class="mt-3 row">

              <!-- div placa-->
              <div class="d-flex flex-column col-sm-2">
                <label class="form-label mb-0" for="placa">Placa: <span style="color: red;">*</span></label>
                <input class="form-control" type="text" name="placa" id="placa" placeholder="Nº da Placa" required>
              </div>

              <!-- div uf-->
              <div class="ms-2 d-flex flex-column col-sm-1">
                <p class="mb-0">UF: <span style="color: red;">*</span></p>
                  <select class="form-select" name="uf" required>
                    <option value="AC">AC</option> 
                    <option value="AL">AL</option>
                    <option value="AP">AP</option> 
                    <option value="AM">AM</option>  
                    <option value="BA">BA</option> 
                    <option value="CE">CE</option> 
                    <option value="DF">DF</option> 
                    <option value="ES">ES</option> 
                    <option value="GO">GO</option> 
                    <option value="MA">MA</option> 
                    <option value="MT">MT</option> 
                    <option value="MS">MS</option> 
                    <option value="MG">MG</option> 
                    <option value="PA">PA</option> 
                    <option value="PB">PB</option> 
                    <option value="PR">PR</option> 
                    <option value="PE">PE</option> 
                    <option value="PI">PI</option> 
                    <option value="RJ">RJ</option> 
                    <option value="RN">RN</option>
                    <option value="RS">RS</option> 
                    <option value="RO">RO</option> 
                    <option value="RR">RR</option> 
                    <option value="SC">SC</option> 
                    <option value="SP">SP</option>
                    <option value="SE">SE</option>  
                    <option value="TO">TO</option> 
                  </select>
              </div>

              <!-- div centro de custo-->
              <!--<div class="ms-2 d-flex flex-column col-sm-3">
                <p class="mb-0"> Centro de custo: <span style="color: red;">*</span> </p>
                <select name="ccusto" class="mb-3 form-select" aria-label="Default select example" required> 
                  <?php 
                    /*$sql = "SELECT DISTINCT(ccusto) FROM bdaniel.tbfuncionario WHERE ccusto<>'' AND status = 'Ativo' ORDER BY ccusto;";
                    $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
                    
                    while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                      $ccusto = utf8_encode($row['ccusto']);

                      print "<option value='$ccusto'> $ccusto </option>
                      ";
                    }*/
                   ?>
                </select>
              </div>-->

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
                      $aplicacao = utf8_encode($row['aplicacao']);

                      print "<option value='$idtbaplicacaoveic'> $aplicacao </option>";
                    }
                  ?>
                </select>
              </div>

              <!-- div 0km -->
              <div class="ms-2 d-flex flex-column col-sm-2">
                <p class="mb-0">O veículo é 0km? <span style="color: red;">*</span> </p>
                <select name="zerokm" class="mb-0 form-select" aria-label="Default select example" required>
                  <option value=''> Selecione... </option>
                  <option value='0'> Não </option>
                  <option value='1'> Sim </option>                  
                </select>  
              </div>

              <!-- div select categoria -->
              <div class="ms-2 d-flex flex-column col-sm-2">
                <p class="mb-0"> Categoria: <span style="color: red;">*</span> </p>
                <select name="categoria" class="mb-3 form-select" aria-label="Default select example" required>
                  <option value=''>Selecione... </option>
                  <option value="motocicleta">Motocicleta</option>
                  <option value="automovel">Automóvel</option>
                  <option value="microonibus">Microônibus</option>
                  <option value="onibus">Ônibus</option>
                  <option value="reboque">Reboque</option>
                  <option value="caminhonete">Caminhonete</option>
                  <option value="caminhao">Caminhão</option>
                  <option value="camioneta">Camioneta</option>
                  <option value="utilitario">Utilitário</option>
                </select>
              </div>

              <!-- div select tipo -->
              <div class="ms-2 d-flex flex-column col-sm-2">
                <p class="mb-0"> Classificação:</p>
                <select name="tipoveic" class="mb-3 form-select" aria-label="Default select example">
                  <option value=''>Selecione... </option>
                  <option value="De passageiros">De passageiros</option>
                  <option value="De carga">De carga</option>
                  <option value="De carga caminhonete">De carga</option>
                  <option value="Misto">Misto</option>
                  <option value="De competição">De competição</option>
                  <option value="De tração">De tração</option>
                  <option value="Especial">Especial</option>
                  <option value="De coleção">De coleção</option>      
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
                  <select name="marca" id='marca' class="mb-3 form-select" required>
                    <option value="">TODAS</option>
                    <?php
                      $i = 0;

                      //$sql = "SELECT * FROM bdfrota.tbmarcaveic WHERE idtbmarcaveic<>'16'  ORDER BY marca";
                      $sql="SELECT * FROM bdfrota.tbmarcaveic where idtbmarcaveic not in (16,35,18,34,39,1059,30,32,1057,29,22,27,25,28,20,24,17,33,31) AND idtbmarcaveic not between 40 and 1055 AND marca<>'' ORDER BY marca";
                      $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
                      
                      while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                        $marca = utf8_encode($row['marca']);
                        $idtbmarcaveic = $row['idtbmarcaveic'];
                        
                        $addmatriz = [$idtbmarcaveic => $i];
                        foreach($addmatriz as $key => $value)
                        {
                          $idselect[$key] = $value;
                        };
                        $i++;
                        
                        print "<option id='$idselect[$idtbmarcaveic]' value='$idtbmarcaveic'> $marca </option>";
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
                  <select name="modelo" id='modelo' class="mb-3 form-select" required>
                    <option value="">TODOS</option>
                    <?php
                      $i = 0;
                      $idselect;

                      $sql = "SELECT DISTINCT(marca), modelo, idtbmodeloveic FROM bdfrota.tbmodeloveic WHERE idtbmodeloveic not in (135,1476,1442,1441,6,1440,1475,158,1449) and idtbmodeloveic not between 159 AND 206 AND idtbmodeloveic not between 209 AND 1438  ORDER BY modelo";
                      $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
                      
                      while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                        $idtbmodeloveic = $row['idtbmodeloveic'];
                        $modelo = utf8_encode($row['modelo']);
                        $marca = $row['marca'];

                        //$lcoordenador = $row[0];
                       //$lgerente = $row[1];
                        $addmatriz = array($modelo => $i );
                        foreach($addmatriz as $key => $value)
                        {
                          $idselect[$key] = $value;
                        }
                        $i++;

                        print "<option id='$idselect[$marca]-$idselect[$modelo]' value='$idtbmodeloveic'> $modelo </option>
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

              <!-- div versão-->
              <div class="ms-2 mb-3 d-flex flex-column col-sm-2">
                <label class="form-label mb-0" for="versao">Versão:</label>
                <input class="form-control" type="text" name="versao" id="versao" placeholder="Versão do veículo">
              </div>

              <!-- div cor-->
              <div class="ms-2 mb-3 col-sm-2 d-flex flex-column">
                <label class="form-label mb-0" for="cor">Cor: <span style="color: red;">*</span> </label>
                <input class="form-control" type="text" name="cor" id="cor" placeholder="Cor do veículo" required>
              </div>

               <!-- div ano fabricação-->
              <div class="ms-2 mb-3 col-sm-2 d-flex flex-column">
                <label class="form-label mb-0" for="anofabric">Ano de fabricação: <span style="color: red;">*</span> </label>
                <input class="form-control" type="text" name="anofabric" id="anofabric" placeholder="Ex.: 1990" required>
              </div>

              <!-- div ano do modelo-->
              <div class="ms-2 mb-3 col-sm-2 d-flex flex-column">
                <label class="form-label mb-0" for="anomodelo">Ano do modelo: </label>
                <input class="form-control" type="text" name="anomodelo" id="anomodelo" placeholder="Ex.: 1990">
              </div>
            </div>

            <!-- div Status Veículo,Tipo Veículo, Situação, Hodômetro Inicial (em Total de Km): *-->
            <!--col-md-12 d-flex justify-content-start-->
            <div class="row">
              <!-- div Status Veículo-->
              <div class="d-flex flex-column col-sm-3">
                <!--div select-->
                <div class="d-flex flex-column">
                  <p class="mb-0"> Status do veículo: <span style="color: red;">*</span> </p>
                  <select name="statusvel" class="mb-3 form-select" id='statusvel' required>
                    <option value=" ">Selecione...</option>
                    <?php
                      $sql = "SELECT * FROM bdfrota.tbastatusvel WHERE idtbastatusvel NOT IN ('18','7','13','19','39') ORDER BY status";
                      $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao)); 
                      
                      while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                        $statusvel = utf8_encode($row['status']);
                        $idtbastatusvel = $row['idtbastatusvel'];
                        $statusgeral = $row['statusgeral'];

                        if($statusgeral == '0'){
                          $statusgeral = 'inativo';
                        }elseif($statusgeral == '1'){
                          $statusgeral = 'ativo';
                        }else{
                          $statusgeral = 'semreg';
                        }

                        print "<option class='$statusgeral' value='$idtbastatusvel'> $statusvel </option>
                        ";
                      }
                     ?>
                  </select>
                </div>
              </div>

              <!-- div tipo de veiculo-->
              <div class="ms-2 d-flex flex-column col-sm-3">
                <!--div select-->
                  <div class="d-flex flex-column">
                  <p class="mb-0"> Tipo: <span style="color: red;">*</span> </p>
                  <select name="tipovel" class="mb-3 form-select" id='tipovel' required>
                    <option value=" ">Selecione...</option>
                    <?php
                      $sql = "SELECT * FROM bdfrota.tbatipovel ORDER BY tipo";
                      $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
                      
                      while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                        $idtbatipovel = utf8_encode($row['idtbatipovel']);
                        $tipovel = utf8_encode($row['tipo']);

                        print "<option value='$idtbatipovel'> $tipovel </option>";
                      }
                     ?>
                  </select>
                </div>
              </div>

              <!-- div situação do veiculo-->
                <div class="ms-2 d-flex flex-column col-sm-2">
                <!--div select-->
                  <div class="d-flex flex-column">
                  <p class="mb-0"> Situação: <span style="color: red;">*</span> </p>
                  <select name="situacao" class="mb-3 form-select" id='situacao' required>
                    <option value=" ">Selecione...</option>
                    <?php
                      $sql = "SELECT DISTINCT(situacao) FROM bdfrota.tbveiculo ORDER BY situacao";
                      $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
                      
                      while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                        $situacao = utf8_encode($row['situacao']);
                        
                        print "<option value='$situacao'> $situacao </option>";
                      }
                     ?>
                  </select>
                  </div>
                </div>

              <!-- div hodometro -->
              <div class='ms-2 d-flex col-sm-4 flex-column'>
                <label class='form-label mb-0' for='hodometroinicial'>Hodômetro Inicial (em Total de Km):</label>
                <input class='form-control' type='text' name='hodometroinicial' id='hodometroinicial' onkeypress='return onlynumber();' placeholder='Hodômetro em Total de Km'>
              </div>
            </div>

            <!-- div Data Ult. Movimentação, Hora Ult. Mov, Matricula, Data Entrega, Data Devolução -->
            <!--col-md-12 d-flex justify-content-start-->
            <div class="row">
              <!-- div data -->
              <div class="col-sm-3 d-flex flex-column justify-content-start">
                <label class="mb-0" for='datamovimentacao'>Data Ult. Movimentação: </label> 
                <input type="date" class="form-control" name="datamovimentacao" id='datamovimentacao' />
              </div>

              <!-- div hora -->
              <div class="ms-1 col-sm-2 d-flex flex-column justify-content-start"> 
                <label class="mb-0" for='horamovimentacao'>Hora Ult. Mov:  </label> 
                <input type="time" class="form-control" name="horamovimentacao" id='horamovimentacao' />
              </div>

              <!-- div Data Entrega -->
              <div class='ms-2 d-flex col-sm-3 flex-column'>
                <label class='form-label mb-0' for='matcond'>Matrícula do condutor:</label>
                <input class='form-control' type='text' name='matcond' id='matcond' onkeypress='return onlynumber();' placeholder='Matrícula do condutor'>
              </div>

              <!-- div data Entrega -->
              <div class="ms-2 col-sm-2 d-flex flex-column justify-content-start">
                <label class="mb-0" for='dtentrega'>Data Entrega: </label> 
                <input type="date" class="form-control" name="dtentrega" id='dtentrega'/>
              </div>

              <!-- div data Devolução -->
              <div class="ms-2 col-sm-2 d-flex flex-column justify-content-start">
                <label class="mb-0" for='dtdevolucao'>Data Devolução:  </label> 
                <input type="date" class="form-control" name="dtdevolucao" id='dtdevolucao'/>
              </div>
            </div>

            <!-- div velocidade máxima, tipo de combustivel, tanque, motorização, calibragem, aro -->
            <!--mt-2 col-md-12 d-flex justify-content-start-->
            <div class="mt-3 row">
              <!-- div velocidade máxima-->
              <div class="col-sm-3 d-flex flex-column">
                <label class="form-label mb-0" for="velmax">Velocidade máxima (em km/h): </label>
                <input class="form-control" type="text" name="velmax" id="velmax" onkeypress="return onlynumber();" placeholder="Velocidade máxima">
              </div>

              <!-- div select tipo de combustível -->
              <div class="ms-2 d-flex flex-column col-sm-2">
                  <p class="mb-0"> Tipo de combustível: <span style="color: red;">*</span> </p>
                  <select name="combustivel" class="form-select mb-3" aria-label="Default select example" required>
                    <option value=''> Selecione... </option>
                    <option value='ALCOOL'> Álcool </option>
                    <option value='GASOLINA'> Gasolina </option>
                    <option value='FLEX'> Flex </option>
                    <option value='DIESEL'> Diesel </option>
                    <option value='NAO SE APLICA'> Não se aplica </option>

                  </select>
              </div>

              <!-- lts tanque -->
              <div class="ms-2 d-flex col-sm-2 flex-column">
                <label class="form-label mb-0" for="tanque">Capac. tanque(em L):</label>
                <input class="form-control" type="text" name="tanque" id='tanque' placeholder="Capacidade em Litros" onkeypress="return onlynumber();">
              </div>

              <!-- motorização -->
              <div class="ms-2 d-flex flex-column col-sm-2">
                <p class="mb-0">Motorização:</p>
                <select name="motorizacao" class="form-select" aria-label="Default select example">
                  <option value=''> Selecione... </option>
                  <option value='1.0'> 1.0 </option>
                  <option value='1.4'> 1.4 </option>
                  <option value='1.6'> 1.6 </option>
                  <option value='1.8'> 1.8 </option>
                  <option value='2.0'> 2.0 </option>
                </select>
              </div>

              <!-- div calibragem -->
              <div class="ms-2 d-flex col-sm-2 flex-column">
                <label class="form-label mb-0" for="calibragem">Calibragem (em PSI): </label>
                <input class="form-control" type="text" name="calibragem" id='calibragem' onkeypress="return onlynumber();" placeholder="Calibragem (em PSI)">
              </div>

              <!-- div aro-->
              <div class="ms-2 d-flex col-sm-1 flex-column">
                <label class="form-label mb-0" for="aro">Aro:</label>
                <input class="form-control" type="text" name="aro" id='aro' onkeypress="return onlynumber();" placeholder="Aro">
              </div>
            </div>

            <!-- div renavam, numero do chassi, num motor, nº de portas, nº de passageiros, nº de pneus -->
            <!--mb-3 col-sm-12 d-flex justify-content-start-->
            <div class="row">
              <!--div renavam-->
              <div class="d-flex col-sm-2 flex-column">
                <label class="form-label mb-0"  for="renavam">RENAVAM: </label>
                <input class="form-control" type="renavam" name="renavam" id="renavam" placeholder="RENAVAM">
              </div>

              <!--div num chassi -->
              <div class="ms-2 d-flex col-sm-2 flex-column">
                <label class="form-label mb-0"  for="chassi">Número do Chassi:</label>
                <input class="form-control" type="text" name="chassi" id="chassi" onKeyPress="if(this.value.length==17) return false;" placeholder="Nº do Chassi">
              </div>

              <!--div num motor -->
              <div class="ms-2 d-flex col-sm-2 flex-column">
                <label class="form-label mb-0"  for="nummotor">Número do Motor:</label>
                <input class="form-control" type="text" name="nummotor" id="nummotor" placeholder="Nº do motor">
              </div>

              <!-- div num portas-->
              <div class="ms-2 d-flex col-sm-2 flex-column">
                <label class="form-label mb-0" for="nportas">Número de Portas: </label>
                <input class="form-control" type="text" name="nportas" id='nportas' onkeypress="return onlynumber();" placeholder="Nº de portas">
              </div>

              <!-- div num passsageiros-->
              <div class="ms-2 d-flex col-sm-2 flex-column">
                <label class="form-label mb-0" for="npassageiros">Nº de Passageiros: </label>
                <input class="form-control" type="text" name="npassageiros" id='npassageiros' onkeypress="return onlynumber();" placeholder="Nº de passageiros">
              </div>

              <!-- div num qtd pneus-->
              <div class="ms-2 d-flex col-sm-2 flex-column">
                <label class="form-label mb-0" for="qtdpneus">Quantidade de Pneus: </label>
                <input class="form-control" type="text" name="qtdpneus" id='qtdpneus' onkeypress="return onlynumber();" placeholder="Quantidade de Pneus">
              </div>
            </div>

            <!-- div qtd estepes, qtd eixos, gnv, gps, tag pedágio -->
            <!--col-md-12 d-flex justify-content-start-->

            <div class="mt-3 row">
              <!-- div qtd estepes -->
              <div class="d-flex col-sm-2 flex-column">
                <label class="form-label mb-0" for="qtdestepes">Quant. de estepes:</label>
                <input class="form-control" type="text" name="qtdestepes" id='qtdestepes' onkeypress="return onlynumber();" placeholder="Quantidade de estepes">
              </div>

              <!-- div qtd eixos -->
              <div class="ms-2 d-flex col-sm-2 flex-column">
                <label class="form-label mb-0" for="qtdeixos">Quantidade de eixos:</label>
                <input class="form-control" type="text" name="qtdeixos" id='qtdeixos' onkeypress="return onlynumber();" placeholder="Quantidade de eixos">
              </div>

              <!-- div gnv -->
              <div class="ms-2 d-flex flex-column col-sm-2">
                <p class="mb-0">O veículo é GNV? <span style="color: red;">*</span></p>
                <select name="gnv" class="mb-3 form-select" aria-label="Default select example" required>
                  <option value=''> Selecione... </option>
                  <option value='0'> Não </option>
                  <option value='1'> Sim </option>                  
                </select>  
              </div>

              <!-- div gps -->
              <div class="ms-2 d-flex flex-column col-sm-2">
                <p class="mb-0">O veículo possui GPS? <span style="color: red;">*</span></p>
                <select name="gps" class="mb-3 form-select" aria-label="Default select example" required>
                  <option value=''> Selecione... </option>
                  <option value='0'> Não </option>
                  <option value='1'> Sim </option>                  
                </select>  
              </div>

              <!-- div tag pedágio -->
              <div class="ms-2 d-flex flex-column col-sm-3">
                <p class="mb-0">O veículo possui Tag Pedágio?</p>
                <select name="tagpedagio" class="mb-3 form-select" aria-label="Default select example">
                  <option value=''> Selecione... </option>
                  <option value='0'> Não </option>
                  <option value='1'> Sim </option>                  
                </select>  
              </div>
            </div>

            <!-- GPS Empresa, Doc. CRLV, Airbag, Rack, Blindagem: -->
            <!--col-md-12 d-flex justify-content-start-->
            <div class="row">
              <!-- div GPS Empresa -->
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
                        $gpsemp = utf8_encode($row['fantasia']);

                        print "<option value='$gpsemp'> $gpsemp </option>";
                      }
                     ?>
                     <option value='nao possui'>Não possui</option>
                  </select>
                </div>
              </div>
            
              <!-- div doc CRVL -->
              <div class='ms-2 d-flex col-sm-2 flex-column'>
                <label class='form-label mb-0' for='doccrlv'>Doc. CRLV:</label>
                <input class='form-control' type='text' name='doccrlv' id='doccrlv' placeholder='Doc. CRLV'>
              </div>

              <!-- div airbag -->
              <div class="ms-2 d-flex flex-column col-sm-3">
                <p class="mb-0">O veículo possui Airbag?</p>
                <select name="airbag" class="mb-3 form-select" aria-label="Default select example">
                  <option value=''> Selecione... </option>
                  <option value='0'> Não </option>
                  <option value='1'> Sim </option>                  
                </select>  
              </div>

              <!-- div rack -->
              <div class="ms-2 d-flex flex-column col-sm-3">
                <p class="mb-0">O veículo possui rack?</p>
                <select name="rack" class="mb-3 form-select" aria-label="Default select example">
                  <option value=''> Selecione... </option>
                  <option value='0'> Não </option>
                  <option value='1'> Sim </option>                  
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
                      $blindagem = utf8_encode($row['blindagem']);
                      
                      print "<option value='$blindagem'> $blindagem </option>";
                    }
                   ?>                 
                </select>  
              </div> 
            </div>
            
            <!-- Oficina, N° Contrato Locação, Data Disp. Locadora, Data Dev. Locadora, Valor Aquisição -->
            <!--col-md-12 d-flex justify-content-start-->
            <div class="row">
              <!-- div Oficina-->
              <div class='d-flex col-sm-4 flex-column'>
                <label class='form-label mb-0' for='oficina'>Oficina: </label>
                <input class='form-control' type='text' name='oficina' id='oficina' placeholder='Oficina'>
              </div>

              <!-- div N° Contrato Locação -->
              <div class='ms-2 d-flex col-sm-2 flex-column'>
                <label class='form-label mb-0' for='ncontloc'>N° Contrato Locação:</label>
                <input class='form-control' type='text' name='ncontloc' id='ncontloc' placeholder='Número Contrato Locação'>
              </div>

              <!-- div Data Disp. Locadora -->
              <div class="ms-2 col-sm-2 d-flex flex-column justify-content-start">
                <label class="mb-0" for='dtdisponivelloc'>Data Disp. Locadora:  </label> 
                <input type="date" class="form-control" name="dtdisponivelloc" id='dtdisponivelloc'  />
              </div>

              <!-- div Data Dev. Locadora -->
              <div class="ms-2 col-sm-2 d-flex flex-column justify-content-start">
                <label class="mb-0" for='dtdevolucaoloc'>Data Dev. Locadora:  </label> 
                <input type="date" class="form-control" name="dtdevolucaoloc" id='dtdevolucaoloc' />
              </div>

              <!-- div Valor Aquisição -->
              <div class='ms-2 d-flex col-sm-2 flex-column'>
                <label class='form-label mb-0' for='ncontloc'>Valor Aquisição:</label>
                <input class='form-control valor' type='text' name='valaquisicao' id='valaquisicao' placeholder='R$ 0,00'>
              </div>             
            </div>

            <!--mt-3 col-md-12 d-flex justify-content-start-->
            <div class="mt-3 row">
              <!-- div hodometro -->
              <div class="d-flex col-sm-3 flex-column">
                <label class="form-label mb-0" for="hodometro">Hodômetro (em Total de Km): </label>
                <input class="form-control" type="text" name="hodometro" id='hodometro' onkeypress="return onlynumber();" placeholder="Hodômetro em Total de Km">
              </div>

              <!-- div tag propriedade -->
              <div class="ms-2 d-flex flex-column col-sm-3">
                <p class="mb-0">Tipo de posse: <span style="color: red;">*</span></p>
                <select name="tipoposse" id='tipoposse' class="mb-3 form-select" aria-label="Default select example" required onchange="verifica(this.value)">
                  <option value=''> Selecione uma opção </option>
                  <option value='AGREGADO'> Agregado </option>
                  <option value='LOCADO'> Locado </option>
                  <option value='PRÓPRIO'> Próprio </option>
                  <option value='TERCEIRO'> Terceiro </option>
                  <option value='PARTICULAR'> Particular </option>
                </select>  
              </div>

              <!-- div locador -->
              <div class="ms-2 d-flex col-sm-3 flex-column">
                <label class="form-label mb-0" for="qtdestepes">Locadora (se o veículo for locado): </label>
                <select name="locador" id='locador' class="mb-3 form-select" disabled>
                  <option value=" "> </option>
                  <?php 
                    $sql = "SELECT idtbfornecedor, fantasia FROM bdfrota.tbfornecedor WHERE tipo='4' AND status='1'";
                    $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
                    
                    while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                      $idtbfornecedor = $row['idtbfornecedor'];
                      $fantasia = utf8_encode($row['fantasia']);

                      print "<option value='$idtbfornecedor'> $fantasia </option>
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
                      $nome = utf8_encode($row['nome']);

                      print "<option value='$cnpj'> $cnpj </option>
                      ";
                    }
                   ?>
                </select>
              </div>
            </div>

            <!--div documentos-->
            <div class="col-md-12 d-flex justify-content-start">
              <!--div CRLV-->
              <div class="d-flex flex-column col-sm-4">
                <label class="form-label mb-0" for="crlv"> Certificado de Registro e Licenciamento do Veículo (CRLV):</label>
                <input class="form-control" type="file" name="crlv" id="crlv" accept=".pdf"/>
              </div>

             <!--div CRV -->
              <div class="mt-4 ms-2 d-flex col-sm-4 flex-column">
                <label class="form-label mb-0" for="crv">Certificado de Registro do Veículo (CRV): </label>
                <input class="form-control" type="file" name="crv" id='crv' accept=".pdf">
              </div>

              <!-- div pagamento IPVA -->
              <div class="mt-4 ms-2 d-flex flex-column col-sm-4">
                <label class="form-label mb-0" for="ipva">Certidão de Pagamento do IPVA:</label>
                <input class="form-control" type="file" name="ipva" id='ipva' accept=".pdf"> 
              </div>
            </div>

            <!-- div base gestão e centro de custo -->
            <div class="mt-3 col-md-12 d-flex justify-content-start">
              <div class="col-sm-4">                
                <label for="basegestao">Base Gestão:</label>
                <!--<input class='form-control' type='text' name='basegestao' id='basegestao' placeholder='Base Gestão'>-->

                <select class='form-select' name='basegestao' id='basegestao'>
                  <option  value=''>Selecione...</option>
                <?php
                  $sql="SELECT DISTINCT (basegestao) FROM bdfrota.tbveiculo ORDER BY basegestao;";
                  $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
                    
                  while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                    $basegestao = $row['basegestao'];
                    if($basegestao!=''){
                      print "<option value='$basegestao'> $basegestao </option>";
                    }                    
                  }
                ?>                  
                </select>
              </div>

              <div class="ms-2 col-sm-4">                
                <label for="centrocusto">Centro de Custo:</label>
                <select name="centrocusto" id='centrocusto'  class="mb-3 form-select">
                  <option value=" ">Selecione... </option>
                  <?php 
                    $sql1 = "SELECT distinct(ccusto) FROM bdaniel.tbfuncionario;";
                    $resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));
                    
                    while($row1 = mysqli_fetch_array($resultado1, MYSQLI_ASSOC)){
                      $ccusto = utf8_encode($row1['ccusto']);                     
                      if($ccusto !=''){
                        print "<option value='$ccusto'> $ccusto </option>
                      ";
                      }
                      
                    }
                   ?>
                </select>
              </div>
            </div>

            <!-- div observação -->
            <div class="mt-3 col-md-12 d-flex justify-content-start">
              <div class="col-sm-12 form-floating mb-3">
                <textarea class="form-control" name='obsveiculo' id="obsveiculo" type="text" placeholder="Observação" rows="4" cols="50" style="height: 200px !important;"></textarea>
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
                <input type="submit" class="btn btn-success" value="Confirmar cadastro" />
              </div>

              <div class="ms-5">
                <a class="btn btn-danger" href='./listagemveiculos.php'> Cancelar cadastro </a>
              </div>
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
      ///var idmarca1 = idmarca.substring(0,2);
      var modelo = document.getElementById('modelo');
      
      for (var i = 0; i < modelo.length; i++) {
        var idmodelo = modelo.options[i].id;
        
        var idmodelo1 = idmodelo.split("-");
        var idmodelo1 = idmodelo1[0];

        if (idmodelo1 != idmarca) {
          $(modelo.options[i]).attr('disabled', 'disabled').hide();
        } else {
          $(modelo.options[i]).removeAttr('disabled').show();
        }
      }
        /*console.log(idmodelo1); !idmodelo1.startsWith(idmarca1)*/
        console.log(idmarca);
      
    });

    /*$("#modelo").change(function() {
      var marca = document.getElementById('marca');
      var idmarca = marca[marca.selectedIndex].id;
      var modelo = document.getElementById('modelo');
      var idmodelo = modelo[modelo.selectedIndex].id;
      const id = idmodelo.split('-');
      const [marca1, modelo1] = id;
      for (var i = 0; i < marca.length; i++) {
        if (marca[i].id == marca) {
          marca[i].selected = true;
        }else{
          marca[i].selected = false;
        }
      }
    });*/
    
    $(document).ready(function(){
      //mascaras de input
      $('#anofabric').mask('0000');
      $('#anomodelo').mask('0000');
      $('#renavam').mask('00000000000');
      //$('#nummotor').mask('000000'); 
      //$('#hodometro').mask('000.000');

      $("#chassi").on("input", function(){
        $(this).val($(this).val().toUpperCase());
      });

      $("#nummotor").on("input", function(){
        $(this).val($(this).val().toUpperCase());
      });

      $('.valor').maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:',', affixesStay: true});

      //formatação de placa
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

    //função para habilitar ou desabilitar input
    function verifica(value){
      var input = document.getElementById("locador");
      var input2 = document.getElementById("baseffa");


      if(value == 'LOCADO'){
        input.disabled = false;
      }else {
        input.disabled = true;
      }

      if(value == 'PRÓPRIO'){
        input2.disabled = false;
      }else {
        input2.disabled = true;
      }

      //console.log(value);
    };

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