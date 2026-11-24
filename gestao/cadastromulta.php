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
  $tipo=$_SESSION['tipo'];

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

  //include"../conecta.php";
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

    <title> Cadastro de Multa </title>

    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />

    <link href="../src/css/styles.css" rel="stylesheet" />    

    <!--link Material Symbols - simbolos do input-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
  </head>
  <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

    <!--nav-->
    <?php include"../menu.php"; ?>

    <div id="layoutSidenav_content">

      <main style="width: 100%;">

        <div class="container-fluid px-4" style="width: 100%;"> 
          <h1 class="h1 pt-2 pb-2 m-auto text-center"> Cadastro de Multa </h1>

          <!-- div do formulario -->
          <div>
            <form method="post" action="./control/cadastrarmulta.php" style="width: 80%;" class="m-auto">
              <!-- div infos iniciais-->
              <?php print"<input type='hidden' name='matr_autor' id='matr_autor' value='$matricula1'>"; ?>
              <div class="d-flex col-sm-12 m-auto justify-content-start align-items-end">
                <!-- div placa-->
                <div class="col-sm-3 d-flex flex-column">
                  <label class="form-label" for="placa">Placa:<span style="color: red;">*</span> </label>
                  <input class="form-control" style="min-width: 100px;" type="text" name="placa" id="placa" pattern="[Aa-Zz]{3}[0-9]{1}[Aa-Zz]{1}[0-9]{2}|[Aa-Zz]{3}[0-9]{4}" maxlength="7" placeholder="Nº placa do veículo multado" required>
                </div>

                <!-- div filial caso seja input
                <div class="mt-2 d-flex input-group">
                  <label class="form-label mt-2" for='filial'>Filial:<span style="color: red;">*</span> </label>
                  <input class="form-control" style="width: 80%;" type="text" name="filial" id="filial" required>
                </div>-->

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

                          print "<option value='$idtbfilial'> $nomefilial </option>";
                        }
                      ?> 
                    </select>
                </div>

                <!-- div centro de custo -->
                <div class="ms-1 col-sm-3 d-flex flex-column">
                  <label class="form-label mt-2" for='ccusto'>Centro de custo:<span style="color: red;">*</span> </label>
                  <select class="form-select" name="ccusto" required>
                      <option value=" "> Selecione o centro de custo  </option>
                      <?php  
                        $sql="SELECT DISTINCT(ccusto) FROM bdaniel.tbfuncionario WHERE ccusto<>'' AND status = 'Ativo' ORDER BY ccusto;";
                        $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
                      
                        while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                          $ccusto = $row['ccusto'];

                          print "<option value='$ccusto'> $ccusto </option>";
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
                        $sql="SELECT idtbfornecedor, fantasia FROM bdfrota.tbfornecedor WHERE tipo=4;";
                        $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
                      
                        while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                          $idtbfornecedor = $row['idtbfornecedor'];
                          $fantasia = $row['fantasia'];

                          print "<option value='$idtbfornecedor'> $fantasia </option>";
                        }
                      ?> 
                    </select>
                </div>
              </div>

              <!-- div anotação, tipo de infração, nº auto infração, expedida em -->
              <div class="mt-2 d-flex col-sm-12 m-auto justify-content-start align-items-start">
                <!-- div anotação -->
                <div class="col-sm-6 d-flex flex-column justify-content-start">
                  <p class="mb-0">Anotação: </p>
                  <textarea class="form-control" name="anotacao" placeholder="Deixe aqui sua anotação"></textarea>
                </div>

                <!-- div tipo de infração -->
                <div class="ms-1 col-sm-3 d-flex flex-column justify-content-start">
                  <p class="mb-0">Tipo de infração: <span style="color: red;">*</span></p>
                  <select name='tipoinfracao' class="form-select" required>
                    <option value=''>Selecione tipo de infração</option>
                    <option value='NOTIFICAÇÃO' >Notificação</option>
                    <option value="MULTA" >Multa</option>
                    <option value="MULTA FORA DO PRAZO" >Multa fora do prazo</option>
                    <option value="AGRAVO" >Agravo</option>
                  </select>
                </div>

                <!-- div nº auto infração -->
                <div class="ms-1 col-sm-3 d-flex flex-column justify-content-start">
                  <label class="mb-0" for='autoinfracao'>Número do auto de infração: <span style="color: red;">*</span> </label> 
                  <input type="text" class="form-control" name="autoinfracao" id='autoinfracao'  placeholder="Nº do auto de infração" required/>
                </div>
              </div>

              <!-- div data, hora, datas limites -->
              <div class="mt-2 d-flex col-sm-12 m-auto justify-content-start align-items-end">
                <!-- div data -->
                <div class="col-sm-2 d-flex flex-column justify-content-start">
                  <label class="mb-0" for='datainfracao'>Data da infração: <span style="color: red;">*</span> </label> 
                  <input type="date" class="form-control" name="datainfracao" id='datainfracao' required />
                </div>

                <!-- div hora -->
                <div class="ms-1 col-sm-2 d-flex flex-column justify-content-start">
                  <label class="mb-0" for='horainfracao'>Hora infr.: <span style="color: red;">*</span> </label> 
                  <input type="time" class="form-control" name="horainfracao" id='horainfracao' required />
                </div>

                <!-- div limite condutor -->
                <div class="ms-1 col-sm-3 d-flex flex-column justify-content-start">
                  <label class="mb-0" for='datalimitecond' style='font-size: 15px;'>Data limite - apresentar o condutor: <span style="color: red;">*</span> </label> 
                  <input type="date" class="form-control" name="datalimitecond" id='datalimitecond' required />
                </div>

                <!-- div limite locador -->
                <div class="ms-1 col-sm-3 d-flex flex-column justify-content-start">
                  <label class="mb-0" for='datalimiteloc'>Data limite - apresentar a locadora: <span style="color: red;">*</span> </label> 
                  <input type="date" class="form-control" name="datalimiteloc" id='datalimiteloc' required />
                </div>

                <!-- div expedida em -->
                <div class="ms-1 col-sm-2 d-flex flex-column justify-content-start">
                  <label class="mb-0" for='expedicao'>Multa expedida em: <span style="color: red;">*</span> </label> 
                  <input type="date" class="form-control" name="expedicao" id='expedicao' required />
                </div>
              </div>

              <!-- div recebida em, data venc multa, código da multa, pontos -->
              <div class="mt-2 d-flex col-sm-12 m-auto justify-content-start align-items-end">
                <!-- div recebida em -->
                <div class="col-sm-3 d-flex flex-column justify-content-start">
                  <label class="mb-0" for='recebimento'>Multa recebida em: <span style="color: red;">*</span> </label> 
                  <input type="date" class="form-control" name="recebimento" id='recebimento' required />
                </div>

                <!-- div data de vencimento -->
                <div class="ms-1 col-sm-3 d-flex flex-column justify-content-start">
                  <label class="mb-0" for='vencimento'>Data de vencimento da multa: <span style="color: red;">*</span> </label> 
                  <input type="date" class="form-control" name="vencimento" id='vencimento' required />
                </div>

                <!-- div código da multa -->
                <div class="ms-1 col-sm-3 d-flex flex-column justify-content-start">
                  <label class="mb-0" for='codigom'>Código da multa: <span style="color: red;">*</span> </label> 
                  <input type="text" class="form-control" name="codigom" id='codigom' onkeypress="return event.charCode >= 48 && event.charCode <= 57" placeholder="Código da multa" required/>
                </div>

                <!-- div pontos -->
                <div class="ms-1 col-sm-3 d-flex flex-column justify-content-start">
                  <label class="mb-0" for='pontos'>Pontos: <span style="color: red;">*</span> </label> 
                  <input type="text" class="form-control" name="pontos" id='pontos' onkeypress="return event.charCode >= 48 && event.charCode <= 57" placeholder='Nº de pontos da multa' required/>
                </div>
              </div>

              <!--div gravidade, valor, valor com desconto, taxa adm -->
              <div class="mt-2 d-flex col-sm-12 m-auto justify-content-start align-items-end">
                <!-- div gravidade -->
                <div class=" col-sm-3 d-flex flex-column justify-content-start">
                  <!--<label class="mb-0" for='gravidade'>Gravidade da multa: </label> 
                  <input type="text" class="form-control" name="gravidade" id='gravidade' />-->
                  <p class="mb-0">Gravidade da multa: <span style="color: red;">*</span> </p>
                  <select name='gravidade' class="form-select" required>
                    <option value=''>Selecione a gravidade</option>
                    <option value='LEVE'>Leve</option>
                    <option value="MEDIA">Média</option>
                    <option value="GRAVE">Grave</option>
                    <option value="GRAVISSIMA">Gravíssima</option>
                  </select>
                </div>

                <!-- div valor -->
                <div class=" ms-1 col-sm-3 d-flex flex-column justify-content-start">
                  <label class="mb-0" for='valor'>Valor da multa: <span style="color: red;">*</span> </label> 
                  <input type="text" class="form-control valor" name="valor" id='valor' placeholder="R$ 0,00" required />
                </div>

                <!-- div valor com desconto -->
                <div class=" ms-1 col-sm-3 d-flex flex-column justify-content-start">
                  <label class="mb-0" for='valdesconto'>Valor do desconto:  </label> 
                  <input type="text" class="form-control valor" name="valdesconto" id='valdesconto' placeholder="R$ 0,00" />
                </div>

                <!-- div taxa adm 
                <div class="ms-1 col-sm-3 d-flex flex-column justify-content-start">
                  <label class="mb-0" for='taxaadm'>Taxa Administrativa:  </label> 
                  <input type="text" class="form-control valor" name="taxaadm" id='taxaadm' placeholder="R$ 0,00" />
                </div>-->
              </div>

              <!-- div juros, valor total -->
              <div class="mt-2 d-flex col-sm-12 m-auto justify-content-start align-items-end">
                <!-- div juros -->
                <div class="col-sm-3 d-flex flex-column justify-content-start">
                  <label class="mb-0" for='juros'>Juros (em %):  </label> 
                  <input type="text" class="form-control" name="juros" id='juros' placeholder="Taxa de juros - 000.00%" maxlength='6' />
                </div>

                <!-- div valortotal -->
                <div class="ms-1 col-sm-3 d-flex flex-column justify-content-start">
                  <label class="mb-0" for='valtotal'>Valor total da multa:  </label> 
                  <input type="text" class="form-control valor" name="valtotal" id='valtotal' placeholder="R$ 0,00" />
                </div>

                <!-- div valor com desconto -->
                <div class=' ms-1 col-sm-3 d-flex flex-column justify-content-start'>
                  <label class='mb-0' for='valcomdesc'>Valor da multa com desconto: </label> 
                  <input type='text' class='form-control valor' name='valcomdesc' id='valcomdesc' placeholder='R$ 0,00'/>
                </div>
              
              </div>

              <!-- orgão atuador, endereço, municipio -->
              <div class="mt-2 d-flex col-sm-12 m-auto justify-content-start align-items-end">
                <!-- div orgão autuador -->
                <div class="col-sm-3 d-flex flex-column justify-content-start">
                  <label class="mb-0" for='orgao'>Órgão autuador: <span style="color: red;">*</span> </label> 
                  <input type="text" class="form-control" name="orgao" id='orgao' placeholder="Nome do órgão" required />
                </div>

                <!-- div endereço -->
                <div class="ms-1 col-sm-6 d-flex flex-column justify-content-start">
                  <label class="mb-0" for='endereco'>Endereço: <span style="color: red;">*</span> </label> 
                  <input type="text" class="form-control" name="endereco" id='endereco' placeholder="Endereço" required />
                </div>

                <!-- div municipio -->
                <div class="ms-1 col-sm-3 d-flex flex-column justify-content-start">
                  <label class="mb-0" for='municipio'>Municipio: <span style="color: red;">*</span> </label> 
                  <input type="text" class="form-control" name="municipio" id='municipio' placeholder="Município" required />
                </div>
              </div>

              <!-- div status, etapa, descricaoinfra, matricula, condutor, -->
              <div class="mt-2 d-flex col-sm-12 m-auto justify-content-start align-items-start">
                
                <!-- div status 
                <div class='col-sm-3 d-flex flex-column justify-content-start'>
                  <label class='mb-0' for='status'>Status da multa: </label> 
                  <input type='text' class='form-control' name='status' id='status' >
                </div>-->

                <!-- div etapa 
                <div class='ms-1 col-sm-3 d-flex flex-column justify-content-start'>
                  <label class='mb-0' for='etapa'>Etapa da multa: </label> 
                  <input type='text' class='form-control' name='etapa' id='etapa' >
                </div>-->

                <!-- div descrição infra -->
                <div class='ms-1 col-sm-6 d-flex flex-column justify-content-start'>
                  <label class='mb-0' for='descricao'>Descrição da multa: </label> 
                  <textarea class='form-control' name='descricao' placeholder='Descrição da infração'> </textarea>
                </div>

                <!-- div matricula -->
                <div class="ms-1 col-sm-3 d-flex flex-column justify-content-start">
                  <label class="mb-0" for='matriculac'>Matrícula do Condutor: <span style="color: red;">*</span> </label> 
                  <input type="text" class="form-control" name="matriculac" id='matriculac' placeholder="Matrícula do condutor" onkeypress="return event.charCode >= 48 && event.charCode <= 57" maxlength="6" required/>
                </div>

                <!-- div condutor -->
                <div class="ms-1 col-sm-3 d-flex flex-column justify-content-start">
                  <label class="mb-0" for='nomec'>Nome do Condutor: <span style="color: red;">*</span> </label> 
                  <input type="text" class="form-control" name="nomec" id='nomec' placeholder="Nome do Condutor"  required />
                </div>
                
              </div>

              <!--  nº parcelas, valor da parcela 
              <div class="mt-2 d-flex col-sm-12 m-auto justify-content-start align-items-end">
                <-- div nºparcelas 
                <div class="ms-1 col-sm-3 d-flex flex-column justify-content-start">
                  <label class="mb-0" for='numparcelas'>Número de parcelas: <span style="color: red;">*</span> </label> 
                  <input type="text" class="form-control" name="numparcelas" id='numparcelas' onkeypress="return event.charCode >= 48 && event.charCode <= 57" placeholder="Nº de parcelas"/>
                </div>

                <-- div valor das parcelas
                <div class="ms-1 col-sm-3 d-flex flex-column justify-content-start">
                  <label class="mb-0" for='valparcelas'>Valor das parcelas: <span style="color: red;">*</span> </label> 
                  <input type="text" class="form-control valor" name="valparcelas" id='valparcelas' placeholder="R$ 0,00" />
                </div>
              </div>-->

              <!-- descontar a partir, descontado do condutor, tipo desconto, data desconto 
              <div class="mt-2 d-flex col-sm-12 m-auto justify-content-start align-items-end">
                div descontar a partir 
                <div class="col-sm-3 d-flex flex-column justify-content-start">
                  <label class="mb-0" for='descontoapartir'>Descontar a partir de: <span style="color: red;">*</span> </label> 
                  <input type="text" class="form-control" name="descontoapartir" id='descontoapartir' placeholder="Descontar a partir de..." required />
                  <input class="form-control" type="month" name="descontoapartir" id='descontoapartir' >
                </div>

                div descontado condutor 
                <div class="ms-1 col-sm-3 d-flex flex-column justify-content-start">
                  <p class="mb-0">Descontado do condutor:<span style="color: red;">*</span>  </p> 
                  <select name="descontadocondutor" class="form-select" onchange="verifica(this.value)" >
                    <option value=''>Selecione</option>
                    <option value="0"> Não </option>
                    <option value="1"> Sim </option>
                  </select>
                </div>

                div tipo de desconto 
                <div class="ms-1 col-sm-3 d-flex flex-column justify-content-start">
                  <label class="mb-0" for='tipodesconto'>Tipo do desconto: </label> 
                  <input type="text" class="form-control" name="tipodesconto" id='tipodesconto' disabled placeholder="Tipo de desconto">
                </div>

                div data do desconto 
                <div class="ms-1 col-sm-3 d-flex flex-column justify-content-start">
                  <label class="mb-0" for='datadesconto'>Data do desconto: <span style="color: red;">*</span> </label> 
                  <input type="date" class="form-control" name="datadesconto" id='datadesconto' >
                </div>
              </div>-->

              <!-- real infrator, pessoa indicada, assinatura condutor, data assinatura, data recusa 
              <div class="mt-2 d-flex col-sm-12 m-auto justify-content-start align-items-end">
                <-- div real infrator --
                <div class="col-sm-3 d-flex flex-column justify-content-start">
                  <p class="mb-0">Condutor é o real infrator: <span style="color: red;">*</span> </p> 
                  <select name="realinfrator" class="form-select" onchange="verifica2(this.value)" >
                    <option value=''>Selecione</option>
                    <option value="0"> Não </option>
                    <option value="1"> Sim </option>
                  </select>
                </div>

                <-- div pessoa indicada --
                <div class="ms-1 col-sm-3 d-flex flex-column justify-content-start">
                  <label class="mb-0" for='pessoaindicada'>Pessoa indicada: </label> 
                  <input type="text" class="form-control" name="pessoaindicada" id='pessoaindicada' disabled placeholder="Pessoa indicada">
                </div>

                <-- div condutor assinou --
                <div class="ms-1 col-sm-2 d-flex flex-column justify-content-start">
                  <p class="mb-0">Condutor assinou: <span style="color: red;">*</span> </p> 
                  <select name="condassinatura" class="form-select" onchange="verifica3(this.value)" required>
                    <option value=" ">Selecione </option>
                    <option value="0"> Não </option>
                    <option value="1"> Sim </option>
                  </select>
                </div>

                <-- div data assinatura --
                <div class="ms-1 col-sm-2 d-flex flex-column justify-content-start">
                  <label class="mb-0" for='reciboassinado'>Data do recibo assinado: </label> 
                  <input type="date" class="form-control" name="reciboassinado" id='reciboassinado' disabled>
                </div>

                <-- div data da recusa 
                <div class="ms-1 col-sm-2 d-flex flex-column justify-content-start">
                  <label class="mb-0" for='recusaassinar'>Data da recusa em assinar: </label> 
                  <input type="date" class="form-control" name="recusaassinar" id='recusaassinar' disabled>
                </div>
              </div>-->

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

              <!-- div comprpag, pgempresa, datapagempr, motivonpg 
              <div class="mt-2 d-flex col-sm-12 m-auto justify-content-start align-items-start">
                
                <-- div comprpag --
                <div class="col-sm-3 d-flex flex-column justify-content-start">
                  <p class="mb-0">Existe comprovante de pagamento? </p> 
                  <select name="comprpag" class="form-select">
                    <option value="0"  > Não </option>
                    <option value="1"  > Sim </option>
                  </select>
                </div>

                <-- div pgempresa --
                <div class="ms-1 col-sm-2 d-flex flex-column justify-content-start">
                  <p class="mb-0">Pago pela empresa? </p> 
                  <select name="pgempresa" class="form-select">
                    <option value="0"  > Não </option>
                    <option value="1" > Sim </option>
                  </select>
                </div>

              
                <-- div datapagempr --
                <div class='ms-1 col-sm-3 d-flex flex-column justify-content-start'>
                  <label class='mb-0' for='datapagempr'>Data de pagamento: </label> 
                  <input type='date' class='form-control' name='datapagempr' id='datapagempr' >
                </div>

                <-- div motivonpg --
                <div class='ms-1 col-sm-3 d-flex flex-column justify-content-start'>
                  <label class='mb-0' for='motivonpg'>Motivo não pagamento: </label> 
                  <input type='text' class='form-control' name='motivonpg' id='motivonpg' >
                </div>
              </div>-->

              <!-- div numprotocolo, expprotocolo, recprotocolo -->
              <div class="mt-2 d-flex col-sm-12 m-auto justify-content-start align-items-start">
                
                <!-- div numprotocolo -->
                <div class='col-sm-3 d-flex flex-column justify-content-start'>
                  <label class='mb-0' for='datanumprotocolo'>Protocolo da multa: </label> 
                  <input type='text' class='form-control' name='datanumprotocolo' id='datanumprotocolo' >
                </div>

                <!-- div expprotocolo -->
                <div class='ms-1 col-sm-3 d-flex flex-column justify-content-start'>
                  <label class='mb-0' for='expprotocolo'>Dia de expedição: </label> 
                  <input type='date' class='form-control' name='expprotocolo' id='expprotocolo' >
                </div>

                <!-- div recprotocolo -->
                <div class='ms-1 col-sm-3 d-flex flex-column justify-content-start'>
                  <label class='mb-0' for='recprotocolo'>Dia de recebimento: </label> 
                  <input type='date' class='form-control' name='recprotocolo' id='recprotocolo' >
                </div>
              </div>

              <!-- div texto campos obrigatório -->
              <div style="color: red;" class="mt-3 pt-2 col-sm-12 m-auto border-top border-dark">
                <p>* Campos obrigatórios.<p>
              </div>

              <!-- div botão -->
              <div class="mt-3 mb-3 pb-2 d-flex col-sm-12 justify-content-center">
                <div>
                  <button class="btn btn-success" type='submit' id='btnSubmit'> Confirmar cadastro </button>
                </div>

                <!--<div class="ms-5">
                  <a class="btn btn-danger" href='../../multas/testemultas2.php'> Cancelar cadastro </a>
                </div>-->
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

    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <script src="../src/js/scripts.js"></script>

    <script type="text/javascript">
      //mascaras de input
      $(document).ready(function(){
        $("#autoinfracao").on("input", function(){
          $(this).val($(this).val().toUpperCase());
        });

        $("#nomec").on("input", function(){
          $(this).val($(this).val().toUpperCase());
        });

        $("#orgao").on("input", function(){
          $(this).val($(this).val().toUpperCase());
        }); 

        $("#tipodesconto").on("input", function(){
          $(this).val($(this).val().toUpperCase());
        });

        $("#endereco").on("input", function(){
          $(this).val($(this).val().toUpperCase());
        });

        $("#municipio").on("input", function(){
          $(this).val($(this).val().toUpperCase());
        });

        $("#pessoaindicada").on("input", function(){
          $(this).val($(this).val().toUpperCase());
        });

        //$('#juros').mask('000.00');
        $('#codigom').mask('000-00');
        $('.valor').maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:',', affixesStay: true});

        $('#juros').maskMoney({allowNegative: true, decimal:'.', affixesStay: true});

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

        // Proteção contra duplo clique no submit
        $("form").on("submit", function(event) {
          $("#btnSubmit").prop("disabled", true);
          $("#btnSubmit").html('<i class="fas fa-spinner fa-spin me-2"></i>Processando...');
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
          input.disabled = false;
        }else {
          input.disabled = true;
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