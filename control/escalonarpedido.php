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
$id = $_POST['id'];
$idtbsaldo = $_POST['idtbsaldo'];
$decisao = $_POST['decisao'];
$valor = $_POST["valor"];
$valor = str_replace(",", ".", $valor);
$valor_ant = $_POST["valor_ant"];
$valor_ant = str_replace(",", ".", $valor_ant);
$orcpedido = $_POST["orcpedido"];
$orcpedido = str_replace(",", ".", $orcpedido);
$tipocota = $_POST["tipocota"];
$valor_desc = $_POST["valor_desc"];
$valor_desc = str_replace(",", ".", $valor_desc);
$orcsem = $_POST["orcsem"];
$orcsem = str_replace(",", ".", $orcsem);
$kmproj = $_POST["kmproj"];
$kmproj = str_replace(",", ".", $kmproj);
$kmos = $_POST["kmos"];
$kmos = str_replace(",", ".", $kmos);
$kmsem = $_POST["kmsem"];
$kmsem = str_replace(",", ".", $kmsem);
$totextra = $_POST["totextra"];
$totextra = str_replace(",", ".", $totextra);
$dataplantao = $_POST["dataplantao"];
$justsup = $_POST["justificativa"];

$sql = "UPDATE bdfrota.tbpedidostec SET escalonado = '1' where idtbpedidostec = $id";
$resultado = mysql_query($sql) or die (mysql_error());

 
$acao = 'Escalonou cota';
$flag = '2';
$logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, '', '');

echo"<script language='javascript' type='text/javascript'>alert('Cota Escalonada');window.location=\"../aprovacaocota.php\"</script>";
    




        
?>
