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
$valor = str_replace(".", "", $valor);
$valor = str_replace(",", ".", $valor);
$valor_ant = $_POST["valor_ant"];
$valor_ant = str_replace(",", ".", $valor_ant);

    $sql = "SELECT valor FROM bdfrota.tbcoord where matricula = '$matricula'";
    $resultado = mysql_query($sql) or die (mysql_error());
    $row = mysql_fetch_array($resultado);
    $saldocoord = $row[0];



if ($valor > $saldocoord) {
                
                echo"<script language='javascript' type='text/javascript'>alert('Saldo Insuficiente!');window.location=\"../distorccoord.php\"</script>";

            } else {

            $sql = "UPDATE bdfrota.tbsupervisor SET  valor = valor + $valor, totorccomp = totorccomp + $valor where matricula = '$matriculaf'";
            $resultado = mysql_query($sql) or die (mysql_error());
            
            $sql = "UPDATE bdfrota.tbcoord SET valor = valor - $valor where matricula = '$matricula'";
            $resultado = mysql_query($sql) or die (mysql_error());
            
            $acao = 'Aceitou orçamento extra';
            $flag = '2';
            $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $valor_ant, $valor);
               echo"<script language='javascript' type='text/javascript'>alert('Orçamento Enviado');window.location=\"../distorccoord.php\"</script>";
            }





        
?>
