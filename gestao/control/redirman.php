<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
include("../conecta2.php");

$placa = $_GET['placa'];

//print $placa;

$sql="SELECT placa FROM bdfrota.tbmanprev WHERE placa='$placa'";
$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

//colocar alguma trava de manutenção por mês/data ou status, pra não gerar retrabalho, duas marcaçoes de manutenção dentro de um curto período

$sql1 = "SELECT idtbveiculo FROM bdfrota.tbveiculo WHERE placa='$placa'";
$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));
$row1 = mysqli_fetch_array($resultado, MYSQLI_BOTH);
    $idtbveiculo = $row['idtbveiculo'];

print "<script>window.location=\"\" </script>";
 
?>