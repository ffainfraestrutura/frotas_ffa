<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('America/Sao_Paulo');
session_start();

$hoje = date('Y-m-d H:i:s');

function atualizarSaldoCartao($codemptec, $placa, $valor)
{
    if ($codemptec == '2') {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://kissapi.valecard.com.br/core/services/frotaCliente/alterarLimiteveiculo');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, true);
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
    }

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/relatorioExtratoSimplificado/search');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, true);
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

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/usuarioCartaoLimite');
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
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
    curl_exec($curl);
    curl_close($curl);

    return true;
}

$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

if (file_exists('../../conecta05.php')) {
    include '../../conecta05.php';
} elseif (file_exists('../conecta05.php')) {
    include '../conecta05.php';
} else {
    die('ERRO: Arquivo conecta05.php não encontrado');
}

header('Content-type: text/html; charset=utf-8');

if (!isset($conexao05) || !$conexao05) {
    die('ERRO CRÍTICO: Conexão com banco de dados não foi estabelecida.');
}

// Função para enviar log usando mysqli
function enviarlog($data_hora, $acao, $flag, $matricula_func, $matricula_autor, $saldo_anterior, $valor) {
    global $conexao05;
    
    $stmt = mysqli_prepare($conexao05, 'INSERT INTO bdfrota.tblog(data_e_hora, acao, flag, matricula, matricula_autor, saldo_anterior, valor) VALUES (?, ?, ?, ?, ?, ?, ?)');
    
    if (!$stmt) {
        error_log('Erro ao preparar statement de log: ' . mysqli_error($conexao05));
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, 'ssissds', $data_hora, $acao, $flag, $matricula_func, $matricula_autor, $saldo_anterior, $valor);
    
    if (!mysqli_stmt_execute($stmt)) {
        error_log('Erro ao inserir log: ' . mysqli_error($conexao05));
        mysqli_stmt_close($stmt);
        return false;
    }
    
    mysqli_stmt_close($stmt);
    return true;
}

if (!mysqli_ping($conexao05)) {
    die('ERRO CRÍTICO: Conexão com banco de dados perdida. Erro: ' . mysqli_connect_error());
}

$matriculatec = $_POST['matriculatec'];
$saldoatual = str_replace(',', '.', $_POST['saldoatual']);
$valor = str_replace(',', '.', $_POST['valor']);
$idtbsaldo = $_POST['idtbsaldo'];
$codemptec = $_POST['codemptec'];
$placa = $_POST['placa'];
$idtbsupervisor = $_POST['idtbsupervisor'];
$tipoacao = $_POST['tipoacao'];

if ($valor <= 0) {
    echo "<script language='javascript' type='text/javascript'>alert('Valor inválido');window.location=\"../retiradadesaldosup.php\"</script>";
    exit;
}

