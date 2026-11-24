<?php
/**
 * ====================================================================
 * VERIFICADOR DE STATUS DE PEDIDOS - PROIBIDO / INDEVIDO / DEVIDO
 * ====================================================================
 * 
 * Este arquivo verifica se um pedido de cota extra seria classificado como:
 * - PROIBIDO: Saldo do cartão >= R$ 40,00
 * - INDEVIDO: Saldo < R$ 40,00 E KM rodado < 75% do projetado
 * - DEVIDO: Saldo < R$ 40,00 E KM rodado >= 75% do projetado
 * 
 * Consulta tanto o banco de dados quanto as APIs externas (Valecard/Ticketlog)
 * ====================================================================
 */

setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();

// Verifica autenticação
$nome = $_SESSION['nome'] ?? null;
$usuariof = $_SESSION['usuario'] ?? null;
$matricula_logado = $_SESSION['matricula'] ?? null;
$perfil = $_SESSION['perfil'] ?? null;

if ($perfil == null) {
    echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
    exit();
}

include "conecta.php";
header("Content-type: text/html; charset=utf-8");

// Pega a matrícula a ser verificada (do POST ou do GET)
$matricula_verificar = isset($_POST['matricula']) ? $_POST['matricula'] : (isset($_GET['matricula']) ? $_GET['matricula'] : '');
$verificar = isset($_POST['verificar']) || isset($_GET['verificar']);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Verificador de Status de Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v6.1.0/css/all.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 1200px;
            margin-top: 30px;
        }
        .card {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border: none;
            border-radius: 10px;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 20px;
        }
        .badge-proibido {
            background-color: #dc3545;
            font-size: 1.2rem;
            padding: 10px 20px;
        }
        .badge-indevido {
            background-color: #ffc107;
            color: #000;
            font-size: 1.2rem;
            padding: 10px 20px;
        }
        .badge-devido {
            background-color: #28a745;
            font-size: 1.2rem;
            padding: 10px 20px;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
        .valor-destaque {
            font-size: 1.3rem;
            font-weight: bold;
            color: #667eea;
        }
        .table-resultado {
            margin-top: 20px;
        }
        .status-icon {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h3 class="mb-0">
                <i class="fas fa-search"></i> Verificador de Status de Pedidos de Cota Extra
            </h3>
            <small>Consulta dados do banco + APIs (Valecard/Ticketlog)</small>
        </div>
        <div class="card-body">
            
            <!-- Formulário de busca -->
            <form method="POST" action="" id="formVerificar">
                <div class="row">
                    <div class="col-md-8">
                        <label for="matricula" class="form-label"><strong>Matrícula do Técnico:</strong></label>
                        <input type="text" class="form-control form-control-lg" name="matricula" id="matricula" 
                               placeholder="Digite a matrícula (ex: 12345)" 
                               value="<?= htmlspecialchars($matricula_verificar) ?>" required>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" name="verificar" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-search"></i> Verificar Status
                        </button>
                    </div>
                </div>
            </form>

            <div class="loading" id="loading">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="mt-2">Consultando banco de dados e APIs...</p>
            </div>

            <?php if ($verificar && !empty($matricula_verificar)): ?>
                
                <?php
                // ====================================================================
                // Define o período da semana atual (segunda a domingo)
                // ====================================================================
                if (strftime('%A') == "domingo") {
                    $segunda = date('Y-m-d', strtotime('last monday'));
                    $domingo = date('Y-m-d');
                } elseif (strftime('%A') == "segunda") {
                    $segunda = date('Y-m-d');
                    $domingo = date('Y-m-d', strtotime('next sunday'));
                } else {
                    $segunda = date('Y-m-d', strtotime('last monday'));
                    $domingo = date('Y-m-d', strtotime('next sunday'));
                }
                
                // ====================================================================
                // BUSCA DADOS DO BANCO DE DADOS
                // ====================================================================
                
                $sql = "SELECT 
                            f.matricula,
                            f.nome,
                            f.codempresa,
                            v.placa,
                            v.unidade,
                            s.kmproj,
                            s.kmosatual,
                            s.orcsemanal,
                            s.saldo AS saldo_bd,
                            s.valoraplicado,
                            s.totalextra,
                            s.data AS data_saldo
                        FROM bdaniel.tbfuncionario f
                        INNER JOIN bdfrota.tbveiculo v ON f.matricula = v.matcond
                        LEFT JOIN bdfrota.tbsaldo s ON f.matricula = s.matricula
                        WHERE f.matricula = '$matricula_verificar'
                          AND v.placa IS NOT NULL
                          AND v.placa <> ''
                        ORDER BY s.data DESC
                        LIMIT 1";
                
                $resultado = mysql_query($sql) or die(mysql_error());
                
                if (mysql_num_rows($resultado) > 0):
                    $row = mysql_fetch_array($resultado);
                    
                    $nome_tecnico = $row['nome'];
                    $placa = $row['placa'];
                    $unidade = $row['unidade'];
                    $kmproj = $row['kmproj'];
                    $kmosatual = $row['kmosatual'];
                    $orcsemanal = $row['orcsemanal'];
                    $saldo_bd = $row['saldo_bd'];
                    $valoraplicado = $row['valoraplicado'];
                    $totalextra = $row['totalextra'];
                    $data_saldo = $row['data_saldo'];
                    
                    // ====================================================================
                    // CALCULA VALOR RECEBIDO (Orçamento Semanal + Cotas Extras da Semana)
                    // ====================================================================
                    $sql_valor_recebido = "SELECT
                                ROUND(COALESCE(ts.orcsemanal, 0) + COALESCE(cota_semana.total_cotas, 0), 2) AS valor_recebido_semana
                            FROM (
                                SELECT *
                                FROM bdfrota.tbsaldo
                                WHERE matricula = '$matricula_verificar'
                                ORDER BY data DESC
                                LIMIT 1
                            ) ts
                            LEFT JOIN (
                                SELECT 
                                    matricula,
                                    SUM(valorinserido) AS total_cotas
                                FROM bdfrota.tbcotaextraac
                                WHERE matricula = '$matricula_verificar'
                                  AND data >= '$segunda 00:00:00'
                                  AND data <= '$domingo 23:59:59'
                                GROUP BY matricula
                            ) cota_semana ON ts.matricula = cota_semana.matricula";
                    
                    $resultado_valor = mysql_query($sql_valor_recebido) or die(mysql_error());
                    $row_valor = mysql_fetch_array($resultado_valor);
                    $valor_recebido_semana = $row_valor['valor_recebido_semana'] ?? 0;
                    
                    // ====================================================================
                    // CONSULTA A API PARA OBTER SALDO DO CARTÃO
                    // ====================================================================
                    
                    $saldocartao = 0;
                    $saldoct = 0;
                    $api_consultada = '';
                    $erro_api = '';
                    
                    if ($unidade != 'RJ' && $unidade != '') {
                        // ===== API VALECARD =====
                        $api_consultada = 'Valecard';
                        
                        $curl = curl_init();
                        curl_setopt($curl, CURLOPT_URL, 'https://kissapi.valecard.com.br/core/services/frotaCliente/buscarBasedeCartoes');
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($curl, CURLOPT_POST, TRUE);
                        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                            'Content-Type: application/json',
                            'Authorization: Basic NTI0MDczQHdlYlNlcnZpY2U6MDY5NDc=',
                        ));
                        curl_setopt($curl, CURLOPT_HEADER, 0);
                        
                        $body = '{
                            "contratoCliente": "524073",
                            "numeroCartao": null,
                            "placa": "' . $placa . '",
                            "statusCartao": "A"
                        }';
                        
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
                        $result = json_decode(curl_exec($curl));
                        
                        if (count($result)) {
                            foreach ($result as $itens) {
                                if ($itens->statusCartao == 'A') {
                                    $saldoct = $itens->saldo;
                                    $saldocartao = number_format($saldoct, 2, ',', '.');
                                }
                            }
                        }
                        
                        if ($saldoct == 0) {
                            $erro_api = 'Cartão não encontrado ou inativo na Valecard';
                        }
                        
                    } else {
                        // ===== API TICKETLOG =====
                        $api_consultada = 'Ticketlog';
                        
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
                            "informacoesAdicionais": [{
                                "labelIA": null,
                                "codigoIA": null,
                                "descricaoIA": null
                            }],
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
                        
                        if (isset($result->itens) && count($result->itens)) {
                            foreach ($result->itens as $itens) {
                                if ($itens->situacao == 'A') {
                                    $saldoct = $itens->saldo;
                                    $saldocartao = number_format($saldoct, 2, ',', '.');
                                }
                            }
                        }
                        
                        if ($saldoct == 0) {
                            $erro_api = 'Cartão não encontrado ou inativo no Ticketlog';
                        }
                    }
                    
                    // ====================================================================
                    // APLICA A LÓGICA DE CLASSIFICAÇÃO
                    // ====================================================================
                    
                    $status = '';
                    $status_classe = '';
                    $status_icone = '';
                    $status_explicacao = '';
                    
                    if ($saldoct >= 40) {
                        $status = 'PROIBIDO';
                        $status_classe = 'badge-proibido';
                        $status_icone = '🔴';
                        $status_explicacao = 'O técnico possui saldo no cartão maior ou igual a R$ 40,00. A solicitação de cota extra é PROIBIDA.';
                    } elseif ($kmproj > ($kmosatual * 0.75)) {
                        $status = 'INDEVIDO';
                        $status_classe = 'badge-indevido';
                        $status_icone = '⚠️';
                        $status_explicacao = 'O técnico rodou menos de 75% do KM projetado. A cota extra é considerada INDEVIDA.';
                    } else {
                        $status = 'DEVIDO';
                        $status_classe = 'badge-devido';
                        $status_icone = '✅';
                        $status_explicacao = 'O técnico está sem saldo no cartão (< R$ 40,00) e rodou mais de 75% do KM projetado. A cota extra é DEVIDA.';
                    }
                    
                    $km_75_porcento = $kmosatual * 0.75;
                    $percentual_rodado = ($kmosatual > 0) ? round(($kmosatual / $kmproj) * 100, 2) : 0;
                ?>
                
                <!-- Resultado da Verificação -->
                <hr class="my-4">
                
                <!-- Card de Resumo Rápido -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">Valor Recebido</h6>
                                <h4 class="card-title text-primary">R$ <?= number_format($valor_recebido_semana, 2, ',', '.') ?></h4>
                                <small class="text-muted">Semana Atual</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">Saldo Cartão</h6>
                                <h4 class="card-title <?= $saldoct >= 40 ? 'text-danger' : 'text-success' ?>">
                                    R$ <?= $saldocartao ?>
                                </h4>
                                <small class="text-muted">API (<?= $api_consultada ?>)</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">KM Rodado</h6>
                                <h4 class="card-title text-info"><?= number_format($kmosatual, 0, ',', '.') ?> km</h4>
                                <small class="text-muted"><?= $percentual_rodado ?>% do projetado</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">Data Saldo</h6>
                                <h4 class="card-title text-secondary" style="font-size: 1.3rem;">
                                    <?= !empty($data_saldo) ? date('d/m/Y', strtotime($data_saldo)) : 'N/A' ?>
                                </h4>
                                <small class="text-muted">
                                    <?= !empty($data_saldo) ? strftime('%A', strtotime($data_saldo)) : '' ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center my-4">
                    <div class="status-icon"><?= $status_icone ?></div>
                    <h2>Status do Pedido:</h2>
                    <span class="badge <?= $status_classe ?>"><?= $status ?></span>
                </div>
                
                <div class="info-box">
                    <strong>Explicação:</strong><br>
                    <?= $status_explicacao ?>
                </div>
                
                <!-- Dados do Técnico -->
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-user"></i> Dados do Técnico</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Matrícula:</strong> <?= $matricula_verificar ?></p>
                                <p><strong>Nome:</strong> <?= $nome_tecnico ?></p>
                                <p><strong>Placa:</strong> <?= $placa ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Unidade:</strong> <?= $unidade ?></p>
                                <p><strong>API Consultada:</strong> <?= $api_consultada ?></p>
                                <p><strong>Data do Saldo:</strong> 
                                    <?php 
                                    if (!empty($data_saldo)) {
                                        $data_formatada = date('d/m/Y', strtotime($data_saldo));
                                        $dia_semana = strftime('%A', strtotime($data_saldo));
                                        echo "$data_formatada ($dia_semana)";
                                    } else {
                                        echo "Não informado";
                                    }
                                    ?>
                                </p>
                                <p><strong>Semana Atual:</strong> 
                                    <?= date('d/m', strtotime($segunda)) ?> a <?= date('d/m/Y', strtotime($domingo)) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tabela de Análise -->
                <table class="table table-bordered table-striped table-resultado">
                    <thead class="table-dark">
                        <tr>
                            <th colspan="3" class="text-center">Análise de Dados</th>
                        </tr>
                        <tr>
                            <th style="width: 40%;">Item</th>
                            <th style="width: 30%;">Valor</th>
                            <th style="width: 30%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Saldo do Cartão (API) -->
                        <tr class="<?= $saldoct >= 40 ? 'table-danger' : 'table-success' ?>">
                            <td><strong>💳 Saldo do Cartão (API)</strong></td>
                            <td class="valor-destaque">R$ <?= $saldocartao ?></td>
                            <td>
                                <?php if ($saldoct >= 40): ?>
                                    <span class="badge bg-danger">≥ R$ 40,00 → PROIBIDO</span>
                                <?php else: ?>
                                    <span class="badge bg-success">< R$ 40,00 → OK</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        
                        <?php if (!empty($erro_api)): ?>
                        <tr class="table-warning">
                            <td colspan="3">
                                <i class="fas fa-exclamation-triangle"></i> <strong>Atenção:</strong> <?= $erro_api ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                        
                        <!-- KM Projetado -->
                        <tr>
                            <td><strong>📊 KM Projetado</strong></td>
                            <td class="valor-destaque"><?= number_format($kmproj, 0, ',', '.') ?> km</td>
                            <td>-</td>
                        </tr>
                        
                        <!-- KM OS Atual -->
                        <tr>
                            <td><strong>🚗 KM OS Atual (Rodado)</strong></td>
                            <td class="valor-destaque"><?= number_format($kmosatual, 0, ',', '.') ?> km</td>
                            <td><span class="badge bg-info"><?= $percentual_rodado ?>% do projetado</span></td>
                        </tr>
                        
                        <!-- 75% do KM Projetado -->
                        <tr>
                            <td><strong>📏 75% do KM Projetado</strong></td>
                            <td class="valor-destaque"><?= number_format($km_75_porcento, 0, ',', '.') ?> km</td>
                            <td>-</td>
                        </tr>
                        
                        <!-- Análise KM -->
                        <tr class="<?= $kmproj > ($kmosatual * 0.75) ? 'table-warning' : 'table-success' ?>">
                            <td><strong>📈 Análise de KM</strong></td>
                            <td colspan="2">
                                <?php if ($kmproj > ($kmosatual * 0.75)): ?>
                                    <span class="badge bg-warning text-dark">Rodou < 75% → INDEVIDO</span>
                                    <br><small>KM Projetado (<?= number_format($kmproj, 0) ?>) > 75% do KM Rodado (<?= number_format($km_75_porcento, 0) ?>)</small>
                                <?php else: ?>
                                    <span class="badge bg-success">Rodou ≥ 75% → OK</span>
                                    <br><small>KM Projetado (<?= number_format($kmproj, 0) ?>) ≤ 75% do KM Rodado (<?= number_format($km_75_porcento, 0) ?>)</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                        
                        <!-- Separador -->
                        <tr class="table-secondary">
                            <td colspan="3" class="text-center"><strong>💰 VALORES FINANCEIROS</strong></td>
                        </tr>
                        
                        <!-- Valor Recebido (Orç + Cotas da Semana) -->
                        <tr class="table-info">
                            <td><strong>💵 Valor Recebido (Semana Atual)</strong></td>
                            <td class="valor-destaque">R$ <?= number_format($valor_recebido_semana, 2, ',', '.') ?></td>
                            <td><span class="badge bg-primary">Orç + Cotas da Semana</span></td>
                        </tr>
                        
                        <!-- Orçamento Semanal -->
                        <tr>
                            <td><strong>📅 Orçamento Semanal</strong></td>
                            <td class="valor-destaque">R$ <?= number_format($orcsemanal, 2, ',', '.') ?></td>
                            <td><small>(Orçamento inicial da semana)</small></td>
                        </tr>
                        
                        <!-- Total Extra -->
                        <tr>
                            <td><strong>➕ Total Extra Recebido</strong></td>
                            <td class="valor-destaque">R$ <?= number_format($totalextra, 2, ',', '.') ?></td>
                            <td><small>(Todas as cotas extras aprovadas)</small></td>
                        </tr>
                        
                        <!-- Saldo BD -->
                        <tr>
                            <td><strong>💰 Saldo no BD</strong></td>
                            <td class="valor-destaque">R$ <?= number_format($saldo_bd, 2, ',', '.') ?></td>
                            <td><small>(Saldo interno do sistema)</small></td>
                        </tr>
                        
                        <!-- Valor Aplicado -->
                        <tr>
                            <td><strong>� Valor Aplicado</strong></td>
                            <td class="valor-destaque">R$ <?= number_format($valoraplicado, 2, ',', '.') ?></td>
                            <td><small>(Valor total aplicado)</small></td>
                        </tr>
                    </tbody>
                </table>
                
                <!-- Regras Aplicadas -->
                <div class="card mt-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-book"></i> Regras de Classificação</h5>
                    </div>
                    <div class="card-body">
                        <ol>
                            <li class="mb-2">
                                <strong>🔴 PROIBIDO:</strong> Se saldo do cartão ≥ R$ 40,00
                                <br><small class="text-muted">O técnico ainda tem saldo suficiente, não deveria receber cota extra.</small>
                            </li>
                            <li class="mb-2">
                                <strong>⚠️ INDEVIDO:</strong> Se saldo < R$ 40,00 E KM Projetado > (KM Rodado × 0.75)
                                <br><small class="text-muted">O técnico rodou menos de 75% do esperado, cota pode ser questionável.</small>
                            </li>
                            <li class="mb-2">
                                <strong>✅ DEVIDO:</strong> Se saldo < R$ 40,00 E KM Projetado ≤ (KM Rodado × 0.75)
                                <br><small class="text-muted">O técnico está sem saldo e rodou o suficiente, cota é devida.</small>
                            </li>
                        </ol>
                    </div>
                </div>
                
                <?php else: ?>
                    
                    <div class="alert alert-warning mt-4" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <strong>Matrícula não encontrada!</strong>
                        <p class="mb-0">Não foram encontrados registros para a matrícula <strong><?= htmlspecialchars($matricula_verificar) ?></strong>.</p>
                        <p class="mb-0">Verifique se a matrícula está correta e se o técnico possui veículo vinculado.</p>
                    </div>
                    
                <?php endif; ?>
                
            <?php endif; ?>
            
        </div>
    </div>
    
    <div class="text-center mt-3 mb-5">
        <a href="dashboard.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Mostra loading ao submeter
    document.getElementById('formVerificar').addEventListener('submit', function() {
        document.getElementById('loading').style.display = 'block';
    });
</script>

</body>
</html>
