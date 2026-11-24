<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('America/Sao_Paulo');
session_start();

$hoje = date('Y-m-d H:i:s');

// ============================
// FUNÇÃO PARA ATUALIZAR CARTÃO
// ============================
function atualizarSaldoCartao($codemptec, $placa, $valor) {
    // Se for Valecard (código 2)
    if ($codemptec == '2') {
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
            "tipoAlteracao" : "diminuir",
            "periodo" : "atual",
            "motivo" : "Sistema Combustivel",
            "modulo" : "abastecimento"
        }';

        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        $result = json_decode(curl_exec($curl));
        curl_close($curl);
        
        return isset($result->sucesso) ? $result->sucesso : true;
    } else {
        // Se for Ticketlog
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
        curl_close($curl);

        $numeroCartao = null;
        if (isset($result->itens) && count($result->itens)) {
            foreach ($result->itens as $itens) {
                if ($itens->situacao == 'A') {
                    $numeroCartao = $itens->numeroCartao;
                    break;
                }
            }
        }

        if (!$numeroCartao) {
            return false;
        }

        // Agora atualizar o limite do cartão
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
                "numeroCartao": "' . $numeroCartao . '",
                "valorLimite": ' . $valor . ',
                "valorLimiteProxPeriodo": null,
                "mensagem": "Controle Combustivel"
                }
            ]
        }';

        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        $result = json_decode(curl_exec($curl));
        curl_close($curl);

        return true;
    }
}

$nome = $_SESSION['nome'] ?? '';
$usuariof = $_SESSION['usuario'] ?? '';
$matricula = $_SESSION['matricula'] ?? '';
$perfil = $_SESSION['perfil'] ?? null;

if ((string) $perfil !== '3' || $matricula === '') {
    echo "<script>alert('Você não tem permissão');window.location='../retiradadesaldo.php'</script>";
    exit;
}

include '../../conecta05.php';
header('Content-type: text/html; charset=utf-8');

function voltarComMensagem($mensagem, $coordenadorSelecionado = null) {
    $urlRetorno = '../retiradadesaldo.php';
    if ($coordenadorSelecionado) {
        $urlRetorno .= '?coordenador=' . (int) $coordenadorSelecionado;
    }
    echo '<script>alert(' . json_encode($mensagem) . ');window.location=' . json_encode($urlRetorno) . '</script>';
    exit;
}

function converterMoeda($valor) {
    $valor = trim((string) $valor);
    if (strpos($valor, ',') !== false) {
        $valor = str_replace('.', '', $valor);
        $valor = str_replace(',', '.', $valor);
    }
    return filter_var($valor, FILTER_VALIDATE_FLOAT);
}

function ehValorInteiro($valor) {
    return abs($valor - round($valor)) < 0.00001;
}

$matriculatec = $_POST['matriculatec'] ?? '';
$coordenadorSelecionado = filter_input(INPUT_POST, 'coordenador', FILTER_VALIDATE_INT);
$idtbsaldo = filter_input(INPUT_POST, 'idtbsaldo', FILTER_VALIDATE_INT);
$valor = converterMoeda($_POST['valor'] ?? '');
$saldoatual = converterMoeda($_POST['saldoatual'] ?? '');

if (!$idtbsaldo || $valor === false || $valor <= 0 || $saldoatual === false) {
    voltarComMensagem('Dados inválidos para a retirada.', $coordenadorSelecionado);
}
if (!ehValorInteiro($valor)) {
    voltarComMensagem('Para retirada, informe apenas valores inteiros (sem centavos).', $coordenadorSelecionado);
}
if ($valor > $saldoatual) {
    voltarComMensagem('Saldo insuficiente no cartão do técnico. Saldo disponível: R$ ' . number_format($saldoatual, 2, ',', '.'), $coordenadorSelecionado);
}

