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
$valor = $_POST["valor"];
$valor = str_replace(".", "", $valor);
$valor = str_replace(",", ".", $valor);
$tipocota = $_POST["tipocota"];

    

    $sql = "SELECT valor FROM bdfrota.tbsupervisor where matricula = '$matriculaf'";
    $resultado = mysql_query($sql) or die (mysql_error());
    $row = mysql_fetch_array($resultado);
    $saldosup = $row[0];


            if ($valor > $saldosup) {
                
                echo"<script language='javascript' type='text/javascript'>alert('Saldo Insuficiente!');window.location=\"../remanejamentocoord.php\"</script>";

            } else {


            $sql = "UPDATE bdfrota.tbsupervisor SET valor = valor - $valor, totorccomp = totorccomp - $valor where matricula = '$matriculaf'";
            $resultado = mysql_query($sql) or die (mysql_error());

            $sql = "UPDATE bdfrota.tbcoord SET valor = valor + $valor where matricula = '$matricula'";
            $resultado = mysql_query($sql) or die (mysql_error());

            $acao = 'Retirou saldo';
            $flag = '2';
            $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $saldosup, $valor);

                echo"<script language='javascript' type='text/javascript'>alert('Remanejamento feito com sucesso!');window.location=\"../remanejamentocoord.php\"</script>";
        } 

        
?>
