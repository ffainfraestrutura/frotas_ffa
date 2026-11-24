<?php

include_once '../conecta.php';

function consultacartao($codemptec, $placa) //Função legada, retorna apenas o saldo, utilizar a função consultarCartoes
{
    $saldocartaov = "Sem cartão vinculado";

    if ($codemptec == 2) {
        // ValeCard
        $cartoes = consultarCartoesValecard($placa);

    } else {
        // TicketLog
        $cartoes = consultarCartoesTicketLog($placa);
    }

    if (!empty($cartoes[$placa])) {

        $saldo = $cartoes[$placa]['saldo'];

        if ($saldo !== null) {
            $saldo = str_replace(".", ",", $saldo);
            $saldocartaov = "R$ $saldo";
        }
    }

    return $saldocartaov;
}

function consultarCartoesTicketLog($placa = null, $numeroCartao = null)
{

    $cartoes = [];

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/relatorioExtratoSimplificado/search');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Basic W09wZXJhZG9yV2ViXWFwcDE4MzA2MjQyNDc6RFhYSm5WejA='
    ));

    $body = json_encode([
        "codigoCliente" => 183062,
        "codigoProduto" => 4,
        "numeroCartao" => $numeroCartao ?? null,
        "placaVeiculo" => $placa ?? null,
        "situacaoCartao" => "A",
        "ordem" => "P"
    ]);

    curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        die('Erro cURL: ' . curl_error($curl));
    }

    curl_close($curl);

    $result = json_decode($response);
    if (isset($result->itens) && is_array($result->itens)) {
        foreach ($result->itens as $itens) {

            $cartoes[$itens->placa] = [
                "limiteProximoPeriodo" => $itens->limiteProximoPeriodo,
                "placa" => $itens->placa,
                "saldo" => $itens->saldo,
                "numeroCartao" => $itens->numeroCartao
            ];
        }
    }

    return $cartoes;
}

function consultarCartoesValecard($placa = null, $numeroCartao = null)
{
    $cartoes = [];
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://kissapi.valecard.com.br/core/services/frotaCliente/buscarBasedeCartoes');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, TRUE);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Basic NTI0MDczQHdlYlNlcnZpY2U6MDY5NDc=',
    ));
    curl_setopt($curl, CURLOPT_HEADER, 0);
    $body = json_encode([
        "contratoCliente" => "524073",
        "numeroCartao" => $numeroCartao ?? null,
        "placa" => $placa ?? null,
        "statusCartao" => "A"
    ]);

    curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

    $result = json_decode(curl_exec($curl));
    //var_dump($result);
    if (is_array($result)) {
        foreach ($result as $itens) {

            $cartoes[$itens->placa] = [
                "limiteProximoPeriodo" => $itens->novoLimite,
                "placa" => $itens->placa,
                "saldo" => $itens->saldo,
                "numeroCartao" => $itens->numeroCartao
            ];
        }
    }

    return $cartoes;
}

function criarCurlValecard($placa = null, $numeroCartao = null)
{
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://kissapi.valecard.com.br/core/services/frotaCliente/buscarBasedeCartoes',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Basic NTI0MDczQHdlYlNlcnZpY2U6MDY5NDc='
        ],
        CURLOPT_POSTFIELDS => json_encode([
            "contratoCliente" => "524073",
            "numeroCartao" => $numeroCartao,
            "placa" => $placa,
            "statusCartao" => "A"
        ]),

        // 🔥 IMPORTANTE
        CURLOPT_CONNECTTIMEOUT => 25,   // tempo máximo pra conectar
        CURLOPT_TIMEOUT => 30,          // tempo máximo total
        CURLOPT_FAILONERROR => false
    ]);

    return $curl;
}

function criarCurlTicketLog($placa = null, $numeroCartao = null)
{
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/relatorioExtratoSimplificado/search',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Basic W09wZXJhZG9yV2ViXWFwcDE4MzA2MjQyNDc6RFhYSm5WejA='
        ],
        CURLOPT_POSTFIELDS => json_encode([
            "codigoCliente" => 183062,
            "codigoProduto" => 4,
            "placaVeiculo" => $placa,
            "numeroCartao" => $numeroCartao,
            "situacaoCartao" => "A",
            "ordem" => "C"
        ]),

        // 🔥 IMPORTANTE
        CURLOPT_CONNECTTIMEOUT => 25,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FAILONERROR => false
    ]);

    return $curl;
}

function processarRespostaValecard($response)
{
    $cartoes = [];
    $result = json_decode($response);

    if (is_array($result)) {
        foreach ($result as $item) {
            $placa = $item->placa;
            $placa = str_replace("-", "", $placa);
            $cartoes[$placa] = [
                "placa" => $item->placa,
                "operadora" => "Valecard",
                "saldo" => $item->saldo,
                "numeroCartao" => $item->numeroCartao,
                "limiteProximoPeriodo" => $item->novoLimite ?? null
            ];
        }
    }

    return $cartoes;
}

