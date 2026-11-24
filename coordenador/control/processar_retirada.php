<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

// Verificar se usuário está logado
if (!isset($_SESSION['matricula'])) {
    echo "<script language='javascript' type='text/javascript'>alert('Sessão expirada. Faça login novamente');window.location=\"../../index.php\"</script>";
    exit;
}

$matricula = $_SESSION['matricula'];
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$hoje = date('Y-m-d H:i:s');

include("../../conecta05.php");
include_once "../../func/log.php";

// Validar dados do formulário
if (!isset($_POST['matriculatec']) || !isset($_POST['valor'])) {
    echo "<script language='javascript' type='text/javascript'>alert('Dados incompletos');window.location=\"../retiradadesaldo.php\"</script>";
    exit;
}

// Verificar conexão
if (!isset($conexao05) || !$conexao05) {
    die("ERRO CRÍTICO: Conexão com banco de dados não foi estabelecida.");
}

if (!mysqli_ping($conexao05)) {
    die("ERRO CRÍTICO: Conexão com banco de dados perdida. Erro: " . mysqli_connect_error());
}

$matriculatec = $_POST['matriculatec'];
$valor = $_POST["valor"];
$valor = str_replace(".", "", $valor);
$valor = str_replace(",", ".", $valor);
$saldocartao = $_POST["saldoatual"];
$matriculacoord = $_POST['matriculacoord'];
$codemptec = $_POST["codemptec"];
$placa = $_POST["placa"];

// Validar valores
if ($valor <= 0) {
    echo "<script language='javascript' type='text/javascript'>alert('Valor inválido');window.location=\"../retiradadesaldo.php\"</script>";
    exit;
}

// Obter saldo atual do técnico no cartão
$saldoTecnico = floatval($saldocartao);

// Obter ID do gerente responsável pelo coordenador
$sql = "SELECT idtbgerente FROM bdfrota.tbcoord where matricula = '$matriculacoord'";
$resultado = mysqli_query($conexao05, $sql);
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

// Obter matrícula do gerente
$sql = "SELECT matricula FROM bdfrota.tbgerente where idtbgerente = '$idtbgerente'";
$resultado = mysqli_query($conexao05, $sql);
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

if ($valor > $saldoTecnico) {
    echo "<script language='javascript' type='text/javascript'>alert('Saldo Insuficiente no cartão do técnico! Saldo disponível: R$ " . number_format($saldoTecnico, 2, ',', '.') . "');window.location=\"../retiradadesaldo.php\"</script>";
    exit;
}

// Retirar do técnico
$sql = "UPDATE bdfrota.tbsaldo SET saldo = saldo - $valor where matricula = '$matriculatec'";
$resultado = mysqli_query($conexao05, $sql);
if (!$resultado) {
    echo "<script language='javascript' type='text/javascript'>alert('Erro ao retirar saldo do técnico: " . mysqli_error($conexao05) . "');window.location=\"../retiradadesaldo.php\"</script>";
    exit;
}

// Adicionar ao gerente
$sql = "UPDATE bdfrota.tbgerente SET valor = valor + $valor where matricula = '$matriculagerente'";
$resultado = mysqli_query($conexao05, $sql);
if (!$resultado) {
    echo "<script language='javascript' type='text/javascript'>alert('Erro ao adicionar saldo ao gerente: " . mysqli_error($conexao05) . "');window.location=\"../retiradadesaldo.php\"</script>";
    exit;
}

// Registrar log
$acao = 'Retirada de saldo do técnico para gerente';
$flag = '2';
$logresult = enviarlog($hoje, $acao, $flag, $matriculatec, $matriculagerente, $saldoTecnico, $valor);

echo "<script language='javascript' type='text/javascript'>alert('Saldo transferido do técnico para o gerente com sucesso! Valor: R$ " . number_format($valor, 2, ',', '.') . "');window.location=\"../retiradadesaldo.php\"</script>";
?>
