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

$id = $_POST["id"];
$valor = $_POST["valor"];
$valor = str_replace(",", ".", $valor);

$sql2 = "UPDATE bdfrota.tborcamentomensal SET orcamentocomplementar = orcamentocomplementar + $valor, saldoorcamento = saldoorcamento + $valor, orcamentofinal = orcamentofinal + $valor where idtborcamentomensal='$id'";

$resultado2 = mysql_query($sql2) or die (mysql_error());


        $acao = 'Alterou orcamento';
        $flag = '3';
        $logresult = enviarlog($hoje, $acao, $flag, $matricula, $matricula, '', $valor);


    echo"<script language='javascript' type='text/javascript'>alert('Orçamento alterado com sucesso');window.location=\"../orcamento.php\"</script>";


?>
