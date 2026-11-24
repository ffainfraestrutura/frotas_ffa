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
include("../conecta.php");
include_once"../func/log.php";

$matriculaf = $_POST['matriculatec'];
//echo "MATRICULA = $matriculaf <br>";
$valor = $_POST["valor"];
$valor = str_replace(",", ".", $valor);
$tipocota = $_POST["tipocota"];
$kms = $_POST["kms"];
$justsup = $_POST["justificativa"];
$origem = $_POST["origem"];
$destino1 = $_POST["destino1"];
$destino2 = $_POST["destino2"];
$matricula = $_POST["matsup"];

$sql = "INSERT INTO bdfrota.tbcotarotasup (matricula, matsup, data, kms, valor, justsup, origem, destino1, destino2) VALUES ('$matriculaf', '$matricula', '$hoje', '$kms', '$valor', '$justsup', '$origem', '$destino1', '$destino2')";
$resultado = mysql_query($sql) or die (mysql_error());
$row = mysql_fetch_array($resultado);


$acao = 'Criou cota rota';
$flag = '2';
$logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, '', $valor);

echo"<script language='javascript' type='text/javascript'>alert('Rota criada com sucesso');window.location=\"../aaprovarcota.php\"</script>";

        
?>
