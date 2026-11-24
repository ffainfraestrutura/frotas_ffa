<?php

declare(strict_types=1);

/**
 * Realiza uma requisição HTTP à Ticketlog.
 */
function requisitarTicketlog(
    string $url,
    string $metodo,
    array $payload
): array {
    $auth = getenv('TICKETLOG_AUTH') ? base64_encode(getenv('TICKETLOG_AUTH')) : "W09wZXJhZG9yV2ViXWFwcDE4MzA2MjQyNDc6RFhYSm5WejA=";

    if (!$auth) {
        return [
            'ok' => false,
            'http_code' => null,
            'resposta' => null,
            'erro' => 'Credencial TICKETLOG_AUTH não configurada.',
            'falha_comunicacao' => false
        ];
    }

    $json = json_encode($payload, JSON_UNESCAPED_UNICODE);

    if ($json === false) {
        return [
            'ok' => false,
            'http_code' => null,
            'resposta' => null,
            'erro' => 'Não foi possível gerar o JSON da requisição.',
            'falha_comunicacao' => false
        ];
    }

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $metodo,
        CURLOPT_POSTFIELDS => $json,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
            'Accept-Charset: utf-8',
            'Authorization: Basic ' . $auth
        ],
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_ENCODING => ''
    ]);

    $resposta = curl_exec($curl);
    $erroCurl = curl_error($curl);
    $httpCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    if ($resposta === false) {
        return [
            'ok' => false,
            'http_code' => $httpCode ?: null,
            'resposta' => null,
            'erro' => $erroCurl ?: 'Falha na comunicação com a Ticketlog.',
            'falha_comunicacao' => true
        ];
    }

    $dados = json_decode($resposta, true);

    if ($httpCode < 200 || $httpCode >= 300) {
        return [
            'ok' => false,
            'http_code' => $httpCode,
            'resposta' => $dados,
            'erro' => 'Ticketlog retornou HTTP ' . $httpCode,
            'falha_comunicacao' => false
        ];
    }

    return [
        'ok' => true,
        'http_code' => $httpCode,
        'resposta' => $dados,
        'erro' => null,
        'falha_comunicacao' => false
    ];
}


/**
 * Busca o cartão ativo de uma placa na Ticketlog.
 */
function ticketlogBuscarCartaoAtivo(string $placa): array
{
    $resultado = requisitarTicketlog(
        'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/relatorioExtratoSimplificado/search',
        'POST',
        [
            'codigoCliente' => 183062,
            'codigoProduto' => 4,
            'placaVeiculo' => $placa,
            'situacaoCartao' => 'A',
            'ordem' => 'C'
        ]
    );

    if (!$resultado['ok']) {
        return [
            'sucesso' => false,
            'numero_cartao' => null,
            'http_code' => $resultado['http_code'],
            'mensagem' => $resultado['erro'],
            'etapa' => 'CONSULTA_CARTAO'
        ];
    }

    $itens = $resultado['resposta']['itens'] ?? null;

    if (!is_array($itens)) {
        return [
            'sucesso' => false,
            'numero_cartao' => null,
            'http_code' => $resultado['http_code'],
            'mensagem' => 'Resposta da Ticketlog não contém uma lista válida de cartões.',
            'etapa' => 'CONSULTA_CARTAO'
        ];
    }

    foreach ($itens as $item) {
        if (
            isset($item['situacao'], $item['numeroCartao']) &&
            $item['situacao'] === 'A' &&
            !empty($item['numeroCartao'])
        ) {
            return [
                'sucesso' => true,
                'numero_cartao' => $item['numeroCartao'],
                'http_code' => $resultado['http_code'],
                'mensagem' => 'Cartão ativo localizado.',
                'etapa' => 'CONSULTA_CARTAO'
            ];
        }
    }

    return [
        'sucesso' => false,
        'numero_cartao' => null,
        'http_code' => $resultado['http_code'],
        'mensagem' => 'Nenhum cartão ativo encontrado para a placa ' . $placa . '.',
        'etapa' => 'CONSULTA_CARTAO'
    ];
}

