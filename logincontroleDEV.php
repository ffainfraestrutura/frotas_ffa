<?php
date_default_timezone_set('America/Sao_Paulo');
$hoje = date('Y-m-d H:i:s');
session_start();
include("conecta.php");
include_once "func/log.php";

function redirecionarErroBootstrapDev($mensagem)
{
  $mensagemAlert = str_replace('. ', ".\n", (string) $mensagem);
  echo "<script language='javascript' type='text/javascript'>alert(" . json_encode($mensagemAlert) . ");window.location=" . json_encode('indexDEV.php') . "</script>";
  exit();
}

function carregarClasseDev($nomeClasse, $caminhoArquivo)
{
  if (!is_file($caminhoArquivo)) {
    return false;
  }

  require_once $caminhoArquivo;

  return class_exists($nomeClasse, false);
}

$baseDirDev = realpath(__DIR__);
if ($baseDirDev === false) {
  $baseDirDev = __DIR__;
}

$arquivoPresencaService = $baseDirDev . '/classes/PresencaService.php';
$arquivoPresencaPolicy = $baseDirDev . '/classes/PresencaPolicy.php';

$presencaServiceCarregada = carregarClasseDev('PresencaService', $arquivoPresencaService);
$presencaPolicyCarregada = carregarClasseDev('PresencaPolicy', $arquivoPresencaPolicy);

if (!$presencaServiceCarregada || !$presencaPolicyCarregada) {
  redirecionarErroBootstrapDev('Falha ao carregar classes de presença.');
}

$usuariof = mysql_real_escape_string(isset($_POST['login']) ? $_POST['login'] : '');
$pass = mysql_real_escape_string(isset($_POST['pass']) ? $_POST['pass'] : '');

$presencaService = new PresencaService($connBdPonto);
$presencaPolicy = new PresencaPolicy();

function redirecionarDev($mensagem, $destino)
{
  $mensagem = (string) $mensagem;
  $mensagemAlert = strpos($mensagem, "\n") !== false ? $mensagem : str_replace('. ', ".\n", $mensagem);
  echo "<script language='javascript' type='text/javascript'>alert(" . json_encode($mensagemAlert) . ");window.location=" . json_encode((string) $destino) . "</script>";
  exit();
}

function limparSessaoDev()
{
  $_SESSION = array();

  if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
  }

  session_destroy();
}

function deveValidarPresencaDepoisDasDezDev(DateTimeInterface $dataReferencia = null)
{
  $dataReferencia = $dataReferencia ?: new DateTimeImmutable('now');
  $limiteDezHoras = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $dataReferencia->format('Y-m-d') . ' 10:00:00');

  return $limiteDezHoras instanceof DateTimeInterface && $dataReferencia > $limiteDezHoras;
}

function redirecionarSemAlertaDev($destino)
{
  echo "<script language='javascript' type='text/javascript'>window.location=" . json_encode((string) $destino) . "</script>";
  exit();
}

function limparTextoLogDev($valor)
{
  return str_replace(array("'", "\r", "\n"), array('', ' ', ' '), (string) $valor);
}

function obterSituacaoPresencaLogDev($registroPresenca)
{
  if (!is_array($registroPresenca) || !isset($registroPresenca['situacau']) || trim((string) $registroPresenca['situacau']) === '') {
    return 'Sem registro';
  }

  return trim((string) $registroPresenca['situacau']);
}

function registrarLogoutPresencaDev($matricula, $registroPresenca, $decisaoPresenca)
{
  $datahora = date('Y-m-d H:i:s');
  $acao = 'deslogado por presença não informada';
  $flag = '0';
  $situacao = limparTextoLogDev(obterSituacaoPresencaLogDev($registroPresenca));
  $mensagem = isset($decisaoPresenca['mensagem']) ? limparTextoLogDev($decisaoPresenca['mensagem']) : '';

  enviarlog($datahora, $acao, $flag, $matricula, '', $situacao, $mensagem);
}

function validarPresencaTecnicoDev($matricula, $destino, PresencaService $presencaService, PresencaPolicy $presencaPolicy)
{
  $registroPresenca = $presencaService->buscarUltimaPresencaDoDia($matricula);
  $decisaoPresenca = $presencaPolicy->avaliar($registroPresenca);

  if ($decisaoPresenca['permitido']) {
    redirecionarSemAlertaDev($destino);
  }

  registrarLogoutPresencaDev($matricula, $registroPresenca, $decisaoPresenca);
  limparSessaoDev();
  redirecionarDev($decisaoPresenca['mensagem'], 'indexDEV.php');
}

$sql = "SELECT nome, matricula, perfil, pacesso, tipo, filial FROM bdfrota.tbusuario WHERE usuario = '$usuariof' AND senha = '$pass'";
$resultado = mysql_query($sql) or die(mysql_error());

