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

$matriculaf = $_POST['matriculatec'];
$id = $_POST['id'];
$idtbsaldo = $_POST['idtbsaldo'];
$decisao = $_POST['decisao'];
$valor = $_POST["valor"];
$valor = str_replace(",", ".", $valor);
$valor_ant = $_POST["valor_ant"];
$valor_ant = str_replace(",", ".", $valor_ant);

    $sql = "UPDATE bdfrota.tbpedidostec SET flag = $decisao where idtbpedidostec = $id";
    $resultado = mysql_query($sql) or die (mysql_error());

    if ($decisao == 2) {
            $sql = "UPDATE bdfrota.tbsaldo SET saldo = saldo + $valor where idtbsaldo = '$idtbsaldo'";
            $resultado = mysql_query($sql) or die (mysql_error());
            $acao = 'Aceitou cota extra';
            $flag = '2';
            $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $valor_ant, $valor);

                echo"<script language='javascript' type='text/javascript'>alert('Pedido Aceito!');window.location=\"../../supervisor-adm.php\"</script>";
    } else {
            $acao = 'Não aceitou cota extra';
            $flag = '2';
            $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $valor_ant, $valor);

                echo"<script language='javascript' type='text/javascript'>alert('Pedido não Aceito!');window.location=\"../../supervisor-adm.php\"</script>";
    }





        
?>
