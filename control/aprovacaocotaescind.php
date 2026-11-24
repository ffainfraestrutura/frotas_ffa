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

$matriculaf = $_POST['matriculatec'];
$id = $_POST['id'];
$idtbsaldo = $_POST['idtbsaldo'];
$decisao = $_POST['decisao'];
$valor = $_POST["valor"];
$valor = str_replace(",", ".", $valor);
$valor_ant = $_POST["valor_ant"];
$valor_ant = str_replace(",", ".", $valor_ant);
$cota_validade = '1';
$orcpedido = $_POST["orcpedido"];
$orcpedido = str_replace(",", ".", $orcpedido);
$tipocota = $_POST["tipocota"];
$valor_desc = $_POST["valor_desc"];
$valor_desc = str_replace(",", ".", $valor_desc);
$orcsem = $_POST["orcsem"];
$orcsem = str_replace(",", ".", $orcsem);
$kmproj = $_POST["kmproj"];
$kmproj = str_replace(",", ".", $kmproj);
$kmos = $_POST["kmos"];
$kmos = str_replace(",", ".", $kmos);
$kmsem = $_POST["kmsem"];
$kmsem = str_replace(",", ".", $kmsem);
$totextra = $_POST["totextra"];
$totextra = str_replace(",", ".", $totextra);
$dataplantao = $_POST["dataplantao"];
$justsup = $_POST["justsup"];

if ($valor == '') {
    $valor = $orcpedido;
}




$sql = "SELECT valor, matricula FROM bdfrota.tbsupervisor where idtbsupervisor in (SELECT idtbsupervisor FROM bdfrota.tbusuario where matricula = '$matriculaf')";
$resultado = mysql_query($sql) or die(mysql_error());
$row = mysql_fetch_array($resultado);
$saldosup = $row[0];
$matsup = $row[1];