if (mysql_num_rows($resultado) <= 0) {
  $sql = "SELECT matricula FROM bdfrota.tbusuario WHERE usuario = '$usuariof'";
  $resultado = mysql_query($sql) or die(mysql_error());

  if (mysql_num_rows($resultado) <= 0) {
    $sql = "SELECT idtbfuncionario FROM bdaniel.tbfuncionario where matricula = '$usuariof'";
    $resultado = mysql_query($sql) or die(mysql_error());

    if (mysql_num_rows($resultado) >= 1) {
      $_SESSION['matricula'] = $usuariof;
      redirecionarDev('PRIMEIRO ACESSO: Criar usuário', 'cadastro.php');
    } else {
      redirecionarDev('matricula não existe', 'indexDEV.php');
    }
  } else {
    redirecionarDev('Senha incorreta', 'indexDEV.php');
  }
} else {
  $row = mysql_fetch_array($resultado);
  $pacesso = $row['pacesso'];

  if ($pacesso == 0) {
    redirecionarDev('PRIMEIRO ACESSO. É necessário alterar a sua senha para acesso ao sistema.', 'altsenha.php');
  }

  $nome = $row['nome'];
  $matricula = $row['matricula'];
  $perfil = $row['perfil'];
  $tipo = $row['tipo'];
  $filial = $row['filial'];

  $sql = "SELECT ccusto FROM bdaniel.tbfuncionario WHERE matricula = '$matricula'";
  $resultado = mysql_query($sql) or die(mysql_error());
  $row = mysql_fetch_array($resultado);
  $ccusto = $row['ccusto'];

  if (stripos($ccusto, "claro") !== false) {
    $ccusto = 'claro';
  }

  if (stripos($ccusto, "tim") !== false) {
    $ccusto = 'tim';
  }

  if (stripos($ccusto, "ligga") !== false) {
    $ccusto = 'ligga';
  }

  $_SESSION['filial'] = $filial;
  $_SESSION['nome'] = $nome;
  $_SESSION['usuario'] = $usuariof;
  $_SESSION['matricula'] = $matricula;
  $_SESSION['perfil'] = $perfil;
  $_SESSION['tipo'] = $tipo;
  $_SESSION['ccusto'] = $ccusto;

  $datahora = $hoje;
  $acao = 'logou DEV presença';
  $flag = '0';
  $logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');

  if ($perfil == '0') {
    if (deveValidarPresencaDepoisDasDezDev()) {
      validarPresencaTecnicoDev($matricula, 'tecnico.php', $presencaService, $presencaPolicy);
    }

    redirecionarSemAlertaDev('tecnico.php');
  } elseif ($perfil == '1') {
    echo "<script language='javascript' type='text/javascript'>window.location=\"aprovacaocota.php\"</script>";
  } elseif ($perfil == '2') {
    echo "<script language='javascript' type='text/javascript'>window.location=\"coordenador.php\"</script>";
  } elseif ($perfil == '3') {
    echo "<script language='javascript' type='text/javascript'>window.location=\"dashboard.php\"</script>";
  } elseif ($perfil == '4') {
    echo "<script language='javascript' type='text/javascript'>window.location=\"dashboard.php\"</script>";
  } elseif ($perfil == '5') {
    if ($matricula == '002625') {
      echo "<script language='javascript' type='text/javascript'>window.location=\"gestao/relinventario.php\"</script>";
    } else {
      echo "<script language='javascript' type='text/javascript'>window.location=\"dashboard.php\"</script>";
    }
  } elseif ($perfil == '6') {
    if ($ccusto == 'tim') {
      echo "<script language='javascript' type='text/javascript'>window.location=\"atividadecomplementartim.php\"</script>";
    } elseif ($ccusto == 'claro') {
      echo "<script language='javascript' type='text/javascript'>window.location=\"atividadecomplementarclaro.php\"</script>";
    } else {
      echo "<script language='javascript' type='text/javascript'>window.location=\"atividadecomplementar.php\"</script>";
    }
  } elseif ($perfil == '7') {
    echo "<script language='javascript' type='text/javascript'>window.location=\"gestao/relinventario.php\"</script>";
  } elseif ($perfil == '8') {
    echo "<script language='javascript' type='text/javascript'>window.location=\"checklist/checklistinicio.php\"</script>";
  } elseif ($perfil == '10') {
    echo "<script language='javascript' type='text/javascript'>window.location=\"importoperadora.php\"</script>";
  } else {
    $acao = 'erro login DEV presença';
    $logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');
    redirecionarDev('Erro no perfil do usuário', 'indexDEV.php');
  }
}
?>