// A consulta também garante que o técnico pertence a um coordenador deste gerente.
$sql = "SELECT s.valoraplicado, s.kmproj, s.valrem, f.codempresa, v.placa
        FROM bdfrota.tbgerente g
        INNER JOIN bdfrota.tbcoord c ON c.idtbgerente = g.idtbgerente
        INNER JOIN bdfrota.tbsupervisor sup ON sup.idtbcoordenador = c.idtbcoordenador
        INNER JOIN bdfrota.tbusuario u ON u.idtbsupervisor = sup.idtbsupervisor AND u.perfil = 0
        INNER JOIN bdaniel.tbfuncionario f ON f.matricula = u.matricula
        INNER JOIN bdfrota.tbveiculo v ON v.matcond = u.matricula
        INNER JOIN bdfrota.tbsaldo s ON s.matricula = u.matricula
        INNER JOIN (SELECT matricula, MAX(idtbsaldo) AS idtbsaldo FROM bdfrota.tbsaldo GROUP BY matricula) atual
            ON atual.idtbsaldo = s.idtbsaldo
        WHERE g.matricula = ? AND u.matricula = ? AND s.idtbsaldo = ?";
$stmt = mysqli_prepare($conexao05, $sql);
mysqli_stmt_bind_param($stmt, 'ssi', $matricula, $matriculatec, $idtbsaldo);
mysqli_stmt_execute($stmt);
$tecnico = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$tecnico) {
    voltarComMensagem('Técnico não encontrado ou não vinculado à sua gerência.', $coordenadorSelecionado);
}

$valorAplicado = (float) $tecnico['valoraplicado'];
$kmProjetado = (float) $tecnico['kmproj'];
$novoValorAplicado = $valorAplicado - $valor;
$novoValorRemanescente = (float) $tecnico['valrem'] - $valor;
$novoKmProjetado = ($kmProjetado != 0 && $valorAplicado != 0)
    ? round(($novoValorAplicado * $kmProjetado) / $valorAplicado, 2)
    : $kmProjetado;

mysqli_begin_transaction($conexao05);
try {
    $stmt = mysqli_prepare($conexao05, 'UPDATE bdfrota.tbsaldo SET valoraplicado = ?, kmproj = ?, valrem = ? WHERE idtbsaldo = ?');
    mysqli_stmt_bind_param($stmt, 'dddi', $novoValorAplicado, $novoKmProjetado, $novoValorRemanescente, $idtbsaldo);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception(mysqli_stmt_error($stmt));
    }

    $stmt = mysqli_prepare($conexao05, 'UPDATE bdfrota.tbgerente SET valor = COALESCE(valor, 0) + ? WHERE matricula = ?');
    mysqli_stmt_bind_param($stmt, 'ds', $valor, $matricula);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Não foi possível atualizar o saldo do gerente.');
    }

    $stmt = mysqli_prepare($conexao05, 'INSERT INTO bdfrota.tbremanejamento (data_e_hora, matricula, matr_autor, valor, perfil_autor, tipo, valor_anterior) VALUES (?, ?, ?, ?, 3, 0, ?)');
    mysqli_stmt_bind_param($stmt, 'sssdd', $hoje, $matriculatec, $matricula, $valor, $saldoatual);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception(mysqli_stmt_error($stmt));
    }
    mysqli_commit($conexao05);
} catch (Exception $e) {
    mysqli_rollback($conexao05);
    voltarComMensagem('Não foi possível concluir a retirada: ' . $e->getMessage(), $coordenadorSelecionado);
}

$cartaoAtualizado = atualizarSaldoCartao($tecnico['codempresa'], $tecnico['placa'], $valor);
$novoSaldoCartao = $saldoatual - $valor;
if (!$cartaoAtualizado) {
    voltarComMensagem('Saldo registrado, mas não foi possível atualizar o cartão. Contate o suporte.', $coordenadorSelecionado);
}

voltarComMensagem('Saldo retirado e transferido para o gerente com sucesso! Valor: R$ '
    . number_format($valor, 2, ',', '.') . ' | Novo saldo do cartão: R$ '
    . number_format($novoSaldoCartao, 2, ',', '.'), $coordenadorSelecionado);