if ($decisao == 2) {

    if ($orcpedido == $valor) {
        if ($valor > $saldosup) {

            echo "<script language='javascript' type='text/javascript'>alert('Saldo Insuficiente!');window.location=\"../aprovacaocotaesc.php\"</script>";

        } else {

            $sql = "UPDATE bdfrota.tbpedidostec SET flag = $decisao, tipocota = '$tipocota', dataplantao = '$dataplantao', valorinserido = $valor where idtbpedidostec = $id";
            $resultado = mysql_query($sql) or die(mysql_error());

            $sql = "INSERT INTO bdfrota.tbcotaextraac (matricula, matsup, valor, justificativa, data, flag, dataaceite, dir, valorinserido, kmhodometro, orcsemanal, kmproj, orcsematual, kmprojatual, kmos, gps, valordescontado, sldcartao, totalextra, cota_validade, descontar, valdesc, justsup, idtbpedidostec, forahorario, foraescala) SELECT matricula, '$matsup', valor, justificativa, data, '$decisao', '$hoje', dir, valorinserido, kmhodometro, '$orcsem', '$kmprojini', '$orcsematu', '$kmproj', '$kmos', '$kmsem', '$valorainserir_desc', '$valorainserir_ant', '$totextra', '$cota_validade', 'desc', '$valdesc', '$justsup', '$idtbpedidostec', '$forahorario', '$foraescala' FROM bdfrota.tbpedidostec WHERE idtbpedidostec = $id";
            //echo "$sql <br>";

            $resultado = mysql_query($sql) or die(mysql_error());

            $sql = "UPDATE bdfrota.tbsaldo SET saldo = saldo + $valor, valoraplicado = valoraplicado + $valor, totalextra = totalextra + $valor where idtbsaldo = '$idtbsaldo'";
            $resultado = mysql_query($sql) or die(mysql_error());

            $sql = "UPDATE bdfrota.tbsaldo SET kmproj = ROUND(( kmorcsem / orcsemanal ) * valoraplicado, 2) where idtbsaldo = '$idtbsaldo'";
            $resultado = mysql_query($sql) or die(mysql_error());

            $sql = "UPDATE bdfrota.tbsupervisor SET valor = valor - $valor where matricula = '$matsup'";
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
                            /*if ($saldocartao >= 40) {
    
    echo"<script language='javascript' type='text/javascript'>alert('Colaborador possui saldo no cartão, não é possível inserir saldo solicitado');window.location=\"../aprovacaocotaesc.php\"</script>";

}*/
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

            $acao = 'Aceitou cota extra Escalonada';
            $flag = '2';
            $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $saldocartao, $valor);

            echo "<script language='javascript' type='text/javascript'>alert('Pedido Aceito!');window.location=\"../aprovacaocotaesc.php\"</script>";
        }
    } else {

        if ($valor > $saldosup) {

            echo "<script language='javascript' type='text/javascript'>alert('Saldo Insuficiente!');window.location=\"../aprovacaocotaesc.php\"</script>";

        } else {
            $sql = "UPDATE bdfrota.tbpedidostec SET flag = $decisao, tipocota = '$tipocota', dataplantao = '$dataplantao', valorinserido = $valor where idtbpedidostec = $id";
            $resultado = mysql_query($sql) or die(mysql_error());

            $sql = "INSERT INTO bdfrota.tbcotaextraac (matricula, matsup, valor, justificativa, data, flag, dataaceite, dir, valorinserido, kmhodometro, orcsemanal, kmproj, orcsematual, kmprojatual, kmos, gps, valordescontado, sldcartao, totalextra, cota_validade, descontar, valdesc, justsup, idtbpedidostec, forahorario, foraescala) SELECT matricula, '$matsup', valor, justificativa, data, '$decisao', '$hoje', dir, valorinserido, kmhodometro, '$orcsem', '$kmprojini', '$orcsematu', '$kmproj', '$kmos', '$kmsem', '$valorainserir_desc', '$valorainserir_ant', '$totextra', '$cota_validade', 'desc', '$valdesc', '$justsup', '$idtbpedidostec', '$forahorario', '$foraescala' FROM bdfrota.tbpedidostec WHERE idtbpedidostec = $id";
            //echo "$sql <br>";

            $resultado = mysql_query($sql) or die(mysql_error());

            $sql = "UPDATE bdfrota.tbsaldo SET saldo = saldo + $valor, valoraplicado = valoraplicado + $valor, totalextra = totalextra + $valor where idtbsaldo = '$idtbsaldo'";
            $resultado = mysql_query($sql) or die(mysql_error());

            $sql = "UPDATE bdfrota.tbsaldo SET kmproj = ROUND(( kmorcsem / orcsemanal ) * valoraplicado, 2) where idtbsaldo = '$idtbsaldo'";
            $resultado = mysql_query($sql) or die(mysql_error());

            $sql = "UPDATE bdfrota.tbsupervisor SET valor = valor - $valor where matricula = '$matsup'";
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
                            /*if ($saldocartao >= 40) {
    
    echo"<script language='javascript' type='text/javascript'>alert('Colaborador possui saldo no cartão, não é possível inserir saldo solicitado');window.location=\"../aprovacaocotaesc.php\"</script>";

}*/
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



            $acao = 'Aceitou cota extra Escalonada';
            $flag = '2';
            $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $saldocartao, $valor);

            echo "<script language='javascript' type='text/javascript'>alert('Pedido Aceito!');window.location=\"../aprovacaocotaesc.php\"</script>";
        }
    }

} else {
    $sql = "UPDATE bdfrota.tbpedidostec SET flag = $decisao, tipocota = '$tipocota', dataplantao = '$dataplantao' where idtbpedidostec = $id";
    $resultado = mysql_query($sql) or die(mysql_error());
    $acao = 'Não aceitou cota extra escalonada';
    $flag = '2';
    $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $valor_ant, $valor);

    echo "<script language='javascript' type='text/javascript'>alert('Pedido não Aceito!');window.location=\"../aprovacaocotaesc.php\"</script>";
}






?>