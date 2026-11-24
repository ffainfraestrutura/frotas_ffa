<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');

$data = date('Y-m-d');
include_once "./controleacesso.php";
include_once "../conecta.php";
include_once "../func/log.php";
include_once "../func/consultacartao.php";

header("Content-type: text/html; charset=utf-8");

function h($valor)
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function boolTexto($valor)
{
    return $valor ? 'true' : 'false';
}

function normalizarPlaca($placa)
{
    $placa = strtoupper(trim((string) $placa));
    return preg_replace('/[^A-Z0-9]/', '', $placa);
}

function testarEndpointOperadora($codEmpresa)
{
    $url = '';
    $nome = 'Desconhecida';

    if ((int) $codEmpresa === 2) {
        $nome = 'Valecard';
        $url = 'https://kissapi.valecard.com.br/core/services/frotaCliente/buscarBasedeCartoes';
    } elseif ((int) $codEmpresa === 1) {
        $nome = 'TicketLog';
        $url = 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/relatorioExtratoSimplificado/search';
    }

    if ($url === '') {
        return [
            'operadora' => $nome,
            'url' => '',
            'conectou' => false,
            'http_code' => 0,
            'tempo_ms' => 0,
            'erro_curl' => 'Codigo da empresa nao mapeado para endpoint.',
            'ip_remoto' => '',
            'dns_ms' => 0,
            'tcp_ms' => 0,
            'tls_ms' => 0
        ];
    }

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_NOBODY => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_FAILONERROR => false,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2
    ]);

    curl_exec($curl);

    $erroNumero = curl_errno($curl);
    $erroTexto = curl_error($curl);
    $httpCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $tempoTotal = (float) curl_getinfo($curl, CURLINFO_TOTAL_TIME);
    $ipRemoto = (string) curl_getinfo($curl, CURLINFO_PRIMARY_IP);
    $tempoDns = (float) curl_getinfo($curl, CURLINFO_NAMELOOKUP_TIME);
    $tempoTcp = (float) curl_getinfo($curl, CURLINFO_CONNECT_TIME);
    $tempoTls = (float) curl_getinfo($curl, CURLINFO_APPCONNECT_TIME);

    curl_close($curl);

    $conectou = ($erroNumero === 0 && $httpCode > 0);

    return [
        'operadora' => $nome,
        'url' => $url,
        'conectou' => $conectou,
        'http_code' => $httpCode,
        'tempo_ms' => (int) round($tempoTotal * 1000),
        'erro_curl' => $erroTexto !== '' ? $erroTexto : '',
        'ip_remoto' => $ipRemoto,
        'dns_ms' => (int) round($tempoDns * 1000),
        'tcp_ms' => (int) round($tempoTcp * 1000),
        'tls_ms' => (int) round($tempoTls * 1000)
    ];
}

$idPedido = isset($_GET['id_pedido']) ? (int) $_GET['id_pedido'] : 0;
$erroBusca = '';

$pedido = null;
$tecnico = null;
$veiculo = null;
$debugIfs = [];

