<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
include("../conecta.php");

$placa = $_GET['placa'];

//print $placa;
$sql="SELECT placa FROM bdfrota.tbmanprev WHERE placa='$placa'";
$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

//colocar alguma trava de manutenção por mês/data ou status, pra não gerar retrabalho, duas marcaçoes de manutenção dentro de um curto período

$sql1 = "SELECT idtbveiculo FROM bdfrota.tbveiculo WHERE placa='$placa'";
$resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));
$row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
    $idtbveiculo = $row1['idtbveiculo'];
//print $idtbveiculo;

print "<script>window.location=\"../../gestao/editarcadveiculo.php?idtbveiculo=$idtbveiculo\" </script>";
 
?>