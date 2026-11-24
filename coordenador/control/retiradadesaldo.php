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

$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

// Tentar carregar de ambos os caminhos possíveis
if (file_exists("../../conecta05.php")) {
    include "../../conecta05.php";
} elseif (file_exists("../conecta05.php")) {
    include "../conecta05.php";
} else {
    die("ERRO: Arquivo conecta05.php não encontrado");
}

header("Content-type: text/html; charset=utf-8");

// Verificar conexão
if (!isset($conexao05) || !$conexao05) {
    die("ERRO CRÍTICO: Conexão com banco de dados não foi estabelecida.");
}

if (!mysqli_ping($conexao05)) {
    die("ERRO CRÍTICO: Conexão com banco de dados perdida. Erro: " . mysqli_connect_error());
}

$matriculatec = $_POST['matriculatec'];
$saldoatual = $_POST['saldoatual']; // saldo atual no cartão
$saldoatual = str_replace(",", ".", $saldoatual);

$saldosup = $_POST['saldosup']; // saldo do coordenador
$saldosup = str_replace(",", ".", $saldosup);

$valor = $_POST['valor']; // valor retirado
$valor = str_replace(",", ".", $valor);

$idtbsaldo = $_POST['idtbsaldo']; // onde editar na tbsaldo
$codemptec = $_POST['codemptec']; // se é SP ou outro
$placa = $_POST['placa'];
$idtbcoordenador = $_POST['idtbcoordenador'];
$matriculacoord = $_POST['matriculacoord'];
$tipoacao = $_POST['tipoacao'];

// Validar valores
if ($valor <= 0) {
    echo "<script language='javascript' type='text/javascript'>alert('Valor inválido');window.location=\"../retiradadesaldo.php\"</script>";
    exit;
}

