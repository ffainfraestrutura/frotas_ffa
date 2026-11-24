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

$valor = $_POST['valor'];//valor retirado
$valor = str_replace(",", ".", $valor);

$idtbsaldo = $_POST['idtbsaldo'];//onde editar na tbsaldo
$unidade = $_POST['unidade'];//se é SP ou outro
$placa = $_POST['placa'];
$tipoacao = $_POST['tipoacao'];
$justificativa = isset($_POST['justificativa']) ? $_POST['justificativa'] : '';
// Escapa para evitar quebra de query (usa extensão mysql já usada no projeto)
$justificativa = function_exists('mysql_real_escape_string') ? mysql_real_escape_string($justificativa) : addslashes($justificativa);

//print $tipoacao;
//print "$matriculatec $saldoatual $saldosup $valor $idtbsaldo $codemptec $placa $idtbsupervisor";

//if($valor<0){//retirar do tecnico
if($tipoacao==0){
    
        
    
        $sql = "SELECT valoraplicado, kmproj, valrem, totalextra FROM bdfrota.tbsaldo WHERE idtbsaldo = '$idtbsaldo'";
        $resultado = mysql_query($sql) or die (mysql_error());
        $row = mysql_fetch_array($resultado);
        $valoraplicado = $row[0];
        $kmproj = $row[1];
        $kmproj = round($kmproj, 2);
        $valrem = $row[2];
        $totalextra = $row[3];
    
        $novovaloraplic = $valoraplicado - $valor;
        $novovalrem = $valrem - $valor;
        $novototalextra = $totalextra - $valor;
        /*$novokmproj = ($novovaloraplic*$kmproj)/$valoraplicado;
        $novokmproj = round($novokmproj, 2);*/
        if($kmproj !== 0){
            $novokmproj = ($novovaloraplic*$kmproj)/$valoraplicado;
            $novokmproj = round($novokmproj, 2);
        } 
    
        //print "$novosaldosup, $valorsuperv, $valor, $totorccomp, $novototorccomp";
        if ($idtbsaldo <> '') {
            $sql = "UPDATE bdfrota.tbsaldo SET valoraplicado = $novovaloraplic, kmproj = $novokmproj, valrem = $novovalrem, totalextra = $novototalextra where idtbsaldo = '$idtbsaldo'";
            $resultado = mysql_query($sql) or die (mysql_error());
        }

        $sql2="INSERT INTO bdfrota.tbremanejamento(data_e_hora, matricula, matr_autor, valor, perfil_autor, tipo, valor_anterior) VALUES ('$hoje', '$matriculatec', '$matricula', '$valor', 1,0, '$saldoatual');";
        $resultado2 = mysql_query($sql2) or die (mysql_error());

        // Salva justificativa na tabela de justificativas (tbjustificativa)
        if (trim($justificativa) !== '') {
            $sqlj = "INSERT INTO bdfrota.tbjustificativa (matricula_autor, data_hora, tipo_acao, justificativa, matricula_tecnico, placa, valor) VALUES ('{$matricula}', '$hoje', 0, '{$justificativa}', '{$matriculatec}', '{$placa}', {$valor});";
            $resj = mysql_query($sqlj) or die(mysql_error());
        }

        if ($unidade == 'SP' or $unidade == 'PR' or $unidade == 'MG') {

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
        
        echo"<script language='javascript' type='text/javascript'>alert('Remanejamento feito com sucesso!');window.location=\"../relsaldoveiculos.php\"</script>";
    

    
}elseif ($tipoacao == 1) {//adicionar ao técnico


        $sql = "SELECT valoraplicado, kmproj, totalextra FROM bdfrota.tbsaldo WHERE idtbsaldo = '$idtbsaldo'";

        $resultado = mysql_query($sql) or die (mysql_error());
        $row = mysql_fetch_array($resultado);
        $valoraplicado = $row[0];
        $kmproj = $row[1];
        $kmproj = round($kmproj, 2);
        $totalextra = $row[2];

        $novovaloraplic = $valoraplicado + $valor; 
        $novototalextra = $totalextra + $valor;        

        if($kmproj !== 0){
            $novokmproj = ($novovaloraplic*$kmproj)/$valoraplicado;
            $novokmproj = round($novokmproj, 2);
        }   

        //print "$novosaldosup, $valorsuperv, $valor, $totorccomp, $novototorccomp";
        if ($idtbsaldo <> '') {
            $sql = "UPDATE bdfrota.tbsaldo SET valoraplicado = $novovaloraplic, kmproj = $novokmproj, totalextra = $novototalextra where idtbsaldo = '$idtbsaldo'";
            $resultado = mysql_query($sql) or die (mysql_error());

        }

        $sql2="INSERT INTO bdfrota.tbremanejamento(data_e_hora, matricula, matr_autor, valor, perfil_autor, tipo, valor_anterior) VALUES ('$hoje', '$matriculatec', '$matricula', '$valor', 1,1, '$saldoatual');";
        $resultado2 = mysql_query($sql2) or die (mysql_error());

        // Salva justificativa na tabela de justificativas (tbjustificativa)
        if (trim($justificativa) !== '') {
            $sqlj = "INSERT INTO bdfrota.tbjustificativa (matricula_autor, data_hora, tipo_acao, justificativa, matricula_tecnico, placa, valor) VALUES ('{$matricula}', '$hoje', 1, '{$justificativa}', '{$matriculatec}', '{$placa}', {$valor});";
            $resj = mysql_query($sqlj) or die(mysql_error());
        }

        if ($unidade == 'SP' or $unidade == 'PR' or $unidade == 'MG') {

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

        //echo "$body <br>";

        echo"<script language='javascript' type='text/javascript'>alert('Remanejamento feito com sucesso!');window.location=\"../relsaldoveiculos.php\"</script>";
    
} else {
    echo"<script language='javascript' type='text/javascript'>alert('Nenhuma ação encontrada');window.location=\"../relsaldoveiculos.php\"</script>";
}


?>