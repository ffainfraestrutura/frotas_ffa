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

$desc = 'nao';
$matriculaf = $_POST['matriculatec'];
$id = $_POST['id'];
$idtbsaldo = $_POST['idtbsaldo'];
$decisao = $_POST['decisao'];
$valor = $_POST["valor"];
$valor = str_replace(",", ".", $valor);
$valor_ant = $_POST["valor_ant"];
$valor_ant = str_replace(",", ".", $valor_ant);
if ($valor_ant >= 40) {

    echo "<script language='javascript' type='text/javascript'>alert('Colaborador possui saldo no cartão, não é possível inserir saldo solicitado');window.location=\"../aprovacaocota.php\"</script>";
    die();

}
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
$justsup = $_POST["justificativa"];

$justsup = str_replace("'", "", $justsup);
$desc = $_POST["descontocombustivel"];
$valdesc = $_POST["valdescontocombustivel"];
$valdesc = str_replace(",", ".", $valdesc);
$foraescala = $_POST["foraescala"];
if ($foraescala == 'Sim') {
    $foraescala = '1';
} else {
    $foraescala = '0';
}
$forahorario = $_POST["forahorario"];
if ($forahorario == 'Sim') {
    $forahorario = '1';
} else {
    $forahorario = '0';
}
if ($valor == '') {
    $valor = $orcpedido;
}
if ($kmproj > $kmos or $justsup <> '') {
    $cota_validade = '1';
} else {
    $cota_validade = '0';
}
$kmprojini = $_POST["kmprojini"];
$orcsematu = $_POST["orcsematu"];
$orcsematu = str_replace(",", ".", $orcsematu);
if ($orcsematu == 0 or $orcsematu == '') {
    $orcsematu = $orcsem + $totextra;
}


if ($desc == 'nao') {

    $sql = "UPDATE bdfrota.tbpedidostec SET valsup = '$valor', justsup = '$justsup' where idtbpedidostec = $id";
    $resultado = mysql_query($sql) or die(mysql_error());

    echo "<script language='javascript' type='text/javascript'>window.location=\"./escalonarpedidoind.php?idpedido=$id\"</script>";
    exit();
}


$sql = "SELECT valor FROM bdfrota.tbsupervisor where matricula = '$matricula'";
$resultado = mysql_query($sql) or die(mysql_error());
$row = mysql_fetch_array($resultado);
$saldosup = $row[0];

if ($valor > $saldosup) {

    echo "<script language='javascript' type='text/javascript'>alert('Saldo Insuficiente!');window.location=\"../aprovacaocota.php\"</script>";

}