if ($tipoacao == 0) {
    // Remover saldo do técnico
    
    if ($valor > $saldoatual) {
        echo "<script language='javascript' type='text/javascript'>alert('Saldo Insuficiente no cartão do técnico! Saldo disponível: R$ " . number_format($saldoatual, 2, ',', '.') . "');window.location=\"../retiradadesaldo.php\"</script>";
        exit;
    }

    // Obter dados do saldo do técnico
    $stmt = mysqli_prepare($conexao05, "SELECT valoraplicado, kmproj, valrem, orcsemanal FROM bdfrota.tbsaldo WHERE idtbsaldo = ?");
    mysqli_stmt_bind_param($stmt, "i", $idtbsaldo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    
    if (!$resultado) {
        echo "<script language='javascript' type='text/javascript'>alert('Erro ao buscar saldo do técnico: " . mysqli_error($conexao05) . "');window.location=\"../retiradadesaldo.php\"</script>";
        exit;
    }

    $row = mysqli_fetch_array($resultado);
    $valoraplicado = $row[0];
    $kmproj = $row[1];
    $kmproj = round($kmproj, 2);
    $valrem = $row[2];
    $orcsemanal = $row[3];

    // Calcular novo valor aplicado
    $novovaloraplic = $valoraplicado - $valor;
    $novovalrem = $valrem - $valor;

    // Calcular novo kmproj
    if ($kmproj !== 0 && $valoraplicado !== 0) {
        $novokmproj = ($novovaloraplic * $kmproj) / $valoraplicado;
        $novokmproj = round($novokmproj, 2);
    } else {
        $novokmproj = $kmproj;
    }

    // Obter saldo do gerente responsável pelo coordenador
    $stmt = mysqli_prepare($conexao05, "SELECT idtbgerente FROM bdfrota.tbcoord where matricula = ?");
    mysqli_stmt_bind_param($stmt, "s", $matriculacoord);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    
    if (!$resultado) {
        echo "<script language='javascript' type='text/javascript'>alert('Erro ao buscar gerente: " . mysqli_error($conexao05) . "');window.location=\"../retiradadesaldo.php\"</script>";
        exit;
    }

    $row = mysqli_fetch_array($resultado);
    if (!$row) {
        echo "<script language='javascript' type='text/javascript'>alert('Coordenador não encontrado');window.location=\"../retiradadesaldo.php\"</script>";
        exit;
    }
    $idtbgerente = $row[0];

    // Obter dados do gerente
    $stmt = mysqli_prepare($conexao05, "SELECT matricula, valor FROM bdfrota.tbgerente WHERE idtbgerente = ?");
    mysqli_stmt_bind_param($stmt, "i", $idtbgerente);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    
    if (!$resultado) {
        echo "<script language='javascript' type='text/javascript'>alert('Erro ao buscar gerente: " . mysqli_error($conexao05) . "');window.location=\"../retiradadesaldo.php\"</script>";
        exit;
    }

    $row = mysqli_fetch_array($resultado);
    if (!$row) {
        echo "<script language='javascript' type='text/javascript'>alert('Gerente não encontrado');window.location=\"../retiradadesaldo.php\"</script>";
        exit;
    }

    $matriculagerente = $row[0];
    $valorgerente = $row[1];

    // Calcular novo saldo do gerente
    $novosaldogerente = $valorgerente + $valor;

    // Atualizar saldo do técnico
    $stmt = mysqli_prepare($conexao05, "UPDATE bdfrota.tbsaldo SET valoraplicado = ?, kmproj = ?, valrem = ? WHERE idtbsaldo = ?");
    mysqli_stmt_bind_param($stmt, "dddi", $novovaloraplic, $novokmproj, $novovalrem, $idtbsaldo);
    $resultado = mysqli_stmt_execute($stmt);
    
    if (!$resultado) {
        echo "<script language='javascript' type='text/javascript'>alert('Erro ao atualizar saldo do técnico: " . mysqli_error($conexao05) . "');window.location=\"../retiradadesaldo.php\"</script>";
        exit;
    }

    // Atualizar saldo do gerente
    $stmt = mysqli_prepare($conexao05, "UPDATE bdfrota.tbgerente SET valor = ? WHERE matricula = ?");
    mysqli_stmt_bind_param($stmt, "ds", $novosaldogerente, $matriculagerente);
    $resultado = mysqli_stmt_execute($stmt);
    
    if (!$resultado) {
        echo "<script language='javascript' type='text/javascript'>alert('Erro ao adicionar saldo ao gerente: " . mysqli_error($conexao05) . "');window.location=\"../retiradadesaldo.php\"</script>";
        exit;
    }

    // Registrar remanejamento
    $stmt = mysqli_prepare($conexao05, "INSERT INTO bdfrota.tbremanejamento(data_e_hora, matricula, matr_autor, valor, perfil_autor, tipo, valor_anterior) VALUES (?, ?, ?, ?, 2, 0, ?)");
    mysqli_stmt_bind_param($stmt, "sssds", $hoje, $matriculatec, $matricula, $valor, $saldoatual);
    $resultado = mysqli_stmt_execute($stmt);
    
    if (!$resultado) {
        echo "<script language='javascript' type='text/javascript'>alert('Erro ao registrar remanejamento: " . mysqli_error($conexao05) . "');window.location=\"../retiradadesaldo.php\"</script>";
        exit;
    }

    // ============================
    // ATUALIZAR SALDO DO CARTÃO
    // ============================
    $novoSaldoCartao = $saldoatual - $valor;
    
    // Tentar atualizar o saldo do cartão via API
    $atualizacaoCartao = atualizarSaldoCartao($codemptec, $placa, $valor);
    
    if (!$atualizacaoCartao) {
        echo "<script language='javascript' type='text/javascript'>alert('AVISO: Saldo retirado com sucesso, mas houve erro ao atualizar o cartão. Saldo anterior: R$ " . number_format($saldoatual, 2, ',', '.') . " | Novo saldo esperado: R$ " . number_format($novoSaldoCartao, 2, ',', '.') . "');window.location=\"../retiradadesaldo.php\"</script>";
        exit;
    }

    echo "<script language='javascript' type='text/javascript'>alert('Saldo retirado do técnico e transferido para o gerente com sucesso! Valor: R$ " . number_format($valor, 2, ',', '.') . " | Novo saldo do cartão: R$ " . number_format($novoSaldoCartao, 2, ',', '.') . "');window.location=\"../retiradadesaldo.php\"</script>";
} else {
    echo "<script language='javascript' type='text/javascript'>alert('Nenhuma ação encontrada');window.location=\"../retiradadesaldo.php\"</script>";
}

?>
