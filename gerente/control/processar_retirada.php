<?php
date_default_timezone_set('America/Sao_Paulo');
$hoje = date('Y-m-d H:i:s');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
include("../../conecta.php");
include_once "../../func/log.php";

$matriculatec = $_POST['matriculatec'];
$valor = $_POST["valor"];
$valor = str_replace(".", "", $valor);
$valor = str_replace(",", ".", $valor);
$saldocartao = $_POST["saldoatual"];
$matriculacoord = $_POST['matriculacoord'];
$codemptec = $_POST["codemptec"];
$placa = $_POST["placa"];

// Obter saldo atual do técnico no cartão
$saldoTecnico = $saldocartao;

// Obter ID do gerente responsável pelo coordenador
$sql = "SELECT idtbgerente FROM bdfrota.tbcoord where matricula = '$matriculacoord'";
$resultado = mysql_query($sql) or die(mysql_error());
$row = mysql_fetch_array($resultado);
$idtbgerente = $row[0];

// Obter matrícula do gerente
$sql = "SELECT matricula FROM bdfrota.tbgerente where idtbgerente = '$idtbgerente'";
$resultado = mysql_query($sql) or die(mysql_error());
$row = mysql_fetch_array($resultado);
$matriculagerente = $row[0];

if ($valor > $saldoTecnico) {
    echo "<script language='javascript' type='text/javascript'>alert('Saldo Insuficiente no cartão do técnico!');window.location=\"../retiradadesaldo.php\"</script>";
} else {
    // Retirar do técnico (sbaldo)
    $sql = "UPDATE bdfrota.tbsaldo SET saldo = saldo - $valor where matricula = '$matriculatec'";
    $resultado = mysql_query($sql) or die(mysql_error());

    // Adicionar ao gerente
    $sql = "UPDATE bdfrota.tbgerente SET valor = valor + $valor where matricula = '$matriculagerente'";
    $resultado = mysql_query($sql) or die(mysql_error());

    // Registrar log
    $acao = 'Retirada de saldo do técnico para gerente';
    $flag = '2';
    $logresult = enviarlog($hoje, $acao, $flag, $matriculatec, $matriculagerente, $saldoTecnico, $valor);

    echo "<script language='javascript' type='text/javascript'>alert('Saldo transferido do técnico para o gerente com sucesso!');window.location=\"../retiradadesaldo.php\"</script>";
}
?>