if ($idPedido > 0) {
    // CONSULTA 1: Buscar dados do pedido + placa
    $sqlPedido = "SELECT p.idtbpedidostec, p.matricula, p.valor, p.valorinserido, p.data, p.justificativa, p.flag, p.kmhodometro, p.dataplantao, p.placa, u.nome
                  FROM bdfrota.tbpedidostec p
                  LEFT JOIN bdfrota.tbusuario u ON u.matricula = p.matricula
                  WHERE p.idtbpedidostec = '$idPedido'
                  LIMIT 1";
    $resPedido = mysql_query($sqlPedido);

    if (!$resPedido) {
        $erroBusca = 'Erro ao consultar pedido: ' . mysql_error();
    } else {
        $pedido = mysql_fetch_assoc($resPedido);
        if (!$pedido) {
            $erroBusca = 'Pedido não encontrado para o ID informado.';
        }
    }

    if ($pedido) {
        $matTec = $pedido['matricula'];
        $placaPedido = isset($pedido['placa']) ? $pedido['placa'] : '';

        // CONSULTA 2: Dados do técnico (bdaniel.tbfuncionario)
        $sqlTec = "SELECT matricula, nome, estado, status, ccusto, codempresa, uf_trabalho, cargo
                   FROM bdaniel.tbfuncionario
                   WHERE matricula = '$matTec'
                   LIMIT 1";
        $resTec = mysql_query($sqlTec);
        if ($resTec) {
            $tecnico = mysql_fetch_assoc($resTec);
        }

        // Debug: Verificar se técnico foi encontrado
        if (!$tecnico) {
            error_log("DEBUG: Técnico não encontrado para matrícula: $matTec");
        } else {
            error_log("DEBUG: Técnico encontrado - Nome: " . $tecnico['nome']);
        }

        // CONSULTA 3: Veículo usando PLACA (do pedido)
        $sqlVeic = "SELECT idtbveiculo, placa, marca, modelo, tipo, hodometro, status, unidade, combustivel, airbag, gps, uf
                    FROM bdfrota.tbveiculo
                    WHERE placa = '$placaPedido'
                    ORDER BY idtbveiculo DESC
                    LIMIT 1";
        $resVeic = mysql_query($sqlVeic);
        if ($resVeic) {
            $veiculo = mysql_fetch_assoc($resVeic);
        }

        // Debug: Verificar se veículo foi encontrado
        if (!$veiculo) {
            error_log("DEBUG: Veículo não encontrado para placa: $placaPedido");
        } else {
            error_log("DEBUG: Veículo encontrado - Placa: " . $veiculo['placa']);
        }

        // CONSULTA 4: Hierarquia - Técnico -> Supervisor -> Coordenador -> Gerente
        $hierarquia = [
            'tecnico' => ['nome' => isset($tecnico['nome']) ? $tecnico['nome'] : '', 'matricula' => $matTec, 'status' => isset($tecnico['status']) ? $tecnico['status'] : ''],
            'supervisor' => ['nome' => '', 'matricula' => '', 'status' => ''],
            'coordenador' => ['nome' => '', 'matricula' => '', 'status' => ''],
            'gerente' => ['nome' => '', 'matricula' => '', 'status' => '']
        ];

        // Dados do técnico (já temos)
        if ($tecnico) {
            $hierarquia['tecnico']['nome'] = $tecnico['nome'];
            $hierarquia['tecnico']['status'] = $tecnico['status'];
        }

        // Buscar supervisor do técnico
        $sqlSup = "SELECT s.idtbsupervisor, s.matricula, f.nome, f.status 
                   FROM bdfrota.tbsupervisor s
                   JOIN bdaniel.tbfuncionario f ON f.matricula = s.matricula
                   WHERE s.idtbsupervisor = (
                       SELECT idtbsupervisor FROM bdfrota.tbusuario WHERE matricula = '$matTec' LIMIT 1
                   )
                   LIMIT 1";
        $resSup = mysql_query($sqlSup);
        if ($resSup && mysql_num_rows($resSup) > 0) {
            $rowSup = mysql_fetch_assoc($resSup);
            $hierarquia['supervisor']['nome'] = $rowSup['nome'];
            $hierarquia['supervisor']['matricula'] = $rowSup['matricula'];
            $hierarquia['supervisor']['status'] = $rowSup['status'];
            $idtbSupervisor = $rowSup['idtbsupervisor'];
        }

        // Buscar coordenador do supervisor
        if (isset($idtbSupervisor)) {
            $sqlCoord = "SELECT c.idtbcoordenador, c.matricula, f.nome, f.status 
                         FROM bdfrota.tbcoord c
                         JOIN bdaniel.tbfuncionario f ON f.matricula = c.matricula
                         WHERE c.idtbcoordenador = (
                             SELECT idtbcoordenador FROM bdfrota.tbsupervisor WHERE idtbsupervisor = '$idtbSupervisor' LIMIT 1
                         )
                         LIMIT 1";
            $resCoord = mysql_query($sqlCoord);
            if ($resCoord && mysql_num_rows($resCoord) > 0) {
                $rowCoord = mysql_fetch_assoc($resCoord);
                $hierarquia['coordenador']['nome'] = $rowCoord['nome'];
                $hierarquia['coordenador']['matricula'] = $rowCoord['matricula'];
                $hierarquia['coordenador']['status'] = $rowCoord['status'];
                $idtbCoordenador = $rowCoord['idtbcoordenador'];
            }
        }

        // Buscar gerente do coordenador
        if (isset($idtbCoordenador)) {
            $sqlGer = "SELECT g.idtbgerente, g.matricula, f.nome, f.status 
                       FROM bdfrota.tbgerente g
                       JOIN bdaniel.tbfuncionario f ON f.matricula = g.matricula
                       WHERE g.idtbgerente = (
                           SELECT idtbgerente FROM bdfrota.tbcoord WHERE idtbcoordenador = '$idtbCoordenador' LIMIT 1
                       )
                       LIMIT 1";
            $resGer = mysql_query($sqlGer);
            if ($resGer && mysql_num_rows($resGer) > 0) {
                $rowGer = mysql_fetch_assoc($resGer);
                $hierarquia['gerente']['nome'] = $rowGer['nome'];
                $hierarquia['gerente']['matricula'] = $rowGer['matricula'];
                $hierarquia['gerente']['status'] = $rowGer['status'];
            }
        }

        // Função para mapear código de empresa
        $operadorasMap = [
            '1' => 'TicketLog',
            '2' => 'Valecard'
        ];
        
        // Verificar se codempresa existe antes de usar
        $codEmpresaTec = (isset($tecnico) && !empty($tecnico['codempresa'])) ? $tecnico['codempresa'] : '';
        $nomeOperadora = (isset($operadorasMap[$codEmpresaTec])) ? $operadorasMap[$codEmpresaTec] : 'Desconhecida';

        $valorPedido = (float) str_replace(',', '.', ($pedido['valorinserido'] !== null && $pedido['valorinserido'] !== '' ? $pedido['valorinserido'] : $pedido['valor']));
        $ccustoTec = isset($tecnico['ccusto']) ? $tecnico['ccusto'] : '';
        $codEmpresaTec = isset($tecnico['codempresa']) ? $tecnico['codempresa'] : '';
        $placa = isset($veiculo['placa']) ? $veiculo['placa'] : '';

        $max = 51;
        $ifRedeExterna = (
            stripos(trim($ccustoTec), 'rede externa') !== false ||
            stripos(trim($ccustoTec), 'implanta') !== false ||
            stripos(trim($ccustoTec), 'obra') !== false
        );
        if ($ifRedeExterna) {
            $max = 81;
        }

        $placaNormalizada = normalizarPlaca($placa);
        $saldoCartao = null;
        $operadora = 'Operadora não identificada';
        $motivoOperadora = '';
        $temEmpresaNoRetorno = false;
        $temPlacaNoRetorno = false;
        $chavePlacaEncontrada = '';
        $placaNormalizadaEncontrada = '';
        $totalPlacasEmpresa = 0;
        $amostraChavesPlaca = [];
        $resumoProblemaOperadora = '';
        $endpointOperadora = '';
        $statusCartaoConsultado = '';
        $tipoConsultaOperadora = 'placa';
        $testeConexaoOperadora = [
            'operadora' => 'Desconhecida',
            'url' => '',
            'conectou' => false,
            'http_code' => 0,
            'tempo_ms' => 0,
            'erro_curl' => '',
            'ip_remoto' => '',
            'dns_ms' => 0,
            'tcp_ms' => 0,
            'tls_ms' => 0
        ];
        $diagnosticoApiReal = [
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

        if ($codEmpresaTec !== '') {
            $testeConexaoOperadora = testarEndpointOperadora($codEmpresaTec);
        }

        if ($codEmpresaTec !== '' && function_exists('consultarCartoesComDebug')) {
            $diagnosticoApiReal = consultarCartoesComDebug($placa, null, $codEmpresaTec);
        }

        if ($placa !== '' && $codEmpresaTec !== '') {
            $cartoes = consultarCartoes($placa, null, $codEmpresaTec);
            if ((int) $codEmpresaTec === 2) {
                $endpointOperadora = 'Valecard - buscarBasedeCartoes';
                $statusCartaoConsultado = 'A';
            } elseif ((int) $codEmpresaTec === 1) {
                $endpointOperadora = 'TicketLog - relatorioExtratoSimplificado/search';
                $statusCartaoConsultado = 'A';
            }
            $temEmpresaNoRetorno = isset($cartoes[$codEmpresaTec]);
            if ($temEmpresaNoRetorno) {
                if (is_array($cartoes[$codEmpresaTec])) {
                    $totalPlacasEmpresa = count($cartoes[$codEmpresaTec]);
                    $amostraChavesPlaca = array_slice(array_keys($cartoes[$codEmpresaTec]), 0, 10);
                }

                if (isset($cartoes[$codEmpresaTec][$placaNormalizada])) {
                    $temPlacaNoRetorno = true;
                    $chavePlacaEncontrada = $placaNormalizada;
                    $placaNormalizadaEncontrada = $placaNormalizada;
                } else {
                    foreach ($cartoes[$codEmpresaTec] as $chavePlaca => $dadosCartao) {
                        $chaveNormalizada = normalizarPlaca($chavePlaca);
                        if ($chaveNormalizada === $placaNormalizada) {
                            $temPlacaNoRetorno = true;
                            $chavePlacaEncontrada = (string) $chavePlaca;
                            $placaNormalizadaEncontrada = $chaveNormalizada;
                            break;
                        }
                    }
                }

                if ($temPlacaNoRetorno) {
                    $saldoCartao = isset($cartoes[$codEmpresaTec][$chavePlacaEncontrada]['saldo'])
                        ? $cartoes[$codEmpresaTec][$chavePlacaEncontrada]['saldo']
                        : null;
                    $operadora = ((int) $codEmpresaTec === 2) ? 'Valecard' : 'TicketLog';
                    $motivoOperadora = 'Operadora identificada com sucesso.';
                    $resumoProblemaOperadora = 'Fluxo OK: empresa e placa foram encontradas no retorno.';
                } else {
                    if ($totalPlacasEmpresa === 0) {
                        $motivoOperadora = 'A empresa foi encontrada no retorno, mas veio sem nenhuma placa.';
                        $resumoProblemaOperadora = 'A UF do colaborador esta correta para a operadora, mas a integracao retornou a empresa sem placas/cartoes.';
                    } else {
                        $motivoOperadora = 'A empresa foi encontrada no retorno e possui placas, mas esta placa nao apareceu entre elas.';
                        $resumoProblemaOperadora = 'A UF do colaborador esta correta para a operadora, mas a placa do pedido nao veio no retorno dessa empresa.';
                    }
                }
            } else {
                $motivoOperadora = 'A empresa do técnico não foi encontrada no retorno da consulta de cartões.';
                $resumoProblemaOperadora = 'O codigo da empresa do tecnico nao apareceu no retorno da integracao.';
            }
        } elseif ($placa === '' && $codEmpresaTec === '') {
            $motivoOperadora = 'Placa do veículo e código da empresa estão vazios.';
            $resumoProblemaOperadora = 'Faltam dados basicos para consultar a integracao.';
        } elseif ($placa === '') {
            $motivoOperadora = 'Placa do veículo está vazia.';
            $resumoProblemaOperadora = 'Sem placa, nao ha como localizar cartao na integracao.';
        } else {
            $motivoOperadora = 'Código da empresa do técnico está vazio.';
            $resumoProblemaOperadora = 'Sem codigo de empresa, nao ha como localizar o bloco correto na integracao.';
        }

        if ($motivoOperadora === '' && $saldoCartao === null) {
            $motivoOperadora = 'Saldo não retornou para a placa/empresa consultada.';
            $resumoProblemaOperadora = 'Empresa e placa foram localizadas, mas o saldo nao veio preenchido.';
        }

        // Mapeamento de operadoras por UF
        $operadorasUF = [
            '1' => ['nome' => 'TicketLog', 'ufs' => ['RJ', 'ES']],
            '2' => ['nome' => 'Valecard', 'ufs' => ['SP', 'MG', 'BA', 'GO', 'DF', 'MT', 'MS', 'PR', 'SC', 'RS']]
        ];
        
        // Verificar se UF do COLABORADOR está correto para a operadora
        $ufColaborador = isset($tecnico['estado']) ? strtoupper(trim($tecnico['estado'])) : '';
        $ufCorreta = true;
        $ufEsperada = 'Desconhecida';
        $ufAviso = '';
        
        if ($codEmpresaTec !== '' && isset($operadorasUF[$codEmpresaTec])) {
            $ufsPermitidas = $operadorasUF[$codEmpresaTec]['ufs'];
            $ufEsperada = implode('/', $ufsPermitidas);
            if ($ufColaborador !== '' && !in_array($ufColaborador, $ufsPermitidas)) {
                $ufCorreta = false;
                $ufAviso = "⚠️ ERRO: Colaborador em $ufColaborador, mas operadora {$operadorasUF[$codEmpresaTec]['nome']} é para " . implode('/', $ufsPermitidas);
            }
        }

        $debugIfs = [
            'if_rede_externa' => boolTexto($ifRedeExterna),
            'max_aprovacao' => $max,
            'valor_pedido' => $valorPedido,
            'if_valor_maior_que_max' => boolTexto($valorPedido > $max),
            'if_tem_empresa_no_retorno' => boolTexto($temEmpresaNoRetorno),
            'if_tem_placa_no_retorno' => boolTexto($temPlacaNoRetorno),
            'if_saldo_encontrado' => boolTexto($saldoCartao !== null),
            'saldo_cartao' => $saldoCartao,
            'if_saldo_maior_igual_40' => boolTexto($saldoCartao !== null && $saldoCartao >= 40),
            'operadora' => $operadora,
            'motivo_operadora' => $motivoOperadora,
            'resumo_problema_operadora' => $resumoProblemaOperadora,
            'endpoint_operadora' => $endpointOperadora,
            'status_cartao_consultado' => $statusCartaoConsultado,
            'tipo_consulta_operadora' => $tipoConsultaOperadora,
            'teste_conexao_ok' => boolTexto($testeConexaoOperadora['conectou']),
            'teste_conexao_url' => $testeConexaoOperadora['url'],
            'teste_conexao_http_code' => $testeConexaoOperadora['http_code'],
            'teste_conexao_tempo_ms' => $testeConexaoOperadora['tempo_ms'],
            'teste_conexao_ip' => $testeConexaoOperadora['ip_remoto'],
            'teste_conexao_dns_ms' => $testeConexaoOperadora['dns_ms'],
            'teste_conexao_tcp_ms' => $testeConexaoOperadora['tcp_ms'],
            'teste_conexao_tls_ms' => $testeConexaoOperadora['tls_ms'],
            'teste_conexao_erro' => $testeConexaoOperadora['erro_curl'],
            'api_real_http_code' => $diagnosticoApiReal['http_code'],
            'api_real_tempo_ms' => $diagnosticoApiReal['tempo_ms'],
            'api_real_curl_errno' => $diagnosticoApiReal['curl_errno'],
            'api_real_curl_error' => $diagnosticoApiReal['curl_error'],
            'api_real_conectou' => boolTexto($diagnosticoApiReal['conectou']),
            'api_real_autorizado' => boolTexto($diagnosticoApiReal['autorizado']),
            'api_real_qtd_itens' => $diagnosticoApiReal['qtd_itens'],
            'api_real_retorno_dados' => boolTexto($diagnosticoApiReal['retornou_dados']),
            'api_real_endpoint' => $diagnosticoApiReal['endpoint'],
            'api_real_resposta_trecho' => $diagnosticoApiReal['resposta_bruta_trecho'],
            'placa_pedido' => $placaPedido,
            'placa_veiculo' => $placa,
            'placa_normalizada' => $placaNormalizada,
            'chave_placa_encontrada' => $chavePlacaEncontrada,
            'placa_normalizada_encontrada' => $placaNormalizadaEncontrada,
            'total_placas_empresa' => $totalPlacasEmpresa,
            'amostra_chaves_placa' => implode(', ', $amostraChavesPlaca),
            'uf_colaborador' => $ufColaborador,
            'uf_esperada' => $ufEsperada,
            'uf_correta' => boolTexto($ufCorreta),
            'uf_aviso' => $ufAviso
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="FFA" />
    <meta name="author" content="FFA" />
    <link rel="icon" type="image/png" href="../src/images/favicon.png" />
    <title>Support / Debugging</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="../src/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed vsc-initialized">
<?php include_once "../menu.php"; ?>
<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Support / Debugging</h1>
            <p class="mb-4">Informe o ID do pedido para consultar os dados usados nas validações de operadora/cartão.</p>

            <div class="card mb-4">
                <div class="card-header bg-primary text-white"><i class="fas fa-search me-2"></i>Buscar Pedido</div>
                <div class="card-body">
                    <form method="get" class="row g-3">
                        <div class="col-md-4">
                            <label for="id_pedido" class="form-label"><strong>ID do pedido</strong></label>
                            <input type="number" min="1" class="form-control" name="id_pedido" id="id_pedido" required value="<?= $idPedido > 0 ? h($idPedido) : '' ?>">
                        </div>
                        <div class="col-md-2 align-self-end">
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-magnifying-glass me-2"></i>Buscar</button>
                        </div>
                    </form>
                </div>
            </div>

            <?php if ($idPedido > 0 && $erroBusca !== ''): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i><?= h($erroBusca) ?>
                </div>
            <?php endif; ?>

            <!-- DEBUG INFO -->
            <?php if ($idPedido > 0 && $pedido && !isset($tecnico)): ?>
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-bug me-2"></i><strong>Debug:</strong> Técnico não foi encontrado na tabela bdaniel.tbfuncionario
                    <br><small>Matrícula buscada: <code><?= h($matTec) ?></code></small>
                </div>
            <?php endif; ?>

            <!-- FIM DEBUG INFO -->

            <?php if ($pedido): ?>
                <div class="card mb-4">
                    <div class="card-header bg-info text-white"><i class="fas fa-file-alt me-2"></i>Dados do Pedido</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>ID:</strong> <span class="badge bg-secondary"><?= h($pedido['idtbpedidostec']) ?></span></p>
                                <p><strong>Matrícula técnico:</strong> <?= h($pedido['matricula']) ?></p>
                                <p><strong>Nome técnico:</strong> <?= h(isset($pedido['nome']) ? $pedido['nome'] : 'Não encontrado') ?></p>
                                <p><strong>Placa (do pedido):</strong> <span class="badge bg-dark" style="font-size: 1.1em; padding: 8px 12px;"><?= h($placaPedido ?: 'Não registrada') ?></span></p>
                                <p><strong>Valor pedido:</strong> <span class="text-success">R$ <?= h($pedido['valor']) ?></span></p>
                                <p><strong>Valor inserido:</strong> <span class="text-success">R$ <?= h($pedido['valorinserido']) ?></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Data:</strong> <?= h($pedido['data']) ?></p>
                                <p><strong>Data plantão:</strong> <?= h($pedido['dataplantao']) ?></p>
                                <p><strong>Hodômetro:</strong> <?= h($pedido['kmhodometro']) ?></p>
                                <p><strong>Flag:</strong> <span class="badge bg-warning"><?= h($pedido['flag']) ?></span></p>
                                <p><strong>Justificativa:</strong> <?= h($pedido['justificativa']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-info text-white"><i class="fas fa-id-card me-2"></i>Dados do Técnico</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Matrícula:</strong> <?= h($matTec) ?></p>
                                <p><strong>Nome:</strong> <?= h(isset($tecnico) && !empty($tecnico['nome']) ? $tecnico['nome'] : 'Não encontrado') ?></p>
                                <p><strong>Cargo:</strong> <?= h(isset($tecnico) && !empty($tecnico['cargo']) ? $tecnico['cargo'] : 'Não encontrado') ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Status:</strong> <span class="badge bg-success"><?= h(isset($tecnico) && !empty($tecnico['status']) ? $tecnico['status'] : 'Não encontrado') ?></span></p>
                                <p><strong>Centro de custo:</strong> <?= h(isset($tecnico) && !empty($tecnico['ccusto']) ? $tecnico['ccusto'] : 'Não encontrado') ?></p>
                                <p><strong>Cód. empresa:</strong> <span class="badge bg-primary"><?= h(isset($tecnico) && !empty($tecnico['codempresa']) ? $tecnico['codempresa'] : 'Não encontrado') ?></span> - <?= h($nomeOperadora ?? 'Desconhecida') ?></p>
                                <p><strong>UF de trabalho:</strong> <span class="badge bg-secondary"><?= h(isset($tecnico) && !empty($tecnico['uf_trabalho']) ? $tecnico['uf_trabalho'] : 'Não encontrado') ?></span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- NOVA SEÇÃO: HIERARQUIA COMPLETA -->
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark"><i class="fas fa-sitemap me-2"></i>Hierarquia do Colaborador</div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Técnico -->
                            <div class="col-md-3">
                                <div class="card border-primary mb-3">
                                    <div class="card-header bg-primary text-white">
                                        <i class="fas fa-tools me-2"></i>Técnico
                                    </div>
                                    <div class="card-body">
                                        <p><small><strong>Nome:</strong><br><?= h($hierarquia['tecnico']['nome'] ?: 'Não definido') ?></small></p>
                                        <p><small><strong>Matrícula:</strong><br><?= h($hierarquia['tecnico']['matricula'] ?: 'Não definido') ?></small></p>
                                        <p><small><strong>Status:</strong><br><span class="badge bg-info"><?= h($hierarquia['tecnico']['status'] ?: 'Não definido') ?></span></small></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Supervisor -->
                            <div class="col-md-3">
                                <div class="card border-success mb-3">
                                    <div class="card-header bg-success text-white">
                                        <i class="fas fa-user-tie me-2"></i>Supervisor
                                    </div>
                                    <div class="card-body">
                                        <p><small><strong>Nome:</strong><br><?= h($hierarquia['supervisor']['nome'] ?: 'Sem supervisor') ?></small></p>
                                        <p><small><strong>Matrícula:</strong><br><?= h($hierarquia['supervisor']['matricula'] ?: '-') ?></small></p>
                                        <p><small><strong>Status:</strong><br><span class="badge bg-info"><?= h($hierarquia['supervisor']['status'] ?: '-') ?></span></small></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Coordenador -->
                            <div class="col-md-3">
                                <div class="card border-info mb-3">
                                    <div class="card-header bg-info text-white">
                                        <i class="fas fa-users me-2"></i>Coordenador
                                    </div>
                                    <div class="card-body">
                                        <p><small><strong>Nome:</strong><br><?= h($hierarquia['coordenador']['nome'] ?: 'Sem coordenador') ?></small></p>
                                        <p><small><strong>Matrícula:</strong><br><?= h($hierarquia['coordenador']['matricula'] ?: '-') ?></small></p>
                                        <p><small><strong>Status:</strong><br><span class="badge bg-info"><?= h($hierarquia['coordenador']['status'] ?: '-') ?></span></small></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Gerente -->
                            <div class="col-md-3">
                                <div class="card border-danger mb-3">
                                    <div class="card-header bg-danger text-white">
                                        <i class="fas fa-crown me-2"></i>Gerente
                                    </div>
                                    <div class="card-body">
                                        <p><small><strong>Nome:</strong><br><?= h($hierarquia['gerente']['nome'] ?: 'Sem gerente') ?></small></p>
                                        <p><small><strong>Matrícula:</strong><br><?= h($hierarquia['gerente']['matricula'] ?: '-') ?></small></p>
                                        <p><small><strong>Status:</strong><br><span class="badge bg-info"><?= h($hierarquia['gerente']['status'] ?: '-') ?></span></small></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-info text-white"><i class="fas fa-car me-2"></i>Dados do Veículo</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Placa:</strong> <span class="badge bg-dark" style="font-size: 1.1em; padding: 8px 12px;"><?= h(isset($veiculo) && !empty($veiculo['placa']) ? $veiculo['placa'] : 'Não encontrado') ?></span></p>
                                <p><strong>Marca:</strong> <?= h(isset($veiculo) && !empty($veiculo['marca']) ? $veiculo['marca'] : 'Não encontrado') ?></p>
                                <p><strong>Modelo:</strong> <?= h(isset($veiculo) && !empty($veiculo['modelo']) ? $veiculo['modelo'] : 'Não encontrado') ?></p>
                                <p><strong>Tipo:</strong> <?= h(isset($veiculo) && !empty($veiculo['tipo']) ? $veiculo['tipo'] : 'Não encontrado') ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Combustível:</strong> <?= h(isset($veiculo) && !empty($veiculo['combustivel']) ? $veiculo['combustivel'] : 'Não encontrado') ?></p>
                                <p><strong>Status:</strong> <span class="badge bg-info"><?= h(isset($veiculo) && !empty($veiculo['status']) ? $veiculo['status'] : 'Não encontrado') ?></span></p>
                                <p><strong>Unidade:</strong> <?= h(isset($veiculo) && !empty($veiculo['unidade']) ? $veiculo['unidade'] : 'Não encontrado') ?></p>
                                <p><strong>UF:</strong> <span class="badge bg-secondary"><?= h(isset($veiculo) && !empty($veiculo['uf']) ? $veiculo['uf'] : 'Não encontrado') ?></span></p>
                                <p><strong>Hodômetro:</strong> <?= h(isset($veiculo) && !empty($veiculo['hodometro']) ? $veiculo['hodometro'] : 'Não encontrado') ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark"><i class="fas fa-bug me-2"></i>Debug - Valores dos IFs (Validação de Operadora)</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <tbody>
                                    <tr>
                                        <td><strong>Operadora identificada:</strong></td>
                                        <td><span class="badge bg-primary"><?= h($debugIfs['operadora']) ?></span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Motivo da identificação:</strong></td>
                                        <td>
                                            <?php if ($debugIfs['operadora'] === 'Operadora não identificada'): ?>
                                                <span class="badge bg-danger"><?= h($debugIfs['motivo_operadora']) ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-success"><?= h($debugIfs['motivo_operadora']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Resumo do problema real:</strong></td>
                                        <td>
                                            <?php if ($debugIfs['operadora'] === 'Operadora não identificada'): ?>
                                                <span class="badge bg-warning text-dark"><?= h($debugIfs['resumo_problema_operadora']) ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-success"><?= h($debugIfs['resumo_problema_operadora']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Placa do pedido (tbpedidostec):</strong></td>
                                        <td><span class="badge bg-dark"><?= h($debugIfs['placa_pedido']) !== '' ? h($debugIfs['placa_pedido']) : 'Não informada' ?></span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Operadora consultada na integração:</strong></td>
                                        <td><span class="badge bg-secondary"><?= h($debugIfs['endpoint_operadora']) !== '' ? h($debugIfs['endpoint_operadora']) : 'Não consultada' ?></span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Filtro usado na consulta:</strong></td>
                                        <td>
                                            <span class="badge bg-info">Tipo: <?= h($debugIfs['tipo_consulta_operadora']) ?></span>
                                            <span class="badge bg-info">Status do cartao: <?= h($debugIfs['status_cartao_consultado']) !== '' ? h($debugIfs['status_cartao_consultado']) : 'N/A' ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Teste de conexao com a operadora:</strong></td>
                                        <td>
                                            <?= h($debugIfs['teste_conexao_ok']) === 'true' ? '<span class="badge bg-success">OK</span>' : '<span class="badge bg-danger">FALHA</span>' ?>
                                            <span class="badge bg-secondary">HTTP: <?= h($debugIfs['teste_conexao_http_code']) ?></span>
                                            <span class="badge bg-secondary">Tempo: <?= h($debugIfs['teste_conexao_tempo_ms']) ?> ms</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>URL testada:</strong></td>
                                        <td style="font-size: 0.85rem; word-break: break-all;"><?= h($debugIfs['teste_conexao_url']) !== '' ? h($debugIfs['teste_conexao_url']) : 'Nao aplicavel' ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>IP remoto / Latencias (ms):</strong></td>
                                        <td>
                                            <span class="badge bg-dark"><?= h($debugIfs['teste_conexao_ip']) !== '' ? h($debugIfs['teste_conexao_ip']) : 'N/A' ?></span>
                                            <span class="badge bg-info">DNS: <?= h($debugIfs['teste_conexao_dns_ms']) ?></span>
                                            <span class="badge bg-info">TCP: <?= h($debugIfs['teste_conexao_tcp_ms']) ?></span>
                                            <span class="badge bg-info">TLS: <?= h($debugIfs['teste_conexao_tls_ms']) ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Erro cURL no teste:</strong></td>
                                        <td><?= h($debugIfs['teste_conexao_erro']) !== '' ? '<span class="badge bg-danger">' . h($debugIfs['teste_conexao_erro']) . '</span>' : '<span class="badge bg-success">Sem erro de transporte</span>' ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Chamada real autenticada (API):</strong></td>
                                        <td>
                                            <?= h($debugIfs['api_real_conectou']) === 'true' ? '<span class="badge bg-success">Conectou</span>' : '<span class="badge bg-danger">Falhou</span>' ?>
                                            <?= h($debugIfs['api_real_autorizado']) === 'true' ? '<span class="badge bg-success">Autorizado</span>' : '<span class="badge bg-danger">Nao autorizado</span>' ?>
                                            <?= h($debugIfs['api_real_retorno_dados']) === 'true' ? '<span class="badge bg-success">Com dados</span>' : '<span class="badge bg-warning text-dark">Sem dados</span>' ?>
                                            <span class="badge bg-secondary">HTTP: <?= h($debugIfs['api_real_http_code']) ?></span>
                                            <span class="badge bg-secondary">Tempo: <?= h($debugIfs['api_real_tempo_ms']) ?> ms</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Endpoint da chamada real:</strong></td>
                                        <td style="font-size: 0.85rem; word-break: break-all;"><?= h($debugIfs['api_real_endpoint']) !== '' ? h($debugIfs['api_real_endpoint']) : 'Nao disponivel' ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Itens retornados na chamada real:</strong></td>
                                        <td>
                                            <span class="badge bg-info">Quantidade: <?= h($debugIfs['api_real_qtd_itens']) ?></span>
                                            <span class="badge bg-dark">cURL errno: <?= h($debugIfs['api_real_curl_errno']) ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Erro da chamada real:</strong></td>
                                        <td><?= h($debugIfs['api_real_curl_error']) !== '' ? '<span class="badge bg-danger">' . h($debugIfs['api_real_curl_error']) . '</span>' : '<span class="badge bg-success">Sem erro cURL</span>' ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Trecho da resposta da API:</strong></td>
                                        <td style="font-size: 0.85rem; word-break: break-all;"><?= h($debugIfs['api_real_resposta_trecho']) !== '' ? h($debugIfs['api_real_resposta_trecho']) : 'Sem corpo de resposta' ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total de placas retornadas para a empresa:</strong></td>
                                        <td><span class="badge bg-info"><?= h($debugIfs['total_placas_empresa']) ?></span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Amostra de chaves retornadas:</strong></td>
                                        <td style="font-size: 0.85rem; word-break: break-all;"><?= h($debugIfs['amostra_chaves_placa']) !== '' ? h($debugIfs['amostra_chaves_placa']) : 'Sem chaves retornadas para essa empresa' ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Placa do veículo (tbveiculo):</strong></td>
                                        <td><span class="badge bg-dark"><?= h($debugIfs['placa_veiculo']) !== '' ? h($debugIfs['placa_veiculo']) : 'Não encontrada' ?></span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Placa normalizada:</strong></td>
                                        <td><span class="badge bg-secondary"><?= h($debugIfs['placa_normalizada']) ?></span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Chave da placa encontrada no retorno:</strong></td>
                                        <td><span class="badge bg-dark"><?= h($debugIfs['chave_placa_encontrada']) !== '' ? h($debugIfs['chave_placa_encontrada']) : 'Não encontrada' ?></span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Placa normalizada da chave encontrada:</strong></td>
                                        <td><span class="badge bg-secondary"><?= h($debugIfs['placa_normalizada_encontrada']) !== '' ? h($debugIfs['placa_normalizada_encontrada']) : 'Não encontrada' ?></span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>if (ccusto é rede externa/implanta/obra):</strong></td>
                                        <td><?= h($debugIfs['if_rede_externa']) === 'true' ? '<span class="badge bg-danger">TRUE</span>' : '<span class="badge bg-success">FALSE</span>' ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Max permitido:</strong></td>
                                        <td><span class="badge bg-info">R$ <?= h($debugIfs['max_aprovacao']) ?></span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Valor usado na validação:</strong></td>
                                        <td><span class="badge bg-primary">R$ <?= h($debugIfs['valor_pedido']) ?></span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>if (valor &gt; max):</strong></td>
                                        <td><?= h($debugIfs['if_valor_maior_que_max']) === 'true' ? '<span class="badge bg-danger">TRUE</span>' : '<span class="badge bg-success">FALSE</span>' ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>if (empresa existe no retorno cartão):</strong></td>
                                        <td><?= h($debugIfs['if_tem_empresa_no_retorno']) === 'true' ? '<span class="badge bg-success">TRUE</span>' : '<span class="badge bg-danger">FALSE</span>' ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>if (placa existe no retorno cartão):</strong></td>
                                        <td><?= h($debugIfs['if_tem_placa_no_retorno']) === 'true' ? '<span class="badge bg-success">TRUE</span>' : '<span class="badge bg-danger">FALSE</span>' ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>if (saldo encontrado):</strong></td>
                                        <td><?= h($debugIfs['if_saldo_encontrado']) === 'true' ? '<span class="badge bg-success">TRUE</span>' : '<span class="badge bg-danger">FALSE</span>' ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Saldo cartão:</strong></td>
                                        <td><?= $debugIfs['saldo_cartao'] !== null ? '<span class="badge bg-primary">R$ ' . h($debugIfs['saldo_cartao']) . '</span>' : '<span class="badge bg-secondary">NULL</span>' ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>if (saldo &gt;= 40):</strong></td>
                                        <td><?= h($debugIfs['if_saldo_maior_igual_40']) === 'true' ? '<span class="badge bg-danger">TRUE</span>' : '<span class="badge bg-success">FALSE</span>' ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>UF do Colaborador:</strong></td>
                                        <td><span class="badge bg-secondary"><?= h($debugIfs['uf_colaborador']) ?: 'Não encontrada' ?></span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>UF esperada para operadora:</strong></td>
                                        <td><span class="badge bg-info"><?= h($debugIfs['uf_esperada']) ?></span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>UF correta para operadora?</strong></td>
                                        <td><?= h($debugIfs['uf_correta']) === 'true' ? '<span class="badge bg-success">✓ SIM</span>' : '<span class="badge bg-danger">✗ NÃO</span>' ?></td>
                                    </tr>
                                    <?php if ($debugIfs['uf_aviso'] !== ''): ?>
                                    <tr style="background-color: #fff3cd; border-left: 4px solid #ff6b6b;">
                                        <td colspan="2"><strong style="color: #d32f2f;">⚠️ AVISO:</strong> <span style="color: #d32f2f;"><?= h($debugIfs['uf_aviso']) ?></span></td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <footer class="py-4 bg-light mt-auto">
        <div class="container-fluid px-4">
            <div class="d-flex align-items-center justify-content-between small">
                <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
            </div>
        </div>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../src/js/scripts.js"></script>

<style>
    /* Estilos para a hierarquia */
    .card-header.bg-primary, 
    .card-header.bg-success, 
    .card-header.bg-info, 
    .card-header.bg-danger {
        font-weight: bold;
        text-transform: uppercase;
        font-size: 0.9rem;
    }

    .card.border-primary {
        border-left: 4px solid #0d6efd;
    }

    .card.border-success {
        border-left: 4px solid #198754;
    }

    .card.border-info {
        border-left: 4px solid #0dcaf0;
    }

    .card.border-danger {
        border-left: 4px solid #dc3545;
    }

    /* Estilos para os cards da hierarquia */
    .card.border-primary .card-body,
    .card.border-success .card-body,
    .card.border-info .card-body,
    .card.border-danger .card-body {
        background-color: #f8f9fa;
        padding: 1rem;
    }

    .card.border-primary .card-body small strong,
    .card.border-success .card-body small strong,
    .card.border-info .card-body small strong,
    .card.border-danger .card-body small strong {
        color: #212529;
    }

    /* Estilos para setas de conexão (visual) */
    .hierarquia-conectado {
        position: relative;
    }

    .hierarquia-conectado::after {
        content: '→';
        position: absolute;
        right: -20px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1.5rem;
        color: #6c757d;
    }

    /* Badges melhoradas */
    .badge {
        padding: 0.5rem 0.75rem;
        font-size: 0.85rem;
    }

    /* Responsividade */
    @media (max-width: 768px) {
        .card.border-primary,
        .card.border-success,
        .card.border-info,
        .card.border-danger {
            margin-bottom: 1rem;
        }
    }
</style>

</body>
</html>