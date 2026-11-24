<?php

declare(strict_types=1);

/**
 * ============================================================
 *  SCRIPT MONOLÍTICO - ZERAR TODOS OS CARTÕES TICKETLOG
 * ============================================================
 *  Uso:
 *      php zerar_cartoes.php              -> lista e zera todos
 *      php zerar_cartoes.php --dry-run    -> apenas lista (não altera)
 *      php zerar_cartoes.php --placa=ABC1234 -> zera apenas 1 placa
 * ============================================================
 */

// ------------------------------------------------------------
// 1) CONFIGURAÇÕES
// ------------------------------------------------------------
const TICKETLOG_CLIENTE  = 183062;
const TICKETLOG_PRODUTO  = 4;
const TICKETLOG_BASE_URL = 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs';

// ------------------------------------------------------------
// 2) REQUISIÇÃO HTTP GENÉRICA
// ------------------------------------------------------------
function requisitarTicketlog(
    string $url,
    string $metodo,
    array $payload
): array {
    $auth = getenv('TICKETLOG_AUTH')
        ? base64_encode(getenv('TICKETLOG_AUTH'))
        : "W09wZXJhZG9yV2ViXWFwcDE4MzA2MjQyNDc6RFhYSm5WejA=";

    if (!$auth) {
        return [
            'ok' => false,
            'http_code' => null,
            'resposta' => null,
            'erro' => 'Credencial TICKETLOG_AUTH não configurada.',
            'falha_comunicacao' => false,
        ];
    }

    $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return [
            'ok' => false,
            'http_code' => null,
            'resposta' => null,
            'erro' => 'Não foi possível gerar o JSON da requisição.',
            'falha_comunicacao' => false,
        ];
    }

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => $metodo,
        CURLOPT_POSTFIELDS     => $json,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Accept: application/json',
            'Accept-Charset: utf-8',
            'Authorization: Basic ' . $auth,
        ],
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_ENCODING       => '',
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
            'falha_comunicacao' => true,
        ];
    }

    $dados = json_decode($resposta, true);

    if ($httpCode < 200 || $httpCode >= 300) {
        return [
            'ok' => false,
            'http_code' => $httpCode,
            'resposta' => $dados,
            'erro' => 'Ticketlog retornou HTTP ' . $httpCode,
            'falha_comunicacao' => false,
        ];
    }

    return [
        'ok' => true,
        'http_code' => $httpCode,
        'resposta' => $dados,
        'erro' => null,
        'falha_comunicacao' => false,
    ];
}

// ------------------------------------------------------------
// 3) LISTAR CARTÕES (sem placa => traz TODOS os cartões)
// ------------------------------------------------------------
/**
 * Consulta o relatório simplificado de cartões.
 * Se $placa for null/vazia, a API retorna TODOS os cartões.
 *
 * @param string|null $placa
 * @return array{sucesso:bool, cartoes:array, http_code:?int, mensagem:string}
 */
function ticketlogListarCartoes(?string $placa = null): array
{
    $payload = [
        'codigoCliente' => TICKETLOG_CLIENTE,
        'codigoProduto' => TICKETLOG_PRODUTO,
        'situacaoCartao' => 'A', // T = Todos (não filtra por situação)
        'ordem' => 'C',
    ];

    // Só envia a placa se ela foi informada.
    if ($placa !== null && trim($placa) !== '') {
        $payload['placaVeiculo'] = trim($placa);
    }

    $resultado = requisitarTicketlog(
        TICKETLOG_BASE_URL . '/relatorioExtratoSimplificado/search',
        'POST',
        $payload
    );

    if (!$resultado['ok']) {
        return [
            'sucesso' => false,
            'cartoes' => [],
            'http_code' => $resultado['http_code'],
            'mensagem' => $resultado['erro'],
        ];
    }

    $itens = $resultado['resposta']['itens'] ?? null;

    if (!is_array($itens)) {
        return [
            'sucesso' => false,
            'cartoes' => [],
            'http_code' => $resultado['http_code'],
            'mensagem' => 'Resposta da Ticketlog não contém uma lista válida de cartões.',
        ];
    }

    return [
        'sucesso' => true,
        'cartoes' => $itens,
        'http_code' => $resultado['http_code'],
        'mensagem' => 'Lista de cartões obtida com sucesso.',
    ];
}

