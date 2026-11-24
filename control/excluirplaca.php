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
$placa = $_POST['placa'];
$matriculaf = '';

    $sql = "UPDATE bdfrota.tbcarrosxcolaborador SET id_colaborador = '' where ds_placa = '$placa'";
    //echo "$sql <br>";
    $resultado = mysql_query($sql) or die (mysql_error());
  
            $acao = 'Mudou placa';
            $flag = '8';
            $logresult = enviarlog($hoje, $acao, $flag, '$placa', '$matriculaf', '', '');

                echo"<script language='javascript' type='text/javascript'>alert('Transferencia concluida!');window.location=\"../alterarplaca.php\"</script>";

    





        
?>
