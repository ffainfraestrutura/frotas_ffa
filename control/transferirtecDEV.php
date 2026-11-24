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
include("../conectaDEV.php");
include_once"../func/log.php";

$matriculaf = $_POST['matriculatec'];

$sql = "SELECT idtbsupervisor FROM bdfrota.tbsupervisor where matricula = '$matricula'";
//echo "$sql";
$resultado = mysql_query($sql) or die (mysql_error());
$row = mysql_fetch_array($resultado);
$idtbsupervisor = $row[0];

    $sql = "UPDATE bdfrota.tbusuario SET idtbsupervisor = $idtbsupervisor where matricula = '$matriculaf'";
    //echo "$sql";
    $resultado = mysql_query($sql) or die (mysql_error());


            $resultado = mysql_query($sql) or die (mysql_error());
            $acao = 'Transferiu tecnico';
            $flag = '7';
            $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, '', '');

                echo"<script language='javascript' type='text/javascript'>alert('Transferencia concluida!');window.location=\"../transferirtec.php\"</script>";
        
?>
