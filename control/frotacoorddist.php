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
$id = $_POST['id'];
$valor = $_POST["valor"];
$valor = str_replace(",", ".", $valor);
$valor_ant = $_POST["valor_ant"];
$valor_ant = str_replace(",", ".", $valor_ant);



            $sql = "UPDATE bdfrota.tbcoord SET  valor = valor + $valor, orcrecebido = orcrecebido + $valor where matricula = '$matriculaf'";
            $resultado = mysql_query($sql) or die (mysql_error());
            
            $sql = "INSERT INTO bdfrota.tborcdistfrota (matr_frota, matr_coord, valor_ant, valor_ins, data) VALUES ('$matricula', '$matriculaf', '$valor_ant', '$valor', '$hoje')";
            $resultado = mysql_query($sql) or die (mysql_error());
            
            $acao = 'Orçamento extra frota';
            $flag = '2';
            $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $valor_ant, $valor);
               echo"<script language='javascript' type='text/javascript'>alert('Orçamento Enviado');window.location=\"../orcamento.php\"</script>";





        
?>