// ------------------------------------------------------------
// 4) ATUALIZAR SALDO DE UM CARTÃO
// ------------------------------------------------------------
function ticketlogAtualizarSaldo(
    string $placa,
    float $valor,
    string $numeroCartao
): array {
    $resultado = requisitarTicketlog(
        TICKETLOG_BASE_URL . '/usuarioCartaoLimite',
        'PUT',
        [
            'codigoCliente' => TICKETLOG_CLIENTE,
            'codigoProduto' => TICKETLOG_PRODUTO,
            'tipoAlteracao' => 'ZS',
            'tipoLimite' => 'AS',
            'tipoOperacao' => 'SP',
            'cartoes' => [
                [
                    'numeroCartao' => $numeroCartao,
                    'valorLimite' => 0,
                    'valorLimiteProxPeriodo' => 0,
                    'mensagem' => 'Controle Combustivel',
                ],
            ],
        ]
    );

    if (!$resultado['ok']) {
        return [
            'sucesso' => false,
            'http_code' => $resultado['http_code'],
            'mensagem' => $resultado['erro'],
            'resultado_indeterminado' => $resultado['falha_comunicacao'],
        ];
    }

    $resposta = $resultado['resposta'];

    if (!is_array($resposta) || !array_key_exists('sucesso', $resposta)) {
        return [
            'sucesso' => false,
            'http_code' => $resultado['http_code'],
            'mensagem' => 'Resposta da Ticketlog sem confirmação do resultado.',
            'resultado_indeterminado' => true,
        ];
    }

    if ($resposta['sucesso'] === true) {
        return [
            'sucesso' => true,
            'http_code' => $resultado['http_code'],
            'mensagem' => sprintf(
                'Saldo zerado para placa %s (cartão %s).',
                $placa,
                $numeroCartao
            ),
            'resultado_indeterminado' => false,
        ];
    }

    return [
        'sucesso' => false,
        'http_code' => $resultado['http_code'],
        'mensagem' => $resposta['mensagem'] ?? 'Ticketlog não confirmou a alteração do saldo.',
        'resultado_indeterminado' => false,
    ];
}

// ------------------------------------------------------------
// 5) ZERAR TODOS OS CARTÕES
// ------------------------------------------------------------
/**
 * Lista todos os cartões (sem placa) e zera cada um.
 * Se $placa for informada, zera apenas os cartões daquela placa.
 */
function ticketlogZerarTodosCartoes(?string $placa = null, bool $dryRun = false): array
{
    echo "===> Consultando cartões" . ($placa ? " da placa {$placa}" : " (todos)") . "...\n";

    $lista = ticketlogListarCartoes($placa);

    if (!$lista['sucesso']) {
        return [
            'sucesso' => false,
            'total' => 0,
            'sucesso_count' => 0,
            'falha_count' => 0,
            'detalhes' => [],
            'mensagem' => 'Falha ao listar cartões: ' . $lista['mensagem'],
        ];
    }

    $cartoes = $lista['cartoes'];
    $total = count($cartoes);

    echo "===> {$total} cartão(ões) encontrado(s).\n";

    if ($total === 0) {
        return [
            'sucesso' => true,
            'total' => 0,
            'sucesso_count' => 0,
            'falha_count' => 0,
            'detalhes' => [],
            'mensagem' => 'Nenhum cartão encontrado.',
        ];
    }

    $sucessoCount = 0;
    $falhaCount = 0;
    $detalhes = [];

    foreach ($cartoes as $indice => $cartao) {
        $numeroCartao = (string) ($cartao['numeroCartao'] ?? '');
        $placaCartao  = (string) ($cartao['placaVeiculo'] ?? $cartao['placa'] ?? 'N/A');
        $situacao     = (string) ($cartao['situacao'] ?? '?');

        // Ignora cartões sem número (não dá para operar)
        if ($numeroCartao === '') {
            echo sprintf(
                "[%02d/%02d] IGNORADO - placa=%s, cartão vazio (situação=%s)\n",
                $indice + 1,
                $total,
                $placaCartao,
                $situacao
            );
            $falhaCount++;
            $detalhes[] = [
                'placa' => $placaCartao,
                'numero_cartao' => null,
                'situacao' => $situacao,
                'resultado' => 'IGNORADO',
                'mensagem' => 'Cartão sem número.',
            ];
            continue;
        }

        echo sprintf(
            "[%02d/%02d] Zerando placa=%s, cartão=%s, situação=%s%s\n",
            $indice + 1,
            $total,
            $placaCartao,
            $numeroCartao,
            $situacao,
            $dryRun ? ' (DRY-RUN)' : ''
        );

        if ($dryRun) {
            $sucessoCount++;
            $detalhes[] = [
                'placa' => $placaCartao,
                'numero_cartao' => $numeroCartao,
                'situacao' => $situacao,
                'resultado' => 'DRY-RUN',
                'mensagem' => 'Nenhuma alteração enviada.',
            ];
            continue;
        }

        $res = ticketlogAtualizarSaldo($placaCartao, 0.0, $numeroCartao);

        if ($res['sucesso']) {
            $sucessoCount++;
            echo "         OK - {$res['mensagem']}\n";
            $detalhes[] = [
                'placa' => $placaCartao,
                'numero_cartao' => $numeroCartao,
                'situacao' => $situacao,
                'resultado' => 'OK',
                'mensagem' => $res['mensagem'],
            ];
        } else {
            $falhaCount++;
            echo "         FALHA - {$res['mensagem']}\n";
            $detalhes[] = [
                'placa' => $placaCartao,
                'numero_cartao' => $numeroCartao,
                'situacao' => $situacao,
                'resultado' => 'FALHA',
                'mensagem' => $res['mensagem'],
                'indeterminado' => $res['resultado_indeterminado'] ?? false,
            ];
        }
    }

    return [
        'sucesso' => $falhaCount === 0,
        'total' => $total,
        'sucesso_count' => $sucessoCount,
        'falha_count' => $falhaCount,
        'detalhes' => $detalhes,
        'mensagem' => sprintf(
            'Processados: %d | Sucesso: %d | Falha: %d',
            $total,
            $sucessoCount,
            $falhaCount
        ),
    ];
}