if ($desc == 'sim') {

    if ($valdesc < $valor) {

        //aplicar o dinheiro da diferença e depois fazer o documento de desconto
        $valorainserir = $valor - $valordesc;

        $sql = "INSERT INTO bdfrota.tbcotaextraindevida (matricula, valdesc, idpedidotec, matsup, parcial) SELECT matricula, '$valdesc', '$id', '$matricula', '1' FROM bdfrota.tbpedidostec WHERE idtbpedidostec = $id";
        $resultado = mysql_query($sql) or die(mysql_error());

        $sql = "UPDATE bdfrota.tbpedidostec SET desctec = '1' where idtbpedidostec = $id";
        $resultado = mysql_query($sql) or die(mysql_error());

        if ($orcpedido == $valorainserir) {

            $sql = "UPDATE bdfrota.tbpedidostec SET flag = $decisao, tipocota = '$tipocota', dataplantao = '$dataplantao', valorinserido = $valorainserir where idtbpedidostec = $id";
            $resultado = mysql_query($sql) or die(mysql_error());

            $sql = "INSERT INTO bdfrota.tbcotaextraac (matricula, matsup, valor, justificativa, data, flag, dataaceite, dir, valorinserido, kmhodometro, orcsemanal, kmproj, orcsematual, kmprojatual, kmos, gps, valordescontado, sldcartao, totalextra, cota_validade, descontar, valdesc, justsup, idtbpedidostec, forahorario, foraescala) SELECT matricula, '$matricula', valor, justificativa, data, '$decisao', '$hoje', dir, valorinserido, kmhodometro, '$orcsem', '$kmprojini', '$orcsematu', '$kmproj', '$kmos', '$kmsem', '$valorainserir_desc', '$valorainserir_ant', '$totextra', '$cota_validade', 'desc', '$valdesc', '$justsup', '$idtbpedidostec', '$forahorario', '$foraescala' FROM bdfrota.tbpedidostec WHERE idtbpedidostec = $id";
            $resultado = mysql_query($sql) or die(mysql_error());

            $sql = "UPDATE bdfrota.tbsaldo SET saldo = saldo + $valorainserir, valoraplicado = valoraplicado + $valorainserir, totalextra = totalextra + $valorainserir where idtbsaldo = '$idtbsaldo'";
            $resultado = mysql_query($sql) or die(mysql_error());

            $sql = "UPDATE bdfrota.tbsaldo SET kmproj = ROUND(( kmorcsem / orcsemanal ) * valoraplicado, 2) where idtbsaldo = '$idtbsaldo'";
            $resultado = mysql_query($sql) or die(mysql_error());

            $sql = "UPDATE bdfrota.tbsupervisor SET valor = valor - $valorainserir where matricula = '$matricula'";
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
                    'Accept-Encoding: gzip, deflate, br',
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
                            if ($saldocartao >= 40) {

                                echo "<script language='javascript' type='text/javascript'>alert('Colaborador possui saldo no cartão, não é possível inserir saldo solicitado');window.location=\"../aprovacaocota.php\"</script>";

                            }
                        }

                    }
                }

                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/usuarioCartaoLimite');

                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Accept-Charset: utf-8',
                    'Accept-Encoding: gzip',
                    'Connection: keep-alive',
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
            $acao = 'Aceitou cota extra';
            $flag = '2';
            $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $saldocartao, $valor);

            echo "<script language='javascript' type='text/javascript'>alert('Pedido Aceito!');window.location=\"../aprovacaocota.php\"</script>";




        } else {


            $sql = "UPDATE bdfrota.tbpedidostec SET flag = $decisao, tipocota = '$tipocota', dataplantao = '$dataplantao', valorinserido = $valorainserir where idtbpedidostec = $id";
            $resultado = mysql_query($sql) or die(mysql_error());

            $sql = "INSERT INTO bdfrota.tbcotaextraac (matricula, matsup, valor, justificativa, data, flag, dataaceite, dir, valorinserido, kmhodometro, orcsemanal, kmproj, orcsematual, kmprojatual, kmos, gps, valordescontado, sldcartao, totalextra, cota_validade, descontar, valdesc, justsup, idtbpedidostec, forahorario, foraescala) SELECT matricula, '$matricula', valor, justificativa, data, '$decisao', '$hoje', dir, valorinserido, kmhodometro, '$orcsem', '$kmprojini', '$orcsematu', '$kmproj', '$kmos', '$kmsem', '$valorainserir_desc', '$valorainserir_ant', '$totextra', '$cota_validade', 'desc', '$valdesc', '$justsup', '$idtbpedidostec', '$forahorario', '$foraescala' FROM bdfrota.tbpedidostec WHERE idtbpedidostec = $id";
            $resultado = mysql_query($sql) or die(mysql_error());

            $sql = "UPDATE bdfrota.tbsaldo SET saldo = saldo + $valorainserir, valoraplicado = valoraplicado + $valorainserir, totalextra = totalextra + $valorainserir where idtbsaldo = '$idtbsaldo'";
            $resultado = mysql_query($sql) or die(mysql_error());

            $sql = "UPDATE bdfrota.tbsaldo SET kmproj = ROUND(( kmorcsem / orcsemanal ) * valoraplicado, 2) where idtbsaldo = '$idtbsaldo'";
            $resultado = mysql_query($sql) or die(mysql_error());

            $sql = "UPDATE bdfrota.tbsupervisor SET valor = valor - $valorainserir where matricula = '$matricula'";
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
                    'Accept-Encoding: gzip, deflate, br',
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
                            if ($saldocartao >= 40) {

                                echo "<script language='javascript' type='text/javascript'>alert('Colaborador possui saldo no cartão, não é possível inserir saldo solicitado');window.location=\"../aprovacaocota.php\"</script>";

                            }
                        }

                    }
                }

                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/usuarioCartaoLimite');

                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Accept-Charset: utf-8',
                    'Accept-Encoding: gzip',
                    'Connection: keep-alive',
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


        }

        $acao = 'cota parcial';
        $flag = '2';
        $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $saldocartao, $valorainserir);


        $acao = 'desconto parcial gerado';
        $flag = '2';
        $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $saldocartao, $valdesc);

        echo "<script language='javascript' type='text/javascript'>alert('Cota de $valorainserir inserida e desconto de $valdesc gerado');window.location=\"../aprovacaocota.php\"</script>";


    } else {



        $sql = "INSERT INTO bdfrota.tbcotaextraindevida (matricula, valdesc, idpedidotec, matsup, parcial) SELECT matricula, '$valdesc', '$id', '$matricula', '0' FROM bdfrota.tbpedidostec WHERE idtbpedidostec = $id";
        $resultado = mysql_query($sql) or die(mysql_error());

        $sql = "INSERT INTO bdfrota.tbcotaextraac (matricula, matsup, valor, justificativa, data, flag, dataaceite, dir, valorinserido, kmhodometro, orcsemanal, kmproj, orcsematual, kmprojatual, kmos, gps, valordescontado, sldcartao, totalextra, cota_validade, descontar, valdesc, justsup, idtbpedidostec) SELECT matricula, '$matricula', valor, justificativa, data, '$decisao', '$hoje', dir, valorinserido, kmhodometro, '$orcsem', '$kmprojini', '$orcsematu', '$kmproj', '$kmos', '$kmsem', '0', '$valorainserir_ant', '$totextra', '$cota_validade', 'desc', '$valdesc', '$justsup', '$idtbpedidostec' FROM bdfrota.tbpedidostec WHERE idtbpedidostec = $id";
        $resultado = mysql_query($sql) or die(mysql_error());

        $sql = "UPDATE bdfrota.tbpedidostec SET desctec = '1' where idtbpedidostec = $id";
        $resultado = mysql_query($sql) or die(mysql_error());

        $acao = 'desconto gerado';
        $flag = '2';
        $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $saldocartao, $valdesc);

        echo "<script language='javascript' type='text/javascript'>alert('Desconto gerado para o tecnico');window.location=\"../aprovacaocota.php\"</script>";
    }

} else {

    //inserir o saldo no cartão


    echo "<script language='javascript' type='text/javascript'>alert(erro na justificatica');window.location=\"../aprovacaocota.php\"</script>";




    if ($orcpedido == $valor) {
        if ($valor > $saldosup) {

            echo "<script language='javascript' type='text/javascript'>alert('Saldo Insuficiente!');window.location=\"../aprovacaocota.php\"</script>";

        } else {

            $sql = "UPDATE bdfrota.tbpedidostec SET flag = $decisao, tipocota = '$tipocota', dataplantao = '$dataplantao', valorinserido = $valor where idtbpedidostec = $id";
            $resultado = mysql_query($sql) or die(mysql_error());

            $sql = "INSERT INTO bdfrota.tbcotaextraac (matricula, matsup, valor, justificativa, data, flag, dataaceite, dir, valorinserido, kmhodometro, orcsemanal, kmproj, orcsematual, kmprojatual, kmos, gps, valordescontado, sldcartao, totalextra, cota_validade, descontar, valdesc, justsup, idtbpedidostec, forahorario, foraescala) SELECT matricula, '$matricula', valor, justificativa, data, '$decisao', '$hoje', dir, valorinserido, kmhodometro, '$orcsem', '$kmprojini', '$orcsematu', '$kmproj', '$kmos', '$kmsem', '$valorainserir_desc', '$valorainserir_ant', '$totextra', '$cota_validade', 'desc', '$valdesc', '$justsup', '$idtbpedidostec', '$forahorario', '$foraescala' FROM bdfrota.tbpedidostec WHERE idtbpedidostec = $id";
            //echo "$sql <br>";

            $resultado = mysql_query($sql) or die(mysql_error());

            $sql = "UPDATE bdfrota.tbsaldo SET saldo = saldo + $valor, valoraplicado = valoraplicado + $valor, totalextra = totalextra + $valor where idtbsaldo = '$idtbsaldo'";
            $resultado = mysql_query($sql) or die(mysql_error());

            $sql = "UPDATE bdfrota.tbsaldo SET kmproj = ROUND(( kmorcsem / orcsemanal ) * valoraplicado, 2) where idtbsaldo = '$idtbsaldo'";
            $resultado = mysql_query($sql) or die(mysql_error());

            $sql = "UPDATE bdfrota.tbsupervisor SET valor = valor - $valor where matricula = '$matricula'";
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
                    'Accept-Encoding: gzip, deflate, br',
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
                            if ($saldocartao >= 40) {

                                echo "<script language='javascript' type='text/javascript'>alert('Colaborador possui saldo no cartão, não é possível inserir saldo solicitado');window.location=\"../aprovacaocota.php\"</script>";

                            }
                        }

                    }
                }

                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/usuarioCartaoLimite');

                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Accept-Charset: utf-8',
                    'Accept-Encoding: gzip',
                    'Connection: keep-alive',
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

            $acao = 'Aceitou cota extra';
            $flag = '2';
            $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $saldocartao, $valor);

            echo "<script language='javascript' type='text/javascript'>alert('Pedido Aceito!');window.location=\"../aprovacaocota.php\"</script>";
        }
    } else {

        if ($valor > $saldosup) {

            echo "<script language='javascript' type='text/javascript'>alert('Saldo Insuficiente!');window.location=\"../aprovacaocota.php\"</script>";

        } else {
            $sql = "UPDATE bdfrota.tbpedidostec SET flag = $decisao, tipocota = '$tipocota', dataplantao = '$dataplantao', valorinserido = $valor where idtbpedidostec = $id";
            $resultado = mysql_query($sql) or die(mysql_error());

            $sql = "INSERT INTO bdfrota.tbcotaextraac (matricula, matsup, valor, justificativa, data, flag, dataaceite, dir, valorinserido, kmhodometro, orcsemanal, kmproj, orcsematual, kmprojatual, kmos, gps, valordescontado, sldcartao, totalextra, cota_validade, descontar, valdesc, justsup, idtbpedidostec, forahorario, foraescala) SELECT matricula, '$matricula', valor, justificativa, data, '$decisao', '$hoje', dir, valorinserido, kmhodometro, '$orcsem', '$kmprojini', '$orcsematu', '$kmproj', '$kmos', '$kmsem', '$valorainserir_desc', '$valorainserir_ant', '$totextra', '$cota_validade', 'desc', '$valdesc', '$justsup', '$idtbpedidostec', '$forahorario', '$foraescala' FROM bdfrota.tbpedidostec WHERE idtbpedidostec = $id";
            $resultado = mysql_query($sql) or die(mysql_error());

            $sql = "UPDATE bdfrota.tbsaldo SET saldo = saldo + $valor, valoraplicado = valoraplicado + $valor, totalextra = totalextra + $valor where idtbsaldo = '$idtbsaldo'";
            $resultado = mysql_query($sql) or die(mysql_error());

            $sql = "UPDATE bdfrota.tbsaldo SET kmproj = ROUND(( kmorcsem / orcsemanal ) * valoraplicado, 2) where idtbsaldo = '$idtbsaldo'";
            $resultado = mysql_query($sql) or die(mysql_error());

            $sql = "UPDATE bdfrota.tbsupervisor SET valor = valor - $valor where matricula = '$matricula'";
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
                    'Accept-Encoding: gzip, deflate, br',
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
                            if ($saldocartao >= 40) {

                                echo "<script language='javascript' type='text/javascript'>alert('Colaborador possui saldo no cartão, não é possível inserir saldo solicitado');window.location=\"../aprovacaocota.php\"</script>";

                            }
                        }

                    }
                }

                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/usuarioCartaoLimite');

                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Accept-Charset: utf-8',
                    'Accept-Encoding: gzip',
                    'Connection: keep-alive',
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

            $acao = 'Adicionou Cota';
            $flag = '2';
            $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $saldocartao, $valor);

            echo "<script language='javascript' type='text/javascript'>alert('Cota Adicionada');window.location=\"../aprovacaocota.php\"</script>";
        }
    }





}






?>