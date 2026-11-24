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

$matriculaf = $_POST['matricula'];
$justificativa = $_POST['justificativa'];

$valor = $_POST['valor'];
$valor = str_replace(".", "", $valor);
$valor = str_replace(",", ".", $valor);
    $sql = "INSERT INTO bdfrota.tbpedidoscoord (matricula, valor, justificativa, data) values ('$matriculaf', '$valor', '$justificativa', '$hoje')";
    $resultado = mysql_query($sql) or die (mysql_error());

    $acao = 'Pediu orçamento extra';
    $flag = '2';
    $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, '', $valor);
    echo"<script language='javascript' type='text/javascript'>alert('Pedido realizado com sucesso');window.location=\"../coordenador.php\"</script>";
        
?>
