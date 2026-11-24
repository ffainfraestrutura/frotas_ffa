<?php
date_default_timezone_set('America/Sao_Paulo');
//header('Content-type: text/html; charset=ISO-8859-1');
//include("../conecta.php");

include_once"../../func/log.php";
include("../../conecta2.php");


$hoje=date('Y-m-d H:i:s');

$idtbveiculo = utf8_decode($_POST['idtbveiculo']);
$escolha = $_POST['escolha'];
$matr_autor = $_POST['matr_autor'];
$placa = $_POST['placa'];

//print "$idtbveiculo $escolha";

if($escolha == 0){
  
  echo "<script language='javascript' type='text/javascript'> alert('Processo impedido.');window.location=\"../listagemveiculos.php\"  </script>";

}elseif($escolha == 1){
  $sql = "UPDATE bdfrota.tbveiculo SET visivel=0 WHERE idtbveiculo='$idtbveiculo';";
  
  $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));  
  
  $acao='Apagou registro placa';
  $tipo = 'cadastro';
  //print "enviarlognovo($hoje, $acao, $matricula, $matr_autor, $tipo, $placa)";
  $logresult = enviarlognovo($hoje, $acao, '', $matr_autor, $tipo, $placa);

  echo "<script language='javascript' type='text/javascript'> alert('Registro do veículo apagado.');window.location=\"../listagemveiculos.php\";  </script>";

}

?>