function ticketlogZerarSaldo(
    string $placa,
    string $numeroCartao
): array {
    $resultado = requisitarTicketlog(
        'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/usuarioCartaoLimite',
        'PUT',
        [
            'codigoCliente' => 183062,
            'codigoProduto' => 4,
            'tipoAlteracao' => 'ZS',
            'tipoLimite' => 'AS',
            'tipoOperacao' => 'SP',
            'cartoes' => [
                [
                    'numeroCartao' => $numeroCartao,
                    'valorLimite' => 0,
                    'valorLimiteProxPeriodo' => null,
                    'mensagem' => 'Controle Combustivel'
                ]
            ]
        ]
    );

    if (!$resultado['ok']) {
        return [
            'sucesso' => false,
            'http_code' => $resultado['http_code'],
            'mensagem' => $resultado['erro'],
            'etapa' => 'ZERAR_SALDO',
            'resultado_indeterminado' => $resultado['falha_comunicacao']
        ];
    }

    $resposta = $resultado['resposta'];

    if (
        !is_array($resposta) ||
        !array_key_exists('sucesso', $resposta)
    ) {
        return [
            'sucesso' => false,
            'http_code' => $resultado['http_code'],
            'mensagem' => 'Resposta da Ticketlog sem confirmação do resultado.',
            'etapa' => 'ZERAR_SALDO',
            'resultado_indeterminado' => true
        ];
    }

    if ($resposta['sucesso'] === true) {
        return [
            'sucesso' => true,
            'http_code' => $resultado['http_code'],
            'mensagem' => 'Saldo zerado para a placa ' . $placa . '.',
            'etapa' => 'ZERAR_SALDO',
            'resultado_indeterminado' => false
        ];
    }

    return [
        'sucesso' => false,
        'http_code' => $resultado['http_code'],
        'mensagem' => $resposta['mensagem'] ?? 'Ticketlog não confirmou a alteração do saldo.',
        'etapa' => 'ZERAR_SALDO',
        'resultado_indeterminado' => false
    ];
}

/**
 * Atualiza o saldo do cartão na Ticketlog.
 */
function ticketlogAtualizarSaldo(
    string $placa,
    float $valor,
    string $numeroCartao
): array {
    $resultado = requisitarTicketlog(
        'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/usuarioCartaoLimite',
        'PUT',
        [
            'codigoCliente' => 183062,
            'codigoProduto' => 4,
            'tipoAlteracao' => 'AR',
            'tipoLimite' => 'AS',
            'tipoOperacao' => 'SP',
            'cartoes' => [
                [
                    'numeroCartao' => $numeroCartao,
                    'valorLimite' => $valor,
                    'valorLimiteProxPeriodo' => null,
                    'mensagem' => 'Controle Combustivel'
                ]
            ]
        ]
    );

    if (!$resultado['ok']) {
        return [
            'sucesso' => false,
            'http_code' => $resultado['http_code'],
            'mensagem' => $resultado['erro'],
            'etapa' => 'ALTERACAO_SALDO',
            'resultado_indeterminado' => $resultado['falha_comunicacao']
        ];
    }

    $resposta = $resultado['resposta'];

    if (
        !is_array($resposta) ||
        !array_key_exists('sucesso', $resposta)
    ) {
        return [
            'sucesso' => false,
            'http_code' => $resultado['http_code'],
            'mensagem' => 'Resposta da Ticketlog sem confirmação do resultado.',
            'etapa' => 'ALTERACAO_SALDO',
            'resultado_indeterminado' => true
        ];
    }

    if ($resposta['sucesso'] === true) {
        return [
            'sucesso' => true,
            'http_code' => $resultado['http_code'],
            'mensagem' => 'Saldo atualizado para a placa ' . $placa . ', valor R$ ' . number_format($valor, 2, ',', '.') . '.',
            'etapa' => 'ALTERACAO_SALDO',
            'resultado_indeterminado' => false
        ];
    }

    return [
        'sucesso' => false,
        'http_code' => $resultado['http_code'],
        'mensagem' => $resposta['mensagem'] ?? 'Ticketlog não confirmou a alteração do saldo.',
        'etapa' => 'ALTERACAO_SALDO',
        'resultado_indeterminado' => false
    ];
}


/**
 * Executa o fluxo completo de uma placa.
 */
function ticketlogProcessarCartao(
    string $placa,
    float $valor
): array {
    $cartao = ticketlogBuscarCartaoAtivo($placa);

    if (!$cartao['sucesso']) {
        return [
            'sucesso' => false,
            'http_code' => $cartao['http_code'],
            'etapa' => $cartao['etapa'],
            'mensagem' => $cartao['mensagem'],
            'resultado_indeterminado' => false
        ];
    }

    ticketlogZerarSaldo($placa, (string) $cartao['numero_cartao']);

    return ticketlogAtualizarSaldo(
        $placa,
        $valor,
        (string) $cartao['numero_cartao']
    );
}
