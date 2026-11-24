<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

$hoje = date('Y-m-d H:i:s');

$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

include"../conecta.php";
include_once"../func/log.php";
header("Content-type: text/html; charset=utf-8");

$matriculatec = $_POST['matriculatec'];

$saldoatual = $_POST['saldoatual'];//saldo atual no cartão
$saldoatual = str_replace(",", ".", $saldoatual);

$saldosup = $_POST['saldosup'];//saldo do supervisor
$saldosup = str_replace(",", ".", $saldosup);

$valor = $_POST['valor'];//valor retirado
$valor = str_replace(",", ".", $valor);

$idtbsaldo = $_POST['idtbsaldo'];//onde editar na tbsaldo
$codemptec = $_POST['codemptec'];//se é SP ou outro
$placa = $_POST['placa'];
$idtbsupervisor = $_POST['idtbsupervisor'];

//print "$matriculatec $saldoatual $saldosup $valor $idtbsaldo $codemptec $placa $idtbsupervisor";

if($valor<0){//retirar do tecnico
    
    $valor = abs($valor);
    //print "$valor, $saldoatual";

    if ($valor > $saldoatual) {
                
        echo"<script language='javascript' type='text/javascript'>alert('Saldo Insuficiente!');window.location=\"../remanejamento-final.php\"</script>";
    
    }else{        
    
        $sql = "SELECT valoraplicado, kmproj FROM bdfrota.tbsaldo WHERE idtbsaldo = '$idtbsaldo'";
        $resultado = mysql_query($sql) or die (mysql_error());
        $row = mysql_fetch_array($resultado);
        $valoraplicado = $row[0];
        $kmproj = $row[1];
        $kmproj = round($kmproj, 2);
    
        $novovaloraplic = $valoraplicado - $valor;
        $novokmproj = ($novovaloraplic*$kmproj)/$valoraplicado;
        $novokmproj = round($novokmproj, 2);
    
        $sql = "SELECT valor, totorccomp  FROM bdfrota.tbsupervisor WHERE idtbsupervisor='$idtbsupervisor';";
        $resultado = mysql_query($sql) or die (mysql_error());
        $row = mysql_fetch_array($resultado);
        $valorsuperv = $row[0];
        $totorccomp = $row[1];
    
        $novosaldosup = $valorsuperv + $valor;
        $novototorccomp = $totorccomp + $valor;
    
        //print "$novosaldosup, $valorsuperv, $valor, $totorccomp, $novototorccomp";
    
        $sql = "UPDATE bdfrota.tbsaldo SET valoraplicado = $novovaloraplic where idtbsaldo = '$idtbsaldo'";
       $resultado = mysql_query($sql) or die (mysql_error());
    
        $sql = "UPDATE bdfrota.tbsaldo SET kmproj = $novokmproj where idtbsaldo = '$idtbsaldo'";
        $resultado = mysql_query($sql) or die (mysql_error());
    
        $sql = "UPDATE bdfrota.tbsupervisor SET valor = $novosaldosup, totorccomp = $novototorccomp where idtbsupervisor='$idtbsupervisor'";
        $resultado = mysql_query($sql) or die (mysql_error());

        $sql2="INSERT INTO bdfrota.tbremanejamento(data_e_hora, matricula, matr_autor, valor, perfil_autor, tipo) VALUES ('$hoje', '$matriculatec', '$matricula', '$valor', 1,0);";
        $resultado2 = mysql_query($sql2) or die (mysql_error());

        if ($codemptec == '2') {

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
                "placa" : "'.$placa.'",
                "numeroCartao" : null,
                "valorAlteracao" : "'.$valor.'",
                "tipoAlteracao" : "diminuir",
                "periodo" : "atual",
                "motivo" : "Sistema Combustivel",
                "modulo" : "abastecimento"
            }';
    
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
    
            $result = json_decode(curl_exec($curl));
                /*print $codemptec;
                print_r($result);*/
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
                "placaVeiculo": "'.$placa.'",
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
                
                            echo"<script language='javascript' type='text/javascript'>alert('Colaborador possui saldo no cartão, não é possível inserir saldo solicitado');window.location=\"../aprovacaocota.php\"</script>";
    
                        }*/
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
                "tipoAlteracao": "DR",
                "tipoLimite": "AS",
                "tipoOperacao": "SP",
                "cartoes": [
                    {
                    "numeroCartao": "'.$numeroCartao.'",
                    "valorLimite": '.$valor.',
                    "valorLimiteProxPeriodo": null,
                    "mensagem": "Controle Combustivel"
                    }
                ]
            }';
    
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
    
            $result = json_decode(curl_exec($curl));
        }
        
        $acao = 'Remanejou cota - retirou';
        $flag = '2';
        $logresult = enviarlog($hoje, $acao, $flag,  $matriculatec, $matricula, $saldoatual, $valor); 
    
        echo"<script language='javascript' type='text/javascript'>alert('Remanejamento feito com sucesso!');window.location=\"../remanejamento-final.php\"</script>";
    }

    
}else{//adicionar ao técnico
    if ($valor > $saldosup) {
            
        echo"<script language='javascript' type='text/javascript'>alert('Saldo Insuficiente!');window.location=\"../remanejamento-final.php\"</script>";
    
    }else{

        $sql = "SELECT valoraplicado, kmproj FROM bdfrota.tbsaldo WHERE idtbsaldo = '$idtbsaldo'";
        $resultado = mysql_query($sql) or die (mysql_error());
        $row = mysql_fetch_array($resultado);
        $valoraplicado = $row[0];
        $kmproj = $row[1];
        $kmproj = round($kmproj, 2);

        $novovaloraplic = $valoraplicado + $valor;
        $novokmproj = ($novovaloraplic*$kmproj)/$valoraplicado;
        $novokmproj = round($novokmproj, 2);

        $sql = "SELECT valor, totorccomp  FROM bdfrota.tbsupervisor WHERE idtbsupervisor='$idtbsupervisor';";
        $resultado = mysql_query($sql) or die (mysql_error());
        $row = mysql_fetch_array($resultado);
        $valorsuperv = $row[0];
        $totorccomp = $row[1];

        $novosaldosup = $valorsuperv - $valor;
        $novototorccomp = $totorccomp - $valor;

        //print "$novosaldosup, $valorsuperv, $valor, $totorccomp, $novototorccomp";

        $sql = "UPDATE bdfrota.tbsaldo SET valoraplicado = $novovaloraplic where idtbsaldo = '$idtbsaldo'";
        $resultado = mysql_query($sql) or die (mysql_error());

        $sql = "UPDATE bdfrota.tbsaldo SET kmproj = $novokmproj where idtbsaldo = '$idtbsaldo'";
        $resultado = mysql_query($sql) or die (mysql_error());

        $sql = "UPDATE bdfrota.tbsupervisor SET valor = $novosaldosup, totorccomp = $novototorccomp where idtbsupervisor='$idtbsupervisor'";
        $resultado = mysql_query($sql) or die (mysql_error());

        $sql2="INSERT INTO bdfrota.tbremanejamento(data_e_hora, matricula, matr_autor, valor, perfil_autor, tipo) VALUES ('$hoje', '$matriculatec', '$matricula', '$valor', 1,1);";
        $resultado2 = mysql_query($sql2) or die (mysql_error());

        if ($codemptec == '2') {

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
                "placa" : "'.$placa.'",
                "numeroCartao" : null,
                "valorAlteracao" : "'.$valor.'",
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
                "placaVeiculo": "'.$placa.'",
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
                
                            echo"<script language='javascript' type='text/javascript'>alert('Colaborador possui saldo no cartão, não é possível inserir saldo solicitado');window.location=\"../aprovacaocota.php\"</script>";
    
                        }*/
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
                    "numeroCartao": "'.$numeroCartao.'",
                    "valorLimite": '.$valor.',
                    "valorLimiteProxPeriodo": null,
                    "mensagem": "Controle Combustivel"
                    }
                ]
            }';
    
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
    
            $result = json_decode(curl_exec($curl));
        }

        $acao = 'Remanejou cota - adicionou';
        $flag = '2';
        $logresult = enviarlog($hoje, $acao, $flag, $matriculatec, $matricula, $saldoatual, $valor);

        echo"<script language='javascript' type='text/javascript'>alert('Remanejamento feito com sucesso!');window.location=\"../remanejamento-final.php\"</script>";
    }
}


?>