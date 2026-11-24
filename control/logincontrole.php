<?php
date_default_timezone_set('America/Sao_Paulo');
$hoje = date('Y-m-d H:i:s');
session_start();
include("../conecta.php");
include_once "../func/log.php";
include_once "../classes/PresencaService.php";
include_once "../classes/PresencaPolicy.php";

if (!function_exists('validarLancamentoPresencaTecnico')) {
    function validarLancamentoPresencaTecnico($matricula, $connBdPonto)
    {
        $matricula = trim((string) $matricula);
        $dataReferencia = new DateTime('now');

        $servicoPresenca = new PresencaService($connBdPonto);
        $registroPresenca = $servicoPresenca->buscarUltimaPresencaDoDia($matricula, $dataReferencia);

        $politicaPresenca = new PresencaPolicy();
        $resultado = $politicaPresenca->avaliar($registroPresenca, $dataReferencia);

        if (!empty($resultado['permitido'])) {
            return true;
        }

        $mensagem = isset($resultado['mensagem']) && trim((string) $resultado['mensagem']) !== ''
            ? trim((string) $resultado['mensagem'])
            : 'Você será deslogado. Entre em contato com seu supervisor.';
        $motivo = isset($resultado['motivo']) ? (string) $resultado['motivo'] : 'presenca_nao_permitida';
        $acaoLog = 'deslogou - ' . $motivo;

        if ($motivo === 'sem_presenca') {
            $acaoLog = 'deslogado por presenca nao informada';
        } elseif ($motivo === 'situacao_diferente') {
            $acaoLog = 'deslogado por presenca diferente de presente';
        }

        if (function_exists('enviarlog')) {
            enviarlog($dataReferencia->format('Y-m-d H:i:s'), $acaoLog, '0', $matricula, '', '', '');
        } else {
            error_log('[PRESENCA_LOGIN] enviarlog indisponivel. matricula=' . $matricula . ' motivo=' . $motivo . ' data=' . $dataReferencia->format('Y-m-d H:i:s'));
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = array();
            session_destroy();
        }

        echo '<script>alert(' . json_encode($mensagem) . '); window.location="../index.php";</script>';
        exit;
    }
}

$usuariof = $_POST['login'];
$pass = $_POST['pass'];

$sql = "SELECT nome, matricula, perfil, pacesso, tipo FROM bdfrota.tbusuario WHERE usuario = '$usuariof' AND senha = '$pass'";
$resultado = mysql_query($sql) or die(mysql_error());

if (mysql_num_rows($resultado) <= 0) {
    $sql = "SELECT matricula FROM bdfrota.tbusuario WHERE usuario = '$usuariof'";
    $resultado = mysql_query($sql) or die(mysql_error());

    if (mysql_num_rows($resultado) <= 0) {
        $sql = "SELECT idtbfuncionario FROM bdaniel.tbfuncionario WHERE matricula = '$usuariof'";
        $resultado = mysql_query($sql) or die(mysql_error());

        if (mysql_num_rows($resultado) >= 1) {
            $_SESSION['matricula'] = $usuariof;
            echo "<script>alert('PRIMEIRO ACESSO: Criar usuário'); window.location=\"../cadastro.php\";</script>";
            die();
        } else {
            echo "<script>alert('Matrícula não existe'); window.location=\"../index.php\";</script>";
        }
    } else {
        echo "<script>alert('Senha incorreta'); window.location=\"../index.php\";</script>";
        die();
    }
} else {
    $row = mysql_fetch_array($resultado);
    $pacesso = $row['pacesso'];

    // if ($pacesso == 0) {
    //     echo "<script>alert('PRIMEIRO ACESSO. É necessário alterar a sua senha para acesso ao sistema.'); window.location=\"../altsenha.php\";</script>";
    // }

    $nome = $row['nome'];
    $matricula = $row['matricula'];
    $perfil = $row['perfil'];
    $tipo = $row['tipo'];

    $sql = "SELECT codempresa, ccusto FROM bdaniel.tbfuncionario WHERE matricula = '$matricula'";
    $resultado = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_array($resultado);

    $ccusto = $row['ccusto'];
    $filial = $row['codempresa'];

    if (stripos($ccusto, "claro") !== false)
        $ccusto = 'claro';
    if (stripos($ccusto, "tim") !== false)
        $ccusto = 'tim';
    if (stripos($ccusto, "ligga") !== false)
        $ccusto = 'ligga';

    $_SESSION['filial'] = $filial;
    $_SESSION['nome'] = $nome;
    $_SESSION['usuario'] = $usuariof;
    $_SESSION['matricula'] = trim($matricula);
    $_SESSION['perfil'] = $perfil;
    $_SESSION['tipo'] = $tipo;
    $_SESSION['ccusto'] = $ccusto;

    $datahora = $hoje;
    $acao = 'logou';
    $flag = '0';
    enviarlog($datahora, $acao, $flag, $matricula, '', '', '');

    switch ($perfil) {
        case '0':
            $sqlSupervisor = "SELECT 1 FROM bdfrota.tbusuario u WHERE u.matricula = '$matricula' AND u.perfil = '0' AND u.idtbsupervisor IS NOT NULL AND u.idtbsupervisor <> 0 LIMIT 1";
            $resultadoSupervisor = mysql_query($sqlSupervisor) or die(mysql_error());

/*             if (mysql_num_rows($resultadoSupervisor) > 0) {
                validarLancamentoPresencaTecnico($matricula, $connBdPonto);
            } */

            echo "<script>window.location=\"../tecnico.php\";</script>";
            break;
        case '1':
            echo "<script>window.location=\"../supervisor/solicitarcotaextra.php\";</script>";
            break;
        case '2':
            echo "<script>window.location=\"../gerenciar-equipe-coord.php\";</script>";
            break;
        case '3':
            echo "<script>window.location=\"../dashboard.php\";</script>";
            break;
        case '4':
            echo "<script>window.location=\"../telainicio.php\";</script>";
            break;
        case '5':
            if ($matricula == '002625') {
                echo "<script>window.location=\"../gestao/relinventario.php\";</script>";
            } else {
                echo "<script>window.location=\"../dashboard.php\";</script>";
            }
            break;
        case '6':
            if ($ccusto == 'tim') {
                echo "<script>window.location=\"../atividadecomplementartim.php\";</script>";
            } elseif ($ccusto == 'claro') {
                echo "<script>window.location=\"../atividadecomplementarclaro.php\";</script>";
            } elseif ($ccusto == 'ligga') {
                echo "<script>window.location=\"../atividadecomplementarligga.php\";</script>";
            } else {
                echo "<script>window.location=\"../atividadecomplementar.php\";</script>";
            }
            break;
        case '7':
            echo "<script>window.location=\"../gestao/relinventario.php\";</script>";
            break;
        case '8':
            echo "<script>window.location=\"../checklist/checklistinicio.php\";</script>";
            break;
        case '9':
            echo "<script>window.location=\"../telainicio.php\";</script>";
            break;
        case '10':
            echo "<script>window.location=\"../diretor/aprovarorcamentocomplementar.php\";</script>";
            break;
        case '11':
            echo "<script>window.location=\"../gestao/listagemveiculos.php\";</script>";
            break;
        case '12':
            echo "<script>window.location=\"../../multas/multasdp.php\";</script>";
            break;
        default:
            $datahora = $hoje;
            $acao = 'erro login';
            $flag = '0';
            enviarlog($datahora, $acao, $flag, $matricula, '', '', '');
            echo "<script>alert('Erro no perfil do usuario'); window.location=\"../index.php\";</script>";
    }
}
?>