if ($tipoacao == 0) {
    if ($valor > $saldoatual) {
        echo "<script language='javascript' type='text/javascript'>alert('Saldo Insuficiente no cartão do técnico! Saldo disponível: R$ " . number_format($saldoatual, 2, ',', '.') . "');window.location=\"../retiradadesaldosup.php\"</script>";
        exit;
    }

    $stmt = mysqli_prepare($conexao05, 'SELECT valoraplicado, kmproj, valrem FROM bdfrota.tbsaldo WHERE idtbsaldo = ?');
    mysqli_stmt_bind_param($stmt, 'i', $idtbsaldo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if (!$resultado) {
        echo "<script language='javascript' type='text/javascript'>alert('Erro ao buscar saldo do técnico: " . mysqli_error($conexao05) . "');window.location=\"../retiradadesaldosup.php\"</script>";
        exit;
    }

    $row = mysqli_fetch_array($resultado);
    $valoraplicado = $row[0];
    $kmproj = round($row[1], 2);
    $valrem = $row[2];

    $novovaloraplic = $valoraplicado - $valor;
    $novovalrem = $valrem - $valor;

    if ($kmproj !== 0 && $valoraplicado !== 0) {
        $novokmproj = round(($novovaloraplic * $kmproj) / $valoraplicado, 2);
    } else {
        $novokmproj = $kmproj;
    }

    $stmt = mysqli_prepare($conexao05, 'SELECT idtbcoordenador FROM bdfrota.tbsupervisor WHERE idtbsupervisor = ?');
    mysqli_stmt_bind_param($stmt, 'i', $idtbsupervisor);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if (!$resultado || !($row = mysqli_fetch_array($resultado))) {
        echo "<script language='javascript' type='text/javascript'>alert('Coordenador do supervisor não encontrado');window.location=\"../retiradadesaldosup.php\"</script>";
        exit;
    }
    $idtbcoordenador = $row[0];

    $stmt = mysqli_prepare($conexao05, 'SELECT idtbgerente FROM bdfrota.tbcoord WHERE idtbcoordenador = ?');
    mysqli_stmt_bind_param($stmt, 'i', $idtbcoordenador);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if (!$resultado || !($row = mysqli_fetch_array($resultado))) {
        echo "<script language='javascript' type='text/javascript'>alert('Gerente não encontrado para o coordenador');window.location=\"../retiradadesaldosup.php\"</script>";
        exit;
    }
    $idtbgerente = $row[0];

    $stmt = mysqli_prepare($conexao05, 'SELECT matricula, valor FROM bdfrota.tbgerente WHERE idtbgerente = ?');
    mysqli_stmt_bind_param($stmt, 'i', $idtbgerente);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if (!$resultado || !($row = mysqli_fetch_array($resultado))) {
        echo "<script language='javascript' type='text/javascript'>alert('Dados do gerente não encontrados');window.location=\"../retiradadesaldosup.php\"</script>";
        exit;
    }

    $matriculagerente = $row[0];
    $novosaldogerente = $row[1] + $valor;

    $stmt = mysqli_prepare($conexao05, 'UPDATE bdfrota.tbsaldo SET valoraplicado = ?, kmproj = ?, valrem = ? WHERE idtbsaldo = ?');
    mysqli_stmt_bind_param($stmt, 'dddi', $novovaloraplic, $novokmproj, $novovalrem, $idtbsaldo);
    if (!mysqli_stmt_execute($stmt)) {
        echo "<script language='javascript' type='text/javascript'>alert('Erro ao atualizar saldo do técnico: " . mysqli_error($conexao05) . "');window.location=\"../retiradadesaldosup.php\"</script>";
        exit;
    }

    $stmt = mysqli_prepare($conexao05, 'UPDATE bdfrota.tbgerente SET valor = ? WHERE matricula = ?');
    mysqli_stmt_bind_param($stmt, 'ds', $novosaldogerente, $matriculagerente);
    if (!mysqli_stmt_execute($stmt)) {
        echo "<script language='javascript' type='text/javascript'>alert('Erro ao adicionar saldo ao gerente: " . mysqli_error($conexao05) . "');window.location=\"../retiradadesaldosup.php\"</script>";
        exit;
    }

    $stmt = mysqli_prepare($conexao05, 'INSERT INTO bdfrota.tbremanejamento(data_e_hora, matricula, matr_autor, valor, perfil_autor, tipo, valor_anterior) VALUES (?, ?, ?, ?, 1, 0, ?)');
    mysqli_stmt_bind_param($stmt, 'sssds', $hoje, $matriculatec, $matricula, $valor, $saldoatual);
    if (!mysqli_stmt_execute($stmt)) {
        echo "<script language='javascript' type='text/javascript'>alert('Erro ao registrar remanejamento: " . mysqli_error($conexao05) . "');window.location=\"../retiradadesaldosup.php\"</script>";
        exit;
    }

    $acao = 'Remanejou cota - retirou';
    $flag = '2';
    enviarlog($hoje, $acao, $flag, $matriculatec, $matricula, $saldoatual, $valor);

    $novoSaldoCartao = $saldoatual - $valor;
    $atualizacaoCartao = atualizarSaldoCartao($codemptec, $placa, $valor);

    if (!$atualizacaoCartao) {
        echo "<script language='javascript' type='text/javascript'>alert('AVISO: Saldo retirado com sucesso, mas houve erro ao atualizar o cartão. Saldo anterior: R$ " . number_format($saldoatual, 2, ',', '.') . " | Novo saldo esperado: R$ " . number_format($novoSaldoCartao, 2, ',', '.') . "');window.location=\"../retiradadesaldosup.php\"</script>";
        exit;
    }

    echo "<script language='javascript' type='text/javascript'>alert('Saldo retirado do técnico e transferido para o gerente com sucesso! Valor: R$ " . number_format($valor, 2, ',', '.') . " | Novo saldo do cartão: R$ " . number_format($novoSaldoCartao, 2, ',', '.') . "');window.location=\"../retiradadesaldosup.php\"</script>";
} else {
    echo "<script language='javascript' type='text/javascript'>alert('Nenhuma ação encontrada');window.location=\"../retiradadesaldosup.php\"</script>";
}
// testado e ok
?>