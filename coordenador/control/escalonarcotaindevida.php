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


$id = $_GET['idpedido'];

$sql = "UPDATE bdfrota.tbpedidostec SET escalonado = '2' where idtbpedidostec = $id";
$resultado = mysql_query($sql) or die(mysql_error());


$acao = 'Escalonou cota indevida';
$flag = '2';
$logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, '', '');

echo "<script language='javascript' type='text/javascript'>alert('Cota escalonada para o Gerente');window.location=\"../aprovarcota.php\"</script>";






?>