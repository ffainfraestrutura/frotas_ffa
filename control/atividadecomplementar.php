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

$tecnico = $_POST["tecnico"];
$data = $_POST["data"];
$hora = $_POST["hora"];
$endereco1 = $_POST["endereco1"];
$motivo1 = $_POST["motivo1"];

$sql = "SELECT nomeaux FROM bdaniel.tbfuncionario where matricula = '$tecnico' and nomeaux is not null";
$resultado = mysql_query($sql) or die (mysql_error());
if (mysql_num_rows($resultado)<=0){
  $sql = "SELECT nome FROM bdaniel.tbfuncionario where matricula = '$tecnico'";
  $resultado = mysql_query($sql) or die (mysql_error());
}
$row = mysql_fetch_array($resultado);
$provedor = $row[0];

$sql2 = "INSERT INTO bdfrota.tbosrota (provedor, endereco, data, eta, fim, matricula, operador, foratoa, motivo) values ('$provedor', '$endereco1', '$data', '$hora', '$hora', '$tecnico', '$matricula', '1', '$motivo1')";

$resultado2 = mysql_query($sql2) or die (mysql_error());


        $acao = 'Adicionou Endereco';
        $flag = '3';
        $logresult = enviarlog($hoje, $acao, $flag, $tecnico, $matricula, '', '');


    echo"<script language='javascript' type='text/javascript'>alert('Endereço adicionado com sucesso');window.location=\"../atividadecomplementar.php\"</script>";


?>
