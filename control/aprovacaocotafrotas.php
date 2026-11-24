<?php
date_default_timezone_set('America/Sao_Paulo');
$hoje = date('Y-m-d H:i:s');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
include("../conecta.php");
include_once "../func/log.php";

$matriculaf = $_POST['matriculasup'];
$id = $_POST['id'];
$idtbsaldo = $_POST['idtbsaldo'];
$decisao = $_POST['decisao'];
$valor = $_POST["valor"];
$valor = str_replace(",", ".", $valor);
$valor_ant = $_POST["valor_ant"];
$valor_ant = str_replace(",", ".", $valor_ant);
$orcpedido = $_POST["orcpedido"];
$orcpedido = str_replace(",", ".", $orcpedido);
$tipocota = $_POST["tipocota"];
$dataplantao = $_POST["dataplantao"];


if ($valor == '') {
  $valor = $orcpedido;
}




$sql = "SELECT valor FROM bdfrota.tbcoord where matricula = '$matnnnricula'";
$resultado = mysql_query($sql) or die(mysql_error());
$row = mysql_fetch_array($resultado);
$saldosup = $row[0];



if ($decisao == 2) {

  if ($orcpedido == $valor) {

    $sql = "UPDATE bdfrota.tbpedidoscoordcota SET flag = $decisao, tipocota = '$tipocota', dataplantao = '$dataplantao' where idtbpedidoscoordcota = $id";
    $resultado = mysql_query($sql) or die(mysql_error());

    $sql = "UPDATE bdfrota.tbcoord SET saldo = saldo + $valor, orcrecebido = orcrecebido + $valor where matricula = '$matriculaf'";
    $resultado = mysql_query($sql) or die(mysql_error());

    $sql = "SELECT idtborcamentomensal FROM bdfrota.tborcamentomensal order by data desc";
    $resultado = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_array($resultado);
    $idorcmen = $row[0];

    $sql = "UPDATE bdfrota.tborcamentomensal SET cotafrota = cotafrota + $valor where idtborcamentomensal = '$idorcmen'";
    $resultado = mysql_query($sql) or die(mysql_error());

    $sql = "SELECT ds_placa FROM bdfrota.tbcarrosxcolaborador where id_colaborador = '$matriculaf'";
    $resultado = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_array($resultado);
    $placa = $row[0];

    $sql = "SELECT unidade FROM bdfrota.tbveiculo where matcond = '$matriculaf'";
    $resultado = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_array($resultado);
    $unidade = $row[0];

    if ($unidade != 'RJ' && $unidade != "") {

      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, 'https://kissapi.valecard.com.br/core/services/frotaCliente/alterarLimiteveiculo');
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_POST, TRUE);
      curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Basic NTI0MDczQHdlYlNlcnZpY2U6MDY5NDc=',
      ));
      curl_setopt($curl, CURLOPT_HEADER, 0);


      $body = '{
"contratoCliente" : "524073",
"veiculoId" : null,
"placa" : "' . $placa . '",
"numeroCartao" : null,
"valorAlteracao" : "' . $valor . '",
"tipoAlteracao" : "adicionar",
"periodo" : "atual",
"motivo" : "Sistema Combustivel",
"modulo" : "abastecimento"
}';

      curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
      echo "$body";
      $result = json_decode(curl_exec($curl));

    } else {

      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/relatorioExtratoSimplificado/search');
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_POST, TRUE);
      curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Accept: application/json',
        'Accept-Charset: utf-8',
        'Accept-Encoding: gzip',
        'Authorization: Basic W09wZXJhZG9yV2ViXWFwcDE4MzA2MjQyNDc6RFhYSm5WejA=',
      ));
      curl_setopt($curl, CURLOPT_HEADER, 0);


      $body = '{
  "codigoCliente": 183062,
  "codigoProduto": 4,
  "numeroCartao": null,
  "especieCartao": null,
  "numeroMatricula": null,
  "nomeCompleto": null,
  "placaVeiculo": "' . $placa . '",
  "numeroFrota": null,
  "codigoTipoFrota": null,
  "codigoFamiliaVeiculo": null,
  "codigoVeiculoModelo": null,
  "codigoCidade": null,
  "codigoClienteTCVeiculoResponsavel": null,
  "nomeResponsavel": null,
  "codigoGrupo": 4,
  "codigoTipoCombustivel": null,
  "informacoesAdicionais": [
    {
      "labelIA": null,
      "codigoIA": null,
      "descricaoIA": null
    }
  ],
  "situacaoCartao": "T",
  "ordem": "C",
  "quebra": null,
  "tipoMotorista": null,
  "ativacao": null,
  "tipoCartao": null,
  "controlaHodometro": null,
  "controlaHorimetro": null,
  "situacaoVeiculo": null,
  "trackOnLine": null
}';

      curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

      $result = json_decode(curl_exec($curl));

      if (count($result->itens)) {

        foreach ($result->itens as $itens) {


          $situacao = $itens->situacao;

          if ($situacao == 'A') {
            $numeroCartao = $itens->numeroCartao;
            $saldocartao = $itens->saldo;
            echo "Cartão $numeroCartao - Saldo $saldocartao";
          }


        }
      }

      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/usuarioCartaoLimite');

      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
      curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Basic W09wZXJhZG9yV2ViXWFwcDE4MzA2MjQyNDc6RFhYSm5WejA=',
      ));
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_HEADER, 0);

      $body = '{
  "codigoCliente": 183062,
  "codigoProduto": 4,
  "tipoAlteracao": "AR",
  "tipoLimite": "AS",
  "tipoOperacao": "SP",
  "cartoes": [
    {
      "numeroCartao": "' . $numeroCartao . '",
      "valorLimite": ' . $valor . ',
      "valorLimiteProxPeriodo": null,
      "mensagem": "Controle Combustivel"
    }
  ]
}';

      curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

      $result = json_decode(curl_exec($curl));
    }
    echo "$body";
    $acao = 'Aceitou cota extra';
    $flag = '2';
    $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $valor_ant, $valor);

    echo "<script language='javascript' type='text/javascript'>alert('Pedido Aceito!');window.location=\"../aprovacaocotafrotas.php\"</script>";

  } else {


    $sql = "UPDATE bdfrota.tbpedidoscoordcota SET flag = $decisao, tipocota = '$tipocota', dataplantao = '$dataplantao' where idtbpedidoscoordcota = $id";
    $resultado = mysql_query($sql) or die(mysql_error());

    $sql = "UPDATE bdfrota.tbcoord SET saldo = saldo + $valor, orcrecebido = orcrecebido + $valor where matricula = '$matriculaf'";
    $resultado = mysql_query($sql) or die(mysql_error());

    $sql = "SELECT idtborcamentomensal FROM bdfrota.tborcamentomensal order by data desc";
    $resultado = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_array($resultado);
    $idorcmen = $row[0];

    $sql = "UPDATE bdfrota.tborcamentomensal SET cotafrota = cotafrota + $valor where idtborcamentomensal = '$idorcmen'";
    $resultado = mysql_query($sql) or die(mysql_error());

    $sql = "SELECT ds_placa FROM bdfrota.tbcarrosxcolaborador where id_colaborador = '$matriculaf'";
    $resultado = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_array($resultado);
    $placa = $row[0];


    $sql = "SELECT unidade FROM bdfrota.tbveiculo where matcond = '$matriculaf'";
    $resultado = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_array($resultado);
    $unidade = $row[0];

    if ($unidade != 'RJ' && $unidade != "") {

      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, 'https://kissapi.valecard.com.br/core/services/frotaCliente/alterarLimiteveiculo');
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_POST, TRUE);
      curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Basic NTI0MDczQHdlYlNlcnZpY2U6MDY5NDc=',
      ));
      curl_setopt($curl, CURLOPT_HEADER, 0);


      $body = '{
