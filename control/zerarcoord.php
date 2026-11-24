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

$matriculaf = $_POST['matriculasup'];
$tipocota = $_POST["tipocota"];

    

    $sql = "SELECT valor FROM bdfrota.tbsupervisor where matricula = '$matriculaf'";
    $resultado = mysql_query($sql) or die (mysql_error());
    $row = mysql_fetch_array($resultado);
    $saldosup = $row[0];

    if ($saldosup <= 0) {
        echo"<script language='javascript' type='text/javascript'>alert('Saldo já zerado');window.location=\"../remanejamentocoord.php\"</script>";
    }



            $sql = "UPDATE bdfrota.tbsupervisor SET valor = 0, totorccomp = totorccomp - $saldosup where matricula = '$matriculaf'";
            $resultado = mysql_query($sql) or die (mysql_error());


$sql = "UPDATE bdfrota.tbcoord SET valor = valor + $saldosup where matricula = '$matricula'";

            $resultado = mysql_query($sql) or die (mysql_error());

            $acao = 'Saldo Zerado';
            $flag = '2';
            $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $saldotec, '0');

                echo"<script language='javascript' type='text/javascript'>alert('Orçamento zerado com sucesso!');window.location=\"../remanejamentocoord.php\"</script>";


        
?>
