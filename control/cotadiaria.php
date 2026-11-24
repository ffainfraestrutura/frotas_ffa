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
$cotadiaria = $_POST['cotadiaria'];
$cotadiaria = str_replace(",", ".", $cotadiaria);
    $sql = "INSERT INTO bdfrota.tbcotadiaria (valor, dtalteracao) values ('$cotadiaria', '$hoje')";
    $resultado = mysql_query($sql) or die (mysql_error());

    $acao = 'Alterou cota diária';
    $flag = '1';
    $logresult = enviarlog($hoje, $acao, $flag, $matricula);
    echo"<script language='javascript' type='text/javascript'>alert('Nova cota adicionada com sucesso');window.location=\"../inicial.php\"</script>";
        
?>
