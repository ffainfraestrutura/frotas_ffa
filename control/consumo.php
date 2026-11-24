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

$tipo = $_POST["tipo"];
$valor = $_POST["valor"];
$valor = str_replace(",", ".", $valor);
$cons = $_POST["cons"];
$tipocomb = $_POST["tipocomb"];

$sql2 = "INSERT INTO bdfrota.tbconsumocombustivel (tipoveiculo, consumo_litro, valorcomb, tipo) values ('$tipo', '$cons', '$valor', '$tipocomb')";

$resultado2 = mysql_query($sql2) or die (mysql_error());


        $acao = 'Adicionou consumo combustível';
        $flag = '3';
        $logresult = enviarlog($hoje, $acao, $flag, $matricula, $matricula, '', $valor);


    echo"<script language='javascript' type='text/javascript'>alert('Consumo adicionado com sucesso');window.location=\"../consumo.php\"</script>";


?>
