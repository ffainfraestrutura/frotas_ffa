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

$id = $_POST['id'];
$decisao = $_POST['decisao'];

$sql = "UPDATE bdfrota.tbtoaqui SET aceite = '$decisao' where idtbtoaqui = '$id'";
//echo "$sql";
$resultado = mysql_query($sql) or die (mysql_error());
$row = mysql_fetch_array($resultado);

            $resultado = mysql_query($sql) or die (mysql_error());
            $acao = 'aceitou endereco';
            $flag = '1';
            $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, '', '');

                echo"<script language='javascript' type='text/javascript'>alert('Endereço alterado');window.location=\"../aceitetoaqui.php\"</script>";

    





        
?>
