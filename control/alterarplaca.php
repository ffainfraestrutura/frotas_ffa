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
$matriculaf = $_POST['matriculaplaca'];
$placa = $_POST['placa'];

$sql = "SELECT ds_placa FROM bdfrota.tbcarrosxcolaborador where id_colaborador = '$matriculaf '";
//echo "$sql <br>";

$resultado = mysql_query($sql) or die (mysql_error());
if (mysql_num_rows($resultado)>=1){ 
    $placam = $row[0];
    $sql = "UPDATE bdfrota.tbcarrosxcolaborador SET id_colaborador = '' where ds_placa = '$placam'";
    //echo "$sql <br>";
    $resultado = mysql_query($sql) or die (mysql_error());
  }
            $sql = "UPDATE bdfrota.tbcarrosxcolaborador SET id_colaborador = '$matriculaf' where ds_placa = '$placa'";
            $resultado = mysql_query($sql) or die (mysql_error());
            //echo "$sql <br>";
            $acao = 'Mudou placa';
            $flag = '8';
            $logresult = enviarlog($hoje, $acao, $flag, 'jordan', 'jordan', '', '');

                echo"<script language='javascript' type='text/javascript'>alert('Transferencia concluida!');window.location=\"../alterarplaca.php\"</script>";

    





        
?>