// ------------------------------------------------------------
// 6) EXECUÇÃO VIA LINHA DE COMANDO
// ------------------------------------------------------------
function main(array $argv): int
{
    // Parse simples de argumentos
    $placa = null;
    $dryRun = false;

    foreach (array_slice($argv, 1) as $arg) {
        if ($arg === '--dry-run') {
            $dryRun = true;
        } elseif (str_starts_with($arg, '--placa=')) {
            $placa = substr($arg, strlen('--placa='));
        } elseif ($arg === '--help' || $arg === '-h') {
            echo "Uso: php zerar_cartoes.php [--placa=ABC1234] [--dry-run]\n";
            return 0;
        }
    }

    echo str_repeat('=', 60) . "\n";
    echo " ZERAR CARTÕES TICKETLOG\n";
    echo " Cliente: " . TICKETLOG_CLIENTE . " | Produto: " . TICKETLOG_PRODUTO . "\n";
    echo " Modo: " . ($dryRun ? "DRY-RUN (não altera)" : "EXECUÇÃO REAL") . "\n";
    echo " Filtro: " . ($placa ?: "TODOS OS CARTÕES (sem placa)") . "\n";
    echo str_repeat('=', 60) . "\n\n";

    if (!$dryRun) {
        echo "ATENÇÃO: Você está prestes a ZERAR o saldo dos cartões.\n";
        echo "Pressione ENTER para continuar ou CTRL+C para abortar...\n";
        fgets(STDIN);
    }

    $resultado = ticketlogZerarTodosCartoes($placa, $dryRun);

    echo "\n" . str_repeat('=', 60) . "\n";
    echo " RESUMO FINAL\n";
    echo str_repeat('=', 60) . "\n";
    echo "Mensagem: {$resultado['mensagem']}\n";

    if ($resultado['falha_count'] > 0) {
        echo "\n--- Itens com falha ---\n";
        foreach ($resultado['detalhes'] as $d) {
            if (($d['resultado'] ?? '') === 'FALHA' || ($d['resultado'] ?? '') === 'IGNORADO') {
                printf(
                    "Placa: %-10s | Cartão: %-20s | %s | %s\n",
                    $d['placa'] ?? 'N/A',
                    $d['numero_cartao'] ?? '(vazio)',
                    $d['resultado'],
                    $d['mensagem'] ?? ''
                );
            }
        }
    }

    echo "\n";
    return $resultado['sucesso'] ? 0 : 1;
}

// Só executa se for chamado diretamente pela CLI
if (PHP_SAPI === 'cli' && realpath($argv[0]) === __FILE__) {
    exit(main($argv));
}