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

//print "$idtbveiculo $escolha";

if($escolha == 0){
  echo "<script language='javascript' type='text/javascript'> alert('Processo impedido.');window.location=\"../listagemveiculos.php\"  </script>";
}elseif($escolha == 1){
  $sql = "DELETE FROM bdfrota.tbveiculo WHERE idtbveiculo='$idtbveiculo';";
  //$sql = "SELECT * FROM `bdfrota-teste`.tbveiculo WHERE idtbveiculo='$idtbveiculo';";

  $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

  $acao='Apagou registro de veiculo';
  $tipo = 'cadastro';
  $logresult = enviarlognovo($hoje, $acao, '', $matr_autor, $tipo, $placa);

  //print $sql;

  echo "<script language='javascript' type='text/javascript'> alert('Registro do veículo apagado.');window.location=\"../listagemveiculos.php\"  </script>";
}

?>