function processarRespostaTicketLog($response)
{
    $cartoes = [];
    $result = json_decode($response);
    if (isset($result->itens) && is_array($result->itens)) {

        foreach ($result->itens as $item) {
            $placa = $item->placa;
            $placa = str_replace("-", "", $placa);
            $cartoes[$placa] = [
                "placa" => $item->placa,
                "operadora" => "Ticketlog",
                "saldo" => $item->saldo,
                "numeroCartao" => $item->numeroCartao,
                "limiteProximoPeriodo" => $item->limiteProximoPeriodo ?? null,

            ];
        }
    }

    return $cartoes;
}

function consultarCartoes($placa = null, $numeroCartao = null, $codempresa = null)
{
    $mh = curl_multi_init();

    $curlValecard = null;
    $curlTicket = null;

    // VALCARD
    if ($codempresa == 2 || $codempresa == null) {
        $curlValecard = criarCurlValecard($placa, $numeroCartao);
        curl_multi_add_handle($mh, $curlValecard);
    }

    // TICKETLOG
    if ($codempresa == 1 || $codempresa == null) {
        $curlTicket = criarCurlTicketLog($placa, $numeroCartao);
        curl_multi_add_handle($mh, $curlTicket);
    }

    // Executa paralelo
    do {
        $status = curl_multi_exec($mh, $active);
        curl_multi_select($mh);
    } while ($active && $status == CURLM_OK);

    $cartoesValecard = [];
    $cartoesTicket = [];

    // Processa Valecard
    if ($curlValecard) {
        $responseValecard = curl_multi_getcontent($curlValecard);
        $cartoesValecard = processarRespostaValecard($responseValecard);
        curl_multi_remove_handle($mh, $curlValecard);
    }

    // Processa TicketLog
    if ($curlTicket) {
        $responseTicket = curl_multi_getcontent($curlTicket);
        $cartoesTicket = processarRespostaTicketLog($responseTicket);
        curl_multi_remove_handle($mh, $curlTicket);
    }

    curl_multi_close($mh);

    return [
        '1' => $cartoesTicket,
        '2' => $cartoesValecard
    ];
}

function consultarCartoesComDebug($placa = null, $numeroCartao = null, $codempresa = null)
{
    $diagnostico = [
        'operadora' => 'Desconhecida',
        'endpoint' => '',
        'http_code' => 0,
        'tempo_ms' => 0,
        'curl_errno' => 0,
        'curl_error' => '',
        'conectou' => false,
        'autorizado' => false,
        'qtd_itens' => 0,
        'retornou_dados' => false,
        'resposta_bruta_trecho' => ''
    ];

    if ((int) $codempresa !== 1 && (int) $codempresa !== 2) {
        $diagnostico['curl_error'] = 'Codigo de empresa invalido para consulta.';
        return $diagnostico;
    }

    if ((int) $codempresa === 2) {
        $diagnostico['operadora'] = 'Valecard';
        $diagnostico['endpoint'] = 'https://kissapi.valecard.com.br/core/services/frotaCliente/buscarBasedeCartoes';
        $curl = criarCurlValecard($placa, $numeroCartao);
    } else {
        $diagnostico['operadora'] = 'TicketLog';
        $diagnostico['endpoint'] = 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/relatorioExtratoSimplificado/search';
        $curl = criarCurlTicketLog($placa, $numeroCartao);
    }

    $response = curl_exec($curl);

    $diagnostico['curl_errno'] = curl_errno($curl);
    $diagnostico['curl_error'] = curl_error($curl);
    $diagnostico['http_code'] = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $diagnostico['tempo_ms'] = (int) round(((float) curl_getinfo($curl, CURLINFO_TOTAL_TIME)) * 1000);

    curl_close($curl);

    $diagnostico['conectou'] = ($diagnostico['curl_errno'] === 0 && $diagnostico['http_code'] > 0);
    $diagnostico['autorizado'] = ($diagnostico['http_code'] >= 200 && $diagnostico['http_code'] < 300);

    $respostaTexto = is_string($response) ? trim($response) : '';
    if ($respostaTexto !== '') {
        $diagnostico['resposta_bruta_trecho'] = mb_substr(preg_replace('/\s+/', ' ', $respostaTexto), 0, 500);
    }

    $cartoes = [];
    if ((int) $codempresa === 2) {
        $cartoes = processarRespostaValecard($response);
    } else {
        $cartoes = processarRespostaTicketLog($response);
    }

    $diagnostico['qtd_itens'] = is_array($cartoes) ? count($cartoes) : 0;
    $diagnostico['retornou_dados'] = ($diagnostico['qtd_itens'] > 0);

    return $diagnostico;
}
?>