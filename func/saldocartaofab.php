<?php

include_once '../conecta.php';

function buscarCartaoTicketLog($placa)
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/relatorioExtratoSimplificado/search',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Basic W09wZXJhZG9yV2ViXWFwcDE4MzA2MjQyNDc6RFhYSm5WejA='
        ]
    ]);

    $body = json_encode([
        "codigoCliente" => 183062,
        "codigoProduto" => 4,
        "placaVeiculo" => $placa,
        "situacaoCartao" => "T",
        "codigoGrupo" => 4,
        "ordem" => "C"
    ]);

    curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

    $response = curl_exec($curl);
    curl_close($curl);

    $result = json_decode($response);

    if (!empty($result->itens)) {
        foreach ($result->itens as $item) {
            if ($item->situacao == 'A') {
                return $item->numeroCartao;
            }
        }
    }

    return false;
}

function inserirSaldoTicketLog($placa, $valor)
{
    $numeroCartao = buscarCartaoTicketLog($placa);

    if (!$numeroCartao) {
        return false;
    }

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/usuarioCartaoLimite',
        CURLOPT_CUSTOMREQUEST => "PUT",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Basic W09wZXJhZG9yV2ViXWFwcDE4MzA2MjQyNDc6RFhYSm5WejA='
        ]
    ]);

    $body = json_encode([
        "codigoCliente" => 183062,
        "codigoProduto" => 4,
        "tipoAlteracao" => "AR",
        "tipoLimite" => "AS",
        "tipoOperacao" => "SP",
        "cartoes" => [
            [
                "numeroCartao" => $numeroCartao,
                "valorLimite" => $valor,
                "valorLimiteProxPeriodo" => null,
                "mensagem" => "Controle Combustivel"
            ]
        ]
    ]);

    curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if (curl_errno($curl) || $httpCode != 200) {
        curl_close($curl);
        return false;
    }

    curl_close($curl);
    return true;
}

function removerSaldoTicketLog($placa, $valor)
{
    $numeroCartao = buscarCartaoTicketLog($placa);

    if (!$numeroCartao) {
        return false;
    }

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/usuarioCartaoLimite',
        CURLOPT_CUSTOMREQUEST => "PUT",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Basic W09wZXJhZG9yV2ViXWFwcDE4MzA2MjQyNDc6RFhYSm5WejA='
        ]
    ]);

    $body = json_encode([
        "codigoCliente" => 183062,
        "codigoProduto" => 4,
        "tipoAlteracao" => "DR",
        "tipoLimite" => "AS",
        "tipoOperacao" => "SP",
        "cartoes" => [
            [
                "numeroCartao" => $numeroCartao,
                "valorLimite" => $valor,
                "valorLimiteProxPeriodo" => null,
                "mensagem" => "Controle Combustivel"
            ]
        ]
    ]);

    curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if (curl_errno($curl) || $httpCode != 200) {
        curl_close($curl);
        return false;
    }

    curl_close($curl);
    return true;
}

function inserirSaldoValeCard($placa, $valor)
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://kissapi.valecard.com.br/core/services/frotaCliente/alterarLimiteveiculo',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Basic NTI0MDczQHdlYlNlcnZpY2U6MDY5NDc='
        ]
    ]);

    $body = json_encode([
        "contratoCliente" => "524073",
        "veiculoId" => null,
        "placa" => $placa,
        "numeroCartao" => null,
        "valorAlteracao" => $valor,
        "tipoAlteracao" => "adicionar",
        "periodo" => "atual",
        "motivo" => "Sistema Combustivel",
        "modulo" => "abastecimento"
    ]);

    curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if (curl_errno($curl) || $httpCode != 200) {
        curl_close($curl);
        return false;
    }

    curl_close($curl);
    return true;
}
function removerSaldoValeCard($placa, $valor)
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://kissapi.valecard.com.br/core/services/frotaCliente/alterarLimiteveiculo',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Basic NTI0MDczQHdlYlNlcnZpY2U6MDY5NDc='
        ]
    ]);

    $body = json_encode([
        "contratoCliente" => "524073",
        "veiculoId" => null,
        "placa" => $placa,
        "numeroCartao" => null,
        "valorAlteracao" => $valor,
        "tipoAlteracao" => "diminuir",
        "periodo" => "atual",
        "motivo" => "Sistema Combustivel",
        "modulo" => "abastecimento"
    ]);

    curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if (curl_errno($curl) || $httpCode != 200) {
        curl_close($curl);
        return false;
    }

    curl_close($curl);
    return true;
}

function inserirSaldo($codemptec, $placa, $valor)
{
    if ($codemptec == 2) {
        return inserirSaldoValeCard($placa, $valor);
    } else {
        return inserirSaldoTicketLog($placa, $valor);
    }
}

function removerSaldo($codemptec, $placa, $valor)
{
    if ($codemptec == 2) {
        return removerSaldoValeCard($placa, $valor);
    } else {
        return removerSaldoTicketLog($placa, $valor);
    }
}
?>