"contratoCliente" : "524073",
"veiculoId" : null,
"placa" : "' . $placa . '",
"numeroCartao" : null,
"valorAlteracao" : "' . $valor . '",
"tipoAlteracao" : "adicionar",
"periodo" : "atual",
"motivo" : "Sistema Combustivel",
"modulo" : "abastecimento"
}';

      curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

      $result = json_decode(curl_exec($curl));

    } else {

      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/relatorioExtratoSimplificado/search');
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_POST, TRUE);
      curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Basic W09wZXJhZG9yV2ViXWFwcDE4MzA2MjQyNDc6RFhYSm5WejA=',
      ));
      curl_setopt($curl, CURLOPT_HEADER, 0);


      $body = '{
  "codigoCliente": 183062,
  "codigoProduto": 4,
  "numeroCartao": null,
  "especieCartao": null,
  "numeroMatricula": null,
  "nomeCompleto": null,
  "placaVeiculo": "' . $placa . '",
  "numeroFrota": null,
  "codigoTipoFrota": null,
  "codigoFamiliaVeiculo": null,
  "codigoVeiculoModelo": null,
  "codigoCidade": null,
  "codigoClienteTCVeiculoResponsavel": null,
  "nomeResponsavel": null,
  "codigoGrupo": 4,
  "codigoTipoCombustivel": null,
  "informacoesAdicionais": [
    {
      "labelIA": null,
      "codigoIA": null,
      "descricaoIA": null
    }
  ],
  "situacaoCartao": "T",
  "ordem": "C",
  "quebra": null,
  "tipoMotorista": null,
  "ativacao": null,
  "tipoCartao": null,
  "controlaHodometro": null,
  "controlaHorimetro": null,
  "situacaoVeiculo": null,
  "trackOnLine": null
}';

      curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

      $result = json_decode(curl_exec($curl));

      if (count($result->itens)) {

        foreach ($result->itens as $itens) {

          $situacao = $itens->situacao;

          if ($situacao == 'A') {
            $numeroCartao = $itens->numeroCartao;
            $saldocartao = $itens->saldo;
          }

        }
      }

      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/usuarioCartaoLimite');

      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
      curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Basic W09wZXJhZG9yV2ViXWFwcDE4MzA2MjQyNDc6RFhYSm5WejA=',
      ));
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_HEADER, 0);

      $body = '{
  "codigoCliente": 183062,
  "codigoProduto": 4,
  "tipoAlteracao": "AR",
  "tipoLimite": "AS",
  "tipoOperacao": "SP",
  "cartoes": [
    {
      "numeroCartao": "' . $numeroCartao . '",
      "valorLimite": ' . $valor . ',
      "valorLimiteProxPeriodo": null,
      "mensagem": "Controle Combustivel"
    }
  ]
}';

      curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

      $result = json_decode(curl_exec($curl));
    }
    $acao = 'Adicionou Cota Extra';
    $flag = '2';
    $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $valor_ant, $valor);

    echo "<script language='javascript' type='text/javascript'>alert('Cota Adicionada');window.location=\"../aprovacaocotafrotas.php\"</script>";

  }

} else {
  $sql = "UPDATE bdfrota.tbpedidoscoordcota SET flag = $decisao, tipocota = '$tipocota', dataplantao = '$dataplantao' where idtbpedidoscoordcota = $id";
  $resultado = mysql_query($sql) or die(mysql_error());
  $acao = 'Não aceitou cota extra';
  $flag = '2';
  $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $valor_ant, $valor);

  echo "<script language='javascript' type='text/javascript'>alert('Pedido não Aceito!');window.location=\"../aprovacaocotafrotas.php\"</script>